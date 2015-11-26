<?php
namespace bigland\geo\area\tests\unit;

use yii\console\Application;

class BaseTestCase extends \PHPUnit_Framework_TestCase {
	protected function setUp() {
		new Application( require( __DIR__ . '/config.php' ) );
	}
}