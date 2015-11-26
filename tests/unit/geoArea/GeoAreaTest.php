<?php

/**
 * Author: Anton Sadovnikoff
 * Email:  sadovnikoff@gmail.com
 */

namespace siddthartha\geo\area\tests\unit\geoArea;


use siddthartha\geo\area\tests\unit\BaseTestCase;
use siddthartha\geo\area\helpers\GeoAreaCalculator;

class GeoAreaTest extends BaseTestCase
{

        public function testGeoArea()
        {
                $area = GeoAreaCalculator::getArea(
                                [
                                        [ -10.812317,  18 ],
                                        [  10.812317, -18 ],
                                        [  26.565051,  18 ],
                                        [  52.622632, -18 ],
                                        [  52.622632,  54 ],
                                        [  10.812317,  54 ],
                                        [ -10.812317,  18 ],
                                ] 
                );

                $this->assertTrue( floor( $area ) == 33953235 );
        }
}
