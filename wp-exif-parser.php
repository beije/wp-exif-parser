<?php
/*
Plugin Name: Image EXIF parser.
Plugin URI: http://www.benjaminhorn.se
Description: Parses the EXIF data from images.
Version: 0.0.0
Author: Benjamin Horn
Author URI: http://www.benjaminhorn.se
*/
include('includes/BHExifParser.php');
include('includes/BHExifSaver.php');

new BHExifSaver();

?>