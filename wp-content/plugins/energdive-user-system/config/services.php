<?php
return [
    'email' => [
        'driver' => 'ses', // amazon ses
        'from'   => 'no-reply@energdive.com'
    ],
    'sms' => [
        'driver' => 'msg91'
    ]
];
