	<div <?php if ($settings['show_printers']!='on') echo 'style="display:none;"';?> class="p3dlite-info">
		<fieldset id="printer_fieldset" class="p3dlite-fieldset">
			<legend><?php _e( 'Printer', '3dprint-lite' );?></legend>
			<ul class="p3dlite-list">
<?php
		foreach ( $db_printers as $db_printer ) {
			$i = $db_printer['id'];
			echo '<li class="p3dlite-tooltip '.($db_printer['photo'] ? 'p3dlite-li-photo' : '').'" data-tooltip-content="#p3dlite-tooltip-printer-'.$i.'" onclick="p3dliteSelectPrinter(this);" data-name="'.esc_attr( $db_printer['name'] ).'"><input id="p3dlite_printer_'.$i.'" class="p3dlite-control" autocomplete="off" data-full_color="'.esc_attr( isset($db_printer['full_color']) ? $db_printer['full_color'] : '1' ).'" data-platform_shape="'.esc_attr( isset($db_printer['platform_shape']) ? $db_printer['platform_shape'] : 'rectangle' ).'" data-diameter="'.(float)$db_printer['diameter'].'" data-width="'.(float)$db_printer['width'].'" data-length="'.(float)$db_printer['length'].'" data-height="'.(float)$db_printer['height'].'" data-min_side="'.(float)$db_printer['min_side'].'" data-id="'.$i.'" data-materials="'.(strlen($db_printer['materials']) ?  $db_printer['materials'] : '').'" data-price="'.esc_attr( $db_printer['price'] ).'" data-price_type="'.$db_printer['price_type'].'" data-price1="'.esc_attr( $db_printer['price1'] ).'" data-price_type1="'.$db_printer['price_type1'].'" data-price2="'.esc_attr( $db_printer['price2'] ).'" data-price_type2="'.$db_printer['price_type2'].'" data-price3="'.esc_attr( $db_printer['price3'] ).'" data-price_type3="'.$db_printer['price_type3'].'" type="radio" name="product_printer">'.__($db_printer['name'], '3dprint-lite').'</li>';
		}
?>
		  	</ul>
	  	</fieldset>
	</div>