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
    <div class="card-body p-1">
        <div class="row">
			<div class="container-fluid">
				<div class="accordion" id="accordionExample">
					<?php if (empty($data)): ?>
						<div class="alert alert-info text-center" role="alert">
							<i class="fas fa-info-circle"></i> Content not found
						</div>
					<?php endif; ?>
					<?php foreach ($data as $index => $video): ?>
						<div class="card">
							<div class="card-header" id="heading<?php echo $index; ?>">
								<button class="btn btn-block text-left <?php echo $index === 0 ? 'collapsed' : ''; ?>" type="button" data-toggle="collapse" data-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $index; ?>">
									<h5 class="card-title"><?php echo $video['name']; ?></h5>
								</button>
							</div>

							<div id="collapse<?php echo $index; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-parent="#accordionExample">
							<div class="card-body">
								<div class="video-container">
								<?php 
									$url = $video['link'];
									if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false):
									// Get video ID from YouTube URL
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
									<source src="<?php echo base_url($video['file_attachment']); ?>" type="video/mp4">
									<p>Your browser does not support HTML5 video. 
									<a href="<?php echo base_url($video['file_attachment']); ?>">Download the video</a> instead.</p>
									</video>
								<?php endif; ?>
								</div>
							</div>
							</div>
							<div class="card-footer bg-light">
							<small class="text-muted">
								<i class="fas fa-clock"></i> Last updated: <?php echo date('d M Y H:i', strtotime($video['update_at'])); ?>
							</small>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
        </div>
    </div>
</div>
