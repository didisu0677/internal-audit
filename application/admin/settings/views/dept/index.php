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
	table_open('',true,base_url('settings/dept/data'),'tbl_m_audit_section');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('parent_id'),'','data-content="parent_id"');
				th(lang('section_code'),'','data-content="section_code"');
				th(lang('section_name'),'','data-content="section_name"');
				th(lang('description'),'','data-content="description"');
				th(lang('group_section'),'','data-content="group_section"');
				th(lang('id_group_section'),'','data-content="id_group_section"');
				th(lang('urutan'),'','data-content="urutan"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/dept/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('parent_id'),'parent_id');
			input('text',lang('section_code'),'section_code');
			input('text',lang('section_name'),'section_name');
			input('text',lang('description'),'description');
			input('text',lang('group_section'),'group_section');
			input('text',lang('id_group_section'),'id_group_section');
			input('text',lang('urutan'),'urutan');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/dept/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
