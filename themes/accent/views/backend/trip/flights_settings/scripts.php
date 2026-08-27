<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script>
var forms_data = <?php echo json_encode($data); ?>;
var $err_container = $('#result_flightsSettingsForm');
console.log(forms_data);
$('form.flights_settings').on('change input','input', function(){
  form_change = true;
});
for (var k in forms_data) {
  var value = forms_data[k];
  if(k.indexOf('travel_') === 0){
    $('form[name=travel] [name="' + k.substring(7) + '"]').val(value);
  }
  if(k.indexOf('storno_') === 0){
    $('form[name=storno] [name="' + k.substring(7) + '"]').val(value);
  }
  // if(k.indexOf('service_') === 0){
    // $('form[name=service] [name="' + k.substring(8) + '"]').val(value);
  // }
}
var form_currently_submitting;
var setting_forms;
var setting_forms_iterator;
var submitFlightsSettings = function(e){
  setting_forms = $('form.flights_settings');
  setting_forms_iterator = 0;
  var $submit_input = $('<input type="submit" style="display:none;" />');
  $(setting_forms[setting_forms_iterator]).append($submit_input);
  $submit_input.trigger('click').remove();
  return false;
}
function addPriceElement($form, $table_tbody, $insert_after, data){
  var max_index = $table_tbody.data('max_index');
  if(!max_index){
    max_index = $table_tbody.children('tr').length;
  }
  var element_index = parseInt(max_index) + 1;
  $table_tbody.data('max_index', element_index);
  var $new_tr = $('<tr class="ac_inc" />');
  if($insert_after){
    if(true === $insert_after){
      $new_tr.appendTo($table_tbody);
    } else {
      $new_tr.insertAfter($insert_after);
    }
  } else {
    $new_tr.appendTo($table_tbody);
  }
  var $td_crt = $('<td class="ac_dis text-center" />');
  $td_crt.appendTo($new_tr);
  var $ordering_input = $('<input type="hidden" name="ordering[]" value="' + (element_index) + '" />');
  $ordering_input.appendTo($td_crt);
  
  var $td_interval = $('<td class="contains-form-control"/>');
  $td_interval.appendTo($new_tr);
  var $interval_input = $('<input class="form-control" required type="text" name="prices[' + element_index + '][interval]" />');
  if(typeof data !== 'undefined' && typeof data.interval  !== 'undefined'){
    $interval_input.val(data.interval);
  }
  $interval_input.appendTo($td_interval);
  
  var $td_price = $('<td class="contains-form-control"/>');
  $td_price.appendTo($new_tr);
  var $price_group = $('<div class="input-group" />');
  $price_group.appendTo($td_price);
  var $price_input = $('<input class="form-control" required type="number" step="0.01" min="0" name="prices[' + element_index + '][price]" />');
  if(typeof data !== 'undefined' && typeof data.price  !== 'undefined'){
    $price_input.val(data.price);
  }
  $price_input.appendTo($price_group);
  $price_group.append('<span class="input-group-addon"><?php echo $this->_ci->currency_symbol; ?></span>');
  
  
  var $td_action = $('<td class="contains-form-control"/>');
  $td_action.appendTo($new_tr);
  var $action_group = $('<div class="btn-group" />');
  $action_group.appendTo($td_action);
  $action_group.append('<span class="btn btn-info move-price"><i class="fa fa-arrows-v"></i></span>');
  $action_group.append('<button type="button" class="btn btn-success add-price-after"><i class="fa fa-plus"></i></button>');
  $action_group.append('<button type="button" class="btn btn-danger delete-price"><i class="fa fa-trash"></i></button>');
}
$(document).on('submit','form.flights_settings',function(event){
  event.preventDefault();
  event.stopPropagation();
  if(!$(setting_forms[setting_forms_iterator]).is(this)){
    console.log('returning false');
    return false;
  }
  if(form_currently_submitting === this){
    console.log('returning false');
    return false;
  }
  form_currently_submitting = this;
  var serialized_data = $(this).serializeArray();
  
  var form_name = this.name;
  var data = {};
  var replace_name;
  var replace_name_with;
  var ordering_index = 0;
  for (var i = 0; i < serialized_data.length; i++){
    var input_name = serialized_data[i]['name'];
    var input_value = serialized_data[i]['value'];
    if(input_name === 'ordering[]'){
      replace_name = parseInt(input_value);
      replace_name_with = ordering_index;
      ordering_index++;
      continue;
    }
    if(typeof replace_name !== 'undefined'){
      console.log(input_name, replace_name,  replace_name_with);
      input_name = input_name.replace('[' + replace_name + ']', '[' + replace_name_with + ']');
      console.log(input_name);
    }
    data[form_name + '_' + input_name] = input_value;
  }
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
  <?php } ?>
  console.log(serialized_data, data);
  
  $.ajax({
    url: '<?php echo site_url('backend/trip_flight/flights_settings/save'); ?>',
    method: 'POST',
    dataType: 'json',
    data: data
  }).done(function(response){
    console.log('done', response);
    form_change = false;
    form_currently_submitting = null;
    if(response.status==='success'){
      setting_forms_iterator++;
      if(setting_forms.length!=setting_forms_iterator){
        var $submit_input = $('<input type="submit" style="display:none;" />');
        $(setting_forms[setting_forms_iterator]).append($submit_input);
        $submit_input.trigger('click').remove();
      }
      showMessage($err_container, 'Informatiile au fost salvate', 'success');
    }
    else {
      showMessage($err_container, response.message, response.message_type);
    }
  }).fail(function(){
    showMessage($err_container, 'Nu mai sunteti autentificat');
  });
});
$(document).on('click', 'button.add-price', function(){
  form_change = true;
  var $button = $(this);
  var $table_tbody = $('table>tbody', $(this).closest('.form-group'));
  var $form = $(this.form);
  addPriceElement($form,$table_tbody);
});
$(document).on('click', 'button.add-price-after', function(e){
  form_change = true;
  var $button = $(this);
  var $tr = $button.closest('tr');
  var $table_tbody = $('table>tbody', $(this).closest('.form-group'));
  var $form = $(this.form);
  addPriceElement($form,$table_tbody,$tr);
});
$(document).on('click', 'button.delete-price', function(){
  $(this).closest('tr').remove();
});
$('table.prices-table > tbody').sortable({
  revert: true,
  items: ">tr",
  handle: ".move-price",
  start: function(e, ui){
    ui.placeholder.width(ui.item.width());
    ui.placeholder.height(ui.item.height());
  }
});
if(forms_data.travel_prices && forms_data.travel_prices.length){
  var $form = $('form[name=travel]');
  var $table_tbody = $('table.prices-table',$form);
  for(var i=0; i<forms_data.travel_prices.length;i++){
    var item_data = forms_data.travel_prices[i];
    var data = {
      interval: item_data.interval,
      price: item_data.price
    };
    addPriceElement($form, $table_tbody, null, data);
  }
}
if(forms_data.storno_prices && forms_data.storno_prices.length){
  var $form = $('form[name=storno]');
  var $table_tbody = $('table.prices-table',$form);
  for(var i=0; i<forms_data.storno_prices.length;i++){
    var item_data = forms_data.storno_prices[i];
    var data = {
      interval: item_data.interval,
      price: item_data.price
    };
    addPriceElement($form, $table_tbody, true, data);
  }
}
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>