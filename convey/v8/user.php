<?php

/*
|-------------------------------------------------------------------
|  Mobile Application Routes For User Application
|-------------------------------------------------------------------
*/
 
$route['v8/api/user/validate'] = 'v8/api/user/check_account';
$route['v8/api/user/validate/social'] = 'v8/api/user/check_social_login';
$route['v8/api/user/register'] = 'v8/api/user/register_user';
$route['v8/api/user/otp/resend'] = 'v8/api/user_profile/resend_otp';
$route['v8/api/user/login'] = 'v8/api/user/login_user';
$route['v8/api/user/login/social'] = 'v8/api/user/social_login';
$route['v8/api/user/logout'] = 'v8/api/user/logout_user';


$route['v8/api/user/location/update'] = 'v8/api/user/update_user_location';

$route['v8/api/locations'] = 'v8/api/user/get_location_list';
$route['v8/api/locations/category'] = 'v8/api/user/get_category_list';
$route['v8/api/ratecard'] = 'v8/api/user/get_rate_card';

$route['v8/api/invites/get'] = 'v8/api/user/get_invites';
$route['v8/api/earnings/get'] = 'v8/api/user/get_earnings_list';
$route['v8/api/wallet/get'] = 'v8/api/user/get_money_page';
$route['v8/api/wallet/trans'] = 'v8/api/user/get_transaction_list';

$route['v8/api/trip/share'] = 'v8/api/user/share_trip_status';

$route['v8/api/tips/apply'] = 'v8/api/user/apply_tips_amount';
$route['v8/api/tips/remove'] = 'v8/api/user/remove_tips_amount';
$route['v8/api/fare/breakup'] = 'v8/api/user/get_fare_breakup';

$route['v8/api/user/password/forgot'] = 'v8/api/user_profile/forgot_password_initiate';
$route['v8/api/user/password/reset'] = 'v8/api/user_profile/reset_password';

$route['v8/api/favourite/location/add'] = 'v8/api/user_profile/add_favourite_location';
$route['v8/api/favourite/location/edit'] = 'v8/api/user_profile/edit_favourite_location';
$route['v8/api/favourite/location/remove'] = 'v8/api/user_profile/remove_favourite_location';
$route['v8/api/favourite/location/display'] = 'v8/api/user_profile/display_favourite_location';

$route['v8/api/favourite/driver/add'] ='v8/api/user_profile/add_favourite_driver';
$route['v8/api/favourite/driver/edit'] ='v8/api/user_profile/edit_favourite_driver';
$route['v8/api/favourite/driver/remove'] ='v8/api/user_profile/remove_favourite_driver';
$route['v8/api/favourite/driver/display'] ='v8/api/user_profile/display_favourite_driver';

$route['v8/api/user/profile/get'] = 'v8/api/user_profile/get_user_profile';
$route['v8/api/user/profile/change/name'] = 'v8/api/user_profile/change_user_name';
$route['v8/api/user/profile/change/mobile'] = 'v8/api/user_profile/change_user_mobile_number';
$route['v8/api/user/profile/change/password'] = 'v8/api/user_profile/change_user_password';
$route['v8/api/user/profile/change/image'] = 'v8/api/user_profile/change_profile_image';

$route['v8/api/user/emergency/contact/update'] = 'v8/api/user_profile/emergency_contact_add_edit';
$route['v8/api/user/emergency/contact/view'] = 'v8/api/user_profile/emergency_contact_view';
$route['v8/api/user/emergency/contact/remove'] = 'v8/api/user_profile/emergency_contact_delete';
$route['v8/api/user/emergency/contact/alert'] = 'v8/api/user_profile/emergency_contact_alert';