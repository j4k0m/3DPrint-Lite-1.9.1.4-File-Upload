	<div <?php if ($settings['show_materials']!='on') echo 'style="display:none;"';?> class="p3dlite-info">
		<fieldset id="material_fieldset" class="p3dlite-fieldset">
			<legend><?php _e( 'Material', '3dprint-lite' );?></legend>
			<ul class="p3dlite-list">
<?php
		foreach ( $db_materials as $db_material ) {
			$i = $db_material['id'];
			if (!in_array($i, $assigned_materials)) continue;
			echo '<li class="p3dlite-tooltip '.($db_material['photo'] ? 'p3dlite-li-photo' : '').'" data-tooltip-content="#p3dlite-tooltip-material-'.$i.'" data-color=\''.$db_material['color'].'\' data-shininess=\''.(isset($db_material['shininess']) ? $db_material['shininess'] : 'plastic').'\' data-glow=\''.(isset($db_material['glow']) ? $db_material['glow'] : '0').'\' data-transparency=\''.(isset($db_material['transparency']) ? $db_material['transparency'] : 'opaque').'\' data-name="'.esc_attr( $db_material['name'] ).'" onclick="p3dliteSelectFilament(this);"><input id="p3dlite_material_'.$i.'" class="p3dlite-control" autocomplete="off" type="radio" data-id="'.$i.'" data-density="'.esc_attr( $db_material['density'] ).'" data-price="'.esc_attr( $db_material['price'] ).'" data-price_type="'.$db_material['price_type'].'" data-price1="'.esc_attr( $db_material['price1'] ).'" data-price_type1="'.$db_material['price_type1'].'" data-price2="'.esc_attr( $db_material['price2'] ).'" data-price_type2="'.$db_material['price_type2'].'"  name="product_filament" ><div style="background-color:'.$db_material['color'].'" class="color-sample"></div>'.__($db_material['name'], '3dprint-lite').'</li>';
		}
?>
			</ul>
		</fieldset>
	</div>