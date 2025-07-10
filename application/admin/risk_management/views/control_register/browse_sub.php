<?php include_lang('settings'); ?>
<div class="content-body">
	<?php
	table_open('',true,base_url('risk_management/control_register/data_sub_aktivitas'),'tbl_sub_aktivitas');
	thead();
	tr();
		th(lang('no'),'','data-content="id"');
		th(lang('sub_aktivitas'),'','data-content="sub_aktivitas"');
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
		url : '<?=base_url()?>' + 'risk_management/control_register/add_sub_aktivitas',
		data : {id : idRm},
		type : 'post',
		dataType : 'json',
		success : function(response) {
			// console.log(response);
			// console.log(idRm);
			// get_detail($('#info1').text());
			
			window.opener.document.getElementById('id_audit_area').value = response.id_sub_aktivitas;
			window.opener.document.getElementById('audit_area').value = response.sub_aktivitas;
			window.opener.document.getElementById('audit_area').readOnly = true;
			cAlert.open(response.message,response.status);
			window.close();
		}
	});
}
</script>