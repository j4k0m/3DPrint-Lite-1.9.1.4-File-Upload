<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( isset($_POST['action']) && $_POST['action']=='update' && isset( $_POST['p3dlite_coating_name'] ) && count( $_POST['p3dlite_coating_name'] )>0 ) {
		$coatings = array();
		foreach ( $_POST['p3dlite_coating_name'] as $i => $coating ) {
			if (empty($_POST['p3dlite_coating_name'][$i])) continue;
			$coatings[$i]['id']=$i;
			$coatings[$i]['name']=sanitize_text_field( $_POST['p3dlite_coating_name'][$i] );
			$coatings[$i]['description']=sanitize_text_field( $_POST['p3dlite_coating_description'][$i] );
			$coatings[$i]['photo']=sanitize_text_field( $_POST['p3dlite_coating_photo'][$i] );
			$coatings[$i]['price']= (strlen(sanitize_text_field($_POST['p3dlite_coating_price'][$i])) ? sanitize_text_field($_POST['p3dlite_coating_price'][$i]) : 0);
			$coatings[$i]['price_type']=sanitize_text_field($_POST['p3dlite_coating_price_type'][$i]);
			$coatings[$i]['price1']= (strlen(sanitize_text_field($_POST['p3dlite_coating_price1'][$i])) ? sanitize_text_field($_POST['p3dlite_coating_price1'][$i]) : 0);
			$coatings[$i]['price_type1']=sanitize_text_field($_POST['p3dlite_coating_price_type1'][$i]);
			$coatings[$i]['color']=sanitize_text_field($_POST['p3dlite_coating_color'][$i]);
			$coatings[$i]['shininess']=sanitize_text_field($_POST['p3dlite_coating_shininess'][$i]);
			$coatings[$i]['glow']=(int)$_POST['p3dlite_coating_glow'][$i];
			$coatings[$i]['transparency']=sanitize_text_field($_POST['p3dlite_coating_transparency'][$i]);


			if ( isset($_POST['p3dlite_coating_materials']) && count( $_POST['p3dlite_coating_materials'][$i] )>0 ) {

				$coatings[$i]['materials']=implode(',', $_POST['p3dlite_coating_materials'][$i]);
			}

			if (isset($_FILES['p3dlite_coating_photo_upload']['tmp_name'][$i]) && strlen($_FILES['p3dlite_coating_photo_upload']['tmp_name'][$i])>0) {

				$uploaded_file = p3dlite_upload_file('p3dlite_coating_photo_upload', $i);
				$coatings[$i]['photo']=str_replace('http:','',$uploaded_file['url']);
			}


		}
		foreach ($coatings as $coating) {
			p3dlite_update_option( 'p3dlite_coatings', $coating );
		}
#		wp_redirect( admin_url( 'admin.php?page=p3dlite_coatings' ) );
	}

#	$group_names = $wpdb->get_results( "select distinct (group_name) from {$wpdb->prefix}p3d_coatings", 'ARRAY_A' );
#	$groups = array();

?>

	<form method="post" action="admin.php?page=p3dlite_coatings&action=edit&coating=<?php echo (int)$_GET['coating']?>" enctype="multipart/form-data">
				<input type="hidden" name="action" value="update" />
				<br style="clear:both">
				<button class="button-secondary" type="button" onclick="location.href='<?php echo admin_url( 'admin.php?page=p3dlite_coatings' );?>'"><b>&#8592;<?php _e('Back to coatings', '3dprint-lite');?></b></button>
				<h3><?php echo '#'.$coating['id'].' '.$coating['name'];?></h3>
				<div>

				<table id="coating-<?php echo $coating['id'];?>" class="form-table coating">
					<tr>
						<td colspan="2"><hr></td>
					</tr>
				 	<tr>
						<td colspan="2"><span class="item_id"><?php echo "<b>ID #".$coating['id']."</b>";?></span></td>
				 	</tr>
				 	<tr valign="top">
					<th scope="row"><?php _e( 'Coating Name', '3dprint-lite' );?></th>
						<td>
							<input type="text" name="p3dlite_coating_name[<?php echo $coating['id'];?>]" value="<?php echo $coating['name'];?>" />&nbsp;

						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Coating Description', '3dprint-lite' ); ?>
						</th>
						<td>
							<textarea name="p3dlite_coating_description[<?php echo $coating['id'];?>]"/><?php if (isset($coating['description'])) echo $coating['description'];?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Photo', '3dprint-lite' );?></th>
						<td>
						<?php
						if (isset($coating['photo'])) {
						?>
							<a href="<?php echo $coating['photo'];?>"><img class="p3dlite-preview" src="<?php echo esc_url($coating['photo']);?>"></a>
						<?php
						}
						?>
							<input type="text" name="p3dlite_coating_photo[<?php echo $coating['id'];?>]" value="<?php if (isset($coating['photo'])) echo esc_url($coating['photo']);?>" />
							<input type="file" name="p3dlite_coating_photo_upload[<?php echo $coating['id'];?>]" accept="image/*">
						</td>

					</tr>

					<tr class="coating_materials" valign="top">
						<th scope="row"><?php _e( 'Materials', '3dprint-lite' ); ?></th>
						<td>

							<select autocomplete="off" name="p3dlite_coating_materials[<?php echo $coating['id'];?>][]" multiple="multiple" class="sumoselect">
								<?php 

									foreach ($materials as $material) {
										$j = $material['id'];
										if (isset($coating['materials'])  && strlen($coating['materials']) && in_array($j, explode(',',$coating['materials']))) $selected="selected"; else $selected="";
										echo '<option '.$selected.' value="'.$j.'">'.$material['name'];
									}
								?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_coating_price[<?php echo $coating['id'];?>]" value="<?php echo $coating['price'];?>" /><?php echo $settings['currency']; ?> <?php _e('per', '3dprint-lite');?> 
							<select name="p3dlite_coating_price_type[<?php echo $coating['id'];?>]">
								<option <?php if ($coating['price_type']=='cm2') echo 'selected'; ?> value="cm2"><?php _e('cm2 of surface area', '3dprint-lite');?></option>
								<option <?php if ($coating['price_type']=='fixed') echo 'selected'; ?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>
								<option disabled><?php _e('+% to total price (Available in Premium version)', '3dprint-lite');?></option>
							</select>

						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Extra Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_coating_price1[<?php echo $coating['id'];?>]" value="<?php echo $coating['price1'];?>" /><?php echo $settings['currency']; ?> <?php _e('per', '3dprint-lite');?> 
							<select name="p3dlite_coating_price_type1[<?php echo $coating['id'];?>]">
								<option <?php if ($coating['price_type1']=='cm2') echo 'selected'; ?> value="cm2"><?php _e('cm2 of surface area', '3dprint-lite');?></option>
								<option <?php if ($coating['price_type1']=='fixed') echo 'selected'; ?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>
								<option disabled><?php _e('+% to total price (Available in Premium version)', '3dprint-lite');?></option>
							</select>

						</td>
					</tr>


					<tr valign="top">
						<th scope="row"><?php _e( 'Coating Color', '3dprint-lite' );?></th>
						<td class="color_td"><input type="text" class="p3dlite_color_picker" name="p3dlite_coating_color[<?php echo $coating['id'];?>]" value="<?php echo $coating['color'];?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Coating Shininess', '3dprint-lite' );?></th>
						<td>
							<select class="p3dlite_price_type"  name="p3dlite_coating_shininess[<?php echo $coating['id'];?>]">
								<option <?php if ( $coating['shininess']=='none') echo "selected";?> value="none"><?php _e('None', '3dprint-lite');?></option>
								<option <?php if ( $coating['shininess']=='plastic') echo "selected";?> value="plastic"><?php _e('Plastic', '3dprint-lite');?></option>
								<option <?php if ( $coating['shininess']=='wood' ) echo "selected";?> value="wood"><?php _e('Wood', '3dprint-lite');?></option>
								<option <?php if ( $coating['shininess']=='metal' ) echo "selected";?> value="metal"><?php _e('Metal', '3dprint-lite');?></option>
							</select>
						</td>

					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Coating transparency', '3dprint-lite' );?></th>
						<td>
							<select class="p3dlite_price_type"  name="p3dlite_coating_transparency[<?php echo $coating['id'];?>]">
								<option <?php if ( $coating['transparency']=='none') echo "selected";?> value="none"><?php _e('None', '3dprint-lite');?></option>
								<option <?php if ( $coating['transparency']=='opaque') echo "selected";?> value="opaque"><?php _e('Opaque', '3dprint-lite');?></option>
							</select>
						</td>

					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Coating Glow', '3dprint-lite' );?></th>
						<td>
							<select name="p3dlite_coating_glow[<?php echo $coating['id'];?>]">
								<option <?php if ( $coating['glow']=='0') echo "selected";?> value="0"><?php _e('No', '3dprint-lite');?></option>
								<option <?php if ( $coating['glow']=='1' ) echo "selected";?> value="1"><?php _e('Yes', '3dprint-lite');?></option>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Group Name', '3dprint-lite' ); ?></th>
						<td><input type="text" disabled />
							<?php
								_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
							?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Sort Order', '3dprint-lite' ); ?></th>
						<td><input type="text" disabled />
							<?php
								_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
							?>
						</td>
					</tr>


				</table>

				</div>

				<br style="clear:both">

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', '3dprint-lite' ) ?>" />
				</p>

	</form>