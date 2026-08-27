<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_title');
themeFunctions::jsLang('th_email');
themeFunctions::jsLang('th_phone');
themeFunctions::jsLang('th_fullname');
themeFunctions::jsLang('th_status');
themeFunctions::jsLang('th_type');
themeFunctions::jsLang('th_amount');
themeFunctions::jsLang('th_amount_new');
themeFunctions::jsLang('th_date_expire');
themeFunctions::jsLang('th_time_created');
themeFunctions::jsLang('th_times_checked');
themeFunctions::jsLang('th_time_last_checked');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$default_session_data = array(
  'page' => 1,
  'ordering' => 'ns.id DESC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/trip/notifications');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.Trip_Notifications_table>tbody');
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
      url: "<?php echo site_url('backend/trip/notifications/getlist'); ?>",
      method: 'POST',
      dataType: 'json',
      data: $form_data
    }).done(function(msg){
      if(msg.status == 'success'){
        var data = msg.data;
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
        for(var i=0; i<data.notifications.length;i++){
          var notification = data.notifications[i];
          var $tr = $('<tr />');
          
          var type = '';
          switch(notification.type){
            case 'hotel': type = 'Hotel'; break;
            case 'package': type = 'Vacanta'; break;
            case 'citybreak': type = 'City Break'; break;
            case 'flight': type = 'Bilet avion'; break;
            default : type = notification.type; break;
          }
          var $type = $('<span />').text(type);
          
          var $type_td = $('<td class="text-center" />').html($type);
          $type_td.appendTo($tr);
          var $title_td = $('<td class="text-center" />').html(notification.title);
          $title_td.appendTo($tr);
          var $amount_td = $('<td class="text-center" />').html(format_price(notification.amount, notification.currency));
          $amount_td.appendTo($tr);
          var $fullname_td = $('<td class="text-center" />').html(notification.fullname);
          $fullname_td.appendTo($tr);
          var $email_td = $('<td class="text-center" />').html(notification.email);
          $email_td.appendTo($tr);
          var $phone_td = $('<td class="text-center" />').html(notification.phone);
          $phone_td.appendTo($tr);
          var status = '';
          var $status = $('<span />');
          var $status_td = $('<td class="text-center" />');
          switch(parseInt(notification.status)){
            case -2: status = 'Arhivat'; break;
            case -1: status = 'Expirat'; $status_td.addClass('bg-warning text-white'); break;
            case 0: status = 'Anulat'; $status_td.addClass('bg-danger text-white'); break;
            case 1: status = 'Activ'; $status_td.addClass('bg-success text-white'); break;
            case 2: status = 'Pozitiv'; $status_td.addClass('bg-primary text-white'); break;
            default : status = notification.status; break;
          }
          $status.text(status).attr({
            'data-toggle' : 'tooltip',
            'title' : notification.message,
          });
          $status_td.append($status);
          $status_td.appendTo($tr);
          var $date_expire_td = $('<td class="text-center" />').html(notification.date_expire);
          $date_expire_td.appendTo($tr);
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var $actions_btn_group = $('<div class="btn-group btn-group-sm" />');
          $actions_btn_group.appendTo($actions_td);
          var can_view = parseInt(notification.status)>0;
          var can_delete = parseInt(notification.status)>0;
          if(can_delete){
            $actions_btn_group.append($('<a class="btn btn-secondary" target="_BLANK" title="Anuleaza"><i class="fa fa-trash"></i></a>').attr('href',notification.delete_link));
          }
          if(can_view){
            $actions_btn_group.append($('<a class="btn btn-primary" title="Vezi oferta" target="_BLANK"><i class="fa fa-eye"></i></a>').attr('href',notification.view_link));
          }
          $actions_btn_group.append($('<a class="btn btn-secondary" title="Vezi toate notificarile acestui client" target="_BLANK"><i class="fa fa-user"></i></a>').attr('href',notification.notifications_link));
          
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
  var ordering_values = [{
      'text': js_lang.th_id,
      'field': 'id',
      'children': [{
          'id': 'ns.id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_id,
        },{
          'id': 'ns.id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_id,
        }
      ]
    },{
      'text': js_lang.th_email,
      'field': 'email',
      'children': [{
          'id': 'ns.email ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_email + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_email,
        },{
          'id': 'ns.email DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_email + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_email,
        }
      ]
    },{
      'text': js_lang.th_date,
      'field': 'time_created',
      'children': [{
          'id': 'ns.time_created ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_date + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_date,
        },{
          'id': 'ns.time_created DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_date + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_date,
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