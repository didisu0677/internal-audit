<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="card-header text-center">
		<?php echo setting('deskripsi'); ?>		
	</div>
    <div class="card-body p-1">
	<div class="main-container p-4 mb-sm-4 text-center">
		<!-- content video -->
		<div class="row">
			<?php foreach ($data as $video): ?>
				<?php if ($video['type'] == 'video_induction'): ?>
					<div class="col-md-12 mb-4">
						<div class="card shadow-sm">
							<div class="card-header bg-light">
								<h5 class="card-title mb-0"><?php echo $video['name']; ?></h5>
							</div>
							<div class="card-body">
								<div class="video-container">
									<?php 
										$url = $video['link'];
										if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false):
											// Ambil video ID dari YouTube URL
											if (strpos($url, 'youtu.be') !== false) {
												$videoId = basename(parse_url($url, PHP_URL_PATH));
											} else {
												parse_str(parse_url($url, PHP_URL_QUERY), $ytParams);
												$videoId = $ytParams['v'] ?? '';
											}

									
											$embedUrl = "https://www.youtube.com/embed/$videoId";
									?>
										<iframe 
											width="100%" 
											height="600px" 
											src="<?php echo $embedUrl; ?>" 
											title="YouTube video player" 
											frameborder="0" 
											allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
											allowfullscreen>
										</iframe>
									<?php else: ?>
										<video class="w-100 rounded" controls playsinline preload="metadata">
											<source src="<?php echo base_url($url); ?>" type="video/mp4">
											<p>Your browser does not support HTML5 video. 
											<a href="<?php echo base_url($url); ?>">Download the video</a> instead.</p>
										</video>
									<?php endif; ?>
								</div>
							</div>
							<div class="card-footer bg-light">
								<small class="text-muted">
									<i class="fas fa-clock"></i> Last updated: <?php echo date('d M Y H:i', strtotime($video['update_at'])); ?>
								</small>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<div class="text-center card-footer pr-3 pl-4 pb-2 pt-2">
	<a href="<?php echo base_url('info/version'); ?>" class="app-version cInfo pt-2 d-inline-block" data-smallmodal aria-label="<?php echo lang('histori_versi'); ?>"><?php echo setting('title').' v'.APP_VERSION; ?></a>
	<br>
	<span class="text"><?php echo setting('company'); ?></span>
	<br>
	<span class="text"><?php echo nl2br(setting('alamat_perusahaan')); ?></span>
</div>
