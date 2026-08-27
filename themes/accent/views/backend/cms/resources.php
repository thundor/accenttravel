<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('cms_resources'); ?>
<?php themeFunctions::includeAddon('fileman'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/resources/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/resources/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/resources/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/resources/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/resources/stylesheets.php'); ?>
<section class="forms">
  <div class="col-12">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display"><?php echo lang('resources_list/html'); ?></h2>
          </div>
          <div class="card-block">
				<a class="d-block" id="resource_file" target="_blank" style="height:30px;"></a>
			  <iframe id="fileman_iframe" src="https://accenttravel.ro/fileman/index.html?integration=input&amp;env=iframe&amp;type=files" style="width:800px;height:500px;max-width:100%;max-height:100%" frameborder="0"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>