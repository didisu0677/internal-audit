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
			<div class="card-body m-3">
				<div class="table-responsive tab-pane fade active show height-window" id="result">
					<table class="table table-bordered table-hover" id="example">
						<thead class="text-center">
							<tr>
								<th width="10%">Department</th>
								<th width="10%">Aktivitas</th>
								<th width="10%">Audit Area</th>
								<th width="25%">Resiko</th>
								<th>Objektif</th>
								<th width="5%">Durasi</th>
								<th width="5%">Start Date Est</th>
								<th width="5%">End Date Est</th>
								<th width="5%">Expense Est</th>
								<th width="5%"></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$currentYear = null;
							foreach($data as $val): 
								$year = date('Y', strtotime($val['start_date']));
								if ($year !== $currentYear): 
									$currentYear = $year; ?>
									<tr>
										<th colspan="10" class="bg-light">
											<a class="d-block w-100" data-toggle="collapse" href="#collapse<?=$currentYear?>">
												<?= $currentYear ?>
											</a>
										</th>
									</tr>
								<?php endif ?>
								<tr class="collapse" id="collapse<?=$currentYear?>">
			 						<td><?= $val['department'] ?></td>
			 						<td><?= $val['aktivitas'] ?></td>
			 						<td><?= $val['sub_aktivitas'] ?></td>
			 						<td>
										<?php foreach($val['risk'] as $risk) : ?>
			 								<p class="bg-light" style="border-radius: 10px; padding:10px"><?=$risk['risk']?></p>
										<?php endforeach ?>
 									</td>
									<td><?=$val['objektif']?></td>
									<td><?=$val['durasi'] ? $val['durasi'].' Hari' : '' ?> </td>
									<td style="white-space: nowrap;"><?= $val['start_date']?></td>
									<td style="white-space: nowrap;"><?=$val['end_date']?></td>
									<td style="white-space: nowrap;">Rp. <?=number_format($val['expense_est'],0)?></td>
									<td class="text-center"><button class="btn btn-warning btn-icon-only btn-edit" type="button" data-id="<?=$val['id']?>"><i class="fa-edit"></i></button></td>
			 					</tr>
								<?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
	    </div>
	</div>
</div>
	<?php
	modal_open('mEdit', 'Edit');
		modal_body();
			col_init(3,9);
			form_open(base_url('internal/annual_audit_plan/save'), 'post', 'form');
				input('hidden', 'id_plan', 'id_plan');
				input('hidden', 'start_date', 'start_date');
				input('number', 'Durasi', 'durasi', 'required');
				input('number', 'Expense Est', 'expense', 'required');
				textarea('Objektif', 'objektif', 'required');
			form_button('Simpan', 'Batal');
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
</script>