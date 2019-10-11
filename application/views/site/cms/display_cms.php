<?php 
$this->load->view('site/templates/header');
?>

<section class="rider_login_sec row">
   <div class="rider_login_cont" style="max-width: 900px;">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no_padding">
		<?php if(isset($pageDetail['banner_img']) && $pageDetail['use_banner'] == 'Yes') { ?>
		<div class="cms_banner_contianer">
			<img src="<?php echo base_url().'images/banner/'.$pageDetail['banner_img']?>" width="100%">
		</div>
		<?php 
		}
		?>
		
		<div class="cms_base_div">
			<div class="container-new cms-container">
				<?php if (isset($pageDetail[$langCode]['page_title']) && $pageDetail[$langCode]['page_title'] != '') { ?>
                    <h1 class="text-center"><?php echo $pageDetail[$langCode]['page_title']; ?></h1>
                <?php } else if ($pageDetail['page_title'] != '') { ?>
                    <h1 class="text-center"><?php echo $pageDetail['page_title']; ?></h1>
                <?php } ?>
                
                <?php 
                if (isset($pageDetail[$langCode]['description']) && $pageDetail[$langCode]['description'] != '') { 
                    echo $pageDetail[$langCode]['description'];
                } else if ($pageDetail['page_title'] != '') { 
                    echo $pageDetail['description']; 
                } 
                ?>
                
                
                <?php if(isset($pageDetail[$langCode]['css_descrip']) && $pageDetail[$langCode]['css_descrip'] != ''){ ?>
                    <style type="text/css">
                    <?php echo $pageDetail[$langCode]['css_descrip'];  ?>
                    </style>
                <?php } else if(isset($pageDetail['css_descrip']) && $pageDetail['css_descrip'] != ''){  ?>
                    <style type="text/css">
                        <?php echo $pageDetail['css_descrip']; ?>
                    </style>
                <?php } ?>
			</div>
		</div>
        
      </div>
   </div>
</section>

<?php 
$this->load->view('site/templates/footer'); 
?>