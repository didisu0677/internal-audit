<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/m_kriteria/data'),'tbl_kriteria');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('detail'),'','data-content="detail"');
				th(lang('location'),'','data-content="location"');
				th(lang('reff_name'),'','data-content="reff_name"');
				th(lang('publisher'),'','data-content="publisher"');
				th(lang('effective_date'),'','data-content="effective_date" data-type="daterange"');
				th(lang('created_by'),'','data-content="nama" data-table="u"');
				th(lang('created_at'),'','data-content="created_at" data-type="daterange"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/m_kriteria/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			textarea(lang('detail'),'detail');
			input('text',lang('location'),'location');
			input('text',lang('reff_name'),'reff_name');
			input('text',lang('publisher'),'publisher');
			input('date',lang('effective_date'),'effective_date');
			// input('text',lang('created_by'),'created_by');
			// input('datetime',lang('created_at'),'created_at');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/m_kriteria/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
