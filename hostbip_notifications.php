<?php

/**
 * WHMCS Hostbip Notifications Module
 *
 * @package Hostbip Notifications
 * @author Hostbip Limited
 * @version 1.0
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Register the module in WHMCS
function hostbip_notifications_config()
{
    return [
        'name' => 'Hostbip Notifications',
        'description' => 'Send notifications via SMS and WhatsApp using various gateways.',
        'version' => '1.0',
        'author' => 'Hostbip Limited',
        'fields' => [
            'api_provider' => [
                'FriendlyName' => 'Select API Provider',
                'Type' => 'dropdown',
                'Options' => [
                    'africastalking' => 'AfricasTalking',
                    'zender_sms' => 'Zender SMS',
                    'zender_whatsapp' => 'Zender WhatsApp',
                    'aws_sns' => 'AWS SNS',
                    'twilio' => 'Twilio',
                    'termii' => 'Termii',
                    'nexmo' => 'Nexmo',
                    'messagebird' => 'MessageBird',
                    'custom_http' => 'Custom HTTP',
                ],
                'Description' => 'Select the SMS or WhatsApp provider to use for notifications.',
            ],
            // API credentials for Africa's Talking
            'africastalking_api_key' => [
                'FriendlyName' => 'Africa\'s Talking API Key',
                'Type' => 'text',
                'Description' => 'Your Africa\'s Talking API Key.',
            ],
            'africastalking_username' => [
                'FriendlyName' => 'Africa\'s Talking Username',
                'Type' => 'text',
                'Description' => 'Your Africa\'s Talking Username.',
            ],
            // API credentials for Zender SMS
            'zender_sms_api_key' => [
                'FriendlyName' => 'Zender SMS API Key',
                'Type' => 'text',
                'Description' => 'Your Zender SMS API Secret.',
            ],
            'zender_sms_url' => [
                'FriendlyName' => 'Zender SMS API URL',
                'Type' => 'text',
                'Description' => 'The URL for sending SMS via Zender.',
            ],
            'zender_sms_mode' => [
                'FriendlyName' => 'Zender SMS Mode',
                'Type' => 'dropdown',
                'Options' => [
                    'devices' => 'Devices',
                    'credits' => 'Credits',
                ],
                'Description' => 'Select the mode for sending SMS.',
            ],
            'zender_sms_phone' => [
                'FriendlyName' => 'Zender SMS Phone',
                'Type' => 'text',
                'Description' => 'Recipient phone number.',
            ],
            'zender_sms_message' => [
                'FriendlyName' => 'Zender SMS Message',
                'Type' => 'textarea',
                'Description' => 'Message to send via Zender SMS.',
            ],
            'zender_sms_priority' => [
                'FriendlyName' => 'Zender SMS Priority',
                'Type' => 'dropdown',
                'Options' => [
                    '1' => 'Yes',
                    '2' => 'No',
                ],
                'Description' => 'Send message with priority.',
            ],
            // API credentials for Zender WhatsApp
            'zender_whatsapp_api_key' => [
                'FriendlyName' => 'Zender WhatsApp API Key',
                'Type' => 'text',
                'Description' => 'Your Zender WhatsApp API Secret.',
            ],
            'zender_whatsapp_url' => [
                'FriendlyName' => 'Zender WhatsApp API URL',
                'Type' => 'text',
                'Description' => 'The URL for sending messages via Zender WhatsApp.',
            ],
            'zender_whatsapp_account' => [
                'FriendlyName' => 'Zender WhatsApp Account',
                'Type' => 'text',
                'Description' => 'WhatsApp account ID.',
            ],
            'zender_whatsapp_type' => [
                'FriendlyName' => 'Zender WhatsApp Message Type',
                'Type' => 'dropdown',
                'Options' => [
                    'text' => 'Text',
                    'media' => 'Media',
                ],
                'Description' => 'Type of message to send via WhatsApp.',
            ],
            'zender_whatsapp_message' => [
                'FriendlyName' => 'Zender WhatsApp Message',
                'Type' => 'textarea',
                'Description' => 'Message to send via Zender WhatsApp.',
            ],
            'zender_whatsapp_priority' => [
                'FriendlyName' => 'Zender WhatsApp Priority',
                'Type' => 'dropdown',
                'Options' => [
                    '1' => 'Yes',
                    '2' => 'No',
                ],
                'Description' => 'Send WhatsApp message with priority.',
            ],
            // API credentials for AWS SNS
            'aws_region' => [
                'FriendlyName' => 'AWS Region',
                'Type' => 'text',
                'Description' => 'The AWS region for SNS.',
            ],
            // API credentials for Twilio
            'twilio_account_sid' => [
                'FriendlyName' => 'Twilio Account SID',
                'Type' => 'text',
                'Description' => 'Your Twilio Account SID.',
            ],
            'twilio_auth_token' => [
                'FriendlyName' => 'Twilio Auth Token',
                'Type' => 'text',
                'Description' => 'Your Twilio Auth Token.',
            ],
            'twilio_from' => [
                'FriendlyName' => 'Twilio From Number',
                'Type' => 'text',
                'Description' => 'The Twilio phone number to send messages from.',
            ],
            // API credentials for Termii
            'termii_api_key' => [
                'FriendlyName' => 'Termii API Key',
                'Type' => 'text',
                'Description' => 'Your Termii API Key.',
            ],
            'termii_from' => [
                'FriendlyName' => 'Termii From Number',
                'Type' => 'text',
                'Description' => 'Sender ID for Termii messages.',
            ],
            'termii_channel' => [
                'FriendlyName' => 'Termii Channel',
                'Type' => 'dropdown',
                'Options' => [
                    'dnd' => 'DND',
                    'whatsapp' => 'WhatsApp',
                    'generic' => 'Generic',
                ],
                'Description' => 'The channel through which to send the message.',
            ],
            // API credentials for Nexmo
            'nexmo_api_key' => [
                'FriendlyName' => 'Nexmo API Key',
                'Type' => 'text',
                'Description' => 'Your Nexmo API Key.',
            ],
            'nexmo_api_secret' => [
                'FriendlyName' => 'Nexmo API Secret',
                'Type' => 'text',
                'Description' => 'Your Nexmo API Secret.',
            ],
            // API credentials for MessageBird
            'messagebird_api_key' => [
                'FriendlyName' => 'MessageBird API Key',
                'Type' => 'text',
                'Description' => 'Your MessageBird API Key.',
            ],
            'messagebird_originator' => [
                'FriendlyName' => 'MessageBird Originator',
                'Type' => 'text',
                'Description' => 'Your MessageBird sender ID.',
            ],
            // API credentials for Custom HTTP
            'custom_http_url' => [
                'FriendlyName' => 'Custom HTTP URL',
                'Type' => 'text',
                'Description' => 'The URL for sending notifications via Custom HTTP.',
            ],
            // Enable/Disable Hooks
            'enable_admin_send_user_message' => [
                'FriendlyName' => 'Enable Admin Send User Message',
                'Type' => 'yesno',
                'Description' => 'Enable or disable the ability for admins to send messages directly to users.',
            ],
            'enable_client_signup' => [
                'FriendlyName' => 'Enable Client Signup Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for client signups.',
            ],
            'enable_invoice_creation' => [
                'FriendlyName' => 'Enable Invoice Creation Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for invoice creation.',
            ],
            'enable_invoice_paid' => [
                'FriendlyName' => 'Enable Invoice Paid Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for invoice payments.',
            ],
            'enable_user_login' => [
                'FriendlyName' => 'Enable User Login Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for user logins.',
            ],
            'enable_after_module_create' => [
                'FriendlyName' => 'Enable After Module Create Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications after module creation.',
            ],
            'enable_after_registrar_registration' => [
                'FriendlyName' => 'Enable After Registrar Registration Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications after domain registration.',
            ],
            'enable_domain_expiry_notice' => [
                'FriendlyName' => 'Enable Domain Expiry Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for domain expiry.',
            ],
            'enable_after_registrar_expired' => [
                'FriendlyName' => 'Enable After Registrar Expired Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications after domain expiry.',
            ],
            'enable_service_delete' => [
                'FriendlyName' => 'Enable Service Delete Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for service deletions.',
            ],
            'enable_user_logout' => [
                'FriendlyName' => 'Enable User Logout Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for user logouts.',
            ],
            'enable_client_change_password' => [
                'FriendlyName' => 'Enable Client Change Password Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for client password changes.',
            ],
            'enable_user_add' => [
                'FriendlyName' => 'Enable User Add Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for new users.',
            ],
            'enable_service_renewal' => [
                'FriendlyName' => 'Enable Service Renewal Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for service renewals.',
            ],
            'enable_account_balance' => [
                'FriendlyName' => 'Enable Account Balance Notifications',
                'Type' => 'yesno',
                'Description' => 'Enable or disable notifications for account balance updates.',
            ],
            // Message Templates
            'custom_message_client_signup' => [
                'FriendlyName' => 'Client Signup Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for client signup notifications.',
            ],
            'custom_message_invoice_creation' => [
                'FriendlyName' => 'Invoice Creation Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for invoice creation notifications.',
            ],
            'custom_message_invoice_paid' => [
                'FriendlyName' => 'Invoice Paid Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for invoice paid notifications.',
            ],
            'custom_message_user_login' => [
                'FriendlyName' => 'User Login Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for user login notifications.',
            ],
            'custom_message_after_module_create' => [
                'FriendlyName' => 'After Module Create Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for notifications after module creation.',
            ],
            'custom_message_after_registrar_registration' => [
                'FriendlyName' => 'After Registrar Registration Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for notifications after domain registration.',
            ],
            'custom_message_domain_expiry_notice' => [
                'FriendlyName' => 'Domain Expiry Notice Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for domain expiry notifications.',
            ],
            'custom_message_after_registrar_expired' => [
                'FriendlyName' => 'After Registrar Expired Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for notifications after domain expiry.',
            ],
            'custom_message_service_delete' => [
                'FriendlyName' => 'Service Delete Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for service deletion notifications.',
            ],
            'custom_message_user_logout' => [
                'FriendlyName' => 'User Logout Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for user logout notifications.',
            ],
            'custom_message_client_change_password' => [
                'FriendlyName' => 'Client Change Password Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for client change password notifications.',
            ],
            'custom_message_user_add' => [
                'FriendlyName' => 'User Add Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for new user notifications.',
            ],
            'custom_message_service_renewal' => [
                'FriendlyName' => 'Service Renewal Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for service renewal notifications.',
            ],
            'custom_message_account_balance' => [
                'FriendlyName' => 'Account Balance Message Template',
                'Type' => 'textarea',
                'Description' => 'Message template for account balance notifications.',
            ],
        ],
    ];
}

// Activation function for the module
function hostbip_notifications_activate()
{
    // Code to run when the module is activated
}

// Deactivation function for the module
function hostbip_notifications_deactivate()
{
    // Code to run when the module is deactivated
}

// Upgrade function to handle module upgrades
function hostbip_notifications_upgrade($vars)
{
    // Code to run when the module is upgraded
}

// Include hooks
include __DIR__ . '/hooks.php';
