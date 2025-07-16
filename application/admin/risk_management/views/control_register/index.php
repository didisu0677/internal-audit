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
	table_open('',true,base_url('risk_management/control_register/data'),'tbl_internal_control');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('aktivitas'),'','data-content="aktivitas" data-table="tbl_aktivitas"');
				th(lang('audit_area'),'','data-content="sub_aktivitas" data-table="tbl_sub_aktivitas"');
				th(lang('internal_control'),'','data-content="internal_control" data-table="tbl_m_internal_control"');
				th(lang('location_control'),'','data-content="location_control"');
				th(lang('no_pnp'),'','data-content="no_pnp"');
				th(lang('jenis_pnp'),'','data-content="jenis_pnp"');
				th(lang('penerbit_pnp'),'','data-content="penerbit_pnp"');
				th(lang('tanggal_pnp'),'','data-content="tanggal_pnp"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','modal-xl','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('risk_management/control_register/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('hidden','id_sub_aktivitas','id_sub_aktivitas');
			?>
			<div class="form-group row">
				<label class="col-form-label col-sm-3 required" for="id_aktivitas"><?php echo lang('aktivitas'); ?></label>
				<div class="col-sm-9">
					<div class="input-group">
						<input type="hidden" name="id_aktivitas" id="id_aktivitas">
						<input type="text" id="aktivitas_id_data" name="aktivitas_id_data" class="form-control" autocomplete="off" data-validation="required">
						<div class="input-group-append">
							<button type="button" class="btn btn-success btn-icon-only" id="browse-req"><i class="fa-list"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-sm-3 required" for="id_sub_aktivitas"><?php echo lang('sub_aktivitas'); ?></label>
				<div class="col-sm-9">
					<div class="input-group">
						<input type="hidden" name="id_audit_area" id="id_audit_area">
						<input type="text" id="audit_area" name="audit_area" class="form-control" autocomplete="off" data-validation="required">
						<div class="input-group-append">
							<button type="button" class="btn btn-success btn-icon-only" id="browse-sub"><i class="fa-list"></i></button>
						</div>
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
									<th></th>
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
			<br>
			<?php

			

			// toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('risk_management/control_register/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>
	var index1 = 0;
	$(document).ready(function() {
		var index1 = 0;
    	$('#form')[0].reset();
	});

	function formOpen() {
		$('#aktivitas_id_data').val('');
		$('#id_aktivitas').val(0);
		$('#aktivitas_id_data').prop('readonly', false);
		is_edit = true;
		var index1 = 0;
		var response = response_edit;
		$('#result3 tbody').html('')
		if(typeof response.id != 'undefined') {
			$('#aktivitas_id_data').val(response.aktivitas_id_data);
			$('#id_aktivitas').val(response.id_aktivitas);
			$('#id_sub_aktivitas').val(response.id_sub_aktivitas);
			$('#audit_area').val(response.audit_area);
			$.each(response.ctrl_item,function(k,v){
				add_itemcontrol();
				var f = $('#result3 tbody tr').last();
				f.find('.id_control').val(v.id);
				f.find('.id_m_control').val(v.id_internal_control);
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

	$(document).on('click','.btn-add-controlitem',function(){
		add_itemcontrol();
	});

	function add_itemcontrol() {
		var konten = '<tr>'
				+ '<td width="10"><button type="button" class="btn btn-sm btn-danger btn-icon-only btn-removecontrol"><i class="fa-times"></i></button></td>'
				+ '<td><input type="hidden" class="form-control id_control" autocomplete="off" name="id_control[]" data-validation="" />'
				+ '<input type="hidden" class="form-control id_m_control" autocomplete="off" name="id_m_control[]" name="id_m_control'+index1+'" id="id_m_control'+index1+'" data-validation="" />'
				+ '<input type="hidden" class="form-control idx'+index1+'" autocomplete="off" name="idx'+index1+'" id ="idx'+index1+'" value="'+index1+'" data-validation="" />'
				+ '<input type="text" autocomplete="off" class="form-control ctrl_existing" name="ctrl_existing[]" id = "ctrl_existing'+index1+'" value ="" aria-label="" data-validation=""/></td>';
					konten += '<td class="text-center"><button type="button" class="btn btn-success btn-icon-only browse-ctrl" id="browse-ctrl" data-id=""><i class="fa-list"></i></button></a></td>';
					konten += '<td width="150"><input type="text" autocomplete="off" class="form-control ctrl_location" name="ctrl_location[]" id = "ctrl_location'+index1+'" value ="" aria-label="" data-validation=""/></td>';
					konten += '<td width="150"><input type="text" autocomplete="off" class="form-control no_pnp" name="no_pnp[]" id = "no_pnp'+index1+'" value ="" aria-label="" data-validation=""/></td>';
					konten += '<td width="100"><input type="text" autocomplete="off" class="form-control jenis_pnp" name="jenis_pnp[]" id = "jenis_pnp'+index1+'" value ="" aria-label="" data-validation=""/></td>';
					konten += '<td><input type="text" autocomplete="off" class="form-control penerbit" name="penerbit[]" id = "penerbit'+index1+'" value ="" aria-label="" data-validation=""/></td>';	
					konten += '<td><input type="date" autocomplete="off" class="form-control tgl_pnp" name="tgl_pnp[]" id = "tgl_pnp'+index1+'" value ="" aria-label="" data-validation=""/></td>'			
			+ '</tr>';
		$('#result3 tbody').append(konten);
		index1++;
	}

	$(document).on('click','.btn-removecontrol',function(){
		
	    let row = $(this).closest('tr');
		let id_m_control = row.find('.id_m_control').val();
		let id_aktivitas = $('#id_aktivitas').val();
		let id_sub_aktivitas = $('#id_sub_aktivitas').val();

		$.ajax({
			url: base_url + 'risk_management/control_register/delete_internal_control',
			type: 'post',
			data: {
				id_aktivitas:id_aktivitas, id_sub_aktivitas:id_sub_aktivitas, id_m_control:id_m_control
			},
			success: function(res){
				row.remove();
				cAlert.open(res.message, res.status);
			}
		})

	});

	var wi, timer;
	
	let popup = null;
	$('#id_aktivitas').click(function(){
		alert('x')
		$('#browse-req').trigger('click');
	});
	$('#browse-req').click(function(){
		if(!wi) {
			wi = popupWindow(base_url + 'risk_management/control_register/browse_req/', '_blank', window, ($(window).width() * 0.8), ($(window).height() * 0.8));
			timer = setInterval(checkChild, 500);
		} else wi.focus();
	});

	$('#browse-sub').click(function(){
		if(popup == null || popup.closed) {
			popup = popupWindow(base_url + 'risk_management/control_register/browse_sub/', '_blank', window, ($(window).width() * 0.8), ($(window).height() * 0.8));
		} else{
			popup.focus();
		}
	});

	$(document).on('click', '.browse-ctrl', function(){
		if(!wi) {
			var myCtrlId = $('#idx'+(index1-1)).val();
			wi = popupWindow(base_url + 'risk_management/control_register/browse_ctrl/?ctrl_id=' + encodeURIComponent(myCtrlId), '_blank', window, ($(window).width() * 0.8), ($(window).height() * 0.8));
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


//
</script>