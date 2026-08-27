<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('ticketing_ticket'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/ticket/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/common/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/ticket/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/ticket/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/ticket/meta.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
$value_offset_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$ticket = $this->view_data['ticket'];
$editing = $ticket->id != 0;
$users = $this->view_data['users'];
$can_write = $this->_method !='view';
?>

<section class="forms">
  <div class="col-12">
  <div class="row">
    <?php if($can_write){ ?>
    <div class="col-lg-6">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <?php if ($ticket->id) { ?>
          <h2 class="h5 display">Tichet numar <?php echo $ticket->id; ?></h2>
        <?php } else { ?>
          <h2 class="h5 display">Adaugare tichet</h2>
        <?php } ?>
      </div>
      <div class="card-block">
      <form id="ticketForm" name="ticketForm" action="<?php echo site_url('backend/ticketing/save'); ?>" method="POST" onsubmit="return false;">
        <?php if($this->_ci->config->item('csrf_protection') === TRUE) { ?>
          <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
        <?php } ?>
      
        <?php if($can_write){ ?>
          <input type="hidden" id="task" name="task" value="" />
          <input type="hidden" name="id" value="<?php echo $ticket->id; ?>" />
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
      
        <!-- Creare reservation field -->
        <?php if (!$ticket->trip_order_id && $this->_ci->user->can('backend-trip-orders-add')) { ?>
        <div class="form-group row">
          <div class="<?php echo $value_offset_class . $value_class; ?>">
            <div class="custom-controls-stacked d-inline-block">
            <label class="custom-control custom-checkbox">
              <input id="ticket_create_trip_order_id" type="checkbox" name="create_trip_order_id" value="1" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Creeaza rezervare</span>
            </label>
            </div>
          </div>
        </div>
        <?php } elseif($ticket->trip_order_id) { ?>
        <a href="<?php echo base_url("backend/trip/orders/edit?id=" . $ticket->trip_order_id); ?>">Vezi comanda</a>
        <?php } ?>
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
    
    <?php if($ticket->id) { ?>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <h2 class="h5 display">Istoric Tichet</h2>
        </div>
        
        <div class="card-block">
          <div id="ticket_history">
            <table class="table table-striped table-hover table-bordered <?php echo $this->_controller; ?>_tickets_table">
              <thead>
              <tr>
                <th>Status</th>
                <th>Data modificarii </th>
                <th>Mesaj</th>
                <th>Consilier</th>
              </tr>
              </thead>
              <tbody>
              <?php
              $history = $this->view_data['history'];
              foreach ($history as $ticket)
              {
              ?>
                <tr>
                  <td>
                    <?php 
                    switch($ticket->status)
                    {
                      case 1:
                        echo 'Nou';
                        break;
                      case 2:
                        echo 'In lucru';
                        break;
                      case 3:
                        echo 'Finalizat';
                        break;
                    }
                    ?>
                  </td>
                  <td>
                    <?php echo $ticket->time_modified; ?>
                  </td>
                  <td>
                    <?php echo $ticket->message; ?> 
                  </td>
                  <td>
                    <?php echo $ticket->user_name; ?>
                  </td>
                </tr>
              <?php 
              }
              ?>
              </tbody>
            </table>
          </div>
          <nav aria-label="Pagination">
            <ul id="ticket_history_pagination" class="pagination justify-content-center justify-content-md-end"></ul>
          </nav>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>