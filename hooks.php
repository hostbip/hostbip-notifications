<?php
use WHMCS\Database\Capsule;

/**
 * Helper function to send notifications based on the selected API provider.
 */
function send_notification($message, $phone_number)
{
    // Get the selected API provider from the module configuration
    $apiProvider = \WHMCS\Config\Setting::getValue('api_provider');
    logActivity("Hostbip Notification: Preparing to send message via {$apiProvider} to {$phone_number}");

    // Prepare the common headers and URL
    $url = '';
    $headers = [];
    $data = [];

    // Set data and headers based on the selected API provider
    switch ($apiProvider) {
        case 'africastalking':
            $apiKey = \WHMCS\Config\Setting::getValue('africastalking_api_key');
            $username = \WHMCS\Config\Setting::getValue('africastalking_username');
            $data = [
                'username' => $username, // Sender username
                'to' => $phone_number, // Recipient phone number in E.164 format
                'message' => $message // Message to send
            ];
            $url = "https://api.africastalking.com/version1/messaging";
            $headers = [
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Basic " . base64_encode("{$apiKey}:")
            ];
            break;

        case 'zender_sms':
            $apiKey = \WHMCS\Config\Setting::getValue('zender_sms_api_key');
            $data = [
                'secret' => $apiKey, // API Secret
                'mode' => \WHMCS\Config\Setting::getValue('zender_sms_mode'), // devices or credits
                'phone' => \WHMCS\Config\Setting::getValue('zender_sms_phone'), // Recipient phone number
                'message' => $message, // Message to send
                'priority' => \WHMCS\Config\Setting::getValue('zender_sms_priority') // Priority
            ];

            // Additional parameters based on mode
            if ($data['mode'] === 'devices') {
                $data['device'] = \WHMCS\Config\Setting::getValue('zender_sms_device'); // Device ID for devices mode
            } else {
                $data['gateway'] = \WHMCS\Config\Setting::getValue('zender_sms_gateway'); // Gateway ID for credits mode
            }

            // Get URL from module configuration
            $url = \WHMCS\Config\Setting::getValue('zender_sms_url'); // URL for Zender SMS
            $headers = [
                "Content-Type: application/json"
            ];
            break;

        case 'zender_whatsapp':
            $apiKey = \WHMCS\Config\Setting::getValue('zender_whatsapp_api_key');
            $data = [
                'secret' => $apiKey, // API Secret
                'account' => \WHMCS\Config\Setting::getValue('zender_whatsapp_account'), // WhatsApp account ID
                'recipient' => \WHMCS\Config\Setting::getValue('zender_whatsapp_recipient'), // Recipient phone number
                'type' => \WHMCS\Config\Setting::getValue('zender_whatsapp_type') ?: 'text', // Default to 'text'
                'message' => $message, // Message to send
                'priority' => \WHMCS\Config\Setting::getValue('zender_whatsapp_priority') // Priority
            ];

            // Get URL from module configuration
            $url = \WHMCS\Config\Setting::getValue('zender_whatsapp_url'); // URL for Zender WhatsApp
            $headers = [
                "Content-Type: application/json"
            ];
            break;

        case 'aws_sns':
            $region = \WHMCS\Config\Setting::getValue('aws_region'); // AWS Region
            $data = [
                'Message' => $message, // Message body
                'PhoneNumber' => $phone_number // Recipient phone number
            ];
            $url = "https://sns.{$region}.amazonaws.com"; // AWS SNS endpoint for the specified region
            $headers = [
                "Content-Type: application/json"
            ];
            break;

        case 'twilio':
            $apiKey = \WHMCS\Config\Setting::getValue('twilio_account_sid');
            $apiSecret = \WHMCS\Config\Setting::getValue('twilio_auth_token');
            $data = [
                'From' => \WHMCS\Config\Setting::getValue('twilio_from'), // Twilio sender ID
                'To' => $phone_number, // Recipient phone number
                'Body' => $message // Message body
            ];
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$apiKey}/Messages.json";
            $headers = [
                "Content-Type: application/x-www-form-urlencoded"
            ];
            break;

        case 'termii':
            $apiKey = \WHMCS\Config\Setting::getValue('termii_api_key');
            $data = [
                'api_key' => $apiKey, // Termii API Key
                'to' => $phone_number, // Recipient phone number
                'from' => \WHMCS\Config\Setting::getValue('termii_from'), // Sender ID
                'sms' => $message, // Message body
                'type' => \WHMCS\Config\Setting::getValue('termii_type') ?: 'plain', // Default to 'plain'
                'channel' => \WHMCS\Config\Setting::getValue('termii_channel') // Channel for sending the message
            ];
            $url = "https://api.termii.com/api/sms/send"; // Example URL for Termii
            $headers = [
                "Content-Type: application/json"
            ];
            break;

        case 'nexmo':
            $apiKey = \WHMCS\Config\Setting::getValue('nexmo_api_key');
            $apiSecret = \WHMCS\Config\Setting::getValue('nexmo_api_secret');
            $data = [
                'api_key' => $apiKey, // Nexmo API Key
                'api_secret' => $apiSecret, // Nexmo API Secret
                'to' => $phone_number, // Recipient phone number
                'text' => $message // Message body
            ];
            $url = "https://rest.nexmo.com/v1/messages"; // Example URL for Nexmo
            $headers = [
                "Content-Type: application/json"
            ];
            break;

        case 'messagebird':
            $apiKey = \WHMCS\Config\Setting::getValue('messagebird_api_key');
            $data = [
                'recipients' => $phone_number, // Recipient phone number
                'originator' => \WHMCS\Config\Setting::getValue('messagebird_originator'), // MessageBird sender ID
                'body' => $message // Message body
            ];
            $url = "https://rest.messagebird.com/messages"; // Example URL for MessageBird
            $headers = [
                "Authorization: AccessKey {$apiKey}",
                "Content-Type: application/json"
            ];
            break;

        case 'custom_http':
            $customUrl = \WHMCS\Config\Setting::getValue('custom_http_url');
            $data = [
                'message' => $message,
                'phone' => $phone_number
                // Add more placeholders as needed
            ];
            $url = $customUrl; // Use the custom HTTP endpoint
            $headers = [
                "Content-Type: application/json"
            ];
            break;

        default:
            logActivity("Hostbip Notification: Unknown API provider.");
            return;
    }

    // Implementing the HTTP request
    $response = send_http_request($url, $data, $headers);

    // Log the module call
    logModuleCall('Hostbip Notifications', 'Send Notification', $data, $response['response'], $response['http_code']);
    logActivity("Hostbip Notification: Response received: " . print_r($response, true));
}

/**
 * Function to send HTTP requests.
 */
function send_http_request($url, $data, $headers)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send as JSON
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'response' => $response,
        'http_code' => $httpCode
    ];
}

/**
 * Hook for ClientAdd event
 */
add_hook('ClientAdd', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_client_signup')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_client_signup');
        $message = str_replace(['{firstname}', '{lastname}', '{email}'], [$client->firstname, $client->lastname, $client->email], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Client Signup Hook is disabled.");
    }
});

/**
 * Hook for InvoiceCreationPreEmail event
 */
add_hook('InvoiceCreationPreEmail', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_invoice_creation')) {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
        $client = Capsule::table('tblclients')->where('id', $invoice->userid)->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_invoice_creation');
        $message = str_replace(['{invoiceid}', '{amount}', '{invoicedate}'], [$invoice->id, $invoice->total, $invoice->date], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Invoice Creation Hook is disabled.");
    }
});

/**
 * Hook for InvoicePaid event
 */
add_hook('InvoicePaid', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_invoice_paid')) {
        $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
        $client = Capsule::table('tblclients')->where('id', $invoice->userid)->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_invoice_paid');
        $message = str_replace(['{invoiceid}', '{amount}', '{invoicedate}'], [$invoice->id, $invoice->total, $invoice->date], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Invoice Paid Hook is disabled.");
    }
});

/**
 * Hook for UserLogin event
 */
add_hook('UserLogin', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_user_login')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_user_login');
        $message = str_replace(['{firstname}', '{lastname}'], [$client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: User Login Hook is disabled.");
    }
});

/**
 * Hook for AfterModuleCreate event
 */
add_hook('AfterModuleCreate', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_after_module_create')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_after_module_create');
        $message = str_replace(['{domain}', '{firstname}', '{lastname}'], [$vars['domain'], $client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: After Module Create Hook is disabled.");
    }
});

/**
 * Hook for AfterRegistrarRegistration event
 */
add_hook('AfterRegistrarRegistration', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_after_registrar_registration')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_after_registrar_registration');
        $message = str_replace(['{domain}', '{firstname}', '{lastname}'], [$vars['domain'], $client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: After Registrar Registration Hook is disabled.");
    }
});

/**
 * Hook for DomainExpiryNotice event
 */
add_hook('DomainExpiryNotice', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_domain_expiry_notice')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_domain_expiry_notice');
        $message = str_replace(['{domain}', '{expirydate}', '{firstname}', '{lastname}'], [$vars['domain'], $vars['expirydate'], $client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Domain Expiry Notice Hook is disabled.");
    }
});

/**
 * Hook for AfterRegistrarExpired event
 */
add_hook('AfterRegistrarExpired', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_after_registrar_expired')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_after_registrar_expired');
        $message = str_replace(['{domain}', '{firstname}', '{lastname}'], [$vars['domain'], $client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: After Registrar Expired Hook is disabled.");
    }
});

/**
 * Hook for ServiceDelete event
 */
add_hook('ServiceDelete', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_service_delete')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_service_delete');
        $message = str_replace(['{service}', '{firstname}', '{lastname}'], [$vars['serviceid'], $client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Service Delete Hook is disabled.");
    }
});

/**
 * Hook for UserLogout event
 */
add_hook('UserLogout', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_user_logout')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_user_logout');
        $message = str_replace(['{firstname}', '{lastname}'], [$client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: User Logout Hook is disabled.");
    }
});

/**
 * Hook for ClientChangePassword event
 */
add_hook('ClientChangePassword', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_client_change_password')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_client_change_password');
        $message = str_replace(['{firstname}', '{lastname}'], [$client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Client Change Password Hook is disabled.");
    }
});

/**
 * Hook for UserAdd event
 */
add_hook('UserAdd', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_user_add')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_user_add');
        $message = str_replace(['{firstname}', '{lastname}'], [$client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: User Add Hook is disabled.");
    }
});

/**
 * Hook for ServiceRenewal event
 */
add_hook('ServiceRenewal', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_service_renewal')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_service_renewal');
        $message = str_replace(['{service}', '{firstname}', '{lastname}'], [$vars['serviceid'], $client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Service Renewal Hook is disabled.");
    }
});

/**
 * Hook for AccountBalance event
 */
add_hook('AccountBalance', 1, function ($vars) {
    if (\WHMCS\Config\Setting::getValue('enable_account_balance')) {
        $client = Capsule::table('tblclients')->where('id', $vars['userid'])->first();
        $messageTemplate = \WHMCS\Config\Setting::getValue('custom_message_account_balance');
        $message = str_replace(['{balance}', '{firstname}', '{lastname}'], [$vars['balance'], $client->firstname, $client->lastname], $messageTemplate);
        send_notification($message, $client->phonenumber);
    } else {
        logActivity("Hostbip Notification: Account Balance Hook is disabled.");
    }
});

/**
 * Hook to display a form in the client summary page for sending notifications.
 */
add_hook('ClientSummaryPage', 1, function ($vars) {
    $clientId = $vars['userid'];

    // Check if the notification feature is enabled
    if (!\WHMCS\Config\Setting::getValue('enable_admin_send_user_message')) {
        return; // If not enabled, do not display the form
    }

    // Display the form
    echo '
    <form action="" method="post">
        <input type="hidden" name="userid" value="' . htmlspecialchars($clientId) . '">
        <label for="message">Message:</label>
        <textarea name="message" rows="4" cols="50" required></textarea>
        <input type="submit" value="Send Notification">
    </form>
    ';
});

// Processing the notification submission
add_hook('AdminAreaPage', 1, function ($vars) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userid']) && isset($_POST['message'])) {
        $clientId = $_POST['userid'];
        $message = $_POST['message'];

        $client = Capsule::table('tblclients')->where('id', $clientId)->first();
        if ($client) {
            send_notification($message, $client->phonenumber);
            logActivity("Hostbip Notification: Admin sent a message to {$client->firstname} {$client->lastname} ({$client->phonenumber}).");
            echo '<div class="alert alert-success">Notification sent successfully!</div>';
        } else {
            logActivity("Hostbip Notification: Client not found.");
            echo '<div class="alert alert-danger">Client not found!</div>';
        }
    }
});
