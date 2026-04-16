<div class="content-body mt-6">
	<div class="main-container mt-2">
		<div class="row justify-content-center">
			<div class="col-12 col-xl-11">
				<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
					<div class="card-body py-3">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="mb-0 text-dark font-weight-bold">
								Individual Audit Assignment
							</h3>
							<div class="btn-group" role="group" aria-label="Filter Assignment">
								<?php 
									$activeFilter = isset($filter)?$filter:'active';
									$baseUrl = base_url('internal/audit_assignment');
								?>
								<a href="<?="{$baseUrl}?filter=active"?>" class="btn btn-outline-primary filter-switch <?=($activeFilter=='active'?'active':'')?>" data-filter="active">
									<i class="fas fa-calendar-check mr-1"></i> Active
								</a>
								<a href="<?="{$baseUrl}?filter=history"?>" class="btn btn-outline-primary filter-switch <?=($activeFilter=='history'?'active':'')?>" data-filter="history">
									<i class="fas fa-history mr-1"></i> History
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="card shadow-sm border-0">
					<div class="card-body p-4">
						<div id="result">
							<?php foreach($data as $year => $departments): ?>
								<div class="mb-4">
									<div class="card border-0 shadow-sm">
										<div class="card-header text-white border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);border-radius: 15px;">
											<div class="d-flex justify-content-between align-items-center p-3 rounded">

												<button class="btn btn-link text-white text-decoration-none p-0 font-weight-bold year-toggle text-left"
													type="button"
													data-toggle="collapse"
													data-target="#year-collapse<?=$year?>"
													aria-expanded="false"
													aria-controls="year-collapse<?=$year?>">
													<div class="d-flex align-items-center">
														<i class="fas fa-chevron-down mr-3"></i>
														<div>
															<h4 class="mb-1 font-weight-bold text-white">Audit Assignment <?=$year?></h4>
															<small class="text-white-50">Click to expand assignment details</small>
														</div>
													</div>
												</button>
											</div>
										</div>
											<div class="collapse" id="year-collapse<?=$year?>" data-year="<?=$year?>">
												<div class="card-body p-0">
													<?php foreach($departments as $dept => $deptData): ?>
														<div class="border-bottom department-panel">
															<div class="p-3 bg-light font-weight-bold">
																<button class="btn btn-link text-decoration-none text-dark p-0 font-weight-bold dept-toggle flex-grow-1 text-left" 
																			type="button" 
																			data-toggle="collapse" 
																			data-target="#dept-collapse<?=$year?>-<?=md5($dept)?>" 
																			aria-expanded="false" 
																			aria-controls="dept-collapse<?=$year?>-<?=md5($dept)?>">
																		<div class="d-flex justify-content-between align-items-center">
																			<div>
																				<i class="fas fa-chevron-down me-2 text-info"></i>
																				<span><?= $dept ?></span>
																			</div>
																			<!-- <span class="badge badge-info ml-2 assignment-count">Loading...</span> -->
																		</div>
																</button>
																<?php if($deptData['status'] == 'active'): ?> 
																	<button class="btn btn-success btn-sm float-right mark-completed" type="button" data-id-plangroup="<?=$deptData['id_audit_plan_group']?>">
																		<i class="fas fa-check me-1"></i>
																		Mark Complete
																	</button>
																<?php else :  ?>
																	<div class="btn-group float-right" role="group">
																		<button class="btn btn-danger btn-sm download-report" type="button" data-id-plangroup="<?=$deptData['id_audit_plan_group']?>">
																			<i class="fas fa-file-excel me-1"></i>
																			Excel
																		</button>
																		<button class="btn btn-primary btn-sm download-report-docx" type="button" data-id-plangroup="<?=$deptData['id_audit_plan_group']?>" style="margin-left:6px;">
																			<i class="fas fa-file-word me-1"></i>
																			DOCX
																		</button>
																	</div>
																<?php endif?>
															</div>
																<div class="collapse" id="dept-collapse<?=$year?>-<?=md5($dept)?>">
																	<div class="table-responsive department-focus-target" style="overflow-y: auto;">
																		<table class="table table-hover table-bordered mb-0">
																			<thead class="bg-light text-center sticky-top">
																				<tr>
																					<th class="border-0 text-muted small bg-light">Section</th>
																					<th class="border-0 text-muted small bg-light">Aktivitas</th>
																					<th class="border-0 text-muted small bg-light">Audit Area</th>
																					<th class="border-0 text-muted small bg-light">Risk</th>
																					<th class="border-0 text-muted small bg-light">Internal Control</th>
																					<th class="border-0 text-muted small bg-light">Kriteria</th>
																					<th class="border-0 text-muted small bg-light">Pengujian</th>
																					<th class="border-0 text-muted small bg-light">Hasil Review</th>
																					<th class="border-0 text-muted small bg-light">Finding</th>
																					<th class="border-0 text-muted small bg-light">Bobot Finding</th>
																					<th class="border-0 text-muted small bg-light">Unconfirmity</th>
																					<th class="border-0 text-muted small bg-light">Dampak</th>
																					<th class="border-0 text-muted small bg-light">Root Cause</th>
																					<th class="border-0 text-muted small bg-light">Recommendation</th>
																					<th class="border-0 text-muted small bg-light">Finding Control Status</th>
																					<th class="border-0 text-muted small bg-light">Attachment</th>
																				</tr>
																			</thead>
																			<tbody class="department-data" data-group-id="<?=$deptData['id_audit_plan_group']?>">
																				<tr>
																					<td colspan="20" class="text-center py-4">
																						<div class="spinner-border spinner-border-sm text-primary mr-2" role="status">
																							<span class="sr-only">Loading...</span>
																						</div>
																						Loading audit assignments...
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														<?php endforeach; ?>
													</div>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	/* ── Department panel (normal/in-DOM state) ─── */
	.department-panel {
		position: relative;
		overflow: visible;
		transition: opacity .28s ease;
	}
	.department-panel.is-focus-muted { opacity: .45; }

	/* ── Table wrapper (normal state) ─── */
	.department-focus-target {
		scroll-margin-top: 110px;
		max-height: 70vh;
		overflow-y: auto;
		transition: box-shadow .28s ease;
	}
	.department-focus-target.focus-pulse {
		box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.18) inset;
	}
	.department-focus-target table { background: #fff; }

	/* ── Body-level fullscreen overlay (appended to <body> by JS) ─── */
	#dept-fs-overlay {
		display: none;
		position: fixed;  /* direct child of body — guaranteed root stacking context */
		inset: 0;
		z-index: 1060;    /* above navbar (1030), sidebar, everything */
		background: #fff;
		overflow: hidden;
		animation: dept-fs-open .2s ease both;
	}
	#dept-fs-overlay.is-open { display: flex; flex-direction: column; }

	#dept-fs-backdrop {
		display: none;
		position: fixed;
		inset: 0;
		z-index: 1059;    /* just below overlay */
		background: rgba(15, 23, 42, 0.55);
		pointer-events: none; /* never block clicks */
	}
	#dept-fs-backdrop.is-visible { display: block; }

	@keyframes dept-fs-open {
		from { opacity: 0; transform: scale(.98); }
		to   { opacity: 1; transform: scale(1);   }
	}

	/* Panel rendered INSIDE the overlay — reset all position/transform */
	#dept-fs-overlay .department-panel {
		position: static !important;
		display: flex;
		flex-direction: column;
		flex: 1 1 0;
		min-height: 0;
		overflow: hidden;
		width: 100% !important;
		margin: 0 !important;
		transform: none !important;
		border-radius: 0 !important;
		background: #fff;
	}
	#dept-fs-overlay .department-panel > .p-3.bg-light {
		flex-shrink: 0;
		border-radius: 0 !important;
		padding-right: 60px !important;
		border-bottom: 1px solid rgba(0,0,0,.08);
		position: relative;
	}
	#dept-fs-overlay .department-panel > .collapse {
		flex: 1 1 0;
		min-height: 0;
		overflow: hidden;
		display: flex !important;   /* override Bootstrap display:block */
		flex-direction: column;
	}
	#dept-fs-overlay .department-focus-target {
		flex: 1 1 0;
		max-height: none !important;
		overflow-y: auto;
		border-radius: 0;
	}

	/* ── X close button ─── */
	.dept-theater-close {
		position: absolute;
		top: 50%;
		right: 16px;
		transform: translateY(-50%);
		z-index: 10;
		width: 34px;
		height: 34px;
		border-radius: 50%;
		border: 1px solid rgba(0,0,0,.15);
		background: #fff;
		color: #495057;
		font-size: 15px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: background .18s, color .18s, border-color .18s;
		box-shadow: 0 2px 6px rgba(0,0,0,.1);
		padding: 0;
		line-height: 1;
	}
	.dept-theater-close:hover {
		background: #dc3545;
		color: #fff;
		border-color: #dc3545;
	}

	@media (max-width: 767.98px) {
		.department-panel.is-focus-muted { opacity: .8; }
	}
</style>
<?php 
modal_open('modal-form','', 'modal-lg' );
	modal_body();
		// form_open(base_url('internal/audit_assignment/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('hidden','field_name','field_name');
			echo '<div class="form-group row">';
				echo '<div class="col-md-12">';
					echo '<textarea id="field-editor" name="field_value" class="form-control"></textarea>';
				echo '</div>';
			echo '</div>';
			echo '<div class="form-group row">';
				echo '<div class="col-md-12 text-center">';
					echo '<button type="button" class="btn btn-primary mr-2" id="btn_simpan">'.lang('simpan').'</button>';
					echo '<button type="button" class="btn btn-secondary mr-2" id="btn_batal">'.lang('batal').'</button>';
				echo '</div>';
			echo '</div>';
			// form_button(lang('simpan'),lang('batal'));
		// form_close();
	// modal_footer();
modal_close();

modal_open('modal-bobot','Edit Bobot Finding','modal-md');
	modal_body();
		col_init(3,9);
		input('hidden','id','id');
		input('hidden','field_name','field_name');
		input('number', 'Kemungkinan', 'bobot_kemungkinan', 'required','','oninput="if(this.value.length > 0) { this.value = this.value.slice(0,1); if(this.value > 6) this.value = 6; if(this.value < 1) this.value = 1; } previewBobot();"');
		echo '<div class="form-group row"><div class="col-md-9 offset-md-3"><small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Min 1, Maks 6</small></div></div>';
		input('number', 'Dampak', 'bobot_dampak', 'required','','oninput="if(this.value.length > 0) { this.value = this.value.slice(0,1); if(this.value > 6) this.value = 6; if(this.value < 1) this.value = 1; } previewBobot();"');
		echo '<div class="form-group row"><div class="col-md-9 offset-md-3"><small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Min 1, Maks 6</small></div></div>';
		echo '<div class="form-group row" id="bobot-preview-row" style="display:none">';
			echo '<label class="col-md-3 col-form-label">Bobot Finding</label>';
			echo '<div class="col-md-9 d-flex align-items-center">';
				echo '<span id="bobot-preview-badge" class="badge badge-pill badge-secondary px-3 py-2" style="font-size:0.9rem"></span>';
			echo '</div>';
		echo '</div>';
		echo '<div class="form-group row mt-3">';
				echo '<div class="col-md-12 text-center">';
					echo '<button type="button" class="btn btn-primary mr-2" id="btn_simpan">'.lang('simpan').'</button>';
					echo '<button type="button" class="btn btn-secondary mr-2" id="btn_batal">'.lang('batal').'</button>';
				echo '</div>';
			echo '</div>';
modal_close();

modal_open('modal-status-finding','Edit Status Finding','modal-md');
	modal_body();
		col_init(3,9);
		input('hidden','id','id');
		input('hidden','field_name','field_name');
		select2('Status Finding Control', 'status_finding', 'required', get_list_status_finding_control(), 'id', 'description');
		echo '<div class="form-group row">';
				echo '<div class="col-md-12 text-center">';
					echo '<button type="button" class="btn btn-primary mr-2" id="btn_simpan">'.lang('simpan').'</button>';
					echo '<button type="button" class="btn btn-secondary mr-2" id="btn_batal">'.lang('batal').'</button>';
				echo '</div>';
			echo '</div>';
modal_close();


modal_open('modal-kriteria','Edit Kriteria','modal-lg');
	modal_body();
		col_init(3,9);
		input('hidden','id','id');
		input('hidden','field_name','field_name');
		select2('Pilih', 'type', 'required', [
			[
				'key' => 'new',
				'val' => 'Buat Kriteria Baru'
			],[
				'key' => 'exist',
				'val' => 'Pilih Kriteria'
			]
		],'key', 'val');
		echo '<div class="d-none" id="kriteria-box">';
			select2('Kriteria', 'kriteria[]', 'required', get_list_kriteria(), 'id', 'detail' , '','multiple');
		echo '</div>';
		echo '<div class="form-group row">';
				echo '<div class="col-md-12 text-center">';
					echo '<button type="button" class="btn btn-primary mr-2" id="btn_simpan">'.lang('simpan').'</button>';
					echo '<button type="button" class="btn btn-secondary mr-2" id="btn_batal">'.lang('batal').'</button>';
				echo '</div>';
			echo '</div>';
modal_close();

modal_open('modal-attachment','Attachment Management','modal-xl');
	modal_body();
		form_open(base_url('internal/audit_assignment/attach_file'),'post','form-attachment');
		input('hidden','id','id'); 
		echo '<div class="row">
				<div class="col-md-12 text-right">
					<button type="button" class="btn btn-primary btn-icon-only btn-sm mb-3" id="btn-add-attachment"><i class="fas fa-plus"></i></button>
				</div>
			</div>';
		echo '<div class="card mb-4 p-4 row-container-attachments">
			<div class="row">
				<div class="col-md-3">
					<label>Upload File</label>
				</div>
				<div class="col-md-9 mb-3">
					<input type="text" name="original_name[]" class="form-control form-control-sm" placeholder="Optional display name">
				</div>
			</div>';
			fileupload('File','file[]','required','data-accept="xls|xlsx|doc|docx|jpeg|jpg|png|pdf"');
		echo '</div>
		<div id="list-attachments"></div>
		</div>
		';
		form_button(lang('simpan'),lang('batal'));
		form_close();
modal_close();
?>


<script type="text/javascript" src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js') ?>"></script>
<script>
	let planGroupId = null;
	let idSelectedFile = null;
	let idSelectedAssignment = null;
	let currentAttachmentStatus = null;
	let focusPulseTimer = null;
	let activeFocusedGroupId = null;
	let activeFocusedCollapseId = null;

	function setTheaterMode(isActive) {
		$('#dept-fs-backdrop').toggleClass('is-visible', isActive);
	}

	function returnPanelFromOverlay() {
		const $panel = $('#dept-fs-overlay .department-panel');
		if (!$panel.length) return;
		const $ph = $panel.data('dept-placeholder');
		if ($ph && $ph.length) {
			$panel.detach().insertAfter($ph);
			$ph.remove();
			$panel.removeData('dept-placeholder');
		} else {
			$panel.detach(); // fallback — panel lost position, shouldn't happen
		}
		$panel.removeClass('is-focused');
		$panel.find('.dept-theater-close').remove();
		$('#dept-fs-overlay').removeClass('is-open').attr('aria-hidden', 'true').empty();
	}

	function closeActiveDepartmentTheater() {
		// Move panel back to DOM FIRST before collapsing
		returnPanelFromOverlay();

		if (!activeFocusedCollapseId) {
			clearDepartmentFocus();
			return;
		}
		const $activeCollapse = $('#' + activeFocusedCollapseId);
		if ($activeCollapse.length && $activeCollapse.hasClass('show')) {
			$activeCollapse.collapse('hide');
		} else {
			clearDepartmentFocus();
		}
	}

	function clearDepartmentFocus() {
		clearTimeout(focusPulseTimer);
		activeFocusedGroupId = null;
		activeFocusedCollapseId = null;
		setTheaterMode(false);
		// If overlay still open (e.g. called directly), return panel first
		if ($('#dept-fs-overlay').hasClass('is-open')) {
			returnPanelFromOverlay();
		}
		$('.department-panel').removeClass('is-focused is-focus-muted');
		$('.department-focus-target').removeClass('focus-pulse');
		$('.dept-theater-close').remove();
	}

	function openDepartmentFullscreen(groupId) {
		// If already showing this dept, nothing to do
		if (activeFocusedGroupId === groupId && $('#dept-fs-overlay').hasClass('is-open')) return;

		const $tbody = $(`.department-data[data-group-id="${groupId}"]`);
		const $panel = $tbody.closest('.department-panel');
		if (!$tbody.length || !$panel.length) return;

		clearTimeout(focusPulseTimer);
		// Close any previously open overlay first
		if ($('#dept-fs-overlay').hasClass('is-open')) returnPanelFromOverlay();

		// Placeholder keeps the panel's original DOM position for restore
		const $ph = $('<div class="dept-panel-placeholder" style="display:none;height:0;"></div>');
		$panel.before($ph);
		$panel.data('dept-placeholder', $ph);

		// Dim all other panels
		$('.department-panel').not($panel).addClass('is-focus-muted').removeClass('is-focused');

		// Move panel into body-level overlay (detach preserves ALL event handlers)
		$panel.detach().appendTo($('#dept-fs-overlay'));
		$panel.addClass('is-focused');

		// Ensure collapse is open while inside overlay
		const $collapse = $panel.find('.collapse[id^="dept-collapse"]');
		$collapse.addClass('show').css('display', '');

		activeFocusedGroupId = groupId;
		activeFocusedCollapseId = $collapse.attr('id') || null;
		setTheaterMode(true);

		// Show overlay
		$('#dept-fs-overlay').addClass('is-open').removeAttr('aria-hidden');

		// Inject X close button
		$panel.find('.dept-theater-close').remove();
		$panel.find('> .p-3.bg-light').append(
			'<button class="dept-theater-close" title="Tutup (Esc)" aria-label="Tutup"><i class="fas fa-times"></i></button>'
		);

		// Pulse on table wrapper
		const $focusTarget = $panel.find('.department-focus-target').first();
		$focusTarget.addClass('focus-pulse');
		focusPulseTimer = setTimeout(function() {
			$focusTarget.removeClass('focus-pulse');
		}, 1400);
	}

	// AJAX filter switch similar to Annual Audit Plan
	$(document).on('click', '.filter-switch', function(e){
		if(e.ctrlKey || e.metaKey || e.shiftKey) return; // allow open in new tab
		e.preventDefault();
		var url = $(this).attr('href');
		$('.filter-switch').removeClass('active');
		$(this).addClass('active');
		$('#result').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><div class="mt-2 small text-muted">Loading '+$(this).data('filter')+'...</div></div>');
		$.get(url, function(html){
			var temp = $('<div>').html(html);
			var newContent = temp.find('#result').html();
			if(newContent){
				$('#result').html(newContent);
				$(document).trigger('content:rebind');
			}else{
				location.href = url; // fallback
			}
		});
	});

	// Rebinding after AJAX replace
	$(document).on('content:rebind', function(){
		$('.year-toggle .fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
		clearDepartmentFocus();
	});

	$(document).on('click', '.dept-theater-close', function() {
		closeActiveDepartmentTheater();
	});

	$(document).on('keydown', function(e) {
		if (e.key !== 'Escape') return;
		if (!$('#dept-fs-overlay').hasClass('is-open')) return;
		if ($('.modal.show').length) return;
		closeActiveDepartmentTheater();
	});

	$(document).ready(function() {
		// Create body-level fullscreen overlay + backdrop (direct children of <body>
		// to guarantee root stacking context — same pattern as Bootstrap Modal)
		$('body').append('<div id="dept-fs-overlay" aria-hidden="true"></div><div id="dept-fs-backdrop" aria-hidden="true"></div>');

		// Auto expand nearest year collapse on load (current year or closest)
		(function autoOpenNearestYear(){
			var nowYear = (new Date()).getFullYear();
			var nearestBtn = null;
			var nearestDiff = Infinity;
			$('.year-toggle').each(function(){
				var txt = $(this).text();
				var match = txt.match(/(20\d{2})/);
				if(match){
					var y = parseInt(match[1]);
					var diff = Math.abs(y - nowYear);
					if(diff < nearestDiff){
						nearestDiff = diff;
						nearestBtn = $(this);
					}
				}
			});
			if(nearestBtn){
				var target = nearestBtn.data('target');
				$(target).collapse('show');
				nearestBtn.find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
				nearestBtn.attr('aria-expanded','true');
			}
		})();

		// Allow only one year open at a time
		$(document).on('click', '.year-toggle', function(){
			let targetCollapse = $(this).data('target');
			$('.year-toggle').not(this).each(function(){
				let otherTarget = $(this).data('target');
				if(otherTarget !== targetCollapse){
					$(otherTarget).collapse('hide');
					$(this).attr('aria-expanded','false');
					$(this).find('.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
				}
			});
		});

		// Only one department open within same year
		$(document).on('click', '.dept-toggle', function () {
			if (isEditing) return false;

			const $button = $(this);
			const targetCollapse = $button.attr('aria-controls');
			const $collapse = $('#' + targetCollapse);
			const $tbody = $collapse.find('.department-data');
			const groupId = $tbody.data('group-id');
			const $yearCollapse = $button.closest('[id^="year-collapse"]');

			// Pastikan element ditemukan
			if (!groupId) return;

			$yearCollapse.find('.collapse[id^="dept-collapse"]').not($collapse).collapse('hide');

			// Load data hanya kalau belum pernah dimuat atau kosong
			if (!$tbody.data('loaded') && !$tbody.data('loading')) {
				loadDepartmentData(groupId, $tbody);
			}
		});


		// Icon update on collapse show/hide
		$(document).on('shown.bs.collapse', '.collapse', function(){
			let toggle = $(`[data-target="#${this.id}"]`);
			toggle.find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
			toggle.attr('aria-expanded','true');

			// Only activate theater focus for dept collapses, never for year collapses
			// (year collapse contains hidden dept-data in DOM which would falsely trigger theater mode)
			if (!this.id || !this.id.startsWith('dept-collapse')) return;

			const groupId = $(this).find('.department-data').data('group-id');
			if (groupId) {
				openDepartmentFullscreen(groupId);
			}
		});
		$(document).on('hidden.bs.collapse', '.collapse', function(){
			let toggle = $(`[data-target="#${this.id}"]`);
			toggle.find('.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			toggle.attr('aria-expanded','false');

			if ($(this).is('[id^="year-collapse"]')) {
				clearDepartmentFocus();
				return;
			}

			if ($(this).find('.department-data').length && !$('.collapse[id^="dept-collapse"].show').length) {
				clearDepartmentFocus();
			}
		});
		CKEDITOR.replace("field-editor", {
			height: 250,
		    extraPlugins: 'font',
			toolbar: [
				{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'] },
		        { name: 'paragraph',   items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
				{ name: 'insert',      items: ['Table', 'HorizontalRule'] },
				{ name: 'links',       items: ['Link', 'Unlink'] },
				{ name: 'clipboard',   items: ['Undo', 'Redo'] },
				{ name: 'styles',      items: ['Format', 'FontSize'] }
			],
			format_tags: 'p;h4;h5;pre',
    		fontSize_sizes: '10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px'
		});

		let isEditing = false;
		let originalValue = '';
		
		$(document).on('click', '.editable:not(.editable-status)', function() {
			if (isEditing) return;
			if ($(this).closest('tr').hasClass('disabled-row')) return; // jika history, tidak bisa di edit

			const $cell = $(this);
			const field = $cell.data('field');
			const rowId = $cell.closest('tr').data('id');
			originalValue = $cell.html().trim();


			// Set modal data
			$('#modal-form').find('[name="id"]').val(rowId);
			$('#modal-form').find('[name="field_name"]').val(field);
			$('#modal-form .modal-title').text('EDIT ' +  field.replace(/_/g, ' ').toUpperCase());
			
			// Set CKEditor content
			if (CKEDITOR.instances['field-editor']) {
				CKEDITOR.instances['field-editor'].setData(originalValue);
			}
			$('#modal-form').modal('show');
		});

		$(document).on('click', '.bobot, .status-finding, .kriteria', function() {
			if (isEditing) return;
			if ($(this).closest('tr').hasClass('disabled-row')) return; // jika history, tidak bisa di edit

			const $cell = $(this);
			const field = $cell.data('field');
			const rowId = $cell.closest('tr').data('id');

			let modalType;
			if(field == 'bobot_finding'){
				modalType = '#modal-bobot'
			}else if(field == 'status_finding'){
				modalType = '#modal-status-finding';
			}else{
				modalType = '#modal-kriteria';
			}
			// Reset Value ketika modal di show
			$(modalType).find('input, textarea').val('');
			$('#bobot-preview-row').hide();

			// Reset select biasa (tanpa Select2)
			$(modalType).find('select:not(.select2)').val('');

			// Reset semua Select2
			$(modalType).find('select.select2').val(null).trigger('change');

			$(modalType).find('[name="id"]').val(rowId);
			$(modalType).find('[name="field_name"]').val(field);

			// Pre-fill kemungkinan & dampak jika modal bobot
			if(field == 'bobot_finding'){
				const k = $cell.data('kemungkinan');
				const d = $cell.data('dampak');
				if(k){ $(modalType).find('[name="bobot_kemungkinan"]').val(k); }
				if(d){ $(modalType).find('[name="bobot_dampak"]').val(d); }
				if(k && d){ previewBobot(); }
			}

			$(modalType).modal('show');
		});

		$(document).on('click', '#btn_batal', function() {
			let modal = $(this).closest('.modal');
			modal.modal('hide');
		});

		$(document).on('click', '#btn_simpan', async function(e) {
			e.preventDefault();

			const modal = $(this).closest('.modal');
			
			const formData = {
				id: modal.find('[name="id"]').val(),
				field: modal.find('[name="field_name"]').val(),
				value: modal.is('#modal-bobot')
					? (function(){
						const k = modal.find('[name="bobot_kemungkinan"]').val();
						const d = modal.find('[name="bobot_dampak"]').val();
						return JSON.stringify({ kemungkinan: k, dampak: d, score: getScore(k, d) });
					})()
					: modal.find('[name="status_finding"]').length
						? modal.find('[name="status_finding"]').val()
						: modal.find('[name="kriteria[]"]').length
							// ? await getDetailKriteria(modal.find('[name="kriteria[]"]').val())
							? modal.find('[name="kriteria[]"]').val()
							: CKEDITOR.instances['field-editor'].getData()
			};
			if(formData.field == 'kriteria'){
				if(formData.value == null || formData.value.length == 0){
					cAlert.open('Please select at least one Kriteria!', 'info');
					return;
				}
			}

			if(modal.is('#modal-bobot')){
				const parsed = JSON.parse(formData.value);
				if(!parsed.kemungkinan || !parsed.dampak || parsed.score === 'Unknown'){
					cAlert.open('Isi Kemungkinan dan Dampak dengan nilai 1–6!', 'info');
					return;
				}
			}

			if(formData.field == 'status_finding' && (formData.value == null || formData.value == '')){
				cAlert.open('Please select Status Finding Control!', 'info');
				return;
			}

			let response = await $.ajax({
				url: base_url + 'internal/audit_assignment/save',
				type: 'POST',
				dataType: 'json',
				data: formData
			});
			let cellClass = '.editable';
			if (response.status === 'success') {
				if(modal.is('#modal-bobot')){
					cellClass = '.bobot';
					const parsed = JSON.parse(formData.value);
					const bobotName = await get_bobot_name(parsed.score);
					formData.value = renderBobotBadge(parsed.kemungkinan, parsed.dampak, parsed.score, bobotName);
				}else if(modal.is('#modal-status-finding')){
					cellClass = '.status-finding';
					formData.value = await get_status_finding_name(formData.value)
				}else if(modal.is('#modal-kriteria')){
					cellClass = '.kriteria';
					formData.value = await getDetailKriteria(formData.value)
				}
			    const $cell = $(`tr[data-id="${formData.id}"] ${cellClass}[data-field="${formData.field}"]`);
				$cell.html(formData.value);
				modal.modal('hide');
			}
			cAlert.open(response.message, response.status);
		});

			
		// Prevent accordion collapse when editing
		$(document).on('click', '.editable, .editable-status', function(e) {
			if (isEditing) {
				e.stopPropagation();
			}
		});
		
		$(document).on('click', '.attachment', async function() {
			idSelectedAssignment = $(this).data('id');
			let res = await $.ajax({
				url: base_url + 'internal/audit_assignment/get_detail_assignment',
				data: { id: idSelectedAssignment },
				type: 'POST'
			});
			
			currentAttachmentStatus = res.status || null;
			await generateListAttachment(currentAttachmentStatus);
			$('#modal-attachment').find('[name="id"]').val(idSelectedAssignment);
			$('#modal-attachment').modal('show');	

			if(res.status == 'history'){
				$('#btn-add-attachment').hide();
				$('#modal-attachment').find('[type="submit"]').hide();
				$('#modal-attachment').find('[type="reset"]').hide();
				$('.row-container-attachments').hide();
			}else{
				$('#btn-add-attachment').show();
				$('#modal-attachment').find('[type="submit"]').show();
				$('#modal-attachment').find('[type="reset"]').show();
				$('.row-container-attachments').show();
			}	
		});
	});

		$(document).on('click', '.mark-completed', function() {
			planGroupId = $(this).data('id-plangroup');
			if (!planGroupId) return;
			cConfirm.open('Are you sure you want to mark this department as complete?', 'markCompleted');
		});

		$(document).on('click', '.download-report', function() {
			planGroupId = $(this).data('id-plangroup');
			if (!planGroupId) return;
			window.open(base_url + 'internal/audit_assignment/download_report?id_audit_plan_group=' + planGroupId, '_blank');
		});

		$(document).on('click', '.download-report-docx', function() {
			planGroupId = $(this).data('id-plangroup');
			if (!planGroupId) return;
			window.open(base_url + 'internal/audit_assignment/download_report_docx?id_audit_plan_group=' + planGroupId, '_blank');
		});

		$(document).on('click', '#download-file', function() {
			const fileId = $(this).data('id-file');
			if (!fileId) return;
			window.open(base_url + 'internal/audit_assignment/download_file?id=' + fileId, '_blank');
		});

		$(document).on('click', '.delete-file', function() {
			let id = $(this).data('id-file');
			idSelectedFile = id;
			cConfirm.open('Are you sure you want to delete this file?', 'deleteFile');
		});

		$(document).on('change', '#type', function(){
			let val = $(this).val();
			$('#kriteria').val('');
			if(val == 'exist'){
				$('#kriteria-box').removeClass('d-none');
			}else if(val == 'new'){
				$('#kriteria-box').addClass('d-none');
				window.open(base_url + 'settings/m_kriteria', '_blank');
			}
		});

		async function getDetailKriteria(data){
			let res = await $.ajax({
				url: base_url + 'internal/audit_assignment/get_kriteria_string',
				type: 'POST',
				data: {data:data}
			});
			return res;
		}

		function deleteFile() {
			$.ajax({
				url: base_url + 'internal/audit_assignment/delete_file',
				type: 'POST',
				dataType: 'json',
				data: { id: idSelectedFile },
				success: function(res) {
					if (res.status === 'success') {
						cAlert.open(res.message, 'success', 'generateListAttachment');
					} else {
						cAlert.open(res.message, 'error');
					}
				},
				error: function() {
					cAlert.open('An error occurred while processing your request.', 'error');
				}
			});
		}

		function reload_page() {
			location.reload();
		}
		
		function markCompleted() {
			$.ajax({
				url: '<?= base_url('internal/audit_assignment/mark_completed') ?>',
				type: 'POST',
				dataType: 'json',
				data: { id_audit_plan_group: planGroupId },
				success: function(res) {
					if (res.status === 'success') {
						cAlert.open(res.message, 'success', 'reload_page');
					} else {
						cAlert.open(res.message, res.status);
					}
				},
				error: function() {
					cAlert.open('An error occurred while processing your request.', 'error');
				}
			});
		}
		function generateListAttachment(status){
			rowId = idSelectedAssignment;
			const isHistory = (typeof status !== 'undefined' && status !== null) ? (status === 'history') : (currentAttachmentStatus === 'history');
			$.ajax({
				url: base_url + 'internal/audit_assignment/get_attachments',
				type: 'POST',
				dataType: 'json',
				data: { id: rowId },
				success: function(res) {
					$('#list-attachments').html('');

					let html = `<div class="form-group row">
						<div class="col-md-12">
							<label class="font-weight-bold">Daftar File Terunggah</label>
							<div class="table-responsive border rounded" style="max-height:360px;overflow:auto">
								<table class="table table-sm table-hover mb-0">
									<thead class="thead-light">
										<tr>
											<th style="width:45%">Nama File</th>
											<th style="width:45%">Tanggal</th>
											<th class="text-center">Aksi</th>
										</tr>
									</thead>
									<tbody>`;
									$.each(res, function(index, file) {
										html += `<tr data-id-assignment="${file.id_assignment}">
											<td class="align-middle">${file.filename}</td>
											<td class="align-middle">${file.created_at}</td>
											<td class="align-middle text-center" width="1px">
												<button type="button" class="btn btn-sm btn-primary btn-icon-only mr-2" id="download-file" title="Download File" data-id-file="${file.id_file}">
													<i class="fas fa-download"></i>
												</button>
												${isHistory ? '' : `<button type="button" class="btn btn-sm btn-danger btn-icon-only delete-file" title="Delete File" data-id-file="${file.id_file}">
													<i class="fas fa-trash"></i>
												</button>`}
											</td>`	
										})
									html += `</tbody>
								</table>
							</div>
						</div>
					</div>`;
					$('#list-attachments').html(html);
				}
			});
		}
		
		function loadDepartmentData(groupId) {
			const tbody = $(`.department-data[data-group-id="${groupId}"]`);
			const badge = tbody.closest('.card').find('.assignment-count');

			if (!tbody.length || tbody.data('loading')) return;
			tbody.data('loading', true);
			
			// Show loading state
			badge.removeClass('badge-info').addClass('badge-secondary');
			badge.html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...');
			
			$.ajax({
				url: '<?= base_url('internal/audit_assignment/data') ?>',
				data: { id: groupId },
				type: 'POST',
				dataType: 'json',
				success: function(res){
					let html = '';
					if (res.length === 0) {
						html = `<tr>
							<td colspan="19" class="text-center py-4 text-muted">
								<i class="fas fa-inbox mr-2"></i>
								No audit assignments found for this section
							</td>
						</tr>`;
					} else {
						res.forEach(function(item, index) {
						    const disableClass = item.status === 'history' ? ' disabled-row' : '';

							html += `<tr data-id="${item.id}" class="${disableClass}">
								<td class="align-middle text-center" style="width: 120px; min-width: 120px;" data-field="section_name">${item.section || ''}</td>
								<td class="align-middle text-center" style="width: 200px; min-width: 200px;" data-field="aktivitas">${item.aktivitas || ''}</td>
								<td class="align-middle text-center" style="width: 150px; min-width: 150px;" data-field="audit_area">${item.sub_aktivitas || ''}</td>
								<td class="align-middle text-center" style="width: 200px; min-width: 200px;" data-field="risk">
									`
									item.risk.forEach(function(risk) {
										html += `<div class="mb-3 bg-light p-2 rounded">
											${risk.risk}
										</div>`;
									});
								html += `
								</td>

								<td class="align-middle text-center" style="width: 180px; min-width: 180px;" data-field="internal_control">
									`
									item.internal_control.forEach(function(ic) {
										html += `<div class="mb-3 bg-light p-2 rounded">
											${ic.internal_control}
										</div>`;
									});
								html += `</td>
								<td class="align-middle kriteria ${disableClass}" style="width: 300px; min-width: 180px;" data-field="kriteria">${item.kriteria || ''}</td>
								<td class="align-middle editable ${disableClass}" style="width: 600px; min-width: 350px;" data-field="pengujian">${item.pengujian || ''}</td>
								<td class="align-middle editable ${disableClass}" style="width: 600px; min-width: 350px;" data-field="hasil_review">${item.hasil_review || ''}</td>
								<td class="align-middle editable ${disableClass}" style="width: 600px; min-width: 350px;" data-field="finding">${item.finding || ''}</td>
								<td class="align-middle bobot ${disableClass}" style="width: 300px; min-width: 180px;" data-field="bobot_finding" data-kemungkinan="${item.bobot_kemungkinan || ''}" data-dampak="${item.bobot_dampak || ''}">${
								item.bobot_finding
									? renderBobotBadge(item.bobot_kemungkinan || '', item.bobot_dampak || '', getScore(item.bobot_kemungkinan, item.bobot_dampak))
									: ''
							}</td>
								<td class="align-middle editable ${disableClass}" style="width: 600px; min-width: 350px;" data-field="unconfirmity">${item.unconfirmity || ''}</td>
								<td class="align-middle editable ${disableClass}" style="width: 600px; min-width: 350px;" data-field="dampak">${item.dampak || ''}</td>
								<td class="align-middle editable ${disableClass}" style="width: 600px; min-width: 350px;" data-field="root_cause">${item.root_cause || ''}</td>
								<td class="align-middle editable ${disableClass}" style="width: 600px; min-width: 350px;" data-field="recomendation">${item.recomendation || ''}</td>
								<td class="align-middle status-finding ${disableClass}" style="width: 300px; min-width: 180px;" data-field="status_finding">${item.status_finding || ''}</td>
								<td class="align-middle attachment" style="width: 150px; min-width: 150px;" data-id="${item.id}">
									${item.filename ? `<span class='text-primary'>Lihat Data</span>` : `<span class="text-muted">No file</span>`}
								</td>
							</tr>`;
						});
					}
					tbody.html(html);
					tbody.data('loaded', true);

					// Init tooltips for kriteria descriptions
					tbody.find('[data-description]').each(function(){
						$(this).attr('title', $(this).data('description'))
							   .tooltip({ placement: 'top', trigger: 'hover', container: 'body' });
					});
				
					if (activeFocusedGroupId !== groupId || !$('#dept-fs-overlay').hasClass('is-open')) {
					openDepartmentFullscreen(groupId);
				}
				
					// Update counter with number of assignments loaded
					badge.removeClass('badge-secondary').addClass('badge-info').text(res.length + ' item' + (res.length!==1?'s':''));
				},
				complete: function() {
					tbody.data('loading', false);
				}
			})
		}

		
		// Duplicate attachment row card when add button clicked (ensure single binding & unique upload context)
		$(document).off('click', '#btn-add-attachment').on('click', '#btn-add-attachment', function(e){
			e.preventDefault();
			const $last = $('.row-container-attachments').last();
			if($last.length===0) return; // safety
			const $clone = $last.clone(false);
			// Clear user-entered text
			$clone.find('input[type="text"]').val('');
			// Remove any previous preview & unlink old data-file attributes
			$clone.find('.fileupload-preview').each(function(){
				$(this).empty().removeAttr('data-file');
			});
			$clone.find('button.btn-file').each(function(){
				$(this).removeAttr('data-file');
			});
			// Hidden input (upload holder) reset value & remove old data-file
			$clone.find('input.input-file[type="hidden"]').each(function(){
				$(this).val('').removeAttr('data-file').removeAttr('id');
			});
			// Add remove button if missing
			if($clone.find('.btn-remove-attachment').length===0){
				$clone.prepend('<div class="text-right"><button type="button" class="btn btn-danger btn-sm btn-remove-attachment btn-icon-only mb-3" title="Hapus blok"><i class="fas fa-times"></i></button></div>');
			}
			$clone.insertAfter($last);
			// Re-init fileupload plugin for the new block
			initAttachmentFileUpload($clone);
			// Re-index blocks
			$('.row-container-attachments').each(function(i){
				$(this).attr('data-attachment-index', i+1);
			});
		});

		// Helper to initialize a cloned attachment block with unique fileupload instance
		function initAttachmentFileUpload($block){
			const $hidden = $block.find('input.input-file[type="hidden"]');
			if(!$hidden.length) return;
			// Generate unique id token
			const idx = 'upl-file-' + Date.now() + '-' + Math.floor(Math.random()*1000);
			$hidden.attr('data-file', idx);
			$block.find('.fileupload-preview').attr('data-file', idx);
			$block.find('button.btn-file').attr('data-file', idx).text(typeof lang !== 'undefined' ? lang.unggah : 'Upload');
			// Build hidden form with real file input
			let accept = typeof $hidden.attr('data-accept') === 'undefined' ? (typeof upl_alw !== 'undefined' ? Base64.decode(upl_alw) : '*') : $hidden.attr('data-accept');
			let nm = $hidden.attr('name');
			let nmAttr = nm ? nm.replace('[','_').replace(']','') : 'file';
			let formHtml = '<form action="'+$hidden.attr('data-action')+'" class="hidden">';
			formHtml += '<input type="file" name="document" class="input-file" id="'+idx+'">';
			formHtml += '<input type="hidden" name="name" value="'+nmAttr+'">';
			formHtml += '<input type="hidden" name="token" value="'+($hidden.attr('data-token')||'')+'">';
			formHtml += '</form>';
			$('body').append(formHtml);
			// Initialize jQuery fileupload
			if(typeof $.fn.fileupload !== 'function') return; // plugin not loaded yet
			const acceptRegex = accept === '*' ? '*' : new RegExp('(\\.|\\/)('+accept+')$','i');
			const opt = {maxFileSize: (typeof upl_flsz!=='undefined'?upl_flsz:5242880), autoUpload:false, dataType:'text'};
			if(acceptRegex !== '*') opt.acceptFileTypes = acceptRegex;
			$('#'+idx).fileupload(opt)
			.on('fileuploadadd', function(e,data){
				$('button[data-file="'+idx+'"]').attr('disabled',true);
				data.process();
			})
			.on('fileuploadprocessalways', function(e,data){
				if(data.files.error){
					data.abort();
					$('button[data-file="'+idx+'"]').text(typeof lang!=='undefined'?lang.unggah:'Upload').removeAttr('disabled');
				}else{
					data.submit();
				}
			})
			.on('fileuploadprogressall', function(e,data){
				var progress = parseInt(data.loaded / data.total * 100,10);
				$('button[data-file="'+idx+'"]').text(progress+'%');
			})
			.on('fileuploaddone', function(e,data){
				if(data.result && data.result !== 'invalid'){
					var parts = data.result.split('/');
					if(parts.length==1) parts = data.result.split('\\');
					var fname = parts[parts.length-1];
					$('.fileupload-preview[data-file="'+idx+'"]').html('<a href="'+base_url+data.result+'" target="_blank">'+fname+'</a>');
					$('input[data-file="'+idx+'"]').val(data.result);
				}else{
					cAlert.open((typeof lang!=='undefined'?lang.file_gagal_diunggah:'Upload gagal'),'error');
				}
				$('button[data-file="'+idx+'"]').text(typeof lang!=='undefined'?lang.unggah:'Upload').removeAttr('disabled');
			})
			.on('fileuploadfail', function(){
				cAlert.open((typeof lang!=='undefined'?lang.file_gagal_diunggah:'Upload gagal'),'error');
				$('button[data-file="'+idx+'"]').text(typeof lang!=='undefined'?lang.unggah:'Upload').removeAttr('disabled');
			});
		}

		// Remove a duplicated attachment block
		$(document).on('click', '.btn-remove-attachment', function(){
			const $parent = $(this).closest('.row-container-attachments');
			// Keep at least one block
			if($('.row-container-attachments').length > 1){
				$parent.remove();
				$('.row-container-attachments').each(function(i){
					$(this).attr('data-attachment-index', i+1);
				});
			}
		});
					
		function bobotLabel(score){
			const map = { '4': 'Critical', '3': 'Major', '2': 'Moderate', '1': 'Minor' };
			return map[String(score)] || score;
		}

		function renderBobotBadge(kemungkinan, dampak, score, label){
			const colorMap = { '4': 'danger', '3': 'warning', '2': 'info', '1': 'success' };
			const color = colorMap[String(score)] || 'secondary';
			const displayLabel = label || bobotLabel(score);
			return `<small class="text-muted d-block">Kemungkinan: ${kemungkinan} | Dampak: ${dampak}</small>`
				 + `<span class="badge badge-${color}">${displayLabel}</span>`;
		}

		function previewBobot(){
			const k = $('#modal-bobot [name="bobot_kemungkinan"]').val();
			const d = $('#modal-bobot [name="bobot_dampak"]').val();
			if(!k || !d){ $('#bobot-preview-row').hide(); return; }
			const score = getScore(k, d);
			if(score === 'Unknown'){ $('#bobot-preview-row').hide(); return; }
			const colorMap = { '4': 'danger', '3': 'warning', '2': 'info', '1': 'success' };
			const color = colorMap[score] || 'secondary';
			$('#bobot-preview-badge')
				.removeClass('badge-danger badge-warning badge-info badge-success badge-secondary')
				.addClass('badge-' + color)
				.text(bobotLabel(score));
			$('#bobot-preview-row').show();
		}

		async function get_bobot_name(id){
			let data = await $.ajax({
				url: base_url + 'internal/audit_assignment/get_bobot_name',
				type: 'POST',
				dataType: 'json',
				data: { id: id },
			});
			return data.bobot || '';
		}

		async function get_status_finding_name(id){
			let status = await $.ajax({
				url: base_url + 'internal/audit_assignment/get_status_finding',
				type: 'POST',
				dataType: 'json',
				data: { id: id },
			});
			return status.description || '';
		}
	
const scoreMatrix = {
  "6,6": "4",
  "6,5": "4",
  "6,4": "3",
  "6,3": "3",
  "6,2": "2",
  "6,1": "1",
  "5,6": "4",
  "5,5": "3",
  "5,4": "3",
  "5,3": "2",
  "5,2": "2",
  "5,1": "1",
  "4,6": "3",
  "4,5": "3",
  "4,4": "2",
  "4,3": "2",
  "4,2": "2",
  "4,1": "1",
  "3,6": "3",
  "3,5": "2",
  "3,4": "2",
  "3,3": "2",
  "3,2": "1",
  "3,1": "1",
  "2,6": "2",
  "2,5": "2",
  "2,4": "2",
  "2,3": "1",
  "2,2": "1",
  "2,1": "1",
  "1,6": "2",
  "1,5": "1",
  "1,4": "1",
  "1,3": "1",
  "1,2": "1",
  "1,1": "1",
};

function getScore(kemungkinan, dampak) {
  const key = `${kemungkinan},${dampak}`;
  return scoreMatrix[key] || "Unknown"; // fallback kalau kombinasi tidak terdaftar
}
</script>
