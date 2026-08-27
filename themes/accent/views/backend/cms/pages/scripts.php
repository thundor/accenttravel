<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_page_slug');
themeFunctions::jsLang('th_page_title');
themeFunctions::jsLang('th_page_sort_order');
themeFunctions::jsLang('th_page_status');
themeFunctions::jsLang('th_page_id');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$default_session_data = array(
  'page' => 1,
  'ordering' => 'slug DESC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/cms/pages/list');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
$type = $this->_ci->uri->rsegment(2);
if(!in_array($type, array('static', 'dynamic', 'default'))){
  $type = null;
}
?>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.CMS_Pages_table>tbody');
  var list_page = 1;
  var pages_type = <?php echo json_encode($type); ?>;
  function getList(){
    var $form_data = {};
    $form_data.type = pages_type;
    $form_data.page = list_page;
    $form_data.limit = parseInt($('#limit').val());
    $form_data.search = '' + $('#search').val();
    $form_data.ordering = '' + $('#ordering').val();
    $form_data.type = '' + $('#type').val();
    $form_data.blog = '' + $('#blog').val();
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    $form_data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
    <?php } ?>
    $.ajax({
      url: "<?php echo site_url('backend/cms/pages/getlist'); ?>",
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
        for(var i=0; i<data.pages.length;i++){
          var page = data.pages[i];
          var $tr = $('<tr />');
          var $page_id_td = $('<td class="text-center" />').html(page.page_id);
          $page_id_td.appendTo($tr);
          var $title_td = $('<td class="text-center" />').html(page.title);
          $title_td.appendTo($tr);
          var $slug_td = $('<td class="text-center" />').html(page.slug);
          $slug_td.appendTo($tr);
          if(!pages_type){
            var page_type = !page.route && !page.params ? 'Statica' : (page.route && !page.params ? 'Implicita' : 'Dinamica');
            var $type_td = $('<td class="text-center" />').html(page_type);
            $type_td.appendTo($tr);
          }
          var $sort_order_td = $('<td class="text-center" />').html(page.sort_order);
          $sort_order_td.appendTo($tr);
          var $status_td = $('<td class="text-center" />').html(parseInt(page.status) ? '<span class="bg-success text-white">Activ</label>' : '<span class="bg-danger text-white">Inactiv</label>');
          $status_td.appendTo($tr);
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var can_view = page.can_view;
          var can_edit = page.can_edit;
          var can_delete = page.can_delete;
          var can_any = can_view || can_edit || can_delete;
          if(can_any){
            var $actions_btn_group = $('<div class="btn-group" />');
            $actions_btn_group.appendTo($actions_td);
            if(can_view){
              $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',page.view_link));
            }
            if(can_edit){
              $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',page.edit_link));
            }
            if(can_delete){
              $actions_btn_group.append($('<a class="btn btn-danger delete-page"><i class="fa fa-trash"></i></a>').attr('href',page.delete_link));
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
  $('#blog').on('change', function(){
    getList();
  });
  $('#type').on('change', function(){
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
      'text': js_lang.th_page_id,
      'field': 'page_id',
      'children': [{
          'id': 'p.page_id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_page_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_page_id,
        },{
          'id': 'p.page_id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_page_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_page_id,
        }
      ]
    },{
      'text': js_lang.th_page_slug,
      'field': 'page_slug',
      'children': [{
          'id': 'slug ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_page_slug + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_page_slug,
        },{
          'id': 'slug DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_page_slug + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_page_slug,
        }
      ]
    },{
      'text': js_lang.th_page_title,
      'field': 'page_title',
      'children': [{
          'id': 'title ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_page_title + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_page_title,
        },{
          'id': 'title DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_page_title + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_page_title,
        }
      ]
    },{
      'text': js_lang.th_page_sort_order,
      'field': 'page_sort_order',
      'children': [{
          'id': 'sort_order ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_page_sort_order + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_page_sort_order,
        },{
          'id': 'sort_order DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_page_sort_order + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_page_sort_order,
        }
      ]
    },{
      'text': js_lang.th_page_status,
      'field': 'page_status',
      'children': [{
          'id': 'status ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_page_status + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_page_status,
        },{
          'id': 'status DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_page_status + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_page_status,
        }
      ]
    }
  ];
  $($table_tbody).on('click', 'a.delete-page',function(evt){
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
    $('#type').val('<?php echo $session_data['type'] ?? ''; ?>');
    $('#blog').val('<?php echo $session_data['blog'] ?? ''; ?>');
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