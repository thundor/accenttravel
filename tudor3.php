<?php
/* function setPermissions($dir) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir()) {
            chmod($item->getPathname(), 0755);
        } elseif ($item->isFile()) {
            chmod($item->getPathname(), 0644);
        }
    }

    // Asigură-te că și folderul de bază e setat corect
    // chmod($dir, 0755);
}

setPermissions(__DIR__);
echo 'done';
die; */
ob_implicit_flush(true);
@ini_set('output_buffering', 0);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
set_time_limit(1800);
// die;
ini_set('display_errors', 1);
// $directory = realpath(__DIR__ . '/storage/modification');
$directory = realpath(__DIR__);
// $directory = realpath(__DIR__ . '/storage');
// $directory = realpath(__DIR__ . '/dona-manager');
// $directory = realpath(__DIR__ . '/system');
$directory_len = strlen($directory);
$to_zip = false;
// $other_directory = dirname(__DIR__) . '/test/public_html';

$zip = new ZipArchive();
if(!$zip->open($directory . '/backup-' . date('Y-m-d_H-i-s') . '.zip',  ZipArchive::CREATE)){
	die ("Could not open archive");
}

if($to_zip){
// Will exclude everything under these directories
$zipa1 = new ZipArchive();
if(!$zipa1->open($directory . '/a1' . date('Y-m-d_H-i-s') . '.zip',  ZipArchive::CREATE)){
	die ("Could not open archive");
}
$zipa2 = new ZipArchive();
if(!$zipa2->open($directory . '/a2' . date('Y-m-d_H-i-s') . '.zip',  ZipArchive::CREATE)){
	die ("Could not open archive");
}
// $zipb1 = new ZipArchive();
// if(!$zipb1->open($directory . '/b1' . date('Y-m-d_H-i-s') . '.zip',  ZipArchive::CREATE)){
	// die ("Could not open archive");
// }
$zipb2 = new ZipArchive();
if(!$zipb2->open($directory . '/b2' . date('Y-m-d_H-i-s') . '.zip',  ZipArchive::CREATE)){
	die ("Could not open archive");
}
}
$path_filter = array(
	'skip_links' => 1,
	'exclude_dir' => array(
		'.git', 
		'app/cache', 
		'app/tmp', 
		'app/logs', 
		'system/cache', 
		'resources', 
	),
	
	'file_pathname_regex' => array(
		'exclude' => '/\.(zip|gz)$/'
		// 'include' => '/\.(php|tpl|twig|xml)$/'
	),
);

/**
 * @param SplFileInfo $file
 * @param mixed $key
 * @param RecursiveCallbackFilterIterator $iterator
 * @return bool True if you need to recurse or if the item is acceptable
 */
$filter = function ($file, $key, $iterator) use ($path_filter, $directory_len) {
	$rel_pathname = substr($file->getPathname(),$directory_len+1);
	
	if(isset($path_filter['pathname_regex']) && is_array($path_filter['pathname_regex'])){
		$found = false;
		foreach($path_filter['pathname_regex'] as $type => $regexes){
			foreach((array)$regexes as $pattern){
				if(preg_match($pattern, $rel_pathname)){
					$found = $type;
					break(2);
				}
			}
		}
		if('exclude' === $found){
			return false;
		}
		if($found !== 'include' && isset($path_filter['pathname_regex']['include'])){
			return false;
		}
	}
	
    if (!empty($path_filter['skip_link']) && $iterator->isLink()) {
		return false;
	}
    if ($iterator->hasChildren()) {
		if (!empty($path_filter['skip_dir'])) {
			return false;
		}
		if(isset($path_filter['exclude_dir']) && is_array($path_filter['exclude_dir']) && in_array($rel_pathname, $path_filter['exclude_dir'])){
			return false;
		}
		if(isset($path_filter['dir_pathname_regex']) && is_array($path_filter['dir_pathname_regex'])){
			$found = false;
			foreach($path_filter['dir_pathname_regex'] as $type => $regexes){
				foreach((array)$regexes as $pattern){
					if(preg_match($pattern, $rel_pathname)){
						$found = $type;
						break(2);
					}
				}
			}
			if('exclude' === $found){
				return false;
			}
			if($found !== 'include' && isset($path_filter['dir_pathname_regex']['include'])){
				return false;
			}
		}
        return true;
    }
	if (!empty($path_filter['skip_file'])) {
		return false;
	}
	if(isset($path_filter['exclude_ext']) && is_array($path_filter['exclude_ext']) && !in_array($file->getExtension(), $path_filter['exclude_ext'])){
		return false;
	}
	
	if(isset($path_filter['file_pathname_regex']) && is_array($path_filter['file_pathname_regex'])){
		$found = false;
		foreach($path_filter['file_pathname_regex'] as $type => $regexes){
			foreach((array)$regexes as $pattern){
				if(preg_match($pattern, $rel_pathname)){
					// var_dump($pattern);
					$found = $type;
					break(2);
				}
			}
		}
		if('exclude' === $found){
			return false;
		}
		if($found !== 'include' && isset($path_filter['file_pathname_regex']['include'])){
			return false;
		}
	}
	
    return $file->isFile();
};

$innerIterator = new RecursiveDirectoryIterator(
    $directory,
    RecursiveDirectoryIterator::SKIP_DOTS
);
$iterator = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator($innerIterator, $filter)
);
// $ref_time = strtotime("2025-05-01");
$files_count = 0;
foreach ($iterator as $pathname => $file) {
	$rel_pathname = substr($file->getPathname(),$directory_len+1);
    $zip->addFile($file->getPathname(),$rel_pathname);
    $files_count++;
    if(!($files_count % 1000)){
        $files_count = 0;
        echo '.';
    }
    continue;
    
    
	// if($file->getMTime() < $ref_time) continue;
	// echo '<pre>';
	// print_r(get_class_methods($file));
	// die;
	/* if($to_zip){
	// var_dump(is_file($other_directory . '/' . $rel_pathname));
	// die;
	if(!is_file($other_directory . '/' . $rel_pathname)){
		echo "NEW! " . $rel_pathname . '<br />';
		$zipa1->addFile($file->getPathname(),$rel_pathname);
		continue;
	}
	if(sha1_file($other_directory . '/' . $rel_pathname) != sha1_file($file->getPathname())){
		echo "DIFF " . $rel_pathname . '<br />';
		$zipa2->addFile($file->getPathname(),$rel_pathname);
		$zipb2->addFile($other_directory . '/' . $rel_pathname,$rel_pathname);
		continue;
	}
	} */
	/* $content = file_get_contents($file->getPathname());
	// $zip->addFile($file->getPathname(),$rel_pathname);
	// $word = "lph_requests";
	// $word = "config_error_display";
	// $word = "-Verification";
	// $word = "alfaromtrans";
	// $word = "php://input";
	$word = "file_get_contents";
	if(!preg_match('/' . preg_quote($word,'/') . '/i',$content)){
	// if(!preg_match('/(base64_decode|gzinflate|gzuncompress|str_rot13|shell_exec|proc_open|create_function)/i',$content, $matches)){
	// if(!preg_match('/\b' . preg_quote($word,'/') . '\b/',$content)){
	// if(!preg_match('/orders-.*?.csv/',$content)){
	// if(!preg_match('/' . preg_quote($word,'/') . '.*?cart/i',$content)){
		continue;
	}
    // echo htmlspecialchars(print_r($matches, true));
    echo '<b>' . $rel_pathname . '</b><br />';
     */
}
$zip->close();
if($to_zip){
$zipa1->close();
$zipa2->close();
// $zipb1->close();
$zipb2->close();
}
echo 'done';
die;