<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
?>
<!-- Script for timepicker -->	
<script type="text/javascript" src="js/timepicker/jquery.timepicker.js"></script>
<script type="text/javascript" src="js/timepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/timepicker/site.js"></script>
<script type="text/javascript" src="js/timepicker/jquery.timepicker.min.js"></script>
<!-- Script for timepicker -->	

<!-- css for timepicker -->	
<link rel="stylesheet" type="text/css" href="css/timepicker/bootstrap-datepicker.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/site.css" />
<link rel="stylesheet" type="text/css" href="css/timepicker/jquery.timepicker.css" />

<div id="content" class="menu-set">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditlocation_form');
						echo form_open(ADMIN_ENC_URL.'/adminlogin/insertMenu',$attributes) 
					?> 		
	 						<ul class="rite-menu-sec set_menu">
                                
								
                                <?php 
								
								$topMenuArr='';
								if($form_mode){
									if(isset($topMenuLists)){
										$topMenuArr=$topMenuLists;
									}else{
										$topMenuArr='';
									}
								}
								if(!is_array($topMenuArr))$topMenuArr=array();
								#echo "<pre>";print_r($topMenuArr);die;
								?>
								
								<li class="check-top">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_settings_smtp_header_menu') != '') echo stripslashes($this->lang->line('admin_settings_smtp_header_menu')); else echo 'Header Menu'; ?><span class="req">*</span></label>
										<div class="form_input">
										
										<input type="checkbox" name="header_home" value="header_home" <?php if($header_home == 'yes'){ echo "checked";} ?>><?php if ($this->lang->line('admin_common_add') != '') echo stripslashes($this->lang->line('admin_common_add')); else echo 'Add'; ?> <strong><?php if ($this->lang->line('admin_settings_home') != '') echo stripslashes($this->lang->line('admin_settings_home')); else echo 'Home'; ?></strong> <?php if ($this->lang->line('admin_settings_navigation_to') != '') echo stripslashes($this->lang->line('admin_settings_navigation_to')); else echo 'Navigation To'; ?> <strong><?php if ($this->lang->line('admin_settings_header') != '') echo stripslashes($this->lang->line('admin_settings_header')); else echo 'Header'; ?></strong> <?php if ($this->lang->line('admin_settings_menu') != '') echo stripslashes($this->lang->line('admin_settings_menu')); else echo 'Menu'; ?>
										
										<?php if(!empty($allPagesArr)){ ?>
										<select class="chzn-select required Validname" multiple="multiple" id="top_menu" name="top_menu[]"  data-placeholder="<?php if ($this->lang->line('admin_availabe_category') != '') echo stripslashes($this->lang->line('admin_availabe_category')); else echo 'Choose available category';?>">
											<?php foreach($allPagesArr as $row){ 
												if(!in_array($row['_id'],$topMenuArr)){?>
											<option value="<?php echo $row['_id']; ?>"><?php echo $row['name']; ?></option>
											<?php }
											} ?>
											<?php  foreach($topMenuArr as $cat_id){
												
												  foreach($allPagesArr as $row){ 
												 
													  if(($row['_id'] == $cat_id)){
														  
												?>
												<option value="<?php echo $row['_id']; ?>" <?php if (in_array($row['_id'],$topMenuArr)){ echo 'selected="selected"';}  ?>><?php echo $row['name']; ?></option>
												<?php 
												}
												}
											} ?>
										</select>
										<?php }else{ ?>
											<p class="error"><?php if ($this->lang->line('admin_settings_check_pages') != '') echo stripslashes($this->lang->line('admin_settings_check_pages')); else echo 'Kindly check pages list. There is no pages.'; ?></p>
										<?php } ?>
										</div>
									</div>
								</li>
								 <?php 
								
								$footerMenuArr='';
								if($form_mode){
									if(isset($footerMenuLists)){
										$footerMenuArr=$footerMenuLists;
									}else{
										$footerMenuArr='';
									}
								}
								if(!is_array($footerMenuArr))$footerMenuArr=array();
								#echo "<pre>";print_r($topMenuArr);die;
								?>
								<li class="check-top">
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_settings_smtp_footer_menu') != '') echo stripslashes($this->lang->line('admin_settings_smtp_footer_menu')); else echo 'Footer Menu'; ?><span class="req">*</span></label>
										<div class="form_input">
										<input type="checkbox" value="footer_home" name="footer_home" <?php if($footer_home == 'yes'){ echo "checked";} ?>><?php if ($this->lang->line('admin_common_add') != '') echo stripslashes($this->lang->line('admin_common_add')); else echo 'Add'; ?> <strong><?php if ($this->lang->line('admin_settings_home') != '') echo stripslashes($this->lang->line('admin_settings_home')); else echo 'Home'; ?></strong> <?php if ($this->lang->line('admin_settings_navigation_to') != '') echo stripslashes($this->lang->line('admin_settings_navigation_to')); else echo 'Navigation To'; ?> <strong><?php if ($this->lang->line('admin_settings_footer') != '') echo stripslashes($this->lang->line('admin_settings_footer')); else echo 'Footer'; ?> </strong><?php if ($this->lang->line('admin_settings_menu') != '') echo stripslashes($this->lang->line('admin_settings_menu')); else echo 'Menu'; ?> 
										<?php if(!empty($allPagesArr)){ ?>
										<select class="chzn-select1 required Validname" multiple="multiple" id="footer_menu" name="footer_menu[]"  data-placeholder="<?php if ($this->lang->line('admin_availabe_category') != '') echo stripslashes($this->lang->line('admin_availabe_category')); else echo 'Choose available category';?>">
											<?php foreach($allPagesArr as $row){ 
												if(!in_array($row['_id'],$footerMenuArr)){?>
											<option value="<?php echo $row['_id']; ?>"><?php echo $row['name']; ?></option>
											<?php }
											} ?>
											<?php  foreach($footerMenuArr as $cat_id){
												
												  foreach($allPagesArr as $row){ 
												 
													  if(($row['_id'] == $cat_id)){
														  
												?>
												<option value="<?php echo $row['_id']; ?>" <?php if (in_array($row['_id'],$footerMenuArr)){ echo 'selected="selected"';}  ?>><?php echo $row['name']; ?></option>
												<?php 
												}
												}
											} ?>
										</select>
										<?php }else{ ?>
											<p class="error"><?php if ($this->lang->line('admin_settings_check_pages') != '') echo stripslashes($this->lang->line('admin_settings_check_pages')); else echo 'Kindly check pages list. There is no pages.'; ?></p>
										<?php } ?>
										</div>
									</div>
								</li>
								
								
							</ul>
							
							<ul class="top-sec-hide set_menu">
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<input type="hidden" val="" name="added_top_menu">
											<input type="hidden" val="" name="added_footer_menu">
											<button type="submit" class="btn_small btn_blue" ><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
										</div>
									</div>
								</li>
							</ul>
							
						</form>
						
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
</div>
</div>

<script>
$(document).ready(function() {
	
	/* Javascript function closure for Getting Selected order of Menu*/
	var topMenuArr = <?php echo json_encode($topMenuArr); ?>;
	$("input[name='added_top_menu']").val(topMenuArr);
	var footerMenuArr = <?php echo json_encode($footerMenuArr); ?>;
	$("input[name='added_footer_menu']").val(footerMenuArr);
	
$.validator.setDefaults({ ignore: ":hidden:not(select)" });
</script>
<script type='text/javascript'>
var footer_results = [];
$("#footer_menu").on('change',function(){
    var selected_values = $(this).val();
    var temp_results = footer_results;
    footer_results = [];

    for(i in temp_results){
        if($.inArray(temp_results[i],selected_values)>=0){
            footer_results.push(temp_results[i]);
        }
    }

    for(i in selected_values){
        if($.inArray(selected_values[i],temp_results)<=-1){
            footer_results.push(selected_values[i]);
        }
    }               

    $("input[name='added_footer_menu']").val(footer_results.join(","));
});


var top_results = [];
$("#top_menu").on('change',function(){
    var selected_values = $(this).val();
    var temp_results = top_results;
    top_results = [];

    for(i in temp_results){
        if($.inArray(temp_results[i],selected_values)>=0){
            top_results.push(temp_results[i]);
        }
    }

    for(i in selected_values){
        if($.inArray(selected_values[i],temp_results)<=-1){
            top_results.push(selected_values[i]);
        }
    }               

    $("input[name='added_top_menu']").val(top_results.join(","));
});
</script>
<style>
.chzn-container {
	display: block;
	width: 50% !important;
}
.chzn-container-multi .chzn-choices .search-field {
	width: 100%;
}
.chzn-container-multi .chzn-choices .search-field .default {
	float: left;
	width: 100% !important;
}
</style>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>