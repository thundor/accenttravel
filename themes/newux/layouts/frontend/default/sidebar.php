<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<aside class="social-sidebar">
  <div class="social-sidebar-content">
    <div class="search-sidebar">
      <form class="search-sidebar-form has-icon filterform" onsubmit="return false;">
        <label for="sidebar-query" class="fa fa-search"></label>
        <input id="sidebar-query" type="text" placeholder="Search" class="search-query filtertxt">
      </form>
    </div>
    <div class="clearfix"></div>
    <div class="user text-center">
      <i class="fa-1x glyphicon glyphicon-user"></i>
      <span><?php /* echo $this->session->userdata('fullName'); */ ?></span>
    </div>
    <div class="menu">
      <div class="menu-content">
        <ul id="social-sidebar-menu">
          <li class="active">
            <a href="<?php echo config_item('base_url');?>" target="_blank">
              <i class="fa fa-laptop"></i>
              <span><?php //echo trans('02');?></span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</aside>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>