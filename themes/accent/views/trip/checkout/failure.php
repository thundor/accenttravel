<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/failure/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/failure/scripts.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-12">
      <br />
      <br />
      <br />
      <br />
      <br />
      <h3 class=" subTitleFilter pl-0">A fost intalnita o eroare in procesarea comenzii.</h3>
      <br />
      <p>In cateva momente veti fi redirectionat in mod automat catre prima pagina. Alternativ puteti da click <a href="<?php echo site_url(''); ?>">aici</a></p>
      <br />
      <br />
      <br />
      <br />
      <br />
      <br />
    </div>
  </div>
</div>


<?php themeFunctions::debugFileLine('end'); ?>