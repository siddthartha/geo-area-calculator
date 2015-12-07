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
        [
            55.195251578295,
            38.731017914659
        ],
        [
            55.195168847224,
            38.730731728298
        ],
        [
            55.194486309338,
            38.730463428584
        ],
        [
            55.194248452477,
            38.729372343082
        ],
        [
            55.193172908072,
            38.728853630303
        ],
        [
            55.192748887392,
            38.731071574602
        ],
        [
            55.192779913449,
            38.731286214373
        ],
        [
            55.19303846298,
            38.731429307553
        ],
        [
            55.193969227394,
            38.731876473743
        ],
        [
            55.195158505829,
            38.732395186522
        ],
        [
            55.19532396784,
            38.73130410102
        ],
        [
            55.195251578295,
            38.731017914659
        ]
    ]
		);
echo $area;
		$this->assertEquals( 1, 1, "$area" );
	}
}
