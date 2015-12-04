test("Test area of polygon WGS84", function() {
    var area = FFGeo.getGeoPolygonArea([
        [-10.812317, 18],
        [10.812317, -18],
        [26.565051, 18],
        [52.622632, -18],
        [52.622632, 54],
        [10.812317, 54],
    ]);

    var area2 = FFGeo.getGeoPolygonArea([
        [-10.812317, 18],
        [10.812317, -18],
        [26.565051, 18],
        [52.622632, -18],
        [52.622632, 54],
        [10.812317, 54],
    ]);
 
    ok( Math.floor( area2 ) === 33953235824742 );
});
