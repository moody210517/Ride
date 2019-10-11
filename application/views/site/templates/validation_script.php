<?php $checkbox_lan=get_language_array_for_keyword($this->data['langCode']);?>
<?php  $validationArr = get_language_array_for_validation($this->data['langCode']);  ?>
<script>
var datatable_entries_per_page = "<?php if(array_key_exists('datatable_entries_per_page',$checkbox_lan)){ echo $checkbox_lan['datatable_entries_per_page']; }else { echo "Entries per page";} ?>";
var datatable_no_data_available = "<?php if(array_key_exists('datatable_no_data_available',$checkbox_lan)){ echo $checkbox_lan['datatable_no_data_available']; }else { echo "No data available in table";} ?>";
var datatable_no_record_found = "<?php if(array_key_exists('datatable_no_record_found',$checkbox_lan)){ echo $checkbox_lan['datatable_no_record_found']; }else { echo "No matching records found";} ?>";
var datatable_search = "<?php if(array_key_exists('datatable_search',$checkbox_lan)){ echo $checkbox_lan['datatable_search']; }else { echo "Search";} ?>";
var pagination_first = "<?php if(array_key_exists('pagination_first',$checkbox_lan)){ echo $checkbox_lan['pagination_first']; }else { echo "First";} ?>";
var pagination_last = "<?php if(array_key_exists('pagination_last',$checkbox_lan)){ echo $checkbox_lan['pagination_last']; }else { echo "Last";} ?>";
var pagination_previous = "<?php if(array_key_exists('pagination_previous',$checkbox_lan)){ echo $checkbox_lan['pagination_previous']; }else { echo "Previous";} ?>";
var pagination_next = "<?php if(array_key_exists('pagination_next',$checkbox_lan)){ echo $checkbox_lan['pagination_next']; }else { echo "Next";} ?>";
var admin_checkBoxvalidationadmin='<?php if ($this->lang->line('common_please_select_box') != '') echo stripslashes($this->lang->line('common_please_select_box')); else echo 'Please Select the CheckBox'; ?>';
var admin_checkboxvalidationuser='<?php if ($this->lang->line('common_whether_continue_action') != '') echo stripslashes($this->lang->line('common_whether_continue_action')); else echo 'Whether you want to continue this action?'; ?>';
var admin_select_mail_tempolate='<?php if ($this->lang->line('common_select_mail_template') != '') echo stripslashes($this->lang->line('common_select_mail_template')); else echo 'Please select the mail template'; ?>';
var admin_no_records_found='<?php if ($this->lang->line('admin_common_no_record_found') != '') echo stripslashes($this->lang->line('admin_common_no_record_found')); else echo 'No records found'; ?>';
var admin_common_enter_email_id='<?php if ($this->lang->line('admin_common_enter_email_id') != '') echo stripslashes($this->lang->line('admin_common_enter_email_id')); else echo 'Please Enter The Email ID'; ?>';
var admin_common_correct_email_id='<?php if ($this->lang->line('admin_common_enter_correct_email_id') != '') echo stripslashes($this->lang->line('admin_common_enter_correct_email_id')); else echo 'Please Enter The Correct Email ID'; ?>';
var admin_common_change_status_record='<?php if ($this->lang->line('admin_common_change_status_record') != '') echo stripslashes($this->lang->line('admin_common_change_status_record')); else echo 'You are about to change the status of this record ! Continue?'; ?>';
var admin_select_only_one_checkbox='<?php if ($this->lang->line('admin_select_only_one_checkbox') != '') echo stripslashes($this->lang->line('admin_select_only_one_checkbox')); else echo 'Please Select only one CheckBox at a time'; ?>';
var admin_delete_record_restore_later='<?php if ($this->lang->line('admin_delete_record_restore_later') != '') echo stripslashes($this->lang->line('admin_delete_record_restore_later')); else echo 'You are about to delete this record. <br />It cannot be restored at a later time! Continue?'; ?>';

var admin_delete_record_can_restore_later='<?php if ($this->lang->line('admin_delete_record_can_restore_later') != '') echo stripslashes($this->lang->line('admin_delete_record_can_restore_later')); else echo 'You are about to delete this record. <br />It can be restored at a later time! Continue?'; ?>';

var admin_change_mode_record='<?php if ($this->lang->line('admin_change_mode_record') != '') echo stripslashes($this->lang->line('admin_change_mode_record')); else echo 'You are about to change the display mode of this record ! Continue?'; ?>';
var admin_ride_pickup_location='<?php if ($this->lang->line('admin_rides_pickup_location') != '') echo stripslashes($this->lang->line('admin_rides_pickup_location')); else echo 'Pickup Location'; ?>';
var admin_ride_drop_location='<?php if ($this->lang->line('admin_rides_drop_location') != '') echo stripslashes($this->lang->line('admin_rides_drop_location')); else echo 'Drop Location'; ?>';
var admin_ride_payment_by='<?php if ($this->lang->line('admin_ride_payment_by') != '') echo stripslashes($this->lang->line('admin_ride_payment_by')); else echo 'Payment by'; ?>';
var success='<?php if ($this->lang->line('admin_success') != '') echo stripslashes($this->lang->line('admin_success')); else echo 'Success'; ?>';
var error='<?php if ($this->lang->line('admin_error') != '') echo stripslashes($this->lang->line('admin_error')); else echo 'Error'; ?>';
var Confirmation='<?php if ($this->lang->line('admin_confirm') != '') echo stripslashes($this->lang->line('admin_confirm')); else echo 'Confirmation'; ?>';
var Yes='<?php if ($this->lang->line('admin_yes') != '') echo stripslashes($this->lang->line('admin_yes')); else echo 'Yes'; ?>';
var No='<?php if ($this->lang->line('admin_no') != '') echo stripslashes($this->lang->line('admin_no')); else echo 'No'; ?>';
var security_purpose='<?php if ($this->lang->line('security_purpose') != '') echo stripslashes($this->lang->line('security_purpose')); else echo 'For Security Purpose, Please Enter Email Id'; ?>';
var security_delete='<?php if ($this->lang->line('security_delete') != '') echo stripslashes($this->lang->line('security_delete')); else echo 'Delete Confirmation'; ?>';
var no_results_text='<?php if ($this->lang->line('no_results_text') != '') echo stripslashes($this->lang->line('no_results_text')); else echo 'No results match'; ?>';
var required_txt = "<?php if(array_key_exists('required',$validationArr)){ echo $validationArr['required']; }else { echo "This field is required.";} ?>";
	var remote_txt = "<?php if(array_key_exists('remote',$validationArr)){ echo $validationArr['remote']; }else { echo "Please fix this field.";} ?>";
	var email_txt = "<?php if(array_key_exists('email',$validationArr)){ echo $validationArr['email']; }else { echo "Please enter a valid email address.";} ?>";
	var url_txt = "<?php if(array_key_exists('url',$validationArr)){ echo $validationArr['url']; }else { echo "Please enter a valid URL.";} ?>";
	var date_txt = "<?php if(array_key_exists('date',$validationArr)){ echo $validationArr['date']; }else { echo "Please enter a valid date.";} ?>";
	var dateISO_txt = "<?php if(array_key_exists('dateISO',$validationArr)){ echo $validationArr['dateISO']; }else { echo "Please enter a valid date (ISO).";} ?>";
	var number_txt = "<?php if(array_key_exists('number',$validationArr)){ echo $validationArr['number']; }else { echo "Please enter a valid number.";} ?>";
	var alphanumeric_txt = "<?php if(array_key_exists('alphanumeric',$validationArr)){ echo $validationArr['alphanumeric']; }else { echo "Please enter alpha numeric value (2 - 20 characters).";} ?>";	

	var lanlinenumber_txt = "<?php if(array_key_exists('lanlinenumber',$validationArr)){ echo $validationArr['lanlinenumber']; }else { echo "Please enter valid phone number.";} ?>";	
    
	var admin_username = "<?php if(array_key_exists('admin_username',$validationArr)){ echo $validationArr['admin_username']; }else { echo "Please enter admin username";} ?>";	
	var admin_email = "<?php if(array_key_exists('admin_email',$validationArr)){ echo $validationArr['admin_email']; }else { echo "Please enter admin email address";} ?>";	
	
	
	
	var positiveNumber_txt = "<?php if(array_key_exists('positiveNumber',$validationArr)){ echo $validationArr['positiveNumber']; }else { echo "Please enter a valid positive number.";} ?>";
	var wholeNumber_txt = "<?php if(array_key_exists('wholeNumber',$validationArr)){ echo $validationArr['wholeNumber']; }else { echo "Please enter a valid whole number.";} ?>";
	var currencyT_txt = "<?php if(array_key_exists('currencyT',$validationArr)){ echo $validationArr['currencyT']; }else { echo "Please enter lesser than 10000000.";} ?>";
	var minfloatingNumber_txt = "<?php if(array_key_exists('minfloatingNumber',$validationArr)){ echo $validationArr['minfloatingNumber']; }else { echo "Please enter a less than 3 decimal point number.";} ?>";
	var phoneNumber_txt = "<?php if(array_key_exists('phoneNumber',$validationArr)){ echo $validationArr['phoneNumber']; }else { echo "Please enter a valid phone number.";} ?>";
	var digits_txt = "<?php if(array_key_exists('digits',$validationArr)){ echo $validationArr['digits']; }else { echo "Please enter only digits.";} ?>";
	var creditcard_txt = "<?php if(array_key_exists('creditcard',$validationArr)){ echo $validationArr['creditcard']; }else { echo "Please enter a valid credit card number.";} ?>";
	var equalTo_txt = "<?php if(array_key_exists('equalTo',$validationArr)){ echo $validationArr['equalTo']; }else { echo "Please enter the same value again.";} ?>";
	var lesserThan_txt = "<?php if(array_key_exists('lesserThan',$validationArr)){ echo $validationArr['lesserThan']; }else { echo "enter a value less than or equal to maximum amount";} ?>";
	var lesserThanCoup_txt = "<?php if(array_key_exists('lesserThanCoup',$validationArr)){ echo $validationArr['lesserThanCoup']; }else { echo "enter a value less than or equal to usage limit per coupon";} ?>";
	var greaterThan_txt = "<?php if(array_key_exists('greaterThan',$validationArr)){ echo $validationArr['greaterThan']; }else { echo "Please enter a value greater than or equal to minimum amount";} ?>";
	var accept_txt = "<?php if(array_key_exists('accept',$validationArr)){ echo $validationArr['accept']; }else { echo "Please enter a value with a valid extension.";} ?>";
	var maxlength_txt = "<?php if(array_key_exists('maxlength',$validationArr)){ echo $validationArr['maxlength']; }else { echo "Please enter no more than {0} characters.";} ?>";
	var minlength_txt = "<?php if(array_key_exists('minlength',$validationArr)){ echo $validationArr['minlength']; }else { echo "Please enter at least {0} characters.";} ?>";
	var rangelength_txt = "<?php if(array_key_exists('rangelength',$validationArr)){ echo $validationArr['rangelength']; }else { echo "Please enter a value between {0} and {1} characters long.";} ?>";
	var range_txt = "<?php if(array_key_exists('range',$validationArr)){ echo $validationArr['range']; }else { echo "Please enter a value between {0} and {1}.";} ?>";
	var max_txt = "<?php if(array_key_exists('max',$validationArr)){ echo $validationArr['max']; }else { echo "Please enter a value less than or equal to {0}.";} ?>";
	var min_txt = "<?php if(array_key_exists('min',$validationArr)){ echo $validationArr['min']; }else { echo "Please enter a value greater than or equal to {0}.";} ?>";
	var firstname_txt = "<?php if(array_key_exists('firstname',$validationArr)){ echo $validationArr['firstname']; }else { echo "Please enter your firstname";} ?>";
	var username_txt = "<?php if(array_key_exists('username',$validationArr)){ echo $validationArr['username']; }else { echo "Please enter a username";} ?>";
	var username_length_txt = "<?php if(array_key_exists('username_length',$validationArr)){ echo $validationArr['username_length']; }else { echo "Your username must consist of at least 2 characters";} ?>";
	var password_txt = "<?php if(array_key_exists('password',$validationArr)){ echo $validationArr['password']; }else { echo "Please provide a password";} ?>";
	var new_password_txt = "<?php if(array_key_exists('new_password',$validationArr)){ echo $validationArr['new_password']; }else { echo "Please provide a new password";} ?>";
	var password_length_txt = "<?php if(array_key_exists('password_length',$validationArr)){ echo $validationArr['password_length']; }else { echo "Password must be at least 6 characters long";} ?>";
	var retypr_password_txt = "<?php if(array_key_exists('retypr_password',$validationArr)){ echo $validationArr['retypr_password']; }else { echo "Please re-type your new password";} ?>";
	var same_password_txt = "<?php if(array_key_exists('same_password',$validationArr)){ echo $validationArr['same_password']; }else { echo "Please enter the same password as above";} ?>";
	var valid_email_address_txt = "<?php if(array_key_exists('valid_email_address',$validationArr)){ echo $validationArr['valid_email_address']; }else { echo "Please enter a valid email address.";} ?>";
	var accept_policy_txt = "<?php if(array_key_exists('accept_policy',$validationArr)){ echo $validationArr['accept_policy']; }else { echo "Please accept our policy";} ?>";
	var sub_admin_email_txt = "<?php if(array_key_exists('sub_admin_email',$validationArr)){ echo $validationArr['sub_admin_email']; }else { echo "Please enter sub-admin email address";} ?>";
	var admin_username_txt = "<?php if(array_key_exists('sub_admin_username',$validationArr)){ echo $validationArr['sub_admin_username']; }else { echo "Please enter sub admin username";} ?>";
	var checking_number = "<?php if(array_key_exists('checking_number',$validationArr)){ echo $validationArr['checking_number']; }else { echo "Checking number";} ?>";
	var new_admin_password_txt = "<?php if(array_key_exists('new_admin_password',$validationArr)){ echo $validationArr['new_admin_password']; }else { echo "Please enter new admin password";} ?>";
	var admin_menu_choose = "<?php if(array_key_exists('admin_menu_choose',$checkbox_lan)){ echo $checkbox_lan['admin_menu_choose']; }else { echo "Choose an option";} ?>";
	var admin_menu_all = "<?php if(array_key_exists('admin_menu_all',$checkbox_lan)){ echo $checkbox_lan['admin_menu_all']; }else { echo "All";} ?>";
	var ride_transaction='<?php if ($this->lang->line('ride_transaction_proceed') != '') echo stripslashes($this->lang->line('ride_transaction_proceed')); else echo 'Please Wait... Your Transaction Being Processed'; ?>';
    var ride_recharge_amount='<?php if ($this->lang->line('ride_enter_recharge_amount') != '') echo stripslashes($this->lang->line('ride_enter_recharge_amount')); else echo 'Please enter recharge amount'; ?>';
	var ride_recharge_amount_greater='<?php if ($this->lang->line('ride_recharge_amount_greater') != '') echo stripslashes($this->lang->line('ride_recharge_amount_greater')); else echo 'Please enter recharge amount greater than 0'; ?>';
    var ride_recharge_amount_number='<?php if ($this->lang->line('ride_enter_recharge_amount_number') != '') echo stripslashes($this->lang->line('ride_enter_recharge_amount_number')); else echo 'Recharge amount should be a number'; ?>';
    var ride_amount_between='<?php if ($this->lang->line('ride_amount_between') != '') echo stripslashes($this->lang->line('ride_amount_between')); else echo 'Recharge amount should be between'; ?>';
	var onlyalphabets_txt = "<?php if(array_key_exists('onlyalphabets_txt',$validationArr)){ echo $validationArr['onlyalphabets_txt']; }else { echo "Please enter only alpha alphabets.";} ?>";
	var invalid_promo_code = "<?php if($this->lang->line('rider_invalid_promo_code') != '') echo stripslashes($this->lang->line('rider_invalid_promo_code')); else echo 'Invalid promo code'; ?>";
	
	var rider_promo_code = "<?php if($this->lang->line('rider_promo_code') != '') echo stripslashes($this->lang->line('rider_promo_code')); else echo 'Promo code'; ?>";
	var rider_promo_applied = "<?php if($this->lang->line('rider_promo_applied') != '') echo stripslashes($this->lang->line('rider_promo_applied')); else echo 'applied successfully! You will get'; ?>";
	var rider_promo_discount = "<?php if($this->lang->line('rider_promo_discount') != '') echo stripslashes($this->lang->line('rider_promo_discount')); else echo 'discount on this ride.'; ?>";
	
	
	var site_data_table_activate_to_sort_column_ascending='<?php if ($this->lang->line('site_data_table_activate_to_sort_column_ascending') != '') echo stripslashes($this->lang->line('site_data_table_activate_to_sort_column_ascending')); else echo 'activate to sort column ascending'; ?>';
	var site_data_table_activate_to_sort_column_descending='<?php if ($this->lang->line('site_data_table_activate_to_sort_column_descending') != '') echo stripslashes($this->lang->line('site_data_table_activate_to_sort_column_descending')); else echo 'activate to sort column descending'; ?>';
	var site_data_table_first_ucfirst='<?php if ($this->lang->line('site_data_table_first_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_first_ucfirst')); else echo 'First'; ?>';
	var site_data_table_last_ucfirst='<?php if ($this->lang->line('site_data_table_last_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_last_ucfirst')); else echo 'Last'; ?>';
	var site_data_table_next_ucfirst='<?php if ($this->lang->line('site_data_table_next_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_next_ucfirst')); else echo 'Next'; ?>';
	var site_data_table_previous_ucfirst='<?php if ($this->lang->line('site_data_table_previous_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_previous_ucfirst')); else echo 'Previous'; ?>';
	var site_data_table_no_data_available_in_table='<?php if ($this->lang->line('site_data_table_no_data_available_in_table') != '') echo stripslashes($this->lang->line('site_data_table_no_data_available_in_table')); else echo 'No data available in table'; ?>';
	var site_data_table_showing='<?php if ($this->lang->line('site_data_table_showing') != '') echo stripslashes($this->lang->line('site_data_table_showing')); else echo 'Showing'; ?>';
	var site_data_table_to='<?php if ($this->lang->line('site_data_table_to') != '') echo stripslashes($this->lang->line('site_data_table_to')); else echo 'to'; ?>';
	var site_data_table_of='<?php if ($this->lang->line('site_data_table_of') != '') echo stripslashes($this->lang->line('site_data_table_of')); else echo 'of'; ?>';
	var site_data_table_entries='<?php if ($this->lang->line('site_data_table_entries') != '') echo stripslashes($this->lang->line('site_data_table_entries')); else echo 'entries'; ?>';
	var site_data_table_show_ucfirst='<?php if ($this->lang->line('site_data_table_show_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_show_ucfirst')); else echo 'Show'; ?>';
	var site_data_table_loading_ucfirst='<?php if ($this->lang->line('site_data_table_loading_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_loading_ucfirst')); else echo 'Loading'; ?>';
	var site_data_table_processing_ucfirst='<?php if ($this->lang->line('site_data_table_processing_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_processing_ucfirst')); else echo 'Processing'; ?>';
	var site_data_table_search_ucfirst='<?php if ($this->lang->line('site_data_table_search_ucfirst') != '') echo stripslashes($this->lang->line('site_data_table_search_ucfirst')); else echo 'Search'; ?>';
	var site_data_table_no_matching_records_found='<?php if ($this->lang->line('site_data_table_no_matching_records_found') != '') echo stripslashes($this->lang->line('site_data_table_no_matching_records_found')); else echo 'No matching records found'; ?>';
	var site_data_table_filtered='<?php if ($this->lang->line('site_data_table_filtered') != '') echo stripslashes($this->lang->line('site_data_table_filtered')); else echo 'filtered'; ?>';
	var site_data_table_from='<?php if ($this->lang->line('site_data_table_from') != '') echo stripslashes($this->lang->line('site_data_table_from')); else echo 'from'; ?>';
	var site_data_table_total='<?php if ($this->lang->line('site_data_table_total') != '') echo stripslashes($this->lang->line('site_data_table_total')); else echo 'total'; ?>';
    
    var chzn_no_result_found_txt ='<?php if ($this->lang->line('chzn_no_result_found') != '') echo stripslashes($this->lang->line('chzn_no_result_found')); else echo 'No results match'; ?>';
    
    var chzn_select_an_option_txt ='<?php if ($this->lang->line('chzn_select_an_option') != '') echo stripslashes($this->lang->line('chzn_select_an_option')); else echo 'Select an Option'; ?>';
    
    var chzn_select_some_option_txt ='<?php if ($this->lang->line('chzn_select_some_option') != '') echo stripslashes($this->lang->line('chzn_select_some_option')); else echo 'Select Some Options'; ?>';
    
</script>