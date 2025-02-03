<?php
/*
 * Plugin Name: YengaPay
 * Plugin URI: https://yengapay.com/
 * Author: Kreezus
 * Description: This plugin allows for local mobile money payment systems.
 * Version: 1.0.0
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: yengapay
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// WooCommerce Check
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'yengapay_payment_init', 11 );
add_filter( 'woocommerce_payment_gateways', 'yengapay_register_gateway' );
add_filter( 'woocommerce_currencies', 'yengapay_register_xof_currency' );
add_filter( 'woocommerce_currency_symbol', 'yengapay_register_xof_symbol', 10, 2 );

function yengapay_payment_init() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
        require_once plugin_dir_path( __FILE__ ) . '/includes/class-wc-yengapay-gateway.php';
        require_once plugin_dir_path( __FILE__ ) . '/includes/class-yengapay-currency-converter.php';
    }
}

function yengapay_register_gateway( $gateways ) {
    $gateways[] = 'WC_YengaPay_Gateway';
    return $gateways;
}

function yengapay_register_xof_currency( $currencies ) {
    $currencies['XOF'] = __( 'Fcfa', 'yengapay' );
    return $currencies;
}

function yengapay_register_xof_symbol( $currency_symbol, $currency ) {
    if ( $currency === 'XOF' ) {
        $currency_symbol = 'FCFA';
    }
    return $currency_symbol;
}