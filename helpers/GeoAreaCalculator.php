<?php

/*
 * Author: Anton Sadovnikoff
 * E-mail: sadovnikoff@gmail.com
 * 
 * Description: 
 * Вычисление площади полигона в координатах WGS'84
 * портирование Python алгоритма на Php 
 * источник: http://gis-lab.info/qa/polygon-area-sphere-ellipsoid.html
 */

namespace bigland\geo\area\helpers;

use bigland\geo\area\helpers\math\EquivalentSphere;
use League\Geotools\Polygon\Polygon;

/**
 * Description of GeoAreaCalculator
 *
 * @author siddthartha
 */
class GeoAreaCalculator
{

        public static $a = 6378.137;
        public static $f = 1. / 298.257223563;

        public static function getArea( $coordinates )
        {
                $eqSph   = new EquivalentSphere( self::$a, self::$f );
                $polygon = new Polygon( $coordinates );

                $tau = 0.;
                $i   = 1;

                //TODO: обработка зацикливания координат последняя = первая
                //$polygon->add($polygon->getIterator()->current());
                
                foreach( $polygon->getCoordinates() as $coordinate )
                {
                        $lon = deg2rad( $coordinate->getLongitude() );
                        $lat = deg2rad( $coordinate->getLatitude() );

                        // вычислить эквивалентную широту
                        $lat = EquivalentSphere::getTrigSeries( $lat, $eqSph->to_auth_2,
                                                             $eqSph->to_auth_4, $eqSph->to_auth_6 );
                        if( $i > 1 )
                        {
                                // вычислить прямой азимут Qi - Qi+1
                                $qii1 = EquivalentSphere::inverse( $lat1, $lon1, $lat, $lon );
                                $dist = $qii1[ 0 ];
                                $azi1 = $qii1[ 1 ];
                                if( $i == 2 )
                                {
                                        // запомнить азимут Q1 - Q2
                                        $azi0 = $azi1;
                                }
                                else
                                {
                                        // вычислить поворот в i-й вершине
                                        $tau_i = 0.5 - ($azi2 - $azi1) / 2. / M_PI;
                                        // нормализовать величину поворота
                                        $tau_i = $tau_i - floor( $tau_i + 0.5 );
                                        // добавить поворот к сумме поворотов
                                        $tau   = $tau + $tau_i;
                                }
                                // вычислить обратный азимут Qi+1 - Qi
                                $qi1i = EquivalentSphere::inverse( $lat, $lon, $lat1, $lon1 );
                                $dist = $qi1i[ 0 ];
                                $azi2 = $qi1i[ 1 ];
                        }
                        $lon1 = $lon;
                        $lat1 = $lat;

                        $i = $i + 1;
                }

                // вычислить поворот в 1-й вершине
                $tau_i = 0.5 - ($azi2 - $azi0) / 2. / M_PI;
                // нормализовать величину поворота
                $tau_i = $tau_i - floor( $tau_i + 0.5 );
                // добавить поворот к сумме поворотов
                $tau   = $tau + $tau_i;

                // вычислить площадь
                $area = 2. * M_PI * (1. - abs( $tau )) * pow( $eqSph->R_auth, 2 );
                return $area;
        }

}