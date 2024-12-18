<div class="card mb-2">
    <div class="card-header"><?php echo lang('informasi_finding'); ?></div>
    <div class="card-body p-1">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-app table-detail table-normal">
            <tr>
                <th><?php echo lang('finding'); ?></th>
                <td colspan=""><?php echo $finding ;?></td>
            </tr>
            <tr>
                <th><?php echo lang('nama_auditor'); ?></th>
                <td colspan=""><?php echo $nama_auditor ;?></td>
            </tr>

            <tr>
                <th><?php echo lang('auditee'); ?></th>
                <td colspan=""><?php echo $nama_auditee ;?></td>
            </tr>

            <tr>
                <th><?php echo lang('department'); ?></th>
                <td colspan=""><?php echo $department ;?></td>
            </tr>


            <tr>
                <th><?php echo lang('site_auditee'); ?></th>
                <td colspan=""><?php echo $site_auditee ;?></td>
            </tr>

            <tr>
                <th><?php echo lang('tgl_mulai_audit'); ?></th>
                <td colspan=""><?php echo date_indo($tgl_mulai_audit) ;?></td>
            </tr>

            <tr>
                <th><?php echo lang('tgl_akhir_audit'); ?></th>
                <td colspan=""><?php echo date_indo($tgl_akhir_audit) ;?></td>
            </tr>

            <tr>
                <th><?php echo lang('bobot_finding'); ?></th>
                <td colspan=""><?php echo $bobot_finding ;?></td>
            </tr>

            <tr>
                <th><?php echo lang('file_dokumen'); ?></th>
                <td colspan=""></td>
            </tr>
   
        </div>
    </div>
</div>
