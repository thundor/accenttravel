<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadModule('static/home_popup',__FILE__); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="fullWidth bg-home-slider">
	<style>
.notificare {
display: inherit;
background-color: #d5d42e;
max-width: 100%;
padding: 15px;
text-align: center;
color: #132B4F;
}
span.afla_detalii {
margin: 10px;
}
a#button_alfa_detalii {
color: #132B4F;
padding: 5px;
border: 1px solid #132B4F;
border-radius: 4px;
}
	</style>
	<div class="notificare" style="font-weight:bold;">TRAVEL GIFT CARD: UN CADOU FLEXIBIL, MEMORABIL
		<span class="afla_detalii"><a id="button_alfa_detalii" href="https://accenttravel.ro/gift-card" target="_blank" title="Afla detalii">Afla detalii</a></span>
	</div>
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-9">
        <?php include 'index/pos1.php'; ?>
      </div>
      <div class="col-sm-12 col-md-3">
        <?php include 'index/pos2.php'; ?>
      </div>
    </div>
  </div>
</div>
<div class="container">
  <?php include 'index/pos3.php'; ?>
</div>
<?php include 'index/pos8.php'; ?>
<?php include 'index/pos7.php'; ?>
<div class="container mt-5">
  <div class="row">
    <div class="col-sm-12 col-md-6 mb-4">
      <?php include 'index/pos4.php'; ?>
    </div>
    <div class="col-sm-12 col-md-6 mb-4">
      <?php include 'index/pos5.php'; ?>
    </div>
  </div>
</div>
<?php include 'index/pos6.php'; ?>
<?php themeFunctions::debugFileLine('end'); ?>