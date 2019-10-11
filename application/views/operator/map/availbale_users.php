<?php
$this->load->view(OPERATOR_NAME . '/templates/header.php');
?>

<style>
#location {
    clear: both;
    height: 23px;
    margin: 1%;
    width: 50%;
}
#btn_find {
    background-color: #28cbf9;
    border: medium none;
    cursor: pointer;
    padding: 6px 32px;
}
</style>
<div id="content" class="admin-settings map_avail_offers">
		<div class="grid_container">
				<div class="grid_12">
						<div class="widget_wrap">
								<div class="widget_top">
										<span class="h_icon list"></span>
										<h6><?php if ($this->lang->line('admin_map_view_display_users') != '') echo stripslashes($this->lang->line('admin_map_view_display_users')); else echo 'Display available users in their location'; ?></h6>
										<div id="widget_tab">
										</div>
								</div>
								<div class="widget_content chenge-pass-base">												
										<div class="grid_12">
											<?php echo $map['js']; ?>
											<?php echo $map['html']; ?>
										</div>
								</div>
						</div>
				</div>
		</div>
		<span class="clear"></span>
</div>
</div>
<?php 
$this->load->view(OPERATOR_NAME . '/templates/footer.php');
?>