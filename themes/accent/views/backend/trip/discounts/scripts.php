<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_id');
themeFunctions::jsLang('th_type');
themeFunctions::jsLang('th_percentage');
themeFunctions::jsLang('th_status');
themeFunctions::jsLang('th_type_id');
themeFunctions::jsLang('th_name');
themeFunctions::jsLang('th_date_start');
themeFunctions::jsLang('th_date_expire');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$default_session_data = array(
  'page' => 1,
  'ordering' => 'id DESC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/trip/discounts');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.Trip_Discounts_table>tbody');
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
      url: "<?php echo site_url('backend/trip/discounts/getlist'); ?>",
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
        for(var i=0; i<data.discounts.length;i++){
          var discount = data.discounts[i];
          var $tr = $('<tr />');
          var $id_td = $('<td class="text-center" />').html(discount.id);
          $id_td.appendTo($tr);
          var $type_td = $('<td class="text-center" />').html(discount.type);
          $type_td.appendTo($tr);
          
          var $type_id_td = $('<td class="text-center" />').html(discount.type_id === null ? '- nelimitat -' : discount.type_id);
          $type_id_td.appendTo($tr);
          
          var $name_td = $('<td class="text-center" />').html(discount.name);
          $name_td.appendTo($tr);
          
          var $percentage_td = $('<td class="text-center" />').html(discount.percentage + '%');
          $percentage_td.appendTo($tr);
          
          
          var $date_start_td = $('<td class="text-center" />').html(discount.date_start === null ? '-' : discount.date_start);
          $date_start_td.appendTo($tr);
          var $date_expire_td = $('<td class="text-center" />').html(discount.date_expire === null ? '-' : discount.date_expire);
          $date_expire_td.appendTo($tr);
          
          var status = '';
          switch(parseInt(discount.status)){
            case 1: status = 'Activ'; break;
            case 0: status = 'Inactiv'; break;
          }
          var $status_td = $('<td class="text-center" />').html(status);
          $status_td.appendTo($tr);
          
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_view = discount.can_view;
          var can_edit = discount.can_edit;
          var can_delete = discount.can_delete;
          var can_change_status = discount.can_change_status;
          var can_any = can_view || can_edit || can_delete || can_change_status;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',discount.view_link));
            }
            if(can_edit){
              $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',discount.edit_link));
            }
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-danger delete-discount"><i class="fa fa-trash"></i></a>').attr('href',discount.delete_link));
            }
            if(can_change_status){
              if(parseInt(discount.status) == 1){
                $actions_btn_group.append($('<a class="btn btn-warning unpublish-discount" title="Dezactiveaza"><i class="fa fa-thumbs-down"></i></a>').attr('href',discount.unpublish_link));
              } else {
                $actions_btn_group.append($('<a class="btn btn-success publish-discount" title="Activeaza"><i class="fa fa-thumbs-up"></i></a>').attr('href',discount.publish_link));
              }
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
  var ordering_values = [{
      'text': js_lang.th_id,
      'field': 'id',
      'children': [{
          'id': 'id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_id,
        },{
          'id': 'id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_id,
        }
      ]
    },{
      'text': js_lang.th_percentage,
      'field': 'percentage',
      'children': [{
          'id': 'percentage ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_percentage + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_percentage,
        },{
          'id': 'percentage DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_percentage + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_percentage,
        }
      ]
    },{
      'text': js_lang.th_type,
      'field': 'type',
      'children': [{
          'id': 'type ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_type + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_type,
        },{
          'id': 'type DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_type + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_type,
        }
      ]
    },{
      'text': js_lang.th_date_start,
      'field': 'date_start',
      'children': [{
          'id': 'date_start ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_date_start + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_date_start,
        },{
          'id': 'date_start DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_date_start + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_date_start,
        }
      ]
    },{
      'text': js_lang.th_date_expire,
      'field': 'date_expire',
      'children': [{
          'id': 'date_expire ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_date_expire + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_date_expire,
        },{
          'id': 'date_expire DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_date_expire + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_date_expire,
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