<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration - Baileys (FREE)
    |--------------------------------------------------------------------------
    |
    | Using Baileys - WhatsApp Web API (100% FREE)
    | - No API keys needed
    | - No monthly costs
    | - Unlimited messages
    | - Just scan QR code and go!
    |
    */

    'baileys_url' => env('WHATSAPP_BAILEYS_URL', 'http://localhost:3000'),

    /*
    |--------------------------------------------------------------------------
    | Message Settings
    |--------------------------------------------------------------------------
    */

    'max_message_length' => 4096, // WhatsApp limit

    'enable_notifications' => env('WHATSAPP_ENABLE_NOTIFICATIONS', true),
];