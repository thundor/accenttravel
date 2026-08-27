<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script type="text/javascript">
(function($){$(function() {
  var $table_tbody = $('#orders_list');
  var list_page = 1;
  function getList(){
    var $form_data = {};
    $form_data.page = list_page;
    $form_data.limit = 10;
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    $form_data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
    <?php } ?>
    $.ajax({
      url: "<?php echo site_url('account/trip/orders/getlist'); ?>",
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
        if(data.orders.length && data.max_pages > 1){
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
        if(!data.orders.length){
          var $tr = $('<tr />');
          var $no_results_td = $('<td colspan="6" class="text-center">').html('<h3 class="mb-0">Nu au fost gasite comenzi</h3>');
          $no_results_td.appendTo($tr);
          $tr.appendTo($table_tbody);
          return;
        }
        for(var i=0; i<data.orders.length;i++){
          var order = data.orders[i];
          var $tr = $('<tr />');
          var $id_td = $('<td class="text-center">').html(order.id);
          $id_td.appendTo($tr);
          var status = 'Nefinalizata';
          if(order.status == 1){
            status = 'In procesare';
          } else if(order.status == 2){
            status = 'Confirmata';
          } else if(order.status == 3){
            status = 'Anulata';
          } else if(order.status == -1){
            status = '- Eroare -';
          }
          var $status_td = $('<td class="text-center text-nowrap">').html(status);
          if(order.status == -1){
            $status_td.addClass('bg-danger text-white');
          } else if(order.status == 1){
            $status_td.addClass('bg-primary text-white');
          } else if(order.status == 2){
            $status_td.addClass('bg-success text-white');
          } else if(order.status == 3){
            $status_td.addClass('bg-warning text-white');
          }
          if(order.message){
            $status_td.addClass('hasTooltip').attr('title',order.message);
          }
          $status_td.appendTo($tr);
          var $created_date_td = $('<td class="text-center">').html(order.time_created);
          $created_date_td.appendTo($tr);
          var $type_td = $('<td class="text-center">').html(order.type);
          $type_td.appendTo($tr);
          var $payment_method_td = $('<td class="text-center">').html(order.payment_method);
          $payment_method_td.appendTo($tr);
          
          var $discount_td = $('<td class="text-center text-nowrap">').html(order.coupon_percentage ? (order.coupon_percentage + ' %') : '-');
          if(order.coupon_percentage){
            $discount_td.attr('title', 'Cod cupon: ' + order.coupon_code);
          }
          $discount_td.appendTo($tr);
          
          var $price_td = $('<td class="text-center text-nowrap">').html(format_price(order.amount,order.currency));
          $price_td.appendTo($tr);
          
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_view = true;
          var can_any = can_view;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',order.view_link));
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