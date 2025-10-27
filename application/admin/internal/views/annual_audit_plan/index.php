<div class="content-body mt-6">
	<input type="hidden" id="activity_item" value="<?=htmlspecialchars(json_encode($activity))?>">
	<div class="main-container mt-2">
		<div class="row justify-content-center">
			<div class="col-12 col-xl-11">
				<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
					<div class="card-body py-3">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="mb-0 text-dark font-weight-bold">
								Annual Audit Plan
							</h3>
							<div class="btn-group" role="group" aria-label="Filter Plan History">
								<?php 
									$activeFilter = isset($filter)?$filter:'plan';
									$baseUrl = base_url('internal/annual_audit_plan');
								?>
								<a href="<?="{$baseUrl}?filter=plan"?>" class="btn btn-outline-primary filter-switch <?=($activeFilter=='plan'?'active':'')?>" data-filter="plan">
									<i class="fas fa-calendar-alt mr-1"></i> Plan
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
							<?php 
							foreach($data as $year => $departments):?>
								<div class="mb-4">
									<div class="card border-0 shadow-sm">
										<div class="card-header text-white border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);border-radius: 15px;">
											<div class="d-flex justify-content-between align-items-center w-100">
												<button class="btn btn-link text-white text-decoration-none p-0 font-weight-bold year-toggle flex-grow-1 text-left" 
														type="button" 
														data-toggle="collapse" 
														data-target="#year-collapse<?=$year?>" 
														aria-expanded="false" 
														aria-controls="year-collapse<?=$year?>">
													<div class="d-flex justify-content-between align-items-center">
														<div class="d-flex align-items-center">
															<i class="fas fa-chevron-down mr-3"></i>
															<div>
																<h4 class="mb-1 font-weight-bold text-white">Annual Audit Plan <?= $year ?></h4>
																<small class="text-white-50">Click to expand audit plan details</small>
															</div>
														</div>
														<div class="text-right">
															<span class="badge badge-light text-primary font-weight-bold px-3 py-2">
																<?= count($departments) ?> Department<?= count($departments) > 1 ? 's' : '' ?>
															</span>
														</div>
													</div>
												</button>
												<button class="btn btn-light btn-sm ml-3 add-plan" type="button" data-year="<?=$year?>" title="Add New Plan">
													<i class="fas fa-plus mr-2"></i>Add Plan
												</button>
											</div>
										</div>
										
										<div class="collapse" id="year-collapse<?=$year?>" data-year="<?=$year?>">
											<div class="card-body p-0">
												<?php
												// Build summary schedule table (simple calendar-like) for the year
												$summaryRows = [];
												$rowNo = 1;
												foreach($departments as $deptName => $deptData){
													// $deptData structure appears to be an array with one or more detail arrays containing aktivitas
													foreach($deptData as $detail){
														if(!is_array($detail) || !isset($detail['aktivitas'])) continue; // skip non-detail entries
														$months = array_fill(1,12,false);
														$startDate = isset($detail['start_date']) && $detail['start_date'] ? strtotime($detail['start_date']) : null;
														$endDate   = isset($detail['end_date']) && $detail['end_date'] ? strtotime($detail['end_date']) : $startDate;
														if($startDate){
															$startMonth = (int)date('n',$startDate);
															$endMonth   = (int)date('n',$endDate);
															if($endMonth < $startMonth) $endMonth = $startMonth; // safety
															for($m=$startMonth; $m <= $endMonth; $m++){
																$months[$m] = true;
															}
														}
														$summaryRows[] = [
															'no' => $rowNo++,
															'id_department' => $detail['id_department'] ?? '',
															'id_plan_group' => $detail['id_audit_plan_group'] ?? '',
															'department' => $deptName . (isset($detail['objective']) && $detail['objective'] ? '' : ''),
															'auditor' => $detail['auditor'] ?? '',
															'auditee' => $detail['auditee'] ?? '',
															'expense' => isset($detail['expense_est_total']) ? number_format($detail['expense_est_total'],0,',','.') : '',
															'months' => $months
														];
													}
												}
												?>
												<?php if(!empty($summaryRows)): ?>
													<div class="table-responsive px-3 pt-3">
														<table class="table table-sm table-bordered table-striped mb-4 align-middle table-summary-year">
															<thead class="thead-light">
																<tr class="text-center align-middle">
																	<th rowspan="2" class="align-middle" style="vertical-align: middle;" width="40">No</th>
																	<th rowspan="2" class="text-left align-middle">Department</th>
																	<th rowspan="2" class="align-middle">Auditor</th>
																	<th rowspan="2" class="align-middle">Auditee</th>
																	<th rowspan="2" class="text-right align-middle" width="110">Expense (Rp)</th>
																	<th colspan="12">Months</th>
																</tr>
																<tr class="text-center">
																	<?php $mn=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; foreach($mn as $m): ?>
																		<th style="width:42px;" class="p-1"><?=$m?></th>
																	<?php endforeach; ?>
																</tr>
															</thead>
															<tbody>
																<?php foreach($summaryRows as $r): ?>
																	<tr>
																		<td class="text-center" width="1px"><?=$r['no']?></td>
																		<td class="w-25"><?=$r['department']?></td>
																		<td width="10%" class="text-nowrap">
																			<select class="select2 form-control auditor" name="auditor" data-id-plan-group="<?=$r['id_plan_group']?>" data-id-department="<?=$r['id_department']?>">
																				<option value="">-- Auditor --</option>
																				<?php foreach($auditor as $a) :?> 
																					<option value="<?=$a['id']?>" <?= $r['auditor'] == $a['id'] ? 'selected' : '' ?>><?=$a['nama']?></option>
																				<?php endforeach ?>
																			</select>
																		</td>
																		<td width="10%" class="text-nowrap">
																			<select class="select2 form-control auditee" name="auditee" data-id-plan-group="<?=$r['id_plan_group']?>" data-id-department="<?=$r['id_department']?>">
																				<option value="">-- Auditee --</option>
																				<?php foreach($auditee as $a) : ?>
																					<option value="<?=$a['id']?>" <?= $r['auditee'] == $a['id'] ? 'selected' : '' ?>><?=$a['nama']?></option>
																				<?php endforeach ?>
																			</select>
																		</td>
																		<!-- <td class="text-nowrap text-center text-nowrap" style="width: 1px;"><?=$r['auditor']?></td>
																		<td class="text-nowrap text-center text-nowrap" style="width: 1px;"><?=$r['auditee']?></td> -->
																		<td class="text-right text-nowrap"><?=$r['expense']?></td>
																		<?php for($m=1;$m<=12;$m++): $active=$r['months'][$m]; ?>
																			<td class="text-center p-1 month-cell <?=$active?'active-month':''?>"></td>
																		<?php endfor; ?>
																	</tr>
																<?php endforeach; ?>
															</tbody>
														</table>
													</div>
												<?php endif; ?>

												<style>
													.table-summary-year th, .table-summary-year td { font-size: 11px; }
													.table-summary-year .month-cell { font-weight: bold; font-size: 12px; }
													.table-summary-year .month-cell.active-month { background:#007bff; color:#fff; }
												</style>

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
																	<span class="badge badge-info ml-2"><?= count($deptData['data']['aktivitas']) ?> items</span>
																</div>
															</button>
														</div>
														<div class="collapse" id="dept-collapse<?=$year?>-<?=md5($dept)?>">
															<div class="table-responsive">
																<table class="table table-hover mb-0">
																	<thead class="bg-light">
																		<tr>
																			<th class="border-0 text-muted small">Aktivitas</th>
																			<th class="border-0 text-muted small">Audit Area</th>
																			<th class="border-0 text-muted small">Resiko</th>
																			<th class="border-0 text-muted small">Objektif</th>
																			<th class="border-0 text-muted small">Durasi</th>
																			<th class="border-0 text-muted small">Start Date Est</th>
																			<th class="border-0 text-muted small">End Date Est</th>
																			<th class="border-0 text-muted small">Expense Est</th>
																			<th class="border-0 text-muted small">Closing Date</th>
																			<th class="border-0 text-muted small">Expense Real</th>
																			<th class="border-0 text-muted small">Status</th>
																			<th class="border-0 text-muted small text-center"></th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php 
																		foreach($deptData as $key => $detail):
																			$count = count($detail['aktivitas']);
																			foreach($detail['aktivitas'] as $i => $activity) : ?>
																			<tr>
																				<td class="align-middle text-nowrap" width="1px"><?= $activity['aktivitas'] ?></td>
																				<td class="align-middle text-nowrap" width="1px"><?= $activity['sub_aktivitas'] ?></td>
																				<td class="align-middle text-nowrap" width="1px">
																					<div class="d-flex flex-wrap">
																						<?php foreach($activity['risk'] as $risk) : ?>
																							<span class="badge badge-light border mr-1 mb-1 p-2"><?=$risk['risk']?></span>
																						<?php endforeach ?>
																					</div>
																				</td>
																				<?php if($i == 0) : ?>
																					<td rowspan="<?=$count?>" class="align-middle">
																						<div class="text-sm"><?=$detail['objective']?></div>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-center detail-durasi text-nowrap" data-id="<?=$detail['id_audit_plan_group']?>">
																						<?php if(isset($detail['duration'])): ?>
																							<h6 class="badge badge-info"><?= $detail['duration'] ?> Hari</h6>
																						<?php else: ?>
																							<h6 class="text-muted">-</h6>
																						<?php endif; ?>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-nowrap text-center">
																						<h6 class="text-muted"><?= $detail['start_date'] ? date('d M Y', strtotime($detail['start_date'])) : '-' ?></h6>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-nowrap text-center">
																						<h6 class="text-muted"><?= $detail['end_date'] ? date('d M Y', strtotime($detail['end_date'])) : '-' ?></h6>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-nowrap text-right detail-expense" data-id="<?=$detail['id_audit_plan_group']?>" data-cat="est">
																						<h6 class="font-weight-medium">Rp. <?= isset($detail['expense_est_total']) ? number_format($detail['expense_est_total'],0) : 0 ?></h6>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-nowrap text-center">
																						<h6 class="text-muted"><?= $detail['closing_date'] ? date('d M Y', strtotime($detail['closing_date'])) : '-' ?></h6>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-nowrap text-right detail-expense" data-id="<?=$detail['id_audit_plan_group']?>" data-cat="real">
																						<h6 class="font-weight-medium">Rp. <?= isset($detail['expense_real_total']) ? number_format($detail['expense_real_total'],0) : 0 ?></h6>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-center text-nowrap">
																						<?php if(isset($detail['status']) && $detail['status'] == 'unplanned'): ?>
																							<h6 class="badge badge-secondary"><?= ucfirst($detail['status']) ?> </h6>
																						<?php elseif(isset($detail['status']) && $detail['status'] == 'planned'): ?>
																							<h6 class="badge badge-warning"><?= ucfirst($detail['status']) ?> </h6>
																						<?php elseif(isset($detail['status']) && $detail['status'] == 'canceled'): ?>
																							<h6 class="badge badge-danger cancel-detail" data-id="<?=$detail['id_audit_plan_group']?>"><?= ucfirst($detail['status']) ?> </h6>
																						<?php else : ?>
																							<h6 class="badge badge-success"><?= ucfirst($detail['status']) ?> </h6>
																						<?php endif; ?>
																					</td>
																					<td rowspan="<?=$count?>" width="1px" class="align-middle text-center text-nowrap">
																						<?php if($detail['status'] == 'planned' || $detail['status'] == 'unplanned'):?>
																						<button class="btn btn-sm btn-outline-warning btn-icon-only btn-edit" type="button" data-id="<?=$detail['id_audit_plan_group']?>" title="Edit">
																							<i class="fas fa-edit"></i>
																						</button>
																						<button class="btn btn-sm btn-outline-success btn-icon-only btn-completed" type="button" data-id="<?=$detail['id_audit_plan_group']?>" title="Completed">
																							<i class="fas fa-check"></i>
																						</button>
																						<button class="btn btn-sm btn-outline-danger btn-icon-only btn-cancel" type="button" data-id="<?=$detail['id_audit_plan_group']?>" title="Cancel">
																							<i class='fas fa-times'></i>
																						</button>
																						<?php endif; ?>
																					</td>
																				<?php endif ?>
																			</tr>																	
																		<?php 
																	endforeach;
																	endforeach; ?>
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
	<?php
	modal_open('mEdit', 'Edit', '', 'modal-fullscreen');
		modal_body();
			form_open(base_url('internal/annual_audit_plan/save'), 'post', 'form');
				echo "
					<input type='hidden' id='id_plan' name='id_plan'>
					<input type='hidden' id='id_plan_group' name='id_plan_group'>
					<input type='hidden' id='start_date' name='start_date'>
					
					<div class='row mb-4'>
						<div class='col-8'>
							<h6 class='text-muted mb-3'>Activities & Duration</h6>
							<div class='card border-0 shadow-sm'>
								<div class='card-body p-3'>
									<label class='form-label small text-muted'>Activity Type</label>
									<select class='form-control form-control-sm mb-2 w-25' name='activity_type' id='activity_type' required>
										<option value='' selected>-- Select Activity Type --</option>
										<option value='full'>Full</option>
										<option value='partial'>Partial</option>
									</select>
									<div id='activity-container'>
									</div>	
								</div>
							</div>
						</div>
						<div class='col-4' style='border-left: 4px solid #22c2dc; padding-left: 10px; border-radius: 4px;'>
							<h6 class='text-muted mb-3'>Objective</h6>
							<div class='card border-0 shadow-sm'>
								<div class='card-body p-3'>
									<div id='objektif-container'>
										<textarea class='form-control form-control-sm' name='objektif' id='objektif' rows='3' placeholder='Objective' required></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class='row'>
						<div class='col-12'>
							<h6 class='text-muted mb-3'>Expense Estimate</h6>
							<div class='card border-0 shadow-sm'>
								<div class='card-body p-3'>
									<div id='expense-est-container'>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				";
			form_button('Simpan', 'Batal');
			form_close();
	modal_close();
	
	modal_open('mCompleted', 'Completed', '', 'modal-fullscreen');
		modal_body();
			col_init(3,9);
			form_open(base_url('internal/annual_audit_plan/completedPlan'), 'post', 'formCompleted');
				echo "
					<input type='hidden' id='id_plan_completed' name='id_plan_group'>
					
					<div class='row mb-4'>
						<div class='col-12'>
							<h6 class='text-muted mb-3'>Closing Information</h6>
							<div class='card border-0 shadow-sm'>
								<div class='card-body p-3'>
									<div class='row'>
										<div class='col-6'>
											<label class='form-label small text-muted'>Closing Date</label>
											<input type='date' class='form-control form-control-sm' name='closing_date' required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='col-12'>
							<h6 class='text-muted mb-3'>Actual Expense</h6>
							<div class='card border-0 shadow-sm'>
								<div class='card-body p-3'>
									<div id='expense-real-container'>
										<div class='expense-real-row mb-2'>
											<div class='row align-items-center'>
												<div class='col-2'>
													<select class='form-control form-control-sm' name='expense_real_type[]' required>
														<option value=''>-- Select Expense Item --</option>";
														foreach($expense_item as $item){
															echo "<option value='".$item['id']."'>".$item['name']."</option>";
														}
													echo "</select>
												</div>
												<div class='col-2'>
													<input type='number' class='form-control form-control-sm' name='expense_real_amount[]' placeholder='Amount' min='0' required>
												</div>
												<div class='col-1'>
													<input type='number' class='form-control form-control-sm day-real-input' name='expense_real_day[]' placeholder='Days' min='0' required>
												</div>
												<div class='col-2'>
													<input type='number' class='form-control form-control-sm expense-real-input' name='total_real_amount[]' placeholder='Total Amount' min='0' required>
												</div>
												<div class='col-4'>
													<input type='text' class='form-control form-control-sm' name='expense_real_note[]' placeholder='Note' required>
												</div>
												<div class='col-1'>
													<button type='button' class='btn btn-sm btn-outline-danger btn-icon-only remove-expense-real' style='display:none;'>
														<i class='fas fa-trash'></i>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class='d-flex justify-content-between align-items-center mt-3 pt-3 border-top'>
										<button type='button' class='btn btn-sm btn-outline-primary' id='add-expense-real'>
											<i class='fas fa-plus me-1'></i> Add Item
										</button>
										<div class='text-muted'>
											<small>Total: Rp <span id='total-expense-real' class='font-weight-bold'>0</span></small>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				";
			form_button('Complete Audit', 'Cancel');
			form_close();
	modal_close();

	modal_open('mDetailDuration', 'Detail Duration');
		modal_body();
			col_init(3,9);
				echo "
				<table class='table table-bordered'>
					<thead>
						<tr>
							<th class='text-center'>No</th>
							<th>Activity Name</th>
							<th class='text-center'>Start Date</th>
							<th class='text-center'>Duration (Days)</th>
							<th class='text-center'>End Date</th>
						</tr>
					</thead>
					<tbody id='detail-duration-body'>
					</tbody>
				</table>" ;							
	modal_close();

	modal_open('mDetailExpense', 'Detail Expense', 'modal-lg', '');
		modal_body();
			col_init(3,9);
				echo "
				<table class='table table-bordered'>
					<thead class='text-center'>
						<tr>
							<th class='text-center'>No</th>
							<th>Expense Type</th>
							<th>Amount</th>
							<th>Days</th>
							<th>Total Amount</th>
							<th>Note</th>
						</tr>
					</thead>
					<tbody id='detail-expense-body'>
					</tbody>
				</table>" ;							
	modal_close();
	
	modal_open('mConfirmCancel', 'Confirm Cancel');
		modal_body();
			col_init(3,9);
				input('hidden', 'id_plan_cancel', 'id_plan_cancel', );
				echo "<textarea class='form-control' id='reason_cancel' required name='reason_cancel' rows='3' placeholder='Please provide a reason for cancelling this audit plan.'></textarea>";
				echo "<button type='button' class='btn btn-danger mt-3' onclick='confirmCancel()'>Yes, Cancel Plan</button>";
				echo "<button type='button' class='btn btn-secondary mt-3' data-dismiss='modal'>No, Go Back</button>";
	modal_close();

	modal_open('mDetailCancel', 'Detail Cancelation');
		modal_body();
			col_init(3,9);
				echo "<div id='detail-cancel-body'></div>";
	modal_close();

	modal_open('mAddPlan', 'Add Annual Audit Plan', '', '');
		modal_body();
			form_open(base_url('internal/annual_audit_plan/add_plan'), 'post', 'formAddPlan');
				input('hidden', 'year_plan', 'year_plan', '', 'readonly');		
				select2('Audit', 'id_audit_universe[]', 'required', get_detail_all_audit_universe(), 'id', 'val', '', 'multiple');
				form_button('Save Plan', 'Cancel');
			form_close();
	modal_close();
	?>

<script type="text/javascript">

	$(document).on('change', '.auditor', function(){
		let id_plan_group = $(this).data('id-plan-group');
		let id_department = $(this).data('id-department');
		let value = $(this).val();

		if(value.length == 0){
			cAlert.open('Please select an auditor.', 'info');
			return;
		}
		setAuditeeAuditor('auditor', id_plan_group, id_department, value);
	})

	$(document).on('change', '.auditee', function(){
		let id_plan_group = $(this).data('id-plan-group');
		let id_department = $(this).data('id-department');
		let value = $(this).val();

		if(value.length == 0){
			cAlert.open('Please select an auditee.', 'info');
			return;
		}
		setAuditeeAuditor('auditee', id_plan_group, id_department, value);
	})
	// Filter switch via AJAX (progressive enhancement)
	$(document).on('click', '.filter-switch', function(e){
		// Allow normal navigation if user opens in new tab
		if(e.ctrlKey || e.metaKey || e.shiftKey) return;
		e.preventDefault();
		var url = $(this).attr('href');
		$('.filter-switch').removeClass('active');
		$(this).addClass('active');
		$('#result').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><div class="mt-2 small text-muted">Loading '+$(this).data('filter')+'...</div></div>');
		$.get(url, function(html){
			// Extract #result content from full response
			var temp = $('<div>').html(html);
			var newContent = temp.find('#result').html();
			if(newContent){
				$('#result').html(newContent);
				// Re-run auto open year logic after content replacement
				$(document).trigger('content:rebind');
			}else{
				// fallback full reload
				location.href = url;
			}
		});
	});

	// Rebind after dynamic load
	$(document).on('content:rebind', function(){
		// collapse icons reset
		$('.year-toggle .fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
	});
	// Handle year collapse - hanya satu year yang bisa terbuka
	$(document).on('click', '.year-toggle', function(e) {
		let targetCollapse = $(this).data('target');
		
		// Tutup semua year collapse yang lain
		$('.year-toggle').not(this).each(function() {
			let otherTarget = $(this).data('target');
			if (otherTarget !== targetCollapse) {
				$(otherTarget).collapse('hide');
				$(this).attr('aria-expanded', 'false');
				$(this).find('.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			}
		});
	});

	// Handle department collapse - hanya satu department yang bisa terbuka dalam satu year
	$(document).on('click', '.dept-toggle', function(e) {
		let targetCollapse = $(this).data('target');
		let currentYear = $(this).closest('[data-year]').data('year');
		
		// Tutup semua department collapse dalam year yang sama
		$(`[data-year="${currentYear}"] .dept-toggle`).not(this).each(function() {
			let otherTarget = $(this).data('target');
			if (otherTarget !== targetCollapse) {
				$(otherTarget).collapse('hide');
				$(this).attr('aria-expanded', 'false');
				$(this).find('.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			}
		});
	});

	// Update icon saat collapse ditampilkan
	$(document).on('shown.bs.collapse', '.collapse', function() {
		let toggle = $(`[data-target="#${this.id}"]`);
		toggle.find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
		toggle.attr('aria-expanded', 'true');
	});

	// Update icon saat collapse disembunyikan
	$(document).on('hidden.bs.collapse', '.collapse', function() {
		let toggle = $(`[data-target="#${this.id}"]`);
		toggle.find('.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
		toggle.attr('aria-expanded', 'false');
	});

	$(document).on('click', '.btn-edit', async function(){
		let id = $(this).data('id');
		let res = await $.ajax({
			url: base_url + 'internal/annual_audit_plan/getData',
			type: 'post',
			data: {id:id},
			dataType: 'json'
		})
		
		$('#id_plan').val(res.id);
		$('#id_plan_group').val(res.id_audit_plan_group);
		$('#start_date').val(res.start_date);
		$('#objektif').val(res.objective);
		$('#activity_type').val(res.type || '');
		if(res.type == null){
			$('#activity-container').html('');
			$('#activity-footer').remove();
		}else{
			generateActivityItem(res.activity);
		}
	
		generateExpenseEst(res.expense);
		// let html = '';
		// $.each(res.expense, function(i, item){
		// 	html += `
		// 	<div class='expense-est-row mb-2'>
		// 		<div class='row align-items-center'>
		// 			<div class='col-2'>
		// 				<select class='form-control form-control-sm' name='expense_type[]' required>
		// 					<option value=''>-- Select Expense Item --</option>`;
		// 					$.each(res.expense_type, function(j, v){
		// 						let selected = item.expense_type == v.id ? 'selected' : '';
		// 						html += `<option value='${v.id}' ${selected}>${v.name}</option>`;
		// 					});
		// 				html += `</select>
		// 			</div>
		// 			<div class='col-2'>
		// 				<input type='text' class='form-control money form-control-sm expense' name='expense_amount[]' placeholder='Amount' min='0' value='${item.amount}' required>
		// 			</div>
		// 			<div class='col-1'>
		// 				<input type='number' class='form-control form-control-sm day-input' name='expense_day[]' placeholder='Days' min='0' value='${item.days}' required>
		// 			</div>
		// 			<div class='col-2'>
		// 				<input type='text' class='form-control money form-control-sm expense-est-input' name='total_amount[]' placeholder='Total Amount' min='0' value='${item.amount * item.days}' required>
		// 			</div>
		// 			<div class='col-4'>
		// 				<input type='text' class='form-control form-control-sm note-input' name='expense_note[]' placeholder='Note' value='${item.note}' required>
		// 			</div>
		// 			<div class='col-1'>
		// 				<button type='button' class='btn btn-sm btn-outline-danger btn-icon-only remove-expense-est' >
		// 					<i class='fas fa-trash'></i>
		// 				</button>
		// 			</div>
		// 		</div>
		// 	</div>`;	
		// });
		// $('#expense-est-container').html(html);
	
		money_init();
		$('#mEdit').modal('show');
	})
	$(document).on('click', '.btn-completed', function(){
		let id = $(this).data('id');
		$('#id_plan_completed').val(id);
		$('#mCompleted').modal('show');
	})

	$(document).ready(function() {
		// Auto expand nearest year collapse on load
		(function autoOpenNearestYear(){
			var nowYear = (new Date()).getFullYear();
			var nearestBtn = null;
			var nearestDiff = Infinity;
			$('.year-toggle').each(function(){
				var txt = $(this).text();
				var match = txt.match(/(20\d{2})/); // capture year like 2023, 2024, etc
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
				// Open the collapse
				$(target).collapse('show');
				// Set icon state
				nearestBtn.find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
				nearestBtn.attr('aria-expanded','true');
			}
		})();
		// Apply cursor styling after dynamic content load
		$(document).on('content:rebind', function(){
			// collapse icons reset
			$('.year-toggle .fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			// Apply cursor styling to clickable elements
			$('.detail-durasi').css('cursor', 'pointer');
			$('.detail-expense').css('cursor', 'pointer');
			$('.cancel-detail').css('cursor', 'pointer');
		});
		
		// Initial load
		$('.detail-durasi').css('cursor', 'pointer');
		$('.detail-expense').css('cursor', 'pointer');
		$('.cancel-detail').css('cursor', 'pointer');
		
		$(document).on('input', '.expense, .day-input', function() {
			const row = $(this).closest('.expense-est-row');
			const amount = parseInt(row.find('.expense').val().replace(/\./g, '').replace(/,/g, '')) || 0;
			const days = parseInt(row.find('.day-input').val()) || 0;
			const total = amount * days;
			row.find('.expense-est-input').val(total);
			calculateTotalExpenseEst();
		});
		
		// Add activity row
		$(document).on('click','#add-activity', function() {
			const newRow = $('.activity-row:first').clone();
			newRow.find('input').val('');
			newRow.find('.remove-activity').show();
			$('#activity-container').append(newRow);
			updateRemoveButtons('.activity-row', '.remove-activity');
		});
		
		// Remove activity row
		$(document).on('click', '.remove-activity', function() {
			$(this).closest('.activity-row').remove();
			updateRemoveButtons('.activity-row', '.remove-activity');
			calculateTotalDuration();
		});
		
		// Add expense estimate row
		$(document).on('click', '#add-expense-est', function() {
			const newRow = $('.expense-est-row:first').clone();
			newRow.find('input').val('');
			newRow.find('.remove-expense-est').show();
			$('#expense-est-container').append(newRow);
			money_init();
			updateRemoveButtons('.expense-est-row', '.remove-expense-est');
		});

		// Add expense real row
		$('#add-expense-real').click(function() {
			const newRow = $('.expense-real-row:first').clone();
			newRow.find('input').val('');
			newRow.find('.remove-expense-real').show();
			$('#expense-real-container').append(newRow);
			updateRemoveButtons('.expense-real-row', '.remove-expense-real');
		});
		
		// Remove expense estimate row
		$(document).on('click', '.remove-expense-est', function() {
			$(this).closest('.expense-est-row').remove();
			updateRemoveButtons('.expense-est-row', '.remove-expense-est');
			calculateTotalExpenseEst();
		});

		// Remove expense estimate row
		$(document).on('click', '.remove-expense-real', function() {
			$(this).closest('.expense-real-row').remove();
			updateRemoveButtons('.expense-real-row', '.remove-expense-real');
			calculateTotalExpenseReal();
		});
		
		// Calculate totals
		$(document).on('input', '.duration-input', calculateTotalDuration);
		$(document).on('input', '.expense-est-input', calculateTotalExpenseEst);
		$(document).on('input', '.expense-real-input', calculateTotalExpenseReal);
		
		function updateRemoveButtons(rowClass, removeClass) {
			const rows = $(rowClass);
			if (rows.length > 1) {
				$(removeClass).show();
			} else {
				$(removeClass).hide();
			}
		}
		
		function calculateTotalDuration() {
			let total = 0;
			$('.duration-input').each(function() {
				total += parseInt($(this).val()) || 0;
			});
			$('#total-duration').text(total);
		}
		
		function calculateTotalExpenseEst() {
			let total = 0;
			$('.expense-est-input').each(function() {
				total += parseInt($(this).val().replace(/\./g, '').replace(/,/g, '')) || 0;
			});
			$('#total-expense-est').text(total.toLocaleString());
		}
		
		function calculateTotalExpenseReal() {
			let total = 0;
			$('.expense-real-input').each(function() {
				total += parseInt($(this).val()) || 0;
			});
			$('#total-expense-real').text(total.toLocaleString());
		}
	});
	$(document).on('click', '.add-plan', function(){
		let year = $(this).data('year');
		$('#year_plan').val(year);
		$('#mAddPlan').modal('show');
		// $('#id_audit_universe').val(null).trigger('change');
	});

	$(document).on('click', '.cancel-detail', function(){
		let id = $(this).data('id');
		$.ajax({
			url: base_url + 'internal/annual_audit_plan/getCancelDetail',
			type: 'post',
			data: {id:id},
			dataType: 'json',
			success: function(res){
				let detail = `
					<p><strong>Reason for Cancelation:</strong></p>
					<p>${res.reason}</p>
					<p><strong>Canceled By:</strong> ${res.nama}</p>
					<p><strong>Canceled At:</strong> ${res.canceled_at}</p>
				`;
				$('#detail-cancel-body').html(detail);
				$('#mDetailCancel').modal('show');
			}
		})
	});

	$(document).on('click', '.detail-durasi', function(){
		let id = $(this).data('id');
		$.ajax({
			url: base_url + 'internal/annual_audit_plan/getDetailDuration',
			type: 'post',
			data: {id:id},
			dataType: 'json',
			success: function(res){
				let tbody = '';
				console.log(res);
				if(res.length > 0){
					res.forEach((item, index) => {
						tbody += `
							<tr>
								<td class='text-center' width='1px'>${index + 1}</td>
								<td>${item.activity_name}</td>
								<td class='text-nowrap' width='1px'>${item.start_date}</td>
								<td class='text-center'>${item.duration_day}</td>
								<td class='text-nowrap' width='1px'>${item.end_date}</td>
							</tr>
						`;
					});
					tbody += `
						<tr>
							<td colspan='3' class='text-right font-weight-bold'>Total Duration</td>
							<td class='text-center font-weight-bold'>${res.reduce((sum, item) => sum + (parseInt(item.duration_day) || 0), 0)} days</td>
						</tr>`;
				} else {
					tbody = `
						<tr>
							<td colspan='5' class='text-center text-muted'>No data available</td>
						</tr>
					`;
				}
				$('#detail-duration-body').html(tbody);
				$('#mDetailDuration').modal('show');
			}
		})
	});
	$(document).on('click', '.detail-expense', function(){
		let cat = $(this).data('cat');
		let id = $(this).data('id');
		
		$.ajax({
			url: base_url + 'internal/annual_audit_plan/getDetailExpense',
			type: 'post',
			data: {id:id, cat:cat},
			dataType: 'json',
			success: function(res){
				let tbody = '';
				console.log(res);
				if(res.length > 0){
					res.forEach((item, index) => {
						tbody += `
							<tr>
								<td class='text-center' width='1px'>${index + 1}</td>
								<td>${item.name}</td>
								<td class='text-right'>Rp. ${numberFormat(item.amount, 0)}</td>
								<td class='text-right' width='1px'>${item.days}</td>
								<td class='text-right'>Rp. ${numberFormat(item.amount * item.days,0)}</td>
								<td>${item.note}</td>
							</tr>
						`;
					});
					tbody += `
						<tr>
							<td colspan='4' class='text-right font-weight-bold'>Total</td>
							<td class='text-right font-weight-bold'>Rp. ${res.reduce((sum, item) => sum + ((parseInt(item.amount) || 0) * (parseInt(item.days) || 0)), 0).toLocaleString()}</td>
							<td></td>
						</tr>`;
				} else {
					tbody = `
						<tr>
							<td colspan='6' class='text-center text-muted'>No data available</td>
						</tr>
					`;
				}
				$('#detail-expense-body').html(tbody);
				$('#mDetailExpense').modal('show');
			}
		})
	});
	let id_selected;

	$(document).on('click', '.btn-cancel', function(){
		id_selected = $(this).data('id');
		$('#id_plan_cancel').val(id_selected);
		$('#mConfirmCancel').modal('show');
		// cConfirm.open('Are you sure you want to cancel this audit plan?', 'confirmCancel');
	});

	$(document).on('change', '#activity_type', function () {
		let activityItem = JSON.parse($("#activity_item").val());
		
		let type = $(this).val();
		let activityArray = activityItem.map(item => ({
			activity_name: item.activity,
			start_date: '',
			end_date: '',
			duration_day: item.day
		}));
		
		generateActivityItem(activityArray);
		if(type === 'full') {
			$('.remove-activity').addClass('d-none');
		} else if(type === 'partial') {
			$('.remove-activity').removeClass('d-none');
		}else{
			$('#activity-container').html('');
			$('#activity-footer').remove();
		}
	});

	$(document).on('change input', '.start-date, .duration-input', function () {
		let row = $(this).closest('.activity-row');
		let startDate = row.find('.start-date').val();
		let duration = parseInt(row.find('.duration-input').val());

		if (startDate && duration && duration > 0) {
			let start = new Date(startDate);

			// hitung end date dengan skip weekend
			let end = workdays(start, duration);

			// format yyyy-mm-dd
			let endDate = end.toISOString().split('T')[0];
			row.find('.end-date').val(endDate);
		} else {
			row.find('.end-date').val('');
		}
	});


	function confirmCancel(){
		let id = $('#id_plan_cancel').val();
		let reason = $('#reason_cancel').val();
		
		$.ajax({
			url: base_url + 'internal/annual_audit_plan/cancelPlan',
			type: 'post',
			data: {id:id, reason:reason},
			dataType: 'json',
			success: function(res){
				if(res.status) {
					cAlert.open('Audit plan canceled successfully.', 'success', 'reloadPage');
				} else {
					cAlert.open('Audit plan canceled failed.', 'error');
				}
			}
		});
	}

	function reloadPage(){
		location.reload();
	}

	function workdays(startDate, days) {
		let current = new Date(startDate);

		while (days > 1) {  
			current.setDate(current.getDate() + 1);

			// Cek kalau bukan Sabtu(6) atau Minggu(0)
			if (current.getDay() !== 0 && current.getDay() !== 6) {
				days--;
			}
		}

		return current;
	}

	function setAuditeeAuditor(type, id_audit_plan_group, id_department, value) {
		let data = {};
		if(type === 'auditee') {
			data.value = value;
		} else {
			data.value = value;
		}
		data.type = type;
		data.id_audit_plan_group = id_audit_plan_group;
		data.id_department = id_department;

		$.ajax({
			url: base_url + 'internal/annual_audit_plan/setAuditeeAuditor',
			type: 'post',
			data: data,
			dataType: 'json',
			success: function(res){
				cAlert.open(res.message, res.status);
			}
		});
	}

	function generateActivityItem(data){
		let html = '';
		let duration = 0;
		$.each(data, function (index, item) {
			duration += parseInt(item.duration_day)
			html += `
				<div class='activity-row mb-2'>
					<div class='row align-items-center mb-2'>
						<div class='col-4'>
							<input type='text' class='form-control form-control-sm' name='activity_name[]' value="${item.activity_name || ''} " placeholder='Activity Name' required >
						</div>
						<div class='col-3'>
							<div class='input-group input-group-sm'>
								<span class='input-group-text'>Start</span>
								<input type='date' class='form-control start-date' name='start_duration[]' value="${item.start_date || ''}" required>
							</div>
						</div>
						<div class='col-1'>
							<input type='number' class='form-control form-control-sm duration-input' name='duration[]' value="${item.duration_day || ''}" placeholder='Duration (Days)' min='0' required>
						</div>
						<div class='col-3'>
							<div class='input-group input-group-sm'>
								<span class='input-group-text'>End</span>
								<input type='date' class='form-control end-date' name='end_duration[]' value="${item.end_date || ''}" required>
							</div>
						</div>
						<div class='col-1'>
							<button type='button' class='btn btn-sm btn-icon-only btn-outline-danger d-none remove-activity'>
								<i class='fas fa-trash'></i>
							</button>
						</div>
					</div>
				</div>`;
				});
			$('#activity-container').html(html);

			footer = `
				<div id='activity-footer'>
					<div class='d-flex justify-content-between align-items-center mt-3 pt-3 border-top'>
						<button type='button' class='btn btn-sm btn-outline-primary' id='add-activity'>
							<i class='fas fa-plus me-1'></i> Add Activity
						</button>
						<div class='text-muted'>
							<small>Total Duration: <span id='total-duration' class='font-weight-bold'>${duration || 0}</span> Days</small>
						</div>
					</div>
				</div>`;    
			$('#activity-footer').remove();
			$('#activity-container').after(footer);	
	}

	async function generateExpenseEst(data) {
				
    try {
		let res = await $.ajax({
			url: base_url + 'internal/annual_audit_plan/getExpenseItem/1',
			type: 'post',
			dataType: 'json'
		});	

		let html = '';
		if(!data || data.length == 0){
			html += `
			<div class='expense-est-row mb-2'>
				<div class='row align-items-center'>
					<div class='col-2'>
						<select class='form-control form-control-sm' name='expense_type[]' required>
							<option value=''>-- Select Expense Item --</option>`;
							$.each(res, function(j, v){
								html += `<option value='${v.id}'>${v.name}</option>`;
							});
						html += `</select>
					</div>
					<div class='col-2'>
						<input type='text' class='form-control money form-control-sm expense' name='expense_amount[]' placeholder='Amount' min='0' required>
					</div>
					<div class='col-1'>
						<input type='number' class='form-control form-control-sm day-input' name='expense_day[]' placeholder='Days' min='0' required>
					</div>
					<div class='col-2'>
						<input type='text' class='form-control money form-control-sm expense-est-input' name='total_amount[]' placeholder='Total Amount' min='0' required>
					</div>
					<div class='col-4'>
						<input type='text' class='form-control form-control-sm note-input' name='expense_note[]' placeholder='Note' required>
					</div>
					<div class='col-1'>
						<button type='button' class='btn btn-sm btn-outline-danger btn-icon-only remove-expense-est' >
							<i class='fas fa-trash'></i>
						</button>
					</div>
				</div>
			</div>`;	
		}else{
			$.each(data, function(i, item){
				html += `
				<div class='expense-est-row mb-2'>
					<div class='row align-items-center'>
						<div class='col-2'>
							<select class='form-control form-control-sm' name='expense_type[]' required>
								<option value=''>-- Select Expense Item --</option>`;
								$.each(res, function(j, v){
									let selected = item.expense_type == v.id ? 'selected' : '';
									html += `<option value='${v.id}' ${selected}>${v.name}</option>`;
								});
							html += `</select>
						</div>
						<div class='col-2'>
							<input type='text' class='form-control money form-control-sm expense' name='expense_amount[]' placeholder='Amount' min='0' value='${item.amount}' required>
						</div>
						<div class='col-1'>
							<input type='number' class='form-control form-control-sm day-input' name='expense_day[]' placeholder='Days' min='0' value='${item.days}' required>
						</div>
						<div class='col-2'>
							<input type='text' class='form-control money form-control-sm expense-est-input' name='total_amount[]' placeholder='Total Amount' min='0' value='${item.amount * item.days}' required>
						</div>
						<div class='col-4'>
							<input type='text' class='form-control form-control-sm note-input' name='expense_note[]' placeholder='Note' value='${item.note}' required>
						</div>
						<div class='col-1'>
							<button type='button' class='btn btn-sm btn-outline-danger btn-icon-only remove-expense-est' >
								<i class='fas fa-trash'></i>
							</button>
						</div>
					</div>
				</div>`;	
			});
		}
		$('#expense-est-container').html(html);
		$('#expense-est-footer').remove();
		
		let footer = `
			<div id='expense-est-footer'>
				<div class='d-flex justify-content-between align-items-center mt-3 pt-3 border-top'>
					<button type='button' class='btn btn-sm btn-outline-primary' id='add-expense-est'>
						<i class='fas fa-plus me-1'></i> Add Item
					</button>
					<div class='text-muted'>
						<small>Total: Rp <span id='total-expense-est' class='font-weight-bold'>0</span></small>
					</div>
				</div>
			</div>`
		$('#expense-est-container').after(footer);
		money_init();
		
    } catch (err) {
        console.error("Gagal load expense item:", err);
    }
}



</script>