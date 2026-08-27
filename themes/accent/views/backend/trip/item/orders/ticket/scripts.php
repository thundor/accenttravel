<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$ticket = $this->view_data['ticket'];
$can_write = $this->_method !='view';
if($can_write){ ?>
<script>
(function($){
  var form_ready = true;
  var $message_container = $('#result_ticketForm');
  
  function submitFormSubmitCallback($form,resp,$error_container){
    form_ready = true;
    if(resp.status !== 'success'){
      return true;
    }
    $('#ticket_id').val(resp.data.ticket_id);
    $('#ticket_title').text('Tichet numar ' + resp.data.ticket_id);
    getList();
    return true;
  }
  $('#ticketForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
  
  $('#ticket_user_id').select2_4({theme:'bootstrap',placeholder:'Alegeti consilierul', width: '100%'});
  
  var $table_tbody = $('table.tickets_table>tbody');
  function getList(){
    var $form_data = {};
    $form_data.id = $('#ticket_id').val();
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    $form_data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
    <?php } ?>
    $.ajax({
      url: "<?php echo site_url('backend/ticketing/getlisthistory'); ?>",
      method: 'POST',
      dataType: 'json',
      data: $form_data
    }).done(function(msg){
      if(msg.status == 'success'){
        var data = msg.data;
        console.log(data);
        $table_tbody.empty();
        for(var i=0; i<data.tickets.length;i++){
          var ticket = data.tickets[i];
          var $tr = $('<tr />');
          var status = '-';
          if(ticket.status == 1){
            status = 'Nou';
          } else if(ticket.status == 2){
            status = 'In lucru';
          } else if(ticket.status == 3){
            status = 'Finalizata';
          }
          var $status_td = $('<td class="text-center text-nowrap">').html(status);
          if(ticket.status == 1){
            $status_td.addClass('bg-primary text-white');
          } else if(ticket.status == 3){
            $status_td.addClass('bg-success text-white');
          } else if(ticket.status == 2){
            $status_td.addClass('bg-warning text-white');
          }
          $status_td.appendTo($tr);
          var $time_modified_td = $('<td class="text-center" >').html(ticket.time_modified);
          $time_modified_td.appendTo($tr);
          var $message_td = $('<td class="text-center" >').html(ticket.message);
          $message_td.appendTo($tr);
          var $user_name_td = $('<td class="text-center" >').html(ticket.user_name);
          $user_name_td.appendTo($tr);
          $tr.appendTo($table_tbody);
        }
      } else {
        alert(msg.message);
      }
    });
  }
  getList();
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  