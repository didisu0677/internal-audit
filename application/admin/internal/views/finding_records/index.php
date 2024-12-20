<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label class=""><?php echo lang('department'); ?> &nbsp</label>					
			<select class = "select2 infinity custom-select" style="width: 180px;" id="department">
				<?php if(user('id_group') != AUDITEE) { ?>
				<option value="ALL">ALL Department</option>
				<?php } ?>
				<?php foreach($department as $d){ ?>
				<option value="<?php echo $d['id']; ?>"><?php echo $d['department']; ?></option>
				<?php } ?>
			</select>

			

			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('internal/finding_records/data'),'tbl_finding_records');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('periode_audit'),'','data-content="periode_audit"');
				// th(lang('institusi_audit'),'','data-content="nama_institusi" data-table="tbl_institusi_audit"');
				th(lang('auditor'),'','data-content="nama_auditor"');
				th(lang('tgl_mulai_audit'),'','data-content="tgl_mulai_audit" data-type="daterange"');
				// th(lang('tgl_akhir_audit'),'','data-content="tgl_akhir_audit" data-type="daterange"');
				// th(lang('tgl_closing_meeting'),'','data-content="tgl_closing_meeting" data-type="daterange"');
				th(lang('site_auditee'),'','data-content="site_auditee"');
				th(lang('department'),'','data-content="department" data-table="tbl_m_department tbl_department_auditee"');
				th(lang('audit_area'),'','data-content="audit_area"');
				th(lang('finding_description') . ' description','','data-content="finding"');
				th(lang('bobot'),'','data-content="bobot_finding"');
				// th(lang('bobot_finding'),'','data-content="bobot_finding"');
				th(lang('status'),'','data-content="status_finding" data-badge="warna_status" data-replace="0:Open|1:Close"');


				// th(lang('capa'),'','data-content="capa"');
				// th(lang('status_capa'),'','data-content="status_capa"');
				// th(lang('follow_up'),'','data-content="follow_up"');
				// th(lang('capa_score'),'','data-content="capa_score"');
				// th(lang('achivement'),'','data-content="achivement"');
				// th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 

modal_open('modal-form','','modal-lg','data-openCallback="formOpen1"');
	modal_body();
		form_open(base_url('internal/finding_records/save'),'post','form');
			col_init(3,9);
			for($i=0; $i < 1; $i++) { 
			input('hidden','id','id');
			card_open(lang('info_audit'),'mb-2');
				select2(lang('periode_audit'),'periode_audit','required');
				// input('text',lang('institusi_audit'),'institusi_audit');
				
				// input('date',lang('tgl_mulai_audit'),'tgl_mulai_audit');
				?>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="institusi_audit"><?php echo lang('institusi_audit'); ?></label>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="institusi_audit" id="institusi_audit" class="form-control" autocomplete="off" >
							
						</div>
					</div>

					<label class="col-sm-2 col-form-label" for="auditor"><?php echo lang('auditor'); ?></label>
					<div class="col-sm-3">
						<div class="input-group">
						<select class="select2 infinity custom-select" style="width: 80px;" id="auditor" name="auditor">
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
							<input type="text" name="tgl_mulai_audit" id="tgl_mulai_audit" class="form-control dtp1" autocomplete="off">
							
						</div>
					</div>

					<label class="col-sm-2 col-form-label" for="tgl_akhir_audit"><?php echo lang('tgl_akhir_audit'); ?></label>
					<div class="col-sm-3">
						<div class="input-group">
							<input type="text" name="tgl_akhir_audit" id="tgl_akhir_audit" class="form-control dtp1" autocomplete="off" value="<?php echo setting('jumlah_salah_password'); ?>">
							
						</div>
					</div>
				</div>
	
				<?php
				col_init(3,9);
				// input('date',lang('tgl_akhir_audit'),'tgl_akhir_audit');
				// input('date',lang('tgl_closing_meeting'),'tgl_closing_meeting');
				// select2(lang('auditor'),'auditor','required',$auditor,'id','nama');
			card_close();
			
			card_open(lang('auditee'),'mb-2');
				select2(lang('auditee'),'auditee','required');
				select2(lang('site_auditee'),'site_auditee','required|infinity',['Head Office (HO)','Factory']);
				select2(lang('department_auditee'),'id_department_auditee','required',$department,'id','department');
				input('text',lang('audit_area'),'audit_area');
			card_close();

			card_open(lang('finding'),'mb-2');?>
			<input type="hidden" name="id_finding_records[]" id="id_finding_records">
			<div class="form-group row">

				<label class="col-form-label col-sm-3" for="bobot_finding"><?php echo lang('bobot_finding'); ?></label>
				<div class="col-sm-9">
				<select class="select2" name="bobot_finding[]" id="bobot_finding" required>
						<option value=""></option>
						<option value="Critical">Critical</option>
						<option value="Major">Major</option>
						<option value="Moderate">Moderate</option>
						<option value="Minor">Minor</option>
					</select>		
			</div>
			</div>	

			<?php
			// select2(lang('bobot_finding'),'bobot_finding','required',['Critical', 'Major', 'Modearate', 'Minor']);
			?>
			<div class="form-group row">

				<label class="col-form-label col-sm-3" for="lampiran"><?php echo lang('lampiran'); ?></label>
				<div class="col-sm-6">
					<input type="text" name="file_finding[]" id="file_finding <?php echo $i; ?>"  data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
				</div>
				<div class="input-group-append">
					<button class="btn btn-secondary btn-file" type="button"><?php echo lang('unggah'); ?></button>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-form-label col-sm-3" for="isi_finding"><?php echo (lang('isi_finding')); ?></label>	
				<div class="col-sm-9">
					<textarea name="isi_finding[]" id="isi_finding<?php echo $i; ?>" class="form-control editor" data-validation="required" rows="2" data-editor="inline"></textarea>
				</div>
			</div>

			<?php

			card_close();
			}
			?>
			<div id="additional-finding"></div>
			<div class="form-group row">
				<div class="col-sm-3">
					<button type="button" class="btn btn-success" id="btn-add-finding" data-id_finding="<?php echo lang('id_finding'); ?>" data-bobot_finding="<?php echo lang('bobot_finding'); ?>" data-lampiran="<?php echo lang('lampiran'); ?>" data-file_finding="<?php echo lang('file'); ?>" data-isi_finding="<?php echo lang('isi_finding'); ?>"><i class="fa-plus"></i> <?php echo lang('add_finding'); ?></button>
				</div>

			</div>
			<?php
			// input('text',lang('capa'),'capa');
			// input('text',lang('status_capa'),'status_capa');
			// input('text',lang('follow_up'),'follow_up');
			// input('text',lang('capa_score'),'capa_score');
			// input('text',lang('achivement'),'achivement');
			// toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();

// capa //
modal_open('modal-capa','','modal-xl','data-openCallback="formOpen"');
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
						input('text',lang('audit_area'),'area_auditee','','','readonly');
						card_close();
						card_open(lang('finding'),'mb-2');
						?>
						<div class="form-group row">
							<div class="col-sm-12">
								<textarea name="isi_finding" id="isi_finding" class="form-control editor" data-validation="required" rows="2" data-editor="inline" readonly></textarea>
							</div>
						</div>
						<div class="form-group row">
								<label class="col-form-label col-sm-2" for="lampiran<?php echo $i; ?>"><?php echo lang('lampiran'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="file[]" id="file<?php echo $i; ?>" data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
								</div>
								<div class="input-group-append">
									<button class="btn btn-secondary btn-file" type="button"><?php echo lang('unggah'); ?></button>
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
								<select id="username" class="form-control username select2" name="pic_capa[]" data-validation="required" aria-label="<?php echo lang('pic_capa'); ?>" placeholder="<?php echo lang('nama_panitia'); ?>">
								</select>
							</div>

						</div>

						<div class="form-group row">
							<label class="col-form-label col-sm-2" for="lampiran<?php echo $i; ?>"><?php echo lang('lampiran'); ?></label>
							<div class="col-sm-9">
								<input type="text" name="file[]" id="file<?php echo $i; ?>" data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
							</div>

							<div class="input-group-append">
								<button class="btn btn-secondary btn-file" type="button"><?php echo lang('unggah'); ?></button>
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


<!-- <form action="<?php echo base_url('upload/file/datetime'); ?>" class="hidden">
	<input type="hidden" name="name" value="field_document">
	<input type="hidden" name="token" value="<?php echo encode_id([user('id'),(time() + 900)]); ?>">
	<input type="file" name="document" id="upl-file">
</form> -->

<select id="users" class="hidden">
	<?php foreach($user as $v =>$u) {
		echo '<option value="'.$u['nama'].'" data-value="'.$u['nama'].'">'.$u['nama'].'</option>';
	} ?>
</select>

<script type="text/javascript" src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js') ?>"></script>
<script>

var select_value = '';
var select_value2 = '';
$(document).on('click','.btn-capa',function(){


	// $('#username').trigger('change');
	// select_value = $('#username').html();
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

			$('#username').html('<option value=""></option>');
			$.each(response.user,function(k,v){
				$('#username').append('<option value="'+v.username+'" data-nama="'+v.nama+'">'+v.nama+'</option>');
			});

			$('#username').trigger('change');
			select_value = $('#username').html();
	
			CKEDITOR.instances['isi_finding'].setData(decodeEntities(response.finding));
						
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

				} else {
					addPasal(v.nomor,v.id,v.isi_capa,v.dateline_capa,'',v.isi_capa,'','');
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
	var url = base_url + 'internal/finding_records/data/' ;
		url 	+= '/'+$('#department').val() 
	$('[data-serverside]').attr('data-serverside',url);
	refreshData();
});	

$('#department').change(function(){
	var url = base_url + 'internal/finding_records/data/' ;
		url 	+= '/'+$('#department').val() 
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

function formOpen1() {	var response = response_edit;
	$('#additional-file').html('');
	$('#additional-finding').html('');

	$('#bobot_finding').trigger('change');
	select_value2 = $('#bobot_finding').html();
    $('#id_finding_records').val(0);
	get_department();
	
	if(typeof response.id != 'undefined') {

		$('#periode_audit').html(response.nomor_schedule).trigger('change');
		$('#id').val(response.id);
    	$('#id_finding_records').val(response.id);
		get_department();
		$('#auditor').val(response.auditor).trigger('change');
		$('#uditee').val(response.auditee).trigger('change');

		$('#id_department_auditee').html('<option value=""></option><option value="'+response.id_department_auditee+'">'+response.department+'</option>').trigger('change');
		$('#id_department_auditee').val(response.id_department_auditee).trigger('change');
		$.each(response.detail,function(k,v){
			var x = parseInt(k);
			if( x < 1) {
				CKEDITOR.instances['isi_finding'+x].setData('');
				$('#id_finding_records').val(v.id);
				$('#bobot_finding').val(v.bobot_finding).trigger('change');
				CKEDITOR.instances['isi_finding'+x].setData(decodeEntities(v.finding));
				// var konten = '<a href ="'+base_url+'assets/uploads/rekanan/'+response.id_vendor+'/'+v.file+'" target="_blank"><i class="fa-download"></i></a>';
				
				// $('#file'+ x).val(v.file) ;

			} else {
				addFinding(v.id,v.bobot_finding,v.file,'',v.finding);
			}

			setTimeout(function(){
				$('.nomorx').attr('readonly',true);
			},300);
		});


	} else {
		view_combo();
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

$('#periode_audit').change(function(){
	$('#institusi_audit').val($(this).find(':selected').attr('data-nama_institusi'));
	$('#tgl_mulai_audit').val($(this).find(':selected').attr('data-tanggal_mulai'));
	$('#tgl_akhir_audit').val($(this).find(':selected').attr('data-tanggal_akhir'));
	$('#tgl_closing_meeting').val($(this).find(':selected').attr('data-tgl_closing_meeting'));
	get_auditee();
});

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
			+ '<button class="btn btn-secondary btn-file" type="button"><?php echo lang('unggah'); ?></button>'
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
	if(_file) $('#file' + idx).val(_file);
	if(_isi_capa) {
		$('#isi_capa' + idx).val(_isi_capa);
		CKEDITOR.instances['isi_capa'+idx].setData(decodeEntities(_isi_capa));
	}
	idx++;
	initUploadFile();
}

function addFinding(id_finding_records,bobot_finding,file,lampiran, isi_finding) {
	var _id_finding_records 	= typeof id_finding_records == undefined ? '' : id_finding_records;
	var _bobot_finding 	= typeof bobot_finding == undefined ? '' : bobot_finding;
	var _isi_finding 	= typeof isi_finding == undefined ? '' : isi_finding;
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

	// $('#bobot_finding').trigger('change');
	// select_value2 = $('#bobot_finding').html();


	var konten = '<div class="card mb-2">'
			
		+ '<div class="card-body">'
		+ '<input type="hidden" name="id_finding_records[]" id="id_finding_records'+idy+'" autocomplete="off" class="form-control id_capa" readonly>'
		+ '<div class="form-group row">'
			+ '<label class="col-form-label col-sm-3" for="bobot_finding'+idy+'">'+$('#btn-add-finding').attr('data-bobot_finding')+'</label>'
			+ '<div class="col-sm-9">'
			+ '<select class="form-control bobot_temuan" name="bobot_finding[]" id="bobot_finding'+idy+'" data-validation="required" aria-label="'+$('#bobot_finding').attr('aria-label')+'">'+select_value2+'</select> '
			+ '</div>'
		+ '</div>'
		
		+ '<div class="form-group row">'
			+ '<label class="col-form-label col-sm-3" for="lampiran'+idy+'">'+$('#btn-add').attr('data-lampiran')+'</label>'
			+ '<div class="col-sm-6">'
				+ '<input type ="text" name="file_finding[]" id="file_finding'+idy+'" data-validation="" data-action ="'+base_url+'upload/file/datetime'+'" data-token ="'+_token+'" autocomplete="off" class="form-control input-file" value="" placeholder="maksimal 5MB">'
				// + '<input type="text" name="file_finding'+'['+(_lampiran != undefined ? _lampiran : idy)+']" id="file_finding'+idy+'" data-validation="required" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" placeholder="<?php echo lang('maksimal'); ?> 51MB" value="'+(_file != undefined ? _file : '')+'">'
				+ '</div>'

			+ '<div class="input-group-append">'
			+ '<button class="btn btn-secondary btn-file" type="button"><?php echo lang('unggah'); ?></button>'
			+ '</div>'
			+ '</div>'

		+ '<div class="form-group row">'
		+ '<label class="col-form-label col-sm-3" for="isi_finding'+idy+'">'+$('#btn-add-finding').attr('data-isi_finding')+'</label>'
		+ '<div class="col-sm-9">'
		
		+ '<input type="hidden" name="id_finding[]" id="id_finding'+idy+'" autocomplete="off" class="form-control id_finding" readonly>'
			+ '<textarea name="isi_finding[]" id="isi_finding'+idy+'" class="form-control editor" data-validation="required" rows="3"></textarea>'
			+ '</div>'
		+ '</div>'
		+ '</div>'

		+ '<div class="card-footer">'
			+ '<button type="button" class="btn btn-danger btn-remove-finding"><i class="fa-times"></i> '+lang.hapus+'</button>';
		+ '</div>'
	+ '</div>';
	$('#additional-finding').append(konten);

	var $t1 = $('#additional-finding .bobot_temuan:last-child');
	$t1.select2({
		dropdownParent : $t1.parent(),
		placeholder : ''
	});

	var c_id = 'isi_finding'+idy;
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


	if(_id_finding_records) $('#id_finding_records' + idy).val(_id_finding_records);
	// select_value2 = $('#bobot_finding').html();
	// if(_bobot_finding) $('#bobot_finding' + idy).val(_bobot_finding).trigger('change');

	if(_isi_finding) $('#isi_finding' + idy).val(_isi_finding);
	if(_lampiran) $('#lampiran' + idy).val(_lampiran);
	if(_file) $('#file' + idy).val(_file);
	if(_isi_finding) {
		$('#isi_finding' + idy).val(_isi_finding);
		CKEDITOR.instances['isi_finding'+idy].setData(decodeEntities(_isi_finding));
	}
	idy++;
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
			$('#id_department_auditee').html(response.department);
			readonly_ajax = true;
		}
	});
}

function get_auditee() {
	readonly_ajax = false;
	$.ajax({
		url : base_url + 'internal/finding_records/get_auditee',
		data : {id : $('#periode_audit').val()},
		type : 'POST',
		success	: function(response) {
			$('#auditee').html(response.auditee);
			readonly_ajax = true;
		}
	});
}

</script>