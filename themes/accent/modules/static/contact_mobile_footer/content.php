<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$enabled_items = $this->_ci->Options_model->getKeys('static_contact_mobile_footer_status');
$settings = $this->_ci->Options_model->get('static_contact_mobile_footer');
if($enabled_items && $settings){ ?>
<div class="phone_contact_round_container" id="toggle-calls"><i class="fa fa-3x fa-phone" aria-hidden="true"></i></div>
<div id="contact-mobile">
  <div class="contact-mobile-items d-flex visibility_hidden">
    <?php if(in_array('contact',$enabled_items)){ ?>
    <div class="contact-mobile-item type-contact flex-fill">
      <a href="tel:<?php echo $settings['contact_phone_number'] ?>" class="btn btn-primary btn-block rounded-0 btn-lg"><i class="fa fa-phone"></i> <span aria-label="Phone" class=""> <?php echo isset($settings['contact_text']) ? $settings['contact_text'] : $settings['contact_phone_number'] ?></span></a>
    </div>
    <?php } ?>
    <?php if(in_array('whatsapp',$enabled_items)){ ?>
    <div class="contact-mobile-item type-whatsapp flex-fill">
      <a target="_BLANK" href="https://api.whatsapp.com/send?phone=<?php echo $settings['whatsapp_phone_number'] ?>" class="btn btn-success btn-block rounded-0 btn-lg"><i class="fa fa-whatsapp"></i> <span aria-label="WhatsApp" class=""> <?php echo isset($settings['whatsapp_text']) ? $settings['whatsapp_text'] : $settings['whatsapp_phone_number'] ?></span></a>
    </div>
    <?php } ?>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>