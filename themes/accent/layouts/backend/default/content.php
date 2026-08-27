<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/alert'); ?>
<?php include 'sidebar.php'; ?>
<div class="page home-page<?php //echo $this->_ci->input->cookie('backend-side-navbar-shrink') ? ' active' : '';?>">
  <?php include 'headbar.php'; ?>
  <?php include 'breadcrumbs.php'; ?>
  <div id="content">
    <div id="system_messages" class="col-12"><?php if($system_messages = $this->_ci->session->flashdata('flashmsgs')){
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
  <?php echo $this->content(); ?>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>