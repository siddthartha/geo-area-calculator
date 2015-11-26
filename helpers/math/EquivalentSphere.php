<?php

/*
 * Author: Anton Sadovnikoff
 * E-mail: sadovnikoff@gmail.com
 * 
 * Description: 
 * Вспомогательные функции для отображения поверхности эллипсоида на сферу
 * портирование Python алгоритма на Php 
 * источник: http://gis-lab.info/qa/polygon-area-sphere-ellipsoid.html
 */

namespace bigland\geo\area\helpers\math;

/**
 * Description of Sphere
 *
 * @author siddthartha
 */
class EquivalentSphere
{
        public $R_auth;
        public $to_auth2;
        public $to_auth4;
        public $to_auth6;

        public $a_e = 6371.0;

        /**
         * 
         * @param float $a
         * @param float $f
         */
        public function __construct( $a, $f )
        {
                $b         = $a * (1. - $f);
                $e2        = $f * (2. - $f);
                $this->R_auth    = $b * sqrt( 1. + self::getPowSeries( $e2, 2. / 3., 3. / 5., 4. / 7. ) );
                $this->to_auth_2 = self::getPowSeries( $e2, -1. / 3., -31. / 180., -59. / 560. );
                $this->to_auth_4 = self::getPowSeries( $e2, 0., 17. / 360., 61. / 1260. );
                $this->to_auth_6 = self::getPowSeries( $e2, 0., 0., -383. / 45360. );
        }
        
        /**
         * 
         * @param float $x
         * @param float $p1
         * @param float $p2
         * @param float $p3
         * @return float
         */
        public static function getPowSeries( $x, $p1, $p2, $p3 )
        {
                return ( $p1 + ( $p2 + $p3 * $x ) * $x ) * $x;
        }
        
        /**
         * 
         * @param float $x
         * @param float $t2
         * @param float $t4
         * @param float $t6
         * @return float
         */
        public static function getTrigSeries( $x, $t2, $t4, $t6 )
        {
                return $x + $t2 * sin( 2. * $x ) + $t4 * sin( 4. * $x ) + $t6 * sin( 6. * $x );
        }
 
        public static function spherToCart( $lat, $lon )
        {
                $x = cos( $lat ) * cos( $lon );
                $y = cos( $lat ) * sin( $lon );
                $z = sin( $lat );
                return [ $x, $y, $z ];
        }

        public static function cartToSpher( $x, $y, $z )
        {
                $lat = atan2( $z, sqrt( $x * $x + $y * $y ) );
                $lon = atan2( $y, $x );
                return [$lat, $lon ];
        }

        public static function rotate( $x, $y, $a )
        {
                $c = cos( $a );
                $s = sin( $a );
                $u = $x * $c + $y * $s;
                $v = -$x * $s + $y * $c;
                return [ $u, $v ];
        }

        public static function inverse( $lat1, $lon1, $lat2, $lon2 )
        {
                $c    = self::spherToCart( $lat2, $lon2 );
                $x    = $c[ 0 ];
                $y    = $c[ 1 ];
                $z    = $c[ 2 ];
                $r1   = self::rotate( $x, $y, $lon1 );
                $x    = $r1[ 0 ];
                $y    = $r1[ 1 ];
                $r2   = self::rotate( $z, $x, M_PI / 2 - $lat1 );
                $z    = $r2[ 0 ];
                $x    = $r2[ 1 ];
                $s    = self::cartToSpher( $x, $y, $z );
                $lat  = $s[ 0 ];
                $lon  = $s[ 1 ];
                $dist = M_PI / 2 - $lat;
                $azi  = M_PI - $lon;
                return [ $dist, $azi ];
        }

        
        public static function direct( $lat1, $lon1, $dist, $azi )
        {
                $c    = self::spherToCart( M_PI / 2 - $dist, M_PI - $azi );
                $x    = $c[ 0 ];
                $y    = $c[ 1 ];
                $z    = $c[ 2 ];
                $r2   = self::rotate( $z, $x, $lat1 - M_PI / 2 );
                $z    = $r2[ 0 ];
                $x    = $r2[ 1 ];
                $r1   = self::rotate( $x, $y, -$lon1 );
                $x    = $r1[ 0 ];
                $y    = $r1[ 1 ];
                $s    = self::cartToSpher( $x, $y, $z );
                $lat2 = $s[ 0 ];
                $lon2 = $s[ 1 ];
                return [ $lat2, $lon2 ];
        }
        
        public static function angular( $lat1, $lon1, $lat2, $lon2, $azi13, $azi23 )
        {
                $failure    = false;
                $i1         = inverse( $lat2, $lon2, $lat1, $lon1 );
                $dist12     = $i1[ 0 ]; //? 12 or 21 ?
                $azi21      = $i1[ 1 ];
                $i2         = inverse( $lat1, $lon1, $lat2, $lon2 );
                $dist12     = $i2[ 0 ];
                $azi12      = $i2[ 1 ];
                $cos_beta1  = cos( $azi13 - $azi12 );
                $sin_beta1  = sin( $azi13 - $azi12 );
                $cos_beta2  = cos( $azi21 - $azi23 );
                $sin_beta2  = sin( $azi21 - $azi23 );
                $cos_dist12 = cos( $dist12 );
                $sin_dist12 = sin( $dist12 );
                if( $sin_beta1 == 0. and $sin_beta2 == 0. )
                {
                        $failure = true;
                        $lat3    = 0.;
                        $lon3    = 0.;
                }
                elseif( $sin_beta1 == 0. )
                {
                        $lat3 = $lat2;
                        $lon3 = $lon2;
                }
                elseif( $sin_beta2 == 0. )
                {
                        $lat3 = $lat1;
                        $lon3 = $lon1;
                }
                elseif( $sin_beta1 * $sin_beta2 < 0. )
                {
                        if( abs( $sin_beta1 ) >= abs( $sin_beta2 ) )
                        {
                                $cos_beta2 = -$cos_beta2;
                                $sin_beta2 = -$sin_beta2;
                        }
                        else
                        {
                                $cos_beta1 = -$cos_beta1;
                                $sin_beta1 = -$sin_beta1;
                        }
                }
                else
                {
                        $dist13 = atan2( abs( $sin_beta2 ) * $sin_dist12,
                                              $cos_beta2 * abs( $sin_beta1 ) + abs( $sin_beta2 ) * $cos_beta1 * $cos_dist12 );
                        $d      = direct( $lat1, $lon1, $dist13, $azi13 );
                        $lat3   = $d[ 0 ];
                        $lon3   = $d[ 1 ];
                }
                return [ $failure, $lat3, $lon3 ];
        }

        
        
        public static function linear( $lat1, $lon1, $lat2, $lon2, $dist13, $dist23, $clockwise )
        {
                $failure = false;
                if( $dist13 == 0. )
                {
                        $lat3 = $lat1;
                        $lon3 = $lon1;
                }
                elseif( $dist23 == 0. )
                {
                        $lat3 = $lat2;
                        $lon3 = $lon2;
                }
                else
                {
                        $i1        = self::inverse( $lat1, $lon1, $lat2, $lon2 );
                        $dist12    = $i1[ 0 ];
                        $azi12     = $i1[ 1 ];
                        $cos_beta1 = (cos( $dist23 ) - cos( $dist12 ) * cos( $dist13 )) / (sin( $dist12 ) * sin( $dist13 ));
                        if( abs( $cos_beta1 ) > 1. )
                        {
                                $failure = true;
                                $lat3    = 0.;
                                $lon3    = 0.;
                        }
                        else
                        {
                                if( $clockwise )
                                {
                                        $azi13 = $azi12 + acos( $cos_beta1 );
                                }
                                else
                                {
                                        $azi13 = $azi12 - acos( $cos_beta1 );
                                }
                                $d1   = self::direct( $lat1, $lon1, $dist13, $azi13 );
                                $lat3 = $d1[ 0 ];
                                $lon3 = $d1[ 1 ];
                                return [ $failure, $lat3, $lon3 ];
                        }
                }
        }
                        /*




def angular(lat1, lon1, lat2, lon2, azi13, azi23):
    failure = False
    dist12, azi21 = inverse(lat2, lon2, lat1, lon1)
    dist12, azi12 = inverse(lat1, lon1, lat2, lon2)
    cos_beta1 = math.cos(azi13 - azi12)
    sin_beta1 = math.sin(azi13 - azi12)
    cos_beta2 = math.cos(azi21 - azi23)
    sin_beta2 = math.sin(azi21 - azi23)
    cos_dist12 = math.cos(dist12);
    sin_dist12 = math.sin(dist12);
    if sin_beta1 == 0. and sin_beta2 == 0.:
        failure = True
        lat3 = 0.
        lon3 = 0.
    elif sin_beta1 == 0.:
        lat3 = lat2
        lon3 = lon2
    elif sin_beta2 == 0.:
        lat3 = lat1
        lon3 = lon1
    elif sin_beta1 * sin_beta2 < 0.:
        if math.fabs(sin_beta1) >= math.fabs(sin_beta2):
            cos_beta2 = -cos_beta2
            sin_beta2 = -sin_beta2
        else:
            cos_beta1 = -cos_beta1
            sin_beta1 = -sin_beta1
    else:
        dist13 = math.atan2(math.fabs(sin_beta2) * sin_dist12, cos_beta2 * math.fabs(sin_beta1) + math.fabs(sin_beta2) * cos_beta1 * cos_dist12)
        lat3, lon3 = direct(lat1, lon1, dist13, azi13)
    return (failure, lat3, lon3)

def linear(lat1, lon1, lat2, lon2, dist13, dist23, clockwise):
    failure = False
    if dist13 == 0.:
        lat3 = lat1
        lon3 = lon1
    elif dist23 == 0.:
        lat3 = lat2
        lon3 = lon2
    else:
        dist12, azi12 = inverse(lat1, lon1, lat2, lon2)
        cos_beta1 = (math.cos(dist23) - math.cos(dist12) * math.cos(dist13)) / (math.sin(dist12) * math.sin(dist13))
        if math.fabs(cos_beta1) > 1.:
            failure = True
            lat3 = 0.
            lon3 = 0.
        else:
            if clockwise:
                azi13 = azi12 + math.acos(cos_beta1)
            else:
                azi13 = azi12 - math.acos(cos_beta1)
            lat3, lon3 = direct(lat1, lon1, dist13, azi13)
    return (failure, lat3, lon3)

 
  */        
}
