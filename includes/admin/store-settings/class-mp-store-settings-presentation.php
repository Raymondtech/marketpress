<?php

class MP_Store_Settings_Presentation {
	/**
	 * Refers to a single instance of the class
	 *
	 * @since 3.0
	 * @access private
	 * @var object
	 */
	private static $_instance = null;
	
	/**
	 * Gets the single instance of the class
	 *
	 * @since 3.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		if ( is_null(self::$_instance) ) {
			self::$_instance = new MP_Store_Settings_Presentation();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor function
	 *
	 * @since 3.0
	 * @access private
	 */
	private function __construct() {
		$this->init_general_settings();
		$this->init_product_page_settings();
		$this->init_related_product_settings();
		$this->init_product_list_settings();
	}
	
	public function get_image_size_label( $size ) {
		$width = get_option("{$size}_size_w");
		$height = get_option("{$size}_size_h");
		$crop = get_option("{$size}_crop");
		
		return "{$width} x {$height} (" . (( $crop ) ? __('cropped', 'mp') : __('uncropped', 'mp')) . ')';
	}

	/**
	 * Init the product list settings
	 *
	 * @since 3.0
	 * @access public
	 */
	public function init_product_list_settings() {
		$metabox = new WPMUDEV_Metabox(array(
			'id' => 'mp-settings-presentation-product-list',
			'screen_ids' => array('store-settings-presentation', 'store-settings_page_store-settings-presentation'),
			'title' => __('Product Page Settings', 'mp'),
			'desc' => __('Settings related to the display of product lists/grids.', 'mp'),
			'option_name' => 'mp_settings',			
		));
		$metabox->add_field('radio_group', array(
			'name' => 'list_button_type',
			'label' => array('text' => __('Add To Cart Action', 'mp')),
			'desc' => __('MarketPress supports two "flows" for adding products to the shopping cart. After adding a product to their cart, two things can happen:', 'mp'),
			'options' => array(
				'addcart' => __('Stay on current product page', 'mp'),
				'buynow' => __('Redirect to cart page for immediate checkout', 'mp'),
			),
		));	
		$metabox->add_field('checkbox', array(
			'name' => 'show_thumbnail',
			'label' => array('text' => __('Show Product Thumbnail?', 'mp')),
			'message' => __('Yes', 'mp'),
		));
		$metabox->add_field('select', array(
			'name' => 'list_img_size',
			'label' => array('text' => __('Image Size', 'mp')),
			'options' => array(
				'thumbnail' => sprintf(__('Thumbnail - %s', 'mp'), $this->get_image_size_label('thumbnail')),
				'medium' => sprintf(__('Medium - %s', 'mp'), $this->get_image_size_label('medium')),
				'large' => sprintf(__('Large - %s', 'mp'), $this->get_image_size_label('large')),
				'custom' => __('Custom', 'mp'),
			),
			'conditional' => array(
				'name' => 'show_thumbnail',
				'value' => '1',
				'action' => 'show',
			),
		));
		//! TODO: Installer should update old settings names to new. Also need to update all places in code that used old names.
		$custom_size = $metabox->add_field('complex', array(
			'name' => 'list_img_size_custom',
			'label' => array('text' => __('Custom Image Size', 'mp')),
			'conditional' => array(
				'operator' => 'AND',
				'action' => 'show',
				array(
					'name' => 'show_thumbnail',
					'value' => '1',
				),
				array(
					'name' => 'list_img_size',
					'value' => 'custom',
				)
			),
		));
		
		if ( $custom_size instanceof WPMUDEV_Field ) {
			$custom_size->add_field('text', array(
				'name' => 'width',
				'label' => array('text' => __('Width', 'mp')),
			));
			$custom_size->add_field('text', array(
				'name' => 'height',
				'label' => array('text' => __('Height', 'mp')),
			));
		}
		
		$metabox->add_field('radio_group', array(
			'name' => 'image_alignment_list',
			'label' => array('text' => __('Image Alignment', 'mp')),
			'options' => array(
				'alignnone' => __('None', 'mp'),
				'aligncenter' => __('Center', 'mp'),
				'alignleft' => __('Left', 'mp'),
				'alignright' => __('Right', 'mp'),
			),
			'conditional' => array(
				'name' => 'show_thumbnail',
				'value' => '1',
				'action' => 'show',
			),
		));
		$metabox->add_field('checkbox', array(
			'name' => 'show_excerpts',
			'label' => array('text' => __('Show Excerpts?', 'mp')),
			'message' => __('Yes', 'mp'),
		));
		$metabox->add_field('checkbox', array(
			'name' => 'paginate',
			'label' => array('text' => __('Paginate Products?', 'mp')),
			'message' => __('Yes', 'mp'),
		));
		$metabox->add_field('text', array(
			'name' => 'per_page',
			'label' => array('text' => __('Products Per Page', 'mp')),
			'conditional' => array(
				'name' => 'paginate',
				'value' => '1',
				'action' => 'show',
			),
			'validation' => array(
				'digits' => 1,
			),
		));
	}

	/**
	 * Init the related product settings
	 *
	 * @since 3.0
	 * @access public
	 */	
	public function init_related_product_settings() {
		$metabox = new WPMUDEV_Metabox(array(
			'id' => 'mp-settings-presentation-product-related',
			'screen_ids' => array('store-settings-presentation', 'store-settings_page_store-settings-presentation'),
			'title' => __('Related Product Settings', 'mp'),
			'option_name' => 'mp_settings',			
		));	
		$metabox->add_field('checkbox', array(
			'name' => 'related_products[show]',
			'label' => array('text' => __('Show Related Products?', 'mp')),
			'message' => __('Yes', 'mp'),
		));
		$metabox->add_field('text', array(
			'name' => 'related_products[show_limit]',
			'label' => array('text' => __('Related Product Limit', 'mp')),
			'conditional' => array(
				'name' => 'related_products[show]',
				'value' => '1',
				'action' => 'show',
			),
			'validation' => array(
				'digits' => 1,
			),
		));
		$metabox->add_field('select', array(
			'name' => 'related_products[relate_by]',
			'label' => array('text' => __('Relate Products By', 'mp')),
			'options' => array(
				'both' => __('Category &amp; Tags', 'mp'),
				'category' => __('Category Only', 'mp'),
				'tags' => __('Tags Only', 'mp'),
			),
			'conditional' => array(
				'name' => 'related_products[show]',
				'value' => '1',
				'action' => 'show',
			),
		));
		$metabox->add_field('checkbox', array(
			'name' => 'related_products[simple_list]',
			'label' => array('text' => __('Show Related Products As Simple List?', 'mp')),
			'message' => __('Yes', 'mp'),
			'conditional' => array(
				'name' => 'related_products[show]',
				'value' => '1',
				'action' => 'show',
			),
		));
	}
	
	/**
	 * Init the general settings
	 *
	 * @since 3.0
	 * @access public
	 */
	public function init_product_page_settings() {
		$metabox = new WPMUDEV_Metabox(array(
			'id' => 'mp-settings-presentation-product-page',
			'screen_ids' => array('store-settings-presentation', 'store-settings_page_store-settings-presentation'),
			'title' => __('Product Page Settings', 'mp'),
			'desc' => __('Settings related to the display of individual product pages.', 'mp'),
			'option_name' => 'mp_settings',			
		));
		$metabox->add_field('radio_group', array(
			'name' => 'product_button_type',
			'label' => array('text' => __('Add To Cart Action', 'mp')),
			'desc' => __('MarketPress supports two "flows" for adding products to the shopping cart. After adding a product to their cart, two things can happen:', 'mp'),
			'options' => array(
				'addcart' => __('Stay on current product page', 'mp'),
				'buynow' => __('Redirect to cart page for immediate checkout', 'mp'),
			),
		));	
		$metabox->add_field('checkbox', array(
			'name' => 'show_quantity',
			'label' => array('text' => __('Show Quantity Field?', 'mp')),
			'message' => __('Yes', 'mp'),
			'desc' => __('If enabled, users will be able to choose how many of the product they want to purchase before adding to their cart.', 'mp'),
		));
		$metabox->add_field('checkbox', array(
			'name' => 'show_img',
			'label' => array('text' => __('Show Product Image?', 'mp')),
			'message' => __('Yes', 'mp'),
		));
		$metabox->add_field('select', array(
			'name' => 'product_img_size',
			'label' => array('text' => __('Image Size', 'mp')),
			'options' => array(
				'thumbnail' => sprintf(__('Thumbnail - %s', 'mp'), $this->get_image_size_label('thumbnail')),
				'medium' => sprintf(__('Medium - %s', 'mp'), $this->get_image_size_label('medium')),
				'large' => sprintf(__('Large - %s', 'mp'), $this->get_image_size_label('large')),
				'custom' => __('Custom', 'mp'),
			),
			'conditional' => array(
				'name' => 'show_img',
				'value' => '1',
				'action' => 'show',
			),
		));
		//! TODO: Installer should update old settings names to new. Also need to update all places in code that used old names.
		$custom_size = $metabox->add_field('complex', array(
			'name' => 'product_img_size_custom',
			'label' => array('text' => __('Custom Image Size', 'mp')),
			'conditional' => array(
				'operator' => 'AND',
				'action' => 'show',
				array(
					'name' => 'show_img',
					'value' => '1',
				),
				array(
					'name' => 'product_img_size',
					'value' => 'custom',
				)
			),
		));
		
		if ( $custom_size instanceof WPMUDEV_Field ) {
			$custom_size->add_field('text', array(
				'name' => 'width',
				'label' => array('text' => __('Width', 'mp')),
			));
			$custom_size->add_field('text', array(
				'name' => 'height',
				'label' => array('text' => __('Height', 'mp')),
			));
		}
		
		$metabox->add_field('radio_group', array(
			'name' => 'image_alignment_single',
			'label' => array('text' => __('Image Alignment', 'mp')),
			'options' => array(
				'alignnone' => __('None', 'mp'),
				'aligncenter' => __('Center', 'mp'),
				'alignleft' => __('Left', 'mp'),
				'alignright' => __('Right', 'mp'),
			),
			'conditional' => array(
				'name' => 'show_img',
				'value' => '1',
				'action' => 'show',
			),
		));
		$metabox->add_field('checkbox', array(
			'name' => 'disable_large_image',
			'label' => array('text' => __('Disable Large Image Display?', 'mp')),
			'message' => __('Yes', 'mp'),
			'conditional' => array(
				'name' => 'show_img',
				'value' => '1',
				'action' => 'show',
			),
		));
		$metabox->add_field('checkbox', array(
			'name' => 'show_lightbox',
			'label' => array('text' => __('Use Built-In Lightbox for Images?', 'mp')),
			'desc' => __('If you are having conflicts with the lightbox library from your theme or another plugin you should uncheck this.', 'mp'), 
			'message' => __('Yes', 'mp'),
			'conditional' => array(
				'operator' => 'AND',
				'action' => 'show',
				array(
					'name' => 'show_img',
					'value' => '1',
				),
				array(
					'name' => 'disable_large_image',
					'value' => '-1',
				),
			),
		));
	}
	
	/**
	 * Init the general settings
	 *
	 * @since 3.0
	 * @access public
	 */
	public function init_general_settings() {
		$metabox = new WPMUDEV_Metabox(array(
			'id' => 'mp-settings-presentation-general',
			'screen_ids' => array('store-settings-presentation', 'store-settings_page_store-settings-presentation'),
			'title' => __('General Settings', 'mp'),
			'option_name' => 'mp_settings',			
		));
		$metabox->add_field('radio_group', array(
			'name' => 'store_theme',
			'desc' => sprintf(__('This option changes the built-in css styles for store pages. For a custom css style, save your css file with the "MarketPress Style: NAME" header in the <strong>"%s"</strong> folder and it will appear in this list so you may select it. You can also select "None" and create custom theme templates and css to make your own completely unique store design. More information on that <a target="_blank" href="%s">here &raquo;</a>.', 'mp'), WP_CONTENT_DIR . 'marketpress-styles/', mp_plugin_url('ui/themes/Theming_MarketPress.txt')), 
			'label' => array('text' => __('Store Style', 'mp')),
			'options' => array(
				'classic' => __('Classic', 'mp'),
				'icons' => __('Icons', 'mp'),
				'modern' => __('Modern', 'mp'),
				'none' => __('None - Custom Theme Template', 'mp'),
			),
		));
		$metabox->add_field('checkbox', array(
			'name' => 'show_purchase_breadcrumbs',
			'label' => array('text' => __('Show Breadcrumbs?', 'mp')),
			'message' => __('Yes', 'mp'),
			'desc' => __('Shows previous, current and next steps when a customer is checking out -- shown below the title.', 'mp'),
		));
	}
}

MP_Store_Settings_Presentation::get_instance();