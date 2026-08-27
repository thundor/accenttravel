<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<div class="col-md-6 hidden-sm hidden-xs" style="height: 100vh; overflow-y: auto;">
  <a href="<?php echo base_url(); ?>" class="logo">
    <img data-wow-duration="2s" data-wow-delay="4s" src="<?php echo $this->theme_url; ?>assets/images/logo.png" class="wow fadeIn img-responsive center-block"/>
  </a>
</div>
<div class="col-md-6" style="background-color:white;height: 100vh; overflow-y: auto;">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <img data-wow-duration="0.5s" data-wow-delay="0.5s" src="<?php echo $this->theme_url; ?>assets/images/backend.png" class="wow fadeIn center-block" style="width:78px;height:60px;margin-top:100px;margin-bottom:20px" alt="" />
    <?php include 'content/login.php'; ?>
    <?php include 'content/reset.php'; ?>
 </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>