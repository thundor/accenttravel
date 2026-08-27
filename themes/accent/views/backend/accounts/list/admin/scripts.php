<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_user_id');
themeFunctions::jsLang('th_user_username');
themeFunctions::jsLang('th_user_lastname');
themeFunctions::jsLang('th_user_firstname');
themeFunctions::jsLang('th_user_email');
themeFunctions::jsLang('th_user_status');
themeFunctions::jsLang('th_user_role');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');
$role = $data['role'];
$default_session_data = array(
  'page' => 1,
  'ordering' => 'user_id DESC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/accounts/admin/list' . ($role?'/'.$role:''));
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.Admin_users_table>tbody');
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
    <?php if($role){ ?>
    $form_data.role = '<?php echo $role; ?>';
    <?php } ?>
    $.ajax({
      url: "<?php echo site_url('backend/accounts/admin/getlist'); ?>",
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
        for(var i=0; i<data.accounts.length;i++){
          var account = data.accounts[i];
          var $tr = $('<tr />');
          var $id_td = $('<td >').html(account.id);
          $id_td.appendTo($tr);
          var $username_td = $('<td >').html(account.username);
          $username_td.appendTo($tr);
          var $firstname_td = $('<td >').html(account.firstname);
          $firstname_td.appendTo($tr);
          var $lastname_td = $('<td >').html(account.lastname);
          $lastname_td.appendTo($tr);
          var $email_td = $('<td >').html(account.email);
          $email_td.appendTo($tr);
          var $status_td = $('<td class="text-center">').html(account.status == 1 ? '<?php echo lang('option_active'); ?>' : '<?php echo lang('option_blocked'); ?>');
          $status_td.appendTo($tr);
          var $role_td = $('<td class="text-center" >').html(account.role);
          $role_td.appendTo($tr);
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_view = account.can_view;
          var can_edit = account.can_edit;
          var can_delete = account.can_delete;
          var can_any = can_view || can_edit || can_delete;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',account.view_link));
            }
            if(can_edit){
              $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',account.edit_link));
            }
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-danger delete-account"><i class="fa fa-trash"></i></a>').attr('href',account.delete_link));
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
  $($table_tbody).on('click', 'a.delete-account',function(evt){
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
      'text': js_lang.th_user_id,
      'field': 'user_id',
      'children': [{
          'id': 'user_id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_user_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_user_id,
        },{
          'id': 'user_id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_user_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_user_id,
        }
      ]
    },{
      'text': js_lang.th_user_username,
      'field': 'user_username',
      'children': [{
          'id': 'user_username ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_user_username + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_user_username,
        },{
          'id': 'user_username DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_user_username + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_user_username,
        }
      ]
    },{
      'text': js_lang.th_user_lastname,
      'field': 'user_lastname',
      'children': [{
          'id': 'user_lastname ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_user_lastname + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_user_lastname,
        },{
          'id': 'user_lastname DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_user_lastname + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_user_lastname,
        }
      ]
    },{
      'text': js_lang.th_user_firstname,
      'field': 'user_firstname',
      'children': [{
          'id': 'user_firstname ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_user_firstname + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_user_firstname,
        },{
          'id': 'user_firstname DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_user_firstname + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_user_firstname,
        }
      ]
    },{
      'text': js_lang.th_user_email,
      'field': 'user_email',
      'children': [{
          'id': 'user_email ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_user_email + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_user_email,
        },{
          'id': 'user_email DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_user_email + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_user_email,
        }
      ]
    },{
      'text': js_lang.th_user_status,
      'field': 'user_status',
      'children': [{
          'id': 'user_status ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_user_status + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_user_status,
        },{
          'id': 'user_status DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_user_status + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_user_status,
        }
      ]
    },{
      'text': js_lang.th_user_role,
      'field': 'user_role',
      'children': [{
          'id': 'user_role ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_user_role + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_user_role,
        },{
          'id': 'user_role DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_user_role + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_user_role,
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
  }
  setSessionData();
  getList();
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>