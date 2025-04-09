<?php 
	foreach($grup[0] as $m0) { 
    ?>
		<tr>
			<td style="width:1px; white-space: nowrap;"><?php echo $m0->aktivitas; ?></td>
			<td style="width:1px;">							
			<select class="select2 filter infinity" style="width: 250px;" multiple data-width="200" name="section[<?php echo $m0->id; ?>][]"> 
				<?php ; 
				$selected = ''; ?>

				<?php foreach ($sub as $v) 
				{
					if($v['id_aktivitas'] == $m0->id_aktivitas) {
						$selected = 'selected';
					}else{
						$selected = '';
					}
					echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['sub_aktivitas']).'</option>';
				} ?>
			</select>
			</td>
			<td style="width:1px; white-space: nowrap;">							
			<select class="select2 filter infinity" style="width: 250px;" multiple data-width="200" name="section[<?php echo $m0->id; ?>][]"> 
				<?php ; 
				$selected = ''; ?>

				<?php foreach ($section as $v) 
				{
					if($v['level4'] == $m0->id_department && $v['level5'] == $m0->id_section) {
						$selected = 'selected';
					}else{
						$selected = '';
					}
					echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['section_name']).'</option>';
				} ?>
			</select>
			</td>
			<td style="width:1px; white-space: nowrap;">							
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="control[<?php echo $m0->id; ?>][]"> 
				<?php  
				$selected = ''; ?>

				<?php foreach ($int_control as $v) 
				{
					if($v['id_aktivitas'] == $m0->id) {
						$selected = 'selected';
					}else{
						$selected = '';
					}
					echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['internal_control']).'</option>';
				} ?>
			</select>
			</td>

			<td style="width:1px; white-space: nowrap;">							
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="control[<?php echo $m0->id; ?>][]"> 
				<?php  
				$selected = ''; ?>

				<?php foreach ($risk as $v) 
				{
					if($v['id_aktivitas'] == $m0->id) {
						$selected = 'selected';
					}else{
						$selected = '';
					}
					echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['risk']).'</option>';
				} ?>
			</select>
			</td>
		</tr>		


<?php } ?>
			