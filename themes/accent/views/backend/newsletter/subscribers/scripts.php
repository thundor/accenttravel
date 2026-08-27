<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_id');
themeFunctions::jsLang('th_email');
themeFunctions::jsLang('th_account');
themeFunctions::jsLang('th_status');
themeFunctions::jsLang('th_date');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$default_session_data = array(
  'page' => 1,
  'ordering' => 'ns.id DESC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/newsletter/subscribers');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.Newsletter_Subscribers_table>tbody');
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
      url: "<?php echo site_url('backend/newsletter/subscribers/getlist'); ?>",
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
        for(var i=0; i<data.subscribers.length;i++){
          var subscriber = data.subscribers[i];
          var $tr = $('<tr />');
          var $id_td = $('<td class="text-center" />').html(subscriber.id);
          $id_td.appendTo($tr);
          var $email_td = $('<td class="text-center" />').html(subscriber.email);
          $email_td.appendTo($tr);
          var $account_td = $('<td class="text-center" />').html(subscriber.user_id > 0 ? 'Da' : 'Nu');
          $account_td.appendTo($tr);
          var $status_td = $('<td class="text-center" />').html(subscriber.status > 0 ? 'Abonat' : 'Dezabonat');
          $status_td.appendTo($tr);
          var $date_td = $('<td class="text-center" />').html(subscriber.time_created);
          $date_td.appendTo($tr);
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var $actions_btn_group = $('<div class="btn-group" />');
          $actions_btn_group.appendTo($actions_td);
          $actions_btn_group.append($('<button class="btn btn-primary btn-subscribe" title="Aboneaza"><i class="fa fa-user-plus"></i></button>').toggle(subscriber.status < 1).attr('data-email',subscriber.email));
          $actions_btn_group.append($('<button class="btn btn-secondary btn-unsubscribe" title="Dezaboneaza"><i class="fa fa-user-times"></i></button>').toggle(subscriber.status > 0).attr('data-email',subscriber.email));
          /* 
          var can_view = subscriber.can_view;
          var can_edit = subscriber.can_edit;
          var can_delete = subscriber.can_delete;
          var can_any = can_view || can_edit || can_delete;
          if(can_any){
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',subscriber.view_link));
            }
            if(can_edit){
              $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',subscriber.edit_link));
            }
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-danger delete-subscriber"><i class="fa fa-trash"></i></a>').attr('href',subscriber.delete_link));
            }
          }
          */
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
  $table_tbody.on('click', '.btn-subscribe,.btn-unsubscribe',function(evt){
    var $this = $(this);
    $this.prop('disabled',true);
    var subscribe = $this.hasClass('btn-subscribe');
    var form_data = {
      status: subscribe ? 1 : 0,
      email: $this.data('email')
    };
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    form_data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
    <?php } ?>
    $.ajax({
      url: "<?php echo site_url('forms/newsletter/subscribe'); ?>",
      method: 'POST',
      dataType: 'json',
      data: form_data
    }).done(function(msg){
      $this.prop('disabled',false);
      if(msg.status == 'success'){
        if(subscribe){
          $this.hide().next().show();
        } else {
          $this.hide().prev().show();
        }
      } else {
        alert(msg.message);
      }
    });
  });
  $table_tbody.on('click', 'a.delete-page',function(evt){
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