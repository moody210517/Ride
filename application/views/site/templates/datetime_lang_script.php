<?php 
if($langCode != 'en') {
    $languagPath = 'lg_files/datetime_lang_'.$langCode.'.php';
    if (file_exists($languagPath)){
        require($languagPath);
        
        $monthNames = array();
        if(isset($dateTimeKeys['january_long'])) $monthNames[] = $dateTimeKeys['january_long']; else $monthNames[] = 'January';
        
        if(isset($dateTimeKeys['february_long'])) $monthNames[] = $dateTimeKeys['february_long']; else $monthNames[] = 'February';
        
        if(isset($dateTimeKeys['march_long'])) $monthNames[] = $dateTimeKeys['march_long']; else $monthNames[] = 'March';
        
        if(isset($dateTimeKeys['april_long'])) $monthNames[] = $dateTimeKeys['april_long']; else $monthNames[] = 'April';
        
        if(isset($dateTimeKeys['may_long'])) $monthNames[] = $dateTimeKeys['may_long']; else $monthNames[] = 'May';
        
        if(isset($dateTimeKeys['june_long'])) $monthNames[] = $dateTimeKeys['june_long']; else $monthNames[] = 'June';
        
        if(isset($dateTimeKeys['july_long'])) $monthNames[] = $dateTimeKeys['july_long']; else $monthNames[] = 'July';
        
        if(isset($dateTimeKeys['august_long'])) $monthNames[] = $dateTimeKeys['august_long']; else $monthNames[] = 'August';
        
        if(isset($dateTimeKeys['september_long'])) $monthNames[] = $dateTimeKeys['september_long']; else $monthNames[] = 'September';
        
        if(isset($dateTimeKeys['october_long'])) $monthNames[] = $dateTimeKeys['october_long']; else $monthNames[] = 'October';
        
        if(isset($dateTimeKeys['november_long'])) $monthNames[] = $dateTimeKeys['november_long']; else $monthNames[] = 'November';
        
        if(isset($dateTimeKeys['december_long'])) $monthNames[] = $dateTimeKeys['december_long']; else $monthNames[] = 'December';
        
        $monthNamesShort = array();
        if(isset($dateTimeKeys['january_short'])) $monthNamesShort[] = $dateTimeKeys['january_short']; else $monthNamesShort[] = 'Jan';
        
        if(isset($dateTimeKeys['february_short'])) $monthNamesShort[] = $dateTimeKeys['february_short']; else $monthNamesShort[] = 'Feb';
        
        if(isset($dateTimeKeys['march_short'])) $monthNamesShort[] = $dateTimeKeys['march_short']; else $monthNamesShort[] = 'Mar';
        
        if(isset($dateTimeKeys['april_short'])) $monthNamesShort[] = $dateTimeKeys['april_short']; else $monthNamesShort[] = 'Apr';
        
        if(isset($dateTimeKeys['may_short'])) $monthNamesShort[] = $dateTimeKeys['may_short']; else $monthNamesShort[] = 'May';
        
        if(isset($dateTimeKeys['june_short'])) $monthNamesShort[] = $dateTimeKeys['june_short']; else $monthNamesShort[] = 'Jun';
        
        if(isset($dateTimeKeys['july_short'])) $monthNamesShort[] = $dateTimeKeys['july_short']; else $monthNamesShort[] = 'Jul';
        
        if(isset($dateTimeKeys['august_short'])) $monthNamesShort[] = $dateTimeKeys['august_short']; else $monthNamesShort[] = 'Aug';
        
        if(isset($dateTimeKeys['september_short'])) $monthNamesShort[] = $dateTimeKeys['september_short']; else $monthNamesShort[] = 'Sep';
        
        if(isset($dateTimeKeys['october_short'])) $monthNamesShort[] = $dateTimeKeys['october_short']; else $monthNamesShort[] = 'Oct';
        
        if(isset($dateTimeKeys['november_short'])) $monthNamesShort[] = $dateTimeKeys['november_short']; else $monthNamesShort[] = 'Nov';
        
        if(isset($dateTimeKeys['december_short'])) $monthNamesShort[] = $dateTimeKeys['december_short']; else $monthNamesShort[] = 'Dec';
        
        $dayNames = array();
        
        if(isset($dateTimeKeys['sunday_long'])) $dayNames[] = $dateTimeKeys['sunday_long']; else $dayNames[] = 'Sunday';
        
        if(isset($dateTimeKeys['monday_long'])) $dayNames[] = $dateTimeKeys['monday_long']; else $dayNames[] = 'Monday';

        if(isset($dateTimeKeys['tuesday_long'])) $dayNames[] = $dateTimeKeys['tuesday_long']; else $dayNames[] = 'Tuesday';

        if(isset($dateTimeKeys['wednesday_long'])) $dayNames[] = $dateTimeKeys['wednesday_long']; else $dayNames[] = 'Wednesday';

        if(isset($dateTimeKeys['thursday_long'])) $dayNames[] = $dateTimeKeys['thursday_long']; else $dayNames[] = 'Thursday';

        if(isset($dateTimeKeys['friday_long'])) $dayNames[] = $dateTimeKeys['friday_long']; else $dayNames[] = 'Friday';

        if(isset($dateTimeKeys['saturday_long'])) $dayNames[] = $dateTimeKeys['saturday_long']; else $dayNames[] = 'Saturday';
        
        $dayNamesShort = array();
                
        if(isset($dateTimeKeys['sunday_short'])) $dayNamesShort[] = $dateTimeKeys['sunday_short']; else $dayNamesShort[] = 'Sun';

        if(isset($dateTimeKeys['monday_short'])) $dayNamesShort[] = $dateTimeKeys['monday_short']; else $dayNamesShort[] = 'Mon';

        if(isset($dateTimeKeys['tuesday_short'])) $dayNamesShort[] = $dateTimeKeys['tuesday_short']; else $dayNamesShort[] = 'Tue';

        if(isset($dateTimeKeys['wednesday_short'])) $dayNamesShort[] = $dateTimeKeys['wednesday_short']; else $dayNamesShort[] = 'Wed';

        if(isset($dateTimeKeys['thursday_short'])) $dayNamesShort[] = $dateTimeKeys['thursday_short']; else $dayNamesShort[] = 'Thu';

        if(isset($dateTimeKeys['friday_short'])) $dayNamesShort[] = $dateTimeKeys['friday_short']; else $dayNamesShort[] = 'Fri';

        if(isset($dateTimeKeys['saturday_short'])) $dayNamesShort[] = $dateTimeKeys['saturday_short']; else $dayNamesShort[] = 'Sat';
        
        $dayNamesMin = array();
                
        if(isset($dateTimeKeys['sunday_min'])) $dayNamesMin[] = $dateTimeKeys['sunday_min']; else $dayNamesMin[] = 'Su';

        if(isset($dateTimeKeys['monday_min'])) $dayNamesMin[] = $dateTimeKeys['monday_min']; else $dayNamesMin[] = 'Mo';

        if(isset($dateTimeKeys['tuesday_min'])) $dayNamesMin[] = $dateTimeKeys['tuesday_min']; else $dayNamesMin[] = 'Tu';

        if(isset($dateTimeKeys['wednesday_min'])) $dayNamesMin[] = $dateTimeKeys['wednesday_min']; else $dayNamesMin[] = 'We';

        if(isset($dateTimeKeys['thursday_min'])) $dayNamesMin[] = $dateTimeKeys['thursday_min']; else $dayNamesMin[] = 'Th';

        if(isset($dateTimeKeys['friday_min'])) $dayNamesMin[] = $dateTimeKeys['friday_min']; else $dayNamesMin[] = 'Fr';

        if(isset($dateTimeKeys['saturday_min'])) $dayNamesMin[] = $dateTimeKeys['saturday_min']; else $dayNamesMin[] = 'Sa';
        
        if(isset($dateTimeKeys['am_uppercase'])) $am_uppercase = $dateTimeKeys['am_uppercase']; else $am_uppercase = 'AM';
        
        if(isset($dateTimeKeys['pm_uppercase'])) $pm_uppercase = $dateTimeKeys['pm_uppercase']; else $pm_uppercase = 'PM';
      
?>
<script>

<?php if ($this->uri->segment(1) == ADMIN_ENC_URL || $this->uri->segment(1) == COMPANY_NAME || $this->uri->segment(1) == OPERATOR_NAME) { ?>

(function(factory){factory(jQuery.datepicker);}(function(datepicker){
datepicker.regional.lng = {
	closeText: "<?php if(isset($dateTimeKeys['closeText'])) echo $dateTimeKeys['closeText']; else 'Close'; ?>",
	prevText: "<?php if(isset($dateTimeKeys['prevText'])) echo $dateTimeKeys['prevText']; else echo 'Prev'; ?>",
	nextText: "<?php if(isset($dateTimeKeys['nextText'])) echo $dateTimeKeys['nextText']; else echo 'Next'; ?>",
	currentText: "<?php if(isset($dateTimeKeys['currentText'])) echo $dateTimeKeys['currentText']; else echo 'Today'; ?>",
    amNames: ['<?php echo $am_uppercase; ?>', 'A'],
	pmNames: ['<?php echo $pm_uppercase; ?>', 'P'],
	monthNames: <?php echo json_encode($monthNames); ?>,
	monthNamesShort: <?php echo json_encode($monthNamesShort); ?>,
	dayNames: <?php echo json_encode($dayNames); ?>,
	dayNamesShort: <?php echo json_encode($dayNamesShort); ?>,
	dayNamesMin: <?php echo json_encode($dayNamesMin); ?>,
	weekHeader: "He",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
    datepicker.setDefaults(datepicker.regional.lng);
    return datepicker.regional.lng;
}));
<?php }else{ ?> 
;(function($){
	$.fn.datetimepicker.defaults.language = '<?php echo $langCode; ?>';
	$.fn.datetimepicker.dates['<?php echo $langCode; ?>'] = {
		days: <?php echo json_encode($dayNames); ?>,
		daysShort: <?php echo json_encode($dayNamesShort); ?>,
		daysMin: <?php echo json_encode($dayNamesMin); ?>,
		months: <?php echo json_encode($monthNames); ?>,
		monthsShort: <?php echo json_encode($monthNamesShort); ?>,
		today: "<?php if(isset($dateTimeKeys['currentText'])) echo $dateTimeKeys['currentText']; else echo 'Today'; ?>",
		meridiem:    ["<?php echo $am_uppercase; ?>", "<?php echo $pm_uppercase; ?>"],
		suffix:      ["st", "nd", "rd", "th"],
		weekStart: 1
	};
}(jQuery));
<?php } ?>

</script>

<?php 
    }
}
 ?>