(function($){
window.removeFormMessages = function($form,$_err_cont){
  // $('.form-group', $form).removeClass('has-danger has-warning has-success');
  $('.error-container', $form).remove();
  $_err_cont.empty();
};
window.removeFieldMessages = function($field, $form_group){
  if(!$form_group || typeof $form_group === 'undefined'){
    $form_group = $field.closest('.form-group');
  }
  // $form_group.removeClass('has-danger has-warning has-success');
  $('.error-container', $form_group).remove();
};
window.fieldMessage = function($field, message, type, $form_group){
  if(type == 'error'){
    type = 'danger';
  }
  if(!$form_group || typeof $form_group === 'undefined'){
    $form_group = $field.closest('.form-group');
  }
  var $_err_cont = $('<div class="error-container form-control-feedback col-12">');
  $form_group.addClass('has-' + type);
  $_err_cont.appendTo($form_group);
  showMessage($_err_cont,message,type);
}
$.fn.getViewPortAdjustment = function() {
  var top_offset = window.top_offset ? window.top_offset : 0;
  var bottom_offset = window.bottom_offset ? window.bottom_offset : 0;
  var elementTop = $(this).offset().top;
  var outerHeight = $(this).outerHeight();
  var elementBottom = elementTop + outerHeight;
  var viewportTop = $(window).scrollTop() + top_offset;
  var viewportBottom = viewportTop + $(window).height() - bottom_offset;
  var viewportHeight = viewportBottom - viewportTop;
  if(elementBottom < viewportTop){
    console.log('top');
    return elementTop - top_offset;
  } else if(elementTop > viewportBottom){
    return elementBottom - (viewportBottom - viewportTop);
  }
  
  return 0;
};
window.showFormMessages = function($form, errors,$_err_cont,$forms){
  $_err_cont.empty();
  var message_shown = false;
  for(var field_name_str in errors){
    if(!errors.hasOwnProperty(field_name_str)) {
      continue;
    }
    var field_name_arr = field_name_str.split('|');
    var field_name = field_name_arr[0];
    var eq = 0;
    if(field_name_arr.length>1){
      eq = parseInt(field_name_arr[1]);
    }
    var message = errors[field_name];
    var field = $form[0][field_name];
    if(typeof field === 'undefined'){
      if(typeof($forms) !== 'undefined'){
        $field = $('[name="' + field_name + '"]',$forms);
        if($field.length){
          field = $field.toArray();
        }
      }
      if(typeof field === 'undefined'){
        showMessage($_err_cont,message,'danger', true, !message_shown);
        message_shown = false;
        continue;
      }
    }
    if(field.length){
      field = field[eq];
    }
    var $field = $(field);
    var $form_group = $field.closest('.form-group');
    if($form_group.length){
      fieldMessage($field, message, 'danger', $form_group);
    } else {
      var form = field.form;
      var $_err_cont = $("#result_" + form.name);
      showMessage($_err_cont,message,'danger', true, !message_shown);
      message_shown = false;
    }
  }
};
window.showMessage = function($_err_cont, message, type, dismissible, scroll_to){
  if(typeof dismissible === 'undefined'){
    dismissible = true;
  } else if(dismissible !== true){
    dismissible = false;
  }
  if(typeof scroll_to === 'undefined'){
    scroll_to = true;
  } else if(scroll_to !== true){
    scroll_to = false;
  }
  if(!type || typeof type !== 'string'){
    type = 'info';
  }
  if(type == 'error'){
    type = 'danger';
  }
  var $alert = $('<div class="alert alert-' + type + (dismissible ? ' alert-dismissible' : '') + ' fade show mb-0 mt-1" role="alert" />');
  if(dismissible){
    var close_button_html = '<button type="button" class="close" data-dismiss="alert" aria-label="Inchide">\
      <span aria-hidden="true">&times;</span>\
    </button>';
    $alert.append(close_button_html);
  }
  var $alert_message = $('<div class="alert-message" />').html(message);
  $alert_message.appendTo($alert);
  $alert.appendTo($_err_cont);
  $_err_cont.show();
  if(scroll_to){
    scrollToIfNecessary($_err_cont);
  }
};
window.scrollToIfNecessary = function($elem, timeout){
  if(!timeout){
    timeout = 10;
  }
  var viewport_adjustment = $elem.first().getViewPortAdjustment();
  if(viewport_adjustment){
    setTimeout(function(){
      $('html, body').animate({
        scrollTop: viewport_adjustment
      }, timeout);
    });
  }
}
window.basicFormPostSubmit = function(form,url,callback,apply_callback_on_fail,$_err_cont,$forms){
  var $form = $(form);
  if(!$_err_cont){
    $_err_cont = $("#result_" + form.name);
  }
  removeFormMessages($form,$_err_cont);
  showMessage($_err_cont,'Asteptati...','info', true, false);
  if(typeof $forms !== 'undefined'){
    var $form_data = $forms.serialize();
  } else {
    var $form_data = $form.serialize();
  }
  $.ajax({
    url: url,
    dataType: "json",
    method: "POST",
    data: $form_data
  }).done(function(resp, textStatus, jqXHR){
    resp.textStatus = textStatus;
    resp.jqXHR = jqXHR;
    $_err_cont.empty();
    if (typeof callback === "function" && !callback($form,resp,$_err_cont)) {
      return;
    }
    if(resp.status !== 'success'){
      if(resp.data && resp.data.errors){
        showFormMessages($form,resp.data.errors,$_err_cont,$forms);
      } else {
        showMessage($_err_cont,resp.message, resp.message_type ? resp.message_type : 'danger', true, true);
      }
    } else {
      showMessage($_err_cont,resp.message,'success', true, true);
    }
  }).fail(function(jqXHR, textStatus, errorThrown){
    $_err_cont.empty();
    if (apply_callback_on_fail && typeof callback === "function" && !callback($form,{status:'fail',message:errorThrown,textStatus:textStatus,jqXHR:jqXHR},$_err_cont)) {
      return;
    }
    showMessage($_err_cont,'Operatia a esuat, va rugam sa reincarcati pagina','danger', true, true);
  });
  return false;
};
})(jQuery);
