<?php
/**
 *
 *
 * @author Sergey Burkov, http://www.wp3dprinting.com
 * @copyright 2015
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Output buffering allows admin screens to make redirects later on.
 */
add_action( 'admin_init', 'p3dlite_buffer', 1 );
function p3dlite_buffer() {
	ob_start();
}

add_action( 'admin_menu', 'register_3dprintlite_menu_page' );
function register_3dprintlite_menu_page() {
//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
	add_menu_page( 'Settings', '3DPrint Lite', 'manage_options', '3dprint-lite', 'register_3dprintlite_settings_page_callback' );
	add_submenu_page( '3dprint-lite', 'Settings', 'Settings', 'manage_options', 'p3dlite_settings', 'register_3dprintlite_settings_page_callback' );
	add_submenu_page( '3dprint-lite', 'Materials', 'Materials', 'manage_options', 'p3dlite_materials', 'register_3dprintlite_materials_page_callback' );
	add_submenu_page( '3dprint-lite', 'Printers', 'Printers', 'manage_options', 'p3dlite_printers', 'register_3dprintlite_printers_page_callback' );
	add_submenu_page( '3dprint-lite', 'Coatings', 'Coatings', 'manage_options', 'p3dlite_coatings', 'register_3dprintlite_coatings_page_callback' );
	add_submenu_page( '3dprint-lite', 'Infills', 'Infills', 'manage_options', 'p3dlite_infills', 'register_3dprintlite_infills_page_callback' );
	add_submenu_page( '3dprint-lite', 'Price Requests', 'Price Requests', 'manage_options', 'p3dlite_price_requests', 'register_3dprintlite_price_requests_page_callback' );
	add_submenu_page( '3dprint-lite', 'Email Templates', 'Email Templates', 'manage_options', 'p3dlite_email_templates', 'register_3dprintlite_email_templates_page_callback' );
	add_submenu_page( '3dprint-lite', 'File Manager', 'File Manager', 'manage_options', 'p3dlite_file_manager', 'register_3dprintlite_file_manager_page_callback' );
#	add_submenu_page( '3dprint-lite', 'Discounts', 'Discounts', 'manage_options', 'p3dlite_discounts', 'register_3dprintlite_discounts_page_callback' );



}


function register_3dprintlite_settings_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != '3dprint-lite' && $_GET['page'] != 'p3dlite_settings') return false;
	if ( !current_user_can('administrator') ) return false;

	$settings=p3dlite_get_option( 'p3dlite_settings' );

	if ( isset( $_POST['action'] ) && $_POST['action']=='save_login' ) {
		$settings['api_login']=sanitize_text_field($_POST['api_login']);
		update_option( 'p3dlite_settings', $settings );
	}


	if ( isset( $_POST['p3dlite_settings'] ) && !empty( $_POST['p3dlite_settings'] ) ) {
	        $settings_update = array_map('sanitize_text_field', $_POST['p3dlite_settings']);

		if (isset($_FILES['p3dlite_settings']['tmp_name']['ajax_loader']) && strlen($_FILES['p3dlite_settings']['tmp_name']['ajax_loader'])>0) {
			$uploaded_file = p3dlite_upload_file('p3dlite_settings', 'ajax_loader');
			$settings_update['ajax_loader']=str_replace('http:','',$uploaded_file['url']);
		}
		else {
			$settings_update['ajax_loader']=$settings['ajax_loader'];
		}
/*
		if (isset($_FILES['woo3dv_settings']['tmp_name']['view3d_button_image']) && strlen($_FILES['woo3dv_settings']['tmp_name']['view3d_button_image'])>0) {
			$uploaded_file = woo3dv_upload_file('woo3dv_settings', 'view3d_button_image');
			$settings_update['view3d_button_image']=str_replace('http:','',$uploaded_file['url']);
		}
		else {
			$settings_update['view3d_button_image']=$settings['view3d_button_image'];
		}
*/
		if (!is_numeric($settings_update['num_decimals'])) $settings_update['num_decimals'] = 2;
		if (empty($settings_update['canvas_width'])) $settings_update['canvas_width'] = 1024;
		if (empty($settings_update['canvas_height'])) $settings_update['canvas_height'] = 768;
		if (empty($settings_update['file_max_size'])) $settings_update['file_max_size'] = 20;
		if (empty($settings_update['file_chunk_size'])) $settings_update['file_chunk_size'] = 2;
		if (empty($settings_update['items_per_page'])) $settings_update['items_per_page'] = 10;




		update_option( 'p3dlite_settings', $settings_update );
	}

	$settings=p3dlite_get_option( 'p3dlite_settings' );
#var_dump( $settings['ninjaforms_shortcode']);
#	$shortcode_atts = shortcode_parse_atts( $settings['ninjaforms_shortcode'] );
#	if (isset($shortcode_atts['id']))
#		$form_id = (int)$shortcode_atts['id'];
#	else	
#		$form_id = 0;
	

	add_thickbox(); 
#p3dlite_check_install();
	$p3dlite_cache=p3dlite_get_option('p3dlite_cache');
	$p3dlite_triangulation_cache=p3dlite_get_option('p3dlite_triangulation_cache');


?>
<div class="wrap">
	<?php _e('Shortcode:', '3dprint-lite');?> <input type="text" name="textbox" value="[3dprint-lite]" onclick="this.select()" />
	<br>
	<h2><?php _e( '3D printing settings', '3dprint-lite' );?></h2>
	<form method="post" action="admin.php?page=p3dlite_settings" enctype="multipart/form-data">
	<div id="p3dlite_tabs">

		<ul>
			<li><a href="#3dp_tabs-0"><?php _e( 'Settings', '3dprint-lite' );?></a></li>
		</ul>
		<div id="p3dlite_tabs-0">
				<p><b><?php _e( 'Pricing', '3dprint-lite' );?></b></p>
				<table>
					<tr>
						<td>
							<?php _e( 'Get a Quote', '3dprint-lite' );?>
						</td>
						<td>
							<select name="p3dlite_settings[pricing]">
								<option <?php if ( $settings['pricing']=='request_estimate' ) echo 'selected';?> value="request_estimate"><?php _e( 'Give an estimate and request price', '3dprint-lite' );?></option>
								<option <?php if ( $settings['pricing']=='request' ) echo 'selected';?> value="request"><?php _e( 'Request price', '3dprint-lite' );?></option>
								<option disabled value="checkout"><?php _e( 'Calculate price and add to cart (Premium only)' , '3dprint-lite' );?></option>
			 				</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Minimum Price', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[min_price]" value="<?php echo $settings['min_price'];?>"><?php echo $settings['currency'];?>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Minimum Price Type', '3dprint-lite' );?></td>
						<td>
							<select name="p3dlite_settings[minimum_price_type]">
								<option <?php if ( $settings['minimum_price_type']=='minimum_price' ) echo 'selected';?> value="minimum_price"><?php _e( 'Minimum Price' , '3dprint-lite' );?></option>
								<option <?php if ( $settings['minimum_price_type']=='starting_price' ) echo 'selected';?> value="starting_price"><?php _e( 'Starting Price' , '3dprint-lite' );?></option>
						 	</select>
							<img class="tooltip" title="<?php htmlentities(_e( 'Minimum Price: if total is less than minimum price then total = minimum price. <br> Starting Price: total = total + starting price.', '3dprint-lite' ));?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Currency', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[currency]" value="<?php echo $settings['currency'];?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Currency Position', '3dprint-lite' );?>
						</td>
						<td>
							<select name="p3dlite_settings[currency_position]">
								<option <?php if ($settings['currency_position']=='left') echo 'selected';?> value="left"><?php _e('Left', '3dprint-lite');?>
								<option <?php if ($settings['currency_position']=='left_space') echo 'selected';?> value="left_space"><?php _e('Left with space', '3dprint-lite');?>
								<option <?php if ($settings['currency_position']=='right') echo 'selected';?> value="right"><?php _e('Right', '3dprint-lite');?>
								<option <?php if ($settings['currency_position']=='right_space') echo 'selected';?> value="right_space"><?php _e('Right with space', '3dprint-lite');?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Number of Decimals', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[num_decimals]" value="<?php echo $settings['num_decimals'];?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Thousands Separator', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[thousand_sep]" value="<?php echo $settings['thousand_sep'];?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Decimal Point', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[decimal_sep]" value="<?php echo $settings['decimal_sep'];?>">
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Round Price To', '3dprint-lite' );?></td>
						<td>
							<input type="text" size="2" disabled /><?php _e('digits', '3dprint-lite'); ?> 
							<img class="tooltip" title="<?php esc_attr_e( 'Examples:<br>2 digits rounds 1.9558 to 1.96<br>-3 digits rounds 1241757 to 1242000', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
							<?php
								_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
							?>

						</td>
					</tr>
					<tr>
						<td><?php _e( 'Show Support Charges', '3dprint-lite' );?></td>
						<td>
							<input type="checkbox" disabled>
							<img class="tooltip" title="<?php esc_attr_e( 'Shows support removal charges on the product page (Analyse API required).', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
							<?php
								_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
							?>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Price Debug Mode', '3dprint-lite' );?></td>
						<td>
							<input type="checkbox" disabled>
							<img class="tooltip" title="<?php esc_attr_e( 'Shows price calculation details on the product page in the browser console (F12)', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
							<?php
								_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
							?>
						</td>
					</tr>

				</table>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />
				<hr>
				<p><b><?php _e( 'Product Viewer', '3dprint-lite' );?></b></p>
				<table>
					<tr>
						<td><?php _e( 'Canvas Resolution', '3dprint-lite' );?></td>
						<td>
							<input size="3" type="text"  placeholder="<?php _e( 'Width', '3dprint-lite' );?>" name="p3dlite_settings[canvas_width]" value="<?php echo $settings['canvas_width'];?>">px &times; <input size="3"  type="text" placeholder="<?php _e( 'Height', '3dprint-lite' );?>" name="p3dlite_settings[canvas_height]" value="<?php echo $settings['canvas_height'];?>">px
							<img class="tooltip" title="<?php _e('Only affects the image quality', '3dprint-lite');?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>

					<tr>
						<td><?php _e( 'Shading', '3dprint-lite' );?></td>
						<td>
							<select name="p3dlite_settings[shading]">
								<option <?php if ( $settings['shading']=='flat' ) echo 'selected';?> value="flat"><?php _e( 'Flat', '3dprint-lite' );?></option>
								<option <?php if ( $settings['shading']=='smooth' ) echo 'selected';?> value="smooth"><?php _e( 'Smooth', '3dprint-lite' );?></option>
							</select> 
							<img class="tooltip" title="<img src='<?php echo plugins_url( '3dprint-lite/images/shading.jpg' );?>'>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>


					<tr>
						<td><?php _e( 'Cookie Lifetime', '3dprint-lite' );?></td>
						<td>
							<select name="p3dlite_settings[cookie_expire]">
								<option <?php if ( $settings['cookie_expire']=='0' ) echo 'selected';?> value="0">0 <?php _e( '(no cookies)', '3dprint-lite' );?> 
								<option <?php if ( $settings['cookie_expire']=='1' ) echo 'selected';?> value="1">1
								<option <?php if ( $settings['cookie_expire']=='2' ) echo 'selected';?> value="2">2
							</select> <?php _e( 'days', '3dprint-lite' );?> 
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Multistep Process', '3dprint-lite' );?></td>
						<td><input type="checkbox" disabled>
							<img class="tooltip" title="<?php _e('Enables the user to collapse & expand steps by clicking on next/back buttons', '3dprint-lite');?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
					<?php
						_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
					?>
						</td>
					</tr>

					<tr>
						<td><?php _e( 'Adjust canvas position on scroll', '3dprint-lite' );?></td>
						<td><input type="checkbox" disabled>
					<?php
						_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
					?>
					</tr>
					<tr>
						<td><?php _e( 'Background Color', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[background1]" value="<?php echo $settings['background1'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Grid Color', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[plane_color]" value="<?php echo $settings['plane_color'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Ground Color', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[ground_color]" value="<?php echo $settings['ground_color'];?>"></td>
					</tr>

					<tr>
						<td><?php _e( 'Light Sources', '3dprint-lite' );?></td>
						<td>
							<table>
								<tr>
									<td><input type="checkbox" disabled></td>
									<td><input type="checkbox" disabled></td>
									<td><input type="checkbox" disabled checked></td>
								</tr>
								<tr>
									<td><input type="checkbox" disabled></td>
									<td><input type="checkbox" disabled></td>
									<td><input type="checkbox" disabled></td>
								</tr>
								<tr>
									<td><input type="checkbox" disabled checked></td>
									<td><input type="checkbox" disabled></td>
									<td><input type="checkbox" disabled></td>
								</tr>

							</table>
					<?php
						_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
					?>

						</td>
					</tr>

					<tr>
						<td><?php _e( 'Printer Color', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[printer_color]" value="<?php echo $settings['printer_color'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Background', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color1]" value="<?php echo $settings['button_color1'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Shadow', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color2]" value="<?php echo $settings['button_color2'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Progress Bar', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color3]" value="<?php echo $settings['button_color3'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Font', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color4]" value="<?php echo $settings['button_color4'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Tick', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color5]" value="<?php echo $settings['button_color5'];?>"></td>
					</tr>

					<tr>
						<td><?php _e( 'Loading Image', '3dprint-lite' );?></td>
						<td>
							<img class="3dprint-lite-preview" src="<?php echo esc_url($settings['ajax_loader']);?>">
							<input type="file" name="p3dlite_settings[ajax_loader]" accept="image/*">
						</td>
					</tr>

					<tr>
						<td><?php _e( 'Auto Scale', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[auto_scale]" value="0"><input type="checkbox" name="p3dlite_settings[auto_scale]" <?php if ($settings['auto_scale']=='on') echo 'checked';?>>
							<img class="tooltip" title="<?php esc_attr_e( 'Enables automatic scaling if a model is too large or too small.', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">

						</td>
					</tr>
					<tr>
						<td><?php _e( 'Auto Rotation', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[auto_rotation]" value="0"><input type="checkbox" name="p3dlite_settings[auto_rotation]" <?php if ($settings['auto_rotation']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Resize model on scale', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[resize_on_scale]" value="0"><input type="checkbox" name="p3dlite_settings[resize_on_scale]" <?php if ($settings['resize_on_scale']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Fit camera to model on resize', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[fit_on_resize]" value="0"><input type="checkbox" name="p3dlite_settings[fit_on_resize]" <?php if ($settings['fit_on_resize']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Shadows', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_shadow]" value="0"><input type="checkbox" name="p3dlite_settings[show_shadow]" <?php if ($settings['show_shadow']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Ground Mirror', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[ground_mirror]" value="0"><input type="checkbox" name="p3dlite_settings[ground_mirror]" <?php if ($settings['ground_mirror']=='on') echo 'checked';?>></td>
					</tr>

					<tr>
						<td><?php _e( 'Show Grid', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_grid]" value="0"><input type="checkbox" name="p3dlite_settings[show_grid]" <?php if ($settings['show_grid']=='on') echo 'checked';?>></td>
					</tr>

					<tr>
						<td><?php _e( 'Show Upload Button', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_upload_button]" value="0"><input type="checkbox" name="p3dlite_settings[show_upload_button]" <?php if ($settings['show_upload_button']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Scaling', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_scale]" value="0"><input type="checkbox" name="p3dlite_settings[show_scale]" <?php if ($settings['show_scale']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Can Scale Axis Independently', '3dprint-lite' );?></td>
						<td><input type="checkbox" disabled>
						<?php
						_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
						?>
						</td>
					</tr>

					<tr>
						<td><?php _e( 'Show Rotation Controls', '3dprint-lite' );?></td>
						<td><input type="checkbox" disabled>
							<?php
							_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
							?>
						</td>
					</tr>

					<tr>
						<td><?php _e( 'Show Printer Box', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_printer_box]" value="0"><input type="checkbox" name="p3dlite_settings[show_printer_box]" <?php if ($settings['show_printer_box']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Canvas Stats', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[canvas_stats]" value="0"><input type="checkbox" name="p3dlite_settings[canvas_stats]" <?php if ($settings['canvas_stats']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show File Unit', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_unit]" value="0"><input type="checkbox" name="p3dlite_settings[show_unit]" <?php if ($settings['show_unit']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Model Stats', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[model_stats]" value="0"><input type="checkbox" name="p3dlite_settings[model_stats]" <?php if ($settings['model_stats']=='on') echo 'checked';?>>
							<div id="show_model_stats_extra" style="display:none;">
								<table>
									<tr>
										<td><?php _e( 'Material Volume', '3dprint-lite' );?></td>
										<td><input type="hidden" name="p3dlite_settings[show_model_stats_material_volume]" value="0"><input type="checkbox" name="p3dlite_settings[show_model_stats_material_volume]" <?php if ($settings['show_model_stats_material_volume']=='on') echo 'checked';?>></td>
									</tr>
									<tr>
										<td><?php _e( 'Support Material Volume', '3dprint-lite' );?></td>
										<td><input type="checkbox" disabled>
										<?php
											_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
										?>
										</td>
									</tr>
									<tr>
										<td><?php _e( 'Box Volume', '3dprint-lite' );?></td>
										<td><input type="hidden" name="p3dlite_settings[show_model_stats_box_volume]" value="0"><input type="checkbox" name="p3dlite_settings[show_model_stats_box_volume]" <?php if ($settings['show_model_stats_box_volume']=='on') echo 'checked';?>></td>
									</tr>
									<tr>
										<td><?php _e( 'Surface Area', '3dprint-lite' );?></td>
										<td><input type="hidden" name="p3dlite_settings[show_model_stats_surface_area]" value="0"><input type="checkbox" name="p3dlite_settings[show_model_stats_surface_area]" <?php if ($settings['show_model_stats_surface_area']=='on') echo 'checked';?>></td>
									</tr>
									<tr>
										<td><?php _e( 'Model Weight', '3dprint-lite' );?></td>
										<td><input type="hidden" name="p3dlite_settings[show_model_stats_model_weight]" value="0"><input type="checkbox" name="p3dlite_settings[show_model_stats_model_weight]" <?php if ($settings['show_model_stats_model_weight']=='on') echo 'checked';?>></td>
									</tr>
									<tr>
										<td><?php _e( 'Model Dimensions', '3dprint-lite' );?></td>
										<td><input type="hidden" name="p3dlite_settings[show_model_stats_model_dimensions]" value="0"><input type="checkbox" name="p3dlite_settings[show_model_stats_model_dimensions]" <?php if ($settings['show_model_stats_model_dimensions']=='on') echo 'checked';?>></td>
									</tr>
									<tr>
										<td><?php _e( 'Print Time', '3dprint-lite' );?></td>
										<td><input type="checkbox" disabled>
										<?php
											_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
										?>
										</td>
									</tr>

								</table>
							</div>

							<a href="#TB_inline?width=300&height=200&inlineId=show_model_stats_extra" class="thickbox"><button onclick="return false;">...</button></a>

						</td>
					</tr>
					<tr>
						<td><?php _e( 'Selection Order', '3dprint-lite' );?></td>
						<td>
							<select name="p3dlite_settings[selection_order]">
								<option <?php if ( $settings['selection_order']=='materials_printers' ) echo 'selected';?> value="materials_printers"><?php _e( 'First materials, then printers', '3dprint-lite' );?></option>
								<option <?php if ( $settings['selection_order']=='printers_materials' ) echo 'selected';?> value="printers_materials"><?php _e( 'First printers, then materials', '3dprint-lite' );?></option>

							</select> 
						</td>
					</tr>

					<tr>
						<td><?php _e( 'Printers Layout', '3dprint-lite' );?></td>
						<td>
							<select name="p3dlite_settings[printers_layout]">
								<option <?php if ( $settings['printers_layout']=='lists' ) echo 'selected';?> value="lists"><?php _e( 'List', '3dprint-lite' );?></option>
								<option <?php if ( $settings['printers_layout']=='dropdowns' ) echo 'selected';?> value="dropdowns"><?php _e( 'Dropdown', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Searchable Dropdown (available in Premium)', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Slider (available in Premium)', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Group Slider (available in Premium)', '3dprint-lite' );?></option>

							</select> 
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Materials Layout', '3dprint-lite' );?></td>
						<td>
							<select name="p3dlite_settings[materials_layout]">
								<option <?php if ( $settings['materials_layout']=='lists' ) echo 'selected';?> value="lists"><?php _e( 'List', '3dprint-lite' );?></option>
								<option <?php if ( $settings['materials_layout']=='dropdowns' ) echo 'selected';?> value="dropdowns"><?php _e( 'Dropdown', '3dprint-lite' );?></option>
								<option <?php if ( $settings['materials_layout']=='colors' ) echo 'selected';?> value="colors"><?php _e( 'Colors', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Searchable Dropdown (available in Premium)', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Slider (available in Premium)', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Group Slider (available in Premium)', '3dprint-lite' );?></option>
							</select> 
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Coatings Layout', '3dprint-lite' );?></td>
						<td>
							<select name="p3dlite_settings[coatings_layout]">
								<option <?php if ( $settings['coatings_layout']=='lists' ) echo 'selected';?> value="lists"><?php _e( 'List', '3dprint-lite' );?></option>
								<option <?php if ( $settings['coatings_layout']=='dropdowns' ) echo 'selected';?> value="dropdowns"><?php _e( 'Dropdown', '3dprint-lite' );?></option>
								<option <?php if ( $settings['coatings_layout']=='colors' ) echo 'selected';?> value="colors"><?php _e( 'Colors', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Searchable Dropdown (available in Premium)', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Slider (available in Premium)', '3dprint-lite' );?></option>
								<option disabled><?php _e( 'Group Slider (available in Premium)', '3dprint-lite' );?></option>
							</select> 
						</td>
					</tr>


					<tr>
						<td><?php _e( 'Show Printers', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_printers]" value="0"><input type="checkbox" name="p3dlite_settings[show_printers]" <?php if ($settings['show_printers']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Materials', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_materials]" value="0"><input type="checkbox" name="p3dlite_settings[show_materials]" <?php if ($settings['show_materials']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Coatings', '3dprint-lite' );?></td>
						<td><input type="hidden" name="p3dlite_settings[show_coatings]" value="0"><input type="checkbox" name="p3dlite_settings[show_coatings]" <?php if ($settings['show_coatings']=='on') echo 'checked';?>></td>
					</tr>
				</table>
				<hr>
				<p><b><?php _e( 'Form builder', '3dprint-lite' );?></b></p>
				<p>
				<?php
					_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
				?>
				</p>

				<p><?php _e( 'In <a target="_blank" href="https://youtu.be/ZB82ozu8I94">this video</a> you can see how to configure NinjaForms integration.', '3dprint-lite' );?></p>
				<table>
					<tr>
						<td><?php _e( 'Use NinjaForms', '3dprint-lite' );?></td>
						<td>
							<input type="hidden" disabled value="0">
							<input type="checkbox" disabled>&nbsp;
							<img class="tooltip" title="<?php htmlentities(_e( 'Use NinjaForms 3.0+ builder for the price request form.', '3dprint-lite' ));?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>
					<tr>
						<td><?php _e( 'NinjaForms ID', '3dprint-lite' );?></td>
						<td><input id="p3dlite-ninjaforms-shortcode" type="text" placeholder="2" disabled value="">&nbsp;
							<button id="p3dlite-generate-button" type="button" disabled><?php _e('Generate', '3dprint-lite')?></button>
							<img id="p3dlite-generate-image" style="display:inline-block;visibility:hidden;" alt="Generating" src="<?php echo plugins_url( '3dprint-lite/images/ajax-loader-small.gif'); ?>">
						</td>
					</tr>
				</table>



				<hr>
				<p><b><?php _e( 'File Upload', '3dprint-lite' );?></b></p>
				<table>
					<tr>
						<td><?php _e( 'Max. File Size', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[file_max_size]" value="<?php echo $settings['file_max_size'];?>"><?php _e( 'mb', '3dprint-lite' );?> </td>
					</tr>
					<tr>
						<td><?php _e( 'File Chunk Size', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[file_chunk_size]" value="<?php echo $settings['file_chunk_size'];?>"><?php _e( 'mb', '3dprint-lite' );?> </td>
					</tr>
					<tr>
						<td><?php _e( 'Allowed Extensions', '3dprint-lite' );?></td>
						<td><input size="9" type="text" name="p3dlite_settings[file_extensions]" value="<?php echo $settings['file_extensions'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Delete files older than', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[max_days]" value="<?php echo $settings['max_days'];?>"><?php _e( 'days', '3dprint-lite' );?> </td>
					</tr>
				</table>
				<hr>
				<p><b><?php _e( 'Other', '3dprint-lite' );?></b></p>
				<table>
					<tr>
						<td><?php _e( 'Email', '3dprint-lite' );?></td>
						<td><input type="text" placeholder="user@example.com" name="p3dlite_settings[email_address]" value="<?php echo $settings['email_address'];?>">&nbsp;
						<img class="tooltip" title="<?php htmlentities(_e( 'The email where price requests go.', '3dprint-lite' ));?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Items per page', '3dprint-lite' );?></td>
						<td>
							<input size="3" type="text" name="p3dlite_settings[items_per_page]" value="<?php echo $settings['items_per_page'];?>">
							<img class="tooltip" title="<?php esc_attr_e( 'Number of iterms per page in the admin area.', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Load On', '3dprint' );?></td>
						<td>
							<select name="p3dlite_settings[load_everywhere]">
								<option <?php if ( $settings['load_everywhere']=='shortcode' ) echo "selected";?> value="shortcode"><?php _e('Pages with the shortcode', '3dprint-lite');?></option>
								<option <?php if ( $settings['load_everywhere']=='on' ) echo "selected";?> value="on"><?php _e('Everywhere', '3dprint-lite');?></option>
							</select>
							<img class="tooltip" title="<?php esc_attr_e( 'Loads css and js files on certain pages of the site.', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', '3dprint-lite' ) ?>" />
				</p>
		</div>
	</div>
	</form>
</div>
<?php
}

function register_3dprintlite_printers_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != 'p3dlite_printers') return false;
	if ( !current_user_can('administrator') ) return false;
	$settings=p3dlite_get_option( 'p3dlite_settings' );

	$wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_printers where status=1", ARRAY_A );
	if ($wpdb->num_rows==0) { //should not happen, but let's create a default one, at least one printer is required
		$printer_default_data=array( 'name'=>'At least one active printer is required for the plugin to work', 'status'=>1, 'width'=>300, 'length'=>400, 'height'=>300 );
		$wpdb->insert( $wpdb->prefix . 'p3dlite_printers', $printer_default_data );

	}

	if (isset($_POST['p3dlite_printers_description'])) {
		update_option('p3dlite_printers_description', wp_kses_post(nl2br($_POST['p3dlite_printers_description'])));
	}

	if (isset($_GET['action']) && $_GET['action'] == 'edit') {
		$printer_id = (int)$_GET['printer'];
		$printer_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_printers where id='$printer_id'", ARRAY_A );
		$printer = $printer_result[0];

		$materials=p3dlite_get_option( 'p3dlite_materials' );
		$infills=p3dlite_get_option( 'p3dlite_infills' );

		add_thickbox(); 

//		if (count($_POST)) {
//			$printers=p3dlite_get_option( 'p3dlite_printers' );
//			foreach ($printers as $key => $printer) {
//				wp_set_object_terms( 0, strval($key), 'pa_p3dlite_printer' , false );
//			}
//		}
		include('3dprint-lite-admin-printers-edit.php');
		
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'clone') {
		$printer_id = (int)$_GET['printer'];
		$printer_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_printers where id='$printer_id'", ARRAY_A );
		$clone_data = $printer_result[0];
		unset($clone_data['id']);
		$wpdb->insert($wpdb->prefix."p3dlite_printers", $clone_data);

		wp_redirect( admin_url( 'admin.php?page=p3dlite_printers&action=edit&printer='.(int)$wpdb->insert_id ) );
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'add') {

			$default_printer_data = array(
				'status' => '1',
				'name' => 'New Printer',
				'description' => '',
				'photo' => '',
				'type' => 'fff',
				'full_color' => '1',
				'platform_shape' => 'rectangle',
				'width' => '300',
				'length' => '400',
				'height' => '300',
				'diameter' => '300',
				'min_side' => '1',
				'price' => '0',
				'price_type' => 'box_volume',
				'infills' => '0,10,20,30,40,50,60,70,80,90,100',
				'default_infill' => '20',
				'materials' => "",
				'group_name' => '',
				'sort_order' => '0'
			);
			$wpdb->insert($wpdb->prefix."p3dlite_printers", $default_printer_data);
			wp_redirect( admin_url( 'admin.php?page=p3dlite_printers&action=edit&printer='.(int)$wpdb->insert_id ) );
	}
	else {
		include('3dprint-lite-admin-printers.php');
		$p3dlitep_instance = p3dliteP_Plugin::get_instance();
		$p3dlitep_instance->plugin_settings_page();
	}
}

function register_3dprintlite_materials_page_callback() {
	global $wpdb;

	if ( $_GET['page'] != 'p3dlite_materials') return false;
	if ( !current_user_can('administrator') ) return false;

	$settings=p3dlite_get_option( 'p3dlite_settings' );

	if (isset($_POST['p3dlite_materials_description'])) {
		update_option('p3dlite_materials_description', wp_kses_post(nl2br($_POST['p3dlite_materials_description'])));
	}

	$wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_materials where status=1", ARRAY_A );
	if ($wpdb->num_rows==0) { //should not happen, but let's create a default one, at least one material is required
		$default_material_data=array(
				'status' => '1',
				'name' => 'At least one active material is required for the plugin to work',
				'description' => '',
				'photo' => '',
				'type' => 'filament',
				'density' => '1.26',
				'length' => '330',
				'diameter' => '1.75',
				'weight' => '1',
				'price' => '0.03',
				'price_type' => 'gram',
				'roll_price' => '20',
				'group_name' => 'PLA',
				'color' => '#08c101',
				'shininess' => 'plastic',
				'glow' => '0',
				'transparency' => 'opaque'
			);
		$wpdb->insert( $wpdb->prefix . 'p3dlite_materials', $default_material_data );
	}


	if (isset($_GET['action']) && $_GET['action'] == 'edit') {

		$material_id = (int)$_GET['material'];
		$material_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_materials where id='$material_id'", ARRAY_A );
		$material = $material_result[0];


		add_thickbox(); 
//		if (count($_POST)) {
//			$materials=p3dlite_get_option( 'p3dlite_materials' );
//			foreach ($materials as $key => $material) {
//				wp_set_object_terms( 0, strval($key), 'pa_p3dlite_material' , false );
//			}
//		}

       		include('3dprint-lite-admin-materials-edit.php');
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'clone') {
		$material_id = (int)$_GET['material'];
		$material_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_materials where id='$material_id'", ARRAY_A );
		$clone_data = $material_result[0];
		unset($clone_data['id']);
		$wpdb->insert($wpdb->prefix."p3dlite_materials", $clone_data);

		wp_redirect( admin_url( 'admin.php?page=p3dlite_materials&action=edit&material='.(int)$wpdb->insert_id ) );
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'add') {

			$default_material_data = array(
				'status' => '1',
				'name' => 'New Material',
				'description' => '',
				'photo' => '',
				'type' => 'filament',
				'density' => '1.26',
				'length' => '330',
				'diameter' => '1.75',
				'weight' => '1',
				'price' => '0.03',
				'price_type' => 'gram',
				'roll_price' => '20',
				'group_name' => 'PLA',
				'color' => '#08c101',
				'shininess' => 'plastic',
				'glow' => '0',
				'transparency' => 'opaque'

			);
			$wpdb->insert($wpdb->prefix."p3dlite_materials", $default_material_data);
			wp_redirect( admin_url( 'admin.php?page=p3dlite_materials&action=edit&material='.(int)$wpdb->insert_id ) );
	}
	else {
		include('3dprint-lite-admin-materials.php');
		$p3dlitem_instance = p3dliteM_Plugin::get_instance();
		$p3dlitem_instance->plugin_settings_page();
	}


}


function register_3dprintlite_coatings_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != 'p3dlite_coatings') return false;
	if ( !current_user_can('administrator') ) return false;

	$settings=p3dlite_get_option( 'p3dlite_settings' );

	if (isset($_POST['p3dlite_coatings_description'])) {
		update_option('p3dlite_coatings_description', wp_kses_post(nl2br($_POST['p3dlite_coatings_description'])));
	}

	$materials=p3dlite_get_option( 'p3dlite_materials' );

	add_thickbox(); 

//	if (count($_POST)) {
//		$coatings=p3dlite_get_option( 'p3dlite_coatings' );
//		foreach ($coatings as $key => $coating) {
//			wp_set_object_terms( 0, strval($key), 'pa_p3dlite_coating' , false );
//		}
//	}

	if (isset($_GET['action']) && $_GET['action'] == 'edit') {

		$coating_id = (int)$_GET['coating'];
		$coating_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_coatings where id='$coating_id'", ARRAY_A );
		$coating = $coating_result[0];

		add_thickbox(); 
//		if (count($_POST)) {
//			$coatings=p3dlite_get_option( 'p3dlite_coatings' );
//			foreach ($coatings as $key => $coating) {
//				wp_set_object_terms( 0, strval($key), 'pa_p3dlite_coating' , false );
//			}
//		}

       		include('3dprint-lite-admin-coatings-edit.php');
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'clone') {
		$coating_id = (int)$_GET['coating'];
		$coating_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_coatings where id='$coating_id'", ARRAY_A );
		$clone_data = $coating_result[0];
		unset($clone_data['id']);
		$wpdb->insert($wpdb->prefix."p3dlite_coatings", $clone_data);

		wp_redirect( admin_url( 'admin.php?page=p3dlite_coatings&action=edit&coating='.(int)$wpdb->insert_id ) );
	}
	elseif (isset($_GET['action']) && $_GET['action'] == 'add') {

			$default_coating_data = array(
				'status' => '1',
				'name' => 'New Coating',
				'description' => '',
				'photo' => '',
				'color' => '#08c101',
			);
			$wpdb->insert($wpdb->prefix."p3dlite_coatings", $default_coating_data);
			wp_redirect( admin_url( 'admin.php?page=p3dlite_coatings&action=edit&coating='.(int)$wpdb->insert_id ) );
	}
	else {
		include('3dprint-lite-admin-coatings.php');
		$p3dlitec_instance = p3dliteC_Plugin::get_instance();
		$p3dlitec_instance->plugin_settings_page();
	}

}



function register_3dprintlite_price_requests_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != 'p3dlite_price_requests') return false;
	if ( !current_user_can('administrator') ) return false;

	$settings=p3dlite_get_option( 'p3dlite_settings' );

	if (isset($_GET['action']) && $_GET['action'] == 'edit') {
		$price_request_id = (int)$_GET['price_request'];

		if (empty($price_request_id)) {
			wp_redirect( admin_url( 'admin.php?page=p3dlite_price_requests' ) );
		}

		$price_request_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_price_requests where id='$price_request_id'", ARRAY_A );
		$price_request = $price_request_result[0];

       		include('3dprint-lite-admin-price-requests-edit.php');
	}
	else {
		include('3dprint-lite-admin-price-requests.php');
		$p3dlitepr_instance = p3dlitePR_Plugin::get_instance();
		$p3dlitepr_instance->plugin_settings_page();
	}



#	p3dlite_check_install();


}

function register_3dprintlite_email_templates_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != 'p3dlite_email_templates') return false;
	if ( !current_user_can('administrator') ) return false;

	$settings=p3dlite_get_option( 'p3dlite_settings' );

	if ( isset( $_POST['p3dlite_email_templates'] ) && !empty( $_POST['p3dlite_email_templates'] ) ) {
		$templates_update = $_POST['p3dlite_email_templates'];

		if (isset($templates_update['client_email_preserve_html']) && $templates_update['client_email_preserve_html']=='on') {
			$templates_update['client_email_body'] = $templates_update['client_email_body_raw'];

		}
		else {
			$templates_update['client_email_body'] = nl2br($templates_update['client_email_body']);
		}
		if (isset($templates_update['admin_email_preserve_html']) && $templates_update['admin_email_preserve_html']=='on') {
			$templates_update['admin_email_body'] = $templates_update['admin_email_body_raw'];

		}
		else {
			$templates_update['admin_email_body'] = nl2br($templates_update['admin_email_body']);
		}
		update_option( 'p3dlite_email_templates', $templates_update );
	}
	$current_templates = get_option( 'p3dlite_email_templates' );



#p3dlite_check_install();
?>
	<form method="post" action="admin.php?page=p3dlite_email_templates" enctype="multipart/form-data">
	<div id="p3dlite_tabs">

		<ul>
			<li><a href="#3dp_tabs-0"><?php _e( 'Email Templates', '3dprint-lite' );?></a></li>
		</ul>
		<div id="p3dlite_tabs-0">
				<p><b><?php _e('E-mail to admin', '3dprint-lite'); ?>:</b></p>

				<p><?php _e('Available shortcodes', '3dprint-lite'); ?>:<?php echo implode(', ', array('[customer_email]','[product_id]','[printer_name]','[material_name]','[coating_name]','[quantity]', '[model_file]','[unit]','[resize_scale]','[dimensions]','[estimated_price]','[customer_comments]','[price_requests_link]'));?></p>

				<p><?php _e('From', '3dprint-lite');?>:<input type="text" size="50" name="p3dlite_email_templates[admin_email_from]" value="<?php echo esc_attr($current_templates['admin_email_from']);?>" />&nbsp;<i>Name Surname &#x3C;me@example.net&#x3E;</i>
					<img class="tooltip" title="<?php esc_attr_e( 'Please note that if you put a domain name different from your site\'s domain  the email may go to spam.', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
				</p>

				<p><?php _e('Subject', '3dprint-lite');?>:<input type="text" size="50" name="p3dlite_email_templates[admin_email_subject]" value="<?php echo esc_attr($current_templates['admin_email_subject']);?>" /></p>
				<p><?php _e('Preserve HTML', '3dprint-lite');?>:<input type="checkbox" name="p3dlite_email_templates[admin_email_preserve_html]" onchange="p3dliteToggleAdminBodyRaw(this);" <?php if (isset($current_templates['admin_email_preserve_html']) && $current_templates['admin_email_preserve_html']=='on') echo 'checked="checked"';?>/></p>
				<div style="<?php if (isset($current_templates['admin_email_preserve_html']) && $current_templates['admin_email_preserve_html']=='on') echo 'display:none;';?>" id="p3dlite_wpeditor_admin_wrap">
				<?php wp_editor(wpautop(stripslashes($current_templates['admin_email_body'])), 'admin_email_body', array('textarea_name' => 'p3dlite_email_templates[admin_email_body]', 'editor_height'=>100) ); ?>
				</div>
				<textarea class="wp-editor-area" style="height: 200px;width:100%;<?php if (!isset($current_templates['admin_email_preserve_html']) || $current_templates['admin_email_preserve_html']!='on') echo 'display:none;';?>" autocomplete="off" cols="40" name="p3dlite_email_templates[admin_email_body_raw]" id="admin_email_body_raw">
				<?php
					echo stripslashes($current_templates['admin_email_body']);
				?>
				</textarea>
				<p><b><?php _e('E-mail to client', '3dprint-lite'); ?>:</b></p>
				<p><?php _e('Available shortcodes', '3dprint-lite'); ?>:<?php echo implode(', ', array('[printer_name]', '[quantity]', '[original_filename]', '[material_name]', '[coating_name]', '[model_file]', '[dimensions]', '[weight]','[price]', '[price_total]', '[admin_comments]'));?></p>

				<p><?php _e('From', '3dprint-lite');?>:<input type="text" size="50" name="p3dlite_email_templates[client_email_from]" value="<?php echo esc_attr($current_templates['client_email_from']);?>" />&nbsp;<i>Name Surname &#x3C;me@example.net&#x3E;</i>
					<img class="tooltip" title="<?php esc_attr_e( 'Please note that if you put a domain name different from your site\'s domain the email may go to spam.', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
				</p>

				<p><?php _e('Subject', '3dprint-lite');?>:<input type="text" size="50" name="p3dlite_email_templates[client_email_subject]" value="<?php echo esc_attr($current_templates['client_email_subject']);?>" /></p>
				<p><?php _e('Preserve HTML', '3dprint-lite');?>:<input type="checkbox" name="p3dlite_email_templates[client_email_preserve_html]" onchange="p3dliteToggleClientBodyRaw(this);" <?php if (isset($current_templates['client_email_preserve_html']) && $current_templates['client_email_preserve_html']=='on') echo 'checked="checked"';?>/></p>
				<div style="<?php if (isset($current_templates['client_email_preserve_html']) && $current_templates['client_email_preserve_html']=='on') echo 'display:none;';?>" id="p3dlite_wpeditor_client_wrap">
				<?php wp_editor(wpautop(stripslashes($current_templates['client_email_body'])), 'client_email_body', array('textarea_name' => 'p3dlite_email_templates[client_email_body]', 'editor_height'=>100) ); ?>
				</div>
				<textarea class="wp-editor-area" style="height: 200px;width:100%;<?php if (!isset($current_templates['client_email_preserve_html']) || $current_templates['client_email_preserve_html']!='on') echo 'display:none;';?>" autocomplete="off" cols="40" name="p3dlite_email_templates[client_email_body_raw]" id="client_email_body_raw">
				<?php
					echo stripslashes($current_templates['client_email_body']);
				?>
				</textarea>


				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save', '3dprint-lite' ) ?>" />
				</p>
		</div>
	</div>
	</form>
<?php
}

function register_3dprintlite_discounts_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != 'p3dlite_discounts') return false;
	if ( !current_user_can('administrator') ) return false;


?>

<?php
	_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
?>

<?php
}

function register_3dprintlite_infills_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != 'p3dlite_infills') return false;
	if ( !current_user_can('administrator') ) return false;


?>
<p>
<?php
	_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
?>
</p>
<p>
<?php
	_e('Requires <a href="https://www.wp3dprinting.com/feature-comparison/">a subscription!</a>', '3dprint-lite');
?>
</p>
<p>
Screenshot:
</p>
<img src="<?php echo plugins_url( '3dprint-lite/images/infills.jpg' ); ?>">

<?php
}

function register_3dprintlite_file_manager_page_callback() {
	global $wpdb;
	if ( $_GET['page'] != 'p3dlite_file_manager') return false;
	if ( !current_user_can('administrator') ) return false;


?>
<p>
<?php
	_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
?>
</p>
<p>
Screenshot:
</p>
<img src="<?php echo plugins_url( '3dprint-lite/images/file_manager.jpg' ); ?>">
<?php
}

?>