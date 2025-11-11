<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
	th.no-sort::after, th.no-sort::before {
	display: none !important;
	}
	td input.row-check {
		display: block;
		margin: 0 auto;
	}
</style>
</div>

<div class="container-fluid p-3">
    <div class="row mb-4">
		<div class="col-12">
			<div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
				<div class="card-body py-4">
					<div class="row align-items-center">
						<div class="col-md-8">
							<h3 class="mb-1 font-weight-bold text-white"><?php echo $title; ?></h3>
							<div class="text-white-50">Centralized list of all auditable units</div>
						</div>
						<div class="col-md-4 text-md-right">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="content-body p-2">
    <div class="mb-2 text-right">
        <button class="btn btn-primary btn-sm" id="bulkSetInitial"><i class="fas fa-calendar-plus"></i> Set Initial Audit (Bulk)</button>
    </div>
	<table id="example" class="table table-bordered table-hover table-striped">
			<thead>
				<tr>
					<th class="text-center"><input type="checkbox" id="selectAll"></th>
					<th>Lokasi</th>
					<th>Divisi</th>
					<th>Department</th>
					<th>Section</th>
					<th>Aktivitas</th>
					<th>Audit Area</th>
					<th>Resiko</th>
					<th>Bobot</th>
					<th>Status Audit</th>
					<th>Initial Audit</th>
					<th>Last Audit</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($data as $row) :
					if(!empty($row['risk'])) :  
						foreach($row['risk'] as $v) :?>
						<tr>
							<td>
								<input type="checkbox" class="row-check" data-id="<?=$v['id']?>" data-universe="<?=$row['id']?>" <?php if (!empty($row['initial_audit'])) echo 'disabled'; ?>>
							</td>
							<td><?= $row['description']?></td>
							<td><?= $row['divisi']?></td>
							<td><?= $row['department']?></td>
							<td><?= $row['section_name']?></td>
							<td><?= $row['aktivitas']?></td>
							<td><?= $row['sub_aktivitas']?></td>
							<td><?= $v['risk']?></td>
							<td class="text-center"><?= $v['bobot']?></td>
							<td><?= $v['description']?></td>
							<td><?= $row['initial_audit']?></td>
							<td><?= $row['last_audit']?></td>
							<td><?php if (empty($row['initial_audit'])): ?>
								<button class="btn btn-warning btn-icon-only btn-sm" id="edit" data-id="<?=$v['id']?>" data-universe="<?=$row['id']?>"><i class="fas fa-pencil"></i></button>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; endif ?>
				<?php endforeach; ?>
			</tbody>
	</table>
</div>
<?php
modal_open('mEdit');
	modal_body();
		form_open(base_url('internal/audit_universe/set_initial_audit'),'post','form', 'data-callback = "reload_page"');
			col_init(3,9);
			input('hidden', 'id_universe', 'id_universe');
			input('hidden','id_risk','id_risk');
			input('date', 'Initial Audit', 'initial_audit', 'required');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();

?>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
$('#example').DataTable({
  columnDefs: [
    { orderable: false, className:'no-sort', targets: [0] } 
  ]
});

// Master checkbox select all
$('#selectAll').on('change', function(){
	const checked = $(this).is(':checked');
	$('#example tbody .row-check:enabled').prop('checked', checked);
});

// Bulk button
$('#bulkSetInitial').on('click', function(){
	let id_risk = [];
	let id_universe = [];

	$('#example tbody .row-check:checked').each(function(){
		id_risk.push($(this).data('id'));
		id_universe.push($(this).data('universe'));
	});

	if(id_risk.length === 0 || id_universe.length === 0){
		cAlert.open('Select at least one item to proceed.', 'warning');
		return;
	}
	$('#id_risk').val(id_risk);
	$('#id_universe').val(id_universe);
	$('#mEdit').modal('show');
});

$(document).on('click','#edit',  function(){
	let id_risk = $(this).data('id');
	let id_universe = $(this).data('universe');

	$('#id_risk').val(id_risk);
	$('#id_universe').val(id_universe);
	
	$('#mEdit').modal('show');
});

function reload_page(){
	location.reload();
}
</script>