<?php
/**
 * Author: Anton Sadovnikoff
 * Email: sadovnikoff@gmail.com
 */

$_SERVER['SCRIPT_NAME']     = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once( __DIR__ . '/../../autoload.php' );

require_once( __DIR__ . '/BaseTestCase.php' );