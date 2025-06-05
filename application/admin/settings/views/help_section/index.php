<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button(''); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/help_section/data'),'tbl_help');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th('type','','data-content="type"');
				th('name','','data-content="name"');
				th('description','','data-content="description"');
				th('attachment','','data-content="file_attachment"');
				th('link','','data-content="link"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','modal-lg','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('settings/help_section/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2('Type','type','required', $type,'key','value');

			input('text', 'Name', 'name', 'required');
			textarea('Description', 'description');
			fileupload('File Attachment', 'file_attachment', '', '');
			input('text', 'Link', 'link');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
?>

<script>

	// $('#id_department1').change(function(){
	// 	getSection();
	// });


	// function getSection() {
	// 	$('#id_section').html('');
	// 	$.ajax({
	// 		url : base_url + 'settings/auditee/get_section',
	// 		data : {dept : $('#id_department1').val()},
	// 		type : 'post',
	// 		dataType : 'json',
	// 		success : function(response) {
	// 			var konten = '';
	// 			$.each(response,function(k,v){
	// 				konten += '<option value="'+v.id+'">'+v.section_code + ' | ' +v.section_name+'</option>';
	// 			});
	// 			$('#id_section').html(konten);

	// 			apply_value_section()
	// 		}
	// 	});
	// }

	// function apply_value_section(){
	// 	is_edit = true;
	// 	var response = response_edit;
	// 	if (typeof response.id !== 'undefined') {
	// 		if(response.id_section != null && response.id_section.length > 0) {
	// 			$.each(response.id_section, function(k,v){
	// 				$('#id_section').find('[value="'+v+'"]').prop('selected',true);
	// 			});
	// 			$('#id_section').trigger('change');
	// 		};
	// 	}
	// 	is_edit = false;
	// }

	// function formOpen() {
	// 	var response = response_edit;
	// 	$('#id_department1').val(response.section_code).trigger('change');
	// }
</script>