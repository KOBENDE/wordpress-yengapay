=== YengaPay ===
Contributors: Kreezus
Tags: payment gateway, woocommerce, mobile money, yengapay, west africa
Tested up to: 6.7
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrate YengaPay payment gateway with WooCommerce for mobile money payments in West Africa.

== Description ==

YengaPay for WooCommerce allows your customers to pay using local mobile money payment systems. This plugin provides a seamless integration between WooCommerce and YengaPay's payment services.

= Key Features =

* Easy integration with WooCommerce
* Support for mobile money payments
* Automatic currency conversion to XOF
* Real-time payment notification through webhooks
* Secure payment processing
* Customizable payment gateway title and description

= Currency Support =

* Automatic conversion to XOF (CFA Franc)
* FCFA currency symbol display
* Automatic conversion rate handling

= Security =

* SSL certificate required
* Secure webhook integration
* Encrypted payment processing

== Requirements ==

* WordPress 5.0 or higher
* WooCommerce 3.0 or higher
* PHP 7.2 or higher
* SSL certificate installed
* YengaPay merchant account

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/yengapay-woocommerce` directory, or install the plugin through the WordPress plugins screen
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Settings > Payments
4. Find "YengaPay" in the payment methods list
5. Click "Manage" to configure the plugin

== Configuration ==

1. Enable/Disable: Toggle the payment method
2. Title: The name shown to customers during checkout
3. Description: Payment method description shown to customers
4. Group ID: Your YengaPay Group ID
5. ApiKey: Your YengaPay API key
6. Project ID: Your YengaPay project ID
7. Webhook Secret: The secret key for webhook validation

== Frequently Asked Questions ==

= Is SSL required? =

Yes, an SSL certificate is required for secure payment processing.

= Which currencies are supported? =

The plugin automatically converts payments to XOF (CFA Franc).

= How do webhooks work? =

The plugin automatically handles payment notifications through webhooks, updating order status in real-time.

== Screenshots ==

1. WooCommerce payment settings
2. YengaPay configuration panel
3. Checkout payment option
4. Payment confirmation page

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of YengaPay for WooCommerce

== Troubleshooting ==

= Payment not processing =
* Verify your API credentials
* Check SSL certificate
* Ensure webhook URL is properly configured

= Webhook not working =
* Verify webhook secret
* Check server firewall settings
* Ensure URL is accessible

= Currency conversion issues =
* Verify base currency settings in WooCommerce
* Check if XOF currency is enabled