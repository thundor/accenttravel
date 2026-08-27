<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/alert'); ?>
<?php /*
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
 */ ?>
<?php /*
<div id="page-content" class="w-100 fill-height">
  <?php echo $this->content(); ?>
</div> */ ?>
<main>
	<v-app :class="breakpoints">
		<Authpopup v-model="authpopup"></Authpopup>
		<Feedbackpopup v-model="feedbackpopup"></Feedbackpopup>
		<v-overlay v-model="loadingPage" id="page-loading" class="d-flex align-center justify-center" persistent eager :transition="'scale-transition'">
			<v-img src="<?php echo $this->theme_url ?>assets/images/gifmaker.gif" width="600" max-width="100%" cover eager></v-img>
			
			<?php if($this->_can_edit) { ?>
			<div class="position-fixed bottom-0 left-0 right-0" style="z-index:2">
				<div class="d-flex flex-wrap">
				<v-switch v-model="loadingPage" color="primary" hide-details density="compact">
					<template v-slot:label>
					  Loading
					</template>
				</v-switch>
				</div>
			</div>
			<?php } ?>
		</v-overlay>
		<component :is="loadViewAsync('home')">
			<?php if(isset(Modules::$page) && Modules::$page->id && ($this->_can_edit || file_exists($this->theme_path . 'views/partials/module/saved/module/custom/' . Modules::$page->id . '-content-above.json'))) { ?>
			<module id="content-above"></module>
			<?php } ?>
			<Content><template v-slot:default>
			<?php echo $this->content(); ?>
			<?php themeFunctions::loadAddons(__FILE__); ?>
			</template></Content>
			<?php if(isset(Modules::$page) && Modules::$page->id && ($this->_can_edit || file_exists($this->theme_path . 'views/partials/module/saved/module/custom/' . Modules::$page->id . '-content-below.json'))) { ?>
			<module id="content-below"></module>
			<?php } ?>
		</component>
	</v-app>
</main>

<?php themeFunctions::debugFileLine('end'); ?>