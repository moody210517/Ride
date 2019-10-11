<?php
$this->load->view('site/templates/common_header');
#$this->load->view('site/templates/cms_header');
?>


<?php /* <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>  */ ?>

<div class="cms_base_div">
    <div class="container-new cms-container">
        <h1 class="text-center"><?php echo $heading ?></h1>
		<p style="text-align:center;">Generate IOS and Android app language here...</p>
		<div class="col-lg-12 app_selecter">
			<label class="css-label col-md-2" style=" margin-top: 7px; width: 145px !important;">Choose App</label>
			<select id="app_type" class="col-sm-2" style="height:30px;">
				<option value="">Select App</option>
				<optgroup label="----------- Android ----------">
					<option value="AU" <?php if($this->input->get('App') == 'AU') echo 'selected="selected"';?>>Android User App</option>
					<option value="AP" <?php if($this->input->get('App') == 'AP') echo 'selected="selected"';?>>Android Driver App</option>
				</optgroup>
				<optgroup label="------------- IOS --------------">
					<option value="IU" <?php if($this->input->get('App') == 'IU') echo 'selected="selected"';?>>IOS User App</option>
					<option value="IP" <?php if($this->input->get('App') == 'IP') echo 'selected="selected"';?>>IOS Driver App</option>
				</optgroup>
			</select>
			<?php if(count($lang_result) > 0){ ?>
			<?php /* <script type = "text/javascript">
				function googleTranslateElementInit() {
					new google.translate.TranslateElement({pageLanguage: 'pl', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
				}
			</script>
			<div id = "google_translate_element"></div>  */ ?>
			<div class="login_inner"> 
				<input type="button" id="generate" class="btn1 login_ride" value="Generate Lang"/>
			</div>
			<?php } ?>
		</div>
		<div class="col-lg-12">
			<form action="site/lang_converter/download_ios_lang" id="lang_form" method="post">
				<?php 
			if(count($lang_result) > 0){
				foreach($lang_result as $key => $langs){
					?>
					<div class="lang_fields col-lg-12">
						<label class="css-label col-md-2"><?php echo stripslashes($langs); ?></label>
						<input type="text" name="key[<?php echo $key; ?>]" />
					</div>
					<?php 
				}
				?>
				<input type="hidden" value="<?php echo $this->input->get('App');?>" name="app_type" />
			<?php } else { ?>
			
			<h2 class="no-lang-cnt">App Language Content Not Found...</h2>
			
			<?php } ?>
			</form>
		</div>

		
    </div>
</div>

<style>
option {
    height: 25px !important;
}

.cms_base_div {
    padding-top: 0;
}
.app_selecter {
	margin-top:-70px;
}
.no-lang-cnt {
    border: 1px solid #dfdfdf;
    border-radius: 5px;
    margin-top: 15%;
    text-align: center;
}
#google_translate_element {
	float:right;
	margin-top:15px;
	margin-left:15px;
}

.login_inner {
    float: right;
    width: 134px;
}

.login_inner input[type="button"]{
	padding:10px; 
}

.lang_fields {
	margin-top:20px;
	text-align:center;
	background: #f3f3f3 none repeat scroll 0 0;
}
.lang_fields input[type="text"]{
	height:40px;
	width:65%;
	border:solid #8fa97e 1.5px;
}
.lang_fields label{
	width:35%;
	margin-bottom:5px;
	font-weight:normal;
	text-align:left;
}
</style> 

<script>
$(document).ready(function(){
	$('#app_type').change(function(){
		var lang=$(this).val(); 
		window.location.href='<?php echo base_url(); ?>convert-lang?App='+lang;
	});
	$('#generate').click(function(){
		if($('#app_type').val() == 'IP' || $('#app_type').val() == 'IU'){
			$('#lang_form').attr('action','site/lang_converter/download_ios_rtf_lang');
		} else {
			$('#lang_form').attr('action','site/lang_converter/download_android_xml_lang');
		}
		$('#lang_form').submit(); 
	});	
});

</script>

<?php
#$this->load->view('site/templates/footer');
?> 		