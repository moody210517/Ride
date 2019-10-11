<?php

/*
|------------------------------------------------------------------------
|  Routes for Bookings
|------------------------------------------------------------------------
*/

$route['v8/api/location/update/driver'] = 'v8/api/booking/update_driver_location';

$route['v8/api/map/drivers'] = 					'v8/api/booking/get_drivers';
$route['v8/api/estimate/get'] = 					'v8/api/booking/get_estimate';
$route['v8/api/coupon/apply'] = 				'v8/api/booking/apply_coupon_code';
$route['v8/api/booking/make'] = 				'v8/api/booking/make_booking';
$route['v8/api/booking/retry'] = 				'v8/api/booking/retry_ride_request';
$route['v8/api/booking/delete'] = 			'v8/api/booking/delete_ride';
$route['v8/api/booking/accept'] = 			'v8/api/booking/accept_ride_request';

$route['v8/api/booking/track/user'] = 		'v8/api/booking/get_track_information_user';
$route['v8/api/booking/track/driver'] = 	'v8/api/booking/get_track_information_driver';

$route['v8/api/booking/cancel/reason'] = 'v8/api/booking/get_cancellation_reasons';
$route['v8/api/booking/cancel'] = 			'v8/api/booking/cancelling_ride';

$route['v8/api/trip/arrived'] = 					'v8/api/booking/location_arrived';
$route['v8/api/trip/begin'] = 					'v8/api/booking/begin_ride';
$route['v8/api/trip/end'] = 						'v8/api/booking/end_ride';

$route['v8/api/trip/list/user'] = 					'v8/api/booking/user_trip_list';
$route['v8/api/trip/view/user'] = 				'v8/api/booking/user_trip_view';

$route['v8/api/trip/details'] = 				'v8/api/booking/common_trip_details';
