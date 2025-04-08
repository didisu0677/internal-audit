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
				th(lang('aktivitas'),'','data-content="id_aktivitas"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
// modal_open('modal-form');
modal_open('modal-form','','modal-lg','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('risk_management/m_aktivitas/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2(lang('audit_section'),'id_section[]','required',$option,'id','nama','','multiple');
			textarea(lang('aktivitas'),'aktivitas');
			input('text',lang('audit_area'),'audit_area');
			input('text',lang('type_aktivitas'),'type_aktivitas');
			?>
			<div class="table-responsive mb-2">
				<table class="table table-bordered table-detail table-app">
					<thead>
						<tr>
							<th><?php echo lang('sub_aktivitas'); ?></th>
							<th width="10">
								<button type="button" class="btn btn-sm btn-icon-only btn-success btn-add-aspek"><i class="fa-plus"></i></button>
							</th>
						</tr>
					</thead>
					<tbody id="d1">
						<?php 
						if(!empty($sub_aktivitas)) {
						foreach($sub_aktivitas as $t) {
							echo '<tr>';
							echo '<td><input type="text" class="form-control" autocomplete="off" name="deskripsi1[]" value="'.$t['sub_aktivitas'].'" data-validation="required" /></td>';
							echo '<td><button type="button" class="btn btn-sm btn-icon-only btn-danger btn-remove"><i class="fa-times"></i></button></td>';
							echo '</tr>';
						}} ?>
					</tbody>
				</table>
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
function add_sub_aktivitas() {
	var konten = '<tr>'
		+ '<td><input type="text" class="form-control" autocomplete="off" name="deskripsi1[]" data-validation="" /></td>'
		+ '<td><button type="button" class="btn btn-sm btn-icon-only btn-danger btn-remove"><i class="fa-times"></i></button></td>'
	+ '</tr>';
	$('#d1').append(konten);
}
$('.btn-add-aspek').click(function(){
	add_sub_aktivitas();
});
$(document).on('click','.btn-remove',function(){
	$(this).closest('tr').remove();
});
</script>
