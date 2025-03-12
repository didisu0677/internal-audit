<?php 
	foreach($grup[0] as $m0) { 
    ?>
		<tr>
			<td><?php echo $m0->aktivitas; ?></td>
			<td style="width:1px; white-space: nowrap;">							
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="section[<?php echo $m0->id; ?>][]"> 
				<?php $section1 = json_decode($m0->id_section, true); 
				$selected = ''; ?>
				<?php foreach ($section as $v) 
				{
					if(in_array($v['id'],$section1)) {
						$selected = 'selected';
					}else{
						$selected = '';
					}

					echo '<option value="'.$v['id'].'" '.$selected.'>'.trim($v['description']) . ' | ' . trim($v['section_name']).'</option>';
				} ?>
			</select>
			</td>
			<td style="width:1px; white-space: nowrap;">							
			<select class="select2 filter infinity" style="width: 350px;" multiple data-width="200" name="risk[<?php echo $m0->id; ?>][]"> 
				<?php $risk1 = json_decode($m0->id_risk, true); 
				$selected = ''; ?>
				<?php foreach ($risk as $v) 
				{
					if(in_array($v['id'],$risk1)) {
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
			