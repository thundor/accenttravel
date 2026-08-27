<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$selected_value = isset($data['selected']) ? $data['selected'] : '';
if($selected_value){
  $this->_ci->load->model('Account_model');
  $account = $this->_ci->Account_model->getAccounts(array('return_row'=>true,'id'=>$selected_value,'type'=>'customer','select'=>array('CONCAT_WS(", ",`user_lastname`, `user_firstname`,`user_email`,`phone`) AS "text"')));
  if($account){ ?>
  <option value="<?php echo (int)$selected_value; ?>" selected="selected" ><?php echo htmlspecialchars($account->text); ?></option>
  <?php
  }
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>