<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width"/>
<base href="<?php echo base_url(); ?>">
<title><?php echo $heading.' - '.$title;?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>images/logo/<?php echo $favicon;?>">
<link href="css/reset.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/font-awesome.css" rel="stylesheet" type="text/css">
<link href="css/layout.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/themes.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/typography.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/styles.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/rating.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/themes-changes.css" rel="stylesheet" type="text/css" media="screen">

<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/jquery.jqplot.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/data-table.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/form.css" rel="stylesheet" type="text/css" media="screen">

<link href="css/ui-elements.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/wizard.css" rel="stylesheet" type="text/css">
<link href="css/sprite.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/gradient.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/developer.css" rel="stylesheet" type="text/css">
<link href="css/developer_colors.css" rel="stylesheet" type="text/css">
<link href="css/custom-dev-css.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type="text/css" media="all" href="plugins/daterangepicker/css/glyphicons.css" />

<!--<link rel="stylesheet" type="text/css" href="css/ie/ie7.css" />
<link rel="stylesheet" type="text/css" href="css/ie/ie8.css" />
<link rel="stylesheet" type="text/css" href="css/ie/ie9.css" />-->
<script type="text/javascript">
		var BaseURL = '<?php echo base_url();?>';
		var baseURL = '<?php echo base_url();?>';

</script>
<?php
		
	$this->load->view('site/templates/validation_script');
		
?>
<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/jquery.ui.touch-punch.js"></script>
<script src="js/chosen.jquery.js"></script>
<script src="js/uniform.jquery.js"></script>
<script src="js/bootstrap-dropdown.js"></script>
<script src="js/bootstrap-colorpicker.js"></script>
<script src="js/sticky.full.js"></script>
<script src="js/jquery.noty.js"></script>
<script src="js/selectToUISlider.jQuery.js"></script>
<script src="js/fg.menu.js"></script>
<script src="js/jquery.tagsinput.js"></script>
<script src="js/jquery.cleditor.js"></script>
<script src="js/jquery.tipsy.js"></script>
<script src="js/jquery.peity.js"></script>
<script src="js/jquery.simplemodal.js"></script>
<script src="js/jquery.jBreadCrumb.1.1.js"></script>
<script src="js/jquery.colorbox-min.js"></script>
<script src="js/jquery.idTabs.min.js"></script>
<script src="js/jquery.multiFieldExtender.min.js"></script>
<script src="js/jquery.confirm.js"></script>
<script src="js/elfinder.min.js"></script>
<script src="js/accordion.jquery.js"></script>
<script src="js/autogrow.jquery.js"></script>
<script src="js/check-all.jquery.js"></script>
<script src="js/data-table.jquery.js"></script>
<script src="js/ZeroClipboard.js"></script>
<script src="js/TableTools.min.js"></script>
<script src="js/jeditable.jquery.js"></script>
<script src="js/ColVis.min.js"></script>
<script src="js/duallist.jquery.js"></script>
<script src="js/easing.jquery.js"></script>
<script src="js/full-calendar.jquery.js"></script>
<script src="js/input-limiter.jquery.js"></script>
<script src="js/inputmask.jquery.js"></script>
<script src="js/iphone-style-checkbox.jquery.js"></script>
<script src="js/meta-data.jquery.js"></script>
<script src="js/quicksand.jquery.js"></script>
<script src="js/raty.jquery.js"></script>
<script src="js/smart-wizard.jquery.js"></script>
<script src="js/stepy.jquery.js"></script>
<script src="js/treeview.jquery.js"></script>
<script src="js/ui-accordion.jquery.js"></script> 
<script src="js/vaidation.jquery.js"></script>
<script src="js/mosaic.1.0.1.min.js"></script>
<script src="js/jquery.collapse.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/jquery.autocomplete.min.js"></script>
<script src="js/localdata.js"></script>
<script src="js/excanvas.min.js"></script>
<script src="js/jquery.jqplot.min.js"></script>
<script src="js/chart-plugins/jqplot.dateAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.cursor.min.js"></script>
<script src="js/chart-plugins/jqplot.logAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.canvasTextRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.highlighter.min.js"></script>
<script src="js/chart-plugins/jqplot.pieRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.barRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script src="js/chart-plugins/jqplot.pointLabels.min.js"></script>
<script src="js/chart-plugins/jqplot.meterGaugeRenderer.min.js"></script>
<script src="js/jquery.MultiFile.js"></script>
<script src="js/validation.js"></script>

<script src="js/custom-scripts.js"></script>
<script src="js/jquery-input-file-text.js"></script>
<script type="text/javascript" src="js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
// General options
mode : "specific_textareas",
editor_selector : "mceEditor",
theme : "advanced",
plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
 
// Theme options
theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,
file_browser_callback : "ajaxfilemanager",
relative_urls : false,
convert_urls: false,
// Example content CSS (should be your site CSS)
content_css : "css/example.css",
 
// Drop lists for link/image/media/template dialogs
//template_external_list_url : "js/template_list.js",
external_link_list_url : "js/link_list.js",
external_image_list_url : "js/image_list.js",
media_external_list_url : "js/media_list.js",
 
// Replace values for the template plugin
template_replace_values : {
username : "Some User",
staffid : "991234"
}
});

function ajaxfilemanager(field_name, url, type, win) {
		var ajaxfilemanagerurl = '<?php echo base_url();?>js/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php';
		switch (type) {
				case "image":
						break;
				case "media":
						break;
				case "flash": 
						break;
				case "file":
						break;
				default:
						return false;
		}
		tinyMCE.activeEditor.windowManager.open({
				url: '<?php echo base_url();?>js/tinymce/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php',
				width: 782,
				height: 440,
				inline : "yes",
				close_previous : "no"
		},{
				window : win,
				input : field_name
		});
					
		return false;			
		var fileBrowserWindow = new Array();
		fileBrowserWindow["file"] = ajaxfilemanagerurl;
		fileBrowserWindow["title"] = "Ajax File Manager";
		fileBrowserWindow["width"] = "782";
		fileBrowserWindow["height"] = "440";
		fileBrowserWindow["close_previous"] = "no";
		tinyMCE.openWindow(fileBrowserWindow, {
				window : win,
				input : field_name,
				resizable : "yes",
				inline : "yes",
				editor_id : tinyMCE.getWindowArg("editor_id")
		});
		
		return false;
}
</script>
<script type="text/javascript">
function hideErrDiv(arg) {
		document.getElementById(arg).style.display = 'none';
}
</script>
<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
<script>

$(function () {
$('.on_off :checkbox').iphoneStyle();
$('.yes_no :checkbox').iphoneStyle({checkedLabel:'<?php echo $checkbox_lan['verify_status_yes_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['verify_status_no_ucfirst']; ?>'});
$('.flat_percentage :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['coupon_code_flat']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['coupon_code_percent']; ?>'});
$('.active_inactive :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['status_active_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['status_inactive_ucfirst']; ?>'});
$('.publish_unpublish :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['status_publish_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['status_unpublish_ucfirst']; ?>'});
$('.live_sandbox :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['checkbox_live']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['checkbox_sandbox']; ?>'});
$('.disabled :checkbox').iphoneStyle();
$('.ac_nonac :checkbox').iphoneStyle({checkedLabel:'<?php echo $checkbox_lan['checkbox_ac']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['checkbox_non_ac']; ?>'});
$('.cod_on_off :checkbox').iphoneStyle({checkedLabel: '<?php echo $checkbox_lan['status_enable_ucfirst']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['status_disable_ucfirst']; ?>'});
$('.prod_dev :checkbox').iphoneStyle({ checkedLabel: '<?php echo $checkbox_lan['checkbox_production']; ?>', uncheckedLabel: '<?php echo $checkbox_lan['checkbox_development']; ?>' });
});
</script>
</head>
<body id="theme-default" class="full_block">
<?php  $this->load->view(OPERATOR_NAME.'/templates/sidebar.php'); ?>
<?php
$currentUrl = $this->uri->segment(2,0); 
$currentPage = $this->uri->segment(3,0);
if($currentUrl==''){
		$currentUrl = 'dashboard';
} 
if($currentPage==''){
		$currentPage = 'dashboard';
}
$current_url = $_SERVER['REQUEST_URI'];
?>

 <?php  
$this->load->view('site/templates/datetime_lang_script');
?>

<div id="container">
<div id="header">
<div class="header_left">
<div id="responsive_mnu">
<a href="#responsive_menu" class="fg-button" id="hierarchybreadcrumb"><span class="responsive_icon"></span><?php if ($this->lang->line('admin_menu_menu') != '') echo stripslashes($this->lang->line('admin_menu_menu')); else echo 'Menu'; ?></a>
<div id="responsive_menu" class="hidden">
<ul>
<li>
	<a href="<?php echo base_url().OPERATOR_NAME; ?>/dashboard/display_dashboard" <?php
	if ($currentUrl == 'dashboard') {
			echo 'class="active"';
	}
	?>>
			<span class="nav_icon computer_imac"></span> 
			<?php if ($this->lang->line('admin_menu_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_dashboard')); else echo 'Dashboard'; ?>
	</a>
</li>
<li>
		<h6 style="margin: 10px 0;padding-left:10px; font-size:13px; font-weight:bold;color:#333; text-transform:uppercase; "><?php if ($this->lang->line('admin_menu_managements') != '') echo stripslashes($this->lang->line('admin_menu_managements')); else echo 'Managements'; ?></h6>
</li>

<li>
	<a href="<?php echo $current_url; ?>" <?php
	if ($currentUrl == 'settings') {
			echo 'class="active"';
	}
	?>>
			<span class="nav_icon admin_user"></span><?php if ($this->lang->line('admin_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_settings')); else echo 'Settings'; ?><span class="up_down_arrow">&nbsp;</span>
	</a>
	<ul class="acitem" <?php
	if ($currentUrl == 'settings') {
			echo 'style="display: block;"';
	} else {
			echo 'style="display: none;"';
	}
	?>>
			<li>
						<a href="<?php echo OPERATOR_NAME; ?>/settings/edit_profile_form" <?php
						if ($currentPage == 'edit_profile_form') {
								echo 'class="active"';
						}
						?>>
								<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('dash_operator_profile_settings') != '') echo stripslashes($this->lang->line('dash_operator_profile_settings')); else echo 'Profile Settings'; ?>  
						</a>
			</li>

			<li>
					<a href="<?php echo OPERATOR_NAME; ?>/settings/change_password" <?php
					if ($currentPage == 'change_password') {
							echo 'class="active"';
					}
					?>>
							<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?>
					</a>
			</li>
		 
	</ul>
</li>

<li>
	<a href="<?php echo $current_url; ?>" <?php
	if ($currentUrl == 'map') {
			echo 'class="active"';
	}
	?>>
			<span class="nav_icon marker map-new"></span><?php if ($this->lang->line('admin_menu_map_view') != '') echo stripslashes($this->lang->line('admin_menu_map_view')); else echo 'Map View'; ?><span class="up_down_arrow">&nbsp;</span>
	</a>
	<ul class="acitem" <?php
	if ($currentUrl == 'map') {
			echo 'style="display: block;"';
	} else {
			echo 'style="display: none;"';
	}
	?>>
			<li>
						<a href="<?php echo OPERATOR_NAME; ?>/map/map_avail_drivers" <?php
						if ($currentPage == 'map_avail_drivers') {
								echo 'class="active"';
						}
						?>>
								<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_view_available_drivers') != '') echo stripslashes($this->lang->line('admin_menu_view_available_drivers')); else echo 'View available drivers'; ?>  
						</a>
			</li>

			<li>
					<a href="<?php echo OPERATOR_NAME; ?>/map/map_avail_users" <?php
					if ($currentPage == 'map_avail_users') {
							echo 'class="active"';
					}
					?>>
							<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_view_available_users') != '') echo stripslashes($this->lang->line('admin_menu_view_available_users')); else echo 'View available users'; ?>
					</a>
			</li>
		 
	</ul>
</li>

<li>
	<a href="<?php echo $current_url; ?>" <?php
	if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) {
			echo 'class="active"';
	}
	?>>
			<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?><span class="up_down_arrow">&nbsp;</span>
	</a>
	<ul class="acitem" <?php
	if (($currentUrl == 'drivers' || $currentPage == 'view_driver_reviews') && ($currentPage != 'add_edit_category_types' && $currentPage != 'add_edit_category' && $currentPage != 'display_drivers_category' && $currentPage != 'edit_language_category')) {
			echo 'style="display: block;"';
	} else {
			echo 'style="display: none;"';
	}
	?>>
			<li>
					<a href="<?php echo OPERATOR_NAME; ?>/drivers/display_driver_dashboard" <?php
					if ($currentPage == 'display_driver_dashboard') {
							echo 'class="active"';
					}
					?>>
							<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_dashboard') != '') echo stripslashes($this->lang->line('admin_menu_drivers_dashboard')); else echo 'Drivers Dashboard'; ?>
					</a>
			</li>
			<li>
					<a href="<?php echo OPERATOR_NAME; ?>/drivers/display_drivers_list" <?php
					if ($currentPage == 'display_drivers_list' || $currentPage == 'edit_driver_form' || $currentPage == 'change_password_form' || $currentPage == 'view_driver' || $currentPage == 'banking' || $currentPage == 'view_driver_reviews') {
							echo 'class="active"';
					}
					?>>
							<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_drivers_list') != '') echo stripslashes($this->lang->line('admin_menu_drivers_list')); else echo 'Drivers List'; ?>
					</a>
			</li>
		 
			<li>
					<a href="<?php echo OPERATOR_NAME; ?>/drivers/add_driver_form" <?php
					if ($currentPage == 'add_driver_form') {
							echo 'class="active"';
					}
					?>>
							<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_driver') != '') echo stripslashes($this->lang->line('admin_menu_add_driver')); else echo 'Add Driver'; ?>
					</a>
			</li>
		 
	</ul>
</li>


										    <li>
                        <a href="<?php echo $current_url; ?>" <?php
                        if ($currentUrl == 'rides') {
                            echo 'class="active"';
                        }
                        ?>>
                            <span class="nav_icon car-icon"></span> <?php if ($this->lang->line('admin_menu_rides') != '') echo stripslashes($this->lang->line('admin_menu_rides')); else echo 'Rides'; ?><span class="up_down_arrow">&nbsp;</span>
                        </a>
                        <ul class="acitem" <?php
                        $ride_action='';
                        if ($currentUrl == 'trip') {
                            $ride_action=$this->input->get('act');
                            echo 'style="display: block;"';
                        } else {
                            echo 'style="display: none;"';
                        }
                        ?>>
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/ride_dashboard" <?php
                                if ($currentPage == 'ride_dashboard' || $currentPage == 'map_unfilled_rides') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rides_dashboard') != '') echo stripslashes($this->lang->line('rides_dashboard')); else echo 'Rides Dashboard'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/rides_grid_view" <?php
                                if ($currentPage == 'rides_grid_view') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rides_grid_view') != '') echo stripslashes($this->lang->line('rides_grid_view')); else echo 'Rides Grid View'; ?>
                                </a>
                            </li>
							
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Booked" <?php
                                if ($ride_action == 'Booked') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_just_booked') != '') echo stripslashes($this->lang->line('admin_menu_just_booked')); else echo 'Just Booked'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=OnRide" <?php
                                if ($ride_action == 'OnRide') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_on_rides') != '') echo stripslashes($this->lang->line('admin_menu_on_rides')); else echo 'On Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Completed" <?php
                                if ($ride_action == 'Completed') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_completed_rides') != '') echo stripslashes($this->lang->line('admin_menu_completed_rides')); else echo 'Completed Rides'; ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Cancelled" <?php
                                if ($ride_action == 'Cancelled' || $ride_action == 'riderCancelled' || $ride_action == 'driverCancelled') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_cancelled_rides') != '') echo stripslashes($this->lang->line('admin_menu_cancelled_rides')); else echo 'Cancelled Rides'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/display_trips?act=Expired" <?php
                                if ($ride_action == 'Expired') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_expired_rides') != '') echo stripslashes($this->lang->line('admin_menu_expired_rides')); else echo 'Expired Rides'; ?>
                                </a>
                            </li>
							
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/search_ride" <?php
                                if ($currentPage == 'search_ride' || $currentPage == 'cancelling_ride' || $currentPage == 'end_ride_form') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_search_ride') != '') echo stripslashes($this->lang->line('admin_search_ride')); else echo 'Search Ride'; ?>
                                </a>
                            </li>
							<li>
                                <a href="<?php echo OPERATOR_NAME;?>/trip/init_booking_form" <?php
                                if ($currentPage == 'init_booking_form' || $currentPage == 'book_trip') {
                                    echo 'class="active"';
                                }
                                ?>>
                                    <span class="list-icon">&nbsp;</span><?php if ($this->lang->line('rider_book_ride') != '') echo stripslashes($this->lang->line('rider_book_ride')); else echo 'Book Ride'; ?>
                                </a>
                            </li>
                            <!---<li>
                                    <a href="admin/rides/display_rides?act=All" <?php
                            if ($ride_action == 'All') {
                                echo 'class="active"';
                            }
                            ?>>
                                            <span class="list-icon">&nbsp;</span>All Rides
                                    </a>
                            </li> -->
                        </ul>
                    </li>

<li>
	<a href="<?php echo $current_url; ?>" <?php
	if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list') {
			echo 'class="active"';
	}
	?>>
			<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_notification') != '') echo stripslashes($this->lang->line('admin_menu_notification')); else echo 'Notification'; ?><span class="up_down_arrow">&nbsp;</span>
	</a>
	<ul class="acitem" <?php
	if ($currentUrl == 'notification' || $currentPage == 'display_notification_user_list' || $currentPage == 'display_notification_driver_list') {
			echo 'style="display: block;"';
	} else {
			echo 'style="display: none;"';
	}
	?>>
		
			<li>
					<a href="<?php echo OPERATOR_NAME; ?>/notification/display_notification_driver_list" <?php
					if ($currentPage == 'display_notification_driver_list') {
							echo 'class="active"';
					}
					?>>
								<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_drivers') != '') echo stripslashes($this->lang->line('admin_menu_drivers')); else echo 'Drivers'; ?>
					</a>
			</li>
			<li>
					<a href="<?php echo OPERATOR_NAME; ?>/notification/display_notification_user_list" <?php
					if ($currentPage == 'display_notification_user_list') {
							echo 'class="active"';
					}
					?>>
								<span class="nav_icon users"></span> <?php if ($this->lang->line('admin_menu_users') != '') echo stripslashes($this->lang->line('admin_menu_users')); else echo 'Users'; ?>
					</a>
			</li>

	</ul>
</li>

<li>
<a href="<?php echo $current_url; ?>" <?php
if ($currentUrl == 'brand') {
	echo 'class="active"';
}
?>>
	<span class="nav_icon companies"></span> <?php if ($this->lang->line('admin_menu_make_and_model') != '') echo stripslashes($this->lang->line('admin_menu_make_and_model')); else echo 'Make and Model'; ?><span class="up_down_arrow">&nbsp;</span>
</a>
<ul class="acitem" <?php
if ($currentUrl == 'brand' || $currentPage == 'display_brand_list' || $currentPage == 'add_brand_form' || $currentPage == 'edit_brand_form') {
	echo 'style="display: block;"';
} else {
	echo 'style="display: none;"';
}
?>>
	<li>
		<a href="<?php echo OPERATOR_NAME; ?>/brand/display_brand_list" <?php
		if ($currentPage == 'display_brand_list') {
			echo 'class="active"';
		}
		?>>
			<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_makers_list') != '') echo stripslashes($this->lang->line('admin_menu_makers_list')); else echo 'Makers List'; ?>
		</a>
	</li>
	
		<li>
			<a href="<?php echo OPERATOR_NAME; ?>/brand/add_brand_form" <?php
			if ($currentPage == 'add_brand_form' || $currentPage == 'edit_brand_form') {
				echo 'class="active"';
			}
			?>>
				<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_maker') != '') echo stripslashes($this->lang->line('admin_menu_add_new_maker')); else echo 'Add New Maker'; ?>
			</a>
		</li>							
	<li>
		<a href="<?php echo OPERATOR_NAME; ?>/brand/display_model_list" <?php
		if ($currentPage == 'display_model_list') {
			echo 'class="active"';
		}
		?>>
			<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_model_list') != '') echo stripslashes($this->lang->line('admin_menu_model_list')); else echo 'Model List'; ?>
		</a>
	</li>
	<li>
		<a href="<?php echo OPERATOR_NAME; ?>/brand/add_edit_model" <?php
		if ($currentPage == 'add_edit_model') {
			echo 'class="active"';
		}
		?>>
			<span class="list-icon">&nbsp;</span><?php if ($this->lang->line('admin_menu_add_new_model') != '') echo stripslashes($this->lang->line('admin_menu_add_new_model')); else echo 'Add New Model'; ?>
		</a>
	</li>
</ul>
</li>

</ul>
</div>
</div>
</div>
<div class="header_right">
<div id="user_nav" style="width: 300px;">
<ul>
	<li class="user_thumb"><span class="icon"><img src="images/profile.png" class="tipBot" width="30" height="30" alt="<?php echo $this->session->userdata(APP_NAME.'_session_operator_name'); ?>" title="<?php echo $this->session->userdata(APP_NAME.'_session_operator_name'); ?>"></span></li>
	<li class="user_info">
			<span class="user_name">
				
			
			<?php if ($allPrev == '1'){?>
			
				<a href="<?php echo base_url();?>" target="_blank" class="tipBot" title="<?php if ($this->lang->line('driver_view_site') != '') echo stripslashes($this->lang->line('driver_view_site')); else echo 'View Site'; ?>"><?php if ($this->lang->line('admin_header_visit_site') != '') echo stripslashes($this->lang->line('admin_header_visit_site')); else echo 'Visit Site'; ?></a> &#124;               
				<a href="<?php echo OPERATOR_NAME; ?>/settings/edit_profile_form" class="tipBot" title="<?php if ($this->lang->line('driver_edit_account_details') != '') echo stripslashes($this->lang->line('driver_edit_account_details')); else echo 'Edit account details'; ?>"><?php if ($this->lang->line('admin_menu_settings') != '') echo stripslashes($this->lang->line('admin_menu_settings')); else echo 'Settings'; ?></a>
			
			<?php }else {?>
		
				<a href="<?php echo base_url();?>" target="_blank" class="tipBot" title="<?php if ($this->lang->line('driver_view_site') != '') echo stripslashes($this->lang->line('driver_view_site')); else echo 'View Site'; ?>"><?php if ($this->lang->line('admin_header_visit_site') != '') echo stripslashes($this->lang->line('admin_header_visit_site')); else echo 'Visit Site'; ?></a> &#124; 
				<a href="<?php echo OPERATOR_NAME; ?>/settings/change_password" class="tipBot" title="<?php if ($this->lang->line('driver_click_to_change') != '') echo stripslashes($this->lang->line('driver_click_to_change')); else echo 'Click to change your password'; ?>"><?php if ($this->lang->line('admin_menu_change_password') != '') echo stripslashes($this->lang->line('admin_menu_change_password')); else echo 'Change Password'; ?></a> 
			</span>
			<?php }?>
	</li>
	
	<li class="logout"><a href="<?php echo OPERATOR_NAME; ?>/settings/logout" class="tipBot" title="<?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?>"><span class="icon"></span><?php if ($this->lang->line('rider_profile_logout') != '') echo stripslashes($this->lang->line('rider_profile_logout')); else echo 'Logout'; ?></a></li>
</ul>
</div>
	</div>
</div>
<?php if (validation_errors() != ''){?>
<div id="validationErr">
		<script>setTimeout("hideErrDiv('validationErr')", 3000);</script>
		<p><?php echo validation_errors();?></p>
</div>
<?php }?>


<script src="js/jquery.growl.js" type="text/javascript"></script>
<link href="css/jquery.growl.css" rel="stylesheet" type="text/css" />

<?php if($this->session->flashdata('sErrMSG') != '') { ?>
<script type="text/javascript">
		<?php 
		$sErrMSGdecoded = base64_decode($this->session->flashdata('sErrMSG'));
		$sErrMSGKeydecoded = base64_decode($this->session->flashdata('sErrMSGKey'));
		if($this->session->flashdata('sErrMSGType')=='message-red'){
		?>
				$.growl.error({ title:"<?php echo $sErrMSGKeydecoded; ?>",message: "<?php echo  $sErrMSGdecoded;  ?>" });
		<?php } ?>
		<?php
		if($this->session->flashdata('sErrMSGType')=='message-green'){ 
		?>
				$.growl.notice({ title:"<?php echo $sErrMSGKeydecoded; ?>",message: "<?php echo  $sErrMSGdecoded;  ?>"});
		<?php } ?>
		<?php 
		if($this->session->flashdata('sErrMSGType')=='warning'){ 
		?>
				$.growl.warning({ message: "<?php echo  $sErrMSGdecoded;  ?>" });
		<?php } ?>
</script>
<?php } ?>

<input type="hidden" id="tabValidator" value="Yes"/>
