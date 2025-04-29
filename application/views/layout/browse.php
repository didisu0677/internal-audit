<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="appname" content="<?php echo setting('title'); ?>">
<meta name="applang" content="<?php echo setting('language'); ?>">
<meta name="description" content="<?php echo setting('deskripsi'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<title><?php echo setting('title') . ' &raquo; ' . $title; ?></title>
<link rel="shortcut icon" href="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" />
<?php
Asset::css('bootstrap.min.css', true);
Asset::css('bootstrap.color.min.css', true);
Asset::css('bootstrap.tagsinput.css', true);
Asset::css('daterangepicker.css', true);
Asset::css('roboto.css', true);
Asset::css('fontawesome.css', true);
Asset::css('select2.min.css', true);
Asset::css('iconpicker.css', true);
Asset::css('jquery.contextMenu.min.css', true);
Asset::css('jquery.toast.min.css', true);
Asset::css('style.css', true);
if(setting('ukuran_tampilan') == 'small') {
	Asset::css('small-style.css', true);
}
if(setting('custom_template') && file_exists(FCPATH . 'assets/css/template.css')) {
	Asset::css('template.css', true);
}
echo Asset::render();
?>
<?php echo $css_content; ?>
</head>
<body class="<?php
	if(setting('tipe_menu') == 'menubar') echo 'app-menubar ';
	else echo 'app-sidebar ';
	if(setting('sensor_data') == '1') echo 'censored-data ';
	echo setting('warna_dropdown').' ';
	if((get_cookie('menu-minimize') && get_cookie('menu-minimize') == 'minimize') || setting('tipe_menu') == 'sidebar') echo 'body-minimize';
	?>"<?php echo flash_body(); ?> data-size="<?php echo setting('ukuran_tampilan'); ?>">
	<div class="app-content fixed-content serverside-only" id="content">
		<?php echo $view_content; ?>
	</div>
<script type="text/javascript">
var base_url = '<?php echo base_url(); ?>';
var user_key = '<?php echo user('key_id'); ?>';
var ws_server = '<?php if(setting('websocket') && setting('ws_server')) echo base64_encode(setting('ws_server')); ?>';
var upl_flsz = <?php echo (isset($file_upload_max_size) ? $file_upload_max_size : 2048) ?>;
var upl_alw = '<?php echo (defined('ALLOWED_FILE_UPLOAD') ? base64_encode(ALLOWED_FILE_UPLOAD) : '') ?>';
</script>
<?php
Asset::js('jquery.min.js', true);
Asset::js('jquery.hotkeys.js', true);
Asset::js('jquery.browser.min.js', true);
Asset::js('jquery.mousewheel.min.js', true);
Asset::js('jquery.mask.min.js', true);
Asset::js('jquery.fileupload.js', true);
Asset::js('jquery.contextMenu.min.js', true);
Asset::js('jquery.autocomplete.js', true);
Asset::js('jquery.redirect.js', true);
Asset::js('jquery.toast.min.js', true);
Asset::js('push.min.js', true);
Asset::js('hashids.min.js', true);
Asset::js('moment.min.js', true);
Asset::js('other.bundle.js', true);
Asset::js('popper.min.js', true);
Asset::js('bootstrap.min.js', true);
Asset::js('bootstrap.tagsinput.js', true);
Asset::js('daterangepicker.js', true);
Asset::js('sweetalert.min.js', true);
Asset::js('select2.min.js', true);
Asset::js('iconpicker.js', true);
Asset::js('_'.setting('language').'.js', true);
Asset::js('app.fn.js', true);
Asset::js('app.js', true);
if(setting('websocket')) {
	Asset::js('linkify.min.js', true);
	Asset::js('linkify-jquery.min.js', true);
	Asset::js('realtime.js', true);
	Asset::js('chatbox.js', true);
}
Asset::js('main.js', true);
Asset::js('table.js', true);
echo Asset::render();
?>
<?php echo $js_content; ?>
</body>
</html>
