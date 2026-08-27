<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="text-center">
  <nav aria-label="Navigare pagini">
    <ul class="pagination" id="packages_navigation">
    </ul>
  </nav>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>