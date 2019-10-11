<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "site/landing";
$route['404_override'] = '';
$route['admin'] = "admin/adminlogin";

$route[ADMIN_ENC_URL] = ADMIN_ENC_URL."/adminlogin";

$route['login'] = "site/user/login_index_form";
$route['signup'] = "site/user/signup_index_form";
$route['language-settings'] = "site/landing/changeLangage";
$route['rider/login'] = "site/user/login_form";
$route['rider/reset-password'] = "site/user/forgot_password_form";
$route['rider/reset-password-form/(:any)'] = "site/user/reset_password_form";
$route['rider/signup'] = "site/user/rigister_form";
$route['rider/signup/(:any)'] = "site/user/rigister_form";
$route['rider/logout'] = "site/user/logout_rider";
$route['rider'] = "site/rider";
$route['rider/profile'] = "site/rider/profile_view";
$route['rider/my-rides'] = "site/rider/display_my_rides";
$route['rider/view-ride/(:any)'] = "site/rider/view_ride_details";
$route['rider/emergency-contact'] = "site/rider/emergency_contact";
$route['emergency-contact/confirm'] = "site/user/confirm_emergency_contact_form";
$route['rider/emergency-alert'] = "site/rider/emergency_alert_notification";
$route['rider/rate-card'] = "site/rider/display_rate_card";
$route['rider/fav-location'] = "site/rider/display_fav_locations";
$route['rider/change-password-form'] = "site/rider/change_password_form";


$route['rider/my-money'] = "site/rider/display_money_page";
$route['rider/wallet-transactions'] = "site/rider/display_transaction_list";
$route['rider/share-and-earnings'] = "site/rider/display_share_earnings";
$route['rider/earnings'] = "site/rider/display_earnings";
$route['rider/language-settings'] = "site/rider/language_settings_form";

$route['pages/(:any)'] = "site/cms";
$route['share/(:any)'] = "site/cms/share";
$route['rider/booking'] = "site/rider/booking_ride_form";


/* * *** user wallet recharge **** */

$route['rider/wallet-recharge/pay-option'] = "site/wallet_recharge/add_pay_wallet_payment_form";
$route['rider/wallet-recharge/success/(:any)'] = "site/wallet_recharge/pay_success";
$route['rider/wallet-recharge/failed/(:any)'] = "site/wallet_recharge/pay_failed";
$route['rider/wallet-recharge/pay-cancel'] = "site/wallet_recharge/payment_cancel";
$route['rider/wallet-recharge/pay-completed'] = "site/wallet_recharge/payment_return";



$route['invoice/(:any)'] = "site/user/load_invoice";

/* For Driver Panel */
$route['driver'] = "driver/profile";
$route['driver/login'] = "driver/profile/login_form";
$route['driver/logout'] = "driver/profile/driver_logout";
$route['driver/signup'] = "driver/profile/register_index_form";
$route['driver/signup/progress'] = "driver/profile/signup_progress_form";
$route['driver/reset-password'] = "driver/profile/driver_forgot_password_form";
$route['driver/reset-password-form/(:any)'] = "driver/profile/reset_password_form";
$route['driver/billing-summary/(:any)'] = "driver/payments/payment_summary/$1";



/* Cron Url List */
$route['send-req-for-later-rides'] = "cron/ride_later/get_later_rides";
$route['generate-billing'] = "cron/billing/generate_billing";
$route['g-collection'] = "cron/gcollection/garbgaecollection";
$route['cls-rec'] = "cron/cls_records";
$route['cls-rides'] = "cron/cls_rides";



/* * *   Extra routes     * */
$route['welcome-mail'] = 'driver/driver_welcome_mail'; // driver welcome mail 

$route['fb-redirect'] = "sociallogin/facebookRedirect";
$route['google-redirect'] = "sociallogin/googleRedirect";
$route['upload-fb-profile-pic'] = "site/user/upload_fb_profile_pic";
$route['rider/social-signup'] = "site/user/social_rigister_form";

$route['dummy'] = "demo/dummy";

$route['get-category-list'] = "insert_drivers/get_drivers_category";
$route['add-new-driver'] = "insert_drivers/addNewDriver";
$route['prepare-invoice'] = "site/prepare_invoice/make_and_send";
$route['send-bulk-emails']='site/mail/send_bulk_emails_to_users_and_drivers';
$route['convert-lang'] = "site/lang_converter";

$route['app/driver/signup'] = "site/app_driver/register_form";
$route['app/driver/signup/progress'] = "site/app_driver/signup_progress_form";

$route['v4/app/driver/signup'] = "site/app_driver/register_form";

$route['app/driver/signup/success'] = "site/app_driver/success";
$route['v4/app/driver/signup/success'] = "site/app_driver/success";

$route['n1/app/driver/signup'] = "site/app_driver/register_form";
$route['n1/app/driver/signup/success'] = "site/app_driver/success";


$route['v5/app/driver/signup'] = "site/app_driver/register_form";
$route['v5/app/driver/signup/success'] = "site/app_driver/success";

$route['v6/app/driver/signup'] = "site/app_driver/register_form";
$route['v6/app/driver/signup/success'] = "site/app_driver/success";
$route['(:any)/pages/(:any)'] = "site/cms";

$route['track'] = "site/tracking/track_ride_map_details";
$route['track-ride'] = "site/tracking/track_ride_map_details";
#$route['track-ride'] = "site/user/track_ride_location_details";

#$route['load-image'] = "site/landing/load_image";
#$route['images/(:any)'] = 'site/landing/load_image';

/* For operator Panel */
$route[OPERATOR_NAME] = OPERATOR_NAME."/settings";
/* For vendor Panel */
$route[COMPANY_NAME] = COMPANY_NAME."/login";
$route['operator/signup'] = "operator/signup/register";
$route['operator/login'] = "operator/signup/login";
/*
|----------------------------------------------------------------
|Loading Mobile Api  routes file regarding version
|----------------------------------------------------------------
|
*/

foreach (glob("convey/v8/*.php") as $filename) {
    if (is_file($filename)) {
        require_once $filename;
    }
}



/* End of file routes.php */
/* Location: ./application/config/routes.php */