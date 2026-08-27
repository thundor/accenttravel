<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/alert'); ?>
<div id="system_messages" class="container"><?php if($system_messages = $this->_ci->session->flashdata('flashmsgs')){
  foreach($system_messages as $type=>$messages){
    $message_type = $type=='error' ? 'danger' : (in_array($type, array('success','danger','info','warning')) ? $type : 'info');
    foreach($messages as $message){
  ?>
  <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong><?php echo lang('alert_' . $message_type . '/html'); ?></strong> <?php echo $message; ?>
  </div>
  <?php
    }
  }
	$this->_ci->session->set_flashdata('flashmsg', null);
	$this->_ci->session->set_flashdata('flashmsgtype', null);
	$this->_ci->session->set_flashdata('flashmsgs', []);
} ?>
</div>
<div id="page-content" class="w-100 fill-height">
  <?php echo $this->content(); ?>
  <main class="w-100 d-flex flex-column fill-height align-center justify-center v-locale--is-ltr">
    <home-view></home-view>
  </main>
</div>
<?php //include __DIR__ . '/lorem_ipsum.html'; ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>