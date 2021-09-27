<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	if ( isset($_POST['action']) && $_POST['action']=='update' && isset( $_POST['p3dlite_printer_name'] ) && count( $_POST['p3dlite_printer_name'] )>0 ) {
		$printers = array();
		foreach ( $_POST['p3dlite_printer_name'] as $i => $printer ) {
			if (strlen($_POST['p3dlite_printer_name'][$printer_id])==0) continue;
			$printers[$i]['id']=$i;
			$printers[$i]['name']=sanitize_text_field( $_POST['p3dlite_printer_name'][$i] );
			$printers[$i]['description']=sanitize_text_field( $_POST['p3dlite_printer_description'][$i] );
			$printers[$i]['photo']=sanitize_text_field( $_POST['p3dlite_printer_photo'][$i] );
			$printers[$i]['width']=(float)( $_POST['p3dlite_printer_width'][$i] );
			$printers[$i]['length']=(float)( $_POST['p3dlite_printer_length'][$i] );
			$printers[$i]['height']=(float)( $_POST['p3dlite_printer_height'][$i] );
			$printers[$i]['diameter']=(float)( $_POST['p3dlite_printer_platform_diameter'][$i] );
			$printers[$i]['min_side']=(float)( $_POST['p3dlite_printer_min_side'][$i] );
			$printers[$i]['platform_shape']=$_POST['p3dlite_printer_platform_shape'][$i];
			$printers[$i]['full_color']= (int)$_POST['p3dlite_printer_full_color'][$i];
			$printers[$i]['price']= (strlen(sanitize_text_field($_POST['p3dlite_printer_price'][$i])) ? sanitize_text_field($_POST['p3dlite_printer_price'][$i]) : 0);
			$printers[$i]['price_type']=sanitize_text_field($_POST['p3dlite_printer_price_type'][$i]);
			$printers[$i]['price1']= (strlen(sanitize_text_field($_POST['p3dlite_printer_price1'][$i])) ? sanitize_text_field($_POST['p3dlite_printer_price1'][$i]) : 0);
			$printers[$i]['price_type1']=sanitize_text_field($_POST['p3dlite_printer_price_type1'][$i]);
			$printers[$i]['price2']= (strlen(sanitize_text_field($_POST['p3dlite_printer_price2'][$i])) ? sanitize_text_field($_POST['p3dlite_printer_price2'][$i]) : 0);
			$printers[$i]['price_type2']=sanitize_text_field($_POST['p3dlite_printer_price_type2'][$i]);
			$printers[$i]['price3']= (strlen(sanitize_text_field($_POST['p3dlite_printer_price3'][$i])) ? sanitize_text_field($_POST['p3dlite_printer_price3'][$i]) : 0);
			$printers[$i]['price_type3']=sanitize_text_field($_POST['p3dlite_printer_price_type3'][$i]);
			if ( isset($_POST['p3dlite_printer_materials']) && count( $_POST['p3dlite_printer_materials'][$i] )>0 ) {
				$printers[$i]['materials']=implode(',', $_POST['p3dlite_printer_materials'][$i]);
			}
			if (isset($_FILES['p3dlite_printer_photo_upload']['tmp_name'][$i]) && strlen($_FILES['p3dlite_printer_photo_upload']['tmp_name'][$i])>0) {

				$uploaded_file = p3dlite_upload_file('p3dlite_printer_photo_upload', $i);
				$printers[$i]['photo']=str_replace('http:','',$uploaded_file['url']);
			}
		}
		foreach ($printers as $printer) {
			p3dlite_update_option( 'p3dlite_printers', $printer );
		}
#		wp_redirect( admin_url( 'admin.php?page=3dprint_printers' ) );
	}
?>
	<form method="post" action="admin.php?page=p3dlite_printers&action=edit&printer=<?php echo (int)$_GET['printer']?>" enctype="multipart/form-data">
				<input type="hidden" name="action" value="update" />
				<br style="clear:both">
				<button class="button-secondary" type="button" onclick="location.href='<?php echo admin_url( 'admin.php?page=p3dlite_printers' );?>'"><b>&#8592;<?php _e('Back to printers', '3dprint-lite');?></b></button>
				<h3><?php echo '#'.$printer['id'].' '.$printer['name'];?></h3>
				<div>
				<table id="printer-<?php echo $printer['id'];?>" data-id="<?php echo $printer['id'];?>" class="form-table printer">
				<table id="printer-<?php echo $printer['id'];?>" class="form-table printer">
					<tr>
						<td colspan="3"><hr></td>
					</tr>
					<tr>
						<td colspan="3"><span class="item_id"><?php echo "<b>ID #".$printer['id']."</b>";?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Printer Name', '3dprint-lite' ); ?>
						</th>
						<td>
							<input type="text" name="p3dlite_printer_name[<?php echo $printer['id'];?>]" value="<?php echo $printer['name'];?>" />&nbsp;

						</td>
					</tr>
				 	<tr valign="top">
						<th scope="row"><?php _e( 'Printer Type', '3dprint-lite' );?></th>
						<td>
							<select class="select_printer">
								<option value="fff"><?php _e( 'FFF/FDM', '3dprint-lite' );?>
								<option disabled><?php _e( 'DLP/SLA (available in Premium)', '3dprint-lite' );?>
								<option disabled><?php _e( 'Laser Cutting (available in Premium)', '3dprint-lite' );?>
								<option disabled><?php _e( 'Other (available in Premium)', '3dprint-lite' );?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Printer Description', '3dprint-lite' ); ?>
						</th>
						<td>
							<textarea name="p3dlite_printer_description[<?php echo $printer['id'];?>]"/><?php if (isset($printer['description'])) echo $printer['description'];?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Photo', '3dprint-lite' );?></th>
						<td>
						<?php
						if (isset($printer['photo'])) {
						?>
							<a href="<?php echo $printer['photo'];?>"><img class="p3dlite-preview" src="<?php echo esc_url($printer['photo']);?>"></a>
						<?php
						}
						?>
							<input type="text" name="p3dlite_printer_photo[<?php echo $printer['id'];?>]" value="<?php if (isset($printer['photo'])) echo esc_url($printer['photo']);?>" />
							<input type="file" name="p3dlite_printer_photo_upload[<?php echo $printer['id'];?>]" accept="image/*">
						</td>

					</tr>


					<tr>
						<th scope="row"><?php _e( 'Full Color Printing', '3dprint-lite' );?></th>
						<td>
							<select name="p3dlite_printer_full_color[<?php echo $printer['id'];?>]">
								<option <?php if ( $printer['full_color']=='1' ) echo "selected";?> value="1"><?php _e('Yes', '3dprint-lite');?></option>
								<option <?php if ( $printer['full_color']=='0' ) echo "selected";?> value="0"><?php _e('No', '3dprint-lite');?></option>
							</select>

						</td>
					</tr>

				 	<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Shape', '3dprint-lite' );?></th>
						<td>
							<select class="select_shape" name="p3dlite_printer_platform_shape[<?php echo $printer['id'];?>]" onchange="p3dliteSelectPlatformShape(this);">
								<option <?php if ( $printer['platform_shape']=='rectangle' ) echo "selected";?> value="rectangle"><?php _e( 'Rectangle', '3dprint-lite' );?>
								<option <?php if ( $printer['platform_shape']=='circle' ) echo "selected";?> value="circle"><?php _e( 'Circle', '3dprint-lite' );?>
							</select>
						</td>
					</tr>

					<tr class="platform_shape_circle" valign="top" <?php if ( $printer['platform_shape']=='rectangle' ) echo 'style="display:none;"';?>>
						<th scope="row"><?php _e( 'Build Tray Diameter', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_platform_diameter[<?php echo $printer['id'];?>]" value="<?php echo $printer['diameter'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>                                

					<tr class="platform_shape_rectangle" valign="top" <?php if ( $printer['platform_shape']=='circle' ) echo 'style="display:none;"';?>>
						<th scope="row"><?php _e( 'Build Tray Length', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_length[<?php echo $printer['id'];?>]" value="<?php echo $printer['length'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr class="platform_shape_rectangle" valign="top" <?php if ( $printer['platform_shape']=='circle' ) echo 'style="display:none;"';?>>
						<th scope="row"><?php _e( 'Build Tray Width', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_width[<?php echo $printer['id'];?>]" value="<?php echo $printer['width'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Height', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_height[<?php echo $printer['id'];?>]" value="<?php echo $printer['height'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Minimum Model Side', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_min_side[<?php echo $printer['id'];?>]" value="<?php echo $printer['min_side'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr class="printer_materials" valign="top">
						<th scope="row"><?php _e( 'Materials', '3dprint-lite' ); ?></th>
						<td>
							<select autocomplete="off" name="p3dlite_printer_materials[<?php echo $printer['id'];?>][]" multiple="multiple" class="sumoselect">
								<?php 

									foreach ($materials as $material) {
										$j = $material['id'];
										if (isset($printer['materials'])  && strlen($printer['materials']) && in_array($j, explode(',',$printer['materials']))) $selected="selected"; else $selected="";
										echo '<option '.$selected.' value="'.$j.'">'.$material['name'];
									}
								?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"></th>
						<td><button type="button" onclick="jQuery('.p3dlite-advanced').toggle();"><?php _e('Advanced Options');?></button></td>
					</tr>

					<tr valign="top" class="p3dlite-advanced">
						<td colspan="2" align="center"><p>
<?php
	_e('Available in <a href="http://www.wp3dprinting.com/product/request-a-quote/">Premium</a> version', '3dprint-lite');
?>
</p>
<p>
<?php
	_e('Requires <a href="https://www.wp3dprinting.com/feature-comparison/">a subscription!</a>', '3dprint-lite');
?>
</p>
						</td>
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<td colspan="2" align="center"><b>Slicer settings</b>
						</td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Layer Height</th>
						<td><input  type="text" disabled value="" />mm <img class="tooltip" title="Layer height in millimeters.&lt;br&gt;This is the most important setting to determine the quality of your print. Normal quality prints are 0.1mm, high quality is 0.06mm." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced p3d-hidden">
						<th scope="row">Perimeters</th>
						<td>
							<input  type="text" disabled />
							<img class="tooltip" title="This option sets the number of perimeters to generate for each layer. Note that Slic3r may increase this number automatically when it detects sloping surfaces which benefit from a higher number of perimeters if the Extra Perimeters option is enabled." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced p3d-hidden">
						<th scope="row">Top Solid Layers</th>
						<td>
							<input  type="text" disabled />
							<img class="tooltip" title="Number of solid layers to generate on top surfaces." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced p3d-hidden">
						<th scope="row">Bottom Solid Layers</th>
						<td>
							<input  type="text" disabled />
							<img class="tooltip" title="Number of solid layers to generate on bottom surfaces." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Wall Thickness</th>
						<td><input  type="text" disabled></td>
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Bottom/Top Thickness</th>
						<td><input  type="text" disabled></td>
					</tr>


					<tr class="printer_fff p3dlite-advanced" valign="top">
						<th scope="row">Nozzle Size</th>
						<td><input  type="text" disabled></td>
					</tr>

					<tr class="printer_fff printer_dlp p3dlite-advanced " valign="top">
						<th scope="row">Line Width</th>
						<td><input  type="text" disabled />mm 
															<img class="tooltip" title="Width of a single line. Generally, the width of each line should correspond to the width of the nozzle. However, slightly reducing this value could produce better prints.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
													</td>
					</tr>

					<tr class="printer_fff printer_dlp p3dlite-advanced" valign="top">
						<th scope="row">Infill Options</th>
						<td>
							<select  autocomplete="off" disabled>
								<option selected value="0">0<option  value="5">5<option selected value="10">10<option  value="15">15<option selected value="20">20<option  value="25">25<option selected value="30">30<option  value="35">35<option selected value="40">40<option  value="45">45<option selected value="50">50<option  value="55">55<option selected value="60">60<option  value="65">65<option selected value="70">70<option  value="75">75<option selected value="80">80<option  value="85">85<option selected value="90">90<option  value="95">95<option selected value="100">100							</select>&nbsp;
							Default Infill:							<select  disabled>
								<option  value="0">0<option  value="5">5<option  value="10">10<option  value="15">15<option selected value="20">20<option  value="25">25<option  value="30">30<option  value="35">35<option  value="40">40<option  value="45">45<option  value="50">50<option  value="55">55<option  value="60">60<option  value="65">65<option  value="70">70<option  value="75">75<option  value="80">80<option  value="85">85<option  value="90">90<option  value="95">95<option  value="100">100		 					</select>
							<img class="tooltip" title="This controls how densely filled the insides of your print will be. For a solid part use 100%, for an empty part use 0%. A value around 20% is usually enough.&lt;br&gt;This won&#039;t affect the outside of the print and only adjusts how strong the part becomes.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced ">
						<th scope="row">Infill Pattern</th>
						<td>
							<select  autocomplete="off" disabled>
																									<option selected value="grid">Grid</option>
									<option  value="lines">Lines</option>
									<option  value="triangles">Triangles</option>
									<option  value="trihexagon">Tri-Hexagon</option>
									<option  value="cubic">Cubic</option>
									<option  value="cubicsubdiv">Cubic Subdivision</option>
									<option  value="quarter_cubic">Quarter Cubic</option>
									<option  value="concentric">Concentric</option>
									<option  value="concentric_3d">Concentric 3D</option>
									<option  value="zigzag">Zig Zag</option>
									<option  value="cross">Cross</option>
									<option  value="cross_3d">Cross 3D</option>
									<option  value="auto">Auto</option>
										 					</select>
							<img class="tooltip" title="Fill pattern for general low-density infill. &#039;Auto&#039; switches from Lines to Grid depending on infill density.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Enable Retraction</th>
						<td>
							<input type="hidden" disabled>
							<input type="checkbox" disabled>
							<img class="tooltip" title="Retract the filament when the nozzle is moving over a non-printed area." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Retraction Distance</th>
						<td><input  type="text" disabled>
						</td>

					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Retraction Speed</th>
						<td><input  type="text" disabled>
						</td>

					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Outer Before Inner Walls</th>
						<td>
							<input type="hidden" disabled>
							<input type="checkbox" disabled>
							<img class="tooltip" title="Prints walls in order of outside to inside when enabled. This can help improve dimensional accuracy in X and Y when using a high viscosity plastic like ABS; however it can decrease outer surface print quality, especially on overhangs." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>




					<tr valign="top" class="printer_fff p3dlite-advanced p3d-hidden">
						<th scope="row">Top/Bottom Infill Pattern</th>
						<td>
							<select  autocomplete="off" disabled>
								<option selected value="rectilinear">Rectilinear</option>
								<option  value="concentric">Concentric</option>
								<option  value="hilbertcurve">Hilbert Curve</option>
								<option  value="archimedeanchords">Archimedean Chords</option>
								<option  value="octagramspiral">Octagram Spiral</option>
		 					</select>
							<img class="tooltip" title="Fill pattern for top/bottom infill. This only affects the external visible layer, and not its adjacent solid shells.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>





					<tr valign="top" class="printer_fff p3dlite-advanced">
						<th scope="row">Print Speed</th>
						<td><input  type="text" disabled />
							<select  disabled>
								<option  value="mm3s">mm3/s</option>
								<option selected  value="mms">mm/s</option>
		 					</select>
							<img class="tooltip" title="Speed at which printing happens. This is needed if you charge by hour. A well adjusted Ultimaker can reach 150mm/s, but for good quality prints you want to print slower. &lt;br&gt;Printing speed depends on a lot of factors. So you will be experimenting with optimal settings for this.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>

					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Maximum Speed Y</th>
						<td><input  type="text" disabled>
						</td>
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Maximum Speed X</th>
						<td><input  type="text" disabled>
						</td>
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Enable Acceleration Control</th>
						<td>
							<input type="hidden" disabled>
							<input type="checkbox" disabled>
							<img class="tooltip" title="Enables adjusting the print head acceleration. Increasing the accelerations can reduce printing time at the cost of print quality." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>




					<tr valign="top" class="printer_fff p3dlite-advanced">
						<th scope="row">Infill Speed</th>
						<td><input  type="text" disabled>
						</td>
					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Support Speed</th>
						<td><input  type="text" disabled>
						</td>

					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced">
						<th scope="row">Travel Speed</th>
						<td><input  type="text" disabled>
						</td>

					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Bottom Layer Speed</th>
						<td><input  type="text" disabled>
						</td>

					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Top/bottom Speed</th>
						<td><input  type="text" disabled>
						</td>

					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Outer Shell Speed</th>
						<td><input  type="text" disabled>
						</td>

					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Inner Shell Speed</th>
						<td><input  type="text" disabled>
						</td>

					</tr>




					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Support Material</th>
						<td>
							<select  autocomplete="off" disabled>
																<option selected value="0">None</option>
								<option  value="1">Touching Buildplate</option>
								<option  value="2">Everywhere</option>
										 					</select>
														<img class="tooltip" title="Type of support structure build.&lt;br&gt;&#039;Touching buildplate&#039; is the most commonly used support setting.&lt;br&gt;&lt;br&gt;None does not do any support.&lt;br&gt;Touching buildplate only creates support where the support structure will touch the build platform.&lt;br&gt;Everywhere creates support even on top of parts of the model.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
													</td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced ">
						<th scope="row">Support Structure Type</th>
						<td>
							<select  autocomplete="off" disabled>
																								<option selected value="lines">Lines</option>
								<option  value="grid">Grid</option>
								<option  value="triangles">Triangles</option>
								<option  value="concentric">Concentric</option>
								<option  value="concentric_3d">Concentric 3D</option>
								<option  value="zigzag">Zig Zag</option>
								<option  value="cross">Cross</option>
								
		 					</select>
																						<img class="tooltip" title="The pattern of the support structures of the print. The different options available result in sturdy or easy to remove support..&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
							
						</td>
					</tr>


					<tr valign="top" class="printer_fff p3dlite-advanced">
						<th scope="row">Support Overhang Angle</th>
						<td>
							<input  type="text" disabled />&deg; 
							<img class="tooltip" title="The minimal angle that overhangs need to have to get support. With 90 degree being horizontal and 0 degree being vertical.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>


					<tr valign="top" class="printer_fff p3dlite-advanced p3d-hidden">
						<th scope="row">Support Pattern Spacing</th>
						<td>
							<input type="text" disabled>
						</td>
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Support Density</th>
						<td>
							<input  type="text" disabled />%
							<img class="tooltip" title="Adjusts the density of the support structure. A higher value results in better overhangs, but the supports are harder to remove.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>



					<tr valign="top" class="printer_fff p3dlite-advanced p3d-hidden">
						<th scope="row">Don't support bridges</th>
						<td>
							<select  autocomplete="off" disabled>
								<option selected value="0">Yes</option>
								<option  value="1">No</option>
		 					</select>
							<img class="tooltip" title="Experimental option for preventing support material from being generated under bridged areas&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">

						</td>
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced p3d-hidden">
						<th scope="row">Support Raft Layers</th>
						<td>
							<input type="text" disabled />
							<img class="tooltip" title="The object will be raised by this number of layers, and support material will be generated under it.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>
<!--					<tr valign="top" class="printer_fff p3dlite-advanced p3d-hidden">-->
					<tr valign="top" class="printer_fff p3dlite-advanced p3d-hidden">
						<th scope="row">Contact Z Distance</th>
						<td>
							<input type="text" disabled>
						</td>
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced p3d-hidden">
						<th scope="row">Brim Width</th>
						<td>
							<input  type="text" disabled></td> 
					</tr>

					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Enable Print Cooling</th>
						<td>
							<input type="hidden" disabled>
							<input type="checkbox" disabled>
							<img class="tooltip" title="Enables the print cooling fans while printing. The fans improve print quality on layers with short layer times and bridging / overhangs." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Fan Speed</th>
						<td>
							<input  type="text" disabled />%
							<img class="tooltip" title="The speed at which the print cooling fans spin." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
					</tr>
					<tr valign="top" class="printer_fff  p3dlite-advanced ">
						<th scope="row">Regular Fan Speed</th>
						<td>
							<input  type="text" disabled />%
							<img class="tooltip" title="The speed at which the fans spin before hitting the threshold. When a layer prints faster than the threshold, the fan speed gradually inclines towards the maximum fan speed. 0 - auto." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
					</tr>
					<tr valign="top" class="printer_fff  p3dlite-advanced ">
						<th scope="row">Maximum Fan Speed</th>
						<td>
							<input  type="text" disabled />%
							<img class="tooltip" title="The speed at which the fans spin on the minimum layer time. The fan speed gradually increases between the regular fan speed and maximum fan speed when the threshold is hit. 0 - auto." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
					</tr>
					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Minimal layer time</th>
						<td><input  type="text" disabled>
						</td>

					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced ">
						<th scope="row">Build Platform Adhesion Type</th>
						<td>
							<select  autocomplete="off" disabled>
								<option   value="skirt">Skirt</option>
								<option  selected value="brim">Brim</option>
								<option  value="raft">Raft</option>
								<option  value="none">None</option>

		 					</select>
							<img class="tooltip" title="Different options that help to improve both priming your extrusion and adhesion to the build plate. Brim adds a single layer flat area around the base of your model to prevent warping. Raft adds a thick grid with a roof below the model. Skirt is a line printed around the model, but not connected to the model.&lt;br&gt;&lt;b&gt;Analyse API required&lt;/b&gt;" src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">

						</td>
					</tr>

					<tr valign="top" class="printer_dlp p3dlite-advanced p3d-hidden">
						<th scope="row">Raft Base Thickness</th>
						<td>
							<input type="text" disabled>
						</td>
					</tr>



					<tr valign="top" class="printer_fff p3dlite-advanced ">
						<th scope="row">Spiralize Outer Contour</th>
						<td>
							<input type="hidden" disabled>
							<input type="checkbox" disabled>
							<img class="tooltip" title="Spiralize smooths out the Z move of the outer edge. This will create a steady Z increase over the whole print. This feature turns a solid model into a single walled print with a solid bottom. This feature should only be enabled when each layer only contains a single part." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>">
						</td>
					</tr>



					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<td colspan="2" align="center"><b>Energy</b></td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Electricity Tariff</th>
						<td>

							<input  type="text" disabled>
							&#8381;/kWh						</td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Printer Power</th>
						<td>
							<input  type="text" disabled>
							Watts						</td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Hourly Cost</th>
						<td>
							<input class="printer_kwh" type="text" size="3" disabled>
							&#8381;/hr						</td>
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<td colspan="2" align="center"><b>Depreciation</b></td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Printer Purchase Price</th>
						<td>
							<input  type="text" disabled>
							&#8381;						</td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Printer Lifetime</th>
						<td>
							<input  type="text" disabled>
							years							<img class="tooltip" title="The total time before the printer needs to be replaced." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
						</td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Printer Daily Usage</th>
						<td>
							<input  type="text" disabled>
							hours							<img class="tooltip" title="Average usage per day." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
						</td>
					</tr>

<!--					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Hours in Life</th>
						<td>
							<span class="printer_hours_in_life"></span>
							hours						</td>
					</tr>-->
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Depreciation</th>
						<td>
							<input class="printer_depreciation" type="text" size="3" disabled>
							&#8381;/hr						</td>
					</tr>
					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<td colspan="2" align="center"><b>Repairs</b></td>
					</tr>

					<tr valign="top" class="printer_fff printer_dlp p3dlite-advanced">
						<th scope="row">Repair Cost</th>
						<td>
							<input  type="text" disabled>
							% of Purchase Price							<img class="tooltip" title="Requires depreciation fields above to be set." src="<?php echo plugins_url( '3dprint-lite/images/question.png' ); ?>"></td> 
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Printing Cost', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" name="p3dlite_printer_price[<?php echo $printer['id'];?>]" value="<?php echo $printer['price'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select name="p3dlite_printer_price_type[<?php echo $printer['id'];?>]">
								<option <?php if ( $printer['price_type']=='box_volume' ) echo "selected";?> value="box_volume"><?php _e( '1 cm3 of Bounding Box Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type']=='material_volume' ) echo "selected";?> value="material_volume"><?php _e( '1 cm3 of Material Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type']=='removed_material_volume' ) echo "selected";?> value="removed_material_volume"><?php _e( '1 cm3 of Removed Material Volume (bounding box volume - material volume)', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram of Material', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type']=='fixed' ) echo "selected";?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Extra Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" name="p3dlite_printer_price1[<?php echo $printer['id'];?>]" value="<?php echo $printer['price1'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select name="p3dlite_printer_price_type1[<?php echo $printer['id'];?>]">
								<option <?php if ( $printer['price_type1']=='box_volume' ) echo "selected";?> value="box_volume"><?php _e( '1 cm3 of Bounding Box Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type1']=='material_volume' ) echo "selected";?> value="material_volume"><?php _e( '1 cm3 of Material Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type1']=='removed_material_volume' ) echo "selected";?> value="removed_material_volume"><?php _e( '1 cm3 of Removed Material Volume (bounding box volume - material volume)', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type1']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram of Material', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type1']=='fixed' ) echo "selected";?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>

								<option disabled><?php _e('1 cm3 of Support Material Volume (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('Support Material Removal Fixed Charge (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm of Laser Cutting Total Path (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 Hour (Analyse API required) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('+% to total price (Available in Premium version)', '3dprint-lite');?></option>

							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Extra Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" name="p3dlite_printer_price2[<?php echo $printer['id'];?>]" value="<?php echo $printer['price2'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select name="p3dlite_printer_price_type2[<?php echo $printer['id'];?>]">
								<option <?php if ( $printer['price_type2']=='box_volume' ) echo "selected";?> value="box_volume"><?php _e( '1 cm3 of Bounding Box Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type2']=='material_volume' ) echo "selected";?> value="material_volume"><?php _e( '1 cm3 of Material Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type2']=='removed_material_volume' ) echo "selected";?> value="removed_material_volume"><?php _e( '1 cm3 of Removed Material Volume (bounding box volume - material volume)', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type2']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram of Material', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type2']=='fixed' ) echo "selected";?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>

								<option disabled><?php _e('1 cm3 of Support Material Volume (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('Support Material Removal Fixed Charge (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm of Laser Cutting Total Path (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 Hour (Analyse API required) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('+% to total price (Available in Premium version)', '3dprint-lite');?></option>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Extra Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" name="p3dlite_printer_price3[<?php echo $printer['id'];?>]" value="<?php echo $printer['price3'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select name="p3dlite_printer_price_type3[<?php echo $printer['id'];?>]">
								<option <?php if ( $printer['price_type3']=='box_volume' ) echo "selected";?> value="box_volume"><?php _e( '1 cm3 of Bounding Box Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type3']=='material_volume' ) echo "selected";?> value="material_volume"><?php _e( '1 cm3 of Material Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type3']=='removed_material_volume' ) echo "selected";?> value="removed_material_volume"><?php _e( '1 cm3 of Removed Material Volume (bounding box volume - material volume)', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type3']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram of Material', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type3']=='fixed' ) echo "selected";?> value="fixed"><?php _e('Fixed Price', '3dprint-lite');?></option>

								<option disabled><?php _e('1 cm3 of Support Material Volume (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('Support Material Removal Fixed Charge (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 cm of Laser Cutting Total Path (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('1 Hour (Analyse API required) (Available in Premium version)', '3dprint-lite');?></option>
								<option disabled><?php _e('+% to total price (Available in Premium version)', '3dprint-lite');?></option>
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