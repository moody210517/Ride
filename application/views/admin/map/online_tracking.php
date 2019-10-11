<html>
<head>
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0; padding: 0 }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?<?php echo $google_maps_api_key; ?>&sensor=false">
</script>
<?php #echo "<pre>"; print_r($drivers_info->result()); ?>
<script type="text/javascript">

 function initialize() {

    var myOptions = {
      center: new google.maps.LatLng(33.890542, 151.274856),
      zoom: 12,
      mapTypeId: google.maps.MapTypeId.ROADMAP

    };
    var map = new google.maps.Map(document.getElementById("default"),
        myOptions);

    setMarkers(map)

  }

var marker, i;

function setMarkers(map){

      

<?php if($drivers_info->num_rows() >0) {
   $i=1;
   $total_drivers=count($drivers_info->result());
   
   foreach($drivers_info->result() as $data) {
?>

 var lat = "<?php echo $data->lat;  ?>";
 var long = "<?php echo $data->lon;  ?>";
 
  var infowindow = new google.maps.InfoWindow();
  latlngset = new google.maps.LatLng(lat, long);
  <?php if($i==1) {?>  
    var marker = new google.maps.Marker({  
          map: map,position: latlngset,
          icon:'<?php echo base_url(); ?>images/pickup_marker.png',
        });
    
 <?php } else if($i==$total_drivers){ ?>
        var marker = new google.maps.Marker({  
          map: map,position: latlngset,
          icon:'<?php echo base_url(); ?>images/drop_marker.png',
        });
        
 <?php } else { ?>
    var marker = new google.maps.Marker({  
          map: map,position: latlngset
          
        });
        
 <?php }?>
  google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          contentString="<?php echo date('d-m-Y h:i:s a',MongoEPOCH($data->updated_time)); ?>";
            infowindow.setContent(contentString);
          infowindow.open(map, marker);
        }
  })(marker, i));
  map.setCenter(marker.getPosition())
<?php $i++;} } ?>
}



  </script>
 </head>

 <body onload="initialize()">
  <div id="default" style="width:100%; height:100%"></div>
 </body>
  </html>
