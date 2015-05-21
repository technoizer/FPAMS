<!DOCTYPE html>
<html>
<head>
  <title>Twitter Based Road Traffic Analysis</title>
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
        var data;
        $(document).ready(function(){
          refreshRoad();
        });

        function initialize() {
          var mapOptions = {
            zoom: 13,
            center: new google.maps.LatLng(-7.27, 112.76)
          };

          map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

          map.controls[google.maps.ControlPosition.TOP_CENTER].push(document.getElementById('info'));

          $('#namajalan').val();
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

        function refreshRoad(){
          $.ajax({
              url : 'index.php/map/getData',
              type : 'post',
              dataType : 'json',
              success : function(result)
              {      
                  for (var i in result[['road']]) {
                    var weight = parseInt(result['road'][i]['road_weight']);
                    var red = weight;
                    var green = 255-weight;
                    if (red<16){
                      var color = "#" + red.toString(16).toUpperCase() + "0" + green.toString(16).toUpperCase() + "00";
                    }
                    else if (green<16){
                      var color = "#" + red.toString(16).toUpperCase()+ green.toString(16).toUpperCase() + "0" + "00";
                    }
                    else{
                      var color = "#" + red.toString(16).toUpperCase() + green.toString(16).toUpperCase() + "00"; 
                    }
                    polyOptions = {
                      strokeColor: color,
                      strokeOpacity: 1.0,
                      strokeWeight: 3,
                      map: map,
                    };
                    var poly = new google.maps.Polyline(polyOptions);
                    var coordinate = result['road'][i]['road_coord'].split(';');
                    var lat;
                    var lng;
                    var pathRoad = new Array;
                    var count = 0;
                    for (var j in coordinate){
                      lat = coordinate[j].split(',')[0];
                      lng = coordinate[j].split(',')[1];
                      var arr = new google.maps.LatLng(lat,lng);
                      pathRoad.push(arr);
                      count++;
                    }
                    poly.setPath(pathRoad);
                    polys.push(poly);
                    var valKepadatan = result['road'][i]['road_weight'];
                    var kepadatan;
                    if (valKepadatan < 31)
                      kepadatan = "lancar";
                    else if (valKepadatan < 63)
                      kepadatan = "ramai lancar";
                    else if (valKepadatan < 127)
                      kepadatan = "padat merayap";
                    else if (valKepadatan < 191)
                      kepadatan = "padat merambat";
                    else
                      kepadatan = "macet";
                    
                    msg = "<center>" + result['road'][i]['road_name'] + "</center><br>Status Kepadatan : <b>" + kepadatan + "</b><br> Last Update : " + result['road'][i]['last_updated'];
                    //console.log(result['road'][i]['road_name'] + " " + );

                    attachSecretMessage(poly, msg,pathRoad[1]);
                  }

              },
              error : function(res)
              {
              }
          });
        }

        function attachSecretMessage(poly, msg, location) {
          var infowindow = new google.maps.InfoWindow({
            content: msg
          });
          infowindow.setPosition(location);

          google.maps.event.addListener(poly, 'click', function() {
            infowindow.open(poly.get('map'), poly);
          });
        }

      google.maps.event.addDomListener(window, 'load', initialize);

      </script>
    </head>
    <body>
      <div id="map-canvas"></div>
      
      </body>
      </html>