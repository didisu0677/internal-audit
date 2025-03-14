<?php 
	foreach($grup[0] as $m0) { 
    ?>
		<tr>
			<td><?php echo $m0->risk; ?></td>
			<td style="width:1px; white-space: nowrap;">							
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="control[<?php echo $m0->id; ?>][]"> 
				<?php $int_control1 = json_decode($m0->id_internal_control, true); 
				$selected = ''; ?>

				<?php foreach ($int_control as $v) 
				{
					if(in_array($v['id'],$int_control1)) {
						$selected = 'selected';
					}else{
						$selected = '';
					}
					echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['internal_control']).'</option>';
				} ?>
			</select>
			</td>
		</tr>		


<?php } ?>
			