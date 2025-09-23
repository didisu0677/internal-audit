<div class="content-header page-data">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">   		
    		
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
							<?php 
							$currentYear = null;
							$yearGroups = [];

							// Group data by year
							foreach($data as $val): 
								$year = date('Y', strtotime($val['start_date']));
								$yearGroups[$year][] = $val;
							endforeach;
							
							// Sort years (closest to current year first)
							// $currentYearNum = (int)date('Y');
							// uksort($yearGroups, function($a, $b) use ($currentYearNum) {
							// 	$diffA = abs($currentYearNum - (int)$a);
							// 	$diffB = abs($currentYearNum - (int)$b);
							// 	if ($diffA === $diffB) {
							// 		return (int)$a - (int)$b;
							// 	}
							// 	return $diffA - $diffB;
							// });
							
							foreach($yearGroups as $year => $yearData): ?>
								<div class="mb-4">
									<div class="card border-0 shadow-sm">
										<div class="card-header bg-gradient-light border-0 py-3">
											<h5 class="mb-0">
												<div class="d-flex justify-content-between align-items-center w-100">
													<button class="btn btn-link text-decoration-none text-dark p-0 font-weight-bold year-toggle flex-grow-1 text-left" 
															type="button" 
															data-toggle="collapse" 
															data-target="#collapse<?=$year?>" 
															aria-expanded="false" 
															aria-controls="collapse<?=$year?>">
														<div class="d-flex justify-content-between align-items-center">
															<div>
																<i class="fas fa-chevron-down me-2 text-primary"></i>
																<span class="text-primary">Annual Audit Plan <?= $year ?></span>
															</div>
															<span class="badge badge-primary"><?= count($yearData) ?> items</span>
														</div>
													</button>
													<button class="btn btn-sm btn-outline-primary ml-3 add-plan" type="button" data-year="<?=$year?>" title="Add Plan">
														<i class="fas fa-plus"></i> Add Plan
													</button>
												</div>
											</h5>
										</div>
										
										<div class="collapse" id="collapse<?=$year?>" data-year="<?=$year?>">
											<div class="card-body p-0">
												<div class="table-responsive">
													<table class="table table-hover mb-0">
														<thead class="bg-light">
															<tr>
																<th class="border-0 text-muted small">Department</th>
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
															<?php foreach($yearData as $val): ?>
																<tr>
																	<td class="align-middle">
																		<div class="text-sm font-weight-medium"><?= $val['department'] ?></div>
																	</td>
																	<td class="align-middle">
																		<div class="text-sm"><?= $val['aktivitas'] ?></div>
																	</td>
																	<td class="align-middle">
																		<div class="text-sm"><?= $val['sub_aktivitas'] ?></div>
																	</td>
																	<td class="align-middle">
																		<div class="d-flex flex-wrap">
																			<?php foreach($val['risk'] as $risk) : ?>
																				<span class="badge badge-light border mr-1 mb-1 p-2"><?=$risk['risk']?></span>
																			<?php endforeach ?>
																		</div>
																	</td>
																	<td class="align-middle">
																		<div class="text-sm"><?=$val['objektif']?></div>
																	</td>
																	<td class="align-middle text-center detail-durasi" data-id="<?=$val['id']?>">
																		<?php if(isset($val['duration'])): ?>
																			<span class="badge badge-info"><?= $val['duration'] ?> Hari</span>
																		<?php else: ?>
																			<span class="text-muted">-</span>
																		<?php endif; ?>
																	</td>
																	<td class="align-middle text-nowrap">
																		<small class="text-muted"><?= $val['start_date'] ? date('d M Y', strtotime($val['start_date'])) : '-' ?></small>
																	</td>
																	<td class="align-middle text-nowrap">
																		<small class="text-muted"><?= $val['end_date'] ? date('d M Y', strtotime($val['end_date'])) : '-' ?></small>
																	</td>
																	<td class="align-middle text-nowrap text-right detail-expense" data-id="<?=$val['id']?>" data-cat="est">
																		<span class="font-weight-medium">Rp. <?= isset($val['expense_est_total']) ? number_format($val['expense_est_total'],0) : 0 ?></span>
																	</td>
																	<td class="align-middle text-nowrap">
																		<small class="text-muted"><?= $val['closing_date'] ? date('d M Y', strtotime($val['closing_date'])) : '-' ?></small>
																	</td>
																	<td class="align-middle text-nowrap text-right detail-expense" data-id="<?=$val['id']?>" data-cat="real">
																		<span class="font-weight-medium">Rp. <?= isset($val['expense_real_total']) ? number_format($val['expense_real_total'],0) : 0 ?></span>
																	</td>
																	<td>
																		<?php if(isset($val['status']) && $val['status'] == 'planned'): ?>
																			<span class="badge badge-warning"><?= ucfirst($val['status']) ?> </span>
																		<?php elseif(isset($val['status']) && $val['status'] == 'canceled'): ?>
																			<span class="badge badge-danger cancel-detail" data-id="<?=$val['id']?>"><?= ucfirst($val['status']) ?> </span>
																		<?php else : ?>
																			<span class="badge badge-success"><?= ucfirst($val['status']) ?> </span>
																		<?php endif; ?>
																	</td>
																	<td class="align-middle text-center">
																		<?php if(empty($val['end_date'])) : ?>
																			<button class="btn btn-sm btn-outline-warning btn-icon-only btn-edit" type="button" data-id="<?=$val['id']?>" title="Edit">
																				<i class="fas fa-edit"></i>
																			</button>
																		<?php elseif($val['status'] == 'planned'):
																			?>
																			<button class="btn btn-sm btn-outline-success btn-icon-only btn-completed" type="button" data-id="<?=$val['id']?>" title="Completed">
																				<i class="fas fa-check"></i>
																			</button>
																			<button class="btn btn-sm btn-outline-danger btn-icon-only btn-cancel" type="button" data-id="<?=$val['id']?>" title="Cancel">
																				<i class='fas fa-times'></i>
																			</button>
																		<?php endif; ?>
																	</td>
																</tr>
															<?php endforeach; ?>
														</tbody>
													</table>
												</div>
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
					<input type='hidden' id='start_date' name='start_date'>
					
					<div class='row mb-4'>
						<div class='col-7'>
							<h6 class='text-muted mb-3'>Activities & Duration</h6>
							<div class='card border-0 shadow-sm'>
								<div class='card-body p-3'>
									<div id='activity-container'>
										<div class='activity-row mb-2'>
											<div class='row align-items-center'>
												<div class='col-7'>
													<input type='text' class='form-control form-control-sm' name='activity_name[]' placeholder='Activity Name' required>
												</div>
												<div class='col-4'>
													<input type='number' class='form-control form-control-sm duration-input' name='duration[]' placeholder='Duration (Days)' min='0' required>
												</div>
												<div class='col-1'>
													<button type='button' class='btn btn-sm btn-icon-only btn-outline-danger remove-activity' style='display:none;'>
														<i class='fas fa-trash'></i>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class='d-flex justify-content-between align-items-center mt-3 pt-3 border-top'>
										<button type='button' class='btn btn-sm btn-outline-primary' id='add-activity'>
											<i class='fas fa-plus me-1'></i> Add Activity
										</button>
										<div class='text-muted'>
											<small>Total Duration: <span id='total-duration' class='font-weight-bold'>0</span> Days</small>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-5' style='border-left: 4px solid #22c2dc; padding-left: 10px; border-radius: 4px;'>
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
										<div class='expense-est-row mb-2'>
											<div class='row align-items-center'>
												<div class='col-2'>
													<select class='form-control form-control-sm' name='expense_type[]' required>
														<option value=''>-- Select Expense Item --</option>";
														foreach($expense_item as $item){
															echo "<option value='".$item['id']."'>".$item['name']."</option>";
														}
													echo "</select>
												</div>
												<div class='col-2'>
													<input type='number' class='form-control form-control-sm' name='expense_amount[]' placeholder='Amount' min='0' required>
												</div>
												<div class='col-1'>
													<input type='number' class='form-control form-control-sm day-input' name='expense_day[]' placeholder='Days' min='0' required>
												</div>
												<div class='col-2'>
													<input type='number' class='form-control form-control-sm expense-est-input' name='total_amount[]' placeholder='Total Amount' min='0' required>
												</div>
												<div class='col-4'>
													<input type='text' class='form-control form-control-sm note-input' name='expense_note[]' placeholder='Note' required>
												</div>
												<div class='col-1'>
													<button type='button' class='btn btn-sm btn-outline-danger btn-icon-only remove-expense-est' style='display:none;'>
														<i class='fas fa-trash'></i>
													</button>
												</div>
											</div>
										</div>
									</div>
									<div class='d-flex justify-content-between align-items-center mt-3 pt-3 border-top'>
										<button type='button' class='btn btn-sm btn-outline-primary' id='add-expense-est'>
											<i class='fas fa-plus me-1'></i> Add Item
										</button>
										<div class='text-muted'>
											<small>Total: Rp <span id='total-expense-est' class='font-weight-bold'>0</span></small>
										</div>
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
					<input type='hidden' id='id_plan_completed' name='id_plan'>
					
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
							<th class='text-center'>Duration (Days)</th>
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
				select2('Audit', 'id_audit_universe', 'required', get_detail_all_audit_universe(), 'id', 'val');
				form_button('Save Plan', 'Cancel');
			form_close();
	modal_close();
	?>

<script type="text/javascript">
	$(document).on('click', '.btn-edit', function(){
		let id = $(this).data('id');

		$.ajax({
			url: base_url + 'internal/annual_audit_plan/getData',
			type: 'post',
			data: {id:id},
			dataType: 'json',
			success: function(res){
				$('#id_plan').val(res.id);
				$('#start_date').val(res.start_date);
				$('#durasi').val(res.durasi);
				$('#expense').val(res.expense_est);
				$('#objektif').val(res.objektif);
				$('#mEdit').modal('show');
			} 
		})
	})
	$(document).on('click', '.btn-completed', function(){
		let id = $(this).data('id');
		$('#id_plan_completed').val(id);
		$('#mCompleted').modal('show');
	})

	$(document).ready(function() {
		$('.detail-durasi').css('cursor', 'pointer');
		$('.detail-expense').css('cursor', 'pointer');
		$('.cancel-detail').css('cursor', 'pointer');
		
		
		// Add activity row
		$('#add-activity').click(function() {
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
		$('#add-expense-est').click(function() {
			const newRow = $('.expense-est-row:first').clone();
			newRow.find('input').val('');
			newRow.find('.remove-expense-est').show();
			$('#expense-est-container').append(newRow);
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
				total += parseInt($(this).val()) || 0;
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
								<td class='text-center'>${item.duration_day}</td>
							</tr>
						`;
					});
					tbody += `
						<tr>
							<td colspan='2' class='text-right font-weight-bold'>Total Duration</td>
							<td class='text-center font-weight-bold'>${res.reduce((sum, item) => sum + (parseInt(item.duration_day) || 0), 0)} days</td>
						</tr>`;
				} else {
					tbody = `
						<tr>
							<td colspan='3' class='text-center text-muted'>No data available</td>
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

</script>