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
	table_open('',true,base_url('settings/m_control/data'),'tbl_m_internal_control');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('internal_control'),'','data-content="internal_control"');
				th(lang('location_control'),'','data-content="location_control"');
				th(lang('no_pnp'),'','data-content="no_pnp"');
				th(lang('jenis_pnp'),'','data-content="jenis_pnp"');
				th(lang('penerbit_pnp'),'','data-content="penerbit_pnp"');
				th(lang('tanggal_pnp'),'','data-content="tanggal_pnp" data-type="daterange"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/m_control/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			textarea(lang('internal_control'),'internal_control');
			input('text',lang('location_control'),'location_control');
			input('text',lang('no_pnp'),'no_pnp');
			input('text',lang('jenis_pnp'),'jenis_pnp');
			input('text',lang('penerbit_pnp'),'penerbit_pnp');
			input('date',lang('tanggal_pnp'),'tanggal_pnp');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/m_control/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
