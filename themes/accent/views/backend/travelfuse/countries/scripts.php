<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
themeFunctions::jsLang('confirm_delete');
themeFunctions::jsLang('ordering_placeholder');
themeFunctions::jsLang('ordering_by');
themeFunctions::jsLang('th_id');
themeFunctions::jsLang('th_code');
themeFunctions::jsLang('th_name');
themeFunctions::jsLang('th_status');
themeFunctions::jsLang('th_date_start');
themeFunctions::jsLang('th_date_expire');
themeFunctions::jsLang('sort_ascending');
themeFunctions::jsLang('sort_descending');

$default_session_data = array(
  'page' => 1,
  'ordering' => 'Id ASC',
  'search' => '',
  'limit' => 50,
);
$session_data = $this->_ci->session->userdata('backend/travelfuse/countries');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
?>
<script type="text/twig" id="countries_table_model">
<tr>
	<td class="text-center">{{ country.Id }}</td>
	<td class="text-center">{{ country.Code }}</td>
	<td class="text-center">{{ country.namefinal }}{{ country.namefinal != country.Name ? '<br /><small>' ~ country.Name ~ '</small>' : '' }}</td>
	<td class="text-center">{% if country.status == 1 %}Activ{% elseif country.status == -1 %}Anulat{% elseif country.status == -2 %}Sters{% else %}Inactiv{% endif %}</td>
	<td class="unexportable contains-form-control">
		<div class="btn-group">
		{% if country.can_view %}
			<a class="btn btn-secondary" href="{{ country.view_link }}"><i class="fa fa-eye"></i></a>
		{% endif %}
		{% if country.can_edit %}
			<a class="btn btn-primary" href="{{ country.edit_link }}"><i class="fa fa-pencil"></i></a>
		{% endif %}
		<?php /*
		{% if country.can_delete %}
			<a class="btn btn-danger delete-country" title="Sterge" data-status="-2" href="{{ country.delete_link }}"><i class="fa fa-trash"></i></a>
		{% endif %}
		*/ ?>
		{% if country.can_change_status %}
			{% if country.status != 0 %}
			<a class="btn btn-warning unpublish-country" title="Dezactiveaza" data-status="0" href="{{ country.unpublish_link }}"><i class="fa fa-thumbs-down"></i></a>
			{% endif %}
			{% if country.status != 1 %}
			<a class="btn btn-success publish-country" title="Activeaza" data-status="1" href="{{ country.publish_link }}"><i class="fa fa-thumbs-up"></i></a>
			{% endif %}
			<?php /*
			{% if country.status != -1 %}
			<a class="btn btn-inverse archive-country" title="Anuleaza" data-status="-1" href="{{ country.archive_link }}"><i class="fa fa-times"></i></a>
			{% endif %}
			*/ ?>
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


  var $table_tbody = $('table.travelfuse_countries_table>tbody');
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
		url: "<?php echo site_url('backend/travelfuse/travelfuse_countries/getlist'); ?>",
		method: 'POST',
		dataType: 'json',
		data: $form_data,
		beforeSend: function() {
			$('#travelfuse_countries_wrapper').addClass('loading-state');
		},
		complete: function() {
			$('#travelfuse_countries_wrapper').removeClass('loading-state');
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
			$.each(data.countries, function(){
				var country = this;
				html += (get_snippet('countries_table_model', {country:country}));
			});
			console.warn($('#travelfuse_countries_tbody'), html);
			$('#travelfuse_countries_tbody').html(html);
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
      'field': 'cn.Id',
      'children': [{
          'id': 'cn.Id ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_id + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_id,
        },{
          'id': 'cn.Id DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_id + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_id,
        }
      ]
    },{
      'text': js_lang.th_status,
      'field': 'cn.status',
      'children': [{
          'id': 'cn.status ASC',
          'icon': '<i class="fa fa-sort-numeric-asc" />',
          'direction': 'asc',
          'text': js_lang.th_status + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_status,
        },{
          'id': 'cn.status DESC',
          'icon': '<i class="fa fa-sort-numeric-desc" />',
          'direction': 'desc',
          'text': js_lang.th_status + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_status,
        }
      ]
    },{
      'text': js_lang.th_name,
      'field': 'namefinal',
      'children': [{
          'id': 'namefinal ASC',
          'icon': '<i class="fa fa-sort-alpha-asc" />',
          'direction': 'asc',
          'text': js_lang.th_name + ' ' + js_lang.sort_ascending,
          'display': js_lang.sort_ascending,
          'group': js_lang.th_name,
        },{
          'id': 'namefinal DESC',
          'icon': '<i class="fa fa-sort-alpha-desc" />',
          'direction': 'desc',
          'text': js_lang.th_name + ' ' + js_lang.sort_descending,
          'display': js_lang.sort_descending,
          'group': js_lang.th_name,
        }
      ]
    }
  ];
  $table_tbody.on('click', 'a.delete-country, a.archive-country, a.unpublish-country, a.publish-country',function(evt){
	 var country_id = $('>td:nth-child(1)', $(this).closest('tr')).text().trim();
	 var country_code = $('>td:nth-child(2)', $(this).closest('tr')).text().trim();
	 var country_name = $('>td:nth-child(3)', $(this).closest('tr')).text().trim();
	 var status = $(this).attr('data-status');
	 var title = 'Confirmare actiune';
	 var text = 'Confirma actiunea: ' + country_code;
	 var button_text = 'Confirm';
	 if(status == -2){
		 title = js_lang.confirm_delete;
		 text = 'Aceasta actiune este permanenta! \nNu va mai putea fi folosit/activat/dezactivat';
		 button_text = 'Stergere';
	 } else if(status == -1){
		 text = "Anulare inseamna ca nu poate fi folosit in rezervari";
		 button_text = "Anulare";
		 title += ' ' + button_text;
	 } else if(status == 0){
		 text = "Dezactivare inseamna ca nu poate fi folosit in rezervari";
		 button_text = "Dezactivare";
		 title += ' ' + button_text;
	 } else if(status == 1){
		 text = "Activarea inseamna ca poate fi folosit in rezervari";
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
        text: text + ' \n\n\nCod: ' + country_code + ' \nNume: ' + country_name,
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