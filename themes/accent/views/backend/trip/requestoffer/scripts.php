<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 

$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}

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
$session_data = $this->_ci->session->userdata('backend/trip/requestoffer');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<div class="modal fade" id="modal_cerere_oferta" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="d-flex flex-column justify-content-center" style="height:100%;pointer-events:none;">
    <div class="modal-dialog modal-lg" role="document" style="max-height: 90%;pointer-events:auto; width:90%;">
      <div class="modal-content">
        <div class="modal-header pt-2 pl-3 pb-2 pr-3">
          <h4 class="mb-0 mr-3">Cerere oferta</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Inchide">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-2 pr-3 pl-3 pb-0">
          <div class="form-group row">
            <label for="request_offer_title" class="<?php echo $label_class; ?>">Titlu</label>
            <div class="<?php echo $value_class; ?>">
              <input id="request_offer_title" type="text" maxlength="255" readonly placeholder="" class="form-control" value="" />
            </div>
          </div>
          <div class="form-group row">
            <label for="request_offer_fullname" class="<?php echo $label_class; ?>">Nume</label>
            <div class="<?php echo $value_class; ?>">
              <input id="request_offer_fullname" type="text" maxlength="255" readonly placeholder="" class="form-control" value="" />
            </div>
          </div>
          <div class="form-group row">
            <label for="request_offer_email" class="<?php echo $label_class; ?>">Email</label>
            <div class="<?php echo $value_class; ?>">
              <input id="request_offer_email" type="email" maxlength="255" readonly placeholder="" class="form-control" value="" />
            </div>
          </div>
          <div class="form-group row">
            <label for="request_offer_phone" class="<?php echo $label_class; ?>">Telefon</label>
            <div class="<?php echo $value_class; ?>">
              <input id="request_offer_phone" type="text" maxlength="100" readonly placeholder="" class="form-control" value="" />
            </div>
          </div>
          <div class="form-group row">
            <label for="request_offer_message" class="<?php echo $label_class; ?>">Informații</label>
            <div class="<?php echo $value_class; ?>">
              <textarea rows="8" id="request_offer_message" readonly placeholder="" class="form-control"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
(function($){
  var $table_tbody = $('table.Trip_Requestoffer_table>tbody');
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
      url: "<?php echo site_url('backend/trip/requestoffer/getlist'); ?>",
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
        for(var i=0; i<data.requests.length;i++){
          var request = data.requests[i];
          var $tr = $('<tr />');
          
          var type = '';
          switch(request.type){
            case 'hotel': type = 'Hotel'; break;
            case 'package': type = 'Vacanta'; break;
            case 'citybreak': type = 'City Break'; break;
            case 'flight': type = 'Bilet avion'; break;
            default : type = request.type; break;
          }
          var $type = $('<span />').text(type);
          
          var $type_td = $('<td class="text-center" style="white-space:pre;"/>').html($type);
          $type_td.appendTo($tr);
          var $title_td = $('<td class="text-center" />').html(request.title);
          $title_td.appendTo($tr);
          var $amount_td = $('<td class="text-center" style="white-space:pre;" />').html(format_price(request.amount, request.currency));
          $amount_td.appendTo($tr);
          var $fullname_td = $('<td class="text-center" />').html(request.fullname);
          $fullname_td.appendTo($tr);
          var $email_td = $('<td class="text-center" />').html(request.email);
          $email_td.appendTo($tr);
          var $phone_td = $('<td class="text-center" />').html(request.phone);
          $phone_td.appendTo($tr);
          var status = '';
          var $status = $('<span />');
          var $status_td = $('<td class="text-center" />');
          switch(parseInt(request.status)){
            case -2: status = 'Arhivat'; break;
            case -1: status = 'Expirat'; $status_td.addClass('bg-warning text-white'); break;
            case 0: status = 'Anulat'; $status_td.addClass('bg-danger text-white'); break;
            case 1: status = 'Activ'; $status_td.addClass('bg-success text-white'); break;
            case 2: status = 'Pozitiv'; $status_td.addClass('bg-primary text-white'); break;
            default : status = request.status; break;
          }
          $status.text(status).attr({
            'data-toggle' : 'tooltip',
            'title' : request.message,
          });
          $status_td.append($status);
          $status_td.appendTo($tr);
          var $time_created_td = $('<td class="text-center" />').html(request.time_created);
          $time_created_td.appendTo($tr);
          var $date_expire_td = $('<td class="text-center" style="white-space:pre;"/>').html(request.date_expire);
          $date_expire_td.appendTo($tr);
          var $actions_td = $('<td class="text-center contains-form-control" />');
          
          var $actions_btn_group = $('<div class="btn-group btn-group-sm" />');
          $actions_btn_group.appendTo($actions_td);
          var can_view = parseInt(request.status)>0 && request.type !='custom';
          var can_delete = parseInt(request.status)>0;
          if(can_delete){
            // $actions_btn_group.append($('<a class="btn btn-secondary" target="_BLANK" title="Anuleaza"><i class="fa fa-trash"></i></a>').attr('href',request.delete_link));
          }
          $actions_btn_group.append($('<a class="btn btn-primary request-offer-button" href="javascript:void(0);" title="Vezi cererea"><i class="fa fa-eye"></i></a>'));
          if(can_view){
            $actions_btn_group.append($('<a class="btn btn-primary" title="Vezi oferta" target="_BLANK"><i class="fa fa-external-link"></i></a>').attr('href',request.view_link));
          }
          // $actions_btn_group.append($('<a class="btn btn-secondary" title="Vezi toate notificarile acestui client" target="_BLANK"><i class="fa fa-user"></i></a>').attr('href',request.requests_link));
          
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
  $("#content").on("click", ".request-offer-button", function () {
    var $this = $(this);
    var $tr = $(this).closest('tr');
    $('#request_offer_title').val($('>td:nth-child(2)',$tr).text());
    $('#request_offer_fullname').val($('>td:nth-child(4)',$tr).text());
    $('#request_offer_email').val($('>td:nth-child(5)',$tr).text());
    $('#request_offer_phone').val($('>td:nth-child(6)',$tr).text());
    $('#request_offer_message').val($('>td:nth-child(7)>span',$tr).attr('title'));
    $('#modal_cerere_oferta').modal('show');
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>