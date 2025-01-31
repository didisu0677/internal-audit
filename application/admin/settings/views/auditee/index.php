<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/auditee/data'),'tbl_auditee');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('nip'),'','data-content="nip"');
				th(lang('email'),'','data-content="email"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('department'),'','data-content="department" data-table="tbl_m_department"');
				th(lang('section'),'','data-content="section"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','modal-lg','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('settings/auditee/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('nip'),'nip');
			input('text',lang('email'),'email','email');
			input('text',lang('nama'),'nama');
			select2(lang('department'),'id_department1','required',$department,'id','department');
			select2(lang('section'),'id_section[]','required',$section,'id','section','','multiple');
	
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/auditee/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>

	$('#id_department1').change(async function(){
		await getSection();
	});


	async function getSection() {
		$('#id_section').html('');
		await $.ajax({
			url : base_url + 'settings/auditee/get_section',
			data : {dept : $('#id_department1').val()},
			type : 'post',
			dataType : 'json',
			success : function(response) {
				var konten = '';
				$.each(response,function(k,v){
					konten += '<option value="'+v.id+'">'+v.kode + ' | ' +v.section+'</option>';
				});
				$('#id_section').html(konten);
			}
		});
	}

	function formOpen() {
		is_edit = true;
		var response = response_edit;
		if (typeof response.id !== 'undefined') {
			$('#id_department1').val(response.id_department).trigger('change');
			if(response.id_section != null && response.id_section.length > 0) {
				$.each(response.id_section, function(k,v){
					$('#id_section').find('[value="'+v+'"]').prop('selected',true);
				});
				$('#id_section').trigger('change');
			};
		}
		is_edit = false;
	}
</script>