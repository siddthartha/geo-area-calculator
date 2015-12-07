/* 
 * Author: Anton Sadovnikoff
 * E-mail: sadovnikoff@gmail.com
 * 
 * Description: 
 * Вычисление площади полигона в координатах WGS'84
 * портирование Python алгоритма на JavaScript 
 * источник: http://gis-lab.info/qa/polygon-area-sphere-ellipsoid.html
 */

(function (G, U) {
    var UI = G.FFGeo || {};

    function powSeries(x, p1, p2, p3) {
        return (p1 + (p2 + p3 * x) * x) * x;
    }

    // тригонометрический ряд
    function trigSeries(x, t2, t4, t6) {
        return x + t2 * Math.sin(2. * x) + t4 * Math.sin(4. * x) + t6 * Math.sin(6. * x);
    }

    // инициализация эквивалентной сферы
    function initSph(a, f) {
        var b = a * (1. - f);
        var e2 = f * (2. - f);
        var R_auth = b * Math.sqrt(1. + powSeries(e2, 2. / 3., 3. / 5., 4. / 7.));
        var to_auth_2 = powSeries(e2, -1. / 3., -31. / 180., -59. / 560.);
        var to_auth_4 = powSeries(e2, 0., 17. / 360., 61. / 1260.);
        var to_auth_6 = powSeries(e2, 0., 0., -383. / 45360.);
        return [R_auth, to_auth_2, to_auth_4, to_auth_6];
    }

    function deg2rad(angle) {
        return Math.PI / 180 * angle;
    }

    function spherToCart(lat, lon) {
        var x, y, z;
        x = Math.cos(lat) * Math.cos(lon);
        y = Math.cos(lat) * Math.sin(lon);
        z = Math.sin(lat);
        return [x, y, z];
    }


    function cartToSpher(x, y, z) {
        var lat, lon;

        lat = Math.atan2(z, Math.sqrt(x * x + y * y));
        lon = Math.atan2(y, x);
        return [lat, lon];
    }

    function rotate(x, y, a) {
        var c = Math.cos(a);
        var s = Math.sin(a);
        var u = x * c + y * s;
        var v = -x * s + y * c;
        return [u, v];
    }


    function inverse(lat1, lon1, lat2, lon2) {
        var z, y, z;
        var tmp = spherToCart(lat2, lon2);
        x = tmp[0];
        y = tmp[1];
        z = tmp[2];
        tmp = rotate(x, y, lon1);
        x = tmp[0];
        y = tmp[1];
        tmp = rotate(z, x, Math.PI / 2 - lat1);
        z = tmp[0];
        x = tmp[1];
        c = cartToSpher(x, y, z);
        var lat = c[0];
        var lon = c[1];
        var dist = Math.PI / 2 - lat;
        var azi = Math.PI - lon;
        return [dist, azi];
    }

    function getGeoPolygonArea(polygon)
    {
        // последняя точка копия первой
        polygon.push(polygon[0]);

        // большая полуось и сжатие
        var a = 6378137;
        var f = 1. / 298.257223563;

        // инициализировать эквивалентную сферу
        eSph = initSph(a, f);

        var r_auth = eSph[0];
        var to_auth_2 = eSph[1];
        var to_auth_4 = eSph[2];
        var to_auth_6 = eSph[3];

        var tau = 0.;
        var lat1 = 0.;
        var lon1 = 0.;
        
        for(var i= 1; i<=polygon.length; i++) {
            // прочитать долготу и широту
            var lat = deg2rad(polygon[i-1][0]);
            var lon = deg2rad(polygon[i-1][1]);

            // вычислить эквивалентную широту
            lat = trigSeries(lat, to_auth_2, to_auth_4, to_auth_6);

            if (i > 1) {
                // вычислить прямой азимут Qi - Qi+1
                tmp = inverse(lat1, lon1, lat, lon);
                dist = tmp[0];
                azi1 = tmp[1];

                if (i === 2) {
                    // запомнить азимут Q1 - Q2
                    azi0 = azi1;
                } else {
                    // вычислить поворот в i-й вершине
                    tau_i = 0.5 - (azi2 - azi1) / 2. / Math.PI;
                    // нормализовать величину поворота
                    tau_i = tau_i - Math.floor(tau_i + 0.5);
                    // добавить поворот к сумме поворотов
                    tau = tau + tau_i;
                }
                // вычислить обратный азимут Qi+1 - Qi
                tmp = inverse(lat, lon, lat1, lon1);
                dist = tmp[0];
                azi2 = tmp[1];
            }
            lon1 = lon;
            lat1 = lat;
            // some debug
            // console.log(JSON.stringify([ lat1, lon1 ]));
        }

        // вычислить поворот в 1-й вершине
        var tau_i = 0.5 - (azi2 - azi0) / 2. / Math.PI;
        // нормализовать величину поворота
        tau_i = tau_i - Math.floor(tau_i + 0.5);
        // добавить поворот к сумме поворотов
        tau = tau + tau_i;

        // вычислить площадь
        var area = 2. * Math.PI * (1. - Math.abs(tau)) * Math.pow(r_auth, 2);
        return area;
    }

    UI.getGeoPolygonArea = getGeoPolygonArea;
    G.FFGeo = UI;
    
}(this, undefined));
