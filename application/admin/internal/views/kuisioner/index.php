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
			<div class="col-md-5 col-lg-3 mb-4">
				<div class="card shadow-sm border-0 h-100" id="card-entry">
					<div class="card-body bg-primary text-white rounded p-4 d-flex align-items-center justify-content-between">
						<h4 class="mb-0"><i class="fas fa-sign-in-alt fa-2x mr-3"></i> Isi Kuisioner</h4>
					</div>
				</div>
			</div>
			<?php if(user('id_group') != USER_GROUP_USER):?>
			<div class="col-md-5 col-lg-3 mb-4">
				<div class="card shadow-sm border-0 h-100" id="card-send">
					<div class="card-body bg-warning text-white rounded p-4 d-flex align-items-center justify-content-between">
						<h4 class="mb-0"><i class="fas fa-envelope fa-2x mr-3"></i> Kirim Kuisioner</h4>
					</div>
				</div>
			</div>
			<?php endif;?>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<h4 class="mb-0">Periode Audit</h4>
					<select name="tahun" id="tahun" class="form-control select2" style="width: 10%;">
						<?php 
						$year = date('Y');
						foreach($tahun as $v): ?>
							<option value="<?=$v?>" <?= $v == $year ? 'selected' : ''?> ><?= $v?></option>
						<?php endforeach;?>
					</select>
				</div>
				<ul class="list-group" id="list-periode">
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
	modal_open('mSendEmail', 'Send Email');
		modal_body();
				col_init(3, 9);
				select2('Periode Audit', 'periode', 'required', $data, 'nomor', 'nomor_deskripsi');
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

		$('#card-entry').css('cursor', 'pointer');
		get_list_periode();
	})

	$(document).on('click', '#card-send', function(){
		$('#mSendEmail').modal('show');
	});

	$(document).on('click', '.btn-modal', function(){
		let data_id = $(this).data('id');
		$('#no').html('');
		$('#deskripsi').html('');
		$.ajax({
			url: base_url + 'internal/kuisioner/get_detail_periode_audit',
			type: 'post',
			data:{id:data_id},
			success: function(res){
				let data = res.data;
				let responden = res.responden;
				let html = `
						<tr>
							<td class="font-weight-bold">Nomor</td>
							<td>${data.nomor}</td>
						</tr>
						<tr>
							<td class="font-weight-bold">Deskripsi</td>
							<td>${data.deskripsi}</td>
						</tr>
						<tr>
							<td class="font-weight-bold">Responden</td>
						</tr>`;
						let statusClass = '';
						let statusValue = '';
						$.each(responden, function(i, v){
							if(v.status == '0'){
								statusClass = "bg-warning";
								statusValue = "Belum Mengisi";
							}else{
								statusClass = "bg-success";
								statusValue = "Sudah Mengisi";
							}
							html += `
							<tr>
								<td>${v.nama} </td>
								<td class="${statusClass}">${statusValue}</td>
							</tr>`
						})
					$('#tbl-detail tbody').html(html);

					$('#mDetail').modal('show');
			}
		})
		
	})

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
					let nomor = res[0].periode_audit;
					let token = btoa(nomor);
					location.href = base_url + 'internal/kuisioner/entry/'+token;
				}
			}
		})
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
				let html = '';
				$.each(res, function(i, v){
					html += `
						<button type="button" class="list-group-item list-group-item-action btn-modal" data-id="${v.id}" >
							${v.nomor} | ${v.deskripsi}
						</button>`;
					
					$('#list-periode').html(html);	
				})
			}
		})
	}

	function reload_page(){
		location.reload();
	}
</script>
