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
	table_open('',true,base_url('settings/aktivitas/data'),'tbl_aktivitas');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('company'),'','data-content="company"');
				th(lang('site_auditee'),'','data-content="site_auditee"');
				th(lang('id_divisi_auditee'),'','data-content="id_divisi_auditee"');
				th(lang('id_department_auditee'),'','data-content="id_department_auditee"');
				th(lang('id_section_auditee'),'','data-content="id_section_auditee"');
				th(lang('aktivitas'),'','data-content="aktivitas"');
				th(lang('audit_area'),'','data-content="audit_area"');
				th(lang('id_type_aktivitas'),'','data-content="id_type_aktivitas"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/aktivitas/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('company'),'company');
			input('text',lang('site_auditee'),'site_auditee');
			input('text',lang('id_divisi_auditee'),'id_divisi_auditee');
			input('text',lang('id_department_auditee'),'id_department_auditee');
			input('text',lang('id_section_auditee'),'id_section_auditee');
			input('text',lang('aktivitas'),'aktivitas');
			input('text',lang('audit_area'),'audit_area');
			input('text',lang('id_type_aktivitas'),'id_type_aktivitas');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/aktivitas/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
