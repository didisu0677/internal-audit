<?php 
$no = 0;
foreach($finding as $v) {
    $no++
    ?>
    <tr>
        <td style="background-color: #2E2E2E; color: white;"><?php echo $no; ?></td>
        <td style="background-color: #2E2E2E; color: white;" colspan="7"><b> <span style="color: red;"><?php echo 'Finding : ' . $v->bobot_finding; ?></span> <?php echo 'Periode Audit. : ' . $v->periode_audit . ' | Department : ' . $v->department .' | Auditor : ' . $v->nama_auditor; ?>  </b></td>
        <td style="background-color: #2E2E2E; color: white;" class="button">
            <button type="button" class="btn btn-info btn-detail" data-key="view" data-id="<?php echo $v->id; ?>" title="<?php echo lang('detail_finding'); ?>"><i class="fa-search"></i></button>
 		</td>    
    </tr>
    <tr>
        <td style="background-color: #a8faf8; " ></td>
        <td style="background-color: #a8faf8; " colspan ="7"><?php echo $v->finding ; ?> </td>
        <td style="background-color: #a8faf8; "></td>
    </tr>
  <?php foreach($capa as $u) {
  if($u->id_finding == $v->id) { ?>
    <tr>
        <td class="sub-1">Capa</td>
        <td class="sub-1"><?php echo $u->isi_capa; ?></td>
        <td><?php echo $u->pic; ?></td>
        <td><?php echo date_indo($u->dateline_capa); ?></td>
        <td></td>
        <td><?php echo $u->status_capa ; ?></td>
        <td></td>
        <td></td>
        <td class="button">
            <button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $u->id; ?>" title="<?php echo lang('update_capa'); ?>"><i class="fa-edit"></i></button>
            <button type="button" class="btn btn-danger btn-send-reminder" data-key="send" data-id="<?php echo $u->id; ?>" title="<?php echo lang('send_reminder'); ?>"><i class="fa-envelope"></i></button>
		</td>    
    </tr>
    <?php } ?>
<?php } ?>
<?php } ?>