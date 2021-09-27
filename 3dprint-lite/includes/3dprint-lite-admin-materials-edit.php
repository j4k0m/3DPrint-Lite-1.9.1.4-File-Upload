<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

	if ( isset($_POST['action']) && $_POST['action']=='update' && isset( $_POST['p3dlite_material_name'] ) && count( $_POST['p3dlite_material_name'] )>0 ) {
		$materials = array();
//		for ( $i=0;$i<count( $_POST['p3dlite_material_name'] );$i++ ) {
		foreach ( $_POST['p3dlite_material_name'] as $i => $material ) {

			if (strlen($_POST['p3dlite_material_name'][$i])==0) continue;
			$materials[$i]['id']=$i;
			$materials[$i]['density']=$_POST['p3dlite_material_density'][$i];
			$materials[$i]['name']=sanitize_text_field( $_POST['p3dlite_material_name'][$i] );
			$materials[$i]['description']=sanitize_text_field( $_POST['p3dlite_material_description'][$i] );
			$materials[$i]['photo']=sanitize_text_field( $_POST['p3dlite_material_photo'][$i] );
			$materials[$i]['type'] = sanitize_text_field($_POST['p3dlite_material_type'][$i] );
			$materials[$i]['diameter']=(float)( $_POST['p3dlite_material_diameter'][$i] );
			$materials[$i]['length']=(float)( $_POST['p3dlite_material_length'][$i] );
			$materials[$i]['weight']=(float)( $_POST['p3dlite_material_weight'][$i] );
			$materials[$i]['price']=(strlen(sanitize_text_field($_POST['p3dlite_material_price'][$i])) ? sanitize_text_field($_POST['p3dlite_material_price'][$i]) : 0);
			$materials[$i]['price_type']=sanitize_text_field($_POST['p3dlite_material_price_type'][$i]);
			$materials[$i]['price1']=(strlen(sanitize_text_field($_POST['p3dlite_material_price1'][$i])) ? sanitize_text_field($_POST['p3dlite_material_price1'][$i]) : 0);
			$materials[$i]['price_type1']=sanitize_text_field($_POST['p3dlite_material_price_type1'][$i]);
			$materials[$i]['price2']=(strlen(sanitize_text_field($_POST['p3dlite_material_price2'][$i])) ? sanitize_text_field($_POST['p3dlite_material_price2'][$i]) : 0);
			$materials[$i]['price_type2']=sanitize_text_field($_POST['p3dlite_material_price_type2'][$i]);
			$materials[$i]['roll_price']=(float)( $_POST['p3dlite_material_roll_price'][$i] );
			$materials[$i]['color']=sanitize_text_field($_POST['p3dlite_material_color'][$i]);
			$materials[$i]['shininess']=sanitize_text_field($_POST['p3dlite_material_shininess'][$i]);
			$materials[$i]['transparency']=sanitize_text_field($_POST['p3dlite_material_transparency'][$i]);
			$materials[$i]['glow']=(int)$_POST['p3dlite_material_glow'][$i];

			if (isset($_FILES['p3dlite_material_photo_upload']['tmp_name'][$i]) && strlen($_FILES['p3dlite_material_photo_upload']['tmp_name'][$i])>0) {

				$uploaded_file = p3dlite_upload_file('p3dlite_material_photo_upload', $i);
				$materials[$i]['photo']=str_replace('http:','',$uploaded_file['url']);
			}

		}

		foreach ($materials as $material) {

			p3dlite_update_option( 'p3dlite_materials', $material );
		}
#		wp_redirect( admin_url( 'admin.php?page=3dprint_materials' ) );
	}



?>
<script language="javascript">

function p3dliteCalculateFilamentPrice(material_obj) {
	var diameter=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_diameter').val());
	var length=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_length').val());
	var weight=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_weight').val());
	var price=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_roll_price').val());
	var price_type=jQuery(material_obj).closest('table.material').find('select.p3dlite_price_type').val();

	if (price_type=='cm3') {
		if (!diameter || !price || !length) {alert('<?php _e( 'Please input roll price, diameter and length', '3dprint-lite' );?>');return false;}
		var volume=(Math.PI*((diameter*diameter)/4)*(length*1000))/1000;
		var volume_cost=price/volume;
		jQuery(material_obj).closest('table.material').find('input.p3dlite_price').val(volume_cost.toFixed(2));
	}
	else if (price_type=='gram') {

		if (!weight || !price) {alert('<?php _e( 'Please input price and weight', '3dprint-lite' );?>');return false;}
		var weight_cost=price/(weight*1000);
		jQuery(material_obj).closest('table.material').find('input.p3dlite_price').val(weight_cost.toFixed(2));
	}

}

function p3dliteCalculateFilamentDensity(material_obj) {
	var diameter=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_diameter').val());
	var length=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_length').val());
	var weight=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_weight').val());

	if (!diameter || !weight || !length) {alert('<?php _e( 'Please input diameter, length and weight', '3dprint-lite' );?>');return false;}
	var density = parseFloat( ( weight*1000 )/( Math.PI*( Math.pow( diameter, 2 )/4 )*length ) ).toFixed(2);
	jQuery(material_obj).closest('table.material').find('input[name^=p3dlite_material_density]').val(density);
}

</script>

	<form method="post" action="admin.php?page=p3dlite_materials&action=edit&material=<?php echo (int)$_GET['material']?>" enctype="multipart/form-data">
				<input type="hidden" name="action" value="update" />
				<br style="clear:both">
				<button class="button-secondary" type="button" onclick="location.href='<?php echo admin_url( 'admin.php?page=p3dlite_materials' );?>'"><b>&#8592;<?php _e('Back to materials', '3dprint-lite');?></b></button>
				<h3><?php echo '#'.$material['id'].' '.$material['name'];?></h3>
				<div>
				<table id="material-<?php echo $material['id'];?>" class="form-table material">
					<tr>
						<td colspan="2"><hr></td>
					</tr>
				 	<tr>
						<td colspan="2"><span class="item_id"><?php echo "<b>ID #".$material['id']."</b>";?></span></td>
				 	</tr>
				 	<tr valign="top">
					<th scope="row"><?php _e( 'Material Name', '3dprint-lite' );?></th>
						<td>
							<input type="text" name="p3dlite_material_name[<?php echo $material['id'];?>]" value="<?php echo $material['name'];?>" />&nbsp;

						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Material Description', '3dprint-lite' ); ?>
						</th>
						<td>
							<textarea name="p3dlite_material_description[<?php echo $material['id'];?>]"/><?php if (isset($material['description'])) echo $material['description'];?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Photo', '3dprint-lite' );?></th>
						<td>
						<?php
						if (isset($material['photo'])) {
						?>
							<a href="<?php echo $material['photo'];?>"><img class="p3dlite-preview" src="<?php echo esc_url($material['photo']);?>"></a>
						<?php
						}
						?>
							<input type="text" name="p3dlite_material_photo[<?php echo $material['id'];?>]" value="<?php if (isset($material['photo'])) echo esc_url($material['photo']);?>" />
							<input type="file" name="p3dlite_material_photo_upload[<?php echo $material['id'];?>]" accept="image/*">
						</td>

					</tr>



				 	<tr valign="top">
						<th scope="row"><?php _e( 'Material Type', '3dprint-lite' );?></th>
						<td>
							<select class="select_material" name="p3dlite_material_type[<?php echo $material['id'];?>]" onchange="p3dliteSetMaterialType(this)">
								<option <?php if ( $material['type']=='filament' ) echo "selected";?> value="filament"><?php _e( 'Filament', '3dprint-lite' );?>
								<option <?php if ( $material['type']=='other' ) echo "selected";?> value="other"><?php _e( 'Other', '3dprint-lite' );?>
								<option disabled><?php _e( 'Laser Cutting Workpiece (available in Premium version)', '3dprint-lite' );?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_material_price[<?php echo $material['id'];?>]" value="<?php echo $material['price'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select class="p3dlite_price_type"  name="p3dlite_material_price_type[<?php echo $material['id'];?>]">
								<option <?php if ( $material['price_type']=='cm3' ) echo "selected";?> value="cm3"><?php _e( '1 cm3', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type']=='removed_material_volume' ) echo "selected";?> value="removed_material_volume"><?php _e( '1 cm3 of Removed Material Volume (bounding box volume - material volume)', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type']=='fixed' ) echo "selected";?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>

								<option disabled><?php _e('1 cm2 of surface area (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm2 of Bounding Box XY (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm3 of Support Material Volume (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('Support Material Removal Fixed Charge (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm3 of Removed Material Volume (bounding box volume - material volume) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm of Laser Cutting Total Path (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 gram of volumetric weight (higher value of actual and volumetric weight) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 Hour (Analyse API required) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('+% to total price (Available in Premium version)', '3dprint-lite');?></option>
							</select>
							<a class="material_filament" onclick="javascript:p3dliteCalculateFilamentPrice(this)" href="javascript:void(0)"><?php _e( 'Calculate', '3dprint-lite' );?></a>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Extra Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_material_price1[<?php echo $material['id'];?>]" value="<?php echo $material['price1'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select class="p3dlite_price_type"  name="p3dlite_material_price_type1[<?php echo $material['id'];?>]">
								<option <?php if ( $material['price_type1']=='cm3' ) echo "selected";?> value="cm3"><?php _e( '1 cm3', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type1']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type1']=='removed_material_volume' ) echo "selected";?> value="removed_material_volume"><?php _e( '1 cm3 of Removed Material Volume (bounding box volume - material volume)', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type1']=='fixed' ) echo "selected";?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>

								<option disabled><?php _e('1 cm2 of surface area (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm2 of Bounding Box XY (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm3 of Support Material Volume (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('Support Material Removal Fixed Charge (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm3 of Removed Material Volume (bounding box volume - material volume) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm of Laser Cutting Total Path (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 gram of volumetric weight (higher value of actual and volumetric weight) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 Hour (Analyse API required) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('+% to total price (Available in Premium version)', '3dprint-lite');?></option>
							</select>
							<a class="material_filament" onclick="javascript:p3dliteCalculateFilamentPrice(this)" href="javascript:void(0)"><?php _e( 'Calculate', '3dprint-lite' );?></a>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Extra Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_material_price2[<?php echo $material['id'];?>]" value="<?php echo $material['price2'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select class="p3dlite_price_type"  name="p3dlite_material_price_type2[<?php echo $material['id'];?>]">
								<option <?php if ( $material['price_type2']=='cm3' ) echo "selected";?> value="cm3"><?php _e( '1 cm3', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type2']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type2']=='removed_material_volume' ) echo "selected";?> value="removed_material_volume"><?php _e( '1 cm3 of Removed Material Volume (bounding box volume - material volume)', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type2']=='fixed' ) echo "selected";?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>

							</select>
							<a class="material_filament" onclick="javascript:p3dliteCalculateFilamentPrice(this)" href="javascript:void(0)"><?php _e( 'Calculate', '3dprint-lite' );?></a>
						</td>
					</tr>



					<tr valign="top">
						<th scope="row"><?php _e( 'Material Density', '3dprint-lite' );?></th>
						<td>
							<input type="text" name="p3dlite_material_density[<?php echo $material['id'];?>]" value="<?php echo $material['density'];?>" /><?php _e( 'g/cm3', '3dprint-lite' );?>
							<a class="material_filament" onclick="javascript:p3dliteCalculateFilamentDensity(this)" href="javascript:void(0)"><?php _e( 'Calculate', '3dprint-lite' );?></a>
						</td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Filament Diameter', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_diameter" name="p3dlite_material_diameter[<?php echo $material['id'];?>]" value="<?php echo $material['diameter'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Filament Length', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_length" name="p3dlite_material_length[<?php echo $material['id'];?>]" value="<?php echo $material['length'];?>" /><?php _e( 'm', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Roll Weight', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_weight" name="p3dlite_material_weight[<?php echo $material['id'];?>]" value="<?php echo $material['weight'];?>" /><?php _e( 'kg', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Roll Price', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_roll_price" name="p3dlite_material_roll_price[<?php echo $material['id'];?>]" value="<?php echo $material['roll_price'];?>" /><?php echo $settings['currency']; ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Material Color', '3dprint-lite' );?></th>
						<td class="color_td"><input type="text" class="p3dlite_color_picker" name="p3dlite_material_color[<?php echo $material['id'];?>]" value="<?php echo $material['color'];?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Material Shininess', '3dprint-lite' );?></th>
						<td>
							<select name="p3dlite_material_shininess[<?php echo $material['id'];?>]">
								<option <?php if ( $material['shininess']=='plastic') echo "selected";?> value="plastic"><?php _e('Plastic', '3dprint-lite');?></option>
								<option <?php if ( $material['shininess']=='wood' ) echo "selected";?> value="wood"><?php _e('Wood', '3dprint-lite');?></option>
								<option <?php if ( $material['shininess']=='metal' ) echo "selected";?> value="metal"><?php _e('Metal', '3dprint-lite');?></option>
							</select>
						</td>

					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Material Glow', '3dprint-lite' );?></th>
						<td>
							<select name="p3dlite_material_glow[<?php echo $material['id'];?>]">
								<option <?php if ( $material['glow']=='0') echo "selected";?> value="0"><?php _e('No', '3dprint-lite');?></option>
								<option <?php if ( $material['glow']=='1' ) echo "selected";?> value="1"><?php _e('Yes', '3dprint-lite');?></option>
							</select>
						</td>

					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Material Transparency', '3dprint-lite' );?></th>
						<td>
							<select name="p3dlite_material_transparency[<?php echo $material['id'];?>]">
								<option <?php if ( $material['transparency']=='opaque') echo "selected";?> value="opaque"><?php _e('Opaque', '3dprint-lite');?></option>
								<option <?php if ( $material['transparency']=='resin' ) echo "selected";?> value="resin"><?php _e('Resin', '3dprint-lite');?></option>
								<option <?php if ( $material['transparency']=='glass' ) echo "selected";?> value="glass"><?php _e('Glass', '3dprint-lite');?></option>
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


				</table>				</div>

				<br style="clear:both">

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', '3dprint-lite' ) ?>" />
				</p>

	</form>