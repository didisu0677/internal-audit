<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
</div>
<div class="container-fluid p-3">
	<div class="row mb-4">
		<div class="col-12">
			<div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
				<div class="card-body py-4">
					<div class="row align-items-center">
						<div class="col-md-8">
							<h3 class="mb-1 font-weight-bold text-white"><?php echo $title; ?></h3>
							<div class="text-white-50">Find and review recorded findings easily</div>
						</div>
						<div class="col-md-4 text-md-right">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="content-body p-2">
		<table class="table table-hover table-bordered table-striped table-sm" id="table-data">
			<thead>
				<tr>
					<th style="display:none"></th>
					<!-- <th><?= lang('no'); ?></th> -->
					<th><?= lang('auditor'); ?></th>
					<th><?= lang('tgl_mulai_audit'); ?></th>
					<th><?= lang('auditee'); ?></th>
					<th><?= lang('site_auditee'); ?></th>
					<th><?= lang('section'); ?></th>
					<th><?= lang('audit_area'); ?></th>
					<th><?= lang('finding_description'); ?></th>
					<th><?= lang('bobot'); ?></th>
					<th><?= lang('status'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$no = 1;
				$status_finding = [
					'0' => ['text' => 'Open', 'class' => 'badge-danger'],
					'1' => ['text' => 'Delivered', 'class' => 'badge-warning'],
					'2' => ['text' => 'Closed', 'class' => 'badge-success']
				];
				foreach($data as $val):?>
				<tr>
					<td style="display:none"><?= $val['id'] ?></td> <!-- hidden ID -->
					<!-- <td width="1" class="text-center"><?= $no++ ?></td> -->
					<td width="1" class="text-nowrap"><?= $val['nama_auditor'] ?></td>
					<td width="1" class="text-nowrap"><?= $val['tgl_mulai_audit']?></td>
					<td width="1" class="text-nowrap"><?= $val['auditee']?></td>
					<td width="1" class="text-nowrap"><?= $val['site_auditee']?></td>
					<td><?= $val['section_name']?></td>
					<td><?= $val['sub_aktivitas']?></td>
					<td><?= $val['finding']?></td>
					<td width="1" class="text-nowrap"><?= $val['bobot_finding']?></td>
					<td width="1" class="text-nowrap">
						<span class="badge <?= $status_finding[$val['status_finding']]['class'] ?>">
							<?= $status_finding[$val['status_finding']]['text'] ?>
						</span>
					</td>
					<td width="1" class="text-nowrap">
						<button type="button" class="btn btn-secondary btn-sm btn-capa btn-icon-only" data-id="<?= $val['id'] ?>"><i class="far fa-copy"></i> </button>
						<?php if(user('id_group')!=AUDITEE): ?>
							<button type="button" class="btn btn-warning btn-sm btn-input btn-icon-only" data-id="<?= $val['id'] ?>" data-key="edit"><i class="fa fa-edit"></i> </button>
							<button type="button" class="btn btn-danger btn-sm btn-delete btn-icon-only" data-id="<?= $val['id'] ?>" ><i class="fa fa-trash"></i> </button>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach;?>
		</table>
	</div>
</div>
<?php 

modal_open('modal-form','Finding','modal-xl','data-openCallback="formOpen1"');
	modal_body();
		form_open(base_url('internal/finding_records/save'),'post','form');
			col_init(3,9);
			card_open(lang('info_audit'),'mb-2');
				select2(lang('periode_audit'),'periode_audit','required','','','','','disabled');
				?>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="institusi_audit"><?php echo lang('institusi_audit'); ?></label>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="institusi_audit" id="institusi_audit" class="form-control" autocomplete="off" disabled >
						</div>
					</div>

					<label class="col-sm-2 col-form-label" for="auditor"><?php echo lang('auditor'); ?></label>
					<div class="col-sm-3">
						<div class="input-group">
						<select class="select2 infinity custom-select" style="width: 80px;" id="auditor" name="auditor" disabled>
							<?php foreach ($auditor as $a =>$v) { ?>
							<option value="<?php echo $v['id']; ?>"><?php echo $v['nama']; ?></option>
							<?php } ?>
						</select>
							
						</div>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tgl_mulai_audit"><?php echo lang('tgl_mulai_audit'); ?></label>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="tgl_mulai_audit" id="tgl_mulai_audit" class="form-control" autocomplete="off" disabled>
						</div>
					</div>

					<label class="col-sm-2 col-form-label" for="tgl_akhir_audit"><?php echo lang('tgl_akhir_audit'); ?></label>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="tgl_akhir_audit" id="tgl_akhir_audit" class="form-control" autocomplete="off" disabled>
						</div>
					</div>
				</div>
	
				<?php
			card_close();
			card_open(lang('finding'),'mb-2');?>
			<input type="hidden" name="id_finding_records[]" id="id_finding_records">
			<div class="form-group row">

				<label class="col-form-label col-sm-3" for="bobot_finding"><?php echo lang('bobot_finding'); ?></label>
				<div class="col-sm-9">
				<select class="select2" name="bobot_finding[]" id="bobot_finding" required disabled>
						<option value=""></option>
						<option value="Critical">Critical</option>
						<option value="Major">Major</option>
						<option value="Moderate">Moderate</option>
						<option value="Minor">Minor</option>
						<option value="Improvement">Improvement</option>
					</select>		
				</div>
			</div>

			<div class="form-group row">
				<label class="col-form-label col-sm-3" for="status_finding_control"><?php echo lang('status_finding_control'); ?></label>
				<div class="col-sm-9">
				<select class="select2" name="status_finding_control" id="status_finding_control" required disabled>
						<option value=""></option>
						<option value="1">Design control tidak ada</option>
						<option value="2">Design control tidak efektif</option>
						<option value="3">Implementasi control tidak sesuai</option>
					</select>		
				</div>
			</div>	

			<div class="form-group row">
				<label class="col-form-label col-sm-3" for="isi_finding"><?php echo (lang('isi_finding')); ?></label>	
				<div class="col-sm-9">
					<textarea name="isi_finding[]" id="isi_finding" class="form-control editor" data-validation="required" rows="2" data-editor="inline"></textarea>
				</div>
			</div>

			<?php
			card_close();
			card_open('Attachments','mb-2');
				echo '
				<div id="list-attachments"></div>
				';
			card_close();
			// form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();

// capa //
modal_open('modal-capa','CAPA Plan','modal-xl','data-openCallback="formOpen"');
	modal_body('wizard');
		form_open(base_url('internal/finding_records/save_capa'),'post','form-capa'); ?>
		
					<div class="tab-content" id="tab-wizardContent">
						<?php for($i=0; $i < 1; $i++) { ?>
						<?php 
						col_init(2,10);
						input('hidden','id_finding','id_finding');
						card_open(lang('data_auditee'),'mb-2');
						input('text',lang('department_auditee'),'department_auditee','','','readonly');
						input('text',lang('auditee'),'nama_auditee','','','readonly');
						input('text',lang('site_auditee'),'site','','','readonly');
						// input('text',lang('audit_area'),'area_auditee','','','readonly');
						card_close();
						card_open(lang('finding'),'mb-2');
						?>
						<div class="form-group row">
							<div class="col-sm-12">
								<textarea name="isi_finding" id="isi_finding_capa" class="form-control editor" data-validation="required" rows="2" data-editor="inline" readonly></textarea>
							</div>
						</div>
						<div class="form-group row">
								<label class="col-form-label col-sm-2" for="lampiran<?php echo $i; ?>"><?php echo lang('lampiran'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="file_temuan" id="file_temuan" data-validation="" data-action=" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB" readonly>
								</div>
								<div class="input-group-append">
									<button class="btn btn-secondary btn-file-temuan" id ="btn-file-temuan" type="button"><?php echo lang('download'); ?></button>
								</div>
						</div>
						<?php
						card_close();
						card_open(lang('capa_plan'),'mb-2');

			     		?>
						<input type="hidden" name="id_capa[]" id="id_capa<?php echo $i; ?>" autocomplete="off" class="form-control id_capa" readonly>

						<div class="form-group row">
							<label class="col-form-label col-sm-2" for="nomor<?php echo $i; ?>"><?php echo lang('nomor'); ?></label>
							<div class="col-sm-10">
								<input type="text" name="nomor[]" id="nomor<?php echo $i; ?>" autocomplete="off" class="form-control nomorx" readonly placeholder="Otomatis saat disimpan">
							</div>
						</div>

						<div class="form-group row">
							<label class="col-form-label col-sm-2" for="due_date<?php echo $i; ?>"><?php echo lang('due_date'); ?></label>
							<div class="col-sm-10">
								<input type="date" name="due_date[]" id="due_date<?php echo $i; ?>" autocomplete="off" class="form-control" data-validation="required" >
							</div>
						</div>


						<div class="form-group row">
							<label class="col-form-label col-sm-2" for="isi_capa<?php echo $i; ?>"><?php echo (lang('isi_capa')); ?></label>		
							<div class="col-sm-10">
								<textarea name="isi_capa[]" id="isi_capa<?php echo $i; ?>" class="form-control editor" data-validation="required" rows="3" data-editor="inline"></textarea>
							</div>
						</div>

						<div class="form-group row">
							<!-- <label class="col-form-label col-sm-2" for="pic<?php echo $i; ?>"><?php echo lang('pic'); ?></label>
							<div class="col-sm-10">
								<input type="text" name="pic_capa[]" id="pic_capa<?php echo $i; ?>" autocomplete="off" class="form-control" data-validation="required">
							</div> -->


							<label class="col-form-label col-sm-2" for="pic<?php echo $i; ?>"><?php echo lang('pic'); ?></label>
							<div class="col-sm-10">
								<input type="hidden" name="nama_pic[]" class="nama_pic">
								<select id="username" class="form-control username select2" name="pic_capa[]" data-validation="required" aria-label="<?php echo lang('pic_capa'); ?>" placeholder="<?php echo lang('nama_pic'); ?>">
								</select>
							</div>

						</div>

						<div class="form-group row">
							<label class="col-form-label col-sm-2" for="lampiran<?php echo $i; ?>"><?php echo lang('lampiran'); ?></label>
							<div class="col-sm-9">
								<input type="text" name="file[]" id="file<?php echo $i; ?>" data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
							</div>

							<div class="input-group-append">
								<button class="btn btn-secondary btn-file" type="button"><?php echo lang('download'); ?></button>
							</div>
						</div>	
					</div>

					
				</div>
				<?php

				?>
				<?php } ?>
				<div id="additional-pasal"></div>
				<div class="form-group row">
					<div class="col-sm-3">
						<button type="button" class="btn btn-success" id="btn-add" data-nomor="<?php echo lang('nomor'); ?>" data-id_capa="<?php echo lang('id_capa'); ?>" data-perihal="<?php echo lang('perihal'); ?>" data-tanggal_berlaku="<?php echo lang('tanggal_berlaku'); ?>" data-catatan="<?php echo lang('catatan'); ?>" data-lampiran="<?php echo lang('lampiran'); ?>" data-file2="<?php echo lang('file'); ?>" data-isi_capa="<?php echo lang('isi_capa'); ?>" data-pic="<?php echo lang('pic'); ?>"><i class="fa-plus"></i> <?php echo lang('tambah_capa'); ?></button>
					</div>
					<div class="col-sm-9">
						<button type="submit" class="btn btn-success"><?php echo lang('simpan'); ?></button>
					</div>
				</div>
			</div>
		<?php
		form_close();
	modal_footer();
modal_close();

//
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('internal/finding_records/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<select id="users" class="hidden">
	<?php foreach($user as $v =>$u) {
		echo '<option value="'.$u['nama'].'" data-value="'.$u['nama'].'">'.$u['nama'].'</option>';
	} ?>
</select>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js') ?>"></script>
<script>
new DataTable('#table-data');
var select_value = '';
var select_value2 = '';
$(document).on('click','.btn-capa',function(){
	for (var editor in CKEDITOR.instances) {
            CKEDITOR.instances[editor].setData(''); // Mengosongkan editor
        }
	$('#form-capa')[0].reset();
	$('#additional-pasal').html('');
	$.ajax({
		url 	: base_url + 'internal/finding_records/add_capa',
		data 	: {id:$(this).attr('data-id')},
		type 	: 'post',
		dataType : 'json',
		success : function(response) {
			$('#modal-capa').modal();
			$('#id_finding').val(response.id);
			$('#id').val(response.id_capa);
			$('#department_auditee').val(response.department);
			$('#nama_auditee').val(response.nama_auditee);
			$('#site').val(response.site_auditee);
			$('#area_auditee').val(response.audit_area);

			$('#file_temuan').val(response.filename);
			let btn_file_temuan = $('#btn-file-temuan');
			btn_file_temuan.wrap('<a href="'+base_url+'assets/uploads/finding_records/'+response.filename+'" download></a>');


			$('#username').html('<option value=""></option>');
			$.each(response.user,function(k,v){
				$('#username').append('<option value="'+v.username+'" data-nama="'+v.nama+'">'+v.nama+'</option>');
			});

			$('#username').trigger('change');
			select_value = $('#username').html();
	
			CKEDITOR.instances['isi_finding_capa'].setData(decodeEntities(response.finding));
						
			$.each(response.detail,function(k,v){
				var x = parseInt(k);
				if( x < 1) {
					CKEDITOR.instances['isi_capa'+x].setData('');
					$('#id_capa' + x).val(v.id);
					$('#nomor' + x).val(v.nomor);
					$('#due_date' + x).val(v.dateline_capa);
					$('#isi_capa' + x).val(v.isi_capa);
					CKEDITOR.instances['isi_capa'+x].setData(decodeEntities(v.isi_capa));
					// var konten = '<a href ="'+base_url+'assets/uploads/rekanan/'+response.id_vendor+'/'+v.file+'" target="_blank"><i class="fa-download"></i></a>';
					
					// $('#file'+ x).val(v.file) ;
					console.log('ok');
					$('#username').val(v.pic_capa).trigger('change');
				} else {
					addPasal(v.nomor,v.id,v.isi_capa,v.dateline_capa,'',v.isi_capa,v.pic_capa);
				}

				setTimeout(function(){
					$('.nomorx').attr('readonly',true);
				},300);
			});
		}
	});
});

$('#btn-add').click(function(){
	initUploadFile();
	addPasal();
});
$(document).on('click','.btn-remove',function(){
	$(this).closest('.card').remove();
});

$('#btn-add-finding').click(function(){
	initUploadFile();
	addFinding();
});

$(document).on('click','.btn-remove-finding',function(){
	$(this).closest('.card').remove();
});

function initUploadFile() {

$('.input-file[type="text"]').each(function(i,j){
	var _token = "<?php echo encode_id([user('id'),(time() + 900)])?>";

	var idx 	= 'upl-file-' + i;
	var konten 	= '<form action="'+base_url+'upload/file/datetime'+'" class="hidden">';
	var accept 	= typeof $(this).attr('data-accept') == 'undefined' ? Base64.decode(upl_alw) : $(this).attr('data-accept');
	var regex 	= "(\.|\/)("+accept+")$";
	var re 		= accept == '*' ? '*' : new RegExp(regex,"i");
	var name 	= $(this).parent().children('input').attr('name');
	var nm_attr	= name.replace('[','_').replace(']','');
	konten += '<input type="file" name="document" class="input-file" id="'+idx+'">';
	konten += '<input type="hidden" name="name" value="'+nm_attr+'">';
	konten += '<input type="hidden" name="token" value="'+_token+'">';
	konten += '</form>';
	$(this).attr('data-file',idx);
	$(this).parent().find('button').attr('data-file',idx);
	$('body').append(konten);

	if(re == '*') {
		$('#' + idx).fileupload({
			maxFileSize: upl_flsz,
			autoUpload: false,
			dataType: 'text',
		}).on('fileuploadadd', function(e, data) {
			$('button[data-file="'+idx+'"]').attr('disabled',true);
			data.process();
		}).on('fileuploadprocessalways', function (e, data) {
			if (data.files.error) {
				cAlert.open('Tidak dapat mengupload file ini. ' + lang.ukuran_file_maks + ' : ' + (upl_flsz / 1024 / 1024) + 'MB');
				$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			} else {
				data.submit();
			}
		}).on('fileuploadprogressall', function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('button[data-file="'+idx+'"]').text(progress + '%');
		}).on('fileuploaddone', function (e, data) {
			$('input[data-file="'+idx+'"]').val(data.result);
			$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
		}).on('fileuploadfail', function (e, data) {
			cAlert.open('File gagal diupload','error');
			$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
		}).on('fileuploadalways', function() {
		});
	} else {
		$('#' + idx).fileupload({
			maxFileSize: upl_flsz,
			autoUpload: false,
			dataType: 'text',
			acceptFileTypes: re
		}).on('fileuploadadd', function(e, data) {
			$('button[data-file="'+idx+'"]').attr('disabled',true);
			data.process();
		}).on('fileuploadprocessalways', function (e, data) {
			if (data.files.error) {
				data.abort();
				var explode = accept.split('|');
				var acc 	= '';
				$.each(explode,function(i){
					if(i == 0) {
						acc += '*.' + explode[i];
					} else if (i == explode.length - 1) {
						acc += ', ' + lang.atau + ' *.' + explode[i];
					} else {
						acc += ', *.' + explode[i];
					}
				});
				cAlert.open(lang.file_yang_diizinkan + ' ' + acc + '. ' + lang.ukuran_file_maks + ' : ' + (upl_flsz / 1024 / 1024) + 'MB');
				$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
			} else {
				data.submit();
			}
		}).on('fileuploadprogressall', function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('button[data-file="'+idx+'"]').text(progress + '%');
		}).on('fileuploaddone', function (e, data) {
			if(data.result == 'invalid' || data.result == '') {
				cAlert.open(lang.file_gagal_diunggah,'error');
			} else {
				var spl_result = data.result.split('/');
				if(spl_result.length == 1) spl_result = data.result.split('\\');
				if(spl_result.length > 1) {
					var spl_last_str = spl_result[spl_result.length - 1].split('.');
					if(spl_last_str.length == 2) {
						$('input[data-file="'+idx+'"][type="text"]').val(data.result);
					} else {
						cAlert.open(lang.file_gagal_diunggah,'error');
					}
				} else {
					cAlert.open(lang.file_gagal_diunggah,'error');						
				}
			}
			$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
		}).on('fileuploadfail', function (e, data) {
			cAlert.open(lang.file_gagal_diunggah,'error');
			$('button[data-file="'+idx+'"]').text(lang.unggah).removeAttr('disabled');
		}).on('fileuploadalways', function() {
		});
	}
});
}

var idx = 777;
var idy = 999;

$(document).ready(function() {

    let table = $('#table-data').DataTable();
    let id_target = new URLSearchParams(window.location.search).get("id");
    id_target = decodeId(id_target)[0];
    if (id_target) {
        // lakukan search by id
        table.search(id_target).draw();

        // highlight row target
        let row = $('#table-data button[data-id="'+id_target+'"]').closest('tr');
        row.addClass('table-warning');

        // scroll ke row target
        $('html, body').animate({
            scrollTop: row.offset().top - 100
        }, 500);
    }


    // let id_trans = $('#id_transaction').val();
    // let url = base_url + 'internal/finding_records/data/';
	
    // if (id_trans != '') {
    //     // param 1 & 2 kosong, param 3 isi id_trans
    //     url += '?id='+id_trans;   // pakai double slash biar param kosong
    // } else {
    //     // pakai tahun & department
    //     url += $('#filter_tahun').val();
    //     url += '/'+$('#department').val();
    // }

    // $('[data-serverside]').attr('data-serverside', url);
    // refreshData();
});



$('#department').change(function(){
	var url = base_url + 'internal/finding_records/data/' ;
		url 	+= '/'+$('#filter_tahun').val();
		url 	+= '/'+$('#department').val(); 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});

$('#filter_tahun').change(function(){
	var url = base_url + 'internal/finding_records/data/' ;
		url 	+= '/'+$('#filter_tahun').val();
		url 	+= '/'+$('#department').val(); 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});


function formOpen() {
	is_edit = true;
	var response = response_edit;
	$('#additional-pasal').html('');
	$('#additional-finding').html('');
	is_edit = false;	
}
function formOpen1() {
	try {
		
		// Pastikan editor tersedia sebelum digunakan
		if (CKEDITOR.instances['isi_finding']) {
			CKEDITOR.instances['isi_finding'].setReadOnly(true);
			CKEDITOR.instances['isi_finding'].setData('');
		}

		const response = typeof response_edit === 'object' ? response_edit : {};
		// Isi default untuk id_finding_records (support input array)
		$('#id_finding_records').val(response.id || 0);
		console.log(response);
		// Safeguard pengisian tanggal
		if (response.tgl_mulai_audit) $('#tgl_mulai_audit').val(response.tgl_mulai_audit);
		if (response.tgl_akhir_audit) $('#tgl_akhir_audit').val(response.tgl_akhir_audit);

		// Rebuild opsi bobot_finding dari yang sudah ada (hindari duplikasi)
		$('#bobot_finding').val('').trigger('change');
		// const $bf = $('#bobot_finding');
		// if ($bf.length) {
		// 	const opts = $bf.find('option').map(function () {
		// 		return '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
		// 	}).get().join('');
		// 	$bf.html(opts);
		// }

		// Set isi finding jika ada
		if (CKEDITOR.instances['isi_finding'] && response.finding) {
			CKEDITOR.instances['isi_finding'].setData(decodeEntities(response.finding));
		}

		// Isi data ketika edit
		if (typeof response.id !== 'undefined') {
			// Set bobot_finding
			if ($('#bobot_finding').length && response.bobot_finding) {
				$('#bobot_finding').val(response.bobot_finding).trigger('change');
			}
			
			// periode audit
			if ($('#periode_audit').length && response.nomor_schedule) {
				$('#periode_audit').html(response.nomor_schedule).trigger('change');
			}

			// auditor
			if ($('#auditor').length && response.auditor) {
				$('#auditor').val(response.auditor).trigger('change');
			}

			// department auditee
			if ($('#id_department_auditee').length && response.id_department_auditee) {
				const html = '<option value=""></option><option value="' + response.id_department_auditee + '">' + (response.department || '') + '</option>';
				$('#id_department_auditee').html(html).val(response.id_department_auditee).trigger('change');
			}
			generateListAttachment(response.id);

			// Update dependent combo
			if (typeof get_department === 'function') {
				get_department();
			}
		} else {
			// Mode create: load combo periode audit
			if (typeof view_combo === 'function') {
				view_combo();
			}
			if (typeof get_department === 'function') {
				get_department();
			}
		}
	} catch (e) {
		console.error('formOpen1 error:', e);
	}
}
function view_combo() {
	$('#periode_audit').html('').trigger('change');
	$.ajax({
		url			: base_url + 'internal/finding_records/get_combo',
 		dataType	: 'json',
        success     : function(response){
        	$('#periode_audit').html(response.nomor_schedule).trigger('change');
         }
    });
}

// $('#periode_audit').change(function(){
// 	$('#institusi_audit').val($(this).find(':selected').attr('data-nama_institusi'));
// 	$('#tgl_mulai_audit').val($(this).find(':selected').attr('data-tanggal_mulai'));
// 	$('#tgl_akhir_audit').val($(this).find(':selected').attr('data-tanggal_akhir'));
// 	$('#tgl_closing_meeting').val($(this).find(':selected').attr('data-tgl_closing_meeting'));
// 	get_auditee();
// });

function addPasal(nomor,id_capa,isi_capa,tanggal_berlaku,file,lampiran, pic) {
	var _nomor 	= typeof nomor == undefined ? '' : nomor;
	var _id_capa 	= typeof id_capa == undefined ? '' : id_capa;
	var _tanggal_berlaku 	= typeof tanggal_berlaku == undefined ? '' : tanggal_berlaku;
	var _pic 	= typeof pic == undefined ? '' : pic;
	var _isi_capa 	= typeof isi_capa == undefined ? '' : isi_capa;
	var _lampiran 	= typeof lampiran == undefined ? '' : lampiran;
	var _file 	= typeof file == undefined ? '' : file;
	var _token = "<?php echo encode_id([user('id'),(time() + 900)])?>";

	
	var today = new Date();
	var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
	var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
	var dateTime = date+' '+time;
	
	// $('#username').html('<option value=""></option>');
	// $('#users').each(function(){
	// 	$('#username').append('<option value="'+$(this).attr('value')+'" data-nama="'+$(this).attr('data-nama')+'">'+$(this).val()+'</option>');
	// });

	// // $('#username').trigger('change');
	// select_value = $('#username').html();

	var konten = '<div class="card mb-2">'
		+ '<div class="card-header">'
			+ '<input type="hidden" name="id_capa[]" id="id_capa'+idx+'" autocomplete="off" class="form-control id_capa" readonly>'
			+ '<div class="form-group row">'
				+ '<label class="col-form-label col-sm-2" for="nomor'+idx+'">'+$('#btn-add').attr('data-nomor')+'</label>'
				+ '<div class="col-sm-10">'
					+ '<input type="text" name="nomor[]" id="nomor'+idx+'" autocomplete="off" class="form-control nomorx" readonly placeholder="Otomatis saat disimpan">'
				+ '</div>'
			+ '</div>'

			+ '<div class="form-group row">'
			+ '<label class="col-form-label col-sm-2" for="due_date'+idx+'">'+$('#btn-add').attr('data-tanggal_berlaku')+'</label>'
			+ '<div class="col-sm-10">'
				+ '<input type="date" name="due_date[]" id="due_date'+idx+'" autocomplete="off" class="form-control" data-validation="required">'
				+ '</div>'
			+ '</div>'
		+ '</div>'	
			
		+ '<div class="card-body">'
		+ '<div class="form-group row">'
		+ '<label class="col-form-label col-sm-2" for="isi_capa'+idx+'">'+$('#btn-add').attr('data-isi_capa')+'</label>'
		+ '<div class="col-sm-10">'
			+ '<textarea name="isi_capa[]" id="isi_capa'+idx+'" class="form-control editor" data-validation="required" rows="3"></textarea>'
			+ '</div>'
		+ '</div>'

		+ '<div class="form-group row">'
			+ '<label class="col-form-label col-sm-2" for="lampiran'+idx+'">'+$('#btn-add').attr('data-pic')+'</label>'
			+ '<div class="col-sm-10">'
			+ '<input type="hidden" name="nama_pic[]" class="nama_pic">'
			+ '<select class="form-control username" name="pic_capa[]" id="username'+idx+'" data-validation="required" aria-label="'+$('#pic_capa').attr('aria-label')+'">'+select_value+'</select> '
			+ '</div>'
		+ '</div>'

		+ '<div class="form-group row">'
			+ '<label class="col-form-label col-sm-2" for="lampiran'+idx+'">'+$('#btn-add').attr('data-lampiran')+'</label>'
			+ '<div class="col-sm-9">'
				+ '<input type ="text" name="file[]" id="file'+idx+'" data-validation="" data-action ="'+base_url+'upload/file/datetime'+'" data-token ="'+_token+'" autocomplete="off" class="form-control input-file" value="" placeholder="maksimal 5MB">'
				+ '</div>'

			+ '<div class="input-group-append">'
			+ '<button class="btn btn-secondary btn-file" type="button"><?php echo lang('download'); ?></button>'
			+ '</div>'
			+ '</div>'
		+ '</div>'


		
		+ '<div class="card-footer">'
			+ '<button type="button" class="btn btn-danger btn-remove"><i class="fa-times"></i> '+lang.hapus+'</button>';
		+ '</div>'
	+ '</div>';
	$('#additional-pasal').append(konten);

	var $t = $('#additional-pasal .username:last-child');
	$t.select2({
		dropdownParent : $t.parent(),
		placeholder : ''
	});

	var c_id = 'isi_capa'+idx;
	CKEDITOR.inline( c_id ,{
		toolbar : [
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
			'/',
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
		],
		filebrowserImageBrowseUrl : base_url + 'assets/plugins/kcfinder/index.php?type=images',
		width : 'auto',
		height : '250',
		language : $('meta[name="applang"]').attr('content')
	});
	CKEDITOR.instances[c_id].on('change', function() { 
		var vdata = CKEDITOR.instances[c_id].getData();
		$('#' + c_id).val(vdata);
	});


	if(_nomor) $('#nomor' + idx).val(_nomor);
	if(_id_capa) $('#id_capa' + idx).val(_id_capa);
	if(_isi_capa) $('#isi_capa' + idx).val(_isi_capa);
	if(_tanggal_berlaku) $('#due_date' + idx).val(_tanggal_berlaku);
	if(_lampiran) $('#lampiran' + idx).val(_lampiran);
	if(_pic) $('#username' + idx).val(_pic).trigger('change');

	if(_file) $('#file' + idx).val(_file);
	if(_isi_capa) {
		$('#isi_capa' + idx).val(_isi_capa);
		CKEDITOR.instances['isi_capa'+idx].setData(decodeEntities(_isi_capa));
	}


	idx++;
	initUploadFile();
}

$('#auditee').change(function(){
	get_department();
});

function get_department() {
	readonly_ajax = false;
	$.ajax({
		url : base_url + 'internal/finding_records/get_department_auditee',
		data : {id : $('#auditee').val()},
		type : 'POST',
		success	: function(response) {
			$('#id_section_department').html(response.department);
			readonly_ajax = true;
		}
	});
}

function get_auditee() {
	console.log('ok');
	readonly_ajax = false;
	$.ajax({
		url : base_url + 'internal/finding_records/get_auditee',
		data : {id : $('#periode_audit').val()},
		type : 'POST',
		success	: function(response) {
			$('#auditee').html(response.auditee1).trigger('change');
			$('#auditee').val(response_edit.auditee).trigger('change');
			readonly_ajax = true;
		}
	});
}

function generateListAttachment(rowId){
	$.ajax({
		url: base_url + 'internal/finding_records/get_attachments',
		type: 'POST',
		dataType: 'json',
		data: { id: rowId },
		success: function(res) {
			$('#list-attachments').html('');

					let html = `<div class="form-group row">
						<div class="col-md-12">
								<table class="table table-sm table-hover mb-0">
									<thead>
										<tr>
											<th style="width:45%">Nama File</th>
											<th style="width:45%">Tanggal</th>
											<th class="text-center">Aksi</th>
										</tr>
									</thead>
									<tbody>`;
									$.each(res, function(index, file) {
										html += `<tr data-id-assignment="${file.id_assignment}">
											<td class="align-middle">${file.filename}</td>
											<td class="align-middle">${file.created_at}</td>
											<td class="align-middle text-center" width="1px">
												<button type="button" class="btn btn-sm btn-primary btn-icon-only mr-2 download-file" title="Download File" data-id-file="${file.id_file}">
													<i class="fas fa-download"></i>
												</button>
											</td>`	
										})
									html += `</tbody>
								</table>
					</div>`;
					$('#list-attachments').html(html);
				}
			});

		$(document).on('click', '.download-file', function() {
			const fileId = $(this).data('id-file');
			if (!fileId) return;
			window.open(base_url + 'internal/finding_records/download_file?id=' + fileId, '_blank');
		});
		}

</script>