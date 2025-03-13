<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
		<button type="button" class="btn btn-primary btn-sm" onclick="fnExcelReport()"><i class="fa-download"></i>Export</button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
     
<div class="filter-panel">
	<div class="filter-body">
		<?php
			form_open('','','form-filter');
				col_init(12,12);
                ?>
				<div class="form-group row">
					<div class="col-sm-12">
						<div class="input-group">
						<select class="select2 filter custom-select" id="filter-tahun">
							<?php foreach($tahun as $t) {
								echo '<option value="'.$t->tahun.'">'.$t->tahun.'</option>';
							} ?>
						</select>
						</div>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-sm-12">
						<div class="input-group">
							<select class = "select2 infinity custom-select" style="width: 300px;" id="department">
								<?php if(user('id_group') != AUDITEE) { ?>
								<option value="ALL">ALL Department</option>
								<?php } ?>
								<?php foreach($department as $d){ ?>
								<option value="<?php echo $d['id']; ?>"><?php echo $d['description'] . ' | '. $d['section_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div
				
				?>

				<a class="btn btn-sm btn-info" id="btn-show"><?php echo 'View Report'; ?></a>   
            <?php 

            form_close();
       ?>
    </div>
</div>

<div class="content-body">
<?php
	// table_open('',true,'','','id="tData"');
	table_open('table table-app table-bordered table-striped table-hover',true,'','','data-context="false" id="tData"');
		thead();
			tr();
				th(lang('no'),'text-center','width ="60"');
				th(lang('finding') . ' / ' . lang('capa'),'text-center','');
				th(lang('pic'),'text-center','width="100" ');
				th(lang('date_line'),'text-center','width="100" ');
				th(lang('progress'),'text-center','width="100" ');
				th(lang('status'),'text-center','width="90" ');
				th(lang('score'),'text-center','width="100" ');
				th(lang('evidence'),'text-center','width="100" ');
				th('Action','','width="100"');
		tbody();
	table_close();
	?>

</div>
    
<?php 
	modal_open('modal-form','Progress Capa','modal-xl','data-openCallback="formOpen"');
		modal_body();
			form_open(base_url('internal/capa_monitoring/save'),'post','form');
				col_init(3,9);
				input('','id','id');
				input('','id_finding','id_finding');
				input('hidden','id_progress','id_progress');
				input('','activeTab','activeTab');
				?>
					
				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="isi_capa"><?php echo (lang('capa_plan')); ?></label>	
					<div class="col-sm-9">
						<textarea name="isi_capa" id="isi_capa" class="form-control editor" data-validation="required" rows="1" data-editor="inline"></textarea>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="keterangan_progress"><?php echo (lang('keterangan_progress')) ; ?></label>        
					<div class="col-sm-9">
						<div class="card">
							<div class="card-body">
								<ul class="nav nav-tabs" id="myTab" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="progress-1" data-toggle="tab" href="#progress_1" role="tab" aria-controls="general" aria-selected="true">Progress-1</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="progress-2" data-toggle="tab" href="#progress_2" role="tab" aria-controls="email" aria-selected="true">Progress-2</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="progress-3" data-toggle="tab" href="#progress_3" role="tab" aria-controls="email" aria-selected="true">Progress-3</a>
									</li>
								</ul>
								<div class="tab-content" id="myTabContent">
									<div class="tab-pane fade show active" id="progress_1" role="tabpanel" aria-labelledby="general-tab">
										<div class="card-header"><b>Keterangan Progress</b></div>
										<?php 
										input('hidden','no_progress1','no_progress1','','1');
										?>
										<textarea name="keterangan_progress_1" id="keterangan_progress_1" class="form-control xxeditor" data-validation="required" rows="4" xxdata-editor="inline"></textarea>
										<br>
										<div class="form-group row">
											<label class="col-form-label col-sm-3" for="evidence_base"><?php echo lang('evidence_base'); ?></label>        
											<div class="col-sm-9">
												<input type="text" name="evidence_base" id="evidence_base"  data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
											</div>
										</div>

										<?php if(user('id_group') != AUDITEE) { ?>
											<div class="card-header"><b>Comment Auditor</b></div>
											<textarea name="comment_progress_1" id="comment_progress_1" class="form-control xxeditor" rows="4" xxdata-editor="inline"></textarea>
											<br>
											<div id="status_progress" class="form-group row">
												<label class="col-form-label col-sm-3" for="status_capa1"><?php echo (lang('status_capa')); ?></label>        
												<div class="col-sm-9">
													<select class="select2 infinity custom-select" name="status_capa1" id="status_capa1">
														<?php foreach($status_cp as $u) { 
															echo '<option value="'.$u['id'].'" data-value="'.$u['status'].'">'.$u['status'].'</option>';
														}
														?>
													</select>
												</div>
											</div>
										<?php }; ?>
										</br>
									</div>

									<!-- Tab 2 -->
									<div class="tab-pane fade" id="progress_2" role="tabpanel" aria-labelledby="email-tab">
										<div class="card-header"><b>Keterangan Progress</b></div>
										<?php 
										input('hidden','no_progress2','no_progress2','','1');
										?>
										<textarea name="keterangan_progress_2" id="keterangan_progress_2" class="form-control xxeditor" data-validation="required" rows="4" xxdata-editor="inline"></textarea>
										<br>
										<div class="form-group row">
											<label class="col-form-label col-sm-3" for="evidence_base"><?php echo lang('evidence_base'); ?> 22</label>        
											<div class="col-sm-9">
												<input type="text" name="evidence_base" id="evidence_base"  data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
											</div>
										</div>

										<?php if(user('id_group') != AUDITEE) { ?>
											<div class="card-header"><b>Comment Auditor</b></div>
											<textarea name="comment_progress_2" id="comment_progress_2" class="form-control xxeditor" rows="4" xxdata-editor="inline"></textarea>
											<br>
											<div id="status_progress" class="form-group row">
												<label class="col-form-label col-sm-3" for="status_capa2"><?php echo (lang('status_capa')); ?></label>        
												<div class="col-sm-9">
													<select class="select2 infinity custom-select" name="status_capa2" id="status_capa2">
														<?php foreach($status_cp as $u) { 
															echo '<option value="'.$u['id'].'" data-value="'.$u['status'].'">'.$u['status'].'</option>';
														}
														?>
													</select>
												</div>
											</div>
										<?php }; ?>
										</br>
									</div>
									<!-- Tab 2 end -->

									<!-- Tab 3 -->
									<div class="tab-pane fade" id="progress_3" role="tabpanel" aria-labelledby="email-tab">
										<div class="card-header"><b>Keterangan Progress</b></div>
										<?php 
										input('hidden','no_progress3','no_progress3','','1');
										?>
										<textarea name="keterangan_progress_3" id="keterangan_progress_3" class="form-control xxeditor" data-validation="required" rows="4" xxdata-editor="inline"></textarea>
										<br>
										<div class="form-group row">
											<label class="col-form-label col-sm-3" for="evidence_base"><?php echo lang('evidence_base'); ?></label>        
											<div class="col-sm-9">
												<input type="text" name="evidence_base" id="evidence_base"  data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
											</div>
										</div>

										<?php if(user('id_group') != AUDITEE) { ?>
											<div class="card-header"><b>Comment Auditor</b></div>
											<textarea name="comment_progress_3" id="comment_progress_3" class="form-control xxeditor" rows="4" xxdata-editor="inline"></textarea>
											<br>
											<div id="status_progress" class="form-group row">
												<label class="col-form-label col-sm-3" for="status_capa3"><?php echo (lang('status_capa')); ?></label>        
												<div class="col-sm-9">
													<select class="select2 infinity custom-select" name="status_capa3" id="status_capa3">
														<?php foreach($status_cp as $u) { 
															echo '<option value="'.$u['id'].'" data-value="'.$u['status'].'">'.$u['status'].'</option>';
														}
														?>
													</select>
												</div>
											</div>
										<?php }; ?>
										</br>
									</div>
									<!-- Tab 3 end -->
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php

				form_button(lang('simpan'),lang('batal'));
			form_close();
		modal_footer();
	modal_close();
	modal_open('modal-sort',lang('atur_posisi'),'modal-lg','modal-info');
		modal_body();
		modal_footer();
			echo '<form><button type="submit" class="btn btn-success" id="save-posisi">'.lang('simpan').'</button></form>';
	modal_close();
?>

<script type="text/javascript" src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js') ?>"></script>

<script type="text/javascript">

$('#myTab a').on('click', function (e) {
            var activeTabId = $(this).attr('id');
            $('#activeTab').val(activeTabId);
        });

$(document).ready(function(){
	$("#progress-2, #progress-3").hide();

	var activeTabId = $('#myTab .nav-link.active').attr('id');  // Get the ID of the currently active tab
    
    // Set the value of the hidden input to the active tab's ID
    $('#activeTab').val(activeTabId);

	$('.tab, nav-item, nav-tabs').click(function() {
		// Tindakan yang terjadi saat tab diklik, misalnya:
		var targetContent = $(this).data('target'); // Mendapatkan konten berdasarkan data target
		$(targetContent).show().siblings().hide(); // Menampilkan konten yang sesuai, sembunyikan yang lain
	});

	getData();
});

$('#form-filter select').change(function() {
    getData();
});


var xhr = null;
function getData() {
	if(xhr != null) {
		xhr.abort();
		xhr = null;
	}
	cLoader.open(lang.memuat_data + '...');
	$('.table-app tbody').html('');
	xhr = $.ajax({
		url 	: base_url + 'internal/capa_monitoring/data',
		data 	: {
			tahun : $('#filter-tahun').val(),
			dept : $('#department').val(),
		},
		type	: 'post',
		success	: function(response) {

			$('.table-app tbody').html(response);

			cLoader.close();
			if(response) {
				fixedTable();

				setTimeout(syncTable(),300);
			} else {
				$('.fixed-table.header2, .fixed-table.body').remove();
			}
		}
	});
}

function formOpen() {	
	var response = response_edit;
	var activeTabId = $('#myTab .nav-link.active').attr('id');  
    $('#activeTab').val(activeTabId);
	if(typeof response.id != 'undefined') {
		CKEDITOR.instances['isi_capa'].setReadOnly(true);
		CKEDITOR.instances['isi_capa'].setData(decodeEntities(response.isi_capa));
		$('#id_progress').val(0);

		$("#progress-1, #progress-2, #progress-3").hide();  

		if(response.progress_ke == 1) {
			$("#progress-1").show();
			$("#progress-1").click();
		} else if(response.progress_ke == 2) {
			$("#progress-1, #progress-2").show()
			$("#progress-2").click();
		} else if(response.progress_ke == 3) {
			$("#progress-1, #progress-2, #progress-3").show();
			$("#progress-3").click();
		} else {
			$("#progress-1").show();
			$('#progress-1').addClass('active');
			$('#progress-2, #progress-3').removeClass('active');
			$('#progress-1').tab('show'); 
		}


		$.each(response.progress, function(k, v) {
			// Kosongkan semua field terlebih dahulu
			$("#keterangan_progress_1").val('');
			$("#keterangan_progress_2").val('');
			$("#keterangan_progress_3").val('');

			// Tentukan progressField berdasarkan no_progress
			let progressField = null;
			if (v.no_progress == 1) {
				progressField = $("#keterangan_progress_1");
			} else if (v.no_progress == 2) {
				progressField = $("#keterangan_progress_2");
			} else if (v.no_progress == 3) {
				progressField = $("#keterangan_progress_3");
			}

			// Set nilai progressField
			if (progressField) {
				progressField.val(v.progress);
			}
		});

	} 
}


$(document).on('click','.btn-detail',function(){
	__id = $(this).attr('data-id');
	$.get(base_url + 'internal/capa_monitoring/detail/' + __id, function(response){
		cInfo.open(lang.detil,response);
	});
});

$(document).on('click','.btn-act-export',function(e){
		// alert('x');die;
		e.preventDefault();
		$.redirect(base_url + 'internal/capa_monitoring/export/', 
            {tahun:$('#tahun').val(),
            status:$('#status').val(),
            nomor:$('#nomor').val(),
            nomor_permohonan:$('#nomor_permohonan').val(),
            jenis_bantuan : $('#jenis_bantuan').val(),
            } , 'get');
	});


var id_email = 0;
$(document).on('click','.btn-send-reminder',function(e){
	e.preventDefault();
	id_email = $(this).attr('data-id');
	cConfirm.open(lang.apakah_anda_yakin + '?','lanjut');
});
function lanjut() {
	$.ajax({
		url : base_url + 'internal/capa_monitoring/capa_nottification',
		data : {id:id_email},
		type : 'post',
		dataType : 'json',
		success : function(res) {
			cAlert.open(res.message,res.status,'refreshData');
		}
	});
}
/// test ///
</script>