<?php
$p = $_GET['p'];
$ext = pathinfo($p, PATHINFO_EXTENSION);

$allowed_ext = array('js','css','png','jpg','gif');

if(!in_array($ext, $allowed_ext)){
  http_response_code(501);
  echo 'Extension not allowed';
  die;
}
$local_folder = __DIR__ . '/themes/accent/assets/plugins/ckeditor/';
$local_file = $local_folder . $p;

$file_name = basename($p);

$remote_url = 'https://js.plus/js-demo/ckeditor/';
$remote_url_file = $remote_url . $p;

$folder_path = dirname($p);
$folder_path_arr = explode('/', $folder_path);

$file = file_get_contents($remote_url_file);
if(!$file){
  http_response_code(502);
  echo 'Could not get file';
  die;
}

$folder = $local_folder;
foreach($folder_path_arr as $folder_path_item){
  $folder = $folder . $folder_path_item . '/';
  if(is_dir($folder)){
    continue;
  } else {
    $r = mkdir($folder);
    if(!$r){
      http_response_code(503);
      echo 'Could not create folder';
      die;
    }
  }
}
file_put_contents($local_file,$file);
// $content_type = mime_content_type($local_file);
// if(!$content_type){
// }
$content_type = 'application/octet-stream';
http_response_code(201);
header('Content-Description: File Transfer');
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Disposition: attachment; filename=$file_name");
header('Content-Type: ' . $content_type);
header("Content-Transfer-Encoding: binary");
header('Content-Length: ' . filesize($local_file));
ob_clean();
flush();
readfile($local_file);