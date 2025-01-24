<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete'); ?>
			<div class="d-inline">
				<button type="button" class="btn btn-info btn-sm" id="input-otomatis"><?php echo lang('input_otomatis'); ?></button>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/hari_libur/data'),'bas_m_hari_libur');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('keterangan'),'','data-content="keterangan"');
				th(lang('tanggal_mulai'),'','data-content="tanggal_mulai" data-type="daterange"');
				th(lang('tanggal_selesai'),'','data-content="tanggal_selesai" data-type="daterange"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/hari_libur/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('keterangan'),'keterangan','required');
			input('date',lang('tanggal_mulai'),'tanggal_mulai','required');
			input('date',lang('tanggal_selesai'),'tanggal_selesai');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-otomatis');
	modal_body();
		?>
		<div class="alert alert-warning"><?php echo lang('data_libur_diambil_dari');?> <a href="https://www.liburnasional.com" class="fw-bold" target="_blank">https://www.liburnasional.com</a></div>
		<?php
		form_open(base_url('settings/hari_libur/otomatis'),'post','form-otomatis');
			col_init(3,9);
			select2(lang('tahun'),'tahun','required',$tahun,'_key');
			form_button(lang('input_otomatis'),false);
		form_close();
	modal_footer();
modal_close();
?>
<script>
	$('#input-otomatis').click(function(e){
		e.preventDefault();
		$('#modal-otomatis').modal();
	});
</script>