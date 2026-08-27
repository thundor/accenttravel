<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $user = $this->_ci->user; ?>
<header class="header">
  <nav class="navbar fixed-top navbar-super social-navbar">
    <div class="col-12">
      <div class="navbar-holder d-flex align-items-center justify-content-between">
        <div class="navbar-header">
          <?php include 'headbar/navbar_header.php'; ?>
        </div>
        <ul id="page_actions" class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
          <?php include 'headbar/page_actions.php'; ?>
        </ul>
        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
          <li class="nav-item dropdown">
            <a id="user_actions" rel="nofollow" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="menu-btn">
              <i class="icon-user"></i>
            </a>
            <ul aria-labelledby="user_actions" class="dropdown-menu">
              <?php if($user->can('backend-account-profile-access')) { ?>
              <li class="text-left">
                <a href="<?php echo site_url('backend/account/profile'); ?>" class="nav-link profile"><i class="fa fa-user-plus"></i>Edit your profile</a>
              </li>
              <?php } ?>
              <li>
                <small><?php echo $user->firstname; ?> <?php echo $user->lastname; ?> (<?php echo $user->role ? $user->role : $user->type; ?>) <?php echo $user->email; ?></small>
              </li>
              <li class="pull-right">
                <a href="<?php echo site_url('backend/account/logout'); ?>" class="nav-link logout">Logout<i class="fa fa-sign-out"></i></a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>