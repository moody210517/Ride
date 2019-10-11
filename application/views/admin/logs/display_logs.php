<?php
$this->load->view(ADMIN_ENC_URL.'/templates/header.php');
extract($privileges);
?>
<div id="content" class="disply-log-details">
	<div class="grid_container">
		<?php 
			$attributes = array('id' => 'display_form');
			echo form_open(ADMIN_ENC_URL.'/currency/change_currency_status_global',$attributes) 
		?>
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<?php $fileP = $filedir; $filedir =  str_replace('log/logs/','',$filedir); $filedir =  str_replace('.txt','',$filedir); ?>
						<h6><?php echo $heading .' - '.$filedir; ?></h6>
					</div>
					<?php 
						#$file = fopen($fileP,'r');  
						$file = file_get_contents($fileP);  
						?>
					<div class="widget_content" style="line-height: 17px;padding: 20px 15px;">
						<pre class="log-wrap"><?php echo $file; ?></pre>
						<?php /* <ul class="log_info_container">
							<?php while ($line = fgets($file)) { ?>
								<li><?php echo $line; ?></li>
								<?php }
								fclose($file); ?>
						</ul> */ ?>
					</div>
				</div>
			</div>
			<input type="hidden" name="statusMode" id="statusMode"/>
            <input type="hidden" name="SubAdminEmail" id="SubAdminEmail"/>
		</form>	
	</div>
	<span class="clear"></span>
</div>
</div>
<?php 
$this->load->view(ADMIN_ENC_URL.'/templates/footer.php');
?>