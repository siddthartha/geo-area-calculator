<?php

/**
 * Author: Anton Sadovnikoff
 * Email:  sadovnikoff@gmail.com
 */

namespace siddthartha\geo\area\tests\unit\geoArea;


use siddthartha\geo\area\helpers\GeoAreaCalculator;
use siddthartha\geo\area\tests\unit\BaseTestCase;

class GeoAreaTest extends BaseTestCase {

	public function testGeoArea() {
		$area = GeoAreaCalculator::getArea(
			[
				[ -10.812317, 18 ],
				[ 10.812317, -18 ],
				[ 26.565051,  18 ],
				[ 52.622632, -18 ],
				[ 52.622632,  54 ],
				[ 10.812317,  54 ],
				[ -10.812317, 18 ],
			]
		);

		$this->assertEquals( floor( $area ), 33953235824742 );
	}
}
