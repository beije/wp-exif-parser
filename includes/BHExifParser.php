<?php
class BHExifParser {
	public static $fieldNames = array(
		'Model' => [
			'friendly' => 'Model',
			'description' => 'Camera model'
		],
		'DateTime' => [
			'friendly' => 'Taken',
			'description' => 'When picture was taken.'
		],
		'Artist' => [
			'friendly' => 'Photographer',
			'description' => 'Who took the photo.'
		],
		'Copyright' => [
			'friendly' => 'Copyright',
			'description' => 'The copyright notice.'
		],
		'ExposureTime' => [
			'friendly' => 'Shutter speed',
			'description' => 'The exposure time.'
		],
		'FNumber' => [
			'friendly' => 'Aperture',
			'description' => 'The aperture f-stop.'
		],
		'ISOSpeedRatings' => [
			'friendly' => 'ISO',
			'description' => 'The ISO speed.'
		],
		'FocalLength' => [
			'friendly' => 'Focal length',
			'description' => 'What focal length the image was taken with (in mm).'
		]
	);

	private $values;
	private $filePath;
	private $exif;

	public function __construct($filePath) {

		foreach(self::$fieldNames as $key => $value) {
			$this->values[$key] = '';
		}

		$this->setFilePath($filePath);
	}

	public static function hasSupport() {
		if(!function_exists('exif_read_data')) {	
			return false;
		}

		return true;
	}

	public function setFilePath($filePath) {
		if(file_exists($filePath)) {
			$this->filePath = $filePath;
			$this->parseImageExif();
		}
	}

	private function parseImageExif() {
		$exif = exif_read_data($this->filePath);

		foreach($exif as $key => $value) {
			if(array_key_exists($key, self::$fieldNames)) {
				if($key == 'FNumber' || $key == 'FocalLength') {
					$value = $this->handleDividbleNumber($value);
				}

				if($key == 'DateTime') {
					$value = $this->handleDate($value);
				}

				$this->values[$key] = $value;
			}
		}
	}
	private function handleDate($date) {
		$date = date_create($date);
		return date_format($date, 'Y-m-d H:i:s');
	}
	private function handleDividbleNumber($number) {
		$fnumber = explode('/', $number);
		if(!isset($fnumber[0]) || !isset($fnumber[1]) || intval($fnumber[1]) == 0) {
			return $number;
		}

		return intval($fnumber[0]) / intval($fnumber[1]);
	}

	public function getExif() {
		return $this->values;
	}

}