								<!-- progress 2 !-->
                                <div class="tab-pane fade" id="progress_2" role="tabpanel" aria-labelledby="email-tab">
										<div class="card-header"><b>Keterangan Progress</b></div>
										<?php 
										input('hidden','no_progress2','no_progress2','','2');
										?>
										<textarea name="keterangan_progress_2" id="keterangan_progress_2" class="form-control xxeditor" data-validation="required" rows="4" xxdata-editor="inline"></textarea>
									<br>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="evidence_base"><?php echo lang('evidence_base'); ?></label>		
										<div class="col-sm-9">
											<input type="text" name="evidence_base" id="evidence_base"  data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
										</div>
									</div>
									<?php if(user('id_group') != AUDITEE) { ?>
									<div class="card-header"><b>Comment Auditor</b></div>
									<textarea name="comment_progress_2" id="comment_progress_2" class="form-control xxeditior" rows="4" xxdata-editor="inline"></textarea>
									
									<br>
									<div id = "status_progress" class="form-group row">
										<label class="col-form-label col-sm-3" for="status_capa2"><?php echo (lang('status_capa')); ?></label>		
										<div class="col-sm-9">
												<?php foreach($status_cp as $u) { 
													echo '<option value="'.$u['id'].'" data-value="'.$u['status'].'">'.$u['status'].'</option>';
												}
												?>
										</div>

									<?php }; ?>
									</br>

									</div>
									</div>
									<!-- !-->

									<!-- progress 3 !-->
									<div class="tab-pane fade" id="progress_3" role="tabpanel" aria-labelledby="email-tab">
										<div class="card-header"><b>Keterangan Progress</b></div>
										<?php 
										input('hidden','no_progress3','no_progress3','','3');
										?>
										<textarea name="keterangan_progress_3" id="keterangan_progress_3" class="form-control xxeditor" data-validation="required" rows="4" xxdata-editor="inline"></textarea>
									<br>
									<div class="form-group row">
										<label class="col-form-label col-sm-3" for="evidence_base"><?php echo lang('evidence_base'); ?></label>		
										<div class="col-sm-9">
											<input type="text" name="evidence_base" id="evidence_base"  data-validation="" data-action="<?php echo base_url('upload/file/datetime'); ?>" data-token="<?php echo encode_id([user('id'),(time() + 900)]); ?>" autocomplete="off" class="form-control input-file" value="" placeholder="<?php echo lang('maksimal'); ?> 5MB">
										</div>
									</div>
									<?php if(user('id_group') != AUDITEE) { ?>
									<div class="card-header"><b>Comment Auditor</b></div>
									<textarea name="comment_progress_3" id="comment_progress_3" class="form-control xxeditor" rows="4" xxdata-editor="inline"></textarea>
									
									<br>
									<div id = "status_progress" class="form-group row">
										<label class="col-form-label col-sm-3" for="status_capa3"><?php echo (lang('status_capa')); ?></label>		
										<div class="col-sm-9">
												<?php foreach($status_cp as $u) { 
													echo '<option value="'.$u['id'].'" data-value="'.$u['status'].'">'.$u['status'].'</option>';
												}
												?>
										</div>

									<?php }; ?>
									</br>

									</div>
									</div>
									<!-- !-->