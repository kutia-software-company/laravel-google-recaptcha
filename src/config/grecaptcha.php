<?php

return [
    'enabled'    => env('GRECAPTCHA_ENABLED', true),
    'site_key'   => env('GRECAPTCHA_KEY'),
    'secret_key' => env('GRECAPTCHA_SECRET'),
];
