<?php

/*
|-------------------------------------------------------------------
|  Mobile Application Routes For Common Application
|-------------------------------------------------------------------
*/

$route['v8/api/get-app-info'] = 'v8/api/common/get_app_info';
$route['v8/api/notification/status'] ='v8/api/common/update_receive_mode';
$route['v8/api/language/update'] = "v8/api/common/update_primary_language";

$route['v8/api/report/send'] = 'v8/api/common/send_reports';
$route['v8/api/report/list'] = 'v8/api/common/reports_list';

$route['v8/api/reviews/options'] ='v8/api/common/get_review_options';
$route['v8/api/reviews/submit'] ='v8/api/common/submit_reviews';
$route['v8/api/reviews/skip'] ='v8/api/common/skip_reviews';

$route['v8/api/masking/call'] ='v8/api/common/make_call';
$route['v8/api/masking/sms'] ='v8/api/common/send_sms';

$route['v8/api/chat/open'] ='v8/api/common/open_chat';
$route['v8/api/chat/push-chat-message'] ='v8/api/common/push_chat_message';