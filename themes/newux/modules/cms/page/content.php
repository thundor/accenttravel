<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $page = Modules::$page; ?>
<?php if($this->_can_edit || file_exists($this->theme_path . 'views/partials/module/saved/module/custom/' . $page->page_id . '-content-top.json')) { ?>
<module id="content-top"></module>
<?php } ?>
<?php if(!empty($page->images)){ 
$count_images = count($page->images);
?>
<Gallery :images="(<?php echo htmlspecialchars(json_encode($page->images)); ?> || []).map(v => v.src)"></Gallery>
<?php } ?>
<?php if(strlen($page->content)){ ?>
<?php echo $page->content; ?>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php if($this->_can_edit || file_exists($this->theme_path . 'views/partials/module/saved/module/custom/' . $page->page_id . '-content-bottom.json')) { ?>
<module id="content-bottom"></module>
<?php } ?>
<?php themeFunctions::debugFileLine('end'); ?>