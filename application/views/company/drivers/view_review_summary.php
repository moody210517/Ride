<?php
$this->load->view(COMPANY_NAME.'/templates/header.php');
?>
<div id="content" class="admin-settings view_user_set review-summary-wrap">
	<div class="grid_container">
		<div class="grid_12">
			<div class="widget_wrap">
				<div class="widget_top">
					<span class="h_icon list"></span>
					<h6><?php echo $heading; ?></h6>
				</div>
				<div class="widget_content chenge-pass-base">
					<?php 
						$attributes = array('class' => 'form_container left_label');
						echo form_open('admin',$attributes) 
					?>
					<div id="tab1">
	 					<ul class="leftsec-contsec">
								
								
							<?php 
							if(count($reviewsList) > 0){
								foreach($reviewsList as $reviews) { ?>	
								<li>	
									<h2><?php echo $reviews['option_name']; ?></h2>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_no_of_rating_attempts') != '') echo stripslashes($this->lang->line('admin_review_no_of_rating_attempts')); else echo 'No of rating attempts'; ?> </label>
										<div class="form_input">
											<?php if(isset($reviews['no_of_rates'])) echo $reviews['no_of_rates']; else echo '0'; ?>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_total_ratings_points') != '') echo stripslashes($this->lang->line('admin_review_total_ratings_points')); else echo 'Total Ratings Points'; ?></label>
										<div class="form_input">
											<?php if(isset($reviews['IndtotalRates'])) echo number_format($reviews['IndtotalRates'],2); else echo '0'; ?>
										</div>
									</div>
								</li>
								<li>
									<div class="form_grid_12">
										<label class="field_title"><?php if ($this->lang->line('admin_review_avg_ratings') != '') echo stripslashes($this->lang->line('admin_review_avg_ratings')); else echo 'Avg Ratings'; ?></label>
										<div class="form_input">
											<?php if(isset($reviews['avg_rates']))  $avg_rates = number_format($reviews['avg_rates'],2); else $avg_rates = 0; ?>
											<div class="ratingstar-<?php echo trim(round(stripslashes($reviews['avg_rates'])));?>" id="rating-pos<?php echo $reviews['option_id'];?>">  </div>
											<span>&nbsp;</span>
											<span class="starRatings-count">( <?php echo $avg_rates;?> )</span>
										</div>
									</div>
								</li>
							<?php } 
							} else {
							?>
							
								<li>	
									<h2><?php if ($this->lang->line('admin_review_no_records_found_for_this') != '') echo stripslashes($this->lang->line('admin_review_no_records_found_for_this')); else echo 'No records found for this'; ?> <?php $reviews[0]['option_holder']; ?></h2>
								</li>
							
							<?php } ?>
							
						</ul>
						
						<ul class="admin-pass back_view_user">
						<li class="change-pass">
								<div class="form_grid_12">
									<div class="form_input">
										<a href="javascript:void(0);" onclick="javascript: window.history.go(-1);" class="tipLeft" title="<?php if ($this->lang->line('dash_go_users_list') != '') echo stripslashes($this->lang->line('dash_go_users_list')); else echo 'Go to users list'; ?>"><span class="badge_style b_done"><?php if ($this->lang->line('admin_common_back') != '') echo stripslashes($this->lang->line('admin_common_back')); else echo 'Back'; ?></span></a>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
</div>

<style>
.starRatings-count {
	float: right;
    margin-right: 77%;
    margin-top: -21px;
}
</style>

<?php 
$this->load->view(COMPANY_NAME.'/templates/footer.php');
?>