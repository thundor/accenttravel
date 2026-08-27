<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_order_id');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$default_session_data = array(
  'page' => 1,
  'ordering' => 'id DESC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/trip/orders/list');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/javascript">
(function($){
$.extend(
{
    redirectPost: function(location, args)
    {
        var form = $('<form target="_BLANK"></form>');
        form.attr("method", "post");
        form.attr("action", location);

        $.each( args, function( key, value ) {
            var field = $('<input></input>');

            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);

            form.append(field);
        });
        $(form).appendTo('body').submit();
    }
});
	$('.export-button').on('click', function(){
	  var type = $(this).data('type');
    $.redirectPost('<?php echo site_url('backend/trip/orders/getlist'); ?>', {
		<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
		'<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>",
		<?php } ?>
		xls: 1,
		limit: type == 'current_page' ? $('#limit').val() : 0,
		search: type == 'all' ? '' + $('#search').val() : '',
		page: type == 'current_page' ? list_page : 1,
		ordering: $('#ordering').val(),
	});
  });
  var $table_tbody = $('table.Trip_Orders_table>tbody');
  var list_page = 1;
  function getList(){
    var $form_data = {};
    $form_data.page = list_page;
    $form_data.limit = parseInt($('#limit').val());
    $form_data.search = '' + $('#search').val();
    $form_data.ordering = '' + $('#ordering').val();
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    $form_data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
    <?php } ?>
    $.ajax({
      url: "<?php echo site_url('backend/trip/orders/getlist'); ?>",
      method: 'POST',
      dataType: 'json',
      data: $form_data
    }).done(function(msg){
      if(msg.status == 'success'){
        var data = msg.data;
        console.log(data);
        var $pagination = $('ul.create-pagination');
        if($pagination.data("twbs-pagination")){
          $pagination.twbsPagination('destroy');
        }
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
        $table_tbody.empty();
        for(var i=0; i<data.orders.length;i++){
          var order = data.orders[i];
          var $tr = $('<tr />');
          var $id_td = $('<td class="text-center">').html(order.id);
          $id_td.appendTo($tr);
          var $provider_td = $('<td class="text-center">').html(order.provider);
          $provider_td.appendTo($tr);
          var $trip_order_id_td = $('<td class="text-center">').html(order.trip_order_id);
          $trip_order_id_td.appendTo($tr);
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
          var $type_td = $('<td class="text-center">').html(order.type);
          $type_td.appendTo($tr);
          var full_name = order.user_lastname ? order.user_lastname : '';
          if(order.user_firstname){
            full_name += ' ' + order.user_firstname;
          }
          var $client_name_td = $('<td class="text-center">').html(full_name);
          $client_name_td.appendTo($tr);
          var $client_email_td = $('<td class="text-center">').html(order.user_email);
          $client_email_td.appendTo($tr);
          var $payment_method_td = $('<td class="text-center p-0 align-middle">').html('<small>' + (order.payment_method || '') + ' ' + (order.payment_gateway || '') + '</small>');
          $payment_method_td.appendTo($tr);
          var price = '';
          if(order.amount){
            price = parseFloat(order.amount).toLocaleString('ro');
          }
          if(order.currency){
            if(order.currency == 'RON'){
              price += ' Lei';
            } else {
              price += ' <?php echo $this->_ci->currency_symbol; ?>';
            }
          }
          var $price_td = $('<td class="text-center text-nowrap">').html(price);
          $price_td.appendTo($tr);
          // var $created_name_td = $('<td >').html(order.created_by);
          // $created_name_td.appendTo($tr);
          // var $created_email_td = $('<td >').html('');
          // $created_email_td.appendTo($tr);
          var $created_date_td = $('<td class="text-center">').html(order.time_created);
          $created_date_td.appendTo($tr);
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_view = order.can_view;
          var can_edit = order.can_edit;
          var can_delete = order.can_delete;
          var can_any = can_view || can_edit || can_delete;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            // if(can_view){
              // $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',order.view_link));
            // }
            if(can_edit){
              $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',order.edit_link));
            } else if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',order.view_link));
            }
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-danger delete-order"><i class="fa fa-trash"></i></a>').attr('href',order.delete_link));
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
  $($table_tbody).on('click', 'a.delete-order',function(evt){
    var that = this;
    var $that = $(that);
    if($that.data('confirmed')){
      return true;
    } else {
      swal({
        title: 'Aceasta actiune este permanenta!',
        text: js_lang.confirm_delete,
        icon: 'warning',
        buttons: {
          cancel: "Nu... m-am razgandit.",
          delete: {
            text: "Da. Sterge!",
            value: true,
            className: 'btn-danger'
          }
        },
        dangerMode: true
      }).then(function(value){
        if(value){
          $that.data('confirmed',true);
          $that[0].click();
        }
      });
      return false;
    }
  });
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
  var ordering_values = [{
      'text': js_lang.th_order_id,
      'field': 'id',
      'children': [{
          'id': 'id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_order_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_order_id,
        },{
          'id': 'id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_order_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_order_id,
        }
      ]
    }
  ];
  $('#ordering').select2_4({
    theme: 'bootstrap',
    width: '100%',
    placeholder: $('#ordering>option:first-child').text(),
    escapeMarkup: function (markup) { 
      return markup; 
    },
    templateResult: function (item) {
      var text = item.text;
      if(item.display){
        text = item.display;
      }
      if(item.icon){
        text += ' ' + item.icon;
      }
      return text;
    },
    allowClear:true,
    templateSelection: function(item,a) {
      if(!item.group){
        return item.text;
      }
      return '<b>' + js_lang.ordering_by + ' ' + item.group + '</b> ' + item.display + ' ' + item.icon;
    },
    data: ordering_values
  });
  function setSessionData(){
    $('#search').val('<?php echo $session_data['search']; ?>');
    $('#limit').val('<?php echo $session_data['limit']; ?>');
    $('#ordering').val('<?php echo $session_data['ordering']; ?>').trigger('change.select2_4');
    list_page = <?php echo (int)$session_data['page']; ?>;
    if(list_page < 1){
      list_page = 1;
    }
  }
  setSessionData();
  getList();
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>