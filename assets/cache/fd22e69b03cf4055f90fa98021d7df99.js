

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
	var _token = "ZbFaVOMZo";

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
	var _token = "ZbFaVOMZo";

	
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
			+ '<button class="btn btn-secondary btn-file" type="button">Unggah</button>'
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
	var _token = "ZbFaVOMZo";

	
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
				// + '<input type="text" name="file_finding'+'['+(_lampiran != undefined ? _lampiran : idy)+']" id="file_finding'+idy+'" data-validation="required" data-action="http://localhost/internal-audit/upload/file/datetime" data-token="ZbFaVOMZo" autocomplete="off" class="form-control input-file" placeholder="maksimal 51MB" value="'+(_file != undefined ? _file : '')+'">'
				+ '</div>'

			+ '<div class="input-group-append">'
			+ '<button class="btn btn-secondary btn-file" type="button">Unggah</button>'
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

