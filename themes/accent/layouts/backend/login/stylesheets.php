<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<link href="<?php echo $this->theme_url; ?>assets/css/style.css" rel="stylesheet">
<link href="<?php echo $this->theme_url; ?>assets/css/login.css" rel="stylesheet">
<style>

#rotatingDiv {
  display: block;
  margin: 32px auto;
  height: 100px;
  width: 100px;
  -webkit-animation: rotation .9s infinite linear;
  -moz-animation: rotation .9s infinite linear;
  -o-animation: rotation .9s infinite linear;
  animation: rotation .9s infinite linear;
  border-left: 8px solid rgba(0,0,0,.20);
  border-right: 8px solid rgba(0,0,0,.20);
  border-bottom: 8px solid rgba(0,0,0,.20);
  border-top: 8px solid rgba(33,128,192,1);
  border-radius: 100%;
}
@keyframes rotation {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(359deg);
  }
}
@-webkit-keyframes rotation {
  from {
    -webkit-transform: rotate(0deg);
  }
  to {
    -webkit-transform: rotate(359deg);
  }
}
@-moz-keyframes rotation {
  from {
    -moz-transform: rotate(0deg);
  }
  to {
    -moz-transform: rotate(359deg);
  }
}
@-o-keyframes rotation {
  from {
    -o-transform: rotate(0deg);
  }
  to {
    -o-transform: rotate(359deg);
  }
}
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
  transition: background-color 5000s ease-in-out 5s;
}
body { 
  background-color: rgb(217, 217, 217) !important; 
}
</style>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>