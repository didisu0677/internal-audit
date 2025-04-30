<div class="content-header page-data">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">   		
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
			<form id="form-control" action="<?php echo base_url('risk_management/rcm/save_perubahan'); ?>" data-callback="reload" method="post" data-submit="ajax">

				<div class="card-body">
					<div class="table-responsive tab-pane fade active show height-window">
					<?php
					table_open('table table-bordered table-app table-hover table-1');
						thead();
							tr();
								th(lang('location'),'','class="text-center align-middle headcol"');
								th(lang('divisi'),'','class="text-center align-middle headcol"');
								th(lang('department'),'','class="text-center align-middle headcol"');
								th(lang('section'),'','class="text-center align-middle headcol"');
								th(lang('aktivitas'),'','class="text-center align-middle headcol"');
								th(lang('sub_aktivitas'),'','class="text-center align-middle headcol"');
								th(lang('risk'),'','class="text-center align-middle headcol"');
								th(lang('internal_control'),'','class="text-center align-middle headcol"');
								th(lang('keterangan'),'','class="text-center align-middle headcol"');
								// th(lang('bobot'),'','class="text-center align-middle headcol"');
								th('&nbsp;','','width="30"');
						tbody();
					table_close();
					?>
					</div>
				</div>
				
			</form>
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
<script type="text/javascript">

var index1 = 0;
$(document).ready(function () {
	getData();
	var index1 = 0;
	$('#form')[0].reset();
});	


function getData() {
	// cLoader.open(lang.memuat_data + '...');
	$('.overlay-wrap').removeClass('hidden');
	var page = base_url + 'risk_management/rcm/data';
		page 	+= '/'+$('#filter_tahun').val();


	$.ajax({
		url 	: page,
		data 	: {},
		type	: 'get',
		dataType: 'json',
		success	: function(response) {
			$('.table-1 tbody').html(response.table);
			$('#parent_id').html(response.option);
			$('#id_rk').val(response.id_rk);
			cLoader.close();
			cek_autocode();
			fixedTable();
			var item_act	= {};
			if($('.table-1 tbody .btn-input').length > 0) {
				item_act["sep2"] 		= "---------";
				item_act['edit'] 		= {name : lang.ubah, icon : "edit"};					
			}
			if($('.table-app tbody .btn-input').length > 0) {
				item_act['delete'] 		= {name : lang.hapus, icon : "delete"};					
			}

			var act_count = 0;
			for (var c in item_act) {
				act_count = act_count + 1;
			}
			if(act_count > 0) {
				$.contextMenu({
					selector: '.table-1 tbody tr', 
					callback: function(key, options) {
						if($(this).find('[data-key="'+key+'"]').length > 0) {
							if(typeof $(this).find('[data-key="'+key+'"]').attr('href') != 'undefined') {
								window.location = $(this).find('[data-key="'+key+'"]').attr('href');
							} else {
								$(this).find('[data-key="'+key+'"]').trigger('click');
							}
						} 
					},
					items: item_act
				});
			}
			$('.overlay-wrap').addClass('hidden')
			$('.select2').select2()
			$('.select2-search__field').attr('style', 'width:100%')
		}
	});
}

$(function(){
	getData();
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


$(document).on('click','.btn-removerisk',function(){
	$(this).closest('tr').remove();
	calculate_dampak();
	calculate_kemungkinan();
});


$(document).on('keyup','.score_dampak',function(){
	calculate_dampak();
});

$(document).on('keyup','.score_kemungkinan',function(){
	calculate_kemungkinan();
});

function calculate_dampak() {
	var total = 0;
	var jml = 0;
	var idx = 0 ;
	$('#result2 tbody tr').each(function(){
		if($(this).find('.score_dampak').length == 1) {
			var subtotal = moneyToNumber($(this).find('.score_dampak').val());
			var score_kemungkinan = moneyToNumber($(this).find('.score_kemungkinan').val());
			total += subtotal;
			jml++;

			if(score_kemungkinan > 0) {
				$('#total_score'+idx).val(customFormat(subtotal * score_kemungkinan));
			}
		}
		idx++;
	});

	$('#rata2_dampak').val(customFormat(total / jml));
	$('#rata2_total').val(customFormat($('#rata2_dampak').val() * $('#rata2_kemungkinan').val()));
}

function calculate_kemungkinan() {
	var total2 = 0;
	var jml2 = 0;
	var idx1 = 0;
	$('#result2 tbody tr').each(function(){
		if($(this).find('.score_kemungkinan').length == 1) {
			var subtotal2= moneyToNumber($(this).find('.score_kemungkinan').val());
			var score_dampak = moneyToNumber($(this).find('.score_dampak').val());
			total2 += subtotal2;
			jml2++;

			$('#total_score'+idx1).val(customFormat(subtotal2 * score_dampak));
			total_score = moneyToNumber($(this).find('.total_score').val());
		}
		idx1++;
	});

	$('#rata2_kemungkinan').val(customFormat(total2 / jml2));
	$('#rata2_total').val(customFormat($('#rata2_dampak').val() * $('#rata2_kemungkinan').val()));
}

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
</script>