<?php
class BHExifSaver {
	private $domain = 'bh_exif_';

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
		if(!BHExifParser::hasSupport()) {
			return;
		}

		$uploadDir = wp_upload_dir();
		$filePath = $uploadDir['basedir'] . DIRECTORY_SEPARATOR . $data['file'];
		$exifImage = new BHExifParser($filePath);
		$exifData = $exifImage->getExif();

		foreach($exifData as $exifkKey => $exifFieldData) {
			update_post_meta( $postId, $this->domain . $exifkKey, $exifFieldData);
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
		foreach(BHExifParser::$fieldNames as $fieldName => $value) {
			$form_fields[$this->domain . $fieldName] = array(
				'label' => $value['friendly'],
				'input' => 'text',
				'value' => get_post_meta( $post->ID, $this->domain . $fieldName, true ),
				'helps' => $value['description'],
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
		foreach(BHExifParser::$fieldNames as $fieldName => $value) {
			if(isset($attachment['exif_' . $fieldName])) {
				update_post_meta( $post['ID'], 'exif_' . $fieldName, $attachment['exif_' . $fieldName] );
			}
		}

		return $post;
	}

}


