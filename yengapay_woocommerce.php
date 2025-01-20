<?php

/*
 * Plugin Name: YengaPay for Woocommerce
 * Plugin URI: https://yengapay.com/
 * Author Name: Kreezus
 * Author URI: https://yengapay.com/
 * Description: This plugin allows for local mobile money payment systems.
 * Version: 0.0.1
 * License: 0.1.0
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: yengapay-woo
 * 
 * Class WC_Gateway_YengaPat file.
 *
 * @package WooCommerce\YengaPat
 */


 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'yengapay_payment_init', 11 );
add_filter( 'woocommerce_payment_gateways', 'add_to_woo_yengapay_gateway');
add_filter( 'woocommerce_currencies', 'techiepress_add_XOF_currencies' );
add_filter( 'woocommerce_currency_symbol', 'techiepress_add_XOF_currencies_symbol', 10, 2 );
add_action( 'wp_footer', function() {
    if ( is_cart() || is_checkout() ) {
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        echo '<pre>'; print_r( $available_gateways ); echo '</pre>';
    }
});

function yengapay_payment_init() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-wc-yengapay-gateway.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-yengapay-currency-converter.php';
        error_log( 'YengaPay Gateway loaded' );
    }
}


function add_to_woo_yengapay_gateway( $gateways ) {
    $gateways[] = 'WC_YengaPay_Gateway';
    return $gateways;
}

function techiepress_add_XOF_currencies( $currencies ) {
	$currencies['XOF'] = __( 'Fcfa', 'yengapay-woo' );
	return $currencies;
}

function techiepress_add_XOF_currencies_symbol( $currency_symbol, $currency ) {
	switch ( $currency ) {
		case 'XOF': 
			$currency_symbol = 'FCFA'; 
		break;
	}
	return $currency_symbol;
}

