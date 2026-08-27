<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/index/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php'); ?>
<?php 
$user = $this->_ci->user;
$db = $this->_ci->db;
?>
<section class="forms col-lg-12">
  <div class="row"><?php
if($user->role=='consilier'){ ?>
  <div class="col-lg-6 mb-3">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <h2 class="h5 display">Tichete asociate mie</h2>
      </div>
      <div class="card-block">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-nowrap text-center" style="width:1%;">ID</th>
              <th class="text-nowrap text-center">Status</th>
              <th style="width:50%">Mesaj</th>
              <th class="text-nowrap text-center">Ultima modificare</th>
            </tr>
          </thead>
          <tbody><?php
            $db->select('*');
            $db->select('IFNULL(time_modified, time_created) as last_edit');
            $db->where('user_id',$user->id);
            $db->where('status>0');
            $db->order_by('last_edit', 'DESC');
            $q = $db->get('ac_ticket',10,0);
            $tickets = $q->result();
            $statuses = array(
              1 => 'Nou',
              2 => 'In lucru',
              3 => 'Finalizat',
            );
            if($tickets){
              foreach($tickets as $ticket){ ?>
              <tr>
                <td class="text-nowrap text-center"><?php echo $ticket->id; ?></td>
                <td class="text-nowrap text-center"><?php echo isset($statuses[(int)$ticket->status]) ? $statuses[(int)$ticket->status] : '-'; ?></td>
                <td><?php echo $ticket->message; ?></td>
                <td class="text-nowrap text-center"><?php echo $ticket->last_edit; ?></td>
              </tr>
              <?php
              }
            } else { ?>
            <tr colspan="3"><td>Niciun tichet asociat</td></tr>
            <?php
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php
}
if($user->can('backend-ticketing-access')){ ?>
  <div class="col-lg-6 mb-3">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <h2 class="h5 display">Ultimele tichete inregistrate</h2>
      </div>
      <div class="card-block">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-nowrap text-center" style="width:1%;">ID</th>
              <th class="text-nowrap text-center">Status</th>
              <th style="width:50%">Mesaj</th>
              <th class="text-nowrap text-center">Ultima modificare</th>
            </tr>
          </thead>
          <tbody><?php
            $db->select('*');
            $db->select('IFNULL(time_modified, time_created) as last_edit');
            $db->where('status>0');
            $db->order_by('last_edit', 'DESC');
            $q = $db->get('ac_ticket',10,0);
            $tickets = $q->result();
            $statuses = array(
              1 => 'Nou',
              2 => 'In lucru',
              3 => 'Finalizat',
            );
            if($tickets){
              foreach($tickets as $ticket){ ?>
              <tr>
                <td class="text-nowrap text-center"><?php echo $ticket->id; ?></td>
                <td class="text-nowrap text-center"><?php echo isset($statuses[(int)$ticket->status]) ? $statuses[(int)$ticket->status] : '-'; ?></td>
                <td><?php echo $ticket->message; ?></td>
                <td class="text-nowrap text-center"><?php echo $ticket->last_edit; ?></td>
              </tr>
              <?php
              }
            } else { ?>
            <tr colspan="3"><td>Niciun tichet asociat</td></tr>
            <?php
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php
}
?>
  </div>
</section>
<?php themeFunctions::debugFileLine('end'); ?>