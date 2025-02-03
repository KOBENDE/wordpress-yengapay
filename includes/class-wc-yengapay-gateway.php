<?php

/**
 * YengaPay Mobile Payments Gateway.
 *
 * Provides a YengaPay Mobile Payments Payment Gateway.
 *
 * @class       WC_YengaPay_Gateway
 * @extends     WC_Payment_Gateway
 * @version     2.1.2
 * @package     WooCommerce/Classes/Payment
 */

class WC_YengaPay_Gateway extends WC_Payment_Gateway {
    public function __construct() {
        // Setup general properties.
        $this->setup_properties();

        // Generate Webhook URL
        $this->webhook_url = WC()->api_request_url($this->id);

        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        // Get settings.
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->groupeId = $this->get_option('groupeId');
        $this->apikey = $this->get_option('apikey');
        $this->projectId = $this->get_option('projectId');
        $this->webhook_secret = $this->get_option('webhook_secret');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_' . $this->id, array($this, 'webhook_handler'));
    }

    protected function setup_properties() {
        $this->id = 'yengapay';
        $this->icon = apply_filters('woocommerce_yengapay_icon', plugins_url('assets/yengapay_icon.png', dirname(__FILE__)));
        $this->method_title = __('YengaPay', 'yengapay');
        $this->method_description = __('Payment aggregator.', 'yengapay');
        $this->has_fields = false;
    }

     /**
	 * Initialise Gateway Settings Form Fields.
	 */
    public function init_form_fields() {
        $this->form_fields = apply_filters('woo_yengapay_fields', array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'yengapay'),
                'type' => 'checkbox',
                'label' => __('Enable or Disable YengaPay', 'yengapay'),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __('Title', 'yengapay'),
                'type' => 'title', 
                'default' => __('YengaPay', 'yengapay'),
                'desc_tip' => true,
                'description' => __('YengaPay', 'yengapay')
            ),
            'description' => array(
                'title' => __('Description', 'yengapay'),
                'type' => 'title',
                'default' => __('Payez via YengaPay, un agrégateur de paiement local.', 'yengapay'),
                'desc_tip' => true,
                'description' => __('Payez via YengaPay, un agrégateur de paiement local.', 'yengapay')
            ),
            'groupeId' => array(
                'title' => __('Groupe ID', 'yengapay'),
                'type' => 'text',
                'default' => __('0000000', 'yengapay'),
                'desc_tip' => true,
                'description' => __('Add your YengaPay Group ID.', 'yengapay')
            ),
            'apikey' => array(
                'title' => __('ApiKey', 'yengapay'),
                'type' => 'password',
                'default' => __('Ysjhdgjhgszllhdkgui', 'yengapay'),
                'desc_tip' => true,
                'description' => __('Add your ApiKey.', 'yengapay')
            ),
            'projectId' => array(
                'title' => __('Project ID', 'yengapay'),
                'type' => 'text',
                'default' => __('00000', 'yengapay'),
                'desc_tip' => true,
                'description' => __('Add your yenga pay project id', 'yengapay')
            ),
            'webhook_url' => array(
                'title' => __('URL du webhook', 'yengapay'),
                'type' => 'text',
                'default' => $this->webhook_url,
                'description' => __('Copiez cette URL dans votre dashboard YengaPay pour recevoir les notifications de paiement.', 'yengapay'),
                'custom_attributes' => array('readonly' => 'readonly'),
            ),
            'webhook_secret' => array(
                'title' => __('Webhook Secret', 'yengapay'),
                'type' => 'password',
                'description' => __('The webhook secret provided by YengaPay to verify notifications.', 'yengapay'),
                'default' => '',
                'desc_tip' => true,
            ),
        ));
    }

    /**
	 * Check If The Gateway Is Available For Use.
	 *
	 * @return bool
	 */
    public function is_available() {
        return true;
    }

	    /**
    * Process the payment and return the result.
    *
    * @param int $order_id Order ID.
    * @return array
    */
    public function process_payment($order_id) {

        $order = wc_get_order($order_id);
    
        // Get order currency
        $order_currency = $order->get_currency();
    
        try {
            // Format articles data based on order items
            $articles = array();
            foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            $item_total = floatval($item->get_total());
            
            // Convert item price to XOF required
            if ($order_currency !== 'XOF') {
                $item_total = YengaPay_Currency_Converter::convert_to_xof($item_total, $order_currency);
            }
            
            $articles[] = array(
                'title' => $item->get_name(),
                'description' => wp_strip_all_tags($product->get_description()),
                'pictures' => array(
                    wp_get_attachment_url($product->get_image_id())
                ),
                'price' => $item_total
            );
            }

            // Convert the total amount to XOF if necessary
            $total_amount = floatval($order->get_total());
            if ($order_currency !== 'XOF') {
            $total_amount = YengaPay_Currency_Converter::convert_to_xof($total_amount, $order_currency);
            
            // Add a note to the order for conversion
            $rate = YengaPay_Currency_Converter::get_conversion_rate($order_currency);
            $order->add_order_note(
                sprintf(
                    __('Montant converti de %s %s en %s XOF (taux: 1 %s = %s XOF)', 'yengapay'),
                    $order->get_total(),
                    $order_currency,
                    $total_amount,
                    $order_currency,
                    $rate
                )
            );
            }

            // Prepare payment data
            $payment_data = array(
            'paymentAmount' => $total_amount,
            'reference' => strval($order_id),
            'articles' => $articles
            );

            // Construct API URL with organization_id and project_id
            $api_url = sprintf(
            // Change with your YengaPay API URL
            'https://api.yengapay.com/api/v1/groups/%s/payment-intent/%s',
            $this->groupeId,
            $this->projectId
            );

            $args = array(
            'headers' => array(
                'x-api-key' => $this->apikey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ),
            'body' => json_encode($payment_data),
            'method' => 'POST',
            'timeout' => 60,
            'sslverify' => false,
            'blocking' => true,
            'httpversion' => '1.1',
            'redirection' => 5
            );

            $response = wp_remote_post($api_url, $args);

            if (is_wp_error($response)) {
                error_log('YengaPay Error: ' . print_r($response->get_error_message(), true));
                throw new Exception($response->get_error_message());
            }

            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);

            if ($response_code !== 200 && $response_code !== 201) {
                $error_message = wp_remote_retrieve_response_message($response);
                error_log('YengaPay Error Response: Code=' . $response_code . ', Message=' . $error_message);
                throw new Exception('Erreur API YengaPay (' . $response_code . '): ' . $error_message);
            }

            $body = json_decode($response_body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('YengaPay Response Body that failed to decode: ' . $response_body);
                throw new Exception('Erreur de décodage de la réponse: ' . json_last_error_msg());
            }

            if (!isset($body['checkoutPageUrlWithPaymentToken'])) {
                throw new Exception('URL de paiement non trouvée dans la réponse');
            }

            $payment_url = $body['checkoutPageUrlWithPaymentToken'];
            
            // Updating order status
            $order->update_status('pending', __('En attente du paiement YengaPay', 'yengapay'));
            
            // empty the cart
            WC()->cart->empty_cart();

            return array(
                'result' => 'success',
                'redirect' => $payment_url
            );

        } catch (Exception $e) {
            error_log('YengaPay Error: ' . $e->getMessage());
            wc_add_notice(__('Erreur de paiement: ', 'yengapay') . $e->getMessage(), 'error');
            return array('result' => 'fail');
        }
    }       

    /**
    * Process YengaPay webhook notifications
    */
    public function webhook_handler() {
        try {

            // Get only the required header
            $webhook_hash = $this->get_webhook_hash_header();
            
            // Get and log raw payload
            $payload = file_get_contents('php://input');
            
            if (empty($payload)) {
                throw new Exception(__('Empty payload received', 'yengapay'));
            }

            // Verify webhook secret configuration
            if (empty($this->webhook_secret)) {
                throw new Exception(__('Veuillez configurer le webhook secret dans les paramètres YengaPay', 'yengapay'));
            }

            // Verify hash presence
            if (empty($webhook_hash)) {
                throw new Exception(__('Missing X-Webhook-Hash header', 'yengapay'));
            }     
        
            // Verify hash validity
            $calculated_hash = hash_hmac('sha256', $payload, $this->webhook_secret);
            if (!hash_equals($calculated_hash, $webhook_hash)) {
                throw new Exception(__('Invalid webhook signature', 'yengapay'));
            }
        
    
            // Decode and validate JSON payload
            $data = json_decode($payload, true);
            

            if (!$data) {
                throw new Exception(__('Invalid JSON payload: ', 'yengapay') . json_last_error_msg());
            }
    
    
            // Verify required fields
            if (!isset($data['reference']) || !isset($data['paymentStatus'])) {
                throw new Exception('Données de webhook incomplètes - reference ou paymentStatus manquant');
            }
            
            // Get and verify order
            $order = wc_get_order($data['reference']);
            
            if (!$order) {
                throw new Exception('Commande non trouvée: ' . $data['reference']);
            }
            
            // Process different payment statuses
            switch ($data['paymentStatus']) {
                case 'DONE':
                    if ($order->get_status() === 'pending' || $order->get_status() === 'on-hold') {
                        $order->payment_complete();
                        $transaction_id = isset($data['id']) ? $data['id'] : 'N/A';
                        $order->add_order_note(
                            sprintf(
                                __('Paiement YengaPay confirmé. ID Transaction: %s', 'yengapay'),
                                $transaction_id
                            )
                        );
                        
                        if ($transaction_id !== 'N/A') {
                            $order->set_transaction_id($transaction_id);
                            $order->save();
                        }
                    }
                    break;
                    
                case 'FAILED':
                    $order->update_status(
                        'failed',
                        __('Paiement YengaPay échoué.', 'yengapay')
                    );
                    break;
                    
                case 'PENDING':
                    $order->update_status(
                        'on-hold',
                        __('Paiement YengaPay en attente de confirmation.', 'yengapay')
                    );
                    break;
                    
                case 'CANCELLED':
                    $order->update_status(
                        'cancelled',
                        __('Paiement YengaPay annulé par l\'utilisateur.', 'yengapay')
                    );
                    break;
                    
                default:
                    break;
            }
            wp_send_json_success('Webhook traité avec succès');
    
        } catch (Exception $e) {
            error_log('YengaPay Webhook Error: ' . $e->getMessage());
            wp_send_json_error($e->getMessage(), 400);
        }
    }

    /**
     * Helper method to safely get the webhook hash header
     * 
     * @return string|null
     */
    private function get_webhook_hash_header() {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            // Search for the header in a case-insensitive manner
            foreach ($headers as $header_name => $value) {
                if (strtolower($header_name) === 'x-webhook-hash') {
                    return $value;
                }
            }
        }
        
        // Fallback for servers that don't support getallheaders()
        $webhook_hash = isset($_SERVER['HTTP_X_WEBHOOK_HASH']) ? $_SERVER['HTTP_X_WEBHOOK_HASH'] : null;
        return $webhook_hash;
    }
}