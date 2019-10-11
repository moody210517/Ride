<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
$max_radius=100000;
?>
<style>
.yellowbox {
    background: #f7941d none repeat scroll 0 0 !important;
    border: 1px solid #f7941d;
}
.center_driver_mode {
    margin-top: 20px;
    background: none !important;
     width: 98.0%;
}
#location {
    clear: both;
    height: 34px;
    margin: 0 10px 15px 0;
    width: 50%;
}
.map_users .widget_content{
	background: none !important;
	padding: 0px !important;
}
#btn_find {
    background-color: #e84c3d ;
    border:1px solid #e84c3d ;
    border-radius: 3px;
    box-shadow: none;
    color: #fff;
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    font-weight: 400;
    height: auto;
    line-height: 1.42857 !important;
    margin-bottom: 0;
    padding: 6px 25px;
    text-align: center;
    text-shadow: none;
    vertical-align: top;
    white-space: nowrap;
}
.activities_s{
	width: 31.9%;
   
    
}
.block_label small{
	 padding-top: 30px;
}
div#content .grid_container h3
{
    margin-bottom: 0px;
    padding-bottom: 0px;
    width: 90%;
    margin-left: 3% !important; 
    font-weight:normal !important;
}
</style>

<div id="content" class="map_users">
		<div class="grid_container">
			<div class="grid_12">
	
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_map_view_display_drivers') != '') echo stripslashes($this->lang->line('admin_map_view_display_drivers')); else echo 'Display available drivers in their location'; ?><?php if($address != '') {?>
									<b><?php if ($this->lang->line('admin_map_drivers_nears') != '') echo stripslashes($this->lang->line('admin_map_drivers_nears')); else echo '- Drivers Near'; ?> : </b><?php echo $address; ?>
									<?php } ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>
                    		 <div class="grid_12 center_driver_mode">
                             
				<div class="widget_wrap">
				   <div class="widget_content">
                            
							<div class="social_activities mautonew1  auto_dash last-activity">	
									
									<div class="activities_s site_statistics">
										<div class="block_label">
										<small>Total Drivers</small>
											<span><?php if (isset($total_drivers)) echo $total_drivers; ?></span>
											
											<div class="user-img-admin">
											<i class="user_icon_font fa fa-users"></i>
											</div>
										</div>
									</div>
                                    <div class="activities_s site_statistics">
										<div class="block_label">
										<small><?php if ($this->lang->line('admin_map_online_drivers') != '') echo stripslashes($this->lang->line('admin_map_online_drivers')); else echo 'Online Drivers'; ?></small>
											<span><?php if (isset($online_drivers)) echo $online_drivers; ?></span>
											
											<div class="user-img-admin">
											<i class="user_icon_font fa fa-users"></i>
											</div>
										</div>
									</div>								
									<div class="activities_s site_statistics">
										<div class="block_label">
										<small><?php if ($this->lang->line('admin_map_offline_drivers') != '') echo stripslashes($this->lang->line('admin_map_offline_drivers')); else echo 'Offline Drivers'; ?></small>
											<span><?php if (isset($offline_drivers)) echo $offline_drivers; ?></span>
												
											<div class="user-img-admin">
											<i class="user_icon_font fa fa-users"></i>
											</div>
										</div>
									</div>							
									<div class="activities_s site_statistics">
										<div class="block_label">
										<small><?php if ($this->lang->line('admin_map_on_ride_drivers') != '') echo stripslashes($this->lang->line('admin_map_on_ride_drivers')); else echo 'On Ride Drivers'; ?></small>
											<span><?php if (isset($onride_drivers)) echo $onride_drivers; ?></span>
											
											<div class="user-img-admin">
											<i class="user_icon_font fa fa-users"></i>
											</div>
										</div>
									</div>								
								
							</div>
					
					</div>
				</div>
			</div>



					<div class="widget_content">
							<?php
							$attributes = array('class' => 'form_container left_label', 'id' => 'map_view_drivers','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
							echo form_open(ADMIN_ENC_URL.'/map/map_avail_drivers', $attributes)
							?>
								<div class="grid_12">
								<select name="city" id="city" class="form-control" ><option value="">Select City</option>
								<?Php if($location_detail->num_rows()>0){
								
								 foreach($location_detail->result() as $location_detail){
								?>
								<option value="<?php echo$location_detail->city;?>" <?php $city=$this->input->get('city'); if(isset($city)){if($city==$location_detail->city){echo"Selected";}}?>><?php echo$location_detail->city;?></option>
								<?php } } ?>
								</select>
								<select name="radius" id="radius" class="form-control" ><option value="">Select Radius</option>
								<?php for($i=500;$i<=$max_radius;$i=$i+500){?>
								<option value="<?php echo $i;?>" <?php $radius=$this->input->get('radius'); if(isset($radius)){if($radius==$i){echo"Selected";}}?>><?php echo $i/1000;?> K.M</option>
								<?Php }?>
								
								</select>
							<input name="location" id="location" type="text"  class="form-control" value="<?php $location=$this->input->get('location');if(!empty($location)){ echo $location; } ?>" autocomplete="off" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>"/>
								<button type="submit" class="btn" id="btn_find" ><?php if ($this->lang->line('admin_map_find') != '') echo stripslashes($this->lang->line('admin_map_find')); else echo 'Find'; ?></button>
								<?php $location=$this->input->get('location');if((isset($city)&&$city!='')||(isset($radius)&&$radius!='')||(isset($location)&&$location!='')){ ?>
								<a href= "<?php echo ADMIN_ENC_URL ?>/map/map_avail_drivers" class="btn"  id="btn_find" original-title="<?php if ($this->lang->line('admin_notification_remove_filter') != '') echo stripslashes($this->lang->line('admin_notification_remove_filter')); else echo 'Remove Filter'; ?>">
											<span class="icon" >Remove Filter</span>
										</a>
								<?php } ?>
								</div>
								
								<div class="grid_12">
                                    <div id="map_canvas" style="width:100%; height:450px;"></div>
									<?php #echo $mapContent['js']; ?>
									<?php #echo $mapContent['html']; ?>
								</div>
								
							</form>
					</div>
                                        <div class="widget_top">
						<span class="h_icon list"></span>
						<h6>Online Driver's</h6>
                        <div id="widget_tab">
            			</div>
					</div>


				

                    <div class="widget_content">
					
						<table class="display display_tbl" id="area_zone_tbl">
							<thead>
								<tr>
									<th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                        Driver Name
									</th>
                                    
                                    <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                        Phone Number
									</th>
                                    <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                        Last Active Time
									</th>
                                    <th class="tip_top" title="<?php if ($this->lang->line('dash_click_sort') != '') echo stripslashes($this->lang->line('dash_click_sort')); else echo 'Click to sort'; ?>">
                                        Track Map (Last 2 hours)
									</th>
									
								</tr>
							</thead>
							<tbody>
								<?php 
								#echo "<pre>"; print_r($driverList);
								if (count($driverList) > 0){
									foreach ($driverList as $row){
									$current=time()-300;
                                    if(isset($row['last_active_time'])) {
                                        $last_active_time=MongoEPOCH($row['last_active_time']);
                                    }
               						if($row['availability']=='Yes' && $row['mode'] == 'Available' && isset($row['last_active_time']) && $last_active_time > $current){
								?>
								<tr>
									<td class="center">
										<?php echo $row['driver_name'];?>
									</td>
                                    
                                    
                                    <td class="center">
									 <?php echo $row['dail_code']."".$row['mobile_number'];?>
									 <!--<a  class="tip_top" href="javascript:void(0)"; onclick="show_popup('<?php echo (string)"marker_".$row['_id']; ?>')">View Driver</a>-->
                                     
                                        <a href="javascript:void(0)" onclick="myClick('<?php echo (string)$row['_id']; ?>');">View Driver</a>
									</td>
                                    <td class="center">
										<?php if(isset($row['last_active_time']))echo date('d-m-Y h:i:s a',MongoEPOCH($row['last_active_time']));?>
									</td>
                                    <td class="center">
										<a href="<?php echo base_url().ADMIN_ENC_URL; ?>/map/map_online_driver/<?php echo (string)$row['_id']; ?>" target="_blank">Track Map</a>
									</td>
                                    
									
								</tr>
								<?php 
									}
								 }
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>
										Driver Name
									</th>
									<th class="tip_top">									
										Phone Number
									</th>
									<th class="tip_top">
										Last Active Time
									</th>
									<th class="tip_top">
										Track Map (Last 2 hours)
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>

		<span class="clear"></span>
</div>
</div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?<?php echo $google_maps_api_key; ?>&sensor=false&language=en&libraries=places"></script>
<script type="text/javascript">
//<![CDATA[

var map; // Global declaration of the map
var lat_longs_map = new Array();
var markers_map = {};
var iw_map;
var placesService;
var placesAutocomplete;
window.markerStore = {};
iw_map = new google.maps.InfoWindow();
    
function initialize_map() {
    
    var myLatlng = new google.maps.LatLng(<?php echo $center; ?>);
    var myOptions = {
        zoom: 13,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        minZoom: 3,
        maxZoom: 24}
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var autocompleteOptions = {
        }
    var autocompleteInput = document.getElementById('location');
    
    placesAutocomplete = new google.maps.places.Autocomplete(autocompleteInput, autocompleteOptions);
    placesAutocomplete.bindTo('bounds', map);
 <?php foreach($marker as $driver) { ?>       
var myLatlng = new google.maps.LatLng(<?php echo $driver['lat']; ?>,<?php echo $driver['lon']; ?>);

    var marker_icon = {
        url: "<?php echo $driver['icon'];  ?>",
        scaledSize: new google.maps.Size(25,25)};
    
var markerOptions = {
    map: map,
    position: myLatlng,
    icon: marker_icon		
};
marker_<?php echo $driver['id'];  ?> = createMarker_map(markerOptions,"<?php echo $driver['id'];  ?>");

marker_<?php echo $driver['id'];  ?>.set("content", "<?php echo $driver['infowindow_content'];  ?>");

google.maps.event.addListener(marker_<?php echo $driver['id'];  ?>, "click", function(event) {
    iw_map.setContent(this.get("content"));
    iw_map.open(map, this);

});
<?php } ?>
}

function createMarker_map(markerOptions,driver_id) {
var marker = new google.maps.Marker(markerOptions);
markerStore[driver_id] = marker;
lat_longs_map.push(marker.getPosition());
return marker;
}
function placesCallback(results, status) {
    if (status == google.maps.places.PlacesServiceStatus.OK) {
        for (var i = 0; i < results.length; i++) {
            
            var place = results[i];
        
            var placeLoc = place.geometry.location;
            var placePosition = new google.maps.LatLng(placeLoc.lat(), placeLoc.lng());
            var markerOptions = {
                map: map,
                position: placePosition
            };
            var marker = createMarker_map(markerOptions);
            marker.set("content", place.name);
            google.maps.event.addListener(marker, "click", function() {
                iw_map.setContent(this.get("content"));
                iw_map.open(map, this);
            });
            
            lat_longs_map.push(placePosition);
        
        }
        
    }
}

google.maps.event.addDomListener(window, "load", initialize_map);

//]]>
</script>
<script>
function myClick(id){
        google.maps.event.trigger(markerStore[id], 'click');
}
</script>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>