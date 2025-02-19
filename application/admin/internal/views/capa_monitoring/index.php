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
								<option value="<?php echo $d['id']; ?>"><?php echo $d['section_name']; ?></option>
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
	modal_open('modal-form','','modal-lg');
		modal_body();
			form_open(base_url('internal/capa_monitoring/save'),'post','form');
				col_init(3,9);
				input('hidden','id','id');
				select2(lang('follow_up'),'follow_up','',['[date] System','System and Personal e-mail', 'System & meeting']);
				?>
					
				<div class="form-group row">
				<label class="col-form-label col-sm-3" for="keterangan_progress"><?php echo (lang('keterangan_progress')); ?></label>		
				<div class="col-sm-9">
					<textarea name="keterangan_progress" id="keterangan_progress" class="form-control editor" data-validation="required" rows="3" data-editor="inline"></textarea>
				</div>
			</div>



			<div class="form-group row">
			<label class="col-form-label col-sm-3"><?php echo lang('evidence') ?><small><?php echo lang('maksimal'); ?> 5MB</small></label>
			<div class="col-sm-9">
				<button type="button" class="btn btn-info" id="add-file" title="<?php echo lang('tambah_dokumen'); ?>"><?php echo lang('tambah_dokumen'); ?></button>
			</div>
			</div>
			<div id="additional-file" class="mb-2"></div>
			<?php

				checkbox_group(lang('status_capa'));
					checkbox(lang('delivered'),'akses_view',1, 'disabled checked');
					checkbox(lang('on-progress'),'akses_input',1);
					checkbox(lang('done'),'akses_edit',1);
					checkbox(lang('pending'),'akses_delete',1);
					checkbox(lang('cancle'),'akses_additional',1);
					checkbox(lang('deadline_exceeded'),'deadline_exceeded',1);


					select2(lang('capa_score'),'capa_score','',['[date] System','System and Personal e-mail', 'System & meeting']);
					input('text',lang('achievement'). ' %','achievement');

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

$(document).ready(function(){
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

</script>