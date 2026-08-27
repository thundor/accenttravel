<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath(themeFunctions::relativePath(__FILE__), __DIR__ . '/../../common/css/stylesheets.php'); ?>
<link href="<?php echo $this->theme_url; ?>assets/css/admin.style.css?v=1.0.1" rel="stylesheet">
<link href="<?php echo $this->theme_url; ?>assets/css/fontastic.css" rel="stylesheet">
<?php themeFunctions::loadAddons(__FILE__); ?>
<link href="<?php echo $this->theme_url; ?>assets/css/admin.custom.css?v=1.0.1" rel="stylesheet">
<style>
#content{
  padding-top: 81px;
}
.side-navbar-wrapper{
  padding-top: 66px;
}
@media (min-width:1200px){
  nav.side-navbar a{
    white-space:nowrap;
  }
  nav.side-navbar.shrink a{
    white-space:initial;
  }
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>