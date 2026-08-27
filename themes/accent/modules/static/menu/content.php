<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<nav class="navbar navbar-toggleable-md navbar-inverse mainNav">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navBarMain" aria-controls="navBarMain" aria-expanded="false" aria-label="Arata Meniu">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="<?php echo base_url(); ?>"><i class="fa fa-home"></i></a>
  <div class="collapse navbar-collapse" id="navBarMain">
    <?php include 'items.php'; ?>
    <?php include 'search.php'; ?>
    <?php themeFunctions::loadAddons(__FILE__); ?>
  </div>
</nav>
<?php themeFunctions::debugFileLine('end'); ?>