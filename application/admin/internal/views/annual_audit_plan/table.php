<?php 

	foreach($grup as $m0) { ?>
		<tr>
			<td ><?php echo $m0->location; ?></td>
			<td ><?php echo $m0->divisi; ?></td>
			<td ><?php echo $m0->department; ?></td>
			<td ><?php echo $m0->section; ?></td>
			<td ><?php echo $m0->aktivitas; ?></td>
			<td ><?php echo $m0->sub_aktivitas; ?></td>
			<td ><?php echo $m0->risk; ?></td>
			<td ><?php echo $m0->keterangan; ?></td>
			<td ><?php echo $m0->bobot; ?></td>
			<td ><?php echo $m0->internal_control; ?></td>
			<td ><?php echo $m0->location_control; ?></td>
		</tr>
	<?php } ?>
			