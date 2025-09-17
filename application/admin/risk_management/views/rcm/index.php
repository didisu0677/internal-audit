<style>
	table[data-fixed="true"] > thead {
    visibility: hidden;   /* atau display:none; kalau lebar sudah diset via JS */
}
.dataTables_wrapper .dataTables_filter {
		float: right;
		text-align: right;
	}
	.dataTables_wrapper .dataTables_paginate {
		float: right;
		text-align: right;
	}
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<div class="content-header page-data">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		
		<div class="float-right"> 
			<!-- <label class=""><?php echo lang('department'); ?> &nbsp</label>					
			<select class = "select2 infinity custom-select" style="width: 300px;" id="department">
				<option value="ALL">ALL Department</option>
				<?php foreach($filter as $d){ ?>
				<option value="<?= $d['id_department']; ?>"><?= $d['location'].' | '.$d['department'] ?></option>
				<?php } ?>
			</select> -->
  		
    		<?php 
				echo access_button('delete,active,inactive,export,import'); 
			// echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';

			// $arr = [];
			// $arr = [
			// 	['btn-save','Save Data','fa-save'],
			// 	// ['btn-export','Export Data','fa-upload'],
			// 	// ['btn-import','Import Data','fa-download' ],
			// 	// ['btn-template','Template Import','fa-reg-file-alt']
			// ];
		
		
			// echo access_button('',$arr); 

			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body mt-6">
	<div class="main-container mt-2">
		<div class="row">
			<div class="card-body">
				<div class="table-responsive">
					<table id="example" class="table table-bordered table-hover table-sm">
						<thead class="text-center">
							<tr>
								<th><?='Lokasi' ?></th>
								<th><?php echo lang('divisi'); ?></th>
								<th><?php echo lang('department'); ?></th>
								<th><?php echo lang('section'); ?></th>
								<th><?php echo lang('aktivitas'); ?></th>
								<th><?= 'Audit Area'?></th>
								<th><?= 'Risk' ?></th>
								<th><?php echo lang('internal_control'); ?></th>
								<th><?= 'Keterangan'?></th>
								<th style="width:5%"><?php echo lang('aksi'); ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
	    </div>
	</div>

	
	<div class="overlay-wrap hidden">
		<div class="overlay-shadow"></div>
		<div class="overlay-content">
			<div class="spinner"></div>
			<p class="text-center">Please wait ... </p>
		</div>
	</div>
	
</div>
<?php

modal_open('modal-form','','modal-xl','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('risk_management/rcm/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('hidden','id_rk','id_rk');
			select2(lang('audit_section'),'id_section[]','required',$option,'id','nama','','multiple');
			select2(lang('aktivitas'),'id_aktivitas[]','required',$opt_aktivitas,'id','aktivitas','','multiple');
			?>

			</br>
			<div class="card">
				<div class="card-header">Risk Register</div>
				<div class="card-body">
					<div id="result2" class="table-responsive mb-2">
						<table class="table table-bordered table-app table-dokter">
							<thead>
								<tr>
									<th width="10" ><div class="btn-group" role="group""></button><button type="button" class="btn btn-sm btn-success btn-icon-only btn-add-riskitem" title="'+lang.tambah_item+'"><i class="fa-plus"></i></button></div></th>
									<th>Resiko Existing</th>
									<th></th>
									<th width="350">Keterangan</th>
									<th width="60">Score Dampak</th>
									<th width="60">Score kemungkinan</th>
									<th width="160">Bobot</th>
								<tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<br>
			<?php
			// toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();


?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">

function getData(){
    $('#example').DataTable({
        destroy: true, // kalau dipanggil ulang tidak error
        processing: true,
        ajax: {
            url: base_url + 'risk_management/rcm/data',
            type: 'POST',
            dataSrc: 'data' // sesuai key dari PHP render(['data'=>...])
        },
        columns: [
            { data: 'location', width: '1%' },
            { data: 'divisi' },
            { data: 'department' },
            { data: 'section' },
            { data: 'aktivitas' },
            { data: 'sub_aktivitas' },
			{ data: 'risk' },
			{ data: 'internal_control' },
			{ data: 'keterangan' },
            { data: 'aksi', width: '5%'}
        ]
    });
}

var index1 = 0;
$(document).ready(function () {
	getData();
	var index1 = 0;
	$('#form')[0].reset();
});	


// $(document).on('click','.btn-save',function(){
// 	$('#form-control').submit();
// });

var bobot = '';
function formOpen() {
	is_edit = true;
	var index1 = 0;
	get_bobot();
	var response = response_edit;
	$('#id_rk').val(response.id_risk_control);
	$('#result tbody').html('');
	$('#result2 tbody').html('')
	$('#result3 tbody').html('')
	if(typeof response.id != 'undefined') {
		bobot = response.bobot;
		$('total_score').val(response.total_score);
		$('score_dampak').val(response.score_dampak);
		$('score_kemungkinan').val(response.score_kemungkinan);
		$.each(response.detail,function(k,v){
			add_sub_aktivitas();
			var f = $('#result tbody tr').last();
			f.find('.sub_aktivitas').val(v.sub_aktivitas);
			f.find('.id_sub_aktivitas').val(v.id);
		});

		$.each(response.risk,function(k,v){
			add_itemrisk();
			var f = $('#result2 tbody tr').last();
			f.find('.id_risk').val(v.id);
			f.find('.risk').val(v.risk);
			f.find('.keterangan').val(v.keterangan);
			f.find('.dampak').val(v.dampak);
			f.find('.score_dampak').val(v.score_dampak);
			f.find('.kemungkinan').val(v.kemungkinan);
			f.find('.score_kemungkinan').val(v.score_kemungkinan);
			f.find('.total_score').val(v.total_score);
			// f.find('.bobot_risk').val(v.bobot);
			f.find('.bobot_risk').val(v.bobot).trigger('change');
		});

		$.each(response.ctrl_item,function(k,v){
			add_itemcontrol();
			var f = $('#result3 tbody tr').last();
			f.find('.id_control').val(v.id);
			f.find('.ctrl_existing').val(v.internal_control);
			f.find('.ctrl_location').val(v.location_control);
			f.find('.no_pnp').val(v.no_pnp);
			f.find('.jenis_pnp').val(v.jenis_pnp);
			f.find('.penerbit').val(v.penerbit_pnp);
			f.find('.tgl_pnp').val(v.tanggal_pnp);
		});
	} 
	is_edit = false;
}

$(document).on('click','.btn-add-riskitem',function(){
	add_itemrisk();
});

$(document).on('click','.btn-removerisk',function(){
	$(this).closest('tr').remove();
});

function add_itemrisk() {
	var konten = '<tr>'
			+ '<td width="10"><button type="button" class="btn btn-sm btn-danger btn-icon-only btn-removerisk"><i class="fa-times"></i></button></td>'
			+ '<td><input type="hidden" class="form-control id_risk" autocomplete="off" name="id_risk[]" id="id_risk'+index1+'"/><input type="text" autocomplete="off" class="form-control risk" name="risk[]" id = "risk'+index1+'" value ="" aria-label="" data-validation=""/>'
			+ '<input type="hidden" class="form-control idx'+index1+'" autocomplete="off" name="idx'+index1+'" id ="idx'+index1+'" value="'+index1+'" data-validation="" /></td>';
				konten += '<td><button type="button" class="btn btn-success btn-icon-only browse-risk" id="browse-risk" data-id=""><i class="fa-list"></i></button></a></td>';
				konten += '<td width="150"><input type="text" autocomplete="off" class="form-control keterangan" name="keterangan[]" id = "keterangan'+index1+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td><input type="text" autocomplete="off" class="form-control score_dampak" name="score_dampak[]" id = "score_dampak'+index1+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td><input type="text" autocomplete="off" class="form-control score_kemungkinan" name="score_kemungkinan[]" id = "score_kemungkinan'+index1+'" value ="" aria-label="" data-validation=""/></td>';	
				// konten += '<td><input type="text" autocomplete="off" class="form-control bobot_risk" name="bobot_risk[]" id = "bobot_risk'+index1+'" value ="" aria-label="" data-validation=""/></td>'			
				konten += '<td><select class="form-control bobot_risk" name="bobot_risk[]" id = "bobot_risk'+index1+'" value ="" aria-label="" data-validation="required">'+bobot+'</select></td>';
		+ '</tr>';
	$('#result2 tbody').append(konten);
	index1++;
}


$(document).on('keyup','.score_dampak',function(){
	const dampak = $('#score_dampak'+(index1-1)).val();
	const kemungkinan = $('#score_kemungkinan'+(index1-1)).val();
	const score = getScore(dampak, kemungkinan);
	console.log(score);
	$('#bobot_risk'+(index1-1)).val(score).trigger('change');
});

$(document).on('keyup','.score_kemungkinan',function(){
	const dampak = $('#score_dampak'+(index1-1)).val();
	const kemungkinan = $('#score_kemungkinan'+(index1-1)).val();
	const score = getScore(dampak, kemungkinan);
	console.log(score);
	$('#bobot_risk'+(index1-1)).val(score).trigger('change');
});



var wi, timer;

$(document).on('click', '.browse-risk', function(){
	if(!wi) {
		var myCtrlId = $('#idx'+(index1-1)).val();
		wi = popupWindow(base_url + 'risk_management/rcm/browse_risk/?ctrl_id=' + encodeURIComponent(myCtrlId), '_blank', window, ($(window).width() * 0.8), ($(window).height() * 0.8));
		timer = setInterval(checkChild, 500);
	} else wi.focus();
})


function popupWindow(url, title, win, w, h) {
	const y = win.top.outerHeight / 2 + win.top.screenY - ( h / 2);
	const x = win.top.outerWidth / 2 + win.top.screenX - ( w / 2);
	return win.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+y+', left='+x);
}
function checkChild() {
	if (wi.closed) {
		wi = null;
		clearInterval(timer);
	}
}
$(window).bind('beforeunload', function(){
	if (wi) {
		wi.close();
	}
});
$('#modal-form').on('hidden.bs.modal', function () {
	if (wi) {
		wi.close();
	}
});

function get_bobot() {
	if(proccess) {
		readonly_ajax = false;
		$.ajax({
			url : base_url + 'risk_management/rcm/get_bobot',
			data : {},
			type : 'POST',
			success	: function(response) {
				bobot = response;
			}
		});
	}
}

const scoreMatrix = {
  "6,6": "4",
  "6,5": "4",
  "6,4": "3",
  "6,3": "3",
  "6,2": "2",
  "6,1": "2",
  "5,6": "4",
  "5,5": "3",
  "5,4": "3",
  "5,3": "2",
  "5,2": "2",
  "5,1": "1",
  "4,6": "3",
  "4,5": "3",
  "4,4": "2",
  "4,3": "2",
  "4,2": "2",
  "4,1": "1",
  "3,6": "3",
  "3,5": "2",
  "3,4": "2",
  "3,3": "2",
  "3,2": "1",
  "3,1": "1",
  "2,6": "2",
  "2,5": "2",
  "2,4": "2",
  "2,3": "1",
  "2,2": "1",
  "2,1": "1",
  "1,6": "1",
  "1,5": "1",
  "1,4": "1",
  "1,3": "1",
  "1,2": "1",
  "1,1": "1",
};

function getScore(dampak, kemungkinan) {
  const key = `${dampak},${kemungkinan}`;
  return scoreMatrix[key] || "Unknown"; // fallback kalau kombinasi tidak terdaftar
}
</script>