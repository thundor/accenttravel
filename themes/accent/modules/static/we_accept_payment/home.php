<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="container px-0">
	<?php /* <h1 class="text-center">Acceptam toate tipurile de vouchere de vacanta si carduri de vacanta <br/> Sodexo, Up Romania, Edenred</h1> */ ?>
	<div class="d-flex justify-content-center mt-5 flex-wrap flex-md-nowrap" style="">
		<a href="/vouchere" target="_BLANK" class="d-flex mb-5 oferta"><img class="img-fluid w-100" src="/resources/images/metode-plata/vouchere-de-vacanta.jpg" /></a>
		<a href="/gift-card" target="_BLANK" class="d-flex mb-5 oferta"><img class="img-fluid w-100" src="/resources/images/metode-plata/travel-gift-cards3.png" /></a>
	</div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>