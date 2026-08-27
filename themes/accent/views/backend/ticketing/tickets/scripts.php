<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('last_edit');
themeFunctions::jsLang('th_status');
themeFunctions::jsLang('th_ticket_id');
themeFunctions::jsLang('th_trip_order_id');
themeFunctions::jsLang('th_time_created');
themeFunctions::jsLang('th_time_updated');
themeFunctions::jsLang('th_time_span');
themeFunctions::jsLang('th_time_modified');
themeFunctions::jsLang('th_assignee');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$session_data = $this->tickets_session_data;
?>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.tickets_table>tbody');
  var list_page = 1;
  function getList(){
    var $form_data = {};
    $form_data.page = list_page;
    $form_data.limit = parseInt($('#limit').val());
    $form_data.search = '' + $('#search').val();
    $form_data.ordering = '' + $('#ordering').val();
    $form_data.filter_id = $('#filters_adv_id').val();
    $form_data.filter_trip_order_id = $('#filters_adv_trip_order_id').val();
    $form_data.filter_status = $('#filters_adv_status').val();
    $form_data.filter_time_created = $('#filters_adv_time_created').val();
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    $form_data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
    <?php } ?>
    $.ajax({
      url: "<?php echo site_url('backend/ticketing/getlist'); ?>",
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
        for(var i=0; i<data.tickets.length;i++){
          var ticket = data.tickets[i];
          var $tr = $('<tr />');
          var $id_td = $('<td class="text-center">').html(ticket.id);
          $id_td.appendTo($tr);
          var $trip_order_id_td = $('<td class="text-center" >').html(ticket.trip_order_id);
          $trip_order_id_td.appendTo($tr);
          
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
          var $time_created_td = $('<td class="text-center" >').html(ticket.time_created);
          $time_created_td.appendTo($tr);
          var $time_updated_td = $('<td class="text-center" >').html(ticket.time_updated);
          $time_updated_td.appendTo($tr);
          var $time_span_td = $('<td class="text-center" >').html(ticket.response_time);
          $time_span_td.appendTo($tr);
          var $time_modified_td = $('<td class="text-center" >').html(ticket.time_modified);
          $time_modified_td.appendTo($tr);
          var $user_name_td = $('<td class="text-center" >').html(ticket.user_name);
          $user_name_td.appendTo($tr);
          
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_view = ticket.can_view && !isNaN(ticket.trip_order_id);
          var can_edit = ticket.can_edit;
          var can_delete = ticket.can_delete;
          var can_any = can_view || can_edit || can_delete;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',ticket.view_link));
            }
            if(can_edit){
              $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',ticket.edit_link));
            }
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-danger delete-ticket"><i class="fa fa-trash"></i></a>').attr('href',ticket.delete_link));
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
  $($table_tbody).on('click', 'a.delete-ticket',function(evt){
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
  $('input, select').on('change input', function(){
    reloadList();
  });
  $("#filters_adv_time_created").caleran({
    startOnMonday: true,
    locale: 'ro',
    startEmpty: true,
    showFooter: false,
    autoCloseOnSelect: true,
    format: 'YYYY-MM-DD',
  });
  var reload_timer = null;
  function reloadList(){
    clearTimeout(reload_timer);
    reload_timer = setTimeout(function(){
      list_page = '1';
      getList();
    }, 300);
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
  /* $('#ordering').sortable({
    revert: true,
    items: ">span.sortable-item",
    handle: ".sortable-handle",
    start: function(e, ui){
      ui.placeholder.width(ui.item.width());
      ui.placeholder.height(ui.item.height());
    }
  }); */
  var ordering_values = [{
      'text': js_lang.last_edit,
      'field': 'time_modified',
      'children': [{
          'id': 'last_change ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.last_edit + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.last_edit,
        },{
          'id': 'last_change DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.last_edit + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.last_edit,
        }
      ]
    },{
      'text': js_lang.th_ticket_id,
      'field': 'id',
      'children': [{
          'id': 'id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_ticket_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_ticket_id,
        },{
          'id': 'id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_ticket_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_ticket_id,
        }
      ]
    },{
      'text': js_lang.th_trip_order_id,
      'field': 'trip_order_id',
      'children': [{
          'id': 'trip_order_id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_trip_order_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_trip_order_id,
        },{
          'id': 'trip_order_id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_trip_order_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_trip_order_id,
        }
      ]
    },{
      'text': js_lang.th_time_created,
      'field': 'time_created',
      'children': [{
          'id': 'time_created ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_time_created + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_time_created,
        },{
          'id': 'time_created DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_time_created + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_time_created,
        }
      ]
    },{
      'text': js_lang.th_time_updated,
      'field': 'time_updated',
      'children': [{
          'id': 'time_updated ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_time_updated + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_time_updated,
        },{
          'id': 'time_updated DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_time_updated + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_time_updated,
        }
      ]
    },{
      'text': js_lang.th_status,
      'field': 'status',
      'children': [{
          'id': 'status ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_status + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_status,
        },{
          'id': 'status DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_status + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_status,
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