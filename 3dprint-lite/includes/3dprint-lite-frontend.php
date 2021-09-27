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


$p3dlite_email_status_message="";
add_action( 'init', 'p3dlite_request_price' );
function p3dlite_request_price() {
	global $wpdb, $p3dlite_email_status_message;
	if ( isset( $_POST['action'] ) && $_POST['action']=='request_price' ) {
		$product_id=(int)$_POST['p3dlite_product_id'];
		$printer_id=(int)$_POST['attribute_pa_p3dlite_printer'];
		$material_id=(int)$_POST['attribute_pa_p3dlite_material'];
		$coating_id=(int)$_POST['attribute_pa_p3dlite_coating'];
		$model_file= p3dlite_basename( $_POST['attribute_pa_p3dlite_model'] ) ;
		$email_address = sanitize_email( $_POST['p3dlite_email_address'] );
		$request_comment = sanitize_text_field( $_POST['p3dlite_request_comment'] );
		$quantity=(int)$_POST['p3dlite_quantity'];
		$scale=(float)$_POST['p3dlite_resize_scale'];
		if ($_REQUEST['attribute_pa_p3dlite_unit']=='inch')
			$unit='inch';
		else
			$unit='mm';

		$thumbnail_url = '';
		if (isset($_REQUEST['p3dlite_thumb'])) {
			$thumbnail_data=$_REQUEST['p3dlite_thumb'];
			$thumbnail_url=p3dlite_save_thumbnail( $thumbnail_data, $model_file );
		}
#		else {
#			$thumbnail_url=p3dlite_find_thumbnail( $model_file );
#
#		}


		$db_printers=p3dlite_get_option( 'p3dlite_printers' );
		$db_materials=p3dlite_get_option( 'p3dlite_materials' );
		$db_coatings=p3dlite_get_option( 'p3dlite_coatings' );
		$settings=get_option( 'p3dlite_settings' );
		$error=false;
		$upload_dir = wp_upload_dir();

		if ( strlen( $model_file )==0 || !file_exists( $upload_dir['basedir'].'/p3d/'.$model_file ) || strlen( $printer_id )==0 || strlen( $material_id )==0 ) {
			$error=true;
			$p3dlite_email_status_message='<span class="p3dlite-mail-error">'.__( 'Please upload your model and select all options.' , '3dprint-lite' ).'</span>';
		}
		if ( empty( $email_address ) ) {
			$error=true;
			$p3dlite_email_status_message='<span class="p3dlite-mail-error">'.__( 'Please enter valid email address.' , '3dprint-lite' ).'</span>';
		}
		if ( !$error ) {
			//$product_key=$product_id.'_'.$printer_id.'_'.$material_id.'_'.$coating_id.'_'.$unit.'_'.$scale.'_'.$email_address.'_'.base64_encode( p3dlite_basename( $model_file ) );
#			$product_key = p3dlite_generate_request_key($product_id, $printer_id, $material_id, $coating_id, $unit, $scale, $email_address, base64_encode( p3dlite_basename( $model_file )));
			$p3dlite_price_request=array();
			$p3dlite_price_request['printer'] = $db_printers[$printer_id]['name'];
			$p3dlite_price_request['material'] = $db_materials[$material_id]['name'];
			$p3dlite_price_request['coating'] = (isset($db_coatings[$coating_id]) ? $db_coatings[$coating_id]['name'] : '') ;
			$p3dlite_price_request['printer_id'] = $printer_id;
			$p3dlite_price_request['material_id'] = $material_id;
			$p3dlite_price_request['coating_id'] = $coating_id;
			$p3dlite_price_request['product_id'] = $product_id;
			$p3dlite_price_request['unit'] = $unit;
			$p3dlite_price_request['scale'] = $scale;
#			$p3dlite_price_request['base64_filename'] = base64_encode( p3dlite_basename( $model_file ));
			$p3dlite_price_request['thumbnail_url'] = $thumbnail_url;
			$p3dlite_price_request['model_file'] = $model_file;
			$p3dlite_price_request['original_filename'] = $model_file;
			$p3dlite_price_request['quantity'] = $quantity;
#var_dump($quantity);

			$current_templates = get_option( 'p3dlite_email_templates' );
			$template_body = $current_templates['admin_email_body'];
			$template_subject = $current_templates['admin_email_subject'];
			$from = $current_templates['admin_email_from'];


			foreach ( $_POST as $key => $value ) {
				if ( strpos( $key, 'attribute_' )===0 ) {
					if ( !strstr( $key, 'p3dlite_' ) ) $email_attrs[$key]=$value;

					$p3dlite_price_request['attributes'][$key]=$value;
				}

			}

			$p3dlite_price_request['attributes']=json_encode($p3dlite_price_request['attributes']);
			$p3dlite_price_request['price']='';
			$p3dlite_price_request['estimated_price']=(float)$_POST['p3dlite_estimated_price'];
			$p3dlite_price_request['scale']=(float)$_POST['p3dlite_resize_scale'];
			$p3dlite_price_request['scale_x']=(float)$_POST['p3dlite_scale_x'];
			$p3dlite_price_request['scale_y']=(float)$_POST['p3dlite_scale_y'];
			$p3dlite_price_request['scale_z']=(float)$_POST['p3dlite_scale_z'];
			$p3dlite_price_request['weight']=(float)$_POST['p3dlite_weight'];

			$p3dlite_price_request['email']=$email_address;
			$p3dlite_price_request['request_comment']=$request_comment;



			p3dlite_update_option( "p3dlite_price_requests", $p3dlite_price_request );
			$request_id = (int)$wpdb->insert_id;

			// $request_comment
			$upload_dir = wp_upload_dir();
			$filepath = $upload_dir['basedir']."/p3d/$model_file";
			//$original_file = p3dlite_get_original($model_file);
			$link = $upload_dir['baseurl'].'/p3d/'.rawurlencode( p3dlite_basename( $model_file ) );
			$dimensions = (float)$_POST['p3dlite_scale_x']." &times; ".(float)$_POST['p3dlite_scale_y']." &times; ".(float)$_POST['p3dlite_scale_z']." ".__('cm', '3dprint-lite');
			$scale = (float)$_POST['p3dlite_resize_scale'];


			$replace_from = array('[customer_email]','[quantity]', '[printer_name]','[material_name]','[coating_name]','[model_file]','[unit]','[resize_scale]','[dimensions]','[estimated_price]','[customer_comments]','[price_requests_link]');
			$replace_to = array($email_address, $quantity, $db_printers[$printer_id]['name'], $db_materials[$material_id]['name'], 
				   (isset($db_coatings[$coating_id]['name']) ? $db_coatings[$coating_id]['name'] : ''),  $link, $unit, $scale, $dimensions,
					   p3dlite_format_price($p3dlite_price_request['estimated_price'], $settings['currency'], $settings['currency_position']), $request_comment, "<a href='".admin_url( 'admin.php?page=p3dlite_price_requests&action=edit&price_request='.$request_id )."'>".admin_url( 'admin.php?page=p3dlite_price_requests&action=edit&price_request='.$request_id )."</a>");
			$subject=str_ireplace($replace_from, $replace_to, $template_subject);
			$body=str_ireplace($replace_from, $replace_to, $template_body);


#			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
#			$headers[] = "From: $from";
			$headers = array();
			$headers[] = "From: $from";
			$headers[] = 'Content-Type: text/html; charset=UTF-8';


			if ( wp_mail( $settings['email_address'], $subject, stripslashes($body), $headers ) )
				$p3dlite_email_status_message='<span class="p3dlite-mail-success">'.__( 'Store owner has been notified about your request. You\'ll receive the email with the price shortly.' , '3dprint-lite' ).'</span>';
			else
				$p3dlite_email_status_message='<span class="p3dlite-mail-error">'.__( 'Could not send the email. Please try again later.' , '3dprint-lite' ).'</span>';

			p3dlite_clear_cookies();
			do_action( 'p3dlite_request_price' );
		}
	}
}

add_shortcode( '3dprint-lite', 'p3d_lite' );
function p3d_lite( $atts ) {
	global $p3dlite_email_status_message, $post;
	$db_printers=p3dlite_get_option( 'p3dlite_printers' );
	$db_materials=p3dlite_get_option( 'p3dlite_materials' );
	$db_coatings=p3dlite_get_option( 'p3dlite_coatings' );
	$settings=get_option( 'p3dlite_settings' );

	ob_start();
?>
<div class="p3dlite-container">
<div class="p3dlite-images">
	<div id="prompt">
	  <!-- if IE without GCF, prompt goes here -->
	</div>


	<div id="p3dlite-viewer">
		<div class="p3dlite-canvas-wrapper">
			<canvas id="p3dlite-cv" width="<?php echo $settings['canvas_width'];?>" height="<?php echo $settings['canvas_height'];?>"></canvas>
			<div id="p3dlite-file-loading">
				<img alt="Loading file" src="<?php echo $settings['ajax_loader']; ?>">
			</div>
		</div>

		<div id="canvas-stats" style="<?php if ($settings['canvas_stats']!='on') echo 'display:none;';?>">
			<div class="canvas-stats" id="p3dlite-statistics">
			</div>
		</div>
		<div id="p3dlite-model-message">
			<p class="p3dlite-model-message" id="p3dlite-model-message-upload">
				<img alt="Upload" id="p3dlite-model-message-upload-icon" src="<?php echo plugins_url( '3dprint-lite/images/upload45.png'); ?>">
<?php 
				if (preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Trident/', $_SERVER['HTTP_USER_AGENT'])) { //screw ie
?>
				<?php _e("Click here to upload.", '3dprint-lite');?>
<?php
				} else {
?>
				<?php _e("Click here to upload or drag and drop your model to the canvas.", '3dprint-lite');?>
<?php
				}
?>
			</p>
			<p class="p3dlite-model-message" id="p3dlite-model-message-scale"><?php _e("The model is too large and has been resized to fit in the printer's build tray.", '3dprint-lite');?>&nbsp;<span style="cursor:pointer;" onclick='jQuery(this).parent().hide(); return false;'><?php _e('[Hide]', '3dprint-lite');?></span></p>
			<p class="p3dlite-model-message" id="p3dlite-model-message-toolarge"><?php _e("The model is too large to fit in the printer's build tray.", '3dprint-lite');?>&nbsp;<span style="cursor:pointer;" onclick='jQuery(this).parent().hide(); return false;'><?php _e('[Hide]', '3dprint-lite');?></span></p>
			<p class="p3dlite-model-message" id="p3dlite-model-message-fitting-priner"><?php _e("The model is too large, a fitting printer is selected.", '3dprint-lite');?>&nbsp;<span style="cursor:pointer;" onclick='jQuery(this).parent().hide(); return false;'><?php _e('[Hide]', '3dprint-lite');?></span></p>
			<p class="p3dlite-model-message" id="p3dlite-model-message-minside"><?php _e("The model is too small and has been upscaled.", '3dprint-lite');?>&nbsp;<span style="cursor:pointer;" onclick='jQuery(this).parent().hide(); return false;'><?php _e('[Hide]', '3dprint-lite');?></span></p>
			<p class="p3dlite-model-message" id="p3dlite-model-message-fullcolor"><?php _e( 'Warning: The selected printer can not print in full color', '3dprint-lite' );?>&nbsp;<span style="cursor:pointer;" onclick='jQuery(this).parent().hide(); return false;'><?php _e('[Hide]', '3dprint-lite');?></span></p>
			<p class="p3dlite-model-message" id="p3dlite-model-message-multiobj"><?php _e( 'Warning: obj models with multiple meshes are not yet supported', '3dprint-lite' );?>&nbsp;<span style="cursor:pointer;" onclick='jQuery(this).parent().hide(); return false;'><?php _e('[Hide]', '3dprint-lite');?></span></p>


		</div>

	</div>

	<br style="clear:both;">

	<div id="p3dlite-container" onclick="p3dliteDialogCheck();">

<?php
		if (preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Trident/', $_SERVER['HTTP_USER_AGENT']) ) { 
?>
		<button id="p3dlite-pickfiles" style="<?php if ($settings['show_upload_button']!='on') echo 'display:none;';?>background-color:<?php echo $settings['button_color1']?>;" class="progress-button"><?php _e( 'Upload Model', '3dprint-lite' ); ?></button>
<?php
		}
		else {
?>
		<button id="p3dlite-pickfiles" style="<?php if ($settings['show_upload_button']!='on') echo 'display:none;';?>" class="progress-button" data-style="rotate-angle-bottom" data-perspective data-horizontal><?php _e( 'Upload Model', '3dprint-lite' ); ?></button>
<?php
		}

?>
	<div class="p3dlite-info" style="<?php if ($settings['show_unit']!='on') echo 'display:none;';?>">
	<?php _e( 'File Unit:', '3dprint-lite' );?>
		&nbsp;&nbsp;
		<input class="p3dlite-control" autocomplete="off" id="unit_mm" onclick="p3dliteSelectUnit(this);" type="radio" name="p3dlite_unit" value="mm">
		<span style="cursor:pointer;" onclick="p3dliteSelectUnit(jQuery('#unit_mm'));"><?php _e( 'mm', '3dprint-lite' );?></span>
		&nbsp;&nbsp;
		<input class="p3dlite-control" autocomplete="off" id="unit_inch" onclick="p3dliteSelectUnit(this);" type="radio" name="p3dlite_unit" value="inch">
		<span style="cursor:pointer;" onclick="p3dliteSelectUnit(jQuery('#unit_inch'));"><?php _e( 'inch', '3dprint-lite' );?></span>
	</div>
	<div class="p3dlite-info" style="white-space:nowrap;<?php if ($settings['show_scale']!='on') echo 'display:none;';?>">
		<div id="p3dlite-scale-text">
			<?php _e("Scale:", "3dprint-lite"); ?>   
		</div>
		<div id="p3dlite-scale-slider">
			<div id="p3dlite-scale" class="noUiSlider"></div>
		</div>
		<div id="p3dlite-scale-input">
			<input id="p3dlite-slider-range-value" type="text" size="3" autocomplete="off" onchange="p3dliteUpdateSliderValue(this.value)"> %
		</div>
	</div>
	<div class="p3dlite-info" style="white-space:nowrap;<?php if ($settings['show_scale']!='on') echo 'display:none;';?>">
		<div id="p3dlite-scale-text">
			&nbsp;
		</div>
		<div id="p3dlite-scale-dimensions">
			<input type="text" autocomplete="off" class="p3dlite-dim-input" size="3" value="0" id="scale_x" onchange="p3dliteUpdateDimensions(this);"> &times; 
			<input type="text" autocomplete="off" class="p3dlite-dim-input" size="3" value="0" id="scale_y" onchange="p3dliteUpdateDimensions(this);"> &times; 
			<input type="text" autocomplete="off" class="p3dlite-dim-input" size="3" value="0" id="scale_z" onchange="p3dliteUpdateDimensions(this);">&nbsp;<?php _e("cm", "3dprint-lite"); ?>
		</div>
	</div>

	</div>
	<div class="p3dlite-info">
		<pre id="p3dlite-console"></pre>
	</div>
	<div id="p3dlite-filelist"></div>
	<div class="p3dlite-info">
	  	<span id="p3dlite-error-message" class="error"></span>
	</div>

	<div class="p3dlite-info" style="<?php if ($settings['model_stats']!='on') echo 'display:none;';?>">     

		<table class="p3dlite-stats">
			<tr style="<?php if ($settings['show_model_stats_material_volume']!='on') echo 'display:none;';?>">
				<td>
					<?php _e('Material Volume', '3dprint-lite');?>:
				</td>
				<td>
					<span id="stats-material-volume"></span> <?php _e('cm3', '3dprint-lite');?>
				</td>
			</tr>
			<tr style="<?php if ($settings['show_model_stats_box_volume']!='on') echo 'display:none;';?>">
				<td>
					<?php _e('Box Volume', '3dprint-lite');?>:
				</td>
				<td>
					<span id="stats-box-volume"></span> <?php _e('cm3', '3dprint-lite');?>
				</td>
			</tr>
			<tr style="<?php if ($settings['show_model_stats_surface_area']!='on') echo 'display:none;';?>">
				<td>
					<?php _e('Surface Area', '3dprint-lite');?>:
				</td>
				<td>
					<span id="stats-surface-area"></span> <?php _e('cm2', '3dprint-lite');?>
				</td>
			</tr>
			<tr style="<?php if ($settings['show_model_stats_model_weight']!='on') echo 'display:none;';?>">
				<td>
					<?php _e('Model Weight', '3dprint-lite');?>:
				</td>
				<td>
					<span id="stats-weight"></span> <?php _e('g', '3dprint-lite');?>
				</td>
			</tr>
			<tr style="<?php if ($settings['show_model_stats_model_dimensions']!='on') echo 'display:none;';?>">
				<td>
					<?php _e('Model Dimensions', '3dprint-lite');?>:
				</td>
				<td>
					<span id="stats-length"></span> x <span id="stats-width"></span> x <span id="stats-height"></span>
					<?php _e('cm', '3dprint-lite');?>
				</td>
			</tr>

		</table>
	</div>
</div>
<div class="p3dlite-details">
	<div id="price-wrapper">
		<div id="price-container">
			<p class="price">
			        <?php if ( $settings['pricing']=='request_estimate' ) echo '<b>'.__( 'Estimated Price:', '3dprint-lite' ).'</b>';?>
				<span class="amount"></span>
			</p>
		</div>
	</div>

	<form action="" style="margin-bottom:0px;" class="p3dlite_form" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>">
		<input type="hidden" name="p3dlite_product_id" value="<?php echo get_the_ID();?>">
		<input type="hidden" id="pa_p3dlite_printer" name="attribute_pa_p3dlite_printer" value="">
		<input type="hidden" id="pa_p3dlite_material" name="attribute_pa_p3dlite_material" value="">
		<input type="hidden" id="pa_p3dlite_coating" name="attribute_pa_p3dlite_coating" value="">
		<input type="hidden" id="pa_p3dlite_model" name="attribute_pa_p3dlite_model" value="">
		<input type="hidden" id="pa_p3dlite_unit" name="attribute_pa_p3dlite_unit" value="">
		<input type="hidden" id="p3dlite_estimated_price" name="p3dlite_estimated_price" value="">
		<input type="hidden" id="p3dlite-resize-scale" name="p3dlite_resize_scale" value="1">
		<input type="hidden" id="p3dlite-scale-x" name="p3dlite_scale_x" value="">
		<input type="hidden" id="p3dlite-scale-y" name="p3dlite_scale_y" value="">
		<input type="hidden" id="p3dlite-scale-z" name="p3dlite_scale_z" value="">
		<input type="hidden" id="p3dlite-weight" name="p3dlite_weight" value="">
		<input type="hidden" id="p3dlite-thumb" name="p3dlite_thumb" value="">
                <?php do_action( 'p3dlite_form' );?>
		<div id="p3dlite-quote-loading" class="p3dlite-info">
			<img alt="Loading price" src="<?php echo esc_url($settings['ajax_loader']); ?>">
		</div>

<?php
	if ( !empty( $p3dlite_email_status_message ) ) echo '<div class="p3dlite-info">'.$p3dlite_email_status_message.'</div>';
?>
		<div id="add-cart-wrapper">
			<div id="add-cart-container">
				<div class="variations_button p3dlite-info">
					<input type="hidden" value="request_price" name="action">
					<input class="price-request-field" type="text" value="" placeholder="<?php _e( 'Enter Your E-mail', '3dprint-lite' );?>" name="p3dlite_email_address">
					<input class="price-request-field" type="text" value="" placeholder="<?php _e( 'Leave a comment', '3dprint-lite' );?>" name="p3dlite_request_comment"><br>
					<input class="price-request-field" type="number" value="1" min="1" step="1" alt="<?php _e( 'Quantity', '3dprint-lite' );?>" title="<?php _e( 'Quantity', '3dprint-lite' );?>" placeholder="<?php _e( 'Quantity', '3dprint-lite' );?>" name="p3dlite_quantity"><br>
					<button style="float:left;" type="submit" class="button alt"><?php _e( 'Request a Quote', '3dprint-lite' ); ?></button>
				</div>
			</div>
		</div>
	</form>


<?php
	$db_printers=p3dlite_get_option( 'p3dlite_printers' );
	$db_materials=p3dlite_get_option( 'p3dlite_materials' );
	$db_coatings=p3dlite_get_option( 'p3dlite_coatings' );

$assigned_materials = p3dlite_get_assigned_materials($db_printers, $db_materials);
#foreach ($db_materials as $key => $material) {
#	if (!in_array($key, $assigned_materials)) unset($db_materials[$key]);
#}

//prepare photos and descriptions
echo '<div class="tooltip_templates">';
foreach ( $db_printers as $db_printer ) {
	$i = $db_printer['id'];
	if (strlen($db_printer['description']) || strlen($db_printer['photo'])) {
		echo '<div class="p3dlite-tooltip-info" id="p3dlite-tooltip-printer-'.$i.'">';
		if (strlen($db_printer['description']) == 0) $image_class = 'p3dlite-tooltip-image-full'; else $image_class = '';
		if (strlen($db_printer['photo']))
			echo '<div class="p3dlite-tooltip-image '.$image_class.'"><img src="'.esc_url($db_printer['photo']).'"></div>';
		if (strlen($db_printer['description']))
			echo '<div class="p3dlite-tooltip-description">'.esc_html(stripslashes($db_printer['description'])).'</div>';
		echo '</div>';
	}
}

foreach ( $db_materials as $db_material ) {
	$i = $db_material['id'];
	if (strlen($db_material['description']) || strlen($db_material['photo'])) {
		echo '<div class="p3dlite-tooltip-info" id="p3dlite-tooltip-material-'.$i.'">';
		if (strlen($db_material['description']) == 0) $image_class = 'p3dlite-tooltip-image-full'; else $image_class = '';
		if (strlen($db_material['photo']))
			echo '<div class="p3dlite-tooltip-image '.$image_class.'"><img src="'.esc_url($db_material['photo']).'"></div>';
		if (strlen($db_material['description']))
			echo '<div class="p3dlite-tooltip-description">'.esc_html(stripslashes($db_material['description'])).'</div>';
		echo '</div>';
	}
}

foreach ( $db_coatings as $db_coating ) {
	$i = $db_coating['id'];
	if (strlen($db_coating['description']) || strlen($db_coating['photo'])) {
		echo '<div class="p3dlite-tooltip-info" id="p3dlite-tooltip-coating-'.$i.'">';
		if (strlen($db_coating['description']) == 0) $image_class = 'p3dlite-tooltip-image-full'; else $image_class = '';
		if (strlen($db_coating['photo']))
			echo '<div class="p3dlite-tooltip-image '.$image_class.'"><img src="'.esc_url($db_coating['photo']).'"></div>';
		if (strlen($db_coating['description']))
			echo '<div class="p3dlite-tooltip-description">'.esc_html(stripslashes($db_coating['description'])).'</div>';
		echo '</div>';
	}
}
echo '</div>';
if ($settings['selection_order']=='materials_printers') {
	switch ($settings['materials_layout']) {
		case 'lists':
			include('templates/template_material_list.php');
		break;
		case 'dropdowns':
			include('templates/template_material_dropdown.php');
		break;
		case 'colors':
				include('templates/template_material_colors.php');
		break;
		default:
			include('templates/template_material_list.php');
		break;
	}

	switch ($settings['coatings_layout']) {
		case 'lists':
			include('templates/template_coating_list.php');
		break;
		case 'dropdowns':
			include('templates/template_coating_dropdown.php');
		break;
		case 'colors':
			include('templates/template_coating_colors.php');
		break;
		default:
			include('templates/template_coating_list.php');
		break;
	}

	switch ($settings['printers_layout']) {
		case 'lists':
			include('templates/template_printer_list.php');
		break;
		case 'dropdowns':
			include('templates/template_printer_dropdown.php');
		break;
		default:
			include('templates/template_printer_list.php');
		break;
	}
}
elseif ($settings['selection_order']=='printers_materials') {
	switch ($settings['printers_layout']) {
		case 'lists':
			include('templates/template_printer_list.php');
		break;
		case 'dropdowns':
			include('templates/template_printer_dropdown.php');
		break;
		default:
			include('templates/template_printer_list.php');
		break;
	}

	switch ($settings['materials_layout']) {
		case 'lists':
			include('templates/template_material_list.php');
		break;
		case 'dropdowns':
			include('templates/template_material_dropdown.php');
		break;
		case 'colors':
			include('templates/template_material_colors.php');
		break;
		default:
			include('templates/template_material_list.php');
		break;
	}

	switch ($settings['coatings_layout']) {
		case 'lists':
			include('templates/template_coating_list.php');
		break;
		case 'dropdowns':
			include('templates/template_coating_dropdown.php');
		break;
		case 'colors':
			include('templates/template_coating_colors.php');
		break;
		default:
			include('templates/template_coating_list.php');
		break;
	}


}


?>





</div>
</div>



<?php

	$content = ob_get_clean();

	return $content;
}
?>