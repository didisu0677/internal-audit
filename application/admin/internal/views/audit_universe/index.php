<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<div class="content-header page-data">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="content-body m-3">
	<table id="example" class="table table-bordered table-hover table-stripped">
			<thead>
				<tr>
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
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($data as $row) :?>
					<?php foreach($row['risk'] as $v) : ?>
					<tr>
						<td><?= $row['description']?></td>
						<td><?= $row['divisi']?></td>
						<td><?= $row['department']?></td>
						<td><?= $row['section_name']?></td>
						<td><?= $row['aktivitas']?></td>
						<td><?= $row['sub_aktivitas']?></td>
						<td><?= $v['risk']?></td>
						<td><?= $v['bobot']?></td>
						<td><?= $v['description']?></td>
						<td><?= $row['initial_audit']?></td>
						<td><?php if (empty($row['initial_audit'])): ?>
							<button class="btn btn-warning btn-icon-only btn-sm" id="edit" data-id="<?=$v['id']?>" data-universe="<?=$row['id']?>"><i class="fas fa-pencil"></i></button>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
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
new DataTable('#example');

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