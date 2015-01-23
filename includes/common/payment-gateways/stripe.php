<?php

/*
  MarketPress Stripe Gateway Plugin
  Author: Aaron Edwards, Marko Miljus
 */

class MP_Gateway_Stripe extends MP_Gateway_API {

	//build
	var $build					 = 2;
	//private gateway slug. Lowercase alpha (a-z) and dashes (-) only please!
	var $plugin_name				 = 'stripe';
	//name of your gateway, for the admin side.
	var $admin_name				 = '';
	//public name of your gateway, for lists and such.
	var $public_name				 = '';
	//url for an image for your checkout method. Displayed on checkout form if set
	var $method_img_url			 = '';
	//url for an submit button image for your checkout method. Displayed on checkout form if set
	var $method_button_img_url	 = '';
	//whether or not ssl is needed for checkout page
	var $force_ssl;
	//always contains the url to send payment notifications to if needed by your gateway. Populated by the parent class
	var $ipn_url;
	//whether if this is the only enabled gateway it can skip the payment_form step
	var $skip_form				 = false;
	//api vars
	var $publishable_key, $secret_key, $currency;

	/**
	 * Gateway currencies
	 *
	 * @since 3.0
	 * @access public
	 * @var array
	 */
	var $currencies = array(
		"AED"	 => 'AED: United Arab Emirates Dirham',
		"AFN"	 => 'AFN: Afghan Afghani*',
		"ALL"	 => 'ALL: Albanian Lek',
		"AMD"	 => 'AMD: Armenian Dram',
		"ANG"	 => 'ANG: Netherlands Antillean Gulden',
		"AOA"	 => 'AOA: Angolan Kwanza*',
		"ARS"	 => 'ARS: Argentine Peso*',
		"AUD"	 => 'AUD: Australian Dollar*',
		"AWG"	 => 'AWG: Aruban Florin',
		"AZN"	 => 'AZN: Azerbaijani Manat',
		"BAM"	 => 'BAM: Bosnia & Herzegovina Convertible Mark',
		"BBD"	 => 'BBD: Barbadian Dollar',
		"BDT"	 => 'BDT: Bangladeshi Taka',
		"BGN"	 => 'BGN: Bulgarian Lev',
		"BIF"	 => 'BIF: Burundian Franc',
		"BMD"	 => 'BMD: Bermudian Dollar',
		"BND"	 => 'BND: Brunei Dollar',
		"BOB"	 => 'BOB: Bolivian Boliviano*',
		"BRL"	 => 'BRL: Brazilian Real*',
		"BSD"	 => 'BSD: Bahamian Dollar',
		"BWP"	 => 'BWP: Botswana Pula',
		"BZD"	 => 'BZD: Belize Dollar',
		"CAD"	 => 'CAD: Canadian Dollar*',
		"CDF"	 => 'CDF: Congolese Franc',
		"CHF"	 => 'CHF: Swiss Franc',
		"CLP"	 => 'CLP: Chilean Peso*',
		"CNY"	 => 'CNY: Chinese Renminbi Yuan',
		"COP"	 => 'COP: Colombian Peso*',
		"CRC"	 => 'CRC: Costa Rican Colón*',
		"CVE"	 => 'CVE: Cape Verdean Escudo*',
		"CZK"	 => 'CZK: Czech Koruna*',
		"DJF"	 => 'DJF: Djiboutian Franc*',
		"DKK"	 => 'DKK: Danish Krone',
		"DOP"	 => 'DOP: Dominican Peso',
		"DZD"	 => 'DZD: Algerian Dinar',
		"EEK"	 => 'EEK: Estonian Kroon*',
		"EGP"	 => 'EGP: Egyptian Pound',
		"ETB"	 => 'ETB: Ethiopian Birr',
		"EUR"	 => 'EUR: Euro',
		"FJD"	 => 'FJD: Fijian Dollar',
		"FKP"	 => 'FKP: Falkland Islands Pound*',
		"GBP"	 => 'GBP: British Pound',
		"GEL"	 => 'GEL: Georgian Lari',
		"GIP"	 => 'GIP: Gibraltar Pound',
		"GMD"	 => 'GMD: Gambian Dalasi',
		"GNF"	 => 'GNF: Guinean Franc*',
		"GTQ"	 => 'GTQ: Guatemalan Quetzal*',
		"GYD"	 => 'GYD: Guyanese Dollar',
		"HKD"	 => 'HKD: Hong Kong Dollar',
		"HNL"	 => 'HNL: Honduran Lempira*',
		"HRK"	 => 'HRK: Croatian Kuna',
		"HTG"	 => 'HTG: Haitian Gourde',
		"HUF"	 => 'HUF: Hungarian Forint',
		"IDR"	 => 'IDR: Indonesian Rupiah',
		"ILS"	 => 'ILS: Israeli New Sheqel',
		"INR"	 => 'INR: Indian Rupee*',
		"ISK"	 => 'ISK: Icelandic Króna',
		"JMD"	 => 'JMD: Jamaican Dollar',
		"JPY"	 => 'JPY: Japanese Yen',
		"KES"	 => 'KES: Kenyan Shilling',
		"KGS"	 => 'KGS: Kyrgyzstani Som',
		"KHR"	 => 'KHR: Cambodian Riel',
		"KMF"	 => 'KMF: Comorian Franc',
		"KRW"	 => 'KRW: South Korean Won',
		"KYD"	 => 'KYD: Cayman Islands Dollar',
		"KZT"	 => 'KZT: Kazakhstani Tenge',
		"LAK"	 => 'LAK: Lao Kip*',
		"LBP"	 => 'LBP: Lebanese Pound',
		"LKR"	 => 'LKR: Sri Lankan Rupee',
		"LRD"	 => 'LRD: Liberian Dollar',
		"LSL"	 => 'LSL: Lesotho Loti',
		"LTL"	 => 'LTL: Lithuanian Litas',
		"LVL"	 => 'LVL: Latvian Lats',
		"MAD"	 => 'MAD: Moroccan Dirham',
		"MDL"	 => 'MDL: Moldovan Leu',
		"MGA"	 => 'MGA: Malagasy Ariary',
		"MKD"	 => 'MKD: Macedonian Denar',
		"MNT"	 => 'MNT: Mongolian Tögrög',
		"MOP"	 => 'MOP: Macanese Pataca',
		"MRO"	 => 'MRO: Mauritanian Ouguiya',
		"MUR"	 => 'MUR: Mauritian Rupee*',
		"MVR"	 => 'MVR: Maldivian Rufiyaa',
		"MWK"	 => 'MWK: Malawian Kwacha',
		"MXN"	 => 'MXN: Mexican Peso*',
		"MYR"	 => 'MYR: Malaysian Ringgit',
		"MZN"	 => 'MZN: Mozambican Metical',
		"NAD"	 => 'NAD: Namibian Dollar',
		"NGN"	 => 'NGN: Nigerian Naira',
		"NIO"	 => 'NIO: Nicaraguan Córdoba*',
		"NOK"	 => 'NOK: Norwegian Krone',
		"NPR"	 => 'NPR: Nepalese Rupee',
		"NZD"	 => 'NZD: New Zealand Dollar',
		"PAB"	 => 'PAB: Panamanian Balboa*',
		"PEN"	 => 'PEN: Peruvian Nuevo Sol*',
		"PGK"	 => 'PGK: Papua New Guinean Kina',
		"PHP"	 => 'PHP: Philippine Peso',
		"PKR"	 => 'PKR: Pakistani Rupee',
		"PLN"	 => 'PLN: Polish Złoty',
		"PYG"	 => 'PYG: Paraguayan Guaraní*',
		"QAR"	 => 'QAR: Qatari Riyal',
		"RON"	 => 'RON: Romanian Leu',
		"RSD"	 => 'RSD: Serbian Dinar',
		"RUB"	 => 'RUB: Russian Ruble',
		"RWF"	 => 'RWF: Rwandan Franc',
		"SAR"	 => 'SAR: Saudi Riyal',
		"SBD"	 => 'SBD: Solomon Islands Dollar',
		"SCR"	 => 'SCR: Seychellois Rupee',
		"SEK"	 => 'SEK: Swedish Krona',
		"SGD"	 => 'SGD: Singapore Dollar',
		"SHP"	 => 'SHP: Saint Helenian Pound*',
		"SLL"	 => 'SLL: Sierra Leonean Leone',
		"SOS"	 => 'SOS: Somali Shilling',
		"SRD"	 => 'SRD: Surinamese Dollar*',
		"STD"	 => 'STD: São Tomé and Príncipe Dobra',
		"SVC"	 => 'SVC: Salvadoran Colón*',
		"SZL"	 => 'SZL: Swazi Lilangeni',
		"THB"	 => 'THB: Thai Baht',
		"TJS"	 => 'TJS: Tajikistani Somoni',
		"TOP"	 => 'TOP: Tongan Paʻanga',
		"TRY"	 => 'TRY: Turkish Lira',
		"TTD"	 => 'TTD: Trinidad and Tobago Dollar',
		"TWD"	 => 'TWD: New Taiwan Dollar',
		"TZS"	 => 'TZS: Tanzanian Shilling',
		"UAH"	 => 'UAH: Ukrainian Hryvnia',
		"UGX"	 => 'UGX: Ugandan Shilling',
		"USD"	 => 'USD: United States Dollar',
		"UYI"	 => 'UYI: Uruguayan Peso*',
		"UZS"	 => 'UZS: Uzbekistani Som',
		"VEF"	 => 'VEF: Venezuelan Bolívar*',
		"VND"	 => 'VND: Vietnamese Đồng',
		"VUV"	 => 'VUV: Vanuatu Vatu',
		"WST"	 => 'WST: Samoan Tala',
		"XAF"	 => 'XAF: Central African Cfa Franc',
		"XCD"	 => 'XCD: East Caribbean Dollar',
		"XOF"	 => 'XOF: West African Cfa Franc*',
		"XPF"	 => 'XPF: Cfp Franc*',
		"YER"	 => 'YER: Yemeni Rial',
		"ZAR"	 => 'ZAR: South African Rand',
		"ZMW"	 => 'ZMW: Zambian Kwacha',
	);

	/**
	 * Runs when your class is instantiated. Use to setup your plugin instead of __construct()
	 */
	function on_creation() {
		//set names here to be able to translate
		$this->admin_name	 = __( 'Stripe', 'mp' );
		$this->public_name	 = __( 'Credit Card', 'mp' );

		$this->method_img_url		 = mp_plugin_url( 'images/credit_card.png' );
		$this->method_button_img_url = mp_plugin_url( 'images/cc-button.png' );

		$this->publishable_key	 = $this->get_setting( 'api_credentials->publishable_key' );
		$this->secret_key		 = $this->get_setting( 'api_credentials->secret_key' );
		$this->force_ssl		 = (bool) $this->get_setting( 'is_ssl' );
		$this->currency			 = $this->get_setting( 'currency', 'USD' );

		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
	}

	function enqueue_scripts() {
		if ( !mp_is_shop_page( 'checkout' ) ) {
			return;
		}

		wp_enqueue_script( 'js-stripe', 'https://js.stripe.com/v2/', array( 'jquery' ), null );
		wp_enqueue_script( 'stripe-token', mp_plugin_url( 'includes/common/payment-gateways/stripe-files/stripe_token.js' ), array( 'js-stripe', 'jquery-ui-core' ), MP_VERSION );
		wp_localize_script( 'stripe-token', 'stripe', array(
			'publisher_key' => $this->publishable_key
		) );
	}

	/**
	 * Return fields you need to add to the top of the payment screen, like your credit card info fields
	 *
	 * @param array $cart. Contains the cart contents for the current blog, global cart if mp()->global_cart is true
	 * @param array $shipping_info. Contains shipping info and email in case you need it
	 */
	function payment_form( $cart, $shipping_info ) {
		$name = mp_get_user_address_part( 'first_name', 'billing' ) . ' ' . mp_get_user_address_part( 'last_name', 'billing' );

		$content = '
			<input id="mp-stripe-name" type="hidden" value="' . esc_attr( $name ) . '" />
			<div class="mp-checkout-form-row">
				<label>' . __( 'Card Number', 'mp' ) . '<span class="mp-field-required">*</span></label>
				<input
					id="mp-stripe-cc-num"
					type="text"
					pattern="\d*"
					autocomplete="cc-number"
					class="mp-input-cc-num"
					data-rule-required="true"
					data-rule-cc-num="true"
					style="width:200px" />
			</div>
			<div class="mp-checkout-form-row">
				<div class="mp-checkout-input-complex clearfix">
					<div class="mp-checkout-column">
						<label>' . __( 'Expiration', 'mp' ) . '<span class="mp-field-required">*</span> <span class="mp-tooltip-help">' . __( 'Enter in <strong>MM/YYYY</strong> or <strong>MM/YY</strong> format', 'mp' ) . '</span></label>
						<input
							type="text"
							autocomplete="cc-exp"
							id="mp-stripe-cc-exp"
							class="mp-input-cc-exp"
							data-rule-required="true"
							data-rule-cc-exp="true"
							style="width:100px" />
					</div>
					<div class="mp-checkout-column">
						<label>' . __( 'Security Code ', 'mp' ) . '<span class="mp-field-required">*</span> <span class="mp-tooltip-help"><img src="' . mp_plugin_url( 'ui/images/cvv_2.jpg' ) . '" alt="CVV2" /></span></label>
						<input
							id="mp-stripe-cc-cvc"
							class="mp-input-cc-cvc"
							type="text"
							autocomplete="off"
							data-rule-required="true"
							data-rule-cc-cvc="true"
							style="width:75px;" />
					</div>
				</div>
			</div>';

		return $content;
	}

	/**
	 * Return the chosen payment details here for final confirmation. You probably don't need
	 * 	to post anything in the form as it should be in your $_SESSION var already.
	 *
	 * @param array $cart. Contains the cart contents for the current blog, global cart if mp()->global_cart is true
	 * @param array $shipping_info. Contains shipping info and email in case you need it
	 */
	function confirm_payment_form( $cart, $shipping_info ) {
		//make sure token is set at this point
		if ( !isset( $_SESSION[ 'stripeToken' ] ) ) {
			mp()->cart_checkout_error( __( 'The Stripe Token was not generated correctly. Please go back and try again.', 'mp' ) );
			return false;
		}

		//setup the Stripe API
		if ( !class_exists( 'Stripe' ) ) {
			require_once(mp()->plugin_dir . "plugins-gateway/stripe-files/lib/Stripe.php");
		}
		Stripe::setApiKey( $this->secret_key );
		try {
			$token = Stripe_Token::retrieve( $_SESSION[ 'stripeToken' ] );
		} catch ( Exception $e ) {
			mp()->cart_checkout_error( sprintf( __( '%s. Please go back and try again.', 'mp' ), $e->getMessage() ) );
			return false;
		}

		$content = '';
		$content .= '<table class="mp_cart_billing">';
		$content .= '<thead><tr>';
		$content .= '<th>' . __( 'Billing Information:', 'mp' ) . '</th>';
		$content .= '<th align="right"><a href="' . mp_checkout_step_url( 'checkout' ) . '">' . __( '&laquo; Edit', 'mp' ) . '</a></th>';
		$content .= '</tr></thead>';
		$content .= '<tbody>';
		$content .= '<tr>';
		$content .= '<td align="right">' . __( 'Payment method:', 'mp' ) . '</td>';
		$content .= '<td>' . sprintf( __( 'Your <strong>%1$s Card</strong> ending in <strong>%2$s</strong>. Expires <strong>%3$s</strong>', 'mp' ), $token->card->type, $token->card->last4, $token->card->exp_month . '/' . $token->card->exp_year ) . '</td>';
		$content .= '</tr>';
		$content .= '</tbody>';
		$content .= '</table>';
		return $content;
	}

	/**
	 * Initialize the settings metabox
	 *
	 * @since 3.0
	 * @access public
	 */
	public function init_settings_metabox() {
		$metabox = new WPMUDEV_Metabox( array(
			'id'			 => $this->generate_metabox_id(),
			'page_slugs'	 => array( 'store-settings-payments', 'store-settings_page_store-settings-payments' ),
			'title'			 => sprintf( __( '%s Settings', 'mp' ), $this->admin_name ),
			'option_name'	 => 'mp_settings',
			'desc'			 => __( 'Stripe makes it easy to start accepting credit cards directly on your site with full PCI compliance. Accept Visa, MasterCard, American Express, Discover, JCB, and Diners Club cards directly on your site. You don\'t need a merchant account or gateway. Stripe handles everything, including storing cards, subscriptions, and direct payouts to your bank account. Credit cards go directly to Stripe\'s secure environment, and never hit your servers so you can avoid most PCI requirements.', 'mp' ),
			'conditional'	 => array(
				'name'	 => 'gateways[allowed][' . $this->plugin_name . ']',
				'value'	 => 1,
				'action' => 'show',
			),
		) );
		$metabox->add_field( 'checkbox', array(
			'name'	 => $this->get_field_name( 'is_ssl' ),
			'label'	 => array( 'text' => __( 'Force SSL?', 'mp' ) ),
			'desc'	 => __( 'When in live mode Stripe recommends you have an SSL certificate setup for the site where the checkout form will be displayed.', 'mp' ),
		) );
		$creds	 = $metabox->add_field( 'complex', array(
			'name'	 => $this->get_field_name( 'api_credentials' ),
			'label'	 => array( 'text' => __( 'API Credentials', 'mp' ) ),
			'desc'	 => __( 'You must login to Stripe to <a target="_blank" href="https://manage.stripe.com/#account/apikeys">get your API credentials</a>. You can enter your test credentials, then live ones when ready.', 'mp' ),
		) );

		if ( $creds instanceof WPMUDEV_Field ) {
			$creds->add_field( 'text', array(
				'name'		 => 'secret_key',
				'label'		 => array( 'text' => __( 'Secret Key', 'mp' ) ),
				'validation' => array(
					'required' => true,
				),
			) );
			$creds->add_field( 'text', array(
				'name'		 => 'publishable_key',
				'label'		 => array( 'text' => __( 'Publishable Key', 'mp' ) ),
				'validation' => array(
					'required' => true,
				),
			) );
		}

		$metabox->add_field( 'advanced_select', array(
			'name'			 => $this->get_field_name( 'currency' ),
			'label'			 => array( 'text' => __( 'Currency', 'mp' ) ),
			'multiple'		 => false,
			'width'			 => 'element',
			'options'		 => $this->currencies,
			'default_value'	 => mp_get_setting( 'currency' ),
			'desc'			 => __( 'Selecting a currency other than that used for your store may cause problems at checkout.', 'mp' ),
		) );
	}

	/**
	 * Use this to do the final payment. Create the order then process the payment. If
	 * you know the payment is successful right away go ahead and change the order status
	 * as well.
	 *
	 * @param MP_Cart $cart. Contains the MP_Cart object.
	 * @param array $billing_info. Contains billing info and email in case you need it.
	 * @param array $shipping_info. Contains shipping info and email in case you need it
	 */
	function process_payment( $cart, $billing_info, $shipping_info ) {
		//make sure token is set at this point
		$token = mp_get_post_value( 'stripe_token' );
		if ( false === $token ) {
			mp_checkout()->add_error( __( 'The Stripe Token was not generated correctly. Please go back and try again.', 'mp' ), 'order-review-payment' );
			return false;
		}

		//setup the Stripe API
		if ( !class_exists( 'Stripe' ) ) {
			require_once mp_plugin_dir( 'includes/common/payment-gateways/stripe-files/lib/Stripe.php' );
		}

		Stripe::setApiKey( $this->secret_key );

		$totals = array(
			'product_total'	 => $cart->product_total( false ),
			'shipping_total' => $cart->shipping_total( false ),
			'tax_price'		 => 0,
		);

		// Get tax price, if applicable
		if ( !mp_get_setting( 'tax->tax_inclusive' ) ) {
			$totals[ 'tax_price' ] = $cart->tax_total( false );
		}

		// Create a new order object
		$order		 = new MP_Order();
		$order_id	 = $order->get_id();

		// Calc total
		$total = array_sum( $totals );

		try {
			// create the charge on Stripe's servers - this will charge the user's card
			$charge = Stripe_Charge::create( array(
				'amount'		 => round( $total * 100 ), // amount in cents
				'currency'		 => strtolower( $this->currency ),
				'card'			 => $token,
				'description'	 => sprintf( __( '%s Store Purchase - Order ID: %s, Email: %s', 'mp' ), get_bloginfo( 'name' ), $order_id, mp_get_user_address_part( 'email', 'billing' ) ),
			) );

			if ( $charge->paid == 'true' ) {
				//setup our payment details
				$timestamp		 = time();
				$payment_info	 = array(
					'gateway_public_name'	 => $this->public_name,
					'gateway_private_name'	 => $this->admin_name,
					'method'				 => sprintf( __( '%1$s Card ending in %2$s - Expires %3$s', 'mp' ), $charge->card->type, $charge->card->last4, $charge->card->exp_month . '/' . $charge->card->exp_year ),
					'transaction_id'		 => $charge->id,
					'status'				 => array(
						$timestamp => __( 'Paid', 'mp' ),
					),
					'total'					 => $total,
					'currency'				 => $this->currency,
				);

				$order->save( array(
					'cart'			 => $cart,
					'payment_info'	 => $payment_info,
					'billing_info'	 => $billing_info,
					'shipping_info'	 => $shipping_info,
					'paid'			 => true
				) );
			}
		} catch ( Exception $e ) {
			mp_checkout()->add_error( sprintf( __( 'There was an error processing your card: "%s". Please try again.', 'mp' ), $e->getMessage() ), 'payment' );
		}
	}

	/**
	 * INS and payment return
	 */
	function process_ipn_return() {
		
	}
	
	function print_checkout_scripts() {
		// Intentionally left blank
	}

}

//register payment gateway plugin
mp_register_gateway_plugin( 'MP_Gateway_Stripe', 'stripe', __( 'Stripe', 'mp' ) );
