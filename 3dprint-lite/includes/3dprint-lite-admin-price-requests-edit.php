<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

	if ( isset($_POST['action']) && $_POST['action']=='save') {
		foreach ( $_POST['p3dlite_buynow'] as $id=>$price ) {
			$product_id = $price_request['product_id'];
			$comments = $_POST['p3dlite_comments'][$id];
			$weight = $_POST['p3dlite_weight'][$id];
			$quantity = $_POST['p3dlite_quantity'][$id];
			$length = $_POST['p3dlite_length'][$id];
			$width = $_POST['p3dlite_width'][$id];
			$height = $_POST['p3dlite_height'][$id];

			$update_array = array('admin_comment'=>$comments, 'price'=>$price, 'weight'=>$weight, 'quantity'=>$quantity, 'scale_x'=>$length, 'scale_y'=>$width, 'scale_z'=>$height);
//			if ((float)$price>0) {
//				$update_array['status']=1;
//			}

			$wpdb->update($wpdb->prefix.'p3dlite_price_requests', $update_array, array('id'=>$id));
			wp_redirect( admin_url( 'admin.php?page=p3dlite_price_requests&action=edit&price_request='.(int)$_GET['price_request'] ) );
		}

	}
	if ( isset($_POST['action']) && $_POST['action']=='update' && isset( $_POST['p3dlite_buynow'] ) && count( $_POST['p3dlite_buynow'] )>0 ) {
		$is_bundle = false;

		$body='';
		foreach ( $_POST['p3dlite_buynow'] as $id=>$price ) {


#			$product_id = $price_request['product_id'];
//			$request_key = $price_request['request_key'];
#			$product_check = $wpdb->get_results( "select post_type from {$wpdb->prefix}posts where ID='$product_id'", ARRAY_A );
#			if ($product_check[0]['post_type']!='product') {
#				echo '<div class="error"><p>'."Product with ID $id does not exists, can not send a quote!".'</p></div>';
#				wp_die();
#			}
#			$product = new WC_Product_Variable( $product_id );

			$comments = $_POST['p3dlite_comments'][$id];
			$weight = $_POST['p3dlite_weight'][$id];
			$quantity = $_POST['p3dlite_quantity'][$id];
			$length = $_POST['p3dlite_length'][$id];
			$width = $_POST['p3dlite_width'][$id];
			$height = $_POST['p3dlite_height'][$id];
			$custom_attributes = '';
			$message = '';


			if ( count( $price_request ) ) {
				$email=$price_request['email'];
				if (strlen($email)==0) $email=$price_request['email_address'];
				$variation=json_decode($price_request['attributes'], true);
				$filename=$variation['attribute_pa_p3dlite_model'];
//				$original_filename=$variation['attribute_pa_p3dlite_filename'];
				$variation['attribute_pa_p3dlite_model']=rawurlencode( $variation['attribute_pa_p3dlite_model'] );
#				$variation['attribute_pa_p3dlite_cutting_instructions']=base64_encode( $variation['attribute_pa_p3dlite_cutting_instructions'] );



#echo $product_url;
//				if ( $price ) {
				if ( true ) {
					//echo $product_url;
					$price_request['price']=$price;


					#$db_printers=p3dlite_get_option( 'p3dlite_printers' );
#					$db_materials=p3dlite_get_option( 'p3dlite_materials' );
#					$db_coatings=p3dlite_get_option( 'p3dlite_coatings' );

					$current_templates = get_option( 'p3dlite_email_templates' );
					$template_body = $current_templates['client_email_body'];
					$template_subject = $current_templates['client_email_subject'];
					$from = $current_templates['client_email_from'];


					$upload_dir = wp_upload_dir();
					$link = $upload_dir['baseurl'] ."/p3d/".rawurlencode($filename);
					$subject=__( "Your model's price" , '3dprint-lite' );



					$model_link = "<a href='".$link."'>".$filename."</a>";


					$dimensions=$price_request['scale_x']." &times; ".$price_request['scale_y']." &times; ".$price_request['scale_z']." ".__('cm', '3dprint-lite')."<br>";



					#$buy_now = "<a href='".$product_url."'>".$product_url."</a>";

#					$price_requests_url = '<a href="'.wc_get_account_endpoint_url('price-requests').'">'.wc_get_account_endpoint_url('price-requests').'</a>';

					$replace_from = array('[quantity]', '[printer_name]', '[material_name]', '[coating_name]', '[model_file]', '[dimensions]', '[weight]', '[price]', '[price_total]', '[admin_comments]');
					$replace_to = array ($quantity, $price_request['printer'], $price_request['material'], 
							   (isset($price_request['coating']) ? $price_request['coating'] : ''), $link, $dimensions, $weight,
							    p3dlite_format_price($price, $settings['currency'], $settings['currency_position']), p3dlite_format_price($price*$quantity, $settings['currency'], $settings['currency_position']), $comments);
					$subject=str_ireplace($replace_from, $replace_to, $template_subject);
					$body=str_ireplace($replace_from, $replace_to, $template_body);


					do_action('p3dlite_send_quote', $message);
					$headers = array();
					$headers[] = "From: $from";
					$headers[] = 'Content-Type: text/html; charset=UTF-8';

					$wpdb->update($wpdb->prefix.'p3dlite_price_requests', array('status'=>1, 'admin_comment'=>$comments, 'price'=>$price, 'quantity'=>$quantity, 'weight'=>$weight, 'scale_x'=>$length, 'scale_y'=>$width, 'scale_z'=>$height), array('id'=>$id));

				}
#				else {
#					echo '<div class="error"><p>' . __('Please set the price.' ,'3dprint-lite').'</p></div>';
#				}
				
			}//if ( count( $price_requests ) ) 
		}//foreach ( $_POST['p3dlite_buynow'] as $key=>$price )

/*

*/

		$price_request_id = (int)$_GET['price_request'];

		if (wp_mail( $email, $subject, stripslashes($body), $headers )) {
			//p3dlite_update_option( 'p3dlite_price_requests', $price_request );
			wp_redirect( admin_url( 'admin.php?page=p3dlite_price_requests' ) );
		} else {
			$wpdb->update($wpdb->prefix.'p3dlite_price_requests', array('status'=>3), array('id'=>$price_request_id));
			echo '<div class="error"><p>' . __('Could not email the quote! Check if your wordpress site can send emails and consider installing Easy WP SMTP plugin.' ,'3dprint-lite').'</p></div>';
		}


		$price_request_result = $wpdb->get_results( "select * from {$wpdb->prefix}p3dlite_price_requests where id='$price_request_id'", ARRAY_A );
		$price_request = $price_request_result[0];

		do_action('p3dlite_after_send_quotes');
	}//if ( isset( $_POST['p3dlite_buynow'] ) && count( $_POST['p3dlite_buynow'] )>0 )

#	$price_requests=p3dlite_get_option( 'p3dlite_price_requests' );

//		wp_redirect( admin_url( 'admin.php?page=p3dlite_printers' ) );

#p3dlite_check_install();

#echo 

$currency_rate = 1;
if (isset($price_request['estimated_price_currency']) && strlen($price_request['estimated_price_currency'])) {
	$currency_rate = p3dlite_get_currency_rate($price_request['estimated_price_currency']);
}

$price_requests = array();
$price_requests[]=$price_request;




?>
	<form method="post" action="admin.php?page=p3dlite_price_requests&action=edit&price_request=<?php echo (int)$_GET['price_request']?>" enctype="multipart/form-data">
				<input id="p3dlite_action" type="hidden" name="action" value="update" />
				<input type="hidden" id="p3dlite_currency_rate" value="<?php echo $currency_rate;?>">
				<br style="clear:both">
				<button class="button-secondary" type="button" onclick="location.href='<?php echo admin_url( 'admin.php?page=p3dlite_price_requests' );?>'"><b>&#8592;<?php _e('Back to price requests', '3dprint-lite');?></b></button>
<?php
foreach ($price_requests as $price_request) {
	$attributes=json_decode($price_request['attributes'], true);
	$file_url = $original_file_url = '';

	$upload_dir = wp_upload_dir();
	$filepath = $upload_dir['basedir']."/p3d/".$price_request['model_file'];
#	$original_file = p3dlite_get_original($price_request['model_file']);
	$original_file = $price_request['model_file'];
	$link = $upload_dir['baseurl'] ."/p3d/". rawurlencode($price_request['model_file']) ;
	$file_url = "<a href='".$link."'>".p3dlite_basename( $price_request['model_file'] )."</a>";

?>
				<hr>
				<h3><?php echo '#'.$price_request['id'];?></h3>
				<div>
				<table id="price_request-<?php echo $price_request['id'];?>" data-id="<?php echo $price_request['id'];?>" class="form-table price_request">
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Status', '3dprint-lite' );?></th>
						<td>
							<?php 
							$request_status = $price_request['status'];

							switch ($request_status) {
								case "0":
									echo '<span class="p3dlite-request-received">'.__('Request received', '3dprint-lite').'</span>';
								break;
								case "1":
									echo '<span class="p3dlite-quote-sent">'.__('Quote sent', '3dprint-lite').'</span>';
								break;
								case "2":
									echo '<span class="p3dlite-order-placed">'.__('Order placed', '3dprint-lite').'</span>';
								break;
								case "3":
									echo '<span class="p3dlite-failed-email">'.__('Failed to send out e-mail', '3dprint-lite').'</span>';
								break;
							}
							?>
						</td>
					</tr>
					<?php if ($price_request['buynow_link']) { ?>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Buy Now URL', '3dprint-lite' );?></th>
						<td>
							<a href="<?php echo $price_request['buynow_link'];?>">Link</a>
						</td>
					</tr>
					<?php } ?>
					<?php if ($price_request['thumbnail_url']) { ?>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Thumbnail', '3dprint-lite' );?></th>
						<td>
							<a href="<?php echo $price_request['thumbnail_url'];?>"><img class="p3dlite-thumb" src="<?php echo $price_request['thumbnail_url'];?>"></a>
						</td>
					</tr>
					<?php } ?>

				 	<tr valign="top">
						<th scope="row"><?php _e( 'Model File', '3dprint-lite' );?></th>
						<td>
							<?php echo $file_url;?>
						</td>
					</tr>


				 	<tr valign="top">
						<th scope="row"><?php _e( 'Quantity', '3dprint-lite' );?></th>
						<td>
							<?php #echo $price_request['quantity'];?>
							<input type="number" step="1" min="0" name="p3dlite_quantity[<?php echo (int)$price_request['id'];?>]" value="<?php echo $price_request['quantity'];?>">
						</td>
					</tr>



				 	<tr valign="top">
						<th scope="row"><?php _e( 'Printer', '3dprint-lite' );?></th>
						<td>
							<?php echo $price_request['printer'];?>
						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Material', '3dprint-lite' );?></th>
						<td>
							<?php echo $price_request['material'];?>
						</td>
					</tr>
					<?php if ($price_request['coating']) { ?>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Coating', '3dprint-lite' );?></th>
						<td>
							<?php echo $price_request['coating'];?>
						</td>
					</tr>
					<?php } ?>
					<?php if ($price_request['infill']) { ?>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Infill', '3dprint-lite' );?></th>
						<td>
							<?php echo $price_request['infill'];?> %
						</td>
					</tr>
					<?php } ?>

				 	<tr valign="top">
						<th scope="row"><?php _e( 'Model Length', '3dprint-lite' );?></th>
						<td>
							<input type="number" step="any" min="0" name="p3dlite_length[<?php echo (int)$price_request['id'];?>]" value="<?php echo $price_request['scale_x'];?>"><?php _e('cm', '3dprint-lite'); ?>
						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Model Width', '3dprint-lite' );?></th>
						<td>
							<input type="number" step="any" min="0" name="p3dlite_width[<?php echo (int)$price_request['id'];?>]" value="<?php echo $price_request['scale_y'];?>"><?php _e('cm', '3dprint-lite'); ?>
						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Model Height', '3dprint-lite' );?></th>
						<td>
							<input type="number" step="any" min="0" name="p3dlite_height[<?php echo (int)$price_request['id'];?>]" value="<?php echo $price_request['scale_z'];?>"><?php _e('cm', '3dprint-lite'); ?>
						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Dimensions', '3dprint-lite' );?></th>
						<td>
							<?php echo $price_request['scale_x']." &times; ".$price_request['scale_y']." &times; ".$price_request['scale_z']." ".__('cm', '3dprint-lite');?> 
						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Resize Scale', '3dprint-lite' );?></th>
						<td>
							<?php echo $price_request['scale'];?> 
						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Unit', '3dprint-lite' );?></th>
						<td>
							<?php echo $price_request['unit'];?> 
						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Weight', '3dprint-lite' );?></th>
						<td>
							<input type="number" step="any" min="0" name="p3dlite_weight[<?php echo (int)$price_request['id'];?>]" value="<?php echo $price_request['weight'];?>"><?php _e('g', '3dprint-lite'); ?>
						</td>
					</tr>



					<tr valign="top">
						<th scope="row">
							<?php _e( 'Customer E-mail', '3dprint-lite' ); ?>
						</th>
						<td>
							<?php echo $price_request['email'];?>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row">
							<?php _e( 'Customer Notes', '3dprint-lite' ); ?>
						</th>
						<td>
							<?php echo $price_request['request_comment'];?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Admin Notes', '3dprint-lite' ); ?>
						</th>
						<td>
							<textarea style="resize: both;" name="p3dlite_comments[<?php echo (int)$price_request['id'];?>]"><?php echo $price_request['admin_comment'];?></textarea>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Estimated Unit Price', '3dprint-lite' ); ?>
						</th>
						<td>
							<?php echo p3dlite_format_price($price_request['estimated_price'], $settings['currency'], $settings['currency_position']);?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Estimated Total Price', '3dprint-lite' ); ?>
						</th>
						<td>
							<?php echo p3dlite_format_price ($price_request['estimated_price'] * $price_request['quantity'], $settings['currency'], $settings['currency_position']);?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Set Unit Price', '3dprint-lite' ); ?>
						</th>
						<td>

							<input class="p3dlite-price" type="number" step="0.01" name="p3dlite_buynow[<?php echo (int)$price_request['id'];?>]" value="<?php echo $price_request['price'];?>"><?php echo $settings['currency'];?>
						</td>
					</tr>
				</table>
				</div>

				<br style="clear:both">

<?php

				$db_printers=p3dlite_get_option( 'p3dlite_printers' );
				$db_infills=p3dlite_get_option( 'p3dlite_infills' );
				$db_materials=p3dlite_get_option( 'p3dlite_materials' );

				uasort($db_printers, function($a, $b) {
					return strcmp($a['name'], $b['name']);
				});
?>
				<div class="p3dlite-reslice">
					<button disabled type="button" onclick="jQuery(this).closest('.p3dlite-reslice').find('.p3dlite-reslice-options').slideToggle();"><?php _e('Re-Slice Options', '3dprint-lite');?></button>
					<br style="clear:both">
					<div class="p3dlite-reslice-options" style="display:none;padding-top:20px;">
						<input type="hidden" class="p3dlite-unit" value="<?php echo $price_request['unit']; ?>">
						<input type="hidden" class="p3dlite-resize-scale" value="<?php echo $price_request['scale']; ?>">
						<input type="hidden" class="p3dlite-product-id" value="<?php echo $price_request['product_id']; ?>">
						<input type="hidden" class="p3dlite-request-id" value="<?php echo $price_request['id']; ?>">
						<b><?php _e('Printer', '3dprint-lite');?>:</b><br>
						<select class="p3dlite-reslice-printer">
						<?php 
							$default_infill = false;
							foreach ($db_printers as $printer) {
								if (!$default_infill) $default_infill = $printer['default_infill'];
								echo '<option data-type="'.$printer['type'].'" value="'.(int)$printer['id'].'">#'.(int)$printer['id'].' '.esc_html($printer['name']).'</option>';
							}
						?>
						</select>
						<br>
						<b><?php _e('Infill', '3dprint-lite');?>:</b><br>
						<select class="p3dlite-reslice-infill">
						<?php 
							foreach ($db_infills as $infill) {
								if ($infill['infill']==$default_infill) $selected = 'selected'; else $selected = '';
								echo '<option '.$selected.' value="'.(int)$infill['infill'].'">'.esc_html($infill['name']).'</option>';
							}
						?>
						</select>
						<br>
						<b><?php _e('Material', '3dprint-lite');?>:</b><br>

						<select class="p3dlite-reslice-material">
						<?php 
							foreach ($db_materials as $material) {
								if ($material['id']==$price_request['material_id']) $selected = 'selected';
								else $selected = '';
								echo '<option '.$selected.' data-type="'.$material['type'].'" value="'.(int)$material['id'].'">#'.(int)$material['id'].' '.esc_html($material['name']).'</option>';
							}
						?>
						</select>

					<!-- todo  Slicer option?  -->
					<p class="submit">
						<button type="button" class="button-secondary" onclick="p3dAnalyseModel('<?php echo basename($filepath); ?>', this);"><?php _e('Re-Slice', '3dprint-lite');?></button>&nbsp;
						<span style="display:none;" class="p3dlite-analyse-status"><?php _e('Analysing', '3dprint-lite');?></span>&nbsp;<span class="p3dlite-analyse-percent"></span>
					</p>
					<p>
						<span class="p3dlite-console"></span>
					</p>
					</div>

				</div>
<?php
} //foreach $price_requests
?>

							<div id="p3dlite-price-request-totals">
							<?php

								$estimated_total = 0;
								$set_total = 0;
								foreach ($price_requests as $price_request) {
									$currency_rate = 1;
									if (isset($price_request['estimated_price_currency']) && strlen($price_request['estimated_price_currency'])) {
										$currency_rate = p3dlite_get_currency_rate($price_request['estimated_price_currency']);
									}

									$estimated_total+=$price_request['estimated_price'] * $price_request['quantity'] * $currency_rate;
									$set_total+=$price_request['price'] * $price_request['quantity'] * $currency_rate;
								}

								$estimated_total_html = p3dlite_format_price($estimated_total, $settings['currency'], $settings['currency_position']);
								$set_total_html = p3dlite_format_price($set_total, $settings['currency'], $settings['currency_position']);

								echo "<p class=\"p3dlite-totals\">".__('Estimated Price Total', '3dprint-lite').": $estimated_total_html </b>";
								echo "<p class=\"p3dlite-totals\">".__('Set Price Total' ,'3dprint-lite').": $set_total_html </b>";
							?>
							</div>

				<p class="submit">
					<input type="submit" class="button-secondary" onclick="document.getElementById('p3dlite_action').value='save'" value="<?php _e( 'Save', '3dprint-lite' ) ?>" />&nbsp;
					<input type="submit" class="button-primary" value="<?php _e( 'Save & Notify Customer', '3dprint-lite' ) ?>" />
				</p>
	</form>