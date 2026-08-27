<?php
require __DIR__ . '/../../vendor/autoload.php';
use ScssPhp\ScssPhp\Compiler;
$filename = $a;
if(isset($_GET['name']) && !preg_match('/[^a-z0-9]/i', $_GET['name'])){
	$filename = dirname($filename) . "/" . $_GET['name'] . '.css';
}

// Path to your SCSS file
$scssFilePath = $this->theme->theme_path . 'assets/scss/' . basename($filename, '.css') . '.scss';

// Define the output directory and CSS file
$outputDir = $this->theme->theme_path . 'assets/css';
$outputFile = $outputDir . '/' . basename($filename) . '';
function outputCssError($message, $code='203 Error'){
	header("HTTP/1.0 $code");
	echo '/* ' . $message . ' */
	html:before{
		content: "' . $message . '";
		position: fixed;
		z-index: 99999;
		top:0;
		left:0;
		border:1px solid red;
		color: red;
		padding: 50px;
		background: white;
	}
	';
	exit;
}
$scss = new Compiler();
if(file_exists($scssFilePath)){
	if(!(file_exists($outputFile) && filemtime($outputFile) == filemtime($scssFilePath))){
		try {
			// Load the SCSS file content
			$scssContent = file_get_contents($scssFilePath);
			// var_dump($scssContent); die;
			// Compile SCSS to CSS
			$compiledCss = $scss->compile($scssContent);

			// Ensure the output directory exists
			if (!file_exists($outputDir)) {
				mkdir($outputDir, 0775, true);
			}

			// Save the compiled CSS to the output file
			file_put_contents($outputFile, $compiledCss);
			touch($outputFile, filemtime($scssFilePath));
			
			header("HTTP/1.0 201 Created");
			echo '/* CSS is freshly generated from SCSS */';
			// echo "SCSS compiled successfully to $outputFile";
		} catch (Exception $e) {
			outputCssError('Error compiling SCSS: ' . $e->getMessage() . '');
		}
	}
} else {
	outputCssError('SCSS ' . basename($filename, '.css') . '.scss NOT FOUND', '404 Not Found');
}
if(file_exists($outputFile)){
	$f = fopen($outputFile, 'rb');
	fpassthru($f);
	fclose($f);
	return;
} else {
	outputCssError('CSS ' . basename($filename) . ' NOT FOUND', '404 Not Found');
}