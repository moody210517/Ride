<?php
$this->load->view('site/templates/profile_header');
?> 

<section class="favlocation profile_pic_sec row">
    <div  class="profile_login_cont">
        <?php $this->load->view('site/templates/profile_sidebar'); ?>
		<div class="share_detail">
			<div class="share_det_title">
				<h2><?php if ($this->lang->line('site_user_my_favourite_locations') != '') echo stripslashes($this->lang->line('site_user_my_favourite_locations')); else echo 'MY FAVOURITE LOCATIONS'; ?></span></h2>
			</div>

			<?php $favLocations = array(); 
	            if(isset($favouriteList->row()->fav_location)){
					$favLocations = $favouriteList->row()->fav_location;
				} 
			?>

			<div class="profile_ac_inner_det">
				<div class="profile_ac_form favloc">

					<?php if(count($favLocations) > 0 ){ ?>
						<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th><?php if ($this->lang->line('admin_cms_title') != '') echo stripslashes($this->lang->line('admin_cms_title')); else echo 'Title'; ?></th>
									<th><?php if ($this->lang->line('admin_location_and_fare_location_details') != '') echo stripslashes($this->lang->line('admin_location_and_fare_location_details')); else echo 'Location Details'; ?></th>
									<th colspan="2"><?php if ($this->lang->line('admin_common_action') != '') echo stripslashes($this->lang->line('admin_common_action')); else echo 'Action'; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($favLocations as $key => $locations) { ?>
								<tr>
									<td><span class="char-limit-25"><?php echo $locations['title'];?></span></td>
									<td><?php echo $locations['address'];?></td>
									<td><a href="#" data-toggle="modal" onclick="updateAddrKey('<?php echo $key; ?>');" data-target="#make-fav"><?php if ($this->lang->line('admin_common_edit') != '') echo stripslashes($this->lang->line('admin_common_edit')); else echo 'Edit'; ?></a></td>
									<td><a href="#" data-toggle="modal" onclick="updateAddrKey('<?php echo $key; ?>');" data-target="#make-unfav"><?php if ($this->lang->line('site_user_remove') != '') echo stripslashes($this->lang->line('site_user_remove')); else echo 'Remove'; ?></a></td>
								</tr>
								<input type="hidden" id="addrTitle<?php echo $key; ?>" value="<?php echo $locations['title'];?>" />
								<input type="hidden" id="address<?php echo $key; ?>" value="<?php echo $locations['address'];?>" />
								<input type="hidden" id="longitude<?php echo $key; ?>" value="<?php echo $locations['geo']['longitude'];?>" />
								<input type="hidden" id="latitude<?php echo $key; ?>" value="<?php echo $locations['geo']['latitude'];?>" />
								<?php } ?>
							</tbody>
						</table>
					<?php }else{ ?>
						<div class="rate_title">
							<center><?php if ($this->lang->line('no_favourite_locations_added') != '') echo stripslashes($this->lang->line('no_favourite_locations_added')); else echo 'No Favourite locations added'; ?></center>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>	

<input type="hidden" id="addrKey" value="" />
<input type="hidden" id="user_id" value="<?php if(isset($favouriteList->row()->user_id)) echo (string)$favouriteList->row()->user_id;?>" />

<div class="modal fade" id="make-fav" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php if ($this->lang->line('edit_favourite_location') != '') echo stripslashes($this->lang->line('edit_favourite_location')); else echo 'Edit Favourite Location'; ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text" style="min-height: 75px;">
				
					<?php 
								
						if ($this->lang->line('user_favourite_title') != '') $placeholder = stripslashes($this->lang->line('user_favourite_title')); else $placeholder = 'Favourite location title';
						
						
						$input_data = array(
										'type' => 'text',
										'id' => 'favourite_title',
										'class' => 'form-control sign_in_text required',
										'placeholder' => $placeholder,
										'value' => '',
                                        'maxlength' => '30'
						);
						echo form_input($input_data);
					?>
				
					<span id="FavErr" class="favErr"></span>
				</div>
				
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return makeLocFav();" id="cont-btn"><?php if ($this->lang->line('user_continue') != '') echo stripslashes($this->lang->line('user_continue')); else echo 'Continue'; ?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="make-unfav" role="dialog" aria-labelledby="myModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content favourite_container">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title confirm-title" id="myModalLabel1">  <?php if ($this->lang->line('user_are_you_confirm') != '') echo stripslashes($this->lang->line('user_are_you_confirm')); else echo 'Are you sure'; ?>!</h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 text-left sign_driver_text">
					<span> <?php if ($this->lang->line('user_remove_fav_loc_confirm') != '') echo stripslashes($this->lang->line('user_remove_fav_loc_confirm')); else echo 'Do you want to remove this location from your favourite list'; ?>? </span>
					<span id="FavErr1" class="favErr"></span>
				</div>
				
				
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return makeLocUnFav();" id="cont-btn1"><?php if ($this->lang->line('user_yes') != '') echo stripslashes($this->lang->line('user_yes')); else echo 'Yes'; ?></button>
            </div>
        </div>
    </div>
</div>


<script>


<?php if ($this->lang->line('rider_fav_title_updated') != ''){ ?>
var favAdded = "<?php echo stripslashes($this->lang->line('rider_fav_title_updated')); ?>";
<?php }else{ ?>
var favAdded = "Favourite location title updated successfully";
<?php } ?>
<?php if ($this->lang->line('user_fav_location_removed') != ''){ ?>
var favRemoved = "<?php echo stripslashes($this->lang->line('user_fav_location_removed')); ?>";
<?php }else{ ?>
var favRemoved = "Location removed from your favourite list";
<?php } ?>

	function updateAddrKey(row){
		$('#addrKey').val(row); 
		var cur_title = $('#addrTitle'+row).val();
		$('#favourite_title').val(cur_title);
	}
	function makeLocFav(){
		var lkey = $('#addrKey').val();
		var fav_title = $('#favourite_title').val().trim();
		var address = $('#address'+lkey).val();
		var user_id = $('#user_id').val();
		var longitude = $('#longitude'+lkey).val();
		var latitude = $('#latitude'+lkey).val();
		$('#favourite_title').css('border-color','none');
		$('#FavErr').css('display','none');
		if(fav_title != ''){
			$('#cont-btn').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'site/rider/edit_favourite_location',
			    data: {'title':fav_title,'address':address,'user_id':user_id,'longitude':longitude,'latitude':latitude,'location_key':lkey},
			    dataType: 'json',
				success:function(res){
					$('#FavErr').css('display','block');
					$('#cont-btn').html('<?php if ($this->lang->line('user_continue') != '') echo stripslashes($this->lang->line('user_continue')); else echo 'Continue'; ?>');
                    
					if(res.status == '1'){ 
						$('#FavErr').css('color','green');
						$('#FavErr').html(favAdded);
						location.reload();
					} else {
						$('#FavErr').css('color','red');
						$('#FavErr').html(res.message);
					}
				} 
			});
		} else {
			$('#favourite_title').css('border-color','red');	
		}
	}
	
	function makeLocUnFav(){
		var user_id = $('#user_id').val();
		var favLocKey = $('#addrKey').val();
		$('#FavErr1').css('display','none');
		if(favLocKey != ''){
			$('#cont-btn1').html('<img src="images/indicator.gif">');
			$.ajax({
			    type: "POST",
			    url: 'site/rider/remove_favourite_location',
			    data: {'user_id':user_id,'location_key':favLocKey},
			    dataType: 'json',
				success:function(res){
					$('#FavErr1').css('display','block');
					$('#cont-btn').html('<?php if ($this->lang->line('user_yes') != '') echo stripslashes($this->lang->line('user_yes')); else echo 'Yes'; ?>');
					if(res.status == '1'){ 
						$('#FavErr1').css('color','green');
						$('#FavErr').html(favRemoved);
						location.reload();
					} else { 
						$('#FavErr1').css('color','red'); 
						$('#FavErr1').html(res.message);
					}
				} 
			});
		} else {
			alert('Please refresh this page and try again');
		}
	}
	

</script>
<style>
.modal-footer {
    clear: both;
}
#FavErr ,#FavErr1 {
	padding-left:12px;
}
</style>

<script>
$(document).ready(function() {
    
    $('.modal').on('hidden.bs.modal', function(){ 
        $('#FavErr').hide();
    });

	$('#example').dataTable( {
		"language": {
		"paginate": {
		  "previous": "<i class='fa fa-angle-left' aria-hidden='true'></i>",
		  "next": "<i class='fa fa-angle-right' aria-hidden='true'></i>"
		}
		},
		"searching": false
	});
});
</script>
<?php
$this->load->view('site/templates/footer');
?>