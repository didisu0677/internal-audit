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
				th(lang('department'),'','data-content="section_name" data-table="tbl_m_audit_section tbl_m_department"');
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
			input('hidden', 'nip', 'nip');
			input('hidden', 'id_user', 'id_user');
			select2('User','id_user','required',get_active_user(),'id','kode - nama');
			input('text',lang('email'),'email','email');
			input('text',lang('nama'),'nama');
			select2(lang('divisi'),'id_department1','required',$department,'section_code','department');
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

	$('#id_department1').change(function(){
		getSection();
	});


	function getSection() {
		$('#id_section').html('');
		$.ajax({
			url : base_url + 'settings/auditee/get_section',
			data : {dept : $('#id_department1').val()},
			type : 'post',
			dataType : 'json',
			success : function(response) {
				var konten = '';
				$.each(response,function(k,v){
					konten += '<option value="'+v.id+'">'+v.section_code + ' | ' +v.section_name+'</option>';
				});
				$('#id_section').html(konten);

				apply_value_section()
			}
		});
	}

	function apply_value_section(){
		is_edit = true;
		var response = response_edit;
		if (typeof response.id !== 'undefined') {
			if(response.id_section != null && response.id_section.length > 0) {
				$.each(response.id_section, function(k,v){
					$('#id_section').find('[value="'+v+'"]').prop('selected',true);
				});
				$('#id_section').trigger('change');
			};
		}
		is_edit = false;
	}

	function formOpen() {
		var response = response_edit;
		$('#id_department1').val(response.section_code).trigger('change');
	}

	$(document).on('change', '#id_user', function() {
		var userId = $(this).val();
		if(userId) {
			$.ajax({
				url: base_url + 'settings/auditee/get_user_details',
				type: 'POST',
				data: { id: userId },
				dataType: 'json',
				success: function(response) {
					if(response) {
						$('#id_user').val(response.id);
						$('#nip').val(response.kode);
						$('#email').val(response.email);
						$('#nama').val(response.nama);
					} else {
						$('#id_user').val('');
						$('#nip').val('');
						$('#email').val('');
						$('#nama').val('');
					}
				},
				error: function() {
					$('#id_user').val('');
					$('#nip').val('');
					$('#email').val('');
					$('#nama').val('');
				}
			});
		} else {
			$('#email').val('');
			$('#nama').val('');
		}
	});


</script>