<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<form role="form" class="logpanel form-forgot form-horizontal wow flipInY animated" style="display: none"  id="passresetfrm" onsubmit="return false;">
	<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
  <h2 class="form-heading text-center"> Forgot Password</h2>
  <div class="resultreset"></div>
  <div style="font-size: 12px;" class="text-center">Enter your email address to reset your password</div>
  <br>
  <div class="input-group">
    <span class="input-group-addon"><i class="fa fa-envelope"></i>
    </span>
    <input type="email" id="resetemail" name="email" placeholder="Email" class="form-control">
  </div>
  <br>
  <div class="form-actions">
    <button type="button" class="btn btn-primary btn-back"><i class="fa fa-angle-left"></i>&nbsp;Back</button>
    <button id="btn-forgot" type="button" class="btn btn-success pull-right resetbtn ladda-button">Reset My Password</button>
  </div>
</form>
<?php themeFunctions::debugFileLine('end'); ?>