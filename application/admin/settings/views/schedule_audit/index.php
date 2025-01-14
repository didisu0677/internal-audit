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
	table_open('',true,base_url('settings/schedule_audit/data'),'tbl_schedule_audit');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('nomor'),'','data-content="nomor"');
				th(lang('surat_tugas'),'','data-content="surat_tugas"');
				th(lang('deskripsi'),'','data-content="deskripsi"');
				th(lang('institusi_audit'),'','data-content="nama_institusi", data-table="tbl_institusi_audit"');
				th(lang('tanggal_mulai'),'','data-content="tanggal_mulai" data-type="daterange"');
				th(lang('tanggal_akhir'),'','data-content="tanggal_akhir" data-type="daterange"');
				th(lang('tgl_closing_meeting'),'','data-content="tgl_closing_meeting" data-type="daterange"');
				th(lang('department_auditee'),'','data-content="department_auditee"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','modal-lg','data-openCallback="openForm"');
	modal_body();
		form_open(base_url('settings/schedule_audit/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('nomor'),'nomor');
			input('surat_tugas',lang('surat_tugas'),'surat_tugas');
			input('deskripsi',lang('deskripsi'),'deskripsi');
			select2(lang('institusi_audit'),'id_institusi_audit','required',$institusi,'id','nama_institusi');
			input('date',lang('tanggal_mulai'),'tanggal_mulai');
			input('date',lang('tanggal_akhir'),'tanggal_akhir');
			input('date',lang('tgl_closing_meeting'),'tgl_closing_meeting');
			select2(lang('department'),'department_auditee[]','required',$department,'id','department','','multiple');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/schedule_audit/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

