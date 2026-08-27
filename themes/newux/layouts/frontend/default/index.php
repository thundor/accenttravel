<?php
defined('ENVIRONMENT') OR die('Invalid access');
themeFunctions::includeAddon('scroll-into-view-if-needed');
themeFunctions::includeAddon('quasar');
themeFunctions::includeAddon('jquery');
themeFunctions::includeAddon('tooltip');
themeFunctions::includeAddon('bootstrap');
themeFunctions::includeAddon('jquery-ui');
themeFunctions::includeAddon('font-icons');
themeFunctions::includeAddon('backstretch');
themeFunctions::includeAddon('google-tag-manager');
themeFunctions::includeAddon('meta-pixel');
themeFunctions::includeAddon('custom/frontend');
themeFunctions::includeAddon('vue');
themeFunctions::includeAddon('vuetify');
// themeFunctions::includeAddon('vue-router');
themeFunctions::includeAddon('material-design-icons');
themeFunctions::includeAddon('vueuse');
themeFunctions::includeAddon('crypto-js');
// themeFunctions::includeAddon('facebook');
// themeFunctions::includeAddon('schema-org');

if($this->_ci->theme->_can_edit){
	themeFunctions::includeAddon('editors');
}

// themeFunctions::includeAddon('requirejs');
themeFunctions::includeAddon('v-phone-input');
themeFunctions::addIncludePath('includes/body/content.php', __DIR__ . '/content.php');
themeFunctions::addIncludePath('includes/body/content_after.php', __DIR__ . '/footer.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/stylesheets.php');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/scripts.php');
themeFunctions::loadLang('frontend');
themeFunctions::loadModule('cms/page',__DIR__ . '/content.php');
$this->content();