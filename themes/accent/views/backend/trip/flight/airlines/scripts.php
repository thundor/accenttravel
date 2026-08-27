<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_code');
themeFunctions::jsLang('th_name');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$default_session_data = array(
  'page' => 1,
  'ordering' => 'code DESC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/trip/flight/airlines');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.trip_flight_airlines_table>tbody');
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
      url: "<?php echo site_url('backend/trip_flight/airlines/getlist'); ?>",
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
        for(var i=0; i<data.airlines.length;i++){
          var airline = data.airlines[i];
          var $tr = $('<tr />');
          var $code_td = $('<td class="text-center" />').html(airline.code);
          $code_td.appendTo($tr);
          var $name_td = $('<td class="text-center" />').html(airline.name).attr('title', airline.original_name);
          $name_td.appendTo($tr);
          var $image_td = $('<td class="text-center" />').toggleClass('p-0', airline.image ? true : false).html(airline.image ? '<img src="<?php echo $this->theme_url; ?>assets/images/' + airline.image + '" alt="-fisier inexistent-" style="max-width:50px; max-height:45px;"/>' : '-');
          $image_td.appendTo($tr);
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_view = airline.can_view;
          var can_edit = airline.can_edit;
          var can_delete = airline.can_delete;
          var can_any = can_view || can_edit || can_delete;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',airline.view_link));
            }
            if(can_edit){
              $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',airline.edit_link));
            }
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-danger" onclick="return confirm(js_lang.confirm_delete);"><i class="fa fa-trash"></i></a>').attr('href',airline.delete_link));
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
      'text': js_lang.th_code,
      'field': 'code',
      'children': [{
          'id': 'code ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_code + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_code,
        },{
          'id': 'code DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_code + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_code,
        }
      ]
    },{
      'text': js_lang.th_name,
      'field': 'name',
      'children': [{
          'id': 'name ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_name + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_name,
        },{
          'id': 'name DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_name + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_name,
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