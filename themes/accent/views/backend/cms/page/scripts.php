<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$page = $this->view_data['page'];
$can_write = $this->_method !='view';
$editing = $page->page_id !== 0;
if($can_write){
themeFunctions::jsLang('warning_form_not_ready');
themeFunctions::jsLang('param_sdate');
themeFunctions::jsLang('param_edate');
?>
<script>
(function($){
  var $action_buttons = $('button[type=submit][form=pageForm]');
  var $message_container = $('#result_pageForm');
  function enableActionButtons(){
    $action_buttons.prop('disabled',false);
  }
  function disableActionButtons(){
    $action_buttons.prop('disabled',true);
  }
  var form_ready = true;
  var editing = <?php echo $editing ? 1 : 0; ?>;
  $action_buttons.prop('disabled', false);
  $action_buttons.click(function(){
    this.form.task.value = this.value;
  });
	var base_url = '<?php echo base_url(''); ?>';
  function dynamicDateParams(url, params){
		var dates = {};
		if(!params || !params.length){
			return;
		}
		var has_dates = false;
		for (var i=0; i<params.length; i++){
			if(!params[i] || !url.searchParams.get(params[i])){
				continue;
			}
			has_dates = true;
			dates[params[i]] = url.searchParams.get(params[i]);
		}
		if(!has_dates){
			return;
		}
    $.ajax({
      url: "<?php echo site_url('backend/cms/pages/parseDate'); ?>",
      method: 'GET',
      dataType: 'json',
      data: {dates: dates}
    }).done(function(msg){
			if(msg.status !== 'success'){
        alert(msg.message);
        return;
			}
      if(msg.message){
        swal({
          title: 'Data invalida',
          text: msg.message,
          icon: 'warning',
          buttons: {
            cancel: "Ok, corectez",
          },
          dangerMode: true
        });
      }
			var dates = msg.data;
			var url = new URL(base_url + $('#page_route').val());
      var min_date;
			$.each(dates,function(param){
				var options = dates[param];
				var $select = $('<select />').attr({
					'name' : 'params[' + param + ']',
					'class' : 'form-control'
				});
        var exact_value,index=0;
				$.each(options,function(val){
          if(!index){
            exact_value = val;
            index++;
          }
					var label = options[val];
					var $option = $('<option />').attr({
						'value' : val
					}).text(label).prop('selected', url.searchParams.get(param) === val);
					$option.appendTo($select);
				});
				$wrapper = $('<div class="input-group mb-2"/>');
				$label = $('<label class="input-group-addon" title="Deschide calendar" style="cursor:pointer;"/>').html('<span>' + js_lang['param_' + param] + ' <i class="fa fa-calendar"></i></span>');
        $input = $('<input type="text" class="caleran" data-param="' + param + '" style="width:1px;height:1px;position:absolute;visibility:hidden;"/>').val(exact_value);
        $input.appendTo($label);
				$label.appendTo($wrapper);
				$select.appendTo($wrapper);
				$wrapper.appendTo($('#extra_page_params'));
        
        $input.caleran({
          startOnMonday: true,
          locale: 'ro',
          singleDate: true,
          startEmpty: false,
          showFooter: false,
          autoCloseOnSelect: true,
          format: 'Y-MM-DD',
          minDate: min_date ? min_date : moment(),
          startDate: moment(exact_value,'Y-MM-DD'),
          onafterselect: function(caleran, startDate, endDate){
            if(!caleran.globals.firstValueSelected){
              return;
            }
            var param = $(this.target).data('param');
            var url = new URL(base_url + $('#page_route').val());
            url.searchParams.set(param, endDate.format("Y-MM-DD"));
            $('#page_route').val(url.pathname.substring(1,url.pathname.length) + unescape(url.search)).change();
          }
        });
        if(param == 'sdate'){
          min_date = moment(exact_value,'Y-MM-DD');
        }
			});
    });
  }
	$('#extra_page_params').on('change', 'select[name^="params"]', function(){
		var matches = this.name.match(/params\[(\w+)\]/);
		var param = matches[1];
		var url = new URL(base_url + $('#page_route').val());
		url.searchParams.set(param, this.value);
		$('#page_route').val(url.pathname.substring(1,url.pathname.length) + unescape(url.search));
	});
	function routeInterpret(){
		$('#extra_page_params').empty();
		var matches = $('#page_route').val().match(/\/\/[^\/]+\/([^\.]+)/);
		if(matches){
			$('#page_route').val(matches[1]);
		}
		if($('#page_route').val().length){
			try{
				var url = new URL(base_url + $('#page_route').val());
				dynamicDateParams(url, ['sdate', 'edate']);
			} catch (e){}
		}
	}
  $('#page_route').on('change paste', function(){
		routeInterpret();
  });
	routeInterpret();
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
  $('#pageForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
  
	function escapeHtml(unsafe){
		return unsafe
			 .replace(/&/g, "&amp;")
			 .replace(/</g, "&lt;")
			 .replace(/>/g, "&gt;")
			 .replace(/"/g, "&quot;")
			 .replace(/'/g, "&#039;");
	}
  var pageimagepos;
  var imagecounter = $('#page-images-tbody > tr').length;
  function addpageImage(path, b){
	  if($(`input[type=hidden][name^="images["][name$="[name]"]`).filter((i, v) => v.value == path).length){
		  alert('Deja introdusa aceasta imagine');
		  return;
	  }
	  var pos = pageimagepos;
	  var html = `
	  <tr>
		<td>
			<span class="crt"></span>
			<input type="hidden" name="images[${imagecounter}][name]" value="${escapeHtml(path)}">
			<input type="hidden" name="images[${imagecounter}][custom]" value="1">
		</td>
		<td><a href="${path}" target="_blank"><img src="${path}" loading="lazy" class="page-image" /></a></td>
		<td><input type="text" name="images[${imagecounter}][alt]" value="" class="form-control" placeholder="Alt"></td>
		<td>${path}</td>
		<td>
			<label class="i-checks on-off-show">
			  <input type="checkbox" value="1" name="images[${imagecounter}][hide]" class="form-control-custom radio-custom">
			  <i class="fa fa-eye is-off text-success" title="Se afiseaza"></i>
			  <i class="fa fa-eye-slash is-on" title="Ascuns"></i>
			</label>
			<label class="i-checks on-off-show">
			  <input type="checkbox" value="1" class="form-control-custom radio-custom" onchange="$('input[name]', $(this).closest('tr').toggleClass('todelete', $(this).is(':checked'))).prop('disabled', $(this).is(':checked'))">
			  <i class="fa fa-times is-on text-warning" title="Anuleaza stergerea"></i>
			  <i class="fa fa-trash is-off" title="Marcheaza pentru stergere"></i>
			</label>
		</td>
	  </tr>
	  `;
	  if('end' === pos){
		  $('#page-images-tbody').append(html);
	  } else if('start' === pos){
		  $('#page-images-tbody').prepend(html);
	  }
	  imagecounter++;
	  console.warn('adding_images', arguments);
  }
  var CKEditorFuncNum = CKEDITOR.tools.addFunction(addpageImage);
  console.warn('CKEditorFuncNum', CKEditorFuncNum);
  var related_window;
  
  $(document).on('click', '.add_image', function(){
	  pageimagepos = $(this).data('pos');
	  if(related_window) related_window.close();
	  var w = 800;
	  var h = 800;
	  var left = (screen.width/2)-(w/2);
	  var top = (screen.height/2)-(h/2);
	  var windowFeatures = 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left;
	  related_window = window.open('https://accenttravel.ro/fileman/index.html?type=image&CKEditor=add_image&CKEditorFuncNum=' + CKEditorFuncNum + '&langCode=en&d=resources/images', 'pageImage', windowFeatures)
  });
	$(window).on('beforeunload', function(){
      if(related_window) related_window.close();
	});
	
	$("#page-images-tbody").sortable({
		axis: "y", // Restrict movement to vertical
		placeholder: 'ui-state-highlight',
        over: function(event, ui) {
        		var cl = ui.item.attr('class');
        		$('.ui-state-highlight').addClass(cl);
    		}
	}).disableSelection();
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  