<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<form method="POST" data-wow-duration="1s" data-wow-delay="1s" class="form-signin form-horizontal wow fadeIn animated" role="form" onsubmit="return false;">
  <div  >
    <h2 class="form-heading text-center">Login Panel</h2>
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <input type="text" name="username" placeholder="Username" required="" autofocus="" class="form-control">
    <input type="password" name="password" placeholder="Password" required="" class="form-control">
    <div class="row form-group">
      <div class="col-xs-6">
        <label class="checkbox">
        <input type="checkbox" name="remember" value="remember-me"> Remember me
        </label>
      </div>
      <div class="col-xs-6">
        <div class="forget-password">
          Forgot password?
          <div class="clearfix"></div>
          <a id="link-forgot" href="#"> <strong>Click Here</strong></a>
        </div>
      </div>
    </div>
  </div>
  <button data-wow-duration="2s" data-wow-delay="s" type="submit" class="btn btn-primary btn-block ladda-button fadeIn animated" data-style="zoom-in">Login</button>
  <div style="margin-top:10px" class="resultlogin"></div>
</form>
<?php themeFunctions::debugFileLine('end'); ?>