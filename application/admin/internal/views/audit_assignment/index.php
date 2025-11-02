<div class="content-body mt-6">
	<div class="main-container mt-2">
		<div class="row justify-content-center">
			<div class="col-12 col-xl-11">
				<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
					<div class="card-body py-3">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="mb-0 text-dark font-weight-bold">
								Individual Audit Assignment
							</h3>
							<div class="btn-group" role="group" aria-label="Filter Assignment">
								<?php 
									$activeFilter = isset($filter)?$filter:'active';
									$baseUrl = base_url('internal/audit_assignment');
								?>
								<a href="<?="{$baseUrl}?filter=active"?>" class="btn btn-outline-primary filter-switch <?=($activeFilter=='active'?'active':'')?>" data-filter="active">
									<i class="fas fa-calendar-check mr-1"></i> Active
								</a>
								<a href="<?="{$baseUrl}?filter=history"?>" class="btn btn-outline-primary filter-switch <?=($activeFilter=='history'?'active':'')?>" data-filter="history">
									<i class="fas fa-history mr-1"></i> History
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="card shadow-sm border-0">
					<div class="card-body p-4">
						<div id="result">
							<?php foreach($data as $year => $departments): ?>
								<div class="mb-4">
									<div class="card border-0 shadow-sm">
										<div class="card-header text-white border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);border-radius: 15px;">
											<div class="d-flex justify-content-between align-items-center p-3 rounded">

												<button class="btn btn-link text-white text-decoration-none p-0 font-weight-bold year-toggle text-left"
													type="button"
													data-toggle="collapse"
													data-target="#year-collapse<?=$year?>"
													aria-expanded="false"
													aria-controls="year-collapse<?=$year?>">
													<div class="d-flex align-items-center">
														<i class="fas fa-chevron-down mr-3"></i>
														<div>
															<h4 class="mb-1 font-weight-bold text-white">Audit Assignment <?=$year?></h4>
															<small class="text-white-50">Click to expand assignment details</small>
														</div>
													</div>
												</button>
											</div>
										</div>
											<div class="collapse" id="year-collapse<?=$year?>" data-year="<?=$year?>">
												<div class="card-body p-0">
													<?php foreach($departments as $dept => $deptData): ?>
														<div class="border-bottom">
															<div class="p-3 bg-light font-weight-bold">
																<button class="btn btn-link text-decoration-none text-dark p-0 font-weight-bold dept-toggle flex-grow-1 text-left" 
																			type="button" 
																			data-toggle="collapse" 
																			data-target="#dept-collapse<?=$year?>-<?=md5($dept)?>" 
																			aria-expanded="false" 
																			aria-controls="dept-collapse<?=$year?>-<?=md5($dept)?>">
																		<div class="d-flex justify-content-between align-items-center">
																			<div>
																				<i class="fas fa-chevron-down me-2 text-info"></i>
																				<span><?= $dept ?></span>
																			</div>
																			<!-- <span class="badge badge-info ml-2 assignment-count">Loading...</span> -->
																		</div>
																</button>
																<?php if($deptData['status'] == 'active'): ?> 
																	<button class="btn btn-success btn-sm float-right mark-completed" type="button" data-id-plangroup="<?=$deptData['id_audit_plan_group']?>">
																		<i class="fas fa-check me-1"></i>
																		Mark Complete
																	</button>
																<?php endif; ?>
															</div>
																<div class="collapse" id="dept-collapse<?=$year?>-<?=md5($dept)?>">
																	<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
																		<table class="table table-hover table-bordered mb-0">
																			<thead class="bg-light text-center sticky-top">
																				<tr>
																					<th class="border-0 text-muted small bg-light">Section</th>
																					<th class="border-0 text-muted small bg-light">Aktivitas</th>
																					<th class="border-0 text-muted small bg-light">Audit Area</th>
																					<th class="border-0 text-muted small bg-light">Kriteria</th>
																					<th class="border-0 text-muted small bg-light">Risk</th>
																					<th class="border-0 text-muted small bg-light">Internal Control</th>
																					<th class="border-0 text-muted small bg-light">Pengujian</th>
																					<th class="border-0 text-muted small bg-light">Hasil Review</th>
																					<th class="border-0 text-muted small bg-light">Finding</th>
																					<th class="border-0 text-muted small bg-light">Bobot Finding</th>
																					<th class="border-0 text-muted small bg-light">Unconformity</th>
																					<th class="border-0 text-muted small bg-light">Dampak</th>
																					<th class="border-0 text-muted small bg-light">Root Cause</th>
																					<th class="border-0 text-muted small bg-light">Recommendation</th>
																					<th class="border-0 text-muted small bg-light">Finding Control Status Finding</th>
																					<th class="border-0 text-muted small bg-light">CAPA</th>
																					<th class="border-0 text-muted small bg-light">Deadline CAPA</th>
																					<th class="border-0 text-muted small bg-light">PIC CAPA</th>
																					<th class="border-0 text-muted small bg-light">Attachment</th>
																					<!-- <th class="border-0 text-muted small bg-light">Action</th> -->
																				</tr>
																			</thead>
																			<tbody class="department-data" data-group-id="<?=$deptData['id_audit_plan_group']?>">
																				<tr>
																					<td colspan="20" class="text-center py-4">
																						<div class="spinner-border spinner-border-sm text-primary mr-2" role="status">
																							<span class="sr-only">Loading...</span>
																						</div>
																						Loading audit assignments...
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														<?php endforeach; ?>
													</div>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
modal_open('modal-form','', 'modal-lg' );
	modal_body();
		// form_open(base_url('internal/audit_assignment/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('hidden','field_name','field_name');
			echo '<div class="form-group row">';
				echo '<div class="col-md-12">';
					echo '<textarea id="field-editor" name="field_value" class="form-control"></textarea>';
				echo '</div>';
			echo '</div>';
			echo '<div class="form-group row">';
				echo '<div class="col-md-12 text-center">';
					echo '<button type="button" class="btn btn-primary mr-2" id="btn_simpan">'.lang('simpan').'</button>';
					echo '<button type="button" class="btn btn-secondary mr-2" id="btn_batal">'.lang('batal').'</button>';
				echo '</div>';
			echo '</div>';
			// form_button(lang('simpan'),lang('batal'));
		// form_close();
	// modal_footer();
modal_close();

modal_open('modal-attachment','Attachment', 'modal-lg');
	modal_body();
		form_open(base_url('internal/audit_assignment/attach_file'),'post','form-attachment');
			col_init(3,9);
			input('hidden','id','id');
			fileupload('File','file','required','data-accept="xls|xlsx"');
			form_button(lang('simpan'),lang('batal'));
		form_close();
modal_close();

modal_open('modal-datetime','EDIT DEADLINE CAPA', 'modal-md');
	modal_body();
			col_init(3,9);
			input('hidden','id','id');
			input('hidden','field_name','field_name');
			input('date','Deadline CAPA', 'deadline_capa','required');
			echo '<div class="form-group row">';
				echo '<div class="col-md-12 text-center">';
					echo '<button type="button" class="btn btn-primary mr-2" id="btn_simpan">'.lang('simpan').'</button>';
					echo '<button type="button" class="btn btn-secondary mr-2" id="btn_batal">'.lang('batal').'</button>';
				echo '</div>';
			echo '</div>';
modal_close();

modal_open('modal-pic','EDIT PIC CAPA', 'modal-md');
	modal_body();
			col_init(3,9);
			input('hidden','id','id');
			input('hidden','field_name','field_name');
			select2('PIC CAPA', 'pic_capa', 'required', get_active_auditee(), 'id', 'nama');
			echo '<div class="form-group row">';
				echo '<div class="col-md-12 text-center">';
					echo '<button type="button" class="btn btn-primary mr-2" id="btn_simpan">'.lang('simpan').'</button>';
					echo '<button type="button" class="btn btn-secondary mr-2" id="btn_batal">'.lang('batal').'</button>';
				echo '</div>';
			echo '</div>';
modal_close();
?>

<script type="text/javascript" src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js') ?>"></script>
<script>
	let planGroupId = null;

	// AJAX filter switch similar to Annual Audit Plan
	$(document).on('click', '.filter-switch', function(e){
		if(e.ctrlKey || e.metaKey || e.shiftKey) return; // allow open in new tab
		e.preventDefault();
		var url = $(this).attr('href');
		$('.filter-switch').removeClass('active');
		$(this).addClass('active');
		$('#result').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><div class="mt-2 small text-muted">Loading '+$(this).data('filter')+'...</div></div>');
		$.get(url, function(html){
			var temp = $('<div>').html(html);
			var newContent = temp.find('#result').html();
			if(newContent){
				$('#result').html(newContent);
				$(document).trigger('content:rebind');
			}else{
				location.href = url; // fallback
			}
		});
	});

	// Rebinding after AJAX replace
	$(document).on('content:rebind', function(){
		$('.year-toggle .fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
	});

	$(document).ready(function() {
		// Auto expand nearest year collapse on load (current year or closest)
		(function autoOpenNearestYear(){
			var nowYear = (new Date()).getFullYear();
			var nearestBtn = null;
			var nearestDiff = Infinity;
			$('.year-toggle').each(function(){
				var txt = $(this).text();
				var match = txt.match(/(20\d{2})/);
				if(match){
					var y = parseInt(match[1]);
					var diff = Math.abs(y - nowYear);
					if(diff < nearestDiff){
						nearestDiff = diff;
						nearestBtn = $(this);
					}
				}
			});
			if(nearestBtn){
				var target = nearestBtn.data('target');
				$(target).collapse('show');
				nearestBtn.find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
				nearestBtn.attr('aria-expanded','true');
			}
		})();

		// Allow only one year open at a time
		$(document).on('click', '.year-toggle', function(){
			let targetCollapse = $(this).data('target');
			$('.year-toggle').not(this).each(function(){
				let otherTarget = $(this).data('target');
				if(otherTarget !== targetCollapse){
					$(otherTarget).collapse('hide');
					$(this).attr('aria-expanded','false');
					$(this).find('.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
				}
			});
		});

		// Only one department open within same year
		$(document).on('click', '.dept-toggle', function () {
			if (isEditing) return false;

			const $button = $(this);
			const targetCollapse = $button.attr('aria-controls');
			const $collapse = $('#' + targetCollapse);
			const $tbody = $collapse.find('.department-data');
			const groupId = $tbody.data('group-id');

			// Pastikan element ditemukan
			if (!groupId) return;

			// Load data hanya kalau belum pernah dimuat atau kosong
			if (!$tbody.data('loaded')) {
				loadDepartmentData(groupId, $tbody);
			}
		});


		// Icon update on collapse show/hide
		$(document).on('shown.bs.collapse', '.collapse', function(){
			let toggle = $(`[data-target="#${this.id}"]`);
			toggle.find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
			toggle.attr('aria-expanded','true');
		});
		$(document).on('hidden.bs.collapse', '.collapse', function(){
			let toggle = $(`[data-target="#${this.id}"]`);
			toggle.find('.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			toggle.attr('aria-expanded','false');
		});
		CKEDITOR.replace("field-editor", {
			height: 250,
		    extraPlugins: 'font',
			toolbar: [
				{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'] },
		        { name: 'paragraph',   items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
				{ name: 'insert',      items: ['Table', 'HorizontalRule'] },
				{ name: 'links',       items: ['Link', 'Unlink'] },
				{ name: 'clipboard',   items: ['Undo', 'Redo'] },
				{ name: 'styles',      items: ['Format', 'FontSize'] }
			],
			format_tags: 'p;h4;h5;pre',
    		fontSize_sizes: '10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px'
		});

		let isEditing = false;
		let originalValue = '';
		let loadedDepartments = [];
		
		$(document).on('click', '.editable:not(.editable-status)', function() {
			if (isEditing) return;
			
			const $cell = $(this);
			const field = $cell.data('field');
			const rowId = $cell.closest('tr').data('id');
			originalValue = $cell.html().trim();
			
			// Set modal data
			$('#modal-form').find('[name="id"]').val(rowId);
			$('#modal-form').find('[name="field_name"]').val(field);
			$('#modal-form .modal-title').text('EDIT ' +  field.replace(/_/g, ' ').toUpperCase());
			
			// Set CKEditor content
			if (CKEDITOR.instances['field-editor']) {
				CKEDITOR.instances['field-editor'].setData(originalValue);
			}
			$('#modal-form').modal('show');
		});

		$(document).on('click', '.datetime, .pic', function() {
			if (isEditing) return;
			
			const $cell = $(this);
			const field = $cell.data('field');
			const rowId = $cell.closest('tr').data('id');

			let modalType = '#modal-datetime';
			if(field != 'deadline_capa'){
				modalType = '#modal-pic';
			}
			
			$(modalType).find('[name="id"]').val(rowId);
			$(modalType).find('[name="field_name"]').val(field);
			
			
			$(modalType).modal('show');
		});

		$(document).on('click', '#btn_batal', function() {
			let modal = $(this).closest('.modal');
			modal.modal('hide');
		});

		$(document).on('click', '#btn_simpan', async function(e) {
			e.preventDefault();

			const modal = $(this).closest('.modal');

			const formData = {
				id: modal.find('[name="id"]').val(),
				field: modal.find('[name="field_name"]').val(),
				value: modal.find('[name="deadline_capa"]').length
					? modal.find('[name="deadline_capa"]').val()
					: modal.find('[name="pic_capa"]').length
						? modal.find('[name="pic_capa"]').val()
						: CKEDITOR.instances['field-editor'].getData()
			};

			let response = await $.ajax({
				url: '<?= base_url('internal/audit_assignment/save') ?>',
				type: 'POST',
				dataType: 'json',
				data: formData
			});
			
			if (response.status === 'success') {
				const cellClass = modal.is('#modal-datetime') ? '.datetime' : 
								  modal.is('#modal-pic') ? '.pic' : '.editable';

				let temp_value = await $.ajax({
					url: '<?= base_url('internal/audit_assignment/change_value') ?>',
					type: 'POST',
					dataType: 'json',
					data: {field: formData.field, value: formData.value }
				});

			    const $cell = $(`tr[data-id="${formData.id}"] ${cellClass}[data-field="${temp_value.field}"]`);
				$cell.html(temp_value.value);
				modal.modal('hide');
			}
			cAlert.open(response.message, response.status);
		});

			
		// Prevent accordion collapse when editing
		$(document).on('click', '.editable, .editable-status', function(e) {
			if (isEditing) {
				e.stopPropagation();
			}
		});
		
		// Handle accordion expand/collapse and load data
		$('.dept-toggle').on('click', function() {
			// Only proceed if not editing
			if (isEditing) return false;
			
			const $button = $(this);
			const $icon = $button.find('i');
			const target = $button.attr('aria-controls');
			
			// Find the actual assignment ID from the data attribute
			const actualAssignId = $(this).closest('.card').find('.department-data').data('group-id');

			if (actualAssignId && !loadedDepartments.includes(actualAssignId)) {
				loadDepartmentData(actualAssignId);
				loadedDepartments.push(actualAssignId);
			}
		});
		
		// Handle accordion collapse events for icon update
		$(document).on('shown.bs.collapse', '.collapse', function() {
			const collapseId = $(this).attr('id');
			const $toggle = $(`[aria-controls="${collapseId}"]`);
			$toggle.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
		});
		
		$(document).on('hidden.bs.collapse', '.collapse', function() {
			const collapseId = $(this).attr('id');
			const $toggle = $(`[aria-controls="${collapseId}"]`);
			$toggle.find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
		});

		$(document).on('click', '.attachment', function() {
			const rowId = $(this).data('id');
			$('#modal-attachment').find('[name="id"]').val(rowId);
			$('#modal-attachment').modal('show');
		});
	});

		$(document).on('click', '.mark-completed', function() {
			planGroupId = $(this).data('id-plangroup');
			if (!planGroupId) return;
			cConfirm.open('Are you sure you want to mark this department as complete?', 'markCompleted');
		});

		function reload_page() {
			location.reload();
		}
		
		function markCompleted() {
			$.ajax({
				url: '<?= base_url('internal/audit_assignment/mark_completed') ?>',
				type: 'POST',
				dataType: 'json',
				data: { id_audit_plan_group: planGroupId },
				success: function(res) {
					if (res.status === 'success') {
						cAlert.open(res.message, 'success', 'reload_page');
					} else {
						cAlert.open(res.message, 'error');
					}
				},
				error: function() {
					cAlert.open('An error occurred while processing your request.', 'error');
				}
			});
		}
		
		function loadDepartmentData(groupId) {
			const tbody = $(`.department-data[data-group-id="${groupId}"]`);
			const badge = tbody.closest('.card').find('.assignment-count');
			
			// Show loading state
			badge.removeClass('badge-info').addClass('badge-secondary');
			badge.html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...');
			
			$.ajax({
				url: '<?= base_url('internal/audit_assignment/data') ?>',
				data: { id: groupId },
				type: 'POST',
				dataType: 'json',
				success: function(res){
					let html = '';
					
					if (res.length === 0) {
						html = `<tr>
							<td colspan="19" class="text-center py-4 text-muted">
								<i class="fas fa-inbox mr-2"></i>
								No audit assignments found for this section
							</td>
						</tr>`;
					} else {
						res.forEach(function(item, index) {
							html += `<tr data-id="${item.id}">
								<td class="align-middle text-center" style="width: 120px; min-width: 120px;" data-field="section_name">${item.section || ''}</td>
								<td class="align-middle text-center" style="width: 200px; min-width: 200px;" data-field="aktivitas">${item.aktivitas || ''}</td>
								<td class="align-middle text-center" style="width: 150px; min-width: 150px;" data-field="audit_area">${item.sub_aktivitas || ''}</td>
								<td class="align-middle text-center" style="width: 180px; min-width: 180px;" data-field="kriteria"></td>
								<td class="align-middle text-center" style="width: 200px; min-width: 200px;" data-field="risk">
									`
									item.risk.forEach(function(risk) {
										html += `<div class="mb-3 bg-light p-2 rounded">
											${risk.risk}
										</div>`;
									});
								html += `
								</td>

								<td class="align-middle text-center" style="width: 180px; min-width: 180px;" data-field="internal_control">
									`
									item.internal_control.forEach(function(ic) {
										html += `<div class="mb-3 bg-light p-2 rounded">
											${ic.internal_control}
										</div>`;
									});
								html += `</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="pengujian">${item.pengujian || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="hasil_review">${item.hasil_review || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="finding">${item.finding || ''}</td>
								<td class="align-middle text-center editable" style="width: 80px; min-width: 80px;" data-field="bobot_finding">${item.bobot_finding || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="unconformity">${item.unconformity || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="dampak">${item.dampak || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="root_cause">${item.root_cause || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="recomendation">${item.recomendation || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="status_finding">${item.status_finding || ''}</td>
								<td class="align-middle editable" style="width: 600px; min-width: 350px;" data-field="capa">${item.capa || ''}</td>
								<td class="align-middle datetime text-nowrap" style="width: 120px; min-width: 120px;" data-field="deadline_capa">${item.deadline_capa || ''}</td>
								<td class="align-middle pic" style="width: 120px; min-width: 120px;" data-field="pic_capa">${item.pic_capa || ''}</td>
								<td class="align-middle attachment" style="width: 150px; min-width: 150px;" data-id="${item.id}">
									${item.filename ? `<span class='text-primary'>Lihat Data</span>` : `<span class="text-muted">No file</span>`}
								</td>
							</tr>`;
						});
					}
					tbody.html(html);
				
					// Update counter with number of assignments loaded
					badge.removeClass('badge-secondary').addClass('badge-info').text(res.length + ' item' + (res.length!==1?'s':''));
				}
				
			})
		}
	
</script>
