<?php include_lang('settings'); ?>
<div class="content-body">
	<?php
	table_open('',true,base_url('risk_management/rcm/data_risk'),'tbl_risk_register');
	thead();
	tr();
		th(lang('no'),'','data-content="id"');
		th(lang('risk'),'','data-content="risk"');
		th(lang('keterangan'),'','width="250" data-content="keterangan"');
		th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>

<script>
	$(document).on('click','.btn-act-choose1',function(){


		let id = $(this).attr('data-id');
		const urlParams = new URLSearchParams(window.location.search);
		const ctrlId = urlParams.get('ctrl_id');

		$.ajax({
			url: '<?=base_url()?>' + 'risk_management/rcm/add_new_risk',
			type: 'post',
			data: {id:id},
			success: function(resp){
				console.log(resp);
				if (resp.status == 'ok') {
					window.opener.document.getElementById('id_risk'+ctrlId).value = resp.id;
					window.opener.document.getElementById('risk'+ctrlId).value = resp.risk;
					window.opener.document.getElementById('keterangan'+ctrlId).value = resp.keterangan;
					window.opener.document.getElementById('risk'+ctrlId).value = resp.risk;
					window.opener.document.getElementById('score_dampak'+ctrlId).value = resp.score_dampak;
					window.opener.document.getElementById('score_kemungkinan'+ctrlId).value = resp.score_kemungkinan;
					window.opener.document.getElementById('bobot_risk'+ctrlId).value = resp.bobot;
					// cAlert.open(resp.message,resp.status);
					window.close();
				} else {
					alert('Oops! Ada kesalahan silakan coba lagi.');
				}

			}
		})
	})


</script>