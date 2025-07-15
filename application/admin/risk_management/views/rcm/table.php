<?php 
	foreach($grup as $m0) { 
    ?>
		<tr>
			<td ><?php echo $m0->location; ?></td>
			<td ><?php echo $m0->divisi; ?></td>
			<td ><?php echo $m0->department; ?></td>
			<td ><?php echo $m0->section; ?></td>
			<td ><?php echo $m0->aktivitas; ?></td>

			<td style="width:1px; white-space: nowrap;"><?php echo $m0->sub_aktivitas_name; ?></td>
			<td>
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="risk[<?php echo $m0->id; ?>][]" disabled> 

				<?php foreach ($risk[$m0->id_rk] as $v) 
				{
					$selected = 'selected';
					echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['risk']).'</option>';
				} ?>
			</select>
			</td>
			<td>
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="control[<?php echo $m0->id; ?>][]" disabled> 
				<?php  

				foreach($int_control[$m0->id] as $v1) {
					$selected = 'selected';
					echo '<option value="'.$v1['id'].'" '.$selected.'>'.$v1['internal_control'].'</option>';
				}
				//} ?>
			</select>
			</td>
									

			<td style="width:1px; white-space: nowrap;">
				<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="dampak[<?php echo $m0->id; ?>][]" disabled> 
					<?php  
					$selected = ''; ?>

					<?php foreach ($risk[$m0->id_rk] as $v) 
					{
						$selected = 'selected';
						echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['keterangan']).'</option>';
					} ?>
				</select>
			</td>

			<td class="button">
				<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m0->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
				<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m0->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
			</td>
		</tr>		

<?php } ?>
			