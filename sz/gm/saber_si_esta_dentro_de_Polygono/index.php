<script>

function inside(point, vs) {
    // ray-casting algorithm based on
    // http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html

    var x = point[0], y = point[1];

    var inside = false;
    for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
        var xi = vs[i][0], yi = vs[i][1];
        var xj = vs[j][0], yj = vs[j][1];

        var intersect = ((yi > y) != (yj > y))
            && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
    }

    return inside;
};

// array of coordinates of each vertex of the polygon
var polygon = [ [15.520376, 38.231155],[15.160243, 37.444046],[15.309898, 37.134219],[15.099988, 36.619987],[14.335229, 36.996631],[13.826733, 37.104531],[12.431004,37.61295],[12.570944,38.126381],[13.741156, 38.034966],[14.761249, 38.143874],[15.520376, 38.231155]];
  

var a=inside([14.874409, 38.156826], polygon); // true,, 

alert(a);

</script>