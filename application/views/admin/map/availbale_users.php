<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>

<style>
#location {
    clear: both;
    height:34px;
    margin:0 10px 15px 0;
    width: 50%;
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
</style>
<div id="content" class="map_users">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('admin_map_view_display_users') != '') echo stripslashes($this->lang->line('admin_map_view_display_users')); else echo 'Display available users in their location'; ?></h6>
                        <div id="widget_tab">
            			</div>
					</div>
					<div class="widget_content">
							<?php
							$attributes = array('class' => 'form_container left_label', 'id' => 'map_view_users','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
							echo form_open(ADMIN_ENC_URL.'/map/map_avail_users', $attributes)
							?>
								<div class="grid_12">
									<input name="location" id="location" type="text"  class="form-control" value="<?php if(isset($address)){ echo $address; } ?>" autocomplete="off" placeholder="<?php if ($this->lang->line('admin_enter_location') != '') echo stripslashes($this->lang->line('admin_enter_location')); else echo 'Enter location'; ?>"/>
								<button type="submit" class="btn" id="btn_find" ><?php if ($this->lang->line('admin_map_find') != '') echo stripslashes($this->lang->line('admin_map_find')); else echo 'Find'; ?></button>
								</div>
								
								<div class="grid_12">
									<?php echo $mapContent['js']; ?>
									<?php echo $mapContent['html']; ?>
								</div>
							</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
</div>
</div>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>