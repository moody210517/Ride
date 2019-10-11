<?php

/*
|----------------------------------------------------------------------------
|  Mobile Application Routes F
|----------------------------------------------------------------------------
*/
/* User Application payment Gateway integration */
$route['v8/api/payment/list'] = 'v8/api/user/get_payment_list';
$route['v8/api/payment/cash'] = 'v8/api/user/payment_by_cash';
$route['v8/api/payment/wallet'] = 'v8/api/user/payment_by_wallet';
$route['v8/api/payment/gateway'] = 'v8/api/user/payment_by_gateway';
$route['v8/api/payment/auto'] = 'v8/api/user/payment_by_auto_charge';
$route['v8/api/payment/proceed'] = 'v8/api/user/proceed_payment';

$route['v8/api/payment/proceed'] = 'v8/api/user/proceed_payment';
$route['v8/api/payment/proceed'] = 'v8/api/user/proceed_payment';
$route['v8/webview/trip/success/(:any)'] = "v8/api/payment/success";
$route['v8/webview/trip/failed/(:any)'] = "v8/api/payment/failed";
$route['v8/webview/trip/(:any)'] = "v8/api/payment/returns";

$route['v8/api/invoice/send'] = 'v8/api/user/mail_invoice';

$route['v8/webview/wallet/form'] = "v8/api/wallet_payment/add_pay_wallet_payment_form";
$route['v8/webview/wallet/auto'] = "v8/api/wallet_payment/stripe_payment_process";
$route['v8/webview/wallet/success/(:any)'] = "v8/api/wallet_payment/success";
$route['v8/webview/wallet/failed/(:any)'] = "v8/api/wallet_payment/failed";
$route['v8/webview/wallet/cancel'] = "v8/api/wallet_payment/returns";
$route['v8/webview/wallet/completed'] = "v8/api/wallet_payment/returns";