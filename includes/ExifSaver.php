<?php
class ExifSaver {
	public function __construct() {
		$this->setupFilters();
	}

	private function setupFilters() {
		add_filter( 'attachment_fields_to_edit', array($this, 'registerExifAttachmentFields'), 10, 2 );
		add_filter( 'attachment_fields_to_save', array($this, 'saveExifAttachmentFields'), 10, 2 );
		add_filter( 'wp_update_attachment_metadata', array($this, 'fetchExifOnUpload'), 10, 2 );
	}

	/**
	 * Fetch, parse and save exif data.
	 *
	 * @param $data array, Attachment data.
	 * @param $postId int, Attachment id.
	 * @return void.
	 */
	public function fetchExifOnUpload($data, $postId) {
		$uploadDir = wp_upload_dir();
		$filePath = $uploadDir['basedir'] . DIRECTORY_SEPARATOR . $data['file'];
		$exifImage = new Exif($filePath);
		$exifData = $exifImage->getExif();

		foreach($exifData as $exifkKey => $exifFieldData) {
			update_post_meta( $postId, 'exif_' . $exifkKey, $exifFieldData);
		}
	}

	/**
	 * Register exif fields.
	 *
	 * @param $form_fields array, fields to include in attachment form
	 * @param $post object, attachment record in database
	 * @return $form_fields, modified form fields
	 */
	public function registerExifAttachmentFields($form_fields, $post) {
		foreach(Exif::$fieldNames as $fieldName) {
			$form_fields['exif_' . $fieldName] = array(
				'label' => $fieldName,
				'input' => 'text',
				'value' => get_post_meta( $post->ID, 'exif_' . $fieldName, true ),
				'helps' => 'Exif data from image.',
			);
		}

		return $form_fields;
	}

	/**
	 * Update exif fields.
	 *
	 * @param $post array, the post data for database
	 * @param $attachment array, attachment fields from $_POST form
	 * @return $post array, modified post data
	 */
	public function saveExifAttachmentFields($post, $attachment) {
		foreach(Exif::$fieldNames as $fieldName) {
			if(isset($attachment['exif_' . $fieldName])) {
				update_post_meta( $post['ID'], 'exif_' . $fieldName, $attachment['exif_' . $fieldName] );
			}
		}

		return $post;
	}

}


