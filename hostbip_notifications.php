<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly.");
}

function hostbip_notifications_config()
{
    return [
        'name' => 'Hostbip Notifications',
        'description' => 'Sends custom notifications to users using different SMS and WhatsApp gateways.',
        'version' => '1.4',
        'author' => 'Hostbip Limited',
        'fields' => [
            'api_provider' => [
                'FriendlyName' => 'API Provider',
                'Type' => 'dropdown',
                'Options' => [
                    'africastalking' => 'AfricasTalking',
                    'zender' => 'Zender SMS',
                    'zender_whatsapp' => 'Zender WhatsApp',
                    'aws_sns' => 'AWS SNS',
                    'twilio' => 'Twilio',
                    'termii' => 'Termii',
                    'custom_http' => 'Custom HTTP'
                ],
                'Description' => 'Select the gateway to send notifications (SMS or WhatsApp).'
            ],
            'api_key' => [
                'FriendlyName' => 'API Key',
                'Type' => 'text',
                'Size' => '32',
                'Description' => 'Enter the API key for the selected provider.'
            ],
            'api_secret' => [
                'FriendlyName' => 'API Secret',
                'Type' => 'text',
                'Size' => '32',
                'Description' => 'Enter the API secret for the selected provider.'
            ],

            'sender_id' => [
                'FriendlyName' => 'Sender ID',
                'Type' => 'text',
                'Size' => '20',
                'Description' => 'The name or number that appears as the sender, depending on the SMS provider.'
            ],
            'zender_api_url' => [
                'FriendlyName' => 'Zender API URL',
                'Type' => 'text',
                'Size' => '50',
                'Description' => 'Enter the Zender API URL for sending SMS or WhatsApp messages.'
            ],
            'zender_mode' => [
                'FriendlyName' => 'Zender Mode',
                'Type' => 'dropdown',
                'Options' => [
                    'devices' => 'Devices Mode',
                    'credits' => 'Credits Mode'
                ],
                'Description' => 'Select the Zender mode (devices or credits).'
            ],
            'zender_device' => [
                'FriendlyName' => 'Zender Device ID',
                'Type' => 'text',
                'Size' => '10',
                'Description' => 'Enter the Zender device ID to be used (for devices mode).'
            ],
            'zender_sim' => [
                'FriendlyName' => 'Zender SIM ID',
                'Type' => 'text',
                'Size' => '10',
                'Description' => 'Enter the Zender SIM ID to be used (for devices with multiple SIMs).'
            ],
            'custom_http_url' => [
                'FriendlyName' => 'Custom HTTP SMS Gateway URL',
                'Type' => 'text',
                'Size' => '50',
                'Description' => 'Enter the URL for the custom HTTP SMS gateway.'
            ],
            'custom_http_parameters' => [
                'FriendlyName' => 'Custom HTTP Parameters',
                'Type' => 'textarea',
                'Rows' => '5',
                'Description' => 'Define additional parameters for the custom HTTP gateway in key=value format.'
            ],
        ]
    ];
}

function hostbip_notifications_activate()
{
    return ['status' => 'success', 'description' => 'Module activated successfully.'];
}

function hostbip_notifications_deactivate()
{
    return ['status' => 'success', 'description' => 'Module deactivated successfully.'];
}

function hostbip_notifications_output($vars)
{
    echo '<p>Hostbip Notifications Module: This module enables sending notifications through various SMS and WhatsApp gateways.</p>';
}

function hostbip_notifications_upgrade($vars)
{
    // Optional: Handle upgrades here, if necessary
}
