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
	table_open('',true,base_url('settings/set_nottification/data'),'tbl_capa_nottification');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('number_reminder'),'','data-content="number_reminder"');
				th(lang('nottification'),'','data-content="nottification"');
				th(lang('days_nottification'),'','data-content="days_nottification"');
				th(lang('description'),'','data-content="description"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/set_nottification/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('number_reminder'),'number_reminder');
			textarea(lang('nottification'),'nottification');
			input('text',lang('days_nottification'),'days_nottification');
			// select2(lang('hitung_dari'),'hitung_dari','required',['dateline_capa','Terakhir nottification']);
			textarea(lang('description'),'description');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();

modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/set_nottification/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();

?>


<script>
var id_email = 0;
$(document).on('click','.btn-email',function(e){
	e.preventDefault();
	id_email = $(this).attr('data-id');
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});
function lanjut() {
	$.ajax({
		url : base_url + 'settings/set_nottification/capa_nottification',
		data : {id:id_email},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status,'refreshData');
		}
	});
}
</script>