
# Hostbip Notifications

Hostbip Notifications is a WHMCS addon module that allows you to send custom notifications via various SMS and WhatsApp gateways. Supported providers include AfricasTalking, Zender, AWS SNS, Twilio, Termii, and a Custom HTTP gateway.

## Features
- Send notifications via popular SMS and WhatsApp gateways.
- Configure custom messages for events such as client signup, invoice creation, service activation, etc.
- Hooks for key WHMCS events like domain registration and invoice payments.
- Custom placeholders for dynamic message content.
- Extensive logging for both activity and module-level API interactions.

## Installation

### Step 1: Upload Files
Upload the Hostbip Notifications module to the `/modules/addons/hostbip_notifications` directory in your WHMCS installation.

### Step 2: Activate the Module
1. Log in to the WHMCS Admin area.
2. Navigate to **Setup > Addon Modules**.
3. Find **Hostbip Notifications** and click **Activate**.
4. Click **Configure** to set the module options and API settings.

### Step 3: Configure API Providers
The following gateways are supported:
- **AfricasTalking**
- **Zender SMS**
- **Zender WhatsApp**
- **AWS SNS**
- **Twilio**
- **Termii**
- **Custom HTTP Gateway**

For each provider, configure the required fields:
- **API Key**
- **API Secret** (if applicable)
- **Sender ID** (custom name or number)

For **Zender**, you also need to configure:
- **API URL**
- **Mode (devices or credits)**
- **Device ID**
- **SIM ID**

For the **Custom HTTP Gateway**, define parameters in key-value format such as:
```
api_key=YOUR_API_KEY
secret=YOUR_SECRET
sender=YOUR_SENDER_ID
recipient={phonenumber}
message={message}
```

### Step 4: Customize Notification Messages
You can define custom messages for events such as:
- **Client Signup**
- **Invoice Creation**
- **Invoice Paid**
- **Service Activation**
- **Client Login**
- **Domain Registration**
- **Domain Expiry**

These messages support placeholders, including:
- `{firstname}`, `{lastname}`, `{domain}`, `{invoiceid}`, `{invoicedate}`, `{amount}`, `{registrationdate}`, `{expirydate}`

## Notification Hooks
The following hooks are implemented and will trigger notifications when appropriate:
- **Client Signup (ClientAdd)**
- **Invoice Creation (InvoiceCreationPreEmail)**
- **Invoice Paid (InvoicePaid)**
- **Service Activation (AfterModuleCreate)**
- **Client Login (ClientLogin)**
- **Domain Registration (AfterRegistrarRegistration)**
- **Domain Expiry Notice (DomainExpiryNotice)**
- **Domain Expired (AfterRegistrarExpired)**

## Logging
- **Activity Log**: General activity, such as sending messages or receiving responses, is logged under **Utilities > Logs > Activity Log**.
- **Module Log**: Detailed API request and response data is logged using `logModuleCall`. View these logs in **Utilities > Logs > Module Log**.

## Error Handling
- API errors (e.g., invalid credentials or network issues) are logged in the Module Log.
- Ensure that API credentials (API key, secret, sender ID) are correctly configured for each provider.

## Recommendations for Improvement
1. **Retry Mechanism**: Implement a retry mechanism for failed notifications.
2. **Additional Placeholders**: Add more placeholders for events like service renewals and account balances.
3. **Enhanced Logging**: Add more detailed error messages and HTTP response codes to logs.
4. **Rate Limiting**: Implement rate-limiting logic for gateways with rate limits (e.g., Twilio, AWS SNS).
5. **Multi-language Support**: Add support for multiple languages in custom notifications.
6. **Support for More Gateways**: Expand support for additional SMS gateways such as Nexmo (Vonage) and MessageBird.

## Testing the Module
### Step 1: Configure Settings
- Set up the API credentials and custom messages in **Addon Modules > Hostbip Notifications**.

### Step 2: Test Notifications
- Perform test actions such as client signup, invoice creation, and domain registration to verify that notifications are sent as expected.
- Check both **Activity Log** and **Module Log** for potential issues.

## Contact Information
For support or inquiries, please contact **Hostbip Limited**.

---

### License
This project is licensed under the MIT License.
