/**
 * @author Sergey Burkov, http://www.wp3dprinting.com
 * @copyright 2015
 */

p3dlite.bar_progress=0;
p3dlite.xhr1='';
p3dlite.xhr2='';
p3dlite.xhr3='';
p3dlite.filereader_supported=true;
p3dlite.file_selected=0;
p3dlite.aabb = new Array();
p3dlite.resize_scale = 1;
p3dlite.default_scale = 100;
p3dlite.cookie_expire = parseInt(p3dlite.cookie_expire);
p3dlite.refresh_interval = "";
p3dlite.refresh_interval1 = "";
p3dlite.refresh_interval1_running = false;
p3dlite.refresh_interval_repair = "";
p3dlite.uploading = false;
p3dlite.repairing = false;
p3dlite.checking = false;
p3dlite.analyse_error = false;
p3dlite.model_total_volume=0;
p3dlite.model_surface_area=0;
p3dlite.analysed_volume = 0;
p3dlite.analysed_surface_area = 0;
p3dlite.triangulation_required = false;
p3dlite.triangulated_volume = 0;
p3dlite.triangulated_surface_area = 0;
p3dlite.is_fullscreen = 0;
p3dlite.bed_support_height = 8;
p3dlite.image_height=5;
p3dlite.image_map=1;
p3dlite.boundingBox=[];
p3dlite.fatal_error=false;

function p3dliteInit() {

	if (!document.getElementById('p3dlite-cv')) return;


	p3dliteBindSubmit();

	jQuery('p.price span.amount').html('&nbsp;');
	window.p3dlite_canvas = document.getElementById('p3dlite-cv');
	p3dliteCanvasDetails();


	var logoTimerID = 0;

	p3dlite.targetRotation = 0;
	p3dlite.targetRotationOnMouseDown = 0;
	p3dlite.mouseX = 0;
	p3dlite.mouseXOnMouseDown = 0;
	p3dlite.windowHalfX = window.innerWidth / 2;
	p3dlite.windowHalfY = window.innerHeight / 2;


	if (jQuery('input[name=get_printer_id]').val())	{
		printer=jQuery('input[name=get_printer_id]').val()
		jQuery.cookie('p3dlite_printer', printer, { expires: p3dlite.cookie_expire });
	}
	else if (jQuery.cookie('p3dlite_printer')!='undefined' && jQuery('#p3dlite_printer_'+jQuery.cookie('p3dlite_printer')).length>0) {
		printer=jQuery.cookie('p3dlite_printer');
	}
	else {
		printer=jQuery('input[name=product_printer]').data('id');

	}

	if (jQuery('input[name=get_material_id]').val()) {
		material=jQuery('input[name=get_material_id]').val()
		jQuery.cookie('p3dlite_material', material, { expires: p3dlite.cookie_expire });
	}
	else if (jQuery.cookie('p3dlite_material')!='undefined' && jQuery('#p3dlite_material_'+jQuery.cookie('p3dlite_material')).length>0)	{
		material=jQuery.cookie('p3dlite_material');
	}
	else {
		material=jQuery('input[name=product_filament]').data('id');
	}
	if (jQuery('input[name=get_coating_id]').val()) {
		coating=jQuery('input[name=get_coating_id]').val()
		jQuery.cookie('p3dlite_coating', coating, { expires: p3dlite.cookie_expire });
	}
	else if (jQuery.cookie('p3dlite_coating')!='undefined' && jQuery('#p3dlite_coating_'+jQuery.cookie('p3dlite_coating')).length>0)	{
		coating=jQuery.cookie('p3dlite_coating');
	}
	else {
		coating=jQuery('input[name=product_coating]').data('id');
	}

	if (jQuery('input[name=get_infill]').val()) {
		infill=jQuery('input[name=get_infill]').val()
		jQuery.cookie('p3dlite_infill', infill, { expires: p3dlite.cookie_expire });
	}
	else if (jQuery.cookie('p3dlite_infill')!='undefined') {
		infill=jQuery.cookie('p3dlite_infill');
	}
	else {
		infill=jQuery('input[name=product_infill]').data('id');
	}

	if (p3dlite.file_url) {
		product_file=p3dlite.file_url.split('/').reverse()[0];
	}
	else if (jQuery('input[name=get_product_model]').val()) {
		product_file=jQuery('input[name=get_product_model]').val();
		jQuery.cookie('p3dlite_file', product_file, { expires: p3dlite.cookie_expire });
	}
	else {
		product_file=jQuery.cookie('p3dlite_file');
	}

	if (typeof(jQuery.cookie('p3dlite_mtl'))!='undefined') {
		product_mtl=jQuery.cookie('p3dlite_mtl');
	}
	else if (jQuery('#p3dlite_mtl').val()!='') {
		product_mtl=jQuery('#p3dlite_mtl').val();
	}
	else {
		product_mtl='';
	}


	if (jQuery('input[name=get_product_unit]').val()) {
		product_unit=jQuery('input[name=get_product_unit]').val();
		jQuery.cookie('p3dlite_unit', product_unit, { expires: p3dlite.cookie_expire });
	}
	else if (jQuery.cookie('p3dlite_unit')!='undefined') {
		product_unit=jQuery.cookie('p3dlite_unit');
	}
	else {
		product_unit='mm';
	}



	if (typeof(infill)!='undefined') {
		jQuery('#p3dlite_infill_'+infill).attr('checked', 'checked');
		p3dliteSelectInfill(jQuery('#p3dlite_infill_'+infill).closest('li'));
	}

	if (typeof(printer)!='undefined') {
		jQuery('#p3dlite_printer_'+printer).attr('checked', 'checked');
		p3dliteSelectPrinter(jQuery('#p3dlite_printer_'+printer).closest('li'));
	}
	else {
		jQuery('input[name=product_printer]').first().attr('checked', 'checked')
		p3dliteSelectPrinter(jQuery('input[name=product_printer]').first());
	}

	if (typeof(material)!='undefined') {
		jQuery('#p3dlite_material_'+material).attr('checked', 'checked');
		p3dliteSelectFilament(jQuery('#p3dlite_material_'+material).closest('li'));
	}
	else {
		jQuery('input[name=product_filament]').first().attr('checked', 'checked')
		p3dliteSelectFilament(jQuery('input[name=product_filament]').first().closest('li'));
	}

	if (typeof(coating)!='undefined') {
		jQuery('#p3dlite_coating_'+coating).attr('checked', 'checked');
		p3dliteSelectCoating(jQuery('#p3dlite_coating_'+coating).closest('li'));
	}
	else if (jQuery('input[name=product_coating]').length>0) {
		jQuery('input[name=product_coating]').first().attr('checked', 'checked');
		p3dliteSelectCoating(jQuery('input[name=product_coating]').first().closest('li'));
	}

	if (typeof(coating)!='undefined') {
		jQuery('#p3dlite_coating_'+coating).attr('checked', 'checked');
		p3dliteSelectCoating(jQuery('#p3dlite_coating_'+coating).closest('li'));
	}




	if (typeof(product_file)!='undefined') {
		jQuery('#pa_p3dlite_model').val(product_file);
	}
	if (typeof(product_unit)!='undefined') {
		jQuery("input[name=p3dlite_unit][value=" + product_unit + "]").attr('checked', 'checked');
		p3dliteSelectUnit(jQuery("input[name=p3dlite_unit][value=" + product_unit + "]"));
	}
	else {
		p3dliteSelectUnit(jQuery("input[name=p3dlite_unit][value=mm]"));
	}

	if (typeof(printer)!='undefined' && typeof(material)!='undefined' && typeof(product_file)!='undefined') {
		p3dliteGetStats();
	}
	else {
		p3dliteDisplayUserDefinedProgressBar(false);
		p3dliteDisplayQuoteLoading(false);
	}
	if (typeof (product_file) !='undefined' && product_file) {
		var model_type=product_file.split('.').pop().toLowerCase();
		p3dliteViewerInit(p3dlite.upload_url+encodeURIComponent(product_file), product_mtl, model_type, false);
	}
	else 
		p3dliteViewerInit('');

	jQuery('.p3dlite-tooltip').tooltipster({ contentAsHTML: true, maxWidth: 300, theme: 'tooltipster-light' });

	p3dliteAnimate();
}



jQuery(document).ready(function(){

if (!document.getElementById('p3dlite-container')) return;

p3dliteInit();

window.p3dlite_uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,browserplus,gears,html4',
	browse_button : 'p3dlite-pickfiles', // you can pass an id...
	dragdrop: true,
	drop_element : 'p3dlite-viewer',
	multi_selection: false,
	multiple_queues : false,
	max_file_count : 1,
	max_file_size: p3dlite.file_max_size+"mb",
	container: document.getElementById('p3dlite-container'), 
	url : p3dlite.url,
	chunk_size : p3dlite.file_chunk_size+'mb',
	flash_swf_url : p3dlite.plugin_url+'includes/ext/plupload/Moxie.swf',
	silverlight_xap_url : p3dlite.plugin_url+'includes/ext/plupload/Moxie.xap',
	filters : {
	mime_types: [
		{
			title : p3dlite.file_extensions+" files", 
			extensions : p3dlite.file_extensions
		}
	]
	},
	init: {
		QueueChanged: function(p3dlite_uploader) {
			if(p3dlite_uploader.files.length > 1)
			{
				jQuery('#p3dlite-filelist').html('');
				jQuery('#p3dlite-canvas-uploading-status').hide();	
				
				
				p3dlite_uploader.files.splice(0, 1);
			}
		},
		PostInit: function() {
			document.getElementById('p3dlite-filelist').innerHTML = '';
			document.getElementById('p3dlite-console').innerHTML = '';
			jQuery('#p3dlite-canvas-uploading-status').hide();
			

		},
		Browse: function () {

		},
		FilesAdded: function(up, files) {
			p3dlite.bar_progress = 0;
			p3dlite.analysed_volume = 0;
			p3dlite.analysed_surface_area = 0;
			p3dlite.triangulation_required = false;
			p3dlite.triangulated_volume = 0;
			p3dlite.triangulated_surface_area = 0;
			jQuery.removeCookie("p3dlite_mtl");
			jQuery('.p3dlite-mail-success').hide();
			jQuery('.p3dlite-mail-error').hide();
			jQuery('#p3dlite-repair-status, #p3dlite-canvas-repair-status').hide();
			jQuery('#p3dlite-analyse-status, #p3dlite-canvas-analyse-status').hide();
			jQuery('#stats-material-volume-loading, #stats-material-weight-loading, #stats-hours-loading').hide();

			
			if (p3dlite.show_upload_button=='on') {
				jQuery('#p3dlite-model-message-upload').hide();
			}

			var file = files[0].getNative();
			var file_ext = file.name.split('.').pop().toLowerCase();

			window.wp.event_manager.doAction( 'p3dlite.filesAdded');
			if (p3dlite.filereader_supported) {
				if (jQuery.inArray(file_ext, p3dlite.files_to_convert)==-1) {
					p3dlite.filereader_supported = true;
					var reader = new FileReader();
					reader.onload = function(event) {
						var chars  = new Uint8Array(event.target.result);
						var CHUNK_SIZE = 0x8000; 
						var index = 0;
						var length = chars.length;
						var result = '';
						var slice;
						while (index < length) {
							slice = chars.subarray(index, Math.min(index + CHUNK_SIZE, length)); 
							result += String.fromCharCode.apply(null, slice);
							index += CHUNK_SIZE;
						}


						window.wp.event_manager.doAction( 'p3dlite.fileRead');

						p3dliteViewerInit(result, '', file_ext, true);
						
						p3dliteDisplayUserDefinedProgressBar(false);
	
						p3dliteChangeModelColor(p3dliteGetCurrentColor());

						p3dliteGetStats();

						p3dliteInitScaling();

            				}
            
					reader.readAsArrayBuffer(file);
				} //!zip
					else p3dlite.filereader_supported = false; //zip file
        		}
		        plupload.each(files, function(file) {
		        	document.getElementById('p3dlite-filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
				jQuery('#p3dlite-canvas-uploading-status').show();
				
		        });
		        p3dlite_uploader.disableBrowse(true);
//		        jQuery('.p3dlite-stats').hide();

			if (p3dlite.filereader_supported) {
				p3dliteInitScaling();
			}
		        p3dliteDisplayPrice(false);
		        p3dliteDisplayAddToCart(false);
		        p3dliteDisplayConsole(false);
		        p3dliteDisplayUserDefinedProgressBar(true);
		        p3dliteDisplayQuoteLoading(true);

			if (p3dlite.api_repair=='on' || p3dlite.api_optimize=='on') 
				jQuery('#p3dlite-repair-status, #p3dlite-canvas-repair-status').show();


		        up.start();
			p3dlite.uploading = true;
			if(p3dlite.xhr3 && p3dlite.xhr3.readyState != 4) {
				p3dlite.xhr3.abort();
			}
			jQuery('#p3dlite-canvas-repair-status').hide()
			p3dliteDisplayQuoteLoading(true);
		        jQuery('#p3dlite-pickfiles').click();
		},



		UploadProgress: function(up, file) {
			p3dlite.bar_progress=parseFloat(file.percent/100);
			document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
		},

		UploadComplete: function(up, file, response) {
			p3dlite.uploading = false;
			//p3dliteDisplayQuoteLoading(false);
			p3dlite_uploader.disableBrowse(false);
			jQuery('#p3dlite-canvas-uploading-status').hide();
			
		},

		Error: function(up, err) {
			p3dlite.uploading = false;
			p3dlite_uploader.disableBrowse(false);
			jQuery('#p3dlite-canvas-uploading-status').hide();
			
			//p3dliteDisplayQuoteLoading(false);
			document.getElementById('p3dlite-console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
			window.p3dliteProgressButton._stop();
			p3dliteDisplayConsole(true);
		}
	}
});

p3dlite_uploader.bind('BeforeUpload', function (up, file) {
	up.settings.multipart_params = {
		"action" : 'p3dlite_handle_upload',
		"quote_attributes" : jQuery('.woo_attribute').serialize(),
		"product_id" : jQuery('#p3dlite_product_id').val(),
		"printer_id" : jQuery('input[name=product_printer]:checked').data('id'),
		"material_id" : jQuery('input[name=product_filament]:checked').data('id'),
		"coating_id" : jQuery('input[name=product_coating]:checked').data('id'),
		"unit" : jQuery('input[name=p3dlite_unit]:checked').val(),
		"image_height" : p3dlite.image_height,
		"image_map" : p3dlite.image_map
	}
	window.wp.event_manager.doAction( 'p3dlite.beforeUpload');
	});

p3dlite_uploader.init();

p3dlite_uploader.bind('FileUploaded', function(p3dlite_uploader,file,response) {
	p3dlite.uploading = false;
	p3dlite.fatal_error = false;
	jQuery('#p3dlite-canvas-uploading-status').hide();
	
	var data = jQuery.parseJSON( response.response );

	jQuery('p.price span.amount').html('&nbsp;');
	if (typeof(data.error)!=='undefined') { //fatal error
		p3dlite.fatal_error = true;
		jQuery('#p3dlite-console').html(data.error.message).show();
		p3dliteDisplayUserDefinedProgressBar(false);
		p3dliteDisplayQuoteLoading(false);
		return false;
  	}

	p3dliteDisplayQuoteLoading(false);
        p3dliteDisplayAddToCart(true);
	jQuery('.p3dlite-mail-success').remove();
	jQuery('.p3dlite-mail-error').remove();

	if (!p3dlite.filereader_supported) {
		p3dliteDisplayUserDefinedProgressBar(true);
		p3dliteDisplayQuoteLoading(true);
		var model_type=data.filename.split('.').pop().toLowerCase();
		var mtl='';
		var printer_full_color = jQuery('input[name=product_printer]:checked').data('full_color');
//		if (printer_full_color=='1' && typeof(data.material)!=='undefined' && data.material.length>0) {
		mtl = data.material;
		p3dlite.mtl=mtl;

		jQuery.cookie('p3dlite_mtl', mtl, { expires: p3dlite.cookie_expire });
//		}
//		else {
//			jQuery.removeCookie("p3dlite_mtl");
//		}
		p3dliteViewerInit(p3dlite.upload_url+encodeURIComponent(data.filename), mtl, model_type, false); 
	}

	p3dliteShowResponse(data);

	jQuery.cookie('p3dlite_file',data.filename, { expires: p3dlite.cookie_expire });
	product_file=data.filename;
	jQuery('#pa_p3dlite_model').val(product_file);
	p3dliteDisplayStats(true)
	p3dliteGetStats();
        p3dliteInitScaling();

	if (p3dliteCheckPrintability()) {
//		if (!p3dlite.uploading && !p3dlite.checking && !((p3dlite.xhr1 && p3dlite.xhr1.readyState != 4) || (p3dlite.xhr2 && p3dlite.xhr2.readyState != 4))) {
			p3dliteDisplayPrice(true);
			p3dliteDisplayAddToCart(true);
//		}
	}



	window.wp.event_manager.doAction( 'p3dlite.fileUploaded');
});





jQuery("#p3dlite-model-message-upload").click(function() { 
	jQuery('div.moxie-shim input[type=file]').trigger('click');
});


});

function p3dliteBindSubmit() {
	jQuery( "form.p3dlite_form" ).on( "submit", function(e) {

		//get resize scale
		jQuery('#p3dlite-resize-scale').val(p3dlite.resize_scale);

		//screenshot of the current product
		jQuery('#p3dlite-thumb').val(window.p3dlite_canvas.toDataURL().replace('data:image/png;base64,',''));
		window.wp.event_manager.doAction( 'p3dlite.productScreenshot');
		return true;
	})
}



function p3dliteViewerInit(model, mtl, ext, is_string) {


	var p3dlite_canvas = document.getElementById('p3dlite-cv');
	var p3dlite_canvas_width = jQuery('#p3dlite-cv').width()
	var p3dlite_canvas_height = jQuery('#p3dlite-cv').height()
	p3dlite.mtl=mtl;

	//3D Renderer
	p3dlite.renderer = Detector.webgl? new THREEP3DL.WebGLRenderer({ antialias: true, canvas: p3dlite_canvas, preserveDrawingBuffer: true }): new THREEP3DL.CanvasRenderer({canvas: p3dlite_canvas});
	p3dlite.renderer.setClearColor( parseInt(p3dlite.background1, 16) );
	p3dlite.renderer.setPixelRatio( window.devicePixelRatio );
	p3dlite.renderer.setSize( p3dlite_canvas_width, p3dlite_canvas_height );


	if (Detector.webgl) {

		p3dlite.renderer.gammaInput = true;
		p3dlite.renderer.gammaOutput = true;
		p3dlite.renderer.shadowMap.enabled = true;
		//p3dlite.renderer.shadowMap.renderReverseSided = false;
		p3dlite.renderer.shadowMap.Type = THREEP3DL.PCFSoftShadowMap;
	}

	p3dlite.camera = new THREEP3DL.PerspectiveCamera( 35, p3dlite_canvas_width / p3dlite_canvas_height, 1, 1000 );
	p3dlite.camera.position.set( 0, 0, 0 );
	p3dlite.cameraTarget = new THREEP3DL.Vector3( 0, 0, 0 );

	p3dlite.scene = new THREEP3DL.Scene();
	//p3dlite.scene.fog = new THREEP3DL.Fog( 0x72645b, 1, 300 );

	//Group
	if (p3dlite.group) p3dlite.scene.remove(p3dlite.group);
	p3dlite.group = new THREEP3DL.Group();
	p3dlite.group.position.set( 0, 0, 0 )
	p3dlite.group.name = "group";
	p3dlite.scene.add( p3dlite.group );


	//Light
	ambientLight = new THREEP3DL.AmbientLight(0x191919);
	p3dlite.scene.add(ambientLight);
	ambientLight.name = "light";



	directionalLight = new THREEP3DL.DirectionalLight( 0xffffff, 0.75 );
	directionalLight.name = "light";


	directionalLight2 = new THREEP3DL.DirectionalLight( 0xffffff, 0.75 );
	directionalLight2.name = "light2";


	p3dlite.controls = new THREEP3DL.OrbitControls( p3dlite.camera, p3dlite.renderer.domElement );
	if (p3dlite.auto_rotation=='on') {
		p3dlite.controls.autoRotate = true; 
	}

	p3dlite.controls.addEventListener( 'start', function() {
		p3dlite.controls.autoRotate = false;
	});

	if (ext=='stl')
		p3dlite.loader = new THREEP3DL.STLLoader();
	else if (ext=='obj') {
		p3dlite.loader = new THREEP3DL.OBJLoader();
	}

	if (model.length>0 && is_string) {
		var model_geometry = p3dlite.loader.parse(model);
		p3dliteModelOnLoad(model_geometry);
	}
	else if (model.length>0 && !is_string) {

		var mtlLoader = new THREEP3DL.MTLLoader();
		mtlLoader.setPath( p3dlite.upload_url );
		if (ext=='obj' && mtl && mtl.length>0) {
			mtlLoader.load( mtl, function( materials ) {
				materials.preload();
				var objLoader = new THREEP3DL.OBJLoader();
				p3dlite.loader.setMaterials( materials );
				p3dlite.loader.load( model, function ( geometry ) {
		        	    p3dliteModelOnLoad(geometry);
				});
			});
		}
		else {
			p3dlite.loader.load( model, function ( geometry ) {
				p3dliteModelOnLoad(geometry)
			} );
		}
	}


//	jQuery('.p3dlite-tooltip').tooltipster({ contentAsHTML: true, maxWidth: 300, theme: 'tooltipster-light' });
	window.addEventListener( 'resize', p3dliteOnWindowResize, false );

}


function p3dliteModelOnLoad(object) {
	p3dlite.object = object;
	geometry = object;
	if (object.type=='Group') {
		geometry = object.children[0].geometry;
		//todo: merge multiple geometries?
	}

	

	//Material
	var material = p3dliteCreateMaterial(p3dlite.shading);

	geometry.computeBoundingBox();
	p3dlite.boundingBox=geometry.boundingBox;
	if (object.type=='Group' && object.children.length>1) {
		var min_coords=[];
		var max_coords=[];
		for(var i=0;i<object.children.length;i++) {
			object.children[i].geometry.computeBoundingBox();
			if (i==0) {
				min_coords.x=object.children[i].geometry.boundingBox.min.x;
				min_coords.y=object.children[i].geometry.boundingBox.min.y;
				min_coords.z=object.children[i].geometry.boundingBox.min.z;
				max_coords.x=object.children[i].geometry.boundingBox.max.x;
				max_coords.y=object.children[i].geometry.boundingBox.max.y;
				max_coords.z=object.children[i].geometry.boundingBox.max.z;
			}
			else {
				if (object.children[i].geometry.boundingBox.min.x < min_coords.x) min_coords.x = object.children[i].geometry.boundingBox.min.x;
				if (object.children[i].geometry.boundingBox.min.y < min_coords.y) min_coords.y = object.children[i].geometry.boundingBox.min.y;
				if (object.children[i].geometry.boundingBox.min.z < min_coords.z) min_coords.z = object.children[i].geometry.boundingBox.min.z;

				if (object.children[i].geometry.boundingBox.max.x > max_coords.x) max_coords.x = object.children[i].geometry.boundingBox.max.x;
				if (object.children[i].geometry.boundingBox.max.y > max_coords.y) max_coords.y = object.children[i].geometry.boundingBox.max.y;
				if (object.children[i].geometry.boundingBox.max.z > max_coords.z) max_coords.z = object.children[i].geometry.boundingBox.max.z;
			}
		}
		p3dlite.boundingBox.min=min_coords;
		p3dlite.boundingBox.max=max_coords;
	}

	//Model


	p3dliteCreateModel(object, geometry, material, p3dlite.shading);

	if (object.type=='Group' && object.children.length>1)	{
	}
	else {
		geometry.center();
	}


	//Glow mesh
	p3dliteSetCurrentGlow();

	var mesh_width = geometry.boundingBox.max.x - geometry.boundingBox.min.x;
	var mesh_length = geometry.boundingBox.max.y - geometry.boundingBox.min.y;
	var mesh_height = geometry.boundingBox.max.z - geometry.boundingBox.min.z;

	var mesh_diagonal = Math.sqrt(mesh_width * mesh_width + mesh_length * mesh_length + mesh_height * mesh_height);

	if (Detector.webgl) {
		var canvas_width=p3dlite.renderer.getSize().width;
		var canvas_height=p3dlite.renderer.getSize().height;
	}
	else {
		var canvas_width=jQuery('#p3dlite-cv').width();
		var canvas_height=jQuery('#p3dlite-cv').height();
	}

	var canvas_diagonal = Math.sqrt(canvas_width * canvas_width + canvas_height * canvas_height);
	var model_dim = new Array();
	model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
	model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
	model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;

	var max_side = Math.max(model_dim.x, model_dim.y, model_dim.z)*1.2

	//Camera
	p3dlite.camera.position.set(max_side*p3dlite.resize_scale, max_side*p3dlite.resize_scale, max_side*p3dlite.resize_scale);

	p3dlite.camera.far=10000;
	p3dlite.camera.updateProjectionMatrix();

	//Ground
	if (Detector.webgl) {
		if (p3dlite.ground_mirror=='on') {
			var plane_shininess = 2500;
			var plane_transparent = true;
			var plane_opacity = 0.6;
		}
		else {
			var plane_shininess = 30;
			var plane_transparent = false;
			var plane_opacity = 1;
		}

		if (p3dliteMobileCheck()) {
			var plane_material = new THREEP3DL.MeshLambertMaterial( { color: parseInt(p3dlite.ground_color, 16), wireframe: false, flatShading:true, precision: 'mediump' } );
		}
		else {
			var plane_material = new THREEP3DL.MeshPhongMaterial ( { color: parseInt(p3dlite.ground_color, 16), transparent:plane_transparent, opacity:plane_opacity, shininess: plane_shininess, precision: 'mediump' } ) 
		}

		plane = new THREEP3DL.Mesh(
			new THREEP3DL.PlaneBufferGeometry( 2000, 2000 ),
			plane_material
		);
		plane.rotation.x = -Math.PI/2;
		plane.position.y = p3dlite.boundingBox.min.z;
		plane.receiveShadow = true;
		plane.castShadow = true;
		plane.name = 'ground';
		p3dlite.scene.add( plane );
		if (p3dlite.ground_mirror=='on') {
			var planeGeo = new THREEP3DL.PlaneBufferGeometry( 2000, 2000 );
			//p3dlite.groundMirror = new THREEP3DL.Mirror( p3dlite.renderer, p3dlite.camera, { clipBias: 0.003, textureWidth: canvas_width, textureHeight: canvas_height, color: 0xaaaaaa } );
			//var mirrorMesh = new THREEP3DL.Mesh( planeGeo, p3dlite.groundMirror.material );
			var mirrorMesh = new THREEP3DL.Reflector( planeGeo, { //new way
				clipBias: 0.003,
				textureWidth: canvas_width,
				textureHeight: canvas_height,
				color: 0xaaaaaa,
				recursion: 1
			} );
			mirrorMesh.position.y = p3dlite.boundingBox.min.z-0.1;
			//mirrorMesh.add( p3dlite.groundMirror );
			mirrorMesh.rotateX( - Math.PI / 2 );
			p3dlite.scene.add( mirrorMesh );
		}

	}

	//Grid
	if (p3dlite.show_grid=='on' && p3dlite.plane_color.length>0) {

		var size = 1000, step = 50;
		var grid_geometry = new THREEP3DL.Geometry();
		for ( var i = - size; i <= size; i += step ) {
			grid_geometry.vertices.push( new THREEP3DL.Vector3( - size, p3dlite.boundingBox.min.z, i ) );
			grid_geometry.vertices.push( new THREEP3DL.Vector3(   size, p3dlite.boundingBox.min.z, i ) );
			grid_geometry.vertices.push( new THREEP3DL.Vector3( i, p3dlite.boundingBox.min.z, - size ) );
			grid_geometry.vertices.push( new THREEP3DL.Vector3( i, p3dlite.boundingBox.min.z,   size ) );
		
		}


		var grid_material = new THREEP3DL.LineBasicMaterial( { color: parseInt(p3dlite.plane_color, 16), opacity: 0.2 } );
		var line = new THREEP3DL.LineSegments( grid_geometry, grid_material );
		line.name = "grid";
		p3dlite.scene.add( line );
		p3dlite.group.add( line );
	}

	
	directionalLight.position.set( max_side*2, max_side*2, max_side*2 );
	directionalLight2.position.set( -max_side*2, max_side*2, -max_side*2 );
	if (Detector.webgl && p3dlite.show_shadow=='on') {
		directionalLight.castShadow = true;
		directionalLight2.castShadow = true;
		p3dliteMakeShadow();
	}
	p3dlite.scene.add( directionalLight );
	p3dlite.scene.add( directionalLight2 );


	p3dliteDisplayUserDefinedProgressBar(false);
	p3dliteInitScaling();
	p3dliteDrawPrinterBox();



}

function p3dliteCreateMaterial(model_shading) {
	var model_shininess = p3dliteGetCurrentShininess()
	var model_transparency = p3dliteGetCurrentTransparency()
	var color = new THREEP3DL.Color( p3dliteGetCurrentColor() );
	color.offsetHSL(0, 0, -0.1);
	if (Detector.webgl && !p3dliteMobileCheck()) {
		if (model_shading=='smooth') {
			var flat_shading = false;
		}
		else {
			var flat_shading = true;
		}

//		var material = new THREEP3DL.MeshPhongMaterial( { color: color, specular: model_shininess.specular, shininess: model_shininess.shininess, transparent:true, opacity:model_transparency, wireframe:false, shading:shading } );
		var material = new THREEP3DL.MeshPhongMaterial( { color: color, specular: model_shininess.specular, shininess: model_shininess.shininess, transparent:true, opacity:model_transparency, wireframe:false, flatShading:flat_shading, precision: 'mediump' } );
	}
	else {

//		var material = new THREEP3DL.MeshLambertMaterial( { color: color, vertexColors: THREEP3DL.FaceColors, wireframe: false, overdraw:1, shading:THREEP3DL.FlatShading } );
		var material = new THREEP3DL.MeshLambertMaterial( { color: color, transparent:true, opacity:model_transparency, wireframe: false, flatShading:true, precision: 'mediump' } );

	}
	return material;
}

function p3dliteCreateModel(object, geometry, material, shading) {

	var attrib = geometry.getAttribute('position');
	if(attrib === undefined) {
		throw new Error('a given BufferGeometry object must have a position attribute.');
	}
	var positions = attrib.array;
	var vertices = [];
	for(var i = 0, n = positions.length; i < n; i += 3) {
		var x = positions[i];
		var y = positions[i + 1];
		var z = positions[i + 2];
		vertices.push(new THREEP3DL.Vector3(x, y, z));
	}
	var faces = [];
	for(var i = 0, n = vertices.length; i < n; i += 3) {
		faces.push(new THREEP3DL.Face3(i, i + 1, i + 2));
	}

	var new_geometry = new THREEP3DL.Geometry();
	new_geometry.vertices = vertices;
	new_geometry.faces = faces;
	new_geometry.computeFaceNormals();              
	new_geometry.computeVertexNormals();
	new_geometry.computeBoundingBox();

	geometry = new_geometry;
	geometry.center();

	if (shading=='smooth' && Detector.webgl) {
                var smooth_geometry = new THREEP3DL.Geometry();
                smooth_geometry.vertices = vertices;
                smooth_geometry.faces = faces;
                smooth_geometry.computeFaceNormals();              
                smooth_geometry.mergeVertices();
                smooth_geometry.computeVertexNormals();
		smooth_geometry.computeBoundingBox();
		geometry = smooth_geometry;
                p3dlite.model_mesh = new THREEP3DL.Mesh(geometry, material);
	}


	else {
		p3dlite.model_mesh = new THREEP3DL.Mesh( geometry, material );

	}

	if (p3dlite.object.type=='Group') {
		if (!p3dlite.mtl || p3dlite.mtl.length==0) {
			//p3dlite.object.children[0].material=p3dlite.model_mesh.material;
			for (var i=0;i<p3dlite.object.children.length;i++) {
				p3dlite.object.children[i].material=p3dlite.model_mesh.material;
			}
		}

		p3dlite.object.position.set( 0, 0, 0 );
		p3dlite.object.rotation.z = 90 * Math.PI/180;
		p3dlite.object.rotation.x = -90 * Math.PI/180;
		p3dlite.object.name = "object";
		if (Detector.webgl) {
			for (var i=0;i<p3dlite.object.children.length;i++) {
				p3dlite.object.children[i].castShadow = true;
				p3dlite.object.children[i].receiveShadow = true;
			}
		}
		p3dlite.scene.add( p3dlite.object );
		p3dlite.group.add( p3dlite.object );
	}
	else {
		p3dlite.model_mesh.position.set( 0, 0, 0 );
		p3dlite.model_mesh.rotation.z = 90 * Math.PI/180;
		p3dlite.model_mesh.rotation.x = -90 * Math.PI/180;
		p3dlite.model_mesh.name = "model";
		if (Detector.webgl) {
			p3dlite.model_mesh.castShadow = true;
			p3dlite.model_mesh.receiveShadow = true;
		}
		p3dlite.scene.add( p3dlite.model_mesh );
		p3dlite.group.add( p3dlite.model_mesh );
	}



	var p3dliteRangeSlider = document.getElementById('p3dlite-scale'); 
	if (typeof(p3dliteRangeSlider)!=='undefined' && p3dliteRangeSlider.noUiSlider) {
		p3dliteRangeSlider.noUiSlider.set(p3dlite.resize_scale*100);
	}


}

function p3dliteMakeShadow() {
	var model_dim = new Array();
	model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
	model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
	model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;

	var max_side = Math.max(model_dim.x, model_dim.y, model_dim.z)
  	var bias = -0.001;
	var d = max_side*p3dlite.resize_scale;
	if (d<30) bias = -0.0001;
	directionalLight2.shadow.camera.left = directionalLight.shadow.camera.left = -d;
	directionalLight2.shadow.camera.right = directionalLight.shadow.camera.right = d;
	directionalLight2.shadow.camera.top = directionalLight.shadow.camera.top = d;
	directionalLight2.shadow.camera.bottom = directionalLight.shadow.camera.bottom = -d;
	directionalLight2.shadow.camera.near = directionalLight.shadow.camera.near = 1;
	directionalLight2.shadow.camera.far = directionalLight.shadow.camera.far = p3dlite.camera.far;
	directionalLight2.shadow.mapSize.width = directionalLight.shadow.mapSize.width = 2048;
	directionalLight2.shadow.mapSize.height = directionalLight.shadow.mapSize.height = 2048;
	directionalLight2.shadow.bias = directionalLight.shadow.bias = bias;

	if (directionalLight.shadow.map) {
		directionalLight.shadow.map.dispose(); 
		directionalLight.shadow.map = null;
		directionalLight2.shadow.map.dispose(); 
		directionalLight2.shadow.map = null;
	}
}



function p3dliteOnWindowResize() {

	var p3dlite_canvas_width = jQuery('div.p3dlite-images').width()
	var p3dlite_canvas_height = jQuery('#p3dlite-viewer').width()
	p3dlite.camera.aspect = p3dlite_canvas_width / p3dlite_canvas_height;
	p3dlite.camera.updateProjectionMatrix();
	p3dlite.renderer.setSize( p3dlite_canvas_width, p3dlite_canvas_height );

	p3dliteCanvasDetails();
}

function p3dliteCanvasDetails() {
	jQuery("#canvas-stats").css({
		top: jQuery("#p3dlite-cv").position().top ,
		left: jQuery("#p3dlite-cv").position().left+10
	}) ;

}




function p3dliteAnimate() {
	window.requestAnimationFrame( p3dliteAnimate );
	p3dlite.group.rotation.y += ( p3dlite.targetRotation - p3dlite.group.rotation.y ) * 0.05;
	p3dlite.controls.update();
	p3dliteRender();
}

function p3dliteRender() {
	if (Detector.webgl && p3dlite.ground_mirror=='on' && typeof(p3dlite.groundMirror)!=='undefined')
		p3dlite.groundMirror.render();
	p3dlite.renderer.render( p3dlite.scene, p3dlite.camera );
}




function p3dliteBoxFitsBox (dim_x1, dim_y1, dim_z1, dim_x2, dim_y2, dim_z2) {
	
	var fits=true;
	var min_dim1=Math.min(dim_x1, dim_y1, dim_z1);
	var min_dim2=Math.min(dim_x2, dim_y2, dim_z2);
	var max_dim1=Math.max(dim_x1, dim_y1, dim_z1);
	var max_dim2=Math.max(dim_x2, dim_y2, dim_z2);
	var diag1=Math.sqrt(dim_x1 + dim_y1 + dim_z1);
	var diag2=Math.sqrt(dim_x2 + dim_y2 + dim_z2);
	var median1=(dim_x1 + dim_y1 + dim_z1)/3;
	var median2=(dim_x2 + dim_y2 + dim_z2)/3;

	if (min_dim1<=min_dim2 && max_dim1<=max_dim2 && diag1 <= diag2) 
		fits = true;
	else 
		fits = false;


	fits=window.wp.event_manager.applyFilters('p3dlite.boxFitsBox', fits, dim_x1, dim_y1, dim_z1, dim_x2, dim_y2, dim_z2);
	return fits;
}

function p3dliteBoxFitsBoxXY (dim_x1, dim_y1, dim_x2, dim_y2) {
	var fits=true;
	var min_dim1=Math.min(dim_x1, dim_y1);
	var min_dim2=Math.min(dim_x2, dim_y2);
	var max_dim1=Math.max(dim_x1, dim_y1);
	var max_dim2=Math.max(dim_x2, dim_y2);
	var diag1=Math.sqrt(dim_x1 + dim_y1);
	var diag2=Math.sqrt(dim_x2 + dim_y2);

	if (min_dim1<=min_dim2 && max_dim1<=max_dim2) 
		fits = true;
	else 
		fits = false;



	fits=window.wp.event_manager.applyFilters('p3dlite.boxFitsBoxXY', fits, dim_x1, dim_y1, dim_x2, dim_y2);
	return fits;
}

function p3dliteShowError(message) {
	var decoded = jQuery('#p3dlite-console').html(message).text();
	jQuery('#p3dlite-console').html(decoded).show();
	window.wp.event_manager.doAction( 'p3dlite.showError');
}

function p3dliteInitProgressButton () {
	if (!p3dliteDetectIE()) {
		window.p3dliteProgressButton=new ProgressButton(document.getElementById('p3dlite-pickfiles'), {
			callback : function( instance ) {
				interval = setInterval( function() {
					instance._setProgress( p3dlite.bar_progress );
					if( parseInt(p3dlite.bar_progress) === 1 ) {
						instance._stop(1);
						clearInterval( interval );
					}
				}, 200 );
			}
		} );
	}

}

jQuery(document).ready(function() {
	p3dliteInitProgressButton();
        jQuery('nav.applePie').easyPie();
	jQuery('nav.applePie ul.nav').show();

});

function p3dliteChangeModelColor(model_color) {
	if (!p3dlite.model_mesh) return;

	p3dlite.model_mesh.material.color.set(model_color);
	p3dlite.model_mesh.material.color.offsetHSL(0, 0, -0.1);
	if (Detector.webgl) {
		var model_shininess = p3dliteGetCurrentShininess();
		p3dlite.model_mesh.material.shininess = model_shininess.shininess;
		p3dlite.model_mesh.material.specular.set(model_shininess.specular);

		var model_transparency = p3dliteGetCurrentTransparency();
		p3dlite.model_mesh.material.opacity = model_transparency;

		p3dliteSetCurrentGlow();
		if (p3dlite.object && p3dlite.object.type=='Group' && !(p3dlite.mtl && p3dlite.mtl.length>0)) {
			for (var i=0;i<p3dlite.object.children.length;i++) {
				p3dlite.object.children[i].material=p3dlite.model_mesh.material;

			}

		}
	}

	
};

function p3dliteGetCurrentColor() {
	var model_color = '#ffffff';
	if (typeof(jQuery('input[name=product_coating]:checked').closest('li').data('color'))!=='undefined' && jQuery('input[name=product_coating]:checked').closest('li').data('color').length>0 )
		model_color = jQuery('input[name=product_coating]:checked').closest('li').data('color');
	else if (typeof(jQuery('input[name=product_filament]:checked').closest('li').data('color'))!=='undefined') {
		model_color = jQuery('input[name=product_filament]:checked').closest('li').data('color');
	}
	return model_color;

}

function p3dliteGetCurrentShininess() {
	var model_shininess = 'plastic';
	if (typeof(jQuery('input[name=product_coating]:checked').closest('li').data('shininess'))!=='undefined' && jQuery('input[name=product_coating]:checked').closest('li').data('shininess').length>0 && jQuery('input[name=product_coating]:checked').closest('li').data('shininess')!='none')
		model_shininess = jQuery('input[name=product_coating]:checked').closest('li').data('shininess');
	else if (typeof(jQuery('input[name=product_filament]:checked').closest('li').data('shininess'))!=='undefined') {
		model_shininess = jQuery('input[name=product_filament]:checked').closest('li').data('shininess');
	}

	switch(model_shininess) {
		case 'plastic':
			var shininess = 150;
			var specular = 0x111111;
	        break;
		case 'wood':
			var shininess = 15;
			var specular = 0x111111;
	        break;
		case 'metal':
			var shininess = 500;
			var specular = 0xc9c9c9;
	        break;
		default:
			var shininess = 150;
			var specular = 0x111111;

	}
	return {shininess: shininess, specular: specular};
}

function p3dliteGetCurrentTransparency() {
	var model_transparency = 'opaque';
	if (typeof(jQuery('input[name=product_coating]:checked').closest('li').data('transparency'))!=='undefined' && jQuery('input[name=product_coating]:checked').closest('li').data('transparency').length>0 && jQuery('input[name=product_coating]:checked').closest('li').data('transparency')!='none')
		model_transparency = jQuery('input[name=product_coating]:checked').closest('li').data('transparency');
	else if (typeof(jQuery('input[name=product_filament]:checked').closest('li').data('transparency'))!=='undefined') {
		model_transparency = jQuery('input[name=product_filament]:checked').closest('li').data('transparency');
	}

	switch(model_transparency) {
		case 'opaque':
			var transparency = 1;
	        break;
		case 'resin':
			var transparency = 0.8;
	        break;
		case 'glass':
			var transparency = 0.6;
	        break;
		default:
			var transparency = 1;

	}
	return transparency;
}

function p3dliteGetCurrentGlowColor() {
	var model_glow = '';

	if (typeof(jQuery('input[name=product_coating]:checked').closest('li').data('glow'))!=='undefined' && jQuery('input[name=product_coating]:checked').closest('li').data('glow')=='1') {
		model_glow = jQuery('input[name=product_coating]:checked').closest('li').data('color');

	}
	else if (jQuery('input[name=product_filament]:checked').closest('li').data('glow')=='1') {
		model_glow = jQuery('input[name=product_filament]:checked').closest('li').data('color');
	}
	else {
		model_glow = '';
	}
	return model_glow;
}

function p3dliteSetCurrentGlow() {
	if (!Detector.webgl) return;
	if (p3dlite.mtl && p3dlite.mtl.length>0) return;
	if (typeof(p3dlite.glow_mesh)!=='undefined') p3dlite.model_mesh.remove(p3dlite.glow_mesh.object3d);

	
	var glow_color = p3dliteGetCurrentGlowColor();

	if (glow_color.length>0) {

		//var material = p3dliteCreateMaterial('smooth');
		//p3dliteRemoveGroupObjectByName('model');
		//p3dliteCreateModel(p3dlite.backup_geometry, material, 'smooth');

		var model_dim = new Array();
		model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
		model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
		model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;

		var min_side = Math.max(model_dim.x, model_dim.y, model_dim.z)

		p3dlite.glow_mesh = new THREEx.GeometricGlowMesh(p3dlite.model_mesh, 0.01, min_side/20);
		p3dlite.model_mesh.add(p3dlite.glow_mesh.object3d);
		p3dlite.glow_mesh.position = p3dlite.model_mesh.position;

		var insideUniforms	= p3dlite.glow_mesh.insideMesh.material.uniforms
		insideUniforms.glowColor.value.set(glow_color)
		var outsideUniforms	= p3dlite.glow_mesh.outsideMesh.material.uniforms
		outsideUniforms.glowColor.value.set(glow_color)

	}
	else {
		//var material = p3dliteCreateMaterial(p3dlite.shading);
		//p3dliteCreateModel(p3dlite.backup_geometry, material, p3dlite.shading);
	}

}


function p3dliteSelectFilament(obj) {

//	jQuery(obj).find('input[type=radio]').attr('checked','true');
	jQuery('input[name=product_filament]:checked').prop('checked',false);
	jQuery(obj).find('input').prop('checked',true);

	jQuery('#pa_p3dlite_material').val(jQuery(obj).find('input').data('id'));
	material_id=jQuery(obj).find('input').data('id');

	if (jQuery(obj).closest('ul.p3dlite-bxslider').length>0) {
		jQuery(obj).closest('ul.p3dlite-bxslider').find('li').removeClass('p3dlite-selected-li');
		jQuery(obj).addClass('p3dlite-selected-li');
	}


	p3dliteChangeModelColor(p3dliteGetCurrentColor());

	jQuery.cookie('p3dlite_material', jQuery(obj).find('input').attr('data-id'), { expires: p3dlite.cookie_expire });
	if (p3dlite.selection_order=='materials_printers') {
		//check compatible printers
		var compatible_printers = new Array();
		jQuery('input[name=product_printer]').each(function() {
			var materials = jQuery(this).data('materials')+'';
			var materials_array = materials.split(',');
				if (materials.length>0 && jQuery.inArray(material_id+'', materials_array)==-1) {
				jQuery(this).prop('disabled', true);
				jQuery(this).prop('checked', false);
				jQuery(this).css('visibility', 'hidden');
				jQuery(this).parent().find('.p3dlite-dropdown-item').addClass('p3dlite-inactive-dropdown-item');
				jQuery(this).parent().find('.p3dlite-slider-item').addClass('p3dlite-inactive-slider-item');
	
			}
			else {
				jQuery(this).prop('disabled', false);
				jQuery(this).css('visibility', 'visible');
				jQuery(this).parent().find('.p3dlite-dropdown-item').removeClass('p3dlite-inactive-dropdown-item');
				jQuery(this).parent().find('.p3dlite-slider-item').removeClass('p3dlite-inactive-slider-item');
				compatible_printers.push(this);
		
			}
		});

		//check if a compatible printer is already selected
		var selected = false;
		for (i=0;i<compatible_printers.length;i++) {
			if (jQuery('#pa_p3dlite_printer').val()==jQuery(compatible_printers[i]).data('id'))
				selected = true;
		}
		if (!selected && compatible_printers.length>0) {
			jQuery(compatible_printers[0]).prop('checked', true);		
			p3dliteSelectPrinter(jQuery(compatible_printers[0]).parent());
		}
	}
	//check compatible coatings
	var compatible_coatings = new Array();

	jQuery('input[name=product_coating]').each(function() {
		var materials = jQuery(this).data('materials')+'';
		var materials_array = materials.split(',');

			if (materials.length>0 && jQuery.inArray(material_id+'', materials_array)==-1) {
			jQuery(this).prop('disabled', true);
			jQuery(this).prop('checked', false);
			jQuery(this).css('visibility', 'hidden');
			jQuery(this).parent().find('.p3dlite-dropdown-item').addClass('p3dlite-inactive-dropdown-item');
			if (jQuery(this).parent().hasClass('p3dlite-color-item')) jQuery(this).parent().addClass('p3dlite-inactive-color-item');
			jQuery(this).parent().find('.p3dlite-slider-item').addClass('p3dlite-inactive-slider-item');

		}
		else {
			jQuery(this).prop('disabled', false);
			jQuery(this).css('visibility', 'visible');
			jQuery(this).parent().find('.p3dlite-dropdown-item').removeClass('p3dlite-inactive-dropdown-item');
			if (jQuery(this).parent().hasClass('p3dlite-color-item')) jQuery(this).parent().removeClass('p3dlite-inactive-color-item');
			jQuery(this).parent().find('.p3dlite-slider-item').removeClass('p3dlite-inactive-slider-item');
			compatible_coatings.push(this);

		}
	});

	//check if a compatible coating is already selected
	var selected = false;
	for (i=0;i<compatible_coatings.length;i++) {
		if (jQuery('#pa_p3dlite_coating').val()==jQuery(compatible_coatings[i]).data('id'))
			selected = true;
	}
	if (!selected && compatible_coatings.length>0) {
		jQuery(compatible_coatings[0]).prop('checked', true);		
		p3dliteSelectCoating(jQuery(compatible_coatings[0]).parent());
	}




	var material_name=jQuery(obj).find('input').data('name');
	var material_color=jQuery(obj).find('input').data('color');
	if (typeof(document.getElementById('p3dlite-material-name'))!=='undefined') {
		jQuery('#p3dlite-material-name').html(p3dlite.text_material+' : <div style="background-color:'+material_color+'" class="color-sample"></div>'+material_name);
	}

	if (jQuery(obj).hasClass('p3dlite-color-item')) {
		jQuery(obj).closest('.p3dlite-fieldset').find('.p3dlite-color-item').removeClass('p3dlite-active');
		jQuery(obj).addClass('p3dlite-active');
	}
	
	p3dliteGetStats();

	p3dliteCheckPrintability();
	window.wp.event_manager.doAction( 'p3dlite.selectFilament');
}

function p3dliteSelectCoating(obj) {
	if (jQuery(obj).find('input[type=radio]').prop('disabled')) return false;

	if (jQuery(obj).closest('ul.p3dlite-bxslider').length>0) {
		jQuery(obj).closest('ul.p3dlite-bxslider').find('li').removeClass('p3dlite-selected-li');
		jQuery(obj).addClass('p3dlite-selected-li');
	}

//	jQuery(obj).find('input[type=radio]').attr('checked','true');
	jQuery('input[name=product_coating]:checked').prop('checked',false);
	jQuery(obj).find('input').prop('checked',true);

	jQuery('#pa_p3dlite_coating').val(jQuery(obj).find('input').data('id'));
	coating_id=jQuery(obj).find('input').data('id');

	if (typeof(jQuery(obj).attr('data-color'))!=='undefined' && jQuery(obj).attr('data-color').length>0) {
		p3dliteChangeModelColor(jQuery(obj).attr('data-color'));
	}
	else {
		p3dliteChangeModelColor(jQuery('input[name=product_filament]:checked').closest('li').data('color'));
	}

	jQuery.cookie('p3dlite_coating', jQuery(obj).find('input').attr('data-id'), { expires: p3dlite.cookie_expire });

	var coating_name=jQuery(obj).find('input').data('name');
	var material_color=jQuery(obj).find('input').data('color');
	if (typeof(document.getElementById('p3dlite-coating-name'))!=='undefined') {
		jQuery('#p3dlite-coating-name').html(p3dlite.text_coating+' : <div style="background-color:'+material_color+'" class="color-sample"></div>'+coating_name);
	}

	if (jQuery(obj).hasClass('p3dlite-color-item')) {
		jQuery(obj).closest('.p3dlite-fieldset').find('.p3dlite-color-item').removeClass('p3dlite-active');
		jQuery(obj).addClass('p3dlite-active');
	}

	p3dliteGetStats();
	window.wp.event_manager.doAction( 'p3dlite.selectCoating');
}

function p3dliteSelectUnit(obj) {
	jQuery(obj).attr('checked','true');
	jQuery('#p3dlite_unit').val(jQuery(obj).val());
	jQuery('#pa_p3dlite_unit').val(jQuery(obj).val());

	product_unit=jQuery(obj).val();
	if (product_unit=='inch') {
		p3dlite.resize_scale=2.54*10;
	}
	else {
		p3dlite.resize_scale=1;
	}


	if (p3dlite.model_mesh) {
		p3dliteResizeModel(p3dlite.resize_scale);
	}

	jQuery.cookie('p3dlite_unit', jQuery(obj).val(), { expires: p3dlite.cookie_expire });
	
	//p3dliteDrawPrinterBox();
	p3dliteGetStats();
	p3dliteInitScaling();

	window.wp.event_manager.doAction( 'p3dlite.selectUnit');
}


function p3dliteSelectPrinter(obj) {
	if (jQuery(obj).find('input[type=radio]').prop('disabled')) return false;




	if (jQuery(obj).closest('ul.p3dlite-bxslider').length>0) {
		jQuery(obj).closest('ul.p3dlite-bxslider').find('li').removeClass('p3dlite-selected-li');
		jQuery(obj).addClass('p3dlite-selected-li');
	}


	var old_printer = jQuery('#p3dlite_printer_'+jQuery('#pa_p3dlite_printer').val())
	var new_printer = jQuery(obj).find('input');

	var materials = jQuery(obj).find('input[type=radio]').data('materials')+'';
	var materials_array = materials.split(',');


/*	if (p3dlite.object && p3dlite.object.type=='Group' && new_printer.data('full_color')!='1')  {
		jQuery('#p3dlite-model-message-fullcolor').show();
	}
	else {
		jQuery('#p3dlite-model-message-fullcolor').hide;
	}*/

	//jQuery(obj).find('input[type=radio]').attr('checked','true');

	if (p3dlite.selection_order=='printers_materials') {
		//check compatible materials
		var compatible_materials = new Array();
		jQuery('input[name=product_filament]').each(function() {
//			var materials = jQuery(this).data('materials')+'';
//			var materials_array = materials.split(',');
			var material_id = jQuery(this).data('id');
			if (materials.length>0 && jQuery.inArray(material_id+'', materials_array)==-1) {
				jQuery(this).prop('disabled', true);
				jQuery(this).prop('checked', false);
				jQuery(this).css('visibility', 'hidden');
				jQuery(this).parent().find('.p3dlite-dropdown-item').addClass('p3dlite-inactive-dropdown-item');
				if (jQuery(this).parent().hasClass('p3dlite-color-item')) jQuery(this).parent().addClass('p3dlite-inactive-color-item');

			}
			else {
				jQuery(this).prop('disabled', false);
				jQuery(this).css('visibility', 'visible');
				jQuery(this).parent().find('.p3dlite-dropdown-item').removeClass('p3dlite-inactive-dropdown-item');
				if (jQuery(this).parent().hasClass('p3dlite-color-item')) jQuery(this).parent().removeClass('p3dlite-inactive-color-item');
				compatible_materials.push(this);
	
			}
		});


		//check if a compatible material is already selected
		var selected = false;
		for (var i=0;i<compatible_materials.length;i++) {
			if (jQuery('#pa_p3dlite_material').val()==jQuery(compatible_materials[i]).data('id'))
				selected = true;
		}
		if (!selected && compatible_materials.length>0) {
			jQuery(compatible_materials[0]).prop('checked', true);		
			p3dliteSelectFilament(jQuery('#p3dlite_material_'+jQuery(compatible_materials[0]).data('id')).closest('li'));
		}
	}

	jQuery('input[name=product_printer]:checked').prop('checked',false);
	jQuery(obj).find('input').prop('checked',true);

	jQuery('#pa_p3dlite_printer').val(jQuery(obj).find('input').data('id'));

	jQuery.cookie('p3dlite_printer', jQuery(obj).find('input').data('id'), { expires: p3dlite.cookie_expire });
	printer_id=jQuery(obj).find('input').data('id');
	var printer_name=jQuery(obj).find('input').data('name');
	var printer_type=jQuery(obj).find('input').data('type')
	if (typeof(document.getElementById('p3dlite-printer-name'))!=='undefined') {
		jQuery('#p3dlite-printer-name').html(p3dlite.text_printer+' : '+printer_name);
	}
	p3dliteDrawPrinterBox();



	if (jQuery('#pa_p3dlite_infill').length>0) {
	//check compatible infills
	var compatible_infills = new Array();
	jQuery('input[name=product_infill]').each(function() {
		var infills = jQuery(obj).find('input').data('infills')+'';
		var infills_array = infills.split(',');

		if (infills.length>0 && jQuery.inArray(jQuery(this).data('id')+'', infills_array)==-1) {
			jQuery(this).prop('disabled', true);
			jQuery(this).prop('checked', false);
//			jQuery(this).css('visibility', 'hidden');
			jQuery(this).hide();
			jQuery(this).parent().find('.p3dlite-dropdown-item').hide();
		}
		else {
			jQuery(this).prop('disabled', false);
//			jQuery(this).css('visibility', 'visible');

			if (!jQuery(this).hasClass('p3dlite-infill-dropdown'))
				jQuery(this).show();	
			jQuery(this).parent().find('.p3dlite-dropdown-item').show();
			compatible_infills.push(this);

		}
	});

	//check if a compatible infill is already selected
	var selected = false;
	for (i=0;i<compatible_infills.length;i++) {
		if (jQuery('#pa_p3dlite_infill').val().length>0 && jQuery('#pa_p3dlite_infill').val()==jQuery(compatible_infills[i]).data('id')) {
			selected = true;
		}

	}


	if (!selected && compatible_infills.length>0) {
		var default_infill = jQuery(obj).find('input').data('default-infill');
		for (i=0;i<compatible_infills.length;i++) {
			if (jQuery(compatible_infills[i]).data('id') == default_infill) {
				jQuery(compatible_infills[i]).prop('checked', true);		
				p3dliteSelectInfill(jQuery(compatible_infills[i]).parent());
			}

		}
	}
	}


	if (printer_type!='fff') {
		jQuery('#infill-info').css('visibility', 'hidden');
		jQuery('#stats-print-time').hide();

	}
	else {
		if (p3dlite.show_infills=='on') jQuery('#infill-info').css('visibility', 'visible');
		if (p3dlite.show_model_stats_model_hours=='on') jQuery('#stats-print-time').show();
	}
	if (printer_type=='dlp' && p3dlite.show_model_stats_model_hours=='on') {
		jQuery('#stats-print-time, #stats-hours').show();

	}

	p3dliteGetStats();
	//p3dliteInitScaleSlider();
	p3dliteInitScaling();
	p3dliteCheckPrintability();


	window.wp.event_manager.doAction( 'p3dlite.selectPrinter');
}

function p3dliteSelectInfill (obj) {

	if (jQuery(obj).find('input[type=radio]').prop('disabled')) return false;	
	jQuery(obj).find('input[type=radio]').attr('checked','true');
	jQuery('#pa_p3dlite_infill').val(jQuery(obj).find('input').data('id'));
	jQuery.cookie('p3dlite_infill', jQuery(obj).find('input').data('id'), { expires: p3dlite.cookie_expire });

	infill_id=jQuery(obj).find('input').data('id');
	var infill_name=jQuery(obj).find('input').data('name');
	if (typeof(document.getElementById('p3dlite-infill-name'))!=='undefined') {
		jQuery('#p3dlite-infill-name').html(p3dlite.text_infill+' : '+infill_name);
	}
}

function p3dliteCheckPrintability() {
//todo: many things
	var printable=true;
	if (p3dlite.object && p3dlite.object.type=='Group' && p3dlite.mtl && p3dlite.mtl.length>0 && jQuery('input:radio[name=product_printer]:checked').data('full_color')!='1')  {
		jQuery('#p3dlite-model-message-fullcolor').show();
		printable=false;
	}
	else {
		jQuery('#p3dlite-model-message-fullcolor').hide();
	}
	if (p3dlite.object && p3dlite.object.type=='Group' && p3dlite.object.children.length>1) {
		jQuery('#p3dlite-model-message-multiobj').show();
		if (p3dlite.pricing == 'checkout') {
			printable=false;
		}
	}
	else {
		jQuery('#p3dlite-model-message-multiobj').hide();
	}


	var x_dim=parseFloat(jQuery('#stats-length').html());
	var y_dim=parseFloat(jQuery('#stats-width').html());
	var z_dim=parseFloat(jQuery('#stats-height').html());

	if (!x_dim || !y_dim || !z_dim) return false;

	var printer_width=parseFloat(jQuery('input:radio[name=product_printer]:checked').attr('data-width'));
	var printer_length=parseFloat(jQuery('input:radio[name=product_printer]:checked').attr('data-length'));
	var printer_height=parseFloat(jQuery('input:radio[name=product_printer]:checked').attr('data-height'));

/*      //do we need all this now?
	if (!p3dliteBoxFitsBox(x_dim*10, y_dim*10, z_dim*10, printer_width, printer_length, printer_height)) {
		p3dliteShowError(p3dlite.error_box_fit); 
		printable=false;
	}
	else if (!p3dliteBoxFitsBoxXY(x_dim*10, y_dim*10, printer_width, printer_length)) {
		p3dliteShowError(p3dlite.warning_box_fit);
	}
*/

	if (!printable) { 
		p3dliteDisplayPrice(false);
		p3dliteDisplayAddToCart(false);
	}
	else { 
		jQuery('#printer_fit_error').hide();
	}

	printable=window.wp.event_manager.applyFilters('p3dlite.checkPrintability', printable);

	return printable;
}

function p3dliteCalculatePrintingCost( product_info ) {
	var material = jQuery('input[name=product_filament]:checked');
	var coating = jQuery('input[name=product_coating]:checked');
	var printer = jQuery('input[name=product_printer]:checked');
	var material_cost = 0;
	var coating_cost = 0;
	var printing_cost = 0;

	var printer_price_fields = ['', '1', '2', '3'];
	var material_price_fields = ['', '1', '2'];
	var coating_price_fields = ['', '1'];

	
	printing_volume=product_info['model']['material_volume'];
	var removed_material_volume = product_info['model']['box_volume']-printing_volume;

	for (var p=0;p<material_price_fields.length;p++) {
		if ( !isNaN ( material.data('price'+material_price_fields[p]) ) ) {
			if ( material.data('price_type'+material_price_fields[p])=='cm3' ) {
				material_cost+=( printing_volume )*material.data('price'+material_price_fields[p]);
			}
			else if ( material.data('price_type'+material_price_fields[p])=='gram' ) {
				material_cost+=product_info['model']['weight']*material.data('price'+material_price_fields[p]);
			}
			else if ( material.data('price_type'+material_price_fields[p])=="removed_material_volume" ) {
				material_cost+=removed_material_volume*material.data('price'+material_price_fields[p]);
			}
			else if ( material.data('price_type'+material_price_fields[p])=='fixed' ) {
				material_cost+=material.data('price'+material_price_fields[p]);
			}
			else if ( material.data('price_type'+material_price_fields[p])=='pct' ) {
				material_pct+=material.data('price'+material_price_fields[p]);
			}
		}
		else if ( material.data('price'+material_price_fields[p]).indexOf(':')>-1 ) {
	
			var material_volume_pricing_array = material.data('price'+material_price_fields[p]).split(';');

			for (var i = material_volume_pricing_array.length-1; i >= 0; i--) {
				var discount_rule = material_volume_pricing_array[i].split(':');
				if (discount_rule.length == 2) {
					var amount = discount_rule[0];
					var price = discount_rule[1];	
					if ( material.data('price_type'+material_price_fields[p])=='cm3' ) {
						if (printing_volume >= amount ) {
							material_cost += printing_volume * price;
							break;
						}
					}
					else if ( material.data('price_type'+material_price_fields[p])=='gram' ) {
						if (product_info['model']['weight'] >= amount)  {
							material_cost += product_info['model']['weight'] * price;
							break;
						}
					}
					else if ( material.data('price_type'+material_price_fields[p])=='removed_material_volume' ) {
						if (removed_material_volume >= amount)  {
							material_cost += removed_material_volume * price;
							break;
						}
					}
					else if ( material.data('price_type'+material_price_fields[p])=='fixed' ) {
						if (printing_volume >= amount ) {
							material_cost += price;
							break;
						}
					}
				}
			}
		}
	}



	for (var p=0;p<printer_price_fields.length;p++) {

		if ( !isNaN ( printer.data('price'+printer_price_fields[p]) ) ) {
			if ( printer.data('price_type'+printer_price_fields[p])=="material_volume" ) {
				printing_cost+=printing_volume*printer.data('price'+printer_price_fields[p]);
			}
			else if ( printer.data('price_type'+printer_price_fields[p])=="box_volume" ) {
				printing_cost+=product_info['model']['box_volume']*printer.data('price'+printer_price_fields[p]);
			}
			else if ( printer.data('price_type'+printer_price_fields[p])=="removed_material_volume" ) {
				printing_cost+=removed_material_volume*printer.data('price'+printer_price_fields[p]);
			}
			else if ( printer.data('price_type'+printer_price_fields[p])=="gram" ) {
				printing_cost+=product_info['model']['weight']*printer.data('price'+printer_price_fields[p]);
			}
			else if ( printer.data('price_type'+printer_price_fields[p])=="fixed" ) {
				printing_cost += printer.data('price'+printer_price_fields[p]);
			}
			else if ( printer.data('price_type'+printer_price_fields[p])=="pct" ) {
				printing_pct += printer.data('price'+printer_price_fields[p]);
			}
	
		}
		else if ( printer.data('price'+printer_price_fields[p]).indexOf(':')>-1 ) {
			var printer_volume_pricing_array = printer.data('price'+printer_price_fields[p]).split(';');
			for (var i = printer_volume_pricing_array.length-1; i >=0; i--) {
				var discount_rule = printer_volume_pricing_array[i].split(':');
				if (discount_rule.length == 2) {
					var amount = discount_rule[0];
					var price = discount_rule[1];	
					if ( printer.data('price_type'+printer_price_fields[p])=='material_volume' ) {
						if (printing_volume >= amount) {
							printing_cost += printing_volume * price;
							break;
						}
					}
					else if ( printer.data('price_type'+printer_price_fields[p])=='box_volume' ) {
						if (product_info['model']['box_volume'] >= amount) {
							printing_cost += product_info['model']['box_volume'] * price;
							break;
						}
					}
					else if ( printer.data('price_type'+printer_price_fields[p])=='removed_material_volume' ) {
						if (removed_material_volume >= amount) {
							printing_cost += removed_material_volume * price;
							break;
						}
					}
					else if ( printer.data('price_type'+printer_price_fields[p])=='gram' ) {
						if (product_info['model']['weight'] >= amount) {
							printing_cost += product_info['model']['weight'] * price;
							break;
						}
					}
					else if ( printer.data('price_type'+printer_price_fields[p])=='fixed' ) {
						if (printing_volume >= amount) {
							printing_cost += price;
							break;
						}
					}
				}
			}
		}

	}

	for (var p=0;p<coating_price_fields.length;p++) {
		if (typeof(coating.data('price'+coating_price_fields[p]))!=='undefined') {
			if ( !isNaN ( coating.data('price'+coating_price_fields[p]) ) ) {
				if ( coating.data('price_type'+coating_price_fields[p])=='cm2' ) {
					coating_cost += product_info['model']['surface_area'] * coating.data('price'+coating_price_fields[p]);
				}
				else if ( coating.data('price_type'+coating_price_fields[p])=='fixed' ) {
					coating_cost += coating.data('price'+coating_price_fields[p]);
				}
				else if ( coating.data('price_type'+coating_price_fields[p])=='pct' ) {
					coating_pct += coating.data('price'+coating_price_fields[p]);
				}
			}
			else if ( coating.data('price'+coating_price_fields[p]).indexOf(':')>-1 ) {
				var surface_area_pricing_array = coating.data('price'+coating_price_fields[p]).split(';');
				for (var i = surface_area_pricing_array.length-1; i >= 0; i--) {
					var discount_rule = surface_area_pricing_array[i].split(':');
					if (discount_rule.length == 2) {
						var amount = discount_rule[0];
						var price = discount_rule[1];	
						if ( coating.data('price_type'+coating_price_fields[p])=='cm2' ) {
							if (product_info['model']['surface_area'] >= amount) {
								coating_cost += product_info['model']['surface_area'] * price;
								break;
							}
						}
						else if ( coating.data('price_type'+coating_price_fields[p])=='fixed' ) {
							if (product_info['model']['surface_area'] >= amount) {
								coating_cost += price;
								break;
							}
						}
					}
				}
			}
		}
	}
/*
	jQuery( ".woo_attribute" ).each(function() {
		var attr_price=parseFloat(jQuery(this).find('option:selected').data('price'));
		if (isNaN(attr_price)) attr_price = 0;
		var attr_price_type=jQuery(this).find('option:selected').data('price-type');
		var attr_pct_type=jQuery(this).find('option:selected').data('pct-type');

		if (typeof(attr_pct_type)!=='undefined' && attr_price_type=='pct') {
			if (attr_pct_type=='printer') {	
				printing_cost+=(printing_cost/100)*attr_price
			}
			else if (attr_pct_type=='material') {	
				material_cost+=(material_cost/100)*attr_price
			}
			else if (attr_pct_type=='coating') {	
				coating_cost+=(coating_cost/100)*attr_price

			}
		}

	})
*/
	var total=printing_cost+material_cost+coating_cost;
	if (p3dlite.minimum_price_type=='starting_price')  {
		total = total + parseFloat(p3dlite.min_price);
	}
	else if (p3dlite.minimum_price_type=='minimum_price') {
		if (total < p3dlite.min_price) total = p3dlite.min_price;
	}
	if (total < parseFloat(p3dlite.min_price)) total = parseFloat(p3dlite.min_price);
	total=window.wp.event_manager.applyFilters('3dprint-lite.calculatePrintingCost', total, product_info);
	return total;
}
//an example hook
window.wp.event_manager.addFilter( 'p3dlite.calculatePrintingCost', function  (total, product_info) {
	//do something with total
	return total;
})


function p3dliteGetStatsClientSide() {
	var printer_type = jQuery('input[name=product_printer]:checked').data('type');

	if (p3dlite.triangulated_volume>0) {
		var filament_volume = (p3dlite.triangulated_volume/1000)*Math.pow(p3dlite.resize_scale,3); //cm3
	}
	else {
		var filament_volume = Math.abs((p3dlite.model_total_volume/1000)*Math.pow(p3dlite.resize_scale,3)); //cm3
		
	}
	if (printer_type=='dlp') {
		p3dlite.print_time=p3dliteCalculateDLPPrintTime();
	}



	if (p3dlite.triangulated_surface_area>0) {
		var surface_area = (p3dlite.triangulated_surface_area/100)*Math.pow(p3dlite.resize_scale,2); //cm2
	}
	else {
		var surface_area = (p3dlite.model_surface_area/100)*Math.pow(p3dlite.resize_scale,2); //cm2
	}
	var model_x=model_y=model_z=0;


	model_x = (Math.abs(p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x)/10)*p3dlite.resize_scale
	model_y = (Math.abs(p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y)/10)*p3dlite.resize_scale
	model_z = (Math.abs(p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z)/10)*p3dlite.resize_scale


        

	var box_volume = model_x * model_y * model_z; 
	var material_coeff = 100; //%
	var unit_multiplier = p3dliteGetUnitMultiplier();

	jQuery( ".woo_attribute" ).each(function() {
		var attr_price=parseFloat(jQuery(this).find('option:selected').data('price'));
		if (isNaN(attr_price)) attr_price = 0;
		var attr_price_type=jQuery(this).find('option:selected').data('price-type');
		var attr_pct_type=jQuery(this).find('option:selected').data('pct-type');

		if (typeof(attr_pct_type)!=='undefined' && attr_price_type=='pct') {
			if (attr_pct_type=='material_amount') {	
				material_coeff+=attr_price
			}

		}

	})



	model_x = model_x*unit_multiplier;
	model_y = model_y*unit_multiplier;
	model_z = model_z*unit_multiplier;
	surface_area=surface_area*Math.pow(unit_multiplier, 2);
	box_volume = model_x*model_y*model_z; 
	filament_volume = filament_volume*Math.pow(unit_multiplier, 3);



	var product_info = new Array();
	product_info['model'] = new Array();
	product_info['model']['x_dim'] = parseFloat(model_x.toFixed(2));
	product_info['model']['y_dim'] = parseFloat(model_y.toFixed(2));
	product_info['model']['z_dim'] = parseFloat(model_z.toFixed(2));
	product_info['model']['material_volume'] = parseFloat(filament_volume.toFixed(2))*(material_coeff/100);
	product_info['model']['box_volume'] = parseFloat(box_volume.toFixed(2));

	product_info['model']['surface_area'] = parseFloat(surface_area.toFixed(2));
	product_info['model']['weight'] = parseFloat(filament_volume * parseFloat(jQuery('input[name=product_filament]:checked').data('density')) * (material_coeff/100));

	jQuery('#p3dlite-weight').val(product_info['model']['weight']);

	product_info=window.wp.event_manager.applyFilters('p3dlite.getStatsClientSide', product_info);
	return product_info;
}
function p3dliteGetStats() {
//	jQuery('.p3dlite-stats').hide(); 
	p3dliteDisplayPrice(false);
	p3dliteDisplayAddToCart(false);
	jQuery('#p3dlite-console').html('').hide();

	
	var printer_id=jQuery('input:radio[name=product_printer]:checked').attr('data-id');
	var material_id=jQuery('input:radio[name=product_filament]:checked').attr('data-id');
	if (typeof(jQuery('input:radio[name=product_coating]:checked').attr('data-id'))!=='undefined')
		var coating_id=jQuery('input:radio[name=product_coating]:checked').attr('data-id');
	else 
		var coating_id='';
	var product_id=jQuery('#p3dlite_product_id').val();
	var model=jQuery('#pa_p3dlite_model').val();
	var model_unit=jQuery("input[name=p3dlite_unit]:checked").val();

	if (p3dlite.model_mesh) {
		var product_info=p3dliteGetStatsClientSide();

		var product_price=parseFloat(p3dliteCalculatePrintingCost(product_info));

		var response = new Array();
		response.model = new Array();
		response.model = product_info['model'];
		response.price = product_price.toFixed(p3dlite.price_num_decimals);

		if (p3dlite.currency_position=='left')
			accounting.settings.currency.format = "%s%v";
		else if (p3dlite.currency_position=='left_space')
			accounting.settings.currency.format = "%s %v";
		else if (p3dlite.currency_position=='right')
			accounting.settings.currency.format = "%v%s";
		else if (p3dlite.currency_position=='right_space')
			accounting.settings.currency.format = "%v %s";

		response.html_price = accounting.formatMoney(product_price, p3dlite.currency_symbol, p3dlite.price_num_decimals, p3dlite.thousand_sep, p3dlite.decimal_sep);
		jQuery('#p3dlite_estimated_price').val(response.price);
		p3dliteShowResponse(response);

	}
	window.wp.event_manager.doAction( 'p3dlite.getStats');

}

function p3dliteShowResponse(response) {

	if (response.error) { //fatal error
		p3dlite.fatal_error = true
		p3dliteDisplayQuoteLoading(false); 
		p3dliteShowError(response.error);
		return;
	}
	var printer_type = jQuery('input[name=product_printer]:checked').data('type');

//	if (window.p3dlite_uploader.state==1 && !p3dlite.checking) p3dliteDisplayQuoteLoading(false);
	if (!p3dlite.uploading && !p3dlite.checking && !p3dlite.repairing && !((p3dlite.xhr1 && p3dlite.xhr1.readyState != 4) || (p3dlite.xhr2 && p3dlite.xhr2.readyState != 4))) {p3dliteDisplayQuoteLoading(false);}
	if (response.model) {
		if (response.model.error) p3dliteShowError(response.model.error); //soft error
		jQuery('#stats-material-volume').html(response.model.material_volume.toFixed(2));
		jQuery('#stats-box-volume').html(response.model.box_volume.toFixed(2));
		jQuery('#stats-surface-area').html(response.model.surface_area.toFixed(2));
		jQuery('#stats-width').html(response.model.x_dim.toFixed(2));
		jQuery('#stats-length').html(response.model.y_dim.toFixed(2));
		jQuery('#stats-height').html(response.model.z_dim.toFixed(2));
		jQuery('#stats-weight').html(response.model.weight.toFixed(2));
		//jQuery('#stats-hours').html((parseFloat(p3dlite.print_time)/3600).toFixed(1));

		jQuery('#scale_x').val(response.model.x_dim.toFixed(2));
		jQuery('#scale_y').val(response.model.y_dim.toFixed(2));
		jQuery('#scale_z').val(response.model.z_dim.toFixed(2));




		p3dliteDisplayStats(true)
	}

	if ( p3dliteCheckPrintability() ) {
		if ( (!p3dlite.analyse_error && !((p3dlite.xhr1 && p3dlite.xhr1.readyState != 4) || (p3dlite.xhr2 && p3dlite.xhr2.readyState != 4)) && !p3dlite.checking && !p3dlite.uploading && !p3dlite.repairing) || (printer_type != 'fff' && printer_type != 'dlp') ) {

			if (p3dlite.pricing!='request') {
				p3dliteDisplayPrice(true);
			}
			if (!p3dlite.uploading && !p3dlite.repairing) {
				p3dliteDisplayAddToCart(true);
			}
			if (!p3dlite.uploading || !p3dlite.filereader_supported) {
				p3dliteDisplayAddToCart(true);
			}

			//jQuery('p.price meta[itemprop=price]').attr('content',response.price);
			jQuery('p.price span.amount').html(response.html_price);
		}
	}

	window.wp.event_manager.doAction( 'p3dlite.showResponse');
}
function p3dliteCalculateWeight(material_volume) {
	var density = parseFloat(jQuery('input[name=product_filament]:checked').attr('data-density'));
	var weight = material_volume*density;
	return weight.toFixed(2);
}


function p3dliteDisplayUserDefinedProgressBar(show) {
	if(show) {
		jQuery('#p3dlite-file-loading').show();
	}
	else {
		if (!p3dlite.repairing) {
			jQuery('#p3dlite-file-loading').hide();
		}
	}
}



function p3dliteDisplayConsole(show) {
	if (show) {
		jQuery('#p3dlite-console').show();
	}
	else {
		jQuery('#p3dlite-console').hide();
	}
}

function p3dliteDisplayAddToCart(show) {
	if (show && !p3dlite.fatal_error) {
		jQuery('#add-cart-container').css('visibility', 'visible');
	}
	else {
		jQuery('#add-cart-container').css('visibility', 'hidden');
	}
}


function p3dliteDisplayQuoteLoading(show) {
	if (show) {
		jQuery('#p3dlite-quote-loading').css('visibility', 'visible');
	}
	else {
		jQuery('#p3dlite-quote-loading').css('visibility', 'hidden');
	}
}

function p3dliteDisplayPrice(show) {
	if (show && !p3dlite.fatal_error && p3dlite.pricing!='request') {
		jQuery('p.price').css('visibility', 'visible');
	}
	else {
		jQuery('p.price').css('visibility', 'hidden');
	}
}

function p3dliteDisplayStats(show) {
	if (show) {
		jQuery('.p3dlite-stats').css('visibility','visible');
	}
	else {
		jQuery('.p3dlite-stats').css('visibility','hidden');
	}
}




function p3dliteDetectIE() {
	var ua = window.navigator.userAgent;

	var msie = ua.indexOf('MSIE ');
	if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
       // IE 12 => return version number
       return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
   }

    // other browser
    return false;
}



function p3dliteRemoveGroupObjectByName(name) {
	var o = p3dlite.group.getObjectByName(name);
	p3dlite.group.remove( o )
}

function p3dliteDrawPrinterBox() {
	if (!p3dlite.model_mesh) return; //basically we build the box around the model
	p3dliteRemoveGroupObjectByName('printer');
	p3dliteRemoveGroupObjectByName('printer bed');
	p3dliteRemoveGroupObjectByName('printer roof');
	var printer_id = jQuery('input[name=product_printer]:checked').data('id') 
	var unit = jQuery('input[name=p3dlite_unit]:checked').val();
	var platform_shape = jQuery('input[name=product_printer]:checked').data('platform_shape') ;
	var printer_radius = parseFloat(jQuery('input[name=product_printer]:checked').data('diameter'))/2 ;


	var printer_dim=new Array();
	printer_dim.x=jQuery('#p3dlite_printer_'+printer_id).data('length')
	printer_dim.y=jQuery('#p3dlite_printer_'+printer_id).data('width')
	printer_dim.z=jQuery('#p3dlite_printer_'+printer_id).data('height')

	var min_z = p3dlite.boundingBox.min.z;
	var model_ydim = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
	var model_xdim = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
	var model_zdim = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;

	if (platform_shape=='rectangle' || platform_shape.length==0) {
		//xy rotation
		if (model_xdim > model_ydim && printer_dim.y > printer_dim.x) {
			tmpvar=printer_dim.x;
			printer_dim.x=printer_dim.y;
			printer_dim.y=tmpvar;
		}
	
		if (model_ydim > model_xdim && printer_dim.x > printer_dim.y) {
			tmpvar=printer_dim.y;
			printer_dim.y=printer_dim.x;
			printer_dim.x=tmpvar;
		}

	}

	var material = new THREEP3DL.LineBasicMaterial({
		color: parseInt(p3dlite.printer_color, 16)
	});




//	p3dlite.model_mesh.geometry.computeBoundingSphere();
//	var radius = p3dlite.model_mesh.geometry.boundingSphere.radius;
	if (platform_shape=='circle') {
		var segmentCount = 32,
		radius = printer_radius,
		geometry = new THREEP3DL.Geometry(),
		material = new THREEP3DL.LineBasicMaterial({ color: parseInt(p3dlite.printer_color, 16) });

		for (var i = 0; i <= segmentCount; i++) {
			var theta = (i / segmentCount) * Math.PI * 2;
			geometry.vertices.push(
				new THREEP3DL.Vector3(
				Math.cos(theta) * radius,
				Math.sin(theta) * radius,
				0));            
		}
		p3dlite.model_printer_bed = new THREEP3DL.Line(geometry, material);
		p3dlite.model_printer_bed.position.set( 0, min_z, 0 );
		p3dlite.model_printer_bed.rotation.z = 90 * Math.PI/180;
		p3dlite.model_printer_bed.rotation.x = -90 * Math.PI/180;
		p3dlite.model_printer_bed.name = "printer bed";
		p3dlite.model_printer_bed.geometry.computeBoundingBox();
		if (p3dlite.show_printer_box!='on' || p3dlite.printer_color=='') {
			p3dlite.model_printer_bed.visible=false;
		}

		p3dlite.model_printer_roof = p3dlite.model_printer_bed.clone();
		p3dlite.model_printer_roof.position.set( 0, min_z+printer_dim.z, 0 );
		p3dlite.model_printer_roof.name = "printer roof";
		p3dlite.model_printer_roof.geometry.computeBoundingBox();

		p3dlite.scene.add(p3dlite.model_printer_bed);
		p3dlite.scene.add(p3dlite.model_printer_roof);
		p3dlite.group.add( p3dlite.model_printer_bed );
		p3dlite.group.add( p3dlite.model_printer_roof );

	}

	else {
		var geometry = new THREEP3DL.Geometry();
		geometry.vertices.push(
	 		new THREEP3DL.Vector3( - printer_dim.x/2, min_z, - printer_dim.y/2),
	 		new THREEP3DL.Vector3( - printer_dim.x/2, min_z, printer_dim.y/2),
	   		new THREEP3DL.Vector3( printer_dim.x/2, min_z, printer_dim.y/2),
	   		new THREEP3DL.Vector3( printer_dim.x/2, min_z, - printer_dim.y/2),
	 		new THREEP3DL.Vector3( - printer_dim.x/2, min_z, - printer_dim.y/2),

	 		new THREEP3DL.Vector3( - printer_dim.x/2, min_z + printer_dim.z, - printer_dim.y/2),
	 		new THREEP3DL.Vector3( - printer_dim.x/2, min_z + printer_dim.z, printer_dim.y/2),
	   		new THREEP3DL.Vector3( printer_dim.x/2, min_z + printer_dim.z, printer_dim.y/2),
	   		new THREEP3DL.Vector3( printer_dim.x/2, min_z + printer_dim.z, - printer_dim.y/2),
	 		new THREEP3DL.Vector3( - printer_dim.x/2, min_z + printer_dim.z, - printer_dim.y/2),

	 		new THREEP3DL.Vector3(  printer_dim.x/2, min_z + printer_dim.z, - printer_dim.y/2),
	 		new THREEP3DL.Vector3(  printer_dim.x/2, min_z, - printer_dim.y/2),
	 		new THREEP3DL.Vector3(  printer_dim.x/2, min_z,  printer_dim.y/2),
	 		new THREEP3DL.Vector3(  printer_dim.x/2, min_z + printer_dim.z,  printer_dim.y/2),
	 		new THREEP3DL.Vector3(  -printer_dim.x/2, min_z + printer_dim.z,  printer_dim.y/2),
	 		new THREEP3DL.Vector3(  -printer_dim.x/2, min_z,  printer_dim.y/2)


		);
		p3dlite.model_printer = new THREEP3DL.Line( geometry, material );



		p3dlite.model_printer.name = "printer";
		p3dlite.model_printer.geometry.computeBoundingBox();
		if (p3dlite.show_printer_box!='on' || p3dlite.printer_color=='') {
			p3dlite.model_printer.visible=false;
		}

		p3dlite.scene.add( p3dlite.model_printer );
		p3dlite.group.add( p3dlite.model_printer );
	}

 	window.wp.event_manager.doAction( 'p3dlite.drawPrinterBox');
}



function p3dliteSignedVolume(p1, p2, p3) {
	if (p1 && p2 && p3) {
		v321 = p3[0]*p2[1]*p1[2];
		v231 = p2[0]*p3[1]*p1[2];
		v312 = p3[0]*p1[1]*p2[2];
		v132 = p1[0]*p3[1]*p2[2];
		v213 = p2[0]*p1[1]*p3[2];
		v123 = p1[0]*p2[1]*p3[2];

		return (1.0/6.0)*(-v321 + v231 + v312 - v132 - v213 + v123);
	}
}

function p3dliteSurfaceArea(p1, p2, p3) {
	if (p1 && p2 && p3) {
		ax = p2[0] - p1[0];
		ay = p2[1] - p1[1];
		az = p2[2] - p1[2];
		bx = p3[0] - p1[0];
		by = p3[1] - p1[1];
		bz = p3[2] - p1[2];
		cx = ay*bz - az*by;
		cy = az*bx - ax*bz;
		cz = ax*by - ay*bx;
		return 0.5 * Math.sqrt(cx*cx + cy*cy + cz*cz);
	}
}    
THREEP3DL.OBJLoader.prototype.parse = function ( text ) {
		//console.time( 'OBJLoader' );

		p3dlite.model_total_volume=0;
		p3dlite.model_surface_area=0;

		var state = this._createParserState();

		if ( text.indexOf( '\r\n' ) !== - 1 ) {

			// This is faster than String.split with regex that splits on both
			text = text.replace( /\r\n/g, '\n' );

		}

		if ( text.indexOf( '\\\n' ) !== - 1) {

			// join lines separated by a line continuation character (\)
			text = text.replace( /\\\n/g, '' );

		}

		var lines = text.split( '\n' );
		var line = '', lineFirstChar = '', lineSecondChar = '';
		var lineLength = 0;
		var result = [];

		// Faster to just trim left side of the line. Use if available.
		var trimLeft = ( typeof ''.trimLeft === 'function' );

		var v=0;
		var vertexes = new Array();

		//var lines = data.split(/[ \t]*\r?\n[ \t]*/);
		for(var i=0; i<lines.length; i++) {
			var line = lines[i];
			var tokens = line.split(/[ \t]+/);
			if(tokens.length > 0) {
				var keyword = tokens[0];
				switch(keyword) {
				case 'v':
					if(tokens.length > 3) {
						vertexes[v] = new Array();
						for(var j=1; j<4; j++) {
	                                                vertexes[v][j-1] = parseFloat( tokens[j] );
						}
					        v++;
					}
					break;
				case '#':
					// ignore comments
				default:
					break;
	
				}
			}
		}



		for ( var i = 0, l = lines.length; i < l; i ++ ) {
			var tokens = line.split(/[ \t]+/);

			line = lines[ i ];

			line = trimLeft ? line.trimLeft() : line.trim();

			lineLength = line.length;

			if ( lineLength === 0 ) continue;

			lineFirstChar = line.charAt( 0 );

			// @todo invoke passed in handler if any
			if ( lineFirstChar === '#' ) continue;

			if ( lineFirstChar === 'v' ) {

				lineSecondChar = line.charAt( 1 );

				if ( lineSecondChar === ' ' && ( result = this.regexp.vertex_pattern.exec( line ) ) !== null ) {

					// 0                  1      2      3
					// ["v 1.0 2.0 3.0", "1.0", "2.0", "3.0"]

					state.vertices.push(
						parseFloat( result[ 1 ] ),
						parseFloat( result[ 2 ] ),
						parseFloat( result[ 3 ] )
					);

				} else if ( lineSecondChar === 'n' && ( result = this.regexp.normal_pattern.exec( line ) ) !== null ) {

					// 0                   1      2      3
					// ["vn 1.0 2.0 3.0", "1.0", "2.0", "3.0"]

					state.normals.push(
						parseFloat( result[ 1 ] ),
						parseFloat( result[ 2 ] ),
						parseFloat( result[ 3 ] )
					);

				} else if ( lineSecondChar === 't' && ( result = this.regexp.uv_pattern.exec( line ) ) !== null ) {

					// 0               1      2
					// ["vt 0.1 0.2", "0.1", "0.2"]

					state.uvs.push(
						parseFloat( result[ 1 ] ),
						parseFloat( result[ 2 ] )
					);

				} else {

					throw new Error( "Unexpected vertex/normal/uv line: '" + line  + "'" );

				}

			} else if ( lineFirstChar === "f" ) {

				if(tokens.length > 3 && tokens[0]=='f') {
				        var tetrahedron = new Array();
					for(var j=1; j<tokens.length; j++) {
						var refs = tokens[j].split('/');
						var index = parseInt(refs[0]) - 1;
						var vindex = 0;
                                                if (index<0) vindex = vertexes.length+index;
						else vindex = index;
                                                tetrahedron[j] = new Array();
                                                tetrahedron[j] = vertexes[vindex];
                                                if (tokens.length == 4 && j==3) {
                                                 
                                                 var temp_vol = parseFloat(p3dliteSignedVolume (tetrahedron[1], tetrahedron[2], tetrahedron[3]));
						 var surface_area = parseFloat(p3dliteSurfaceArea (tetrahedron[1], tetrahedron[2], tetrahedron[3]));
                                                 if(!isNaN(temp_vol)) {
                                                  p3dlite.model_total_volume+=temp_vol;
                                                 }
                                                 if(!isNaN(surface_area)) {
                                                  p3dlite.model_surface_area+=surface_area;
                                                 }
						}

						else if (tokens.length > 4) {


                                                if (tokens.length == 5 && j==4) {
                                                 var temp_vol1 = parseFloat(p3dliteSignedVolume (tetrahedron[1], tetrahedron[2], tetrahedron[3]));
                                                 var temp_vol2 = parseFloat(p3dliteSignedVolume (tetrahedron[1], tetrahedron[3], tetrahedron[4]));

						 var surface_area1 = parseFloat(p3dliteSurfaceArea (tetrahedron[1], tetrahedron[2], tetrahedron[3]));
						 var surface_area2 = parseFloat(p3dliteSurfaceArea (tetrahedron[1], tetrahedron[3], tetrahedron[4]));

                                                 if(!isNaN(temp_vol1)) {
                                                  p3dlite.model_total_volume+=temp_vol1;
                                                 }
                                                 if(!isNaN(temp_vol2)) {
                                                  p3dlite.model_total_volume+=temp_vol2;
                                                 }

                                                 if(!isNaN(surface_area1)) {
                                                  p3dlite.model_surface_area+=surface_area1;
                                                 }
                                                 if(!isNaN(surface_area2)) {
                                                  p3dlite.model_surface_area+=surface_area2;
                                                 }
                                                }

                                                 if (p3dlite.server_triangulation=='on') {
                                                  p3dlite.triangulation_required=true;

                                                 }
						}
					}
				}

				if ( ( result = this.regexp.face_vertex_uv_normal.exec( line ) ) !== null ) {

					// f vertex/uv/normal vertex/uv/normal vertex/uv/normal
					// 0                        1    2    3    4    5    6    7    8    9   10         11         12
					// ["f 1/1/1 2/2/2 3/3/3", "1", "1", "1", "2", "2", "2", "3", "3", "3", undefined, undefined, undefined]

					state.addFace(
						result[ 1 ], result[ 4 ], result[ 7 ], result[ 10 ],
						result[ 2 ], result[ 5 ], result[ 8 ], result[ 11 ],
						result[ 3 ], result[ 6 ], result[ 9 ], result[ 12 ]
					);

				} else if ( ( result = this.regexp.face_vertex_uv.exec( line ) ) !== null ) {

					// f vertex/uv vertex/uv vertex/uv
					// 0                  1    2    3    4    5    6   7          8
					// ["f 1/1 2/2 3/3", "1", "1", "2", "2", "3", "3", undefined, undefined]

					state.addFace(
						result[ 1 ], result[ 3 ], result[ 5 ], result[ 7 ],
						result[ 2 ], result[ 4 ], result[ 6 ], result[ 8 ]
					);

				} else if ( ( result = this.regexp.face_vertex_normal.exec( line ) ) !== null ) {

					// f vertex//normal vertex//normal vertex//normal
					// 0                     1    2    3    4    5    6   7          8
					// ["f 1//1 2//2 3//3", "1", "1", "2", "2", "3", "3", undefined, undefined]

					state.addFace(
						result[ 1 ], result[ 3 ], result[ 5 ], result[ 7 ],
						undefined, undefined, undefined, undefined,
						result[ 2 ], result[ 4 ], result[ 6 ], result[ 8 ]
					);

				} else if ( ( result = this.regexp.face_vertex.exec( line ) ) !== null ) {

					// f vertex vertex vertex
					// 0            1    2    3   4
					// ["f 1 2 3", "1", "2", "3", undefined]

					state.addFace(
						result[ 1 ], result[ 2 ], result[ 3 ], result[ 4 ]
					);

				} else {

					throw new Error( "Unexpected face line: '" + line  + "'" );

				}

			} else if ( lineFirstChar === "l" ) {

				var lineParts = line.substring( 1 ).trim().split( " " );
				var lineVertices = [], lineUVs = [];

				if ( line.indexOf( "/" ) === - 1 ) {

					lineVertices = lineParts;

				} else {

					for ( var li = 0, llen = lineParts.length; li < llen; li ++ ) {

						var parts = lineParts[ li ].split( "/" );

						if ( parts[ 0 ] !== "" ) lineVertices.push( parts[ 0 ] );
						if ( parts[ 1 ] !== "" ) lineUVs.push( parts[ 1 ] );

					}

				}
				state.addLineGeometry( lineVertices, lineUVs );

			} else if ( ( result = this.regexp.object_pattern.exec( line ) ) !== null ) {

				// o object_name
				// or
				// g group_name

				// WORKAROUND: https://bugs.chromium.org/p/v8/issues/detail?id=2869
				// var name = result[ 0 ].substr( 1 ).trim();
				var name = ( " " + result[ 0 ].substr( 1 ).trim() ).substr( 1 );

				state.startObject( name );

			} else if ( this.regexp.material_use_pattern.test( line ) ) {

				// material

				state.object.startMaterial( line.substring( 7 ).trim(), state.materialLibraries );

			} else if ( this.regexp.material_library_pattern.test( line ) ) {

				// mtl file

				state.materialLibraries.push( line.substring( 7 ).trim() );

			} else if ( ( result = this.regexp.smoothing_pattern.exec( line ) ) !== null ) {

				// smooth shading

				// @todo Handle files that have varying smooth values for a set of faces inside one geometry,
				// but does not define a usemtl for each face set.
				// This should be detected and a dummy material created (later MultiMaterial and geometry groups).
				// This requires some care to not create extra material on each smooth value for "normal" obj files.
				// where explicit usemtl defines geometry groups.
				// Example asset: examples/models/obj/cerberus/Cerberus.obj

				var value = result[ 1 ].trim().toLowerCase();
				state.object.smooth = ( value === '1' || value === 'on' );

				var material = state.object.currentMaterial();
				if ( material ) {

					material.smooth = state.object.smooth;

				}

			} else {

				// Handle null terminated files without exception
				if ( line === '\0' ) continue;

				throw new Error( "Unexpected line: '" + line  + "'" );

			}

		}

		state.finalize();

		var container = new THREEP3DL.Group();
		container.materialLibraries = [].concat( state.materialLibraries );

		for ( var i = 0, l = state.objects.length; i < l; i ++ ) {

			var object = state.objects[ i ];
			var geometry = object.geometry;
			var materials = object.materials;
			var isLine = ( geometry.type === 'Line' );

			// Skip o/g line declarations that did not follow with any faces
			if ( geometry.vertices.length === 0 ) continue;

			var buffergeometry = new THREEP3DL.BufferGeometry();

			buffergeometry.addAttribute( 'position', new THREEP3DL.BufferAttribute( new Float32Array( geometry.vertices ), 3 ) );

			if ( geometry.normals.length > 0 ) {

				buffergeometry.addAttribute( 'normal', new THREEP3DL.BufferAttribute( new Float32Array( geometry.normals ), 3 ) );

			} else {

				buffergeometry.computeVertexNormals();

			}

			if ( geometry.uvs.length > 0 ) {

				buffergeometry.addAttribute( 'uv', new THREEP3DL.BufferAttribute( new Float32Array( geometry.uvs ), 2 ) );

			}

			// Create materials

			var createdMaterials = [];

			for ( var mi = 0, miLen = materials.length; mi < miLen ; mi++ ) {

				var sourceMaterial = materials[mi];
				var material = undefined;

				if ( this.materials !== null ) {

					material = this.materials.create( sourceMaterial.name );

					// mtl etc. loaders probably can't create line materials correctly, copy properties to a line material.
					if ( isLine && material && ! ( material instanceof THREEP3DL.LineBasicMaterial ) ) {

						var materialLine = new THREEP3DL.LineBasicMaterial();
						materialLine.copy( material );
						material = materialLine;

					}

				}

				if ( ! material ) {

					material = ( ! isLine ? new THREEP3DL.MeshPhongMaterial() : new THREEP3DL.LineBasicMaterial() );
					material.name = sourceMaterial.name;

				}

				material.shading = sourceMaterial.smooth ? THREEP3DL.SmoothShading : THREEP3DL.FlatShading;

				createdMaterials.push(material);

			}

			// Create mesh

			var mesh;

			if ( createdMaterials.length > 1 ) {

				for ( var mi = 0, miLen = materials.length; mi < miLen ; mi++ ) {

					var sourceMaterial = materials[mi];
					buffergeometry.addGroup( sourceMaterial.groupStart, sourceMaterial.groupCount, mi );

				}

				var multiMaterial = new THREEP3DL.MultiMaterial( createdMaterials );
				mesh = ( ! isLine ? new THREEP3DL.Mesh( buffergeometry, multiMaterial ) : new THREEP3DL.LineSegments( buffergeometry, multiMaterial ) );

			} else {

				mesh = ( ! isLine ? new THREEP3DL.Mesh( buffergeometry, createdMaterials[ 0 ] ) : new THREEP3DL.LineSegments( buffergeometry, createdMaterials[ 0 ] ) );
			}

			mesh.name = object.name;

			container.add( mesh );

		}

		//console.timeEnd( 'OBJLoader' );

		return container;

}
THREEP3DL.STLLoader.prototype.parseASCII = function ( data ) {
		var geometry, length, normal, patternFace, patternNormal, patternVertex, result, text;
		geometry = new THREEP3DL.BufferGeometry();
		patternFace = /facet([\s\S]*?)endfacet/g;

		p3dlite.model_total_volume=0;
		p3dlite.model_surface_area=0;


		var vertices = new Array();
		var normals = new Array();

		while ( ( result = patternFace.exec( data ) ) !== null ) {

			text = result[ 0 ];
			patternNormal = /normal[\s]+([\-+]?[0-9]+\.?[0-9]*([eE][\-+]?[0-9]+)?)+[\s]+([\-+]?[0-9]*\.?[0-9]+([eE][\-+]?[0-9]+)?)+[\s]+([\-+]?[0-9]*\.?[0-9]+([eE][\-+]?[0-9]+)?)+/g;

			while ( ( result = patternNormal.exec( text ) ) !== null ) {

				normal = new THREEP3DL.Vector3( parseFloat( result[ 1 ] ), parseFloat( result[ 3 ] ), parseFloat( result[ 5 ] ) );

				normals.push(result[ 1 ]);
				normals.push(result[ 3 ]);
				normals.push(result[ 5 ]);
			}


			patternVertex = /vertex[\s]+([\-+]?[0-9]+\.?[0-9]*([eE][\-+]?[0-9]+)?)+[\s]+([\-+]?[0-9]*\.?[0-9]+([eE][\-+]?[0-9]+)?)+[\s]+([\-+]?[0-9]*\.?[0-9]+([eE][\-+]?[0-9]+)?)+/g;
			tetrahedron = new Array();
			var i = 1;

			while ( ( result = patternVertex.exec( text ) ) !== null ) {

				tetrahedron[i] = new Array();
				tetrahedron[i].push(parseFloat( result[ 1 ] ));
				tetrahedron[i].push(parseFloat( result[ 3 ] ));
				tetrahedron[i].push(parseFloat( result[ 5 ] ));

				vertices.push(parseFloat(result[ 1 ]));
				vertices.push(parseFloat(result[ 3 ]));
				vertices.push(parseFloat(result[ 5 ]));

				i++;
			}

			p3dlite.model_total_volume+=p3dliteSignedVolume(tetrahedron[1], tetrahedron[2], tetrahedron[3]);
			p3dlite.model_surface_area+=p3dliteSurfaceArea(tetrahedron[1], tetrahedron[2], tetrahedron[3]);


		}

		var vertices32 = new Float32Array(vertices);
		var normals32 = new Float32Array(normals);
		geometry.addAttribute( 'position', new THREEP3DL.BufferAttribute( vertices32, 3 ) );
		geometry.addAttribute( 'normal', new THREEP3DL.BufferAttribute( normals32, 3 ) );

		geometry.computeBoundingBox();
		geometry.computeBoundingSphere();

		return geometry;
}

THREEP3DL.STLLoader.prototype.parseBinary = function ( data ) {
		var reader = new DataView( data );
		var faces = reader.getUint32( 80, true );

		var r, g, b, hasColors = false, colors;
		var defaultR, defaultG, defaultB, alpha;

		p3dlite.model_total_volume=0;
		p3dlite.model_surface_area=0;

		// process STL header
		// check for default color in header ("COLOR=rgba" sequence).

		for ( var index = 0; index < 80 - 10; index ++ ) {

			if ( ( reader.getUint32( index, false ) == 0x434F4C4F /*COLO*/ ) &&
				( reader.getUint8( index + 4 ) == 0x52 /*'R'*/ ) &&
				( reader.getUint8( index + 5 ) == 0x3D /*'='*/ ) ) {

				hasColors = true;
				colors = new Float32Array( faces * 3 * 3 );

				defaultR = reader.getUint8( index + 6 ) / 255;
				defaultG = reader.getUint8( index + 7 ) / 255;
				defaultB = reader.getUint8( index + 8 ) / 255;
				alpha = reader.getUint8( index + 9 ) / 255;

			}

		}

		var dataOffset = 84;
		var faceLength = 12 * 4 + 2;

		var offset = 0;

		var geometry = new THREEP3DL.BufferGeometry();

		var vertices = new Float32Array( faces * 3 * 3 );
		var normals = new Float32Array( faces * 3 * 3 );

		for ( var face = 0; face < faces; face ++ ) {

			var start = dataOffset + face * faceLength;
			var normalX = reader.getFloat32( start, true );
			var normalY = reader.getFloat32( start + 4, true );
			var normalZ = reader.getFloat32( start + 8, true );

			if ( hasColors ) {

				var packedColor = reader.getUint16( start + 48, true );

				if ( ( packedColor & 0x8000 ) === 0 ) {

					// facet has its own unique color

					r = ( packedColor & 0x1F ) / 31;
					g = ( ( packedColor >> 5 ) & 0x1F ) / 31;
					b = ( ( packedColor >> 10 ) & 0x1F ) / 31;

				} else {

					r = defaultR;
					g = defaultG;
					b = defaultB;

				}

			}

			var tetrahedron = new Array();
			for ( var i = 1; i <= 3; i ++ ) {

				var vertexstart = start + i * 12;

				vertices[ offset ] = reader.getFloat32( vertexstart, true );
				vertices[ offset + 1 ] = reader.getFloat32( vertexstart + 4, true );
				vertices[ offset + 2 ] = reader.getFloat32( vertexstart + 8, true );

				tetrahedron[i] = new Array();
				tetrahedron[i].push(vertices[ offset ]);
				tetrahedron[i].push(vertices[ offset + 1 ]);
				tetrahedron[i].push(vertices[ offset + 2 ]);

				

				normals[ offset ] = normalX;
				normals[ offset + 1 ] = normalY;
				normals[ offset + 2 ] = normalZ;

				if ( hasColors ) {

					colors[ offset ] = r;
					colors[ offset + 1 ] = g;
					colors[ offset + 2 ] = b;

				}

				offset += 3;

			}

			p3dlite.model_total_volume+=p3dliteSignedVolume(tetrahedron[1], tetrahedron[2], tetrahedron[3]);
			p3dlite.model_surface_area+=p3dliteSurfaceArea(tetrahedron[1], tetrahedron[2], tetrahedron[3]);


		}

		geometry.addAttribute( 'position', new THREEP3DL.BufferAttribute( vertices, 3 ) );
		geometry.addAttribute( 'normal', new THREEP3DL.BufferAttribute( normals, 3 ) );

		if ( hasColors ) {

			geometry.addAttribute( 'color', new THREEP3DL.BufferAttribute( colors, 3 ) );
			geometry.hasColors = true;
			geometry.alpha = alpha;

		}

		return geometry;

}

function p3dliteDialogCheck() {
//file not selected fix
	if (p3dlite.file_selected>0)
		jQuery('#p3dlite-container input[type=file]').parent().css('z-index', '999')
	p3dlite.file_selected++;
}

function p3dliteGetMaxScale(model_dim, printer_dim, printer_radius, platform_shape ) {
	var mesh_diagonal = Math.sqrt(model_dim.x * model_dim.x + model_dim.y * model_dim.y);
	var model_radius = mesh_diagonal/2; //model xy radius*/

	var max_printer_side = Math.max(printer_dim.x, printer_dim.y);
	var min_printer_side = Math.min(printer_dim.x, printer_dim.y);
	var max_model_side = Math.max(model_dim.x, model_dim.y);
	var min_model_side = Math.min(model_dim.x, model_dim.y);

	if (platform_shape=='circle') {
		max_model_side = model_radius;
		min_model_side = min_model_side/2;

		max_printer_side = printer_radius;
		min_printer_side = printer_radius;
	}

	var height_diff = printer_dim.z/model_dim.z;
	var max_side_diff = max_printer_side/max_model_side;
	var min_side_diff = min_printer_side/min_model_side;
	var side_diff = Math.min(max_side_diff, min_side_diff, height_diff);
	var max_scale = (side_diff*100)/p3dliteGetUnitMultiplier();

	return max_scale;
}

function p3dliteInitScaleSlider() {
	window.wp.event_manager.doAction( 'p3dlite.p3dliteInitScaleSlider_start');

	if (!p3dlite.model_mesh) return false;
	var p3dliteRangeSlider = document.getElementById('p3dlite-scale');
	var printer_dim=new Array();
	var printer_type = jQuery('input[name=product_printer]:checked').data('type');

	printer_dim.x=jQuery('input[name=product_printer]:checked').data('length');
	printer_dim.y=jQuery('input[name=product_printer]:checked').data('width');
	printer_dim.z=jQuery('input[name=product_printer]:checked').data('height');
	var platform_shape = jQuery('input[name=product_printer]:checked').data('platform_shape') ;
	var printer_radius = parseFloat(jQuery('input[name=product_printer]:checked').data('diameter'))/2 ;

	
	var model_dim = new Array();
	model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
	model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
	model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;

	var max_scale = p3dliteGetMaxScale(model_dim, printer_dim, printer_radius, platform_shape);

	if (p3dlite.auto_scale=='0') {
		if (max_scale<100) {
			max_scale = 100;
			jQuery('#p3dlite-model-message-toolarge').show();
		}
		else {
			jQuery('#p3dlite-model-message-toolarge').hide();
		}
	}

	if (isNaN(max_scale)) return false;


	var new_printer_id = 0;

	if (p3dlite.auto_scale=='on') {
		if (max_scale < 100) {
			//check compatible printers
			var current_material = jQuery('input[name=product_filament]:checked');
			var current_printer = jQuery('input[name=product_printer]:checked');
			var material_id = current_material.data('id');
			var printer_id = current_printer.data('id');


			jQuery('input[name=product_printer]').each(function() {
				var materials = jQuery(this).data('materials')+'';
				var materials_array = materials.split(',');
				if (materials.length>0 && jQuery.inArray(material_id+'', materials_array)==-1) {
				}
				else {
					if (jQuery(this).data('id')!=printer_id) {

						var new_printer_dim=new Array();
						new_printer_dim.x=jQuery(this).data('length');
						new_printer_dim.y=jQuery(this).data('width');
						new_printer_dim.z=jQuery(this).data('height');

						var new_max_scale = p3dliteGetMaxScale(model_dim, new_printer_dim, parseFloat(jQuery(this).data('diameter'))/2, jQuery(this).data('platform_shape'));

						if (new_max_scale>=100) {

							new_printer_id = jQuery(this).data('id');
						}
					}
				}
			});
			jQuery('#p3dlite-model-message-scale').show();
		}
		else jQuery('#p3dlite-model-message-scale').hide();
	}

	if (typeof(p3dliteRangeSlider.noUiSlider)=='undefined') {

		//if (max_scale < 100) p3dlite.resize_scale = max_scale;

		noUiSlider.create(p3dliteRangeSlider, {
			start: [ 100 ],
			range: {
				'min': [ 0.01 ],
				'max': [ max_scale ]
			}
		});
		var p3dliteRangeSliderValueElement = document.getElementById('p3dlite-slider-range-value');

		p3dliteRangeSlider.noUiSlider.on('update', function( values, handle ) {
			if (!p3dliteCheckMinSide(values[handle])) {
				values[handle]=p3dliteGetMinScale()*100;

			}

			p3dliteRangeSliderValueElement.value = values[handle];
			p3dlite.resize_scale = values[handle]/100;
			jQuery('#p3dlite-resize-scale').val(p3dlite.resize_scale);
			printer_id=jQuery('input:radio[name=product_printer]:checked').data('id');

			if (p3dlite.auto_scale=='0') {
				var model_dim_resized = new Array();

				model_dim_resized.x = model_dim.x * p3dlite.resize_scale;
				model_dim_resized.y = model_dim.y * p3dlite.resize_scale;
				model_dim_resized.z = model_dim.z * p3dlite.resize_scale;

				var max_scale = p3dliteGetMaxScale(model_dim_resized, printer_dim, printer_radius, platform_shape);

				if (max_scale>=100) {
					jQuery('#p3dlite-model-message-toolarge').hide();
				}
				else if (max_scale<100) {
					jQuery('#p3dlite-model-message-toolarge').show();
				}
			}

			p3dliteResizeModel(p3dlite.resize_scale);
			p3dliteGetStats();
			jQuery('#p3dlite-scale-x').val(jQuery('#scale_x').val());
			jQuery('#p3dlite-scale-y').val(jQuery('#scale_y').val());
			jQuery('#p3dlite-scale-z').val(jQuery('#scale_z').val());



		});

	}
	else {

		p3dliteRangeSlider.noUiSlider.updateOptions({
			start: [ 100 ],
			range: {
				'min': 0.01,
				'max': max_scale
			}
		});

	}

	if (new_printer_id>0) {
		p3dliteSelectPrinter(jQuery('#p3dlite_printer_'+new_printer_id).closest('li'));
		jQuery('#p3dlite-model-message-fitting-priner').show();
		return;
	}
	else {
		jQuery('#p3dlite-model-message-fitting-priner').hide();
	}

	p3dliteGetStats();
	window.wp.event_manager.doAction( 'p3dlite.p3dliteInitScaleSlider_end');
}

function p3dliteInitScaling() {
	p3dliteInitScaleSlider();
	var p3dliteRangeSlider = document.getElementById('p3dlite-scale');

	if (typeof(p3dliteRangeSlider.noUiSlider)!=='undefined') {

		p3dliteRangeSlider.noUiSlider.set(p3dlite.default_scale)
	}

}



function p3dliteUpdateScale (value) {
	if (jQuery('.noUi-active').length==0) {
		clearInterval(p3dlite.refresh_interval1);	
		if (value=='') {
			var p3dliteRangeSlider = document.getElementById('p3dlite-scale');
			value = p3dliteRangeSlider.noUiSlider.get();
		}
		var model_dim = new Array();
		model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;
		p3dlite.refresh_interval1_running = false;
		p3dlite.resize_scale = value/100;
		jQuery('#p3dlite-resize-scale').val(p3dlite.resize_scale);
		printer_id=jQuery('input:radio[name=product_printer]:checked').data('id');
		p3dliteResizeModel(p3dlite.resize_scale);
		p3dliteGetStats();
		//p3dliteAnalyseModel(jQuery('#pa_p3dlite_model').val());
	}
}

function p3dliteUpdateDimensions (obj) {
	window.wp.event_manager.doAction( 'p3dlite.p3dliteUpdateDimensions_start');
	var cur_value=jQuery(obj).val();
	if (isNaN(cur_value)) return;

	var model_dim = new Array();
	model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
	model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
	model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;
	
	if (jQuery(obj).attr('id')=='scale_x') prev_value = model_dim.x;
	if (jQuery(obj).attr('id')=='scale_y') prev_value = model_dim.y;
	if (jQuery(obj).attr('id')=='scale_z') prev_value = model_dim.z;

	jQuery('#p3dlite-scale-x').val(jQuery('#scale_x').val());
	jQuery('#p3dlite-scale-y').val(jQuery('#scale_y').val());
	jQuery('#p3dlite-scale-z').val(jQuery('#scale_z').val());

	var scale = (cur_value*10)/prev_value/p3dliteGetUnitMultiplier();

	var p3dliteRangeSlider = document.getElementById('p3dlite-scale');
	if (typeof(p3dliteRangeSlider.noUiSlider)!=='undefined') {
		p3dliteRangeSlider.noUiSlider.set(scale*100)
		p3dlite.resize_scale = scale;
	}

}

function p3dliteResizeModel(scale) {
	if (p3dlite.resize_on_scale!='on') return;
	var unit_multiplier = p3dliteGetUnitMultiplier();
	var model_height = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;
	var z_offset = -(model_height/2 - (model_height/2 * p3dlite.resize_scale * unit_multiplier));
	scale*=unit_multiplier;


	p3dlite.model_mesh.scale.set(scale, scale, scale);
	p3dlite.model_mesh.position.set(0, z_offset, 0);
	if (p3dlite.object.type=="Group" && typeof(p3dlite.object.children[0].position)!=='undefined') {
		p3dlite.object.scale.set(scale, scale, scale);
		p3dlite.object.position.set(0, z_offset, 0);

	}

	p3dlite.controls.target.y=z_offset;
	if (p3dlite.fit_on_resize=='on') {
		var model_dim = new Array();
		var unit_multiplier = p3dliteGetUnitMultiplier();
		model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
		model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
		model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;

		var max_side = Math.max(model_dim.x*p3dlite.resize_scale*unit_multiplier, model_dim.y*p3dlite.resize_scale*unit_multiplier, model_dim.z*p3dlite.resize_scale*unit_multiplier)
		p3dlite.camera.position.set(max_side, max_side, max_side);
	}
	p3dliteMakeShadow();
}

function p3dliteGetMinScale() {
	var printer_min_side = jQuery('input[name=product_printer]:checked').data('min_side');
	var model_dim = new Array();

	model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
	model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
	model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;
	var model_min_side = Math.min(model_dim.x, model_dim.y, model_dim.z)*p3dliteGetUnitMultiplier();
	var side_diff = printer_min_side / model_min_side;
	return side_diff;

}

function p3dliteCheckMinSide(requested_scale) {
	if (p3dlite.auto_scale=='0') return true;
	var printer_min_side = jQuery('input[name=product_printer]:checked').data('min_side');
	var model_dim = new Array();

	model_dim.x = p3dlite.boundingBox.max.x - p3dlite.boundingBox.min.x;
	model_dim.y = p3dlite.boundingBox.max.y - p3dlite.boundingBox.min.y;
	model_dim.z = p3dlite.boundingBox.max.z - p3dlite.boundingBox.min.z;
	var model_min_side = Math.min(model_dim.x, model_dim.y, model_dim.z)*p3dliteGetUnitMultiplier()*(requested_scale/100);

	if (model_min_side < printer_min_side) {
		//var side_diff = printer_min_side / model_min_side;

		//p3dlite.default_scale = p3dlite.default_scale * side_diff;

		jQuery('#p3dlite-model-message-minside').show();
		return false;
	}
	else {
		jQuery('#p3dlite-model-message-minside').hide();
		return true;
	}
}

function p3dliteGetUnitMultiplier() {
	var product_unit = jQuery('input[name=p3dlite_unit]:checked').val();
	switch (product_unit) {
		case 'inch':
			var unit_multiplier = 2.54*10;
		break;
		case 'mm':
			var unit_multiplier = 1;
		break;
		default: 
			var unit_multiplier = 1;
	}
	return unit_multiplier;
}


function p3dliteUpdateSliderValue (value) {
	if (isNaN(value)) return false;

	var p3dliteRangeSlider = document.getElementById('p3dlite-scale');
	if (typeof(p3dliteRangeSlider.noUiSlider)!=='undefined') {
		p3dliteRangeSlider.noUiSlider.set(value);
	}
}

function p3dliteMobileCheck () {
	var check = false;
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
	return check;
};

	

if (window.FileReader && window.FileReader.prototype.readAsArrayBuffer) {
	p3dlite.filereader_supported=true;
} else {
	p3dlite.filereader_supported=false;
}



