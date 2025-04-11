<?php 
	foreach($grup[0] as $m0) { 
    ?>
		<tr>
			<td ><?php echo $m0->location; ?></td>
			<td ><?php echo $m0->divisi; ?></td>
			<td ><?php echo $m0->department; ?></td>
			<td ><?php echo $m0->section; ?></td>
			<td ><?php echo $m0->aktivitas; ?></td>

			<td style="width:1px; white-space: nowrap;"></td>
			<td style="width:1px; white-space: nowrap;">							
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="control[<?php echo $m0->id; ?>][]" disabled> 
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
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="control[<?php echo $m0->id; ?>][]" disabled> 
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
			<td style="width:1px; white-space: nowrap;">
				<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="dampak[<?php echo $m0->id; ?>][]" disabled> 
					<?php  
					$selected = ''; ?>

					<?php foreach ($dampak as $v) 
					{
						if($v['id_aktivitas'] == $m0->id) {
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['dampak']).'</option>';
					} ?>
				</select>
			</td>
			<td style="width:1px; white-space: nowrap;"></td>
			<td style="width:1px; white-space: nowrap;">
				<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="kemungkinan[<?php echo $m0->id; ?>][]" disabled> 
					<?php  
					$selected = ''; ?>

					<?php foreach ($kemungkinan as $v) 
					{
						if($v['id_aktivitas'] == $m0->id) {
							$selected = 'selected';
						}else{
							$selected = '';
						}
						echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['kemungkinan']).'</option>';
					} ?>
				</select>
			</td>
			<td style="width:1px; white-space: nowrap;"></td>
			<td style="width:1px; white-space: nowrap;"></td>
			<td style="width:1px; white-space: nowrap;"></td>
		</tr>		
		<?php
		if(count($det[$m0->id])) {
			foreach($det[$m0->id] as $m1) { ?>
				<tr>
				<td ><?php echo $m1->location; ?></td>
				<td ><?php echo $m1->divisi; ?></td>
				<td ><?php echo $m1->department; ?></td>
				<td ><?php echo $m1->section; ?></td>
				<td style="width:1px; white-space: nowrap;"><?php echo $m1->aktivitas; ?></td>
				<td style="width:1px; white-space: nowrap;"><?php echo $m1->sub_aktivitas; ?></td>

				<td style="width:1px; white-space: nowrap;">							
					<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="control[<?php echo $m0->id; ?>][]" disabled> 
						<?php  
						$selected = ''; ?>

						<?php foreach ($int_control as $v) 
						{
							if($v['id_aktivitas'] == $m1->id) {
								$selected = 'selected';
							}else{
								$selected = '';
							}
							echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['internal_control']).'</option>';
						} ?>
					</select>
				</td>

				<td style="width:1px; white-space: nowrap;">							
					<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="risk[<?php echo $m0->id; ?>][]" disabled> 
						<?php  
						$selected = ''; ?>

						<?php foreach ($risk as $v) 
						{
							if($v['id_aktivitas'] == $m1->id) {
								$selected = 'selected';
							}else{
								$selected = '';
							}
							echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['risk']).'</option>';
						} ?>
					</select>
				</td>
				<td style="width:1px; white-space: nowrap;">
					<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="dampak[<?php echo $m0->id; ?>][]" disabled> 
						<?php  
						$selected = ''; ?>

						<?php foreach ($dampak as $v) 
						{
							if($v['id_aktivitas'] == $m1->id) {
								$selected = 'selected';
							}else{
								$selected = '';
							}
							echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['dampak']).'</option>';
						} ?>
					</select>
				</td>
				<td style="width:1px; white-space: nowrap;"></td>
				<td style="width:1px; white-space: nowrap;">
					<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="kemungkinan[<?php echo $m0->id; ?>][]" disabled> 
						<?php  
						$selected = ''; ?>

						<?php foreach ($kemungkinan as $v) 
						{
							if($v['id_aktivitas'] == $m1->id) {
								$selected = 'selected';
							}else{
								$selected = '';
							}
							echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['kemungkinan']).'</option>';
						} ?>
					</select>
				</td>
				<td style="width:1px; white-space: nowrap;"></td>
				<td style="width:1px; white-space: nowrap;"></td>
				<td style="width:1px; white-space: nowrap;"></td>
				</tr>
			<?php
			}
		}
		?>


<?php } ?>
			