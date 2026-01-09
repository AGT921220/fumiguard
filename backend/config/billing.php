<?php

return [
    'stripe' => [
        'secret' => env('STRIPE_SECRET_KEY', ''),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
        'success_url' => env('STRIPE_CHECKOUT_SUCCESS_URL', env('APP_URL').'/billing/success?session_id={CHECKOUT_SESSION_ID}'),
        'cancel_url' => env('STRIPE_CHECKOUT_CANCEL_URL', env('APP_URL').'/billing/cancel'),
        'portal_return_url' => env('STRIPE_PORTAL_RETURN_URL', env('APP_URL').'/billing'),
    ],
];

