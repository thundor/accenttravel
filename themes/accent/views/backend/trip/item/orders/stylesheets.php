<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
#serviceCircuitResults .hotel-image,
#serviceHotelResults .hotel-image {
  width: 64px;
  height: 64px;
  background-size: cover;
}
.input-group > .select2_4-container {
  max-width: calc(100% - 43px);
}
.checkWrapper>label>span{
  position:absolute;
  right:15px;
}
.modal-dialog.modal-xl {
    max-width: calc(100% - 50px);
    height: calc(100% - 60px);
    display: flex;
}
.modal-dialog.modal-xl > .modal-content{
	width:100%;
}
.modal-dialog.has-iframe > .modal-content > .modal-body{
	padding:0;
}
.modal-body > iframe{
	width: 100%;
    height: 100%;
	border:0;
}
.loading-stuff {
    position: absolute;
    top: 0;
    z-index: 10;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1000%;
    color: #0275d8;
    background: rgba(255,255,255,0.8);
}
.modal-body:not(.loading)>.loading-stuff{
	display:none;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>