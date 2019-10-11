<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');




/*
  |
  |--------------------------------------------------------------------------
  | Collection Constants
  |--------------------------------------------------------------------------
  |
 */

define('COL_PREF', 'col_');

define('USERS', COL_PREF . 'users');
define('ADMIN', COL_PREF . 'admin');
define('SUBADMIN', COL_PREF . 'subadmin');
define('CONTINENTS', COL_PREF . 'continents');
define('COUNTRY', COL_PREF . 'country');
define('CURRENCY', COL_PREF . 'currency');
define('PROMOCODE', COL_PREF . 'promocode');
define('NEWSLETTER', COL_PREF . 'newsletter');
define('USER_LOCATION', COL_PREF . 'user_location');
define('BRAND', COL_PREF . 'brand');
define('MODELS', COL_PREF . 'models');
define('DRIVERS', COL_PREF . 'drivers');
define('VEHICLES', COL_PREF . 'vehicles');
define('DOCUMENTS', COL_PREF . 'documents');
define('CATEGORY', COL_PREF . 'category');
define('LOCATIONS', COL_PREF . 'locations');
define('STATISTICS', COL_PREF . 'statistics');
define('SMS_TEMPLATE', COL_PREF . 'sms_template');
define('RIDES', COL_PREF . 'rides');
define('FAVOURITE', COL_PREF . 'favourites');
define('CANCELLATION_REASON', COL_PREF . 'cancellation_reason');
define('PAYMENT_GATEWAY', COL_PREF . 'payment_gateway');
define('REFER_HISTORY', COL_PREF . 'refer_history');
define('WALLET', COL_PREF . 'wallet');
define('REVIEW_OPTIONS', COL_PREF . 'review_options');
define('PAYMENTS', COL_PREF . 'payments');
define('MOBILE_PAYMENT', COL_PREF . 'mobile_payments');
define('BANNER', COL_PREF . 'banner');
define('CMS', COL_PREF . 'cms');
define('WALLET_RECHARGE', COL_PREF . 'wallet_recharge');
define('NEWSLETTER_SUBSCRIBER', COL_PREF . 'newsletter_subscriber');
define('TRANSACTION', COL_PREF . 'transaction');
define('BILLINGS', COL_PREF . 'billings');
define('PAYMENT_TRANSACTION', COL_PREF . 'payment_transaction');

define('TRACKING', COL_PREF . 'tracking');
define('LANGUAGES', COL_PREF . 'languages');
define('MOBILE_LANGUAGES', COL_PREF . 'mobile_languages');

define('NOTIFICATION_TEMPLATES', COL_PREF . 'notification_templates');
define('LANDING_CONTENT', COL_PREF . 'landing_content');
define('INVOICE', COL_PREF . 'invoice_template');
define('SHARE_POOL_INVOICE', COL_PREF . 'share_pool_invoice_template');

define('MENU', COL_PREF . 'menu');

define('TRAVEL_HISTORY', COL_PREF . 'travel_history');
define('RIDE_HISTORY', COL_PREF . 'ride_history');

define('RIDE_STATISTICS', COL_PREF . 'ride_statistics');
define('MULTI_LANGUAGES', COL_PREF . 'multi_languages');

define('OPERATORS', COL_PREF . 'operators');
define('REPORTS', COL_PREF . 'reports');

define('DRIVERS_MILEAGE', COL_PREF . 'driver_mileage');

define('TESTIMONIALS', COL_PREF . 'testimonials');
define('TEMP_DRIVERS', COL_PREF . 'temp_drivers');
define('DRIVERS_ONLINE_DURATION', COL_PREF . 'drivers_online_duration');
define('DRIVERS_ACTIVITY', COL_PREF . 'drivers_activity');
define('DRIVER_STATISTICS', COL_PREF . 'driver_statistics');

define('RIDE_REQ_HISTORY', COL_PREF . 'ride_req_history');
define('MISSED_RIDES', COL_PREF . 'missed_rides');

/*
|
|--------------------------------------------------------------------------
| Operator route constant
|--------------------------------------------------------------------------
|
*/
define('OPERATOR_NAME', 'operator');


/*
|
|--------------------------------------------------------------------------
| Company route constant
|--------------------------------------------------------------------------
|
*/
define('COMPANY_NAME', 'company');

/*
|
|--------------------------------------------------------------------------
| company route constant
|--------------------------------------------------------------------------
|
*/
define('COMPANY', COL_PREF . 'company');

/*
  |
  |--------------------------------------------------------------------------
  | Path Constants
  |--------------------------------------------------------------------------
  |
 */

define('USER_PROFILE_IMAGE_DEFAULT', 'images/users/default.jpg');
define('USER_PROFILE_THUMB_DEFAULT', 'images/users/thumb/default.jpg');
define('USER_PROFILE_IMAGE', 'images/users/');
define('USER_PROFILE_THUMB', 'images/users/thumb/');

define('CATEGORY_IMAGE_DEFAULT', 'images/category/default.png');
define('CATEGORY_IMAGE', 'images/category/');

define('BRAND_IMAGE_DEFAULT', 'images/brand/default.png');
define('BRAND_THUMB_DEFAULT', 'images/brand/thumbnail/default.png');
define('BRAND_IMAGE', 'images/brand/');
define('BRAND_THUMB', 'images/brand/thumbnail/');

define('VEHICLE_TYPE_DEFAULT', 'images/vehicle/default.png');
define('VEHICLE_TYPE', 'images/vehicle/');

define('ICON_IMAGE_DEFAULT', 'images/icons/default.png');
define('ICON_IMAGE_ACTIVE', 'images/icons/active.png');
define('ICON_MAP_CAR_IMAGE', 'images/icons/map_car.png');
define('ICON_IMAGE', 'images/icons/');


define('POOL_ID', 'c-pool');


/* End of file constants.php */
/* Location: ./application/config/constants.php */