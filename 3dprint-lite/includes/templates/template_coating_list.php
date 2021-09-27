<?php 
if ($db_coatings && count($db_coatings)>0) {
?>
	<div <?php if ($settings['show_coatings']!='on') echo 'style="display:none;"';?> class="p3dlite-info">
		<fieldset id="coating_fieldset" class="p3dlite-fieldset">
			<legend><?php _e( 'Coating', '3dprint-lite' );?></legend>
			<ul class="p3dlite-list">
<?php
		foreach ( $db_coatings as $db_coating ) {
			$i = $db_coating['id'];
			echo '<li class="p3dlite-tooltip '.($db_coating['photo'] ? 'p3dlite-li-photo' : '').'" data-tooltip-content="#p3dlite-tooltip-coating-'.$i.'" data-color=\''.$db_coating['color'].'\' data-shininess=\''.(isset($db_coating['shininess']) ? $db_coating['shininess'] : 'none').'\' data-glow=\''.(isset($db_coating['glow']) ? $db_coating['glow'] : '0').'\' data-transparency=\''.(isset($db_coating['transparency']) ? $db_coating['transparency'] : 'none').'\' data-name="'.esc_attr( $db_coating['name'] ).'" onclick="p3dliteSelectCoating(this);"><input id="p3dlite_coating_'.$i.'" class="p3dlite-control" autocomplete="off" type="radio" data-id="'.$i.'"  data-materials="'.(isset($db_coating['materials']) && strlen($db_coating['materials']) ? $db_coating['materials'] : '').'" data-price="'.esc_attr( $db_coating['price'] ).'" data-price_type="'.esc_attr( $db_coating['price_type'] ).'" data-price1="'.esc_attr( $db_coating['price1'] ).'" data-price_type1="'.esc_attr( $db_coating['price_type1'] ).'" name="product_coating" ><div style="background-color:'.$db_coating['color'].'" class="color-sample"></div>'.__($db_coating['name'], '3dprint-lite').'</li>';
		}
?>
			</ul>
		</fieldset>
	</div>
<?php
}
?>