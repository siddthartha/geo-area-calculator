<?php

/**
 * Author: Anton Sadovnikoff
 * Email:  sadovnikoff@gmail.com
 */

namespace bigland\geo\area\tests\unit\geoArea;


use bigland\geo\area\tests\unit\BaseTestCase;
use bigland\geo\area\helpers\GeoAreaCalculator;

class GeoAreaTest extends BaseTestCase
{

        public function testGeoArea()
        {
                $area = \bigland\geo\area\helpers\GeoAreaCalculator::getArea(
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
