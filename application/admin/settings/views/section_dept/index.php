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
	table_open('',true,base_url('settings/section_dept/data'),'tbl_section_department');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('kode'),'','data-content="kode"');
				th(lang('company'),'','data-content="perusahaan" data-table="tbl_m_company"'); 
				th(lang('location'),'','data-content="lokasi" data-table="tbl_location"');
				th(lang('divisi'),'','data-content="divisi" data-table="tbl_m_divisi"');
				th(lang('department'),'','data-content="department" data-table="tbl_m_department"');
				th(lang('section'),'','data-content="section"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form','','modal-lg','data-openCallback="formOpen"');
	modal_body();
		?>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="company-tab" data-toggle="tab" href="#company" role="tab" aria-controls="company" aria-selected="true"><?php echo lang('company'); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="location-tab" data-toggle="tab" href="#location" role="tab" aria-controls="location" aria-selected="true"><?php echo lang('location'); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="divisi-tab" data-toggle="tab" href="#divisi" role="tab" aria-controls="divisi" aria-selected="true"><?php echo lang('divisi'); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="dept-tab" data-toggle="tab" href="#dept" role="tab" aria-controls="dept" aria-selected="true"><?php echo lang('department'); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="section-tab" data-toggle="tab" href="#section" role="tab" aria-controls="section" aria-selected="true"><?php echo lang('section'); ?></a>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				
				<div class="tab-pane fade show active" id="company" role="tabpanel" aria-labelledby="company-tab">
					<br>
					
						<?php
						form_open(base_url('settings/section_dept/save'),'post','form');
						?>
						<?php
							col_init(3,9);
							input('hidden','id','id');
							input('text',lang('kode'),'kode_perusahaan');
							input('text',lang('company'),'perusahaan');
							toggle(lang('aktif').'?','is_active');
							form_button(lang('simpan'),lang('batal'));
						form_close();
						?>
				</div>
	
				<div class="tab-pane fade" id="location" role="tabpanel" aria-labelledby="location-tab">
				<br>
					<?php
					form_open(base_url('settings/section_dept/save_location'),'post','form-1');
						col_init(3,9);
						input('hidden','id','id');
						select2(lang('company'),'id_company','required',$company,'id','perusahaan');
						input('text',lang('kode'),'kode_lokasi');
						input('text',lang('lokasi'),'lokasi');
						toggle(lang('aktif').'?','is_active');
						form_button(lang('simpan'),lang('batal'));
					form_close();
					?>
				</div>

				<div class="tab-pane fade" id="divisi" role="tabpanel" aria-labelledby="divisi-tab">
				<br>
					<?php
					form_open(base_url('settings/section_dept/save_divisi'),'post','form-2');
						col_init(3,9);
						input('hidden','id','id');
						select2(lang('company'),'id_company','required',$company,'id','perusahaan');
						select2(lang('location'),'divid_lokasi','required',$location,'id','lokasi');
						input('text',lang('kode'),'kode_divisi');
						input('text',lang('divisi'),'divisi');
						toggle(lang('aktif').'?','is_active');
						form_button(lang('simpan'),lang('batal'));
					form_close();
					?>
				</div>

				<div class="tab-pane fade" id="dept" role="tabpanel" aria-labelledby="dept-tab">
				<br>
					<?php
					form_open(base_url('settings/section_dept/save_department'),'post','form-3');
						col_init(3,9);
						input('hidden','id','id');
						select2(lang('company'),'id_company','required',$company,'id','perusahaan');
						select2(lang('location'),'deptid_lokasi','required',$location,'id','lokasi');
						select2(lang('location'),'deptid_divisi','required',$divisi,'id','divisi');
						input('text',lang('kode'),'kode_department');
						input('text',lang('department'),'department');
						toggle(lang('aktif').'?','is_active');
						form_button(lang('simpan'),lang('batal'));
					form_close();
					?>
				</div>

				<div class="tab-pane fade" id="section" role="tabpanel" aria-labelledby="section-tab">
				<br>
					<?php
					form_open(base_url('settings/section_dept/save_section'),'post','form-4');
						col_init(3,9);
						input('hidden','id','id');
						select2(lang('company'),'id_company','required',$company,'id','perusahaan');
						select2(lang('location'),'secid_lokasi','required',$location,'id','lokasi');
						select2(lang('divisi'),'secid_divisi','required',$divisi,'id','divisi');
						select2(lang('department'),'id_department','required',$department,'id','department');
						input('text',lang('kode'),'kode_section');
						input('text',lang('section'),'section_dept');
						toggle(lang('aktif').'?','is_active');
						form_button(lang('simpan'),lang('batal'));
					form_close();
					?>
				</div>
			

			</div>
					<?php
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/department/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>
function formOpen() {	
	var response = response_edit;
	
	if(typeof response.id != 'undefined') {
    	$('#kode_perusahaan').val(response.kode_perusahaan)
		$('#divid_lokasi').val(response.id_location).trigger('change')
		$('#deptid_lokasi').val(response.id_location).trigger('change')
		$('#secid_lokasi').val(response.id_location).trigger('change')

		$('#deptid_divisi').val(response.id_divisi).trigger('change')
		$('#secid_divisi').val(response.id_divisi).trigger('change')

		$('#kode_lokasi').val(response.kode_lokasi)
		$('#lokasi').val(response.lokasi)
		$('#kode_divisi').val(response.kode_divisi)
		$('#divisi').val(response.divisi)

		$('#kode_section').val(response.kode)
		$('#section_dept').val(response.section)

	}
}

</script>