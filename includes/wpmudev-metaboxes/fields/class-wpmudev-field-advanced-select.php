<?php

class WPMUDEV_Field_Advanced_Select extends WPMUDEV_Field {
	/**
	 * Runs on parent construct
	 *
	 * @since 1.0
	 * @access public
	 * @param array $args {
	 *		An array of arguments. Optional.
	 *
	 *		@type bool $multiple Whether to allow multi-select or only one option.
	 *		@type string $placeholder The text that shows up when the field is empty.
	 *		@type array $options An array of $key => $value pairs of the available options.
	 *		@type string $width @see http://ivaynberg.github.io/select2/#documentation
	 * }	 
	 */
	public function on_creation( $args ) {
		$this->args = array_replace_recursive(array(
			'multiple' => true,
			'placeholder' => __('Select Some Options', 'mp'),
			'options' => array(),
			'width' => '100%',
		), $args);
		
		$this->args['class'] .= ' wpmudev-advanced-select';
		$this->args['custom']['data-placeholder'] = $this->args['placeholder'];
		$this->args['custom']['data-multiple'] = (int) $this->args['multiple'];
		$this->args['custom']['data-width'] = $this->args['width'];
	}

	/**
	 * Prints scripts
	 *
	 * @since 3.0
	 * @access public
	 */	
	public function print_scripts() {
	?>
<script type="text/javascript">
(function($){
	var initSelect2 = function(){
		$('.wpmudev-advanced-select').each(function(){
			var $this = $(this),
			options = [];
			
			if ( ! $this.is('select') ) {
				$($this.attr('data-options').split('||')).each(function(){
					var val = this.split('=');
					options.push({ "id" : val[0], "text" : val[1] });
				});
			
				$this.select2({
					"allowSelectAllNone" : true,
					"multiple" : $this.attr('data-multiple'),
					"placeholder" : $this.attr('data-placeholder'),
					"initSelection" : function(element, callback){
						var data = [];
						
						$(element.attr('data-value').split('||')).each(function(){
							var val = this.split('=');
							data.push({ "id" : val[0], "text" : val[1] });
						});
						
						callback(data);
					},			
					"data" : options,
					"width" : $this.attr('data-width')
				});
			} else {
				$this.select2({
					"placeholder" : $this.attr('data-placeholder'),
					"width" : $this.attr('data-width')
				});
			}
		});		
	}
	
	$(document).on('wpmudev_repeater_field_before_add_field_group', function(){
		$('.wpmudev-advanced-select').select2('destroy');
		$('[id^="s2id_"]').remove(); // Remove select2 autogenerated elements. For some reason there is a bug in the destroy method.
	});
	
	$(document).on('wpmudev_repeater_field_after_add_field_group', function(e, $group){
		initSelect2();
	});
	
	$(document).ready(function(){
		initSelect2();
	});
}(jQuery));
</script>
	<?php
	parent::print_scripts();
	}

	/**
	 * Sanitizes the field value before saving to database.
	 *
	 * @since 1.0
	 * @access public
	 * @param $value
	 */	
	public function sanitize_for_db( $value ) {
		$value = trim($value, ',');
		return parent::sanitize_for_db($value);
	}

	/**
	 * Displays the field
	 *
	 * @since 1.0
	 * @access public
	 * @param int $post_id
	 */
	public function display( $post_id ) {
		$value = $this->get_value($post_id);
		$vals = explode(',', $value);
		$values = array();
		$options = array();
		
		foreach ( $vals as $val ) {
			$values[] = $val . '=' . $this->args['options'][$val];
		}
		
		foreach ( $this->args['options'] as $val => $label ) {
			$options[] = $val . '=' . $label;
		}
		
		if ( $this->args['multiple'] ) :
			$this->args['custom']['data-options'] = implode('||', $options);
			$this->args['custom']['data-value'] = implode('||', $values);
			echo '<input type="hidden" ' . $this->parse_atts() . ' value="' . $value . '" />';
		else : ?>
			<select <?php echo $this->parse_atts(); ?>>
				<?php foreach ( $this->args['options'] as $val => $label ) : ?>
				<option value="<?php echo $val; ?>"<?php echo in_array($val, $vals) ? ' selected' : ''; ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		<?php
		endif;
	}
	
	/**
	 * Enqueues the field's scripts
	 *
	 * @since 1.0
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('wpmudev-field-select2', WPMUDEV_Metabox::class_url('ui/select2/select2.min.js'), array('jquery'), '3.4.8');
	}
	
	/**
	 * Enqueues the field's styles
	 *
	 * @since 1.0
	 * @access public
	 */
	public function enqueue_styles() {
		wp_enqueue_style('wpmudev-field-select2',  WPMUDEV_Metabox::class_url('ui/select2/select2.css'), array(), '3.4.8');
	}
}