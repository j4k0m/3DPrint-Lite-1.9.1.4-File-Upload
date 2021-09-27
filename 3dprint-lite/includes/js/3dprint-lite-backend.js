/**
 * @author Sergey Burkov, http://www.wp3dprinting.com
 * @copyright 2015
 */
jQuery(document).ready(function() {
	jQuery('.p3dlite-expand').accordion({collapsible:true, active:false});
	jQuery( "#p3dlite_tabs" ).tabs();
	jQuery( ".p3dlite_color_picker" ).wpColorPicker();
//	jQuery('select.select_printer').change()

});

function p3dliteAddPrinter(clone_id) {
        var max_index = 0;

	jQuery('input[name^=p3dlite_printer_name]').each(function(){
		var start = jQuery(this).attr('name').indexOf('[')+1;
		var end = jQuery(this).attr('name').indexOf(']');
		var index = parseInt(jQuery(this).attr('name').substring(start, end));
		if (index > max_index) max_index = index;
	});

//	jQuery('table.printer').first().clone().insertBefore('#add_printer_button');
//	if (isNaN(clone_id)) jQuery('table.printer').last().find('input').val('')

	if (!isNaN(clone_id)) object_to_clone=jQuery('#printer-'+clone_id);
	else object_to_clone=jQuery('table.printer').first();

	jQuery(object_to_clone).clone().insertBefore('#add_printer_button');
	if (isNaN(clone_id)) jQuery('table.printer').last().find('input').val('');

	jQuery('table.printer').last().find('.remove_printer').remove();
	jQuery('table.printer').last().find('.item_id').remove();
	jQuery('table.printer').last().find('input[name^=p3dlite_printer], select[name^=p3dlite_printer]').each(function(){
		var start = jQuery(this).attr('name').indexOf('[')+1;
		var end = jQuery(this).attr('name').indexOf(']');
		var index = parseInt(jQuery(this).attr('name').substring(start, end));
		var new_name = jQuery(this).attr('name').replace('['+index+']', '['+(max_index+1)+']');
		jQuery(this).attr('name', new_name);
		if (isNaN(clone_id)) jQuery(this).val(jQuery(this).find("option:first").val());
	});
//	jQuery('table.printer').last().find('tr.printer_materials').remove();
	jQuery('table.printer').last().find('.CaptionCont, .optWrapper').remove();
	jQuery('table.printer').last().find('.sumoselect').unwrap().show();
	if (isNaN(clone_id)) jQuery('table.printer').last().find('.sumoselect option:selected').prop('selected', false);
	jQuery('table.printer').last().find('.sumoselect').SumoSelect({ okCancelInMulti: true, selectAll: true });

}

function p3dliteAddMaterial(clone_id) {
        var max_index = 0;

	jQuery('input[name^=p3dlite_material_name]').each(function(){
		var start = jQuery(this).attr('name').indexOf('[')+1;
		var end = jQuery(this).attr('name').indexOf(']');
		var index = parseInt(jQuery(this).attr('name').substring(start, end));
		if (index > max_index) max_index = index;
	});

	if (!isNaN(clone_id)) object_to_clone=jQuery('#material-'+clone_id);
	else object_to_clone=jQuery('table.material').first();

	jQuery(object_to_clone).clone().insertBefore('#add_material_button');
	if (isNaN(clone_id)) jQuery('table.material').last().find('input').val('');

	jQuery('table.material').last().find('.wp-picker-container').remove();
	jQuery('table.material').last().find('td.color_td').html('<input type="text" class="p3dlite_color_picker" name="p3dlite_material_color['+(max_index+1)+']" value="" />');
	jQuery('table.material').last().find( ".p3dlite_color_picker" ).wpColorPicker();
	jQuery('table.material').last().find('.remove_material').remove();
	jQuery('table.material').last().find('.item_id').remove();

	jQuery('table.material').last().find('input[name^=p3dlite_material], select[name^=p3dlite_material]').each(function(){
		var start = jQuery(this).attr('name').indexOf('[')+1;
		var end = jQuery(this).attr('name').indexOf(']');
		var index = parseInt(jQuery(this).attr('name').substring(start, end));
		var new_name = jQuery(this).attr('name').replace('['+index+']', '['+(max_index+1)+']');
		jQuery(this).attr('name', new_name);
		if (isNaN(clone_id)) jQuery(this).val(jQuery(this).find("option:first").val());
	});
}

function p3dliteAddCoating(clone_id) {
        var max_index = 0;

	jQuery('input[name^=p3dlite_coating_name]').each(function(){
		var start = jQuery(this).attr('name').indexOf('[')+1;
		var end = jQuery(this).attr('name').indexOf(']');
		var index = parseInt(jQuery(this).attr('name').substring(start, end));
		if (index > max_index) max_index = index;
	});

	if (!isNaN(clone_id)) object_to_clone=jQuery('#coating-'+clone_id);
	else object_to_clone=jQuery('table.coating').first();

	jQuery(object_to_clone).clone().insertBefore('#add_coating_button');
	if (isNaN(clone_id)) jQuery('table.coating').last().find('input').val('');

	jQuery('table.coating').last().find('.wp-picker-container').remove();
	jQuery('table.coating').last().find('td.color_td').html('<input type="text" class="p3dlite_color_picker" name="p3dlite_coating_color[]" value="" />');
	jQuery('table.coating').last().find( ".p3dlite_color_picker" ).wpColorPicker();
	jQuery('table.coating').last().find('.remove_coating').remove();
	jQuery('table.coating').last().find('.item_id').remove();

	jQuery('table.coating').last().find('input[name^=p3dlite_coating], select[name^=p3dlite_coating]').each(function(){
		var start = jQuery(this).attr('name').indexOf('[')+1;
		var end = jQuery(this).attr('name').indexOf(']');
		var index = parseInt(jQuery(this).attr('name').substring(start, end));
		var new_name = jQuery(this).attr('name').replace('['+index+']', '['+(max_index+1)+']');
		jQuery(this).attr('name', new_name);
		if (isNaN(clone_id)) jQuery(this).val(jQuery(this).find("option:first").val());
	});

	jQuery('table.coating').last().find('.CaptionCont, .optWrapper').remove();
	jQuery('table.coating').last().find('.sumoselect').unwrap().show();
	if (isNaN(clone_id)) jQuery('table.coating').last().find('.sumoselect option:selected').prop('selected', false);
	jQuery('table.coating').last().find('.sumoselect').SumoSelect({ okCancelInMulti: true, selectAll: true });
}

function p3dliteRemovePrinter(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-1" method="post"><input type="hidden" name="action" value="remove_printer"><input type="hidden" name="printer_id" value="'+id+'"></form>' ).appendTo('body').submit()
}
function p3dliteRemoveMaterial(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-2" method="post"><input type="hidden" name="action" value="remove_material"><input type="hidden" name="material_id" value="'+id+'"></form>' ).appendTo('body').submit()
}
function p3dliteRemoveCoating(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-3" method="post"><input type="hidden" name="action" value="remove_coating"><input type="hidden" name="coating_id" value="'+id+'"></form>' ).appendTo('body').submit()
}
function p3dliteRemoveRequest(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-4" method="post"><input type="hidden" name="action" value="remove_request"><input type="hidden" name="request_id" value="'+id+'"></form>' ).appendTo('body').submit()
}

function p3dliteSetMaterialType(obj)  {
        var material_type = obj.value;
	jQuery(obj).closest('table.form-table.material').find('tr, a').each(function(i, el){
		var className = jQuery(el).attr('class');
		if (typeof(className)!=='undefined') {
			if (className.indexOf('material')==0) {
				if (className=='material_'+material_type) jQuery(el).show();
				else jQuery(el).hide();
			}
		}
	});
}



function p3dliteSelectPlatformShape(obj) {
	var platform_shape = obj.value;
	jQuery(obj).closest('table.form-table.printer').find('tr').each(function(i, el){

		var className = jQuery(el).attr('class');
		if (typeof(className)!=='undefined') {
			if (className.indexOf('platform_shape')==0) {
				if (className.indexOf('platform_shape_'+platform_shape)!=-1) jQuery(el).show();
				else jQuery(el).hide();
			}
		}
	});
}

function p3dliteToggleClientBodyRaw(obj) {
	if (obj.checked) {
		jQuery('#client_email_body_raw').show();
		jQuery('#p3dlite_wpeditor_client_wrap').hide();
	}
	else {
		jQuery('#client_email_body_raw').hide();
		jQuery('#p3dlite_wpeditor_client_wrap').show();

	}
}
function p3dliteToggleAdminBodyRaw(obj) {
	if (obj.checked) {
		jQuery('#admin_email_body_raw').show();
		jQuery('#p3dlite_wpeditor_admin_wrap').hide();
	}
	else {
		jQuery('#admin_email_body_raw').hide();
		jQuery('#p3dlite_wpeditor_admin_wrap').show();

	}
}


jQuery(document).ready(function(){
	jQuery('.sumoselect').SumoSelect({ okCancelInMulti: true, selectAll: true });
	jQuery('.tooltip').tooltipster({ contentAsHTML: true, multiple: true });
});