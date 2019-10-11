<?php

/*
|-------------------------------------------------------------------
|  Mobile Application Routes For Driver Application
|-------------------------------------------------------------------
*/
 
$route['v8/api/driver/login'] = 'v8/api/driver/login';
$route['v8/api/driver/logout'] = 'v8/api/driver/logout';
$route['v8/api/driver/update/availability'] = 'v8/api/driver/update_driver_availablity';
$route['v8/api/driver/update/mode'] = 'v8/api/driver/update_driver_mode';

$route['v8/api/driver/dashboard'] = 'v8/api/driver/driver_dashboard';
$route['v8/api/driver/password/change'] = 'v8/api/driver/change_password';
$route['v8/api/driver/password/forgot'] = 'v8/api/driver/forgot_password';

$route['v8/api/driver/get/rider'] = 'v8/api/driver/get_rider_information';

$route['v8/api/driver/banking/get'] = 'v8/api/driver/get_banking_details';
$route['v8/api/driver/banking/save'] = 'v8/api/driver/save_banking_details';

$route['v8/api/driver/trip/list'] = 'v8/api/driver/driver_all_ride_list';
$route['v8/api/driver/trip/view'] = 'v8/api/driver/view_driver_ride_information';

$route['v8/api/trip/request/ack'] = 'v8/api/driver/ack_ride_request';
$route['v8/api/trip/request/deny'] = 'v8/api/driver/deny_ride_request';

$route['v8/api/update-ride-location'] = "v8/api/driver/driver_update_ride_location";

$route['v8/api/trip/check'] = 'v8/api/driver/check_trip_payment_status';

$route['v8/api/driver/payment/list'] ='v8/api/driver/driver_all_payment_list';
$route['v8/api/driver/payment/summary'] ='v8/api/driver/view_driver_payment_information';
#------------------------------------------

$route['v8/api/trip/payment/request'] ='v8/api/driver/requesting_payment';
$route['v8/api/trip/payment/received'] ='v8/api/driver/cash_payment_received';
$route['v8/api/trip/payment/completed'] ='v8/api/driver/trip_completed';
$route['v8/api/driver/ride/view'] ='v8/api/driver/ride_list_view';

$route['v8/api/driver/multicar/change'] ='v8/api/driver/change_driver_multicar_status';
