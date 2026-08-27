<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="quick-login-content">
<p id="contulMeu" class="mb-0"><i class="fa fa-user"></i> CONTUL MEU </p> 
<div id="contOpt">
  <?php if($this->_ci->user->type == 'guest') { ?>
  <p id="login"><i class="fa fa-lock"></i> Log In</p>
  <p id="register"><i class="fa fa-key"></i> Inregistrare</p>
  <img src="<?php echo $this->theme_url; ?>assets/images/arrowUp.png" alt="arrowUp" />
  <?php } else { ?>
  <p id="profile"><a href="<?php echo site_url('account/profile'); ?>"><i class="fa fa-user"></i> Setari cont</a></p>
  <p id="myorders"><a href="<?php echo site_url('account/trip/orders'); ?>"><i class="fa fa-history"></i> Istoric comenzi</a></p>
  <p id="mynotifications"><a href="<?php echo site_url('notifications'); ?>"><i class="fa fa-bell"></i> Alerta oferte</a></p>
  <p id="logout"><a href="<?php echo site_url('account/logout'); ?>"><i class="fa fa-unlock"></i> Log Out</a></p>
  <?php } ?>
</div>
<?php if($this->_ci->user->type == 'guest') { ?>
<!--Login modal-->
<div id="loginWindow">
  <h3>Intra in cont</h3>
  <span class="fa fa-2x fa-arrow-circle-o-down"></span>
  <p>Ai cont deja? Autentifica-te mai jos:</p>
  <form id="loginForm" name="loginForm" method="POST" onsubmit="return false;">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
	<input type="text" name="<?php echo md5($this->_ci->security->get_csrf_hash()); ?>" value="" class="form-sec" />
    <?php } ?>
    <div class="form-group">
      <input name="username" type="email" maxlength="255" class="form-control form-control-lg" id="userLogin" placeholder="Introduceti email">
    </div>
    <div class="form-group">
      <input name="password" type="password" class="form-control form-control-lg" id="passLogin" placeholder="Introduceti parola">
      <label class="form-check-label"><input name="remember" class="form-check-input" type="checkbox"> Tine-ma minte</label>
    </div>
    <div id="result_loginForm" class="form-group"></div>
    <button type="submit" class="btn btn-lg btn-primary btn-md btn-block login-submit text-size-small">Intra in cont</button>
    <p><a href="#" id="forgot2">Mi-am uitat parola</a><br />
      <a href="#"  id="register2">Nu ai cont? Inregistreaza-te</a></p>
  </form>
  <i id="loginClose" class="fa fa-close"></i>
  <?php 
  $this->_ci->load->model('Options_model');
  $enabled_social_networks = $this->_ci->Options_model->getKeys('social_networks_status');
  if($enabled_social_networks) { ?>
  <p class="strikeMiddle">SAU</p>
  <?php if(in_array('fb', $enabled_social_networks)){
  $this->_ci->load->library('facebook');
  $authenticated = $this->_ci->facebook->is_authenticated();
  ?>
  <a href="<?php echo $this->_ci->facebook->login_url(); ?>" class="btn btn-info btn-block btn-lg facebook-login text-center"><i class="fa fa-facebook"></i> Conectare cu Facebook</a>
  <?php } ?>
  <?php } ?>
</div>
<!--Inregistrare modal-->      
<div id="regWindow"> 
  <h3>Creeaza un cont</h3>
  <span class="fa fa-2x fa-arrow-circle-o-down"></span>
  <form id="registerForm" name="registerForm" method="POST" onsubmit="return false;">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
	<input type="text" name="<?php echo md5($this->_ci->security->get_csrf_hash()); ?>" value="" class="form-sec" />
    <?php } ?>
    <div class="form-group">
      <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-5">
          <select id="register_title" name="title" class="form-control form-control-lg">
            <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/profile_title_options', array('selected'=>'mr')); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/profile_title_options'); ?>
          </select>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-7">
          <input type="text" class="form-control form-control-lg" name="firstname" maxlength="255" id="register_firstname" placeholder="Prenume">
        </div>
      </div>
    </div>
    <div class="form-group">
      <input type="text" class="form-control form-control-lg" name="lastname" maxlength="255" id="register_lastname" placeholder="Nume" required>
    </div>
    <div class="form-group">
      <input type="tel" class="form-control form-control-lg" name="phone" maxlength="100" id="register_phone" placeholder="Numar de telefon" required>
    </div>
    <div class="form-group">
      <input type="email" class="form-control form-control-lg" name="email" maxlength="255" id="register_email" placeholder="Adresa de email" required>
    </div>
    <div class="form-group">
      <input type="password" class="form-control form-control-lg" name="password" id="register_password" placeholder="Introduceti parola" required>
    </div>
    <div class="form-group">
      <input type="password" class="form-control form-control-lg" name="confirm_password" id="register_confirm_password" placeholder="Confirmare parola" required>
    </div>
    <div class="form-group">
      <input type="hidden" name="newsletter" value="0" />
      <div class="custom-controls-stacked d-block">
        <label class="custom-control custom-checkbox">
          <input id="register_newsletter" type="checkbox" name="newsletter" value="1" class="custom-control-input">
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">Tine-ma la curent cu promotiile si ofertele Accent Travel &amp; Events</span>
        </label>
      </div>
    </div>
    <div class="form-group">
      <input type="hidden" name="tos" value="0" />
      <div class="custom-controls-stacked d-block">
        <label class="custom-control custom-checkbox">
          <input id="register_tos" type="checkbox" name="tos" value="1" class="custom-control-input" required>
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">Sunt de acord cu <a target="_BLANK" href="/termeni-si-conditii">Termenii si Conditiile</a> Accent Travel &amp; Events</span>
        </label>
      </div>
    </div>
    <div class="form-group">
      <input type="hidden" name="tpc" value="0" />
      <div class="custom-controls-stacked d-block">
        <label class="custom-control custom-checkbox">
          <input id="register_tpc" type="checkbox" name="tpc" value="1" class="custom-control-input" required>
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">Sunt de acord cu prelucrarea datelor cu caracter personal conform <a target="_BLANK" href="/declaratie-de-consimtamant">Declaratiei de consimtamant</a></span>
        </label>
      </div>
    </div>
    <button type="submit" class="btn btn-lg btn-primary btn-md btn-block login-submit text-size-small">Inregistrare</button>
  </form>
  <div id="result_registerForm" class="form-group"></div>
  <i id="regClose" class="fa fa-close"></i>
</div>
<div id="forgotWindow"> 
  <h3>Am uitat parola</h3>
  <span class="fa fa-2x fa-arrow-circle-o-down"></span>
  <form id="forgotForm" name="forgotForm" method="POST" onsubmit="return false;">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
	<input type="text" name="<?php echo md5($this->_ci->security->get_csrf_hash()); ?>" value="" class="form-sec" />
    <?php } ?>
    <div class="form-group">
      <input type="email" class="form-control form-control-lg" name="email" maxlength="255" id="forgot_email" placeholder="Adresa de email" required>
    </div>
    <button type="submit" class="btn btn-lg btn-primary btn-md btn-block login-submit text-size-small">Trimite codul pe email</button>
  </form>
  <div id="result_forgotForm" class="form-group"></div>
  <i id="forgotClose" class="fa fa-close"></i>
</div>
<?php } ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>