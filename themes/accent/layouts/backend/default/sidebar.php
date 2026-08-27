<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<!-- Side Navbar -->
<nav class="side-navbar<?php //echo $this->_ci->input->cookie('backend-side-navbar-shrink') ? ' shrink' : '';?>">
  <div class="side-navbar-wrapper">
    <?php include 'sidebar/menu_items.php'; ?>
  </div>
</nav>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>