# geo-area-calculator

Вычисление площади полигона в координатах WGS'84. Порт Python-алгоритма на PHP и JS.
Статья источник: http://gis-lab.info/qa/polygon-area-sphere-ellipsoid.html

# Установка
В корне модуля:
```sh
.../geo-area-calculator$ composer install
```
Если необходимы js-тесты:
```sh
.../geo-area-calculator$ npm install
```
# Тесты
PHP
-----
```sh
.../geo-area-calculator$ phpunit
```

JavaScript
-----
```sh
.../geo-area-calculator$ npm test
```

# Примеры

JavaScript
-----
```js
alert( FFGeo.getGeoPolygonArea(
			[
				[ -10.812317, 18 ],
				[ 10.812317, -18 ],
				[ 26.565051,  18 ],
				[ 52.622632, -18 ],
				[ 52.622632,  54 ],
				[ 10.812317,  54 ],
				[ -10.812317, 18 ],
			]
) );
```

PHP
-----
```php
use siddthartha\geo\area\helpers\GeoAreaCalculator;

echo GeoAreaCalculator::getArea(
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
// 33953235824742.51
```