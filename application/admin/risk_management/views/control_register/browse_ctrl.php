<?php include_lang('settings'); ?>
<div class="content-body">
	<?php
	table_open('',true,base_url('risk_management/control_register/data_control'),'tbl_m_internal_control');
	thead();
	tr();
		th(lang('no'),'','data-content="id"');
		th(lang('internal_control'),'','data-content="internal_control"');
		th(lang('penerbit_pnp'),'','data-content="penerbit_pnp"');
		th(lang('location_control'),'','width="250" data-content="location_control"');
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
			url: '<?=base_url()?>' + 'risk_management/control_register/add_new_control',
			type: 'post',
			data: {id:id},
			success: function(resp){
				console.log(resp);
				if (resp.status == 'ok') {
					window.opener.document.getElementById('id_m_control'+ctrlId).value = resp.id_m_control;
					window.opener.document.getElementById('ctrl_existing'+ctrlId).value = resp.internal_control;
					window.opener.document.getElementById('ctrl_location'+ctrlId).value = resp.location_control;
					window.opener.document.getElementById('no_pnp'+ctrlId).value = resp.no_pnp;
					window.opener.document.getElementById('jenis_pnp'+ctrlId).value = resp.jenis_pnp;
					window.opener.document.getElementById('penerbit'+ctrlId).value = resp.penerbit_pnp;
					window.opener.document.getElementById('tgl_pnp'+ctrlId).value = resp.tanggal_pnp;
					cAlert.open(resp.message,resp.status);
					window.close();
				} else {
					alert('Oops! Ada kesalahan silakan coba lagi.');
				}

			}
		})
	})


</script>