<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="vacanteTem" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Vacante Tematice</a>
  <div class="dropdown-menu" aria-labelledby="vacanteTem">
    <a class="dropdown-item" href="#">Arta &amp; Cultura</a>
    <a class="dropdown-item" href="#">Distractie</a>
    <a class="dropdown-item" href="#">Familie</a>
    <a class="dropdown-item" href="#">Romantice</a>
    <a class="dropdown-item" href="#">Ski &amp; Snowboard</a>
    <a class="dropdown-item" href="#">Shopping</a>
    <a class="dropdown-item" href="#">Sporturi</a>
  </div>
</li>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>