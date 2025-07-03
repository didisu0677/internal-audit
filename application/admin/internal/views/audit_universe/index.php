<style>
	table[data-fixed="true"] > thead {
    visibility: hidden;   /* atau display:none; kalau lebar sudah diset via JS */
}
</style>
<div class="content-header page-data">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">   		
    		<?php 

			$arr = [
				['btn-export','Export Data','fa-upload'],
			];
			echo access_button('',$arr); 

			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body mt-6">

	<div class="main-container mt-2">
		<div class="row">
			<form id="form-control" action="<?php echo base_url('internal/audit_universe/save_perubahan'); ?>" data-callback="reload" method="post" data-submit="ajax">

				<div class="card-body">
					<div class="table-responsive tab-pane fade active show height-window" id="result">
					<?php
					table_open('table table-bordered table-app table-hover table-1',true);
						thead();
							tr();
								th(lang('location'),'','class="text-center align-middle"');
								th(lang('divisi'),'','class="text-center align-middle"');
								th(lang('department'),'','class="text-center align-middle"');
								th(lang('section'),'','class="text-center align-middle"');
								th(lang('aktivitas'),'','class="text-center align-middle"');
								th(lang('sub_aktivitas'),'','class="text-center align-middle"');
								th(lang('risk'),'','class="text-center align-middle"');
								th(lang('keterangan'),'','class="text-center align-middle"');
								th(lang('bobot'),'','class="text-center align-middle"');
								th(lang('internal_control'),'','class="text-center align-middle"');
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
	var page = base_url + 'internal/audit_universe/data';
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


function formOpen() {
	is_edit = true;
	var index1 = 0;
	var response = response_edit;
	$('#result tbody').html('');
	$('#result2 tbody').html('')
	$('#result3 tbody').html('')
	if(typeof response.id != 'undefined') {
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
			f.find('.bobot_risk').val(v.bobot);
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

$(document).on('click', '.btn-export', function() {
	var currentdate = new Date();
	var datetime = currentdate.getDate() + "/" +
		(currentdate.getMonth() + 1) + "/" +
		currentdate.getFullYear() + " @ " +
		currentdate.getHours() + ":" +
		currentdate.getMinutes() + ":" +
		currentdate.getSeconds();

	var table = '';
	table += '<table>'; // Add border style here

	// Add table rows
	table += '<tr><td colspan="1">PT Otsuka Indonesia</td></tr>';
	table += '<tr><td colspan="1">' +' Audit Universe </td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td>: ' + datetime + '</td></tr>';
	table += '</table><br><br>';

	// Add content body
	table += $(result).html();

	var target = table;
	// window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));

	htmlToExcel(target)
	
	// $('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
	// 	$(this).removeAttr('bgcolor');
	// });
});

</script>