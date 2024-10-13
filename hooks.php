<?php

use WHMCS\Database\Capsule;

/**
 * Helper function to send notification using the selected API provider.
 */
function send_notification($message, $phone_number)
{
    $apiProvider = \WHMCS\Config\Setting::getValue('api_provider');
    logActivity("Hostbip Notification: Sending message via {$apiProvider} to {$phone_number}");

    $apiKey = \WHMCS\Config\Setting::getValue('api_key');
    $apiSecret = \WHMCS\Config\Setting::getValue('api_secret');
    $senderId = \WHMCS\Config\Setting::getValue('sender_id');

    switch ($apiProvider) {
        case 'africastalking':
            send_via_africastalking($message, $phone_number, $apiKey, $apiSecret, $senderId);
            break;
        case 'zender':
            send_via_zender($message, $phone_number);
            break;
        case 'zender_whatsapp':
            send_via_zender_whatsapp($message, $phone_number);
            break;
        case 'aws_sns':
            send_via_aws_sns($message, $phone_number, $apiKey, $apiSecret);
            break;
        case 'twilio':
            send_via_twilio($message, $phone_number, $apiKey, $apiSecret, $senderId);
            break;
        case 'termii':
            send_via_termii($message, $phone_number, $apiKey, $senderId);
            break;
        case 'custom_http':
            send_via_custom_http($message, $phone_number, $apiKey, $apiSecret, $senderId);
            break;
        default:
            logActivity("Hostbip Notification: Unknown API provider selected.");
            break;
    }
    logActivity("Hostbip Notification: Message sent successfully to {$phone_number} via {$apiProvider}");
}


/**
 * Send SMS via AfricasTalking API
 */
function send_via_africastalking($message, $phone_number, $apiKey, $apiSecret, $senderId)
{
    $url = "https://api.africastalking.com/version1/messaging";
    $data = [
        'username' => $apiKey,
        'to' => $phone_number,
        'message' => $message,
        'from' => $senderId,
    ];

    logActivity("Hostbip Notification: Sending SMS via AfricasTalking to {$phone_number}");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    logModuleCall(
        'HostbipNotifications',
        'AfricasTalking SMS',
        $data,
        $response,
        json_decode($response, true),
        []
    );

    logActivity("Hostbip Notification: AfricasTalking response: " . $response);
}

/**
 * Send SMS via Zender API
 */
function send_via_zender($message, $phone_number)
{
    $zenderApiUrl = \WHMCS\Config\Setting::getValue('zender_api_url');
    $secret = \WHMCS\Config\Setting::getValue('api_secret');
    $mode = \WHMCS\Config\Setting::getValue('zender_mode');
    $device = \WHMCS\Config\Setting::getValue('zender_device');
    $sim = \WHMCS\Config\Setting::getValue('zender_sim');
    $priority = '1';

    $data = [
        'secret' => $secret,
        'mode' => $mode,
        'device' => $device,
        'sim' => $sim,
        'priority' => $priority,
        'phone' => $phone_number,
        'message' => $message
    ];

    logActivity("Hostbip Notification: Sending SMS via Zender to {$phone_number}");

    $ch = curl_init($zenderApiUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    logModuleCall(
        'HostbipNotifications',
        'Zender SMS',
        $data,
        $response,
        json_decode($response, true),
        []
    );

    logActivity("Hostbip Notification: Zender response: " . $response);
}


/**
 * Send WhatsApp message via Zender API
 */
function send_via_zender_whatsapp($message, $phone_number)
{
    $zenderApiUrl = \WHMCS\Config\Setting::getValue('zender_api_url');
    $secret = \WHMCS\Config\Setting::getValue('api_secret');
    $account = \WHMCS\Config\Setting::getValue('api_key');

    $data = [
        'secret' => $secret,
        'account' => $account,
        'recipient' => $phone_number,
        'type' => 'text',
        'message' => $message
    ];

    logActivity("Hostbip Notification: Sending WhatsApp message via Zender to {$phone_number}");

    $ch = curl_init($zenderApiUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    logModuleCall(
        'HostbipNotifications',
        'Zender WhatsApp',
        $data,
        $response,
        json_decode($response, true),
        []
    );

    logActivity("Hostbip Notification: Zender WhatsApp response: " . $response);
}

/**
 * Send SMS via AWS SNS
 */
function send_via_aws_sns($message, $phone_number, $apiKey, $apiSecret)
{
    $sns = new \Aws\Sns\SnsClient([
        'version' => 'latest',
        'region' => 'us-east-1',
        'credentials' => [
            'key' => $apiKey,
            'secret' => $apiSecret
        ]
    ]);

    logActivity("Hostbip Notification: Sending SMS via AWS SNS to {$phone_number}");

    $result = $sns->publish([
        'Message' => $message,
        'PhoneNumber' => $phone_number,
    ]);

    logModuleCall(
        'HostbipNotifications',
        'AWS SNS',
        ['Message' => $message, 'PhoneNumber' => $phone_number],
        json_encode($result),
        $result,
        []
    );

    logActivity("Hostbip Notification: AWS SNS response: " . print_r($result, true));
}


/**
 * Send SMS via Twilio API
 */
function send_via_twilio($message, $phone_number, $apiKey, $apiSecret, $senderId)
{
    $url = "https://api.twilio.com/2010-04-01/Accounts/$apiKey/Messages.json";
    $data = [
        'From' => $senderId,
        'To' => $phone_number,
        'Body' => $message,
    ];

    logActivity("Hostbip Notification: Sending SMS via Twilio to {$phone_number}");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    logModuleCall(
        'HostbipNotifications',
        'Twilio SMS',
        $data,
        $response,
        json_decode($response, true),
        []
    );

    logActivity("Hostbip Notification: Twilio response: " . $response);
}

/**
 * Send SMS via Termii API
 */
function send_via_termii($message, $phone_number, $apiKey, $senderId)
{
    $url = "https://api.ng.termii.com/api/sms/send";
    $data = [
        'to' => $phone_number,
        'from' => $senderId,
        'sms' => $message,
        'api_key' => $apiKey,
        'type' => 'plain',
        'channel' => 'generic'
    ];

    logActivity("Hostbip Notification: Sending SMS via Termii to {$phone_number}");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    logModuleCall(
        'HostbipNotifications',
        'Termii SMS',
        $data,
        $response,
        json_decode($response, true),
        []
    );

    logActivity("Hostbip Notification: Termii response: " . $response);
}

 
/**
 * Send SMS via Custom HTTP Gateway
 */
function send_via_custom_http($message, $phone_number, $apiKey, $apiSecret, $senderId)
{
    $customHttpUrl = \WHMCS\Config\Setting::getValue('custom_http_url');
    $params = \WHMCS\Config\Setting::getValue('custom_http_parameters');
    $params = str_replace(
        ['{phonenumber}', '{message}'], 
        [$phone_number, urlencode($message)], 
        $params
    );

    logActivity("Hostbip Notification: Sending SMS via Custom HTTP to {$phone_number}");

    $ch = curl_init($customHttpUrl . '?' . $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    logModuleCall(
        'HostbipNotifications',
        'Custom HTTP SMS',
        $params,
        $response,
        json_decode($response, true),
        []
    );

    logActivity("Hostbip Notification: Custom HTTP response: " . $response);
}

// Add hooks for various WHMCS events

add_hook('ClientAdd', 1, function($vars) {
    $clientName = $vars['firstname'] . ' ' . $vars['lastname'];
    $phoneNumber = $vars['phonenumber'];
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_client_signup');
    $message = process_template($customMessage, ['firstname' => $vars['firstname'], 'lastname' => $vars['lastname']]);
    send_notification($message, $phoneNumber);
});

add_hook('InvoiceCreationPreEmail', 1, function($vars) {
    $invoiceId = $vars['invoiceid'];
    $clientId = $vars['userid'];
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $invoiceDate = Capsule::table('tblinvoices')->where('id', $invoiceId)->value('date');
    $amount = Capsule::table('tblinvoices')->where('id', $invoiceId)->value('total');
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_invoice_creation');
    $message = process_template($customMessage, [
        'invoiceid' => $invoiceId,
        'invoicedate' => $invoiceDate,
        'amount' => $amount
    ]);
    send_notification($message, $client->phonenumber);
});

add_hook('InvoicePaid', 1, function($vars) {
    $invoiceId = $vars['invoiceid'];
    $clientId = $vars['userid'];
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $amount = Capsule::table('tblinvoices')->where('id', $invoiceId)->value('total');
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_invoice_paid');
    $message = process_template($customMessage, [
        'invoiceid' => $invoiceId,
        'amount' => $amount
    ]);
    send_notification($message, $client->phonenumber);
});

add_hook('AfterModuleCreate', 1, function($vars) {
    $serviceId = $vars['params']['serviceid'];
    $clientId = $vars['params']['clientsdetails']['userid'];
    $domain = $vars['params']['domain'];
    $registrationDate = Capsule::table('tblhosting')->where('id', $serviceId)->value('regdate');
    $nextDueDate = Capsule::table('tblhosting')->where('id', $serviceId)->value('nextduedate');
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_service_activation');
    $message = process_template($customMessage, [
        'domain' => $domain,
        'registrationdate' => $registrationDate,
        'nextduedate' => $nextDueDate
    ]);
    send_notification($message, $client->phonenumber);
});

add_hook('ClientLogin', 1, function($vars) {
    $clientId = $vars['userid'];
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_client_login');
    $message = process_template($customMessage, ['firstname' => $client->firstname, 'lastname' => $client->lastname]);
    send_notification($message, $client->phonenumber);
});

add_hook('AfterRegistrarRegistration', 1, function($vars) {
    $domain = $vars['params']['sld'] . '.' . $vars['params']['tld'];
    $registrationDate = $vars['params']['regdate'];
    $expiryDate = $vars['params']['expirydate'];
    $clientId = $vars['params']['userid'];
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_domain_registration');
    $message = process_template($customMessage, [
        'domain' => $domain,
        'registrationdate' => $registrationDate,
        'expirydate' => $expiryDate
    ]);
    send_notification($message, $client->phonenumber);
});

add_hook('DomainExpiryNotice', 1, function($vars) {
    $domain = $vars['params']['sld'] . '.' . $vars['params']['tld'];
    $expiryDate = $vars['params']['expirydate'];
    $clientId = $vars['params']['userid'];
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_domain_expiring');
    $message = process_template($customMessage, [
        'domain' => $domain,
        'expirydate' => $expiryDate
    ]);
    send_notification($message, $client->phonenumber);
});

add_hook('AfterRegistrarExpired', 1, function($vars) {
    $domain = $vars['params']['sld'] . '.' . $vars['params']['tld'];
    $expiryDate = $vars['params']['expirydate'];
    $clientId = $vars['params']['userid'];
    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $customMessage = \WHMCS\Config\Setting::getValue('custom_message_domain_expired');
    $message = process_template($customMessage, [
        'domain' => $domain,
        'expirydate' => $expiryDate
    ]);
    send_notification($message, $client->phonenumber);
});

/**
 * Helper function to process a template with placeholders
 */
function process_template($template, $placeholders)
{
    foreach ($placeholders as $key => $value) {
        $template = str_replace("{" . $key . "}", $value, $template);
    }
    return $template;
}
?>
