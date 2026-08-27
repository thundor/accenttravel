<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
.visibility_hidden {
  visibility: hidden;
}
.phone_contact_round_container {
  padding: 11px;
  position: fixed;
  z-index: 999998;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 0 none;
  float: right;
  background-color: #0275d8;
  bottom: 20px;
  right: 5px;
  text-align: center;
  color: #fff;
}
.phone_contact_round_icon {
  
}
#contact-mobile {
  height:50px;
  width:100%;
}
#contact-mobile > .contact-mobile-items{
  position:fixed;
  z-index:100;
  bottom:0;
  left:0;
  right:0;
  background-color: #fff;
  box-shadow: 0px -1px 20px #000;
}
#contact-mobile .contact-mobile-item{
  flex: 1 1 auto;
  flex-basis: 0;
  flex-grow: 1;
}
#back-to-top{
  bottom: 70px;
}
#contact-mobile .btn{
  border-radius: 0 !important;
  color: #fff;
}
#contact-mobile .btn > i {
  font-size: 30px;
  line-height: 30px;
  vertical-align: middle;
}
#contact-mobile .btn > span {
  padding-left: 20px;
  font-size: 20px;
  line-height: 20px;
  vertical-align: middle;
}
#contact-mobile .type-contact .btn {
  padding-left: 81px;
}
.cookieInfo{
  bottom:50px;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>