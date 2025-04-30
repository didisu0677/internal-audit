<?php 

	foreach($grup as $m0) { 
		$no = 1;
		$no1 = 1;
    ?>


		<tr>
			<td ><?php echo $m0->location; ?></td>
			<td ><?php echo $m0->divisi; ?></td>
			<td ><?php echo $m0->department; ?></td>
			<td ><?php echo $m0->section; ?></td>
			<td ><?php echo $m0->aktivitas; ?></td>

			<td style="width:1px; white-space: nowrap;"><?php echo $m0->sub_aktivitas; ?></td>
			
			<?php
			$int = '';
			foreach($int_control[$m0->id_aktivitas][$m0->id_sub_aktivitas] as $v1 => $v2) {
				if($int == '') {
					$int = $v2['internal_control'];
				}else{
					$int =  $int . " , " . $v2['internal_control'];
				}
				$no1++;

			}

			foreach($risk[$m0->id][$m0->id_rk] as $v) { 
			
						
			if($no==1) { ?>
				<td><?php echo $v['risk'];?></td>
				<td><?php echo $v['keterangan'];?></td>
				<td><?php echo $v['bobot'];?></td>
				<td><?php echo $int;?></td>
			<?php 

			} 

			
			?>



		</tr>		

			<?php if($no > 1) { ?>
				<tr>
					<td colspan="5"><td>
					<td><?php echo $v['risk'];?></td>
					<td><?php echo $v['keterangan'];?></td>
					<td><?php echo $v['bobot'];?></td>
					<td><?php echo $int;?></td>
				</tr>
			<?php } ?>


		
	
		<?php 
		$no++;
		}?>
		</tr>


<?php } 


?>
			