<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_id');
themeFunctions::jsLang('th_code');
themeFunctions::jsLang('th_percentage');
themeFunctions::jsLang('th_status');
themeFunctions::jsLang('th_max_uses');
themeFunctions::jsLang('th_nr_uses');
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
$session_data = $this->_ci->session->userdata('backend/trip/coupons');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/twig" id="coupon_table_model">
<tr>
	<td class="text-center">{{ coupon.id }}</td>
	<td class="text-center">{{ coupon.code }}</td>
	<td class="text-center">{{ coupon.ean }}</td>
	<td class="text-center">{{ coupon.type }}</td>
	<td class="text-center">{% if coupon.status == 1 %}Activ{% elseif coupon.status == -1 %}Anulat{% elseif coupon.status == -2 %}Sters{% else %}Inactiv{% endif %}</td>
	<td class="text-center">
		{% set supported_s = [] %}
		{% if(coupon.hotel) %}
			{% set supported_s = supported_s|merge(['Hotel']) %}
		{% endif %}
		{% if(coupon.package) %}
			{% set supported_s = supported_s|merge(['Pachet']) %}
		{% endif %}
		{% if(coupon.flight) %}
			{% set supported_s = supported_s|merge(['Zbor']) %}
		{% endif %}
		{% if(coupon.citybreak) %}
			{% set supported_s = supported_s|merge(['CB']) %}
		{% endif %}
		{% if(coupon.paralela45_strainatate) %}
			{% set supported_s = supported_s|merge(['P45-str']) %}
		{% endif %}
		{% if(coupon.paralela45_circuit) %}
			{% set supported_s = supported_s|merge(['P45-circ']) %}
		{% endif %}
		{% if(coupon.travelfuse_circuit) %}
			{% set supported_s = supported_s|merge(['TF-circ']) %}
		{% endif %}
		{% if(coupon.travelfuse_charter) %}
			{% set supported_s = supported_s|merge(['TF-chart']) %}
		{% endif %}
			{{ supported_s|join(', ')}}
		<div>{{ coupon.observation is not null ? coupon.observation : '- fara observatii -' }}</div>
	</td>
	<td class="text-center">{{ coupon.discount_type == 'P' ? coupon.percentage ~ '%' : coupon.fixed_ron ~ ' Lei / ' ~ coupon.fixed_eur ~ " &euro;" }}</td>
	<td class="text-center"><strong title="Nr. total utilizari">{{ coupon.nr_uses }}</strong> / (<span title="Nr. maxim de utilizari">{{ (coupon.max_uses ? coupon.max_uses : '- nelimitat -') }}</span>{% if coupon.type == 'group' %} X <strong title="Nr. cupoane copil">{{ coupon.total_coupons}}{% endif %}</strong>)</td>
	<td class="text-center">{{ (coupon.date_start ? coupon.date_start : '- fara start -') ~ ' / ' ~ (coupon.date_expire ? coupon.date_expire : '- fara sfarsit -') }}</td>
	<td>{% for order in coupon.orders %}
		<a href="<?php echo site_url('backend/trip/orders/edit'); ?>?id={{ order.order_id }}" class="btn btn-success btn-xs">{{ order.trip_order_id ? order.trip_order_id : order.order_id }}</a>
	{% endfor %}</td>
	<td>
	{% set types = [] %}
	{% for order in coupon.orders if order.type not in types %}
		{% set types = types|merge([order.type]) %}
		{{ order.type }}
	{% endfor %}
	</td>
	<td class="unexportable contains-form-control">
		<div class="btn-group">
		{% if coupon.can_view %}
			<a class="btn btn-secondary" href="{{ coupon.view_link }}"><i class="fa fa-eye"></i></a>
		{% endif %}
		{% if coupon.can_edit %}
			<a class="btn btn-primary" href="{{ coupon.edit_link }}"><i class="fa fa-pencil"></i></a>
		{% endif %}
		{% if coupon.can_delete %}
			<a class="btn btn-danger delete-coupon" title="Sterge" data-status="-2" href="{{ coupon.delete_link }}"><i class="fa fa-trash"></i></a>
		{% endif %}
		{% if coupon.can_change_status %}
			{% if coupon.status != 0 %}
			<a class="btn btn-warning unpublish-coupon" title="Dezactiveaza" data-status="0" href="{{ coupon.unpublish_link }}"><i class="fa fa-thumbs-down"></i></a>
			{% endif %}
			{% if coupon.status != 1 %}
			<a class="btn btn-success publish-coupon" title="Activeaza" data-status="1" href="{{ coupon.publish_link }}"><i class="fa fa-thumbs-up"></i></a>
			{% endif %}
			{% if coupon.status != -1 %}
			<a class="btn btn-inverse archive-coupon" title="Anuleaza" data-status="-1" href="{{ coupon.archive_link }}"><i class="fa fa-times"></i></a>
			{% endif %}
		{% endif %}
		</div>
	</td>
</tr>
</script>
<script type="text/javascript">
(function($){
	  
	var snippets = {};
	function get_snippet(snippet_id, data){
		var snippet;
		if(typeof snippets[snippet_id] == 'undefined'){
			snippets[snippet_id] = Twig.twig({
				data: $('script#' + snippet_id).html()
			});
		}
		return snippets[snippet_id].render(data);
	}


  var $table_tbody = $('table.Trip_Coupons_table>tbody');
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
		url: "<?php echo site_url('backend/trip/coupons/getlist'); ?>",
		method: 'POST',
		dataType: 'json',
		data: $form_data,
		beforeSend: function() {
			$('#serii-cupoane-wrapper').addClass('loading-state');
		},
		complete: function() {
			$('#serii-cupoane-wrapper').removeClass('loading-state');
		},
		success: function(msg) {
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
			var html = '';
			$.each(data.coupons, function(){
				var coupon = this;
				html += (get_snippet('coupon_table_model', {coupon:coupon}));
			});
			$('#serii-cupoane-tbody').html(html);
			/* $table_tbody.empty();
			for(var i=0; i<data.coupons.length;i++){
			  var coupon = data.coupons[i];
			  var $tr = $('<tr />');
			  var $id_td = $('<td class="text-center" />').html(coupon.id);
			  $id_td.appendTo($tr);
			  var $code_td = $('<td class="text-center" />').html(coupon.code);
			  $code_td.appendTo($tr);
			  var $type_td = $('<td class="text-center" />').html(coupon.type);
			  $type_td.appendTo($tr);
			  var status = '';
			  switch(parseInt(coupon.status)){
				case 1: status = 'Activ'; break;
				case 0: status = 'Inactiv'; break;
			  }
			  var $status_td = $('<td class="text-center" />').html(status);
			  $status_td.appendTo($tr);
			  
			  var $percentage_td = $('<td class="text-center" />').html(coupon.percentage + '%');
			  $percentage_td.appendTo($tr);
			  
			  var $max_uses_td = $('<td class="text-center" />').html(coupon.max_uses === null ? '- nelimitat -' : coupon.max_uses);
			  $max_uses_td.appendTo($tr);
			  
			  var $nr_uses_td = $('<td class="text-center" />').html(coupon.nr_uses);
			  $nr_uses_td.appendTo($tr);
			  
			  var $date_start_td = $('<td class="text-center" />').html(coupon.date_start === null ? '-' : coupon.date_start);
			  $date_start_td.appendTo($tr);
			  var $date_expire_td = $('<td class="text-center" />').html(coupon.date_expire === null ? '-' : coupon.date_expire);
			  $date_expire_td.appendTo($tr);
			  
			  var $actions_td = $('<td class="text-center contains-form-control" />');
			  
			  var can_view = coupon.can_view;
			  var can_edit = coupon.can_edit;
			  var can_delete = coupon.can_delete;
			  var can_change_status = coupon.can_change_status;
			  var can_any = can_view || can_edit || can_delete || can_change_status;
			  if(can_any){
				var $actions_btn_group = $('<div class="btn-group" />');
				$actions_btn_group.appendTo($actions_td);
				if(can_view){
				  $actions_btn_group.append($('<a class="btn btn-secondary"><i class="fa fa-eye"></i></a>').attr('href',coupon.view_link));
				}
				if(can_edit){
				  $actions_btn_group.append($('<a class="btn btn-primary"><i class="fa fa-pencil"></i></a>').attr('href',coupon.edit_link));
				}
				if(can_delete){
				  $actions_btn_group.append($('<a class="btn btn-danger delete-coupon"><i class="fa fa-trash"></i></a>').attr('href',coupon.delete_link));
				}
				if(can_change_status){
				  if(parseInt(coupon.status) == 1){
					$actions_btn_group.append($('<a class="btn btn-warning unpublish-coupon" title="Dezactiveaza"><i class="fa fa-thumbs-down"></i></a>').attr('href',coupon.unpublish_link));
				  } else {
					$actions_btn_group.append($('<a class="btn btn-success publish-coupon" title="Activeaza"><i class="fa fa-thumbs-up"></i></a>').attr('href',coupon.publish_link));
				  }
				}
			  }
			  $actions_td.appendTo($tr);
			  $tr.appendTo($table_tbody);
			}
			  */
		  } else {
			alert(msg.message);
		  }
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
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
    },{
      'text': js_lang.th_nr_uses,
      'field': 'nr_uses',
      'children': [{
          'id': 'nr_uses ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_nr_uses + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_nr_uses,
        },{
          'id': 'nr_uses DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_nr_uses + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_nr_uses,
        }
      ]
    },{
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
  $table_tbody.on('click', 'a.delete-coupon, a.archive-coupon, a.unpublish-coupon, a.publish-coupon',function(evt){
	 var coupon_id = $('>td:nth-child(1)', $(this).closest('tr')).text().trim();
	 var coupon_code = $('>td:nth-child(2)', $(this).closest('tr')).text().trim();
	 var coupon_type = $('>td:nth-child(4)', $(this).closest('tr')).text().trim();
	 var srn = $('>td:nth-child(3)', $(this).closest('tr')).text().trim();
	 var status = $(this).attr('data-status');
	 var title = 'Confirmare actiune';
	 var text = 'Confirma actiunea: ' + coupon_code + '(' + srn + ')';
	 var button_text = 'Confirm';
	 if(status == -2){
		 title = js_lang.confirm_delete;
		 text = 'Aceasta actiune este permanenta! \nNu va mai putea fi folosit/activat/dezactivat \n(nici copiii in cazul grupului)';
		 button_text = 'Stergere';
	 } else if(status == -1){
		 text = "Anulare inseamna ca nu poate fi folosit in comanda, \nsi nu mai poate fi activat de Epay (daca este cazul) \n(nici copiii in cazul grupului)";
		 button_text = "Anulare";
		 title += ' ' + button_text;
	 } else if(status == 0){
		 text = "Dezactivare inseamna ca nu poate fi folosit in comanda, \ndar poate fi re-activat fie manual, fie de Epay (daca este cazul) \n(doar copiii in cazul grupului)";
		 button_text = "Dezactivare";
		 title += ' ' + button_text;
	 } else if(status == 1){
		 text = "Activarea inseamna ca poate fi folosit in comanda \n(sau copiii activi in cazul grupului)";
		 button_text = "Activare";
		 title += ' ' + button_text;
	 }
	 
    var that = this;
    var $that = $(that);
    if($that.data('confirmed')){
      return true;
    } else {
      swal({
        title: title,
        text: text + ' \n\nCupon \n\nEAN: ' + srn + ' \nCod: ' + coupon_code + ' \nTip: ' + coupon_type,
        icon: 'warning',
        buttons: {
          cancel: "Nu... m-am razgandit.",
          delete: {
            text: "Da. " + button_text.toUpperCase() + "!",
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