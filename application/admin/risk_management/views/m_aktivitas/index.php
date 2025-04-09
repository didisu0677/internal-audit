<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('risk_management/m_aktivitas/data'),'tbl_m_aktivitas');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				// th(lang('parent_id'),'','data-content="parent_id"');
				th(lang('id_company'),'','data-content="company"');
				th(lang('id_location'),'','data-content="location"');
				th(lang('id_divisi'),'','data-content="divisi"');
				th(lang('id_department'),'','data-content="department"');
				th(lang('id_section'),'','data-content="section"');
				th(lang('aktivitas'),'','data-content="aktivitas" data-table="tbl_aktivitas"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
// modal_open('modal-form');
modal_open('modal-form','','modal-xl','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('risk_management/m_aktivitas/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2(lang('audit_section'),'id_section[]','required',$option,'id','nama','','multiple');
			textarea(lang('aktivitas'),'aktivitas');
			input('text',lang('audit_area'),'audit_area');
			input('text',lang('tipe_aktivitas'),'type_aktivitas');
			?>
			<div class="card">
				<div class="card-body">
					<div id="result" class="table-responsive mb-2">
						<table class="table table-bordered table-detail table-app">
							<thead>
								<tr>
									<th><?php echo lang('sub_aktivitas'); ?></th>
									<th width="10">
										<button type="button" class="btn btn-sm btn-icon-only btn-success btn-add-aspek"><i class="fa-plus"></i></button>
									</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			</br>
			<div class="card">
				<div class="card-header">Risk Register</div>
				<div class="card-body">
					<div id="result2" class="table-responsive mb-2">
						<table class="table table-bordered table-app table-dokter">
							<thead>
								<tr>
									<th width="10"><div class="btn-group" role="group""></button><button type="button" class="btn btn-sm btn-success btn-icon-only btn-add-riskitem" title="'+lang.tambah_item+'"><i class="fa-plus"></i></button></div></th>
									<th width="300">Resiko Existing</th>
									<th>Dampak</th>
									<th width="60">Score Dampak</th>
									<th>Kemungkinan</th>
									<th width="60">Score Kemungkinan</th>
									<th width="60">Bobot</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header">Control Register</div>
				<div class="card-body">
					<div id="result3" class="table-responsive mb-2">
						<table class="table table-bordered table-app table-dokter">
							<thead>
								<tr>
									<th width="10"><div class="btn-group" role="group""></button><button type="button" class="btn btn-sm btn-success btn-icon-only btn-add-controlitem" title="'+lang.tambah_item+'"><i class="fa-plus"></i></button></div></th>
									<th width="300">Control Existing</th>
									<th>Location Control</th>
									<th>No PnP</th>
									<th>Jenis PnP</th>
									<th>Penerbit PnP</th>
									<th>Tgl Berlaku PnP</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('risk_management/m_aktivitas/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>

var idx = 999;
var index0 = 0;
var index1 = 0;
function add_sub_aktivitas() {
	var konten = '<tr>'
		+ '<td><input type="hidden" class="form-control id_sub_aktivitas" autocomplete="off" name="id_sub_aktivitas[]" data-validation="" /><input type="text" class="form-control sub_aktivitas" autocomplete="off" name="sub_aktivitas[]" data-validation="" /></td>'
		+ '<td><button type="button" class="btn btn-sm btn-icon-only btn-danger btn-remove"><i class="fa-times"></i></button></td>'
	+ '</tr>';
	$('#result tbody').append(konten);
}
$('.btn-add-aspek').click(function(){
	add_sub_aktivitas();
});
$(document).on('click','.btn-remove',function(){
	$(this).closest('tr').remove();
});


function formOpen() {
	is_edit = true;
	index0 = 0;
	index1 = 0;
	var response = response_edit;
	$('#result tbody').html('');
	$('#result2 tbody').html('')
	$('#result3 tbody').html('')
	if(typeof response.id != 'undefined') {
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
			f.find('.dampak').val(v.dampak);
			f.find('.score_dampak').val(v.score_dampak);
			f.find('.kemungkinan').val(v.kemungkinan);
			f.find('.score_kemungkinan').val(v.score_kemungkinan);
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

$(document).on('click','.btn-add-riskitem',function(){
	add_itemrisk();
});

$(document).on('click','.btn-removerisk',function(){
	$(this).closest('tr').remove();
});

function add_itemrisk() {
	var konten = '<tr>'
			+ '<td width="10"><button type="button" class="btn btn-sm btn-danger btn-icon-only btn-removerisk"><i class="fa-times"></i></button></td>'
			+ '<td><input type="hidden" class="form-control id_risk" autocomplete="off" name="id_risk[]"/><input type="text" autocomplete="off" class="form-control risk" name="risk[]" id = "risk'+index0+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td width="150"><input type="text" autocomplete="off" class="form-control dampak" name="dampak[]" id = "dampak'+index0+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td><input type="text" autocomplete="off" class="form-control score_dampak" name="score_dampak[]" id = "score_dampak'+index0+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td width="250"><input type="text" autocomplete="off" class="form-control kemungkinan" name="kemungkinan[]" id = "kemungkinan'+index0+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td><input type="text" autocomplete="off" class="form-control score_kemungkinan" name="score_kemungkinan[]" id = "score_kemungkinan'+index0+'" value ="" aria-label="" data-validation=""/></td>';	
				konten += '<td><input type="text" autocomplete="off" class="form-control bobot_risk" name="bobot_risk[]" id = "bobot_risk'+index0+'" value ="" aria-label="" data-validation=""/></td>'			
		+ '</tr>';
	$('#result2 tbody').append(konten);
	index0++;
}

$(document).on('click','.btn-add-controlitem',function(){
	add_itemcontrol();
});

$(document).on('click','.btn-removerisk',function(){
	$(this).closest('tr').remove();
});

function add_itemcontrol() {
	var konten = '<tr>'
			+ '<td width="10"><button type="button" class="btn btn-sm btn-danger btn-icon-only btn-removecontrol"><i class="fa-times"></i></button></td>'
			+ '<td><input type="hidden" class="form-control id_control" autocomplete="off" name="id_control[]" data-validation="" /><input type="text" autocomplete="off" class="form-control ctrl_existing" name="ctrl_existing[]" id = "ctrl_existing'+index1+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td width="150"><input type="text" autocomplete="off" class="form-control ctrl_location" name="ctrl_location[]" id = "ctrl_location'+index1+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td><input type="text" autocomplete="off" class="form-control no_pnp" name="no_pnp[]" id = "no_pnp'+index1+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td width="250"><input type="text" autocomplete="off" class="form-control jenis_pnp" name="jenis_pnp[]" id = "jenis_pnp'+index1+'" value ="" aria-label="" data-validation=""/></td>';
				konten += '<td><input type="text" autocomplete="off" class="form-control penerbit" name="penerbit[]" id = "penerbit'+index1+'" value ="" aria-label="" data-validation=""/></td>';	
				konten += '<td><input type="text" autocomplete="off" class="form-control tgl_pnp" name="tgl_pnp[]" id = "tgl_pnp'+index1+'" value ="" aria-label="" data-validation=""/></td>'			
		+ '</tr>';
	$('#result3 tbody').append(konten);
	index1++;
}

$(document).on('click','.btn-removecontrol',function(){
	$(this).closest('tr').remove();
});

</script>
