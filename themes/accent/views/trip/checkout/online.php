<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/online/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/online/scripts.php'); ?>
<?php $gateway = $this->view_data['gateway']; ?>
<?php themeFunctions::loadModule('payment_gateways/' . $gateway . '/redirect', __FILE__); ?>
<div class="container">
  <div class="row">
    <div class="col-12">
      <br />
      <br />
      <br />
      <br />
      <br />
      <h3 class=" subTitleFilter pl-0">Se trimit informatiile catre procesatorul de plati</h3>
      <p>Va multumim pentru alegerea facuta.</p>
      <p>In in momentul confirmarii platii, un email va fi trimis la adresa specificata de dumneavoastra cu detaliile rezervarii.</p>
      <br />
      <p>In cateva momente veti fi redirectionat in mod automat catre pagina procesatorului. Alternativ puteti da click <button type="submit" class="btn btn-success btn-small" form="onlineForm"><i class="fa fa-paper-plane"></i> aici</button></p>
      <br />
      <br />
      <br />
      <br />
      <br />
      <br />
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>