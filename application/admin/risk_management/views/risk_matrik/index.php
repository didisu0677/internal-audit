<div class="content-header page-data">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">   		
    		<?php 
			echo '<button class="btn btn-success btn-save" href="javascript:;" ><i class="fa-save"></i> Save</button>';

			// $arr = [];
			// $arr = [
			// 	['btn-save','Save Data','fa-save'],
			// 	// ['btn-export','Export Data','fa-upload'],
			// 	// ['btn-import','Import Data','fa-download' ],
			// 	// ['btn-template','Template Import','fa-reg-file-alt']
			// ];
		
		
			// echo access_button('',$arr); 

			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body mt-6">

	<div class="main-container mt-2">
		<div class="row">

			<div class="col-sm-12">

				<div class="card">
				<form id="form-control" action="<?php echo base_url('risk_management/risk_matrik/save_perubahan'); ?>" data-callback="reload" method="post" data-submit="ajax">

	    			<div class="card-body">
	    				<div class="table-responsive tab-pane fade active show height-window">
	    				<?php
						table_open('table table-bordered table-app table-hover table-1');
							thead();
								tr();
									th(lang('aktivitas'),'','class="text-center align-middle headcol"');
									th(lang('sub_aktivitas'),'','class="text-center align-middle headcol"');
									th(lang('audit_section'),'','class="text-center align-middle headcol"');
									th(lang('internal_control'),'','class="text-center align-middle headcol"');
									th(lang('risk'),'','class="text-center align-middle headcol"');
									
							tbody();
						table_close();
						?>
	    				</div>
	    			</div>
					
				</form>
	    		</div>
	    	</div>
	    </div>
	</div>

	
	<div class="overlay-wrap hidden">
		<div class="overlay-shadow"></div>
		<div class="overlay-content">
			<div class="spinner"></div>
			<p class="text-center">Please wait ... </p>
		</div>
	</div>
	
</div>
<?php
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('transaction/usulan_besaran/import_core'),'post','form-import');
			col_init(3,9);

			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script type="text/javascript">


$(document).ready(function () {
	getData();
});	

function getData() {
	// cLoader.open(lang.memuat_data + '...');
	$('.overlay-wrap').removeClass('hidden');
	var page = base_url + 'risk_management/risk_matrik/data';
		page 	+= '/'+$('#filter_tahun').val();


	$.ajax({
		url 	: page,
		data 	: {},
		type	: 'get',
		dataType: 'json',
		success	: function(response) {
			$('.table-1 tbody').html(response.table);
			$('#parent_id').html(response.option);
			cLoader.close();
			cek_autocode();
			fixedTable();
			var item_act	= {};
			if($('.table-1 tbody .btn-input').length > 0) {
				item_act['edit'] 		= {name : lang.realisasi, icon : "edit"};					
			}

			var act_count = 0;
			for (var c in item_act) {
				act_count = act_count + 1;
			}
			if(act_count > 0) {
				$.contextMenu({
					selector: '.table-1 tbody tr', 
					callback: function(key, options) {
						if($(this).find('[data-key="'+key+'"]').length > 0) {
							if(typeof $(this).find('[data-key="'+key+'"]').attr('href') != 'undefined') {
								window.location = $(this).find('[data-key="'+key+'"]').attr('href');
							} else {
								$(this).find('[data-key="'+key+'"]').trigger('click');
							}
						} 
					},
					items: item_act
				});
			}
			$('.overlay-wrap').addClass('hidden')
			$('.select2').select2()
			$('.select2-search__field').attr('style', 'width:100%')
		}
	});
}

$(function(){
	getData();
});

$(document).on('click','.btn-save',function(){
	$('#form-control').submit();
});

</script>