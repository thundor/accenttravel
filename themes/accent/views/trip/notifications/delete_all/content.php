<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="container mt-3 mb-5">
  <h3>Sunteti sigur ca doriti sa va eliminati toate notificarile?</h3>
  <form action="" method="POST">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <button class="btn btn-danger" type="submit" name="confirm" value="1" >Da, elimina</button>
    <a class="btn btn-secondary" href="<?php echo $this->view_data['notifications_link']; ?>" >Nu, m-am razgandit</a>
  </form>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>