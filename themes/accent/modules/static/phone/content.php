<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php if(isset($this->general_settings['contact_phone_number']) && strlen($this->general_settings['contact_phone_number'])) { ?>
<p class="socialTop1 pl-3 mt-1 float-left mb-lg-0"><i class="fa fa-phone"></i><a href="tel:<?php echo $this->general_settings['contact_phone_number']; ?>"><span aria-label="phone"> <?php echo isset($this->general_settings['contact_phone_text']) ? $this->general_settings['contact_phone_text'] : $this->general_settings['contact_phone_number'] ?></span></a></p>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>