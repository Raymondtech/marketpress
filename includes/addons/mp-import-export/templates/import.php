
<div class="wrap theme-options">
	<h2><?php _e( 'MarketPress Import', 'mp' ); ?></h2><br />
	
	<?php if ( ! empty( $this->messages ) ): ?>	
		<?php add_thickbox(); ?>
		<div id="response-wrapper" style="display:none;">
			<ul id="response-top">
				<?php foreach( $this->messages as $message ) : ?>
					<li><?php echo $message; ?></li>
				<?php endforeach; ?>
			</ul>
			<ol id="response-bottom" reversed="true"></ol>
			<div id="progress-bar"></div>
		</div>
		<a href="#TB_inline?width=600&height=250&inlineId=response-wrapper" title="<?php _e( 'MarketPress Import Report', 'mp' ); ?>" id="thickbox-launcher" class="thickbox button-primary"><?php _e( 'MarketPress Import Report', 'mp' ); ?></a><br /><br />
	<?php endif; ?>

	<form action="edit.php?post_type=<?php echo MP_Product::get_post_type(); ?>&page=marketpress_import" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="process" />
		<input type="hidden" id="file_path" name="file_path" value="<?php echo $this->uploaded_file; ?>" />
		<input type="hidden" id="lines_count" name="lines_count" value="<?php echo $this->lines_count; ?>" />
		<input type="hidden" id="items_by_step" name="items_by_step" value="<?php echo $this->items_by_step; ?>" />
		<div class="postbox metabox-holder">
			<h3 class="hndle"><?php _e( 'MarketPress Import', 'mp' ); ?></h3>
			<div class="inside">
				<div class="setting-panel">
					<p class="mp-select">
						<label for="import-from">
							<span><?php _e( 'Import from:', 'mp' ); ?></span>
							<select type="text" name="import-from" id="import-from">
								<option value="mp"><?php _e( 'MarketPress', 'mp' ); ?></option>
								<!-- <option value="woo"><?php _e( 'WooCommerce', 'mp' ); ?></option> -->
							</select>
						</label>
					</p>
					<p class="mp-select">
						<label for="import-version">
							<span><?php _e( 'Plugin Version:', 'mp' ); ?></span>
							<select type="text" name="import-version" id="import-version">
								<option class="mp" value="30"><?php _e( '3.0', 'mp' ); ?></option>
								<option class="mp" value="29"><?php _e( '2.9', 'mp' ); ?></option>
								<option class="woo" value="woo24"><?php _e( '2.4', 'mp' ); ?></option>
							</select>
						</label>
					</p>
					<p class="mp-select">
						<label for="import-types">
							<span><?php _e( 'What do you want to import:', 'mp' ); ?></span>
							<select type="text" name="import-types" id="import-types">
								<option value="products"><?php _e( 'Products', 'mp' ); ?></option>
								<option value="orders"><?php _e( 'Orders', 'mp' ); ?></option>
								<!-- <option value="customers"><?php _e( 'Customers', 'mp' ); ?></option> -->
							</select>
						</label>
					</p>
					<p class="mp-file">
						<label for="datafile">
							<span><?php _e( 'Choose CSV Data file', 'mp' ); ?></span>
							<input type="file" id="datafile" name="datafile" size="40" />
						</label>					
					</p>
					<p class="mp-text">
						<label for="field-separator">
							<span><?php _e( 'CSV field separator', 'mp' ); ?></span>
							<input type="text" id="field-separator" name="field-separator" value="," size="5" />
						</label>					
					</p>
					<p class="mp-text">
						<label for="text-separator">
							<span><?php _e( 'CSV text separator', 'mp' ); ?></span>
							<input type="text" id="text-separator" name="text-separator" value='"' size="5" />
						</label>					
					</p>
					<p class="mp-text">
						<label for="items-by-step">
							<span><?php _e( 'How many items do you want to import by step?', 'mp' ); ?></span>
							<input type="text" id="items-by-step" name="items-by-step" value='10' size="5" />
						</label>					
					</p>
				</div>
			</div>
		</div>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Start Importing', 'mp' ); ?>" class="button-primary">
		</p>
	</form>
</div>