<div class="content-header page-data">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="content-body mt-6">
	<div class="main-container mt-2">
		<div class="row">
			<div class="col-12">
				<div class="card shadow-sm border-0">
					<div class="card-body p-4">
						<div id="result">
							<?php foreach($data as $dept => $val): ?>
								<div class="mb-4">
									<div class="card border-0 shadow-sm">
										<div class="card-header bg-gradient-light border-0 py-3">
											<h5 class="mb-0">
												<button class="btn btn-link text-decoration-none text-dark p-0 font-weight-bold dept-toggle flex-grow-1 text-left" 
														type="button" 
														data-toggle="collapse" 
														data-target="#dept-collapse-<?=md5($dept)?>" 
														aria-expanded="false" 
														aria-controls="dept-collapse-<?=md5($dept)?>">
													<div class="d-flex justify-content-between align-items-center">
														<div>
															<i class="fas fa-chevron-down me-2 text-info"></i>
															<span><?= $dept ?></span>
														</div>
													</div>
												</button>
											</h5>
										</div>
										
										<div class="collapse" id="dept-collapse-<?=md5($dept)?>">
											<div class="table-responsive">
												<table class="table table-hover mb-0">
													<thead class="bg-light">
														<tr>
															<th class="border-0 text-muted small">Section</th>
															<th class="border-0 text-muted small">Aktivitas</th>
															<th class="border-0 text-muted small">Audit Area</th>
															<th class="border-0 text-muted small">Kriteria</th>
															<th class="border-0 text-muted small">Risk</th>
															<th class="border-0 text-muted small">Internal Control</th>
															<th class="border-0 text-muted small">Pengujian</th>
															<th class="border-0 text-muted small">Hasil Review</th>
															<th class="border-0 text-muted small">Finding</th>
															<th class="border-0 text-muted small">Bobot Finding</th>
															<th class="border-0 text-muted small">Unconformity</th>
															<th class="border-0 text-muted small">Dampak</th>
															<th class="border-0 text-muted small">Root Cause</th>
															<th class="border-0 text-muted small">Recommendation</th>
															<th class="border-0 text-muted small">Finding Control Status Finding</th>
															<th class="border-0 text-muted small">CAPA</th>
															<th class="border-0 text-muted small">Deadline CAPA</th>
															<th class="border-0 text-muted small">PIC CAPA</th>
															<th class="border-0 text-muted small text-center">Action</th>
														</tr>
													</thead>
													<tbody class="department-data" data-group-id="<?=$val['id_audit_plan_group']?>">
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
modal_open('modal-form');
	modal_body();
		form_open(base_url('internal/audit_assignment/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('id_plan'),'id_plan');
			input('text',lang('id_risk_control'),'id_risk_control');
			input('datetime',lang('audit_start_date'),'audit_start_date');
			input('datetime',lang('audit_end_date'),'audit_end_date');
			input('datetime',lang('audit_closing_date'),'audit_closing_date');
			input('text',lang('auditor'),'auditor');
			input('text',lang('auditee'),'auditee');
			textarea(lang('review_result'),'review_result');
			textarea(lang('finding'),'finding');
			input('text',lang('bobot_finding'),'bobot_finding');
			textarea(lang('unconformity'),'unconformity');
			textarea(lang('risk_finding'),'risk_finding');
			textarea(lang('root_cause'),'root_cause');
			textarea(lang('recomendation'),'recomendation');
			input('text',lang('status_finding'),'status_finding');
			textarea(lang('capa'),'capa');
			input('datetime',lang('deadline_capa'),'deadline_capa');
			input('text',lang('pic_capa'),'pic_capa');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('internal/audit_assignment/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>


<script>
	$(document).ready(function() {
		let isEditing = false;
		let originalValue = '';
		let loadedDepartments = [];
		
		// Inline editing for regular fields
		$(document).on('click', '.editable:not(.editable-status)', function() {
			if (isEditing) return;
			
			const $cell = $(this);
			const field = $cell.data('field');
			originalValue = $cell.text().trim();
			
			// Create input element
			let inputElement;
			if (field === 'finding' || field === 'review_result' || field === 'root_cause' || field === 'recomendation' || field === 'unconformity' || field === 'risk_finding' || field === 'capa') {
				inputElement = $('<textarea class="inline-input"></textarea>');
			} else {
				inputElement = $('<input type="text" class="inline-input">');
			}
			
			inputElement.val(originalValue);
			
			// Replace cell content
			$cell.addClass('editing').html(inputElement);
			inputElement.focus().select();
			isEditing = true;
			
			// Add editing indicator
			$cell.append('<i class="fas fa-edit editing-indicator"></i>');
		});
		
		// Status editing with dropdown
		$(document).on('click', '.editable-status', function() {
			if (isEditing) return;
			
			const $badge = $(this);
			const currentStatus = $badge.data('value');
			const currentColor = $badge.data('color');
			originalValue = {status: currentStatus, color: currentColor};
			
			const statusOptions = [
				{status: 'Open', color: 'warning'},
				{status: 'In Progress', color: 'info'},
				{status: 'Closed', color: 'success'},
				{status: 'Cancelled', color: 'danger'}
			];
			
			let selectElement = $('<select class="status-dropdown"></select>');
			statusOptions.forEach(option => {
				const selected = option.status === currentStatus ? 'selected' : '';
				selectElement.append(`<option value="${option.status}" data-color="${option.color}" ${selected}>${option.status}</option>`);
			});
			
			$badge.addClass('editing').html(selectElement);
			selectElement.focus();
			isEditing = true;
		});
		
		// Save on Enter key
		$(document).on('keydown', '.inline-input, .status-dropdown', function(e) {
			if (e.key === 'Enter' && !e.shiftKey) {
				e.preventDefault();
				saveInlineEdit($(this));
			} else if (e.key === 'Escape') {
				cancelInlineEdit($(this));
			}
		});
		
		// Save on blur (when leaving the field)
		$(document).on('blur', '.inline-input, .status-dropdown', function() {
			saveInlineEdit($(this));
		});
		
		// Save inline edit
		function saveInlineEdit($input) {
			if (!isEditing) return;
			
			const $cell = $input.closest('.editable, .editable-status');
			const field = $cell.data('field');
			const rowId = $cell.closest('tr').data('id');
			let newValue = $input.val().trim();
			
			// Handle status field
			if ($cell.hasClass('editable-status')) {
				const selectedOption = $input.find('option:selected');
				const newStatus = selectedOption.val();
				const newColor = selectedOption.data('color');
				
				$cell.removeClass('editing').html(`<span class="badge badge-${newColor} editable-status" data-field="status_finding" data-value="${newStatus}" data-color="${newColor}">${newStatus}</span>`);
				newValue = newStatus;
			} else {
				$cell.removeClass('editing').html(newValue || originalValue);
			}
			
			$.ajax({
                url: '<?= base_url('internal/audit_assignment/update_field') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: rowId,
                    field: field,
                    value: newValue
                },
                success: function(response) {
                    $cell.removeClass('saving');
                    
                    if (response.status === 'success') {
                        $cell.addClass('save-success');
                        console.log(`Successfully saved ${field}: ${newValue} for ID: ${rowId}`);
                    } else {
        				$cell.html(originalValue);                            
                    }
					cAlert.open(response.message, response.status);
					isEditing = false;
                },
			})
		}
		
		// Cancel inline edit
		function cancelInlineEdit($input) {
			const $cell = $input.closest('.editable, .editable-status');
			
			if ($cell.hasClass('editable-status')) {
				const status = originalValue.status;
				const color = originalValue.color;
				$cell.removeClass('editing').html(`<span class="badge badge-${color} editable-status" data-field="status_finding" data-value="${status}" data-color="${color}">${status}</span>`);
			} else {
				$cell.removeClass('editing').html(originalValue);
			}
			
			isEditing = false;
		}
		
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
		
		// Load data for specific department
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
								<td class="align-middle" style="width: 120px; min-width: 120px;" data-field="section_name">${item.section || ''}</td>
								<td class="align-middle" style="width: 200px; min-width: 200px;" data-field="aktivitas">${item.aktivitas || ''}</td>
								<td class="align-middle" style="width: 150px; min-width: 150px;" data-field="audit_area">${item.sub_aktivitas || ''}</td>
								<td class="align-middle" style="width: 180px; min-width: 180px;" data-field="kriteria"></td>
								<td class="align-middle" style="width: 200px; min-width: 200px;" data-field="risk">${item.risk || ''}</td>
								<td class="align-middle" style="width: 180px; min-width: 180px;" data-field="internal_control"></td>
								<td class="align-middle editable" style="width: 150px; min-width: 150px;" data-field="pengujian"></td>
								<td class="align-middle editable" style="width: 220px; min-width: 220px;" data-field="hasil_review">${item.hasil_review || ''}</td>
								<td class="align-middle editable" style="width: 250px; min-width: 250px;" data-field="finding">${item.finding || ''}</td>
								<td class="align-middle text-center editable" style="width: 80px; min-width: 80px;" data-field="bobot_finding">${item.bobot_finding || ''}</td>
								<td class="align-middle editable" style="width: 200px; min-width: 200px;" data-field="unconformity">${item.unconformity || ''}</td>
								<td class="align-middle editable" style="width: 180px; min-width: 180px;" data-field="dampak">${item.dampak || ''}</td>
								<td class="align-middle editable" style="width: 200px; min-width: 200px;" data-field="root_cause">${item.root_cause || ''}</td>
								<td class="align-middle editable" style="width: 220px; min-width: 220px;" data-field="recommendation">${item.recommendation || ''}</td>
								<td class="align-middle editable" style="width: 200px; min-width: 200px;" data-field="capa">${item.capa || ''}</td>
								<td class="align-middle text-nowrap editable" style="width: 120px; min-width: 120px;" data-field="deadline_capa">${item.deadline_capa || ''}</td>
								<td class="align-middle editable" style="width: 120px; min-width: 120px;" data-field="pic_capa">${item.pic_capa || ''}</td>
								<td style="width: 150px; min-width: 150px;"></td>
								<td class="align-middle text-center text-nowrap" style="width: 100px; min-width: 100px;">
									<button class="btn btn-sm btn-outline-warning btn-icon-only btn-edit" type="button" data-id="${item.id}" title="Edit">
										<i class="fas fa-edit"></i>
									</button>
									<button class="btn btn-sm btn-outline-danger btn-icon-only btn-delete" type="button" data-id="${item.id}" title="Delete">
										<i class="fas fa-trash"></i>
									</button>
								</td>
							</tr>`;
						});
					}
					tbody.html(html);
				
					// Update counter
					badge.removeClass('badge-secondary').addClass('badge-info');
				}
				
			})
		}
		
		$(document).on('click', '.btn-edit', function() {
			const id = $(this).data('id');
			
			// Create dummy data for editing
			const dummyData = {
				id: id,
				id_plan: `AP-${Math.floor(Math.random() * 1000)}`,
				auditor_name: 'Sample Auditor',
				auditee_name: 'Sample Auditee',
				audit_start_date: '2024-01-15',
				audit_end_date: '2024-01-30'
			};
			
			alert(`Edit mode for ID: ${id}\nData: ${JSON.stringify(dummyData, null, 2)}`);
			
			// Uncomment below to actually open modal if it exists
			// $('#modal-form').modal('show');
			// Object.keys(dummyData).forEach(key => {
			//     $(`[name="${key}"]`).val(dummyData[key]);
			// });
		});
		
		$(document).on('click', '.btn-delete', function() {
			const id = $(this).data('id');
			if (confirm('Are you sure you want to delete this audit assignment?')) {
				// Simulate delete operation
				const row = $(this).closest('tr');
				
				// Add fade out animation
				row.fadeOut(300, function() {
					$(this).remove();
					
					// Update counter
					const assignId = $(this).closest('.department-data').data('assign-id');
					const remainingRows = $(`.department-data[data-assign-id="${assignId}"] tr`).length;
					const badge = $(`.department-data[data-assign-id="${assignId}"]`).closest('.card').find('.assignment-count');
				});
				
				// Show success message
				alert('Audit assignment deleted successfully!');
			}
		});
	});
</script>
