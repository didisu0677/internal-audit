<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body py-4 px-3">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-12">
				<h1 class="display-5 font-weight-bold text-dark">Kuisioner Audit</h1>
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-md-3 mb-4">
				<div class="card shadow-sm border-0 h-100" id="card-entry">
					<div class="card-body bg-primary text-white rounded p-4 d-flex align-items-center justify-content-between">
						<h4 class="mb-0"><i class="fas fa-sign-in-alt fa-2x mr-3"></i> Isi Kuisioner</h4>
					</div>
				</div>
			</div>
			<?php if(user('id_group') != USER_GROUP_USER):?>
			<div class="col-md-3 mb-4">
				<div class="card shadow-sm border-0 h-100" id="card-send">
					<div class="card-body bg-warning text-white rounded p-4 d-flex align-items-center justify-content-between">
						<h4 class="mb-0"><i class="fas fa-envelope fa-2x mr-3"></i> Kirim Kuisioner</h4>
					</div>
				</div>
			</div>
			<div class="col-md-3 mb-4">
				<div class="card shadow-sm border-0 h-100" id="card-export">
					<div class="card-body bg-danger text-white rounded p-4 d-flex align-items-center justify-content-between">
						<h4 class="mb-0"><i class="fas fa-envelope-open-text fa-2x mr-3"></i></i> Report Kuisioner</h4>
					</div>
				</div>
			</div>
			<?php endif;?>
		</div>
		<hr>
		<?php if(user('id_group') != USER_GROUP_USER):?>
		<div class="row" id="list-periode-container">
			<div class="col-12">
				<div class="card shadow-sm">
					<div class="card-header bg-light">
						<div class="row align-items-center">
							<div class="col-md-6">
								<h5 class="mb-0">Status Kuisioner</h5>
							</div>
							<div class="col-md-3 offset-3">
								<div class="form-group mb-0">
									<label for="tahun" class="sr-only">Tahun</label>
									<select name="tahun" id="tahun" class="form-control select2">
										<?php 
										$year = date('Y');
										foreach($tahun as $v): ?>
											<option value="<?=$v?>" <?= $v == $year ? 'selected' : ''?> ><?= $v?></option>
										<?php endforeach;?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="list-group list-group-flush" id="list-periode">
							<div class="list-group-item text-center text-muted py-4">
								<i class="fas fa-spinner fa-spin mr-2"></i>Loading data...
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif;?>
	</div>
</div>
<?php
	modal_open('mSendEmail', 'Send Email');
		modal_body();
				col_init(3, 9);
				select2('Periode Audit', 'periode', 'required', $data);
				select2('Auditee', 'auditee[]', 'required', get_active_auditee(),'id', 'nama', '', 'multiple');
				echo '
					<div class="row">
						<div class="col-md-9 offset-3">
						<button class="btn btn-primary" id="btn-submit">Submit</button>
						</div>
					</div>';
	modal_close();
	modal_open('mDetail', 'Detail');
		modal_body();
				echo '
					<table class="table table-sm" id="tbl-detail">
						<tbody></tbody>
					</table>
				';
	modal_close();
?>

<script>
	$(document).ready(function(){
		$('#card-send .card-body').css('cursor', 'pointer');
		$('#card-export').css('cursor', 'pointer');
		$('#card-entry').css('cursor', 'pointer');
		get_list_periode();
	})

	$(document).on('click', '#card-send', function(){
		$('#mSendEmail').modal('show');
	});

	// $(document).on('click', '.btn-modal', function(){
	// 	let data_id = $(this).data('id');
	// 	$('#no').html('');
	// 	$('#deskripsi').html('');
	// 	$.ajax({
	// 		url: base_url + 'internal/kuisioner/get_detail_periode_audit',
	// 		type: 'post',
	// 		data:{id:data_id},
	// 		success: function(res){
	// 			let data = res.data;
	// 			let responden = res.responden;
	// 			let html = `
	// 					<tr>
	// 						<td class="font-weight-bold">Nomor</td>
	// 						<td>${data.nomor}</td>
	// 					</tr>
	// 					<tr>
	// 						<td class="font-weight-bold">Deskripsi</td>
	// 						<td>${data.deskripsi}</td>
	// 					</tr>
	// 					<tr>
	// 						<td class="font-weight-bold">Responden</td>
	// 					</tr>`;
	// 					let statusClass = '';
	// 					let statusValue = '';
	// 					$.each(responden, function(i, v){
	// 						if(v.status == '0'){
	// 							statusClass = "bg-warning";
	// 							statusValue = "Belum Mengisi";
	// 						}else{
	// 							statusClass = "bg-success";
	// 							statusValue = "Sudah Mengisi";
	// 						}
	// 						html += `
	// 						<tr>
	// 							<td>${v.nama} </td>
	// 							<td class="${statusClass}">${statusValue}</td>
	// 						</tr>`
	// 					})
	// 				$('#tbl-detail tbody').html(html);

	// 				$('#mDetail').modal('show');
	// 		}
	// 	})
		
	// })

	$(document).on('change', '#tahun', function(){
		get_list_periode();
	})

	$(document).on('click', '#card-entry', function(){
		$.ajax({
			url: base_url + 'internal/kuisioner/check_kuisioner',
			type: 'post',
			success: function(res){
				console.log(res);
				if(res.length == 0){
					cAlert.open('Tidak ada kuisioner yang perlu anda isi!', 'info');
				}else{
					let id = res[0].id;
					let token = encodeId(id);
					location.href = base_url + 'internal/kuisioner/entry/'+token;
				}
			}
		})
	});

	$(document).on('click', '#card-export', function(){
		location.href = base_url + 'internal/kuisioner/export';
	});
	
	$(document).on('click', '#btn-submit', function(){
		let periode = $('#periode').val();
		let auditee = $('#auditee').val();

		$.ajax({
			url: base_url + 'internal/kuisioner/send_kuisioner',
			type: 'post',
			data: {periode:periode, auditee:auditee},
			success: function(res){
				let user_fail = [];
				$.each(res, function(i,v){
					if(v !== 'success'){
						user_fail.push(v.nip);
					}
				});
				if(user_fail.length > 0){
					cAlert.open('Berhasil mengirim email!','success', 'reload_page');
				}else{
					cAlert.open('Gagal mengirim email ke (' + user_fail + ')', 'error', 'reload_page');
				}
			}
		})
	})

	function get_list_periode(){
		let tahun = $('#tahun').val();

		$.ajax({
			url: base_url + 'internal/kuisioner/get_list_periode',
			type: 'post',
			data: {tahun:tahun},
			success: function(res){
				console.log(res);
				let html = '';
				
				if(res.length === 0) {
					html = `
						<div class="list-group-item text-center text-muted py-4">
							<i class="fas fa-info-circle mr-2"></i>Tidak ada data periode untuk tahun ${tahun}
						</div>`;
				} else {
					$.each(res, function(i, v){
						let statusClass = '';
						let statusText = '';
						let statusIcon = '';
						
						if(v.status == '0'){
							statusClass = 'badge-warning';
							statusText = 'Belum Mengisi';
							statusIcon = 'fas fa-clock';
						} else {
							statusClass = 'badge-success';
							statusText = 'Sudah Mengisi';
							statusIcon = 'fas fa-check-circle';
						}

						html += `
							<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-id="${v.id}">
								<div class="d-flex align-items-center">
									<i class="${statusIcon} mr-3 text-muted"></i>
									<div>
										<h6 class="mb-1">${v.nama}</h6>
										<p class="mb-0 text-muted small">${v.kode}</p>
										<span class="badge ${statusClass}">${statusText}</span>
									</div>
								</div>
							</div>`;
					});
				}
				
				$('#list-periode').html(html);	
			}
		})
	}

	function reload_page(){
		location.reload();
	}
</script>
