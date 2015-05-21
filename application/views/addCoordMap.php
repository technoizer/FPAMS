<!DOCTYPE html>
<html>
<head>
  <title>Add Road Coordianate</title>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <style>
  html, body, #map-canvas {
    height: 100%;
    margin: 0px;
    padding: 0px;
  }
  #panel {
    position: absolute;
    top: 5%;
    left: 99%;
    margin-left: -225px;
    z-index: 5;
    background-color: #fff;
    padding: 15px;
    border: 1px solid #999;
  }
  </style>
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=geometry"></script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
  <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  <script>
        //var poly;
        var polys = new Array();
        var geodesicPoly;
        var distance = 0;
        var marker = new Array();
        var geos = new Array();
        var map;
        var path = new Array();
        var infow = new Array();
        var flag = false;
        var polyOptions;
        var geodesicOptions;
        function initialize() {
          var mapOptions = {
            zoom: 13,
            center: new google.maps.LatLng(-7.27, 112.76)
          };

          map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

          map.controls[google.maps.ControlPosition.TOP_CENTER].push(document.getElementById('info'));

          google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng);
          });

          polyOptions = {
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 3,
            map: map,
          };
        }


        function placeMarker(location) {
          var markerTest = new google.maps.Marker({
            map: map,
            position: location,
            draggable: false
          });
          var distance;
          marker.push(markerTest);      
          path.push(location);
          if (marker.length > 1){
            var arr = [marker[marker.length-2].getPosition(), marker[marker.length - 1].getPosition()];
            var poly = new google.maps.Polyline(polyOptions);
            poly.setPath(arr);
            polys.push(poly); 
          }
        }

        function changePath(location){
          
          for (i=0;i<polys.length;i++){
            var arr = [marker[i].getPosition(),marker[i+1].getPosition()];
            polys[i].setPath(arr);
          }
        }

        function deleteLast(){
          if (marker.length>0){
            marker[marker.length - 1].setMap(null);
            marker.pop();
            path.pop();
          }
          if (polys.length>0){
            polys[polys.length - 1 ].setMap(null);
            polys.pop();
          }
          changePath();
        }

        function printCoordinate(){
            //alert(path.toString());
            var name = $('#namajalan').val();
            var coord = path.toString();
            $.post( "/ams/index.php/map/insert", { name: name, coord: coord },function(data){
              alert('success');
            });
        }

      google.maps.event.addDomListener(window, 'load', initialize);

      </script>
    </head>
    <body>
      <div id="map-canvas"></div>
      <div id="panel">
        <center><b>INFORMATION</b></center>
        <hr>
        ID Jalan : <br>
        <input type="text" id="namajalan"><br>
        <br>

        <center><b>TOOLS</b>
          <hr>
          <input type="button" value = "Delete Last Marker"readonly onclick="deleteLast()">
          <input type="button" value = "Print Coordinate"readonly onclick="printCoordinate()">
        </div>
      </body>
      </html>