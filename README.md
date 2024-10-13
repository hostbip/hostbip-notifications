# Hostbip Notifications WHMCS Addon

## Overview
The Hostbip Notifications addon module allows sending SMS and WhatsApp notifications using multiple providers such as AfricasTalking, Zender, AWS SNS, Twilio, Termii, Nexmo (Vonage), MessageBird, and Custom HTTP.

## Installation
1. Upload the module directory to `/modules/addons/hostbip_notifications/`.
2. In WHMCS Admin, navigate to **Setup > Addon Modules**.
3. Find **Hostbip Notifications** and click **Activate**.
4. Configure the module by selecting the desired API provider and filling out the API keys and sender information.

## Configuration
1. Go to **Setup > Addon Modules > Hostbip Notifications > Configure**.
2. Select your API provider (e.g., AfricasTalking, Twilio, etc.).
3. Fill in the API key, secret, sender ID, and any additional required fields.
4. Set custom notification messages using placeholders like `{firstname}`, `{lastname}`, `{invoiceid}`, `{domain}`, etc.

## Logging
- All API requests and responses are logged for debugging and tracking purposes using **logActivity** and **logModuleCall**.
- To enable module logging, navigate to **Utilities > Logs > Module Log**.

## Hooks and Notifications
- Notifications are triggered for various WHMCS events such as:
  - **Client Signup**
  - **Invoice Creation**
  - **Invoice Paid**
  - **Service Activation**
  - **Domain Registration**
  - **Domain Expiring**
  - **Domain Expired**
  - **User Login**, **User Logout**, **Password Change**, and more.

## Upgrading
The module includes an `upgrade()` function to handle upgrades. If you upgrade from a previous version, the system will automatically update any necessary settings.

## Rate Limiting
For gateways that have rate limits (e.g., Twilio, AWS SNS), set the **Rate Limit** in the configuration page. This will prevent exceeding limits by controlling the number of requests per minute.

## Error Handling and Retries
The module has built-in error handling and retries failed notifications. Logs will include detailed error messages and HTTP response codes for failed attempts.

## Customization
You can customize notification messages for each hook (e.g., client signup, invoice creation) and use placeholders like:
- `{firstname}`, `{lastname}`, `{email}`
- `{domain}`, `{expirydate}`, `{registrationdate}`, `{invoiceid}`, `{amount}`

## Multi-language Support
Notification messages support multiple languages. You can add custom messages in different languages from the module configuration page.
