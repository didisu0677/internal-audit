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
	table_open('',true,base_url('settings/dept/data'),'tbl_m_department');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('id_divisi'),'','data-content="id_divisi"');
				th(lang('kode'),'','data-content="kode"');
				th(lang('department'),'','data-content="department"');
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
			input('text',lang('id_divisi'),'id_divisi');
			input('text',lang('kode'),'kode');
			input('text',lang('department'),'department');
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
