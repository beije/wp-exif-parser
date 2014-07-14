<?php
class Exif {
	public static $fieldNames = array(
		'Make',
		'Model',
		'DateTime',
		'Artist',
		'Copyright',
		'ExposureTime',
		'FNumber',
		'ISOSpeedRatings',
		'Flash',
		'FocalLength'
	);

	private $values;
	private $filePath;
	private $exif;

	public function __construct($filePath) {

		foreach(self::$fieldNames as $key) {
			$this->values[$key] = '';
		}

		$this->setFilePath($filePath);
	}

	public function setFilePath($filePath) {
		if(file_exists($filePath)) {
			$this->filePath = $filePath;
			$this->parseImageExif();
		}
	}

	private function parseImageExif() {
		$exif = exif_read_data($this->filePath);
		error_log(json_encode($exif));
		foreach($exif as $key => $value) {
			if(in_array($key, self::$fieldNames)) {
				if($key == 'FNumber' || $key == 'FocalLength') {
					$value = $this->handleDividbleNumber($value);
				}
				$this->values[$key] = $value;
			}
		}
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