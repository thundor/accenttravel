<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php 
$hotel_details = $this->view_data['hotel_details'];
$this->hotel_details = &$hotel_details;
$this->_ci->load->model('Trip/Hotels_model');
$hotel_search_data = $this->_ci->Hotels_model->getSearchData($hotel_details->Id);
$this->hotel_search_data = &$hotel_search_data;
$rooms_for_package = $this->view_data['rooms_for_package'];
$this->rooms_for_package = &$rooms_for_package;
$room_objects = $this->view_data['room_objects'];
$this->room_objects = &$room_objects;
$room_codes = $this->view_data['room_codes'];
$this->room_codes = &$room_codes;
themeFunctions::includeAddon('forms-validation');
themeFunctions::includeAddon('formatter');
themeFunctions::includeAddon('datepicker');
themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/booking/scripts.php');
themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/booking/stylesheets.php');
themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/booking/meta.php');

themeFunctions::loadAddons(__FILE__);
themeFunctions::debugFileLine('start'); ?>
<div class="container">
  <?php include 'booking/details.php'; ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>