<?php include_lang('settings'); ?>
<div class="content-body">
	<?php
	table_open('',true,base_url('risk_management/control_register/data_aktivitas'),'tbl_aktivitas');
	thead();
	tr();
		th(lang('no'),'','data-content="id"');
		th(lang('aktivitas'),'','data-content="aktivitas"');
		// th(lang('audit_area'),'','width="250" data-content="audit_area"');
		th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>

<script>
var idRm = '', actRm = '';
$(document).on('click','.btn-act-choose',function(e){
	e.preventDefault();
	idRm = $(this).attr('data-id');
	// actRm = 'res';
	cConfirm.open(lang.apakah_anda_yakin + '?','rmData');
})

function rmData() {
	// alert('x')
	$.ajax({
		url : '<?=base_url()?>' + 'risk_management/control_register/add_aktivitas',
		data : {id : idRm},
		type : 'post',
		dataType : 'json',
		success : function(response) {
			// console.log(idRm);
			// get_detail($('#info1').text());
			
			window.opener.document.getElementById('id_aktivitas').value = response.id_aktivitas;
			window.opener.document.getElementById('aktivitas_id_data').value = response.aktivitas;
			window.opener.document.getElementById('aktivitas_id_data').readOnly = true;
			cAlert.open(response.message,response.status);
			window.close();
		}
	});
}
</script>