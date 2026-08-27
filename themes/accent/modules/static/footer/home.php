<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="footer">
  <div class="container">
    <div class="row">
      <div class="col-sm-6 col-lg-3">
        <img src="<?php echo $this->theme_url; ?>assets/images/logo.png" alt="Logo Accent Travel and Events" id="logoFooter" /><br />
        <p class="socialGo">Afla totul despre destinatiile tale favorite si descopera destinatii noi!</p>
        <a target="_BLANK" href="https://www.facebook.com/AccentTravelAndEvents/"><img src="<?php echo $this->theme_url; ?>assets/images/facebook.png" alt="Accent Facebook" class="socialFoot" /></a>
        <a target="_BLANK" href="https://twitter.com/Accent_Travel"><img src="<?php echo $this->theme_url; ?>assets/images/twitter.png" alt="Accent Twitter" class="socialFoot" /></a>
        <a target="_BLANK" href="https://www.instagram.com/accenttravelevents/"><img src="<?php echo $this->theme_url; ?>assets/images/instagram.png" alt="Accent Instagram" class="socialFoot" /></a>
        <?php /* <a target="_BLANK" href="https://www.linkedin.com/company/accent-travel-&-events"><img src="<?php echo $this->theme_url; ?>assets/images/linkedin.png" alt="Accent Linkedin" class="socialFoot" /></a> */ ?>
        <?php /* <a target="_BLANK" href="#"><img src="<?php echo $this->theme_url; ?>assets/images/google.png" alt="Accent Google" class="socialFoot" /></a> */ ?>
        <a target="_BLANK" href="https://www.youtube.com/channel/UCbkMWCvIEIMiJ-2S8Oz5sYA"><img src="<?php echo $this->theme_url; ?>assets/images/youtube.png" alt="Accent Youtube" class="socialFoot" /></a>
      </div>
      <div class="col-sm-6 col-lg-3">
        <h5>Accent Travel &amp; Events</h5>
        <?php 
        $this->_ci->load->model('Options_model');
        $menu = $this->_ci->Options_model->get('trip_cms_menu', 'footer_coloana_1');
        $this->_ci->load->library('FrontendMenuItem',array('key' => 'footer_coloana_1', 'children' => $menu),'frontend_menu_items_footer_coloana_1');
        $this->_ci->frontend_menu_items_footer_coloana_1->render_style_footer();
        ?>
      </div> 
      <div class="col-sm-6 col-lg-3">
        <h5>Servicii turistice</h5>
        <?php 
        $this->_ci->load->model('Options_model');
        $menu = $this->_ci->Options_model->get('trip_cms_menu', 'footer_coloana_2');
        $this->_ci->load->library('FrontendMenuItem',array('key' => 'footer_coloana_2', 'children' => $menu),'frontend_menu_items_footer_coloana_2');
        $this->_ci->frontend_menu_items_footer_coloana_2->render_style_footer();
        ?>
      </div> 
      <div class="col-sm-6 col-lg-3">
        <h5>Abonare Newsletter</h5>
        <form name="quick_subscribe_newsletter" action="<?php echo site_url('forms/newsletter/subscribe');?>" id="quick_subscribe_newsletter" method="POST" class="form-validate pb-3">
          <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
          <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
		  <input type="text" name="<?php echo md5($this->_ci->security->get_csrf_hash()); ?>" value="" class="form-sec" />
          <?php } ?>
          <div class="form-group">
            <p class="socialGo">Afla primul despre cele mai bune oferte!</p>
            <input type="email" class="form-control" name="email" id="numeNwsl" placeholder="Adresa de e-mail" />
            <br />
            <?php themeFunctions::loadAddons('captcha'); ?>
          </div>
          <button type="submit" id="recNwsl" name="recNwsl" class="btn btn-secondary">INREGISTRARE</button>
        </form>
        <div id="result_quick_subscribe_newsletter" class="form-group mb-0"></div>
      </div> 
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>