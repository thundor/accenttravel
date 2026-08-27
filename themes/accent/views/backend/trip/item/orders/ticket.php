<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/ticket/scripts.php'); ?>
<?php 
$client_label_size = array();
$client_label_size['xl'] = 3;
$client_label_size['lg'] = 4;
$client_label_size['md'] = 4;
$client_label_size['sm'] = 4;
$client_label_size[''] = 12;
$client_label_class = 'pt-1 text-sm-right';
$client_value_class = '';
$client_value_offset_class = '';
foreach($client_label_size as $k=>$v){
  $client_label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $client_value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . $v;
  $client_value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$order = $this->view_data['order'];
$ticket = $this->view_data['ticket'];
$editing = $ticket->id != 0;
$users = $this->view_data['users'];
$can_write = $this->_method !='view';
?>
<div class="row">
    <?php if($can_write){ ?>
    <div class="col-lg-6">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <?php if ($ticket->id) { ?>
          <h2 class="h5 display" id="ticket_title">Tichet numar <?php echo $ticket->id; ?></h2>
        <?php } else { ?>
          <h2 class="h5 display" id="ticket_title">Adaugare tichet</h2>
        <?php } ?>
      </div>
      <div class="card-block">
      <form id="ticketForm" name="ticketForm" action="<?php echo site_url('backend/ticketing/save_from_order'); ?>" method="POST" onsubmit="return false;">
        <?php if($this->_ci->config->item('csrf_protection') === TRUE) { ?>
          <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
        <?php } ?>
      
        <?php if($can_write){ ?>
          <input type="hidden" id="task" name="task" value="" />
          <input type="hidden" id="ticket_id" name="id" value="<?php echo $ticket->id; ?>" />
          <input type="hidden" id="ticket_order_id" name="order_id" value="<?php echo $order->id; ?>" />
        <?php } ?>
        
        <!-- Message field -->
        <div class="form-group row">
          <label for="ticket_message" class="<?php echo $label_class; ?>">
            <?php 
            if (!$ticket->id) {
              echo "Observatie initiala";
            } else {
              echo "Observatie noua";
            }
            ?>             
          </label>
          <div class="<?php echo $value_class; ?>">
            <textarea id="ticket_message" name="message" class="form-control" required rows="6"></textarea>
          </div>
        </div>
      
        <!-- Status field -->
        <?php //if($editing) { ?>
        <div class="form-group row">
          <label for="user_id" class="<?php echo $label_class; ?>">Stare tichet</label>
          <div class="<?php echo $value_class; ?>">
            <select name="status" class="form-control">
              <option value="1" <?php if ($ticket->status == 1) echo 'selected';?>>Nou</option>
              <option value="2" <?php if ($ticket->status == 2) echo 'selected';?>>In lucru</option>
              <option value="3" <?php if ($ticket->status == 3) echo 'selected';?>>Finalizat</option>
            </select>
          </div>
        </div>
        <?php //} ?>
      
        <!-- Assigned user field -->
        <div class="form-group row">
          <label for="user_id" class="<?php echo $label_class; ?>">Consilier asignat</label>
          <div class="<?php echo $value_class; ?>">
            <select name="user_id" id="ticket_user_id" class="form-control">
              <option value="0">Alege un consilier</option>
              <?php 
              foreach ($users as $user) {
                $selected = '';
                if ($user->user_id == $ticket->user_id) $selected = ' selected'; 
              ?>
              
                <option value="<?php echo $user->user_id;?>" <?php echo $selected;?>>
                  <?php echo $user->user_firstname . ', ' . $user->user_lastname; ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-success pull-right">
            <i class="fa fa-plus"></i>
            <?php 
            if (!$ticket->id) {
              echo "Adauga";
            } else {
              echo "Modifica";
            }
            ?> 
          </button>
        </div>
      </form>
      <div id="result_ticketForm" class="form-group"></div>
      </div>
    </div>
    </div>
    <?php } ?>
    
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <h2 class="h5 display">Istoric Tichet</h2>
        </div>
        
        <div class="card-block">
          <div id="ticket_history">
            <table class="table table-striped table-hover table-bordered tickets_table">
              <thead>
              <tr>
                <th>Status</th>
                <th>Data modificarii </th>
                <th>Mesaj</th>
                <th>Consilier</th>
              </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <nav aria-label="Pagination">
            <ul id="ticket_history_pagination" class="pagination justify-content-center justify-content-md-end"></ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>