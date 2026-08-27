<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$hotel = $this->view_data['hotel'];
$can_write = $this->_method !='view';
$editing = trim($hotel->id) !== '';
if($can_write){ ?>
<script>
(function($){
	var form_ready = true;
	function escapeHtml(unsafe){
		return unsafe
			 .replace(/&/g, "&amp;")
			 .replace(/</g, "&lt;")
			 .replace(/>/g, "&gt;")
			 .replace(/"/g, "&quot;")
			 .replace(/'/g, "&#039;");
	}
  var $action_buttons = $('button[type=submit][form=hotelsForm]');
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
  $('#hotelsForm').on('submit',function(e){
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
  var hotelimagepos;
  var imagecounter = $('#hotel-images-tbody > tr').length;
  function addHotelImage(path, b){
	  if($(`input[type=hidden][name^="_images["][name$="[name]"]`).filter((i, v) => v.value == path).length){
		  alert('Deja introdusa aceasta imagine');
		  return;
	  }
	  var pos = hotelimagepos;
	  var html = `
	  <tr>
		<td>
			<span class="crt"></span>
			<input type="hidden" name="_images[${imagecounter}][name]" value="${escapeHtml(path)}">
			<input type="hidden" name="_images[${imagecounter}][custom]" value="1">
		</td>
		<td><a href="${path}" target="_blank"><img src="${path}" loading="lazy" class="hotel-image" /></a></td>
		<td>Custom</td>
		<td>${path}</td>
		<td>
			<label class="i-checks on-off-show">
			  <input type="checkbox" value="1" name="_images[${imagecounter}][hide]" class="form-control-custom radio-custom">
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
		  $('#hotel-images-tbody').append(html);
	  } else if('start' === pos){
		  $('#hotel-images-tbody').prepend(html);
	  }
	  imagecounter++;
	  console.warn('adding_images', arguments);
  }
  var CKEditorFuncNum = CKEDITOR.tools.addFunction(addHotelImage);
  console.warn('CKEditorFuncNum', CKEditorFuncNum);
  var related_window;
  var facilitycounter = $('#hotel-facilities-tbody > tr').length;
  function addHotelFacility(name, othername){
	  if($(`input[type=hidden][name^="_facilities["][name$="[name]"]`).filter((i, v) => v.value == name).length){
		  alert('Deja introdusa aceasta facilitate');
		  return;
	  }
	  var html = `
	  <tr>
		<td>
			<span class="crt"></span>
			<input type="hidden" name="_facilities[${facilitycounter}][name]" value="${escapeHtml(name)}">
			<input type="hidden" name="_facilities[${facilitycounter}][custom]" value="1">
		</td>
		<td>Custom</td>
		<td>${othername}</td>
		<td>
			<label class="i-checks on-off-show">
			  <input type="checkbox" value="1" name="_facilities[${facilitycounter}][hide]" class="form-control-custom radio-custom">
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
	  $('#hotel-facilities-tbody').append(html);
	  facilitycounter++;
  }
  $('#facilitati_modal').on('hide.bs.modal', function (event) {
	  var $checkboxes = $('input[type=checkbox].hotel-facility-checkbox:checked');
	  if(!$checkboxes.length) return;
	  $checkboxes.each((i,el) => {
		  addHotelFacility(el.value, el.getAttribute('data-other-name'));
		  $(el).closest('.input-group').remove();
	  })
  });
  $(document).on('click', '.add_image', function(){
	  hotelimagepos = $(this).data('pos');
	  if(related_window) related_window.close();
	  var w = 800;
	  var h = 800;
	  var left = (screen.width/2)-(w/2);
	  var top = (screen.height/2)-(h/2);
	  var windowFeatures = 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left;
	  related_window = window.open('https://accenttravel.ro/fileman/index.html?type=image&CKEditor=add_image&CKEditorFuncNum=' + CKEditorFuncNum + '&langCode=en&d=resources/images/hoteluri/tf', 'hotelImage', windowFeatures)
  });
	$(window).on('beforeunload', function(){
      if(related_window) related_window.close();
	});
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  