<div class="content-header">
    <div class="main-container position-relative">
        <div class="header-info">
            <div class="content-title"><?php echo $title; ?></div>
            <?php echo breadcrumb(); ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div class="content-body m-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="display-5">LEMBAR IMPROVEMENT AUDITOR</h2>
                <hr class="w-50">
            </div>
        </div>
        <?php form_open(base_url('internal/kuisioner/save'), 'post','form', 'data-submit="ajax" data-callback="back_to_kuisioner"'); ?>
        <div class="row">
            <div class="col-12">
                <?php
                input('hidden','nomor','nomor','',$nomor);
                $labels = [
                    '1' => 'Sangat tidak setuju',
                    '2' => 'Tidak setuju',
                    '3' => 'Setuju',
                    '4' => 'Sangat setuju'
                ];
                foreach ($question as $i => $val):
                    $no = $i + 1;
                    $name = 'question' . $no;
                ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <?= $no . '. ' . $val['question']; ?>
                        </div>
                        <div class="card-body">
                            <?php foreach ($labels as $k => $v):
                                $input_id = $name . '_' . $k; 
                            ?>
                                <div class="form-group">
                                    <input type="radio" name="<?= $name ?>" value="<?= $k ?>" id="<?= $input_id ?>">
                                    <label for="<?= $input_id ?>" class="ml-3"><?= $v ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach;?>
                <label for="komentar">Komentar Tambahan</label>
                <textarea name="komentar" id="komentar" class="form-control mb-3" rows="10"></textarea>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            
        </div>

    </div>
</div>

<script>
    function back_to_kuisioner(){
        location.href = base_url + 'internal/kuisioner';
    }
</script>