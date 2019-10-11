<?php

/*
|-------------------------------------------------------------------
|  Mobile Application Routes For Driver Registration
|-------------------------------------------------------------------
*/
 
$route['v8/api/driver/registration/init'] = 'v8/api/driver_registration/init';
$route['v8/api/driver/registration/vehicles/get'] = 'v8/api/driver_registration/get_vehicle_details';
$route['v8/api/driver/registration/verify/mobile'] = 'v8/api/driver_registration/check_mobile';
$route['v8/api/driver/registration/verify/email'] = 'v8/api/driver_registration/check_email';
$route['v8/api/driver/registration/image/upload'] = 'v8/api/driver_registration/upload_picture';
$route['v8/api/driver/registration/verify/vehiclenumber'] = 'v8/api/driver_registration/check_vehicle_number';
$route['v8/api/driver/registration/document/get'] = 'v8/api/driver_registration/get_document_list';
$route['v8/api/driver/registration/document/upload'] = 'v8/api/driver_registration/upload_document';
$route['v8/api/driver/registration/submit'] = 'v8/api/driver_registration/do_register';