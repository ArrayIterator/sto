<?php
// # Sample
//create_cookie(COOKIE_STUDENT_NAME, create_json_hash(1, 'student'));
//create_cookie(COOKIE_SUPERVISOR_NAME, create_json_hash(1, 'supervisor'));

$loggedAs = [];
$data = get_current_student_data();
if ($data) {
    $loggedAs['student'] = [
        'id' => $data['id'],
        'uuid' => $data['uuid'],
        'username' => $data['user']->username,
    ];
}

$data = get_current_supervisor_data();
if ($data) {
    $loggedAs['supervisor'] = [
        'id' => $data['id'],
        'uuid' => $data['uuid'],
        'username' => $data['user']->username,
    ];
}

json(200, [
    'login' => !empty($loggedAs),
    'as' => $loggedAs,
    'online' => [
        'student' => student_online()->count(),
        'supervisor' => supervisor_online()->count(),
    ],
    'response_time' => microtime(true) - MICRO_TIME_FLOAT,
    'time' => time(),
    'timezone' => date_default_timezone_get(),
]);
