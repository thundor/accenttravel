<?php
if(!$this->theme->_can_edit){
	die;
}
$post_data = $this->input->post();

$data = $post_data['data'];
$data = json_decode($data);
$template = $post_data['template'];
$name = $post_data['name'];

$saved_dir = $this->theme->theme_path . 'views/partials/module/';
$backup_dir = $this->theme->theme_path . 'views/partials/module/backup/';
$rel_dir = dirname(ltrim($name, '/')) . '/';
$name = basename($name);

$post_data['dir'] = $saved_dir . $rel_dir . $name . '.js.php';
ob_start(); ?>import ExtendTemplate from './templates/<?php echo $template; ?>.js?newux=1';
export default {
	extends: ExtendTemplate,
	name: '<?php echo '<?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>'; ?>',
	data: () => ({
		name: '<?php echo '<?php echo basename(__FILE__,\'.js.php\'); ?>'; ?>',
		data: <?php echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
	}),
}<?php
$content = ob_get_clean();
$post_data['content'] = $content;
$exists = is_file($post_data['dir'] );
if($exists){
	if(!is_dir($backup_dir . $rel_dir)){
		mkdir($backup_dir . $rel_dir, 0775, true);
	}
	$dest = $backup_dir . $rel_dir . $name . '-' . date('Y-m-d-H-i-s') . '.js.php';
	copy($post_data['dir'], $dest);
}

$executed = file_put_contents($post_data['dir'], $content);
if(isset($dest)){
	chmod($post_data['dir'], 0664);
	if(sha1_file($post_data['dir']) !== sha1_file($dest)){
		gzCompressFile($dest, 9);
	}
	unlink($dest);
}
// $post_data['executed'] = $executed;
echo json_encode($post_data);