# WordPress (WooCommerce) YengaPay Plugin

A WordPress plugin that integrates YengaPay payment gateway with WooCommerce, allowing customers to pay using local mobile money payment systems.

## Features

- Easy integration with WooCommerce
- Support for mobile money payments
- Automatic currency conversion to XOF
- Real-time payment notification through webhooks
- Secure payment processing
- Customizable payment gateway title and description

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.2 or higher
- SSL certificate installed on your domain
- YengaPay merchant account

## Installation

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New
4. Click on "Upload Plugin"
5. Select the downloaded zip file
6. Click "Install Now"
7. After installation, click "Activate Plugin"

## Configuration

1. Go to WooCommerce > Settings > Payments
2. Find "YengaPay" in the payment methods list
3. Click "Manage"
4. Configure the following settings:
   - Enable/Disable: Toggle the payment method
   - Title: The name shown to customers during checkout
   - Description: Payment method description shown to customers
   - Groupe ID: Your YengaPay Group ID
   - ApiKey: Your YengaPay API key
   - Project ID: Your YengaPay project ID
   - Webhook Secret: The secret key provided by YengaPay for webhook validation
5. Copy the Webhook URL provided in the settings and paste it in your YengaPay dashboard
6. Click "Save changes"

## Testing

To test the plugin:

1. Make sure your YengaPay account is properly configured
2. Create a test product in WooCommerce
3. Proceed to checkout and select YengaPay as the payment method
4. Complete a test transaction using the payment interface

## Webhook Integration

The plugin automatically handles payment notifications through webhooks. When a payment is completed, the order status will be updated automatically. Make sure to:

1. Copy the webhook URL from the plugin settings
2. Add this URL to your YengaPay dashboard
3. Configure the webhook secret in both YengaPay dashboard and plugin settings

## Currency Support

- The plugin automatically handles conversion to XOF (CFA Franc)
- Supports display of FCFA currency symbol
- Conversion rates are handled automatically

## Development

If you want to modify the plugin or run it in a development environment:

1. Clone the repository
2. Navigate to the plugin directory
3. Use the provided docker-compose.yml to set up a development environment:
```bash
docker-compose up -d
```

This will set up:
- WordPress at http://localhost:8080
- MySQL database
- All necessary volumes for persistent data

## Troubleshooting

Common issues and solutions:

1. **Payment not processing**
   - Verify your API credentials
   - Check SSL certificate
   - Ensure webhook URL is properly configured

2. **Webhook not working**
   - Verify webhook secret
   - Check server firewall settings
   - Ensure URL is accessible

3. **Currency conversion issues**
   - Verify base currency settings in WooCommerce
   - Check if XOF currency is enabled

## Support

For support:
- Visit [YengaPay website](https://yengapay.com/)
- Contact YengaPay support team
- Check WordPress plugin documentation

## Credits

Developed by Kreezus for YengaPay