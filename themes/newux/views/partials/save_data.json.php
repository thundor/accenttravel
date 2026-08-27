<?php
if(!$this->theme->_can_edit){
	die;
}
$post_data = $this->input->post();

$data = $post_data['data'];
$data = json_decode($data);
$template = $post_data['template'];
$name = $post_data['name'];
$path = isset($post_data['path']) ? $post_data['path'] : '';

$saved_dir = $this->theme->theme_path . 'views/partials/module/saved/';
$backup_dir = $this->theme->theme_path . 'views/partials/module/backup/saved/';
$rel_dir = dirname($template . '/' . ltrim($path . '/' . $name, '/')) . '/';
$name = basename($name);

$post_data['dir'] = $saved_dir . $rel_dir . $name . '.json';
ob_start(); ?><?php echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?><?php
$content = ob_get_clean();
$post_data['content'] = $content;
$exists = is_file($post_data['dir'] );
$dest = null;
if($exists){
	if(!is_dir($backup_dir . $rel_dir)){
		mkdir($backup_dir . $rel_dir, 0775, true);
	}
	$dest = $backup_dir . $rel_dir . $name . '-' . date('Y-m-d-H-i-s') . '.json';
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