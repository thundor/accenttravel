<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script type="text/javascript">
(function($){$(function() {
  var $table_tbody = $('#notifications_list');
  var list_page = 1;
  function getList(){
    var $form_data = {};
    $form_data.page = list_page;
    $form_data.limit = 10;
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    $form_data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
    <?php } ?>
    $.ajax({
      url: "<?php echo $data['notifications_get_link']; ?>",
      method: 'POST',
      dataType: 'json',
      data: $form_data
    }).done(function(msg){
      if(msg.status == 'success'){
        var data = msg.data;
        console.log(data);
        var $pagination = $('ul.pagination');
        if($pagination.data("twbs-pagination")){
          $pagination.twbsPagination('destroy');
        }
        if(data.notifications.length && data.max_pages > 1){
          $pagination.twbsPagination({
            startPage: data.page,
            totalPages: data.max_pages,
            visiblePages: 10,
            first: "<<",
            prev: "<",
            next: ">",
            last: ">>",
            onPageClick: function (evt, page) {
              if(page == list_page){
                return;
              }
              list_page = page;
              getList();
            }
          });
        }
        $table_tbody.empty();
        if(!data.notifications.length){
          var $tr = $('<tr />');
          var $no_results_td = $('<td colspan="6" class="text-center">').html('<h3 class="mb-0">Nu au fost gasite notificari</h3>');
          $no_results_td.appendTo($tr);
          $tr.appendTo($table_tbody);
          return;
        }
        for(var i=0; i<data.notifications.length;i++){
          var notification = data.notifications[i];
          var $tr = $('<tr />');
          var $title_td = $('<td class="text-center">').html(notification.title);
          $title_td.appendTo($tr);
          
          var $type_td = $('<td class="text-center">');
          if(notification.type == 'hotel'){
            $type_td.html('Hotel');
          } else if(notification.type == 'package'){
            $type_td.html('Vacanta');
          } else if(notification.type == 'citybreak'){
            $type_td.html('City Break');
          } else if(notification.type == 'flight'){
            $type_td.html('Bilet avion');
          }
          if(notification.message){
            $type_td.addClass('hasTooltip').attr('title',notification.message);
          }
          $type_td.appendTo($tr);
          
          var $amount_td = $('<td class="text-center text-nowrap">').html(format_price(notification.amount,notification.currency));
          $amount_td.appendTo($tr);
          
          /* var $amount_new_td = $('<td class="text-center text-nowrap">').html(format_price(notification.amount_new,notification.currency));
          $amount_new_td.appendTo($tr);
          
          var diff_text = '-';
          if(notification.amount_new){
            var difference = (notification.amount - notification.amount_new) / notification.amount * 100;
            diff_text = Math.ceil(difference) + '%';
          }
          var $difference_td = $('<td class="text-center text-nowrap">').html(diff_text);
          if(notification.amount_new && difference<0){
            $difference_td.addClass('text-danger');
          }
          $difference_td.appendTo($tr); */
          
          var $date_expire_td = $('<td class="text-center">').html(notification.date_expire);
          $date_expire_td.appendTo($tr);
          
          
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_delete = true;
          var can_view = true;
          var can_any = can_delete;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-trash"></i></a>').attr('href',notification.delete_link));
            }
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-primary" target="_BLANK"><i class="fa fa-eye"></i></a>').attr('href',notification.view_link));
            }
          }
          $actions_td.appendTo($tr);
          $tr.appendTo($table_tbody);
        }
      } else {
        alert(msg.message);
      }
    });
  }
  function reloadList(){
    list_page = '1';
    getList();
  }
  $('#limit').on('change', function(){
    if(this.value=='0'){
      this.value = '';
    }
    reloadList();
  });
  $('#ordering').on('change', function(){
    getList();
  });
  $('#search_search_button').on('click', function(){
    reloadList();
  });
  $('#search_clear_button').on('click', function(){
    jQuery('#search').val(null);
    reloadList();
  });
  getList();
})})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>