<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_id');
themeFunctions::jsLang('th_code');
themeFunctions::jsLang('th_status');
themeFunctions::jsLang('th_nr_uses');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');
$data = $this->view_data;
$coupon = $this->view_data['coupon'];
$can_write = $this->_method !='view';
$editing = trim($coupon->id) !== '';
if($can_write){ ?>
<script>
(function($){
	function couponTypeChange(){
		var value = $('#coupon_type').val();
		$('.type-only:not(' + value + '-only)').addClass('d-none');
		$('.type-only.' + value + '-only').removeClass('d-none');
	}
	$('#coupon_type').on('change', couponTypeChange);
	couponTypeChange();
	
	function discountTypeChange(){
		var value = $('#coupon_discount_type').val();
		$('.discount-type-only:not(' + value + '-only)').addClass('d-none');
		$('.discount-type-only.' + value + '-only').removeClass('d-none');
	}
	$('#coupon_discount_type').on('change', discountTypeChange);
	discountTypeChange();
	
  var $action_buttons = $('button[type=submit][form=couponsForm]');
  $action_buttons.prop('disabled', false);
  $action_buttons.click(function(){
    this.form.task.value = this.value;
  });
  function submitFormSubmitCallback($form,resp,$error_container){
    form_ready = true;
    if(resp.status !== 'success'){
      return true;
    }
    form_change = false;
    $form[0].submit();
    $form[0].task.value = '';
    return true;
  }
  $('#couponsForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
  
  $('input[type=text].input-date_start').makeCaleranDatepicker({format: 'Y-MM-DD'}).makeInputmaskDate3();
  $('input[type=text].input-date_expire').makeCaleranDatepicker({format: 'Y-MM-DD'}).makeInputmaskDate3();
})(jQuery);
</script>
<?php
}
?>
<script type="text/twig" id="coupon_table_model">
<tr>
	<td class="text-center">{{ coupon.id }}</td>
	<td class="text-center">{{ coupon.code }}</td>
	<td class="text-center">{{ coupon.pan }}</td>
	<td class="text-center">{% if coupon.status == 1 %}Activ{% elseif coupon.status == -1 %}Anulat{% elseif coupon.status == -2 %}Sters{% else %}Inactiv{% endif %}</td>
	<td class="text-center">{{ coupon.nr_uses }}</td>
	<td>{% for order in coupon.orders %}
		<a href="<?php echo site_url('backend/trip/orders/edit'); ?>?id={{ order.order_id }}" class="btn btn-success btn-xs">{{ order.trip_order_id ? order.trip_order_id : order.order_id }}</a>
	{% endfor %}</td>
	<td>
	{% set types = [] %}
	{% for order in coupon.orders if order.type not in types %}
		{% set types = types|merge([order.type]) %}
		{{ order.type }}
	{% endfor %}</td>
	<td class="unexportable">
		{% if coupon.nr_uses == 0 %}
		{% if coupon.status != -2 and coupon.status != -1 and coupon.status != 1 %}
		<button title="Stergere" class="btn btn-danger btn-trash change-status" type="button" data-id="{{ coupon.id }}" data-status="-2"><i class="fa fa-trash"></i></button>
		{% endif %}
		{% if coupon.status != 0 %}
		<button title="Dezactivare" class="btn btn-warning btn-unpublish change-status" type="button" data-id="{{ coupon.id }}" data-status="0"><i class="fa fa-thumbs-down"></i></button>
		{% endif %}
		{% if coupon.status != 1 %}
		<button title="Activare" class="btn btn-success btn-publish change-status" type="button" data-id="{{ coupon.id }}" data-status="1"><i class="fa fa-thumbs-up"></i></button>
		{% endif %}
		{% if coupon.status != -1 %}
		<button title="Anulare" class="btn btn-inverted btn-archive change-status" type="button" data-id="{{ coupon.id }}" data-status="-1"><i class="fa fa-times"></i></button>
		{% endif %}
		{% endif %}
	</td>
</tr>
</script>
<script>
(function($){
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
    }
  ];
  function change_status(coupon_id, status){
	  return $.ajax({
		url: '<?php echo site_url('backend/trip/coupons/change_status'); ?>',
		data: {
			<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
			'<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>",
			<?php } ?>
			id: coupon_id,
			status: status
		},
		method: 'POST',
		dataType: 'json',
		beforeSend: function() {
			$('#serii-cupoane-wrapper').addClass('loading-state');
		},
		complete: function() {
			$('#serii-cupoane-wrapper').removeClass('loading-state');
		},
		success: function(msg) {
			if(msg.status == 'success'){
				
			} else {
				alert(msg.message);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	}).done(function(){return reloadCoupons(list_page) });
  }
  $('body').on('click', '.change-status', function(){
	 var coupon_id = $(this).attr('data-id');
	 var coupon_code = $('>td:nth-child(2)', $(this).closest('tr')).text().trim();
	 var srn = $('>td:nth-child(3)', $(this).closest('tr')).text().trim();
	 var status = $(this).attr('data-status');
	 var title = 'Confirmare actiune';
	 var text = 'Confirma actiunea: ' + coupon_code + '(' + srn + ')';
	 var button_text = 'Confirm';
	 if(status == -2){
		 title = js_lang.confirm_delete;
		 text = 'Aceasta actiune este permanenta! \nNu va mai putea fi folosit/activat/dezactivat';
		 button_text = 'Stergere';
	 } else if(status == -1){
		 text = "Anulare inseamna ca nu poate fi folosit in comanda, \nsi nu mai poate fi activat de Epay (daca este cazul)";
		 button_text = "Anulare";
		 title += ' ' + button_text;
	 } else if(status == 0){
		 text = "Dezactivare inseamna ca nu poate fi folosit in comanda, \ndar poate fi re-activat fie manual, fie de Epay (daca este cazul)";
		 button_text = "Dezactivare";
		 title += ' ' + button_text;
	 } else if(status == 1){
		 text = "Activarea inseamna ca poate fi folosit in comanda";
		 button_text = "Activare";
		 title += ' ' + button_text;
	 }
	swal({
		title: title,
		text: text + ' \n\nCupon \n\nSeria: ' + srn + ' \nCod: ' + coupon_code,
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
		  change_status(coupon_id, status);
		}
	  });
	  return;
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
  $('#ordering').val('id DESC').trigger('change.select2_4');
  
  $('#limit').on('change', function(){
    if(this.value=='0'){
      this.value = '';
    }
    reloadCoupons(1);
  });
  $('#ordering').on('change', function(){
    reloadCoupons(list_page);
  });
  $('#search_search_button').on('click', function(){
    reloadCoupons(1);
  });
  $('#search_clear_button').on('click', function(){
    jQuery('#search').val(null);
    reloadCoupons(1);
  });
  $('#export-button').on('click', function(){
    tablesToExcel('#serii-cupoane', 'Cupoane_' + <?php echo json_encode($coupon->code); ?> + '_' + moment().format('Y-MM-DD') + '.xls');
  });
  // jquery extend function
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
    $.redirectPost('<?php echo site_url('backend/trip/coupons/getlist'); ?>', {
		<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
		'<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>",
		<?php } ?>
		simple: 1,
		xls: 1,
		parent_id: '<?php echo $coupon->id; ?>',
		limit: type == 'current_page' ? $('#limit').val() : 0,
		search: type == 'all' ? '' + $('#search').val() : '',
		page: type == 'current_page' ? list_page : 1,
		ordering: $('#ordering').val(),
	});
  });
  
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
	<?php if($editing) { ?>
	function generateCoupons(){
		return swal({
			title: 'Aceasta actiune este permanenta!',
			text: "Sunteti pe cale sa generati cupoane",
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
			  return $.ajax({
				url: '<?php echo site_url('backend/trip/coupons/generate'); ?>',
				data: {
					<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
					'<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>",
					<?php } ?>
					number: $('#generate-number').val(),
					parent_id: '<?php echo $coupon->id; ?>'
				},
				method: 'POST',
				dataType: 'json',
				beforeSend: function() {
					$('#serii-cupoane-wrapper').addClass('loading-state');
				},
				complete: function() {
					$('#serii-cupoane-wrapper').removeClass('loading-state');
				},
				success: function(msg) {
					if(msg.status == 'success'){
						$('#generate-number').val('');
						
					} else {
						alert(msg.message);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			}).done(function(){return reloadCoupons(1) });
			}
		  });
		
		
	}
	$('button#generate-button').on('click', generateCoupons);
	
	var list_page = 1;
	function reloadCoupons(page){
		list_page = page;
		return $.ajax({
			url: '<?php echo site_url('backend/trip/coupons/getlist'); ?>',
			data: {
				<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
				'<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>",
				<?php } ?>
				simple: 1,
				parent_id: '<?php echo $coupon->id; ?>',
				limit: $('#limit').val(),
				search: '' + $('#search').val(),
				page: page,
				ordering: $('#ordering').val(),
			},
			method: 'POST',
			dataType: 'json',
			beforeSend: function() {
				$('#serii-cupoane-wrapper').addClass('loading-state');
			},
			complete: function() {
				$('#serii-cupoane-wrapper').removeClass('loading-state');
			},
			success: function(msg) {
				if(msg.status == 'success'){
					var data = msg.data;
					var coupons = msg.data.coupons;
					$('#shown-coupons').html(coupons.length);
					$('#total-coupons').html(data.total_items);
					var $pagination = $('ul.create-pagination');
					if($pagination.data("twbs-pagination")){
					  $pagination.twbsPagination('destroy');
					}
					var html = '';
					$.each(coupons, function(){
						var coupon = this;
						html += (get_snippet('coupon_table_model', {coupon:coupon}));
					});
					$('#serii-cupoane-tbody').html(html);
					
					$pagination.twbsPagination({
					  startPage: data.page,
					  totalPages: data.max_pages,
					  visiblePages: 10,
					  first: "<<",
					  prev: "<",
					  next: ">",
					  last: ">>",
					  onPageClick: function (evt, page) {
						  if(list_page == page){
							  return;
						  }
						reloadCoupons(page);
					  }
					});
				} else {
					alert(msg.message);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	reloadCoupons(1);
	<?php } ?>
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  