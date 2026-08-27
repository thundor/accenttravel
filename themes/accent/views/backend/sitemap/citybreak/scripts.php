<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
?>
<script>
var forms_data = <?php echo json_encode($data); ?>;
var $err_container = $('#result_citybreakSettingsForm');
$('input.location').autocomplete({
  source: function(request, response){
    var $button = $('button.addlocation', $(this).closest('.input-group'));
    $.removeData($button);
    $button.prop('disabled',true);
    $.ajax({
      url: "<?php echo site_url('trip/flights/loadLocations'); ?>",
      dataType: "json",
      data: {
        q: request.term
      },
      success: function( result ) {
        console.log(result);
        if(!result.status || result.status !== 'success'){
          return;
        }
        var data = result.response;
        var response_data = [];
        if(data && data.length){
          for (var i=0; i < data.length; i++){
            var item = data[i];
            var label = (item.LocationId>0 ? item.LocationName + ', ' : '') + item.CityName + ' (' + item.CountryName + ')';
            var response_item = {
              id: item.LocationId + '-' + item.CityId,
              location_id: item.LocationId,
              city_id: item.CityId,
              country_id: item.CountryId,
              city_name: item.CityName,
              country_name: item.CountryName,
              location_name: (item.LocationId>0 ? item.LocationName : ''),
              value: (item.LocationId>0 ? item.LocationName + ', ' : '') + item.CityName,
              label: label
            };
            response_data.push(response_item);
          }
        }
        response( response_data );
      }
    });
  },
  minLength: 2,
  select: function( event, ui ) {
    var $button = $('button.addlocation', $(this).closest('.input-group'));
    $button.data('city_name', ui.item.city_name);
    $button.data('country_name', ui.item.country_name);
    $button.data('location_name', ui.item.location_name);
    $button.data('full_location_name', ui.item.value);
    $button.data('location_id', ui.item.location_id);
    $button.data('city_id', ui.item.city_id);
    $button.data('country_id', ui.item.country_id);
    $button.prop('disabled',false);
  }
}).blur(function(){
  var $button = $('button.addlocation', $(this).closest('.input-group'));
  if(!this.value.length || this.value !== $button.data('full_location_name')){
    this.value = '';
    $.removeData($button);
    $button.prop('disabled',true);
  }
});
function addElement(data, $table_tbody){
  var location_id = data.country_id + '-' + data.city_id + '-' + data.location_id;
  var formname = $table_tbody.closest('form').attr('name');
  var $new_tr = $('<tr class="ac_inc" />');
  var $td_crt = $('<td class="ac_dis text-center" />');
  $td_crt.html('&nbsp;');
  $td_crt.appendTo($new_tr);
  
  var $td_title = $('<td/>');
  var $country_hidden = $('<input type="hidden" name="locations[' + location_id + '][country]" />').val(data.country_name);
  $country_hidden.appendTo($td_title);
  var $city_hidden = $('<input type="hidden" name="locations[' + location_id + '][city]" />').val(data.city_name);
  $city_hidden.appendTo($td_title);
  var $location_hidden = $('<input type="hidden" name="locations[' + location_id + '][location]" />').val(data.location_name);
  $location_hidden.appendTo($td_title);
  var $details = $('<div class="text-muted"><a href="javascript:void(0);" data-toggle="collapse" data-target="#details' + formname +location_id+ '"><span>' + data.country_name + '</span>/<span>' + data.city_name + '</span>/<span>' + data.location_name + '</span> <i class="fa fa-pencil"></i></a></div>');
  var $details_details = $('<div id="details' + formname +location_id+ '" class="collapse" />');
  var $title_input = $('<input class="form-control" type="text" name="locations[' + location_id + '][text]" />').attr('placeholder', data.full_location_name).val(data.text ? data.text : '');
  $title_input.appendTo($details_details);
  if(formname === 'arival_locations'){
    var $details_menu = $('<div class="input-group pull-right" />');
    $details_menu.append('<span class="input-group-addon">Meniu:</span>');
    var $details_menu_checkbox = $('<span class="input-group-addon" />');
    var $checkbox = $('<input type="checkbox" name="locations[' + location_id + '][menu]" />');
    if(data.menu){
      $checkbox.prop('checked',true);
    }
    $checkbox.appendTo($details_menu_checkbox);
    $details_menu_checkbox.appendTo($details_menu);
    $details_menu.appendTo($details_details);
  }
  $details_details.appendTo($details);
  $details.appendTo($td_title);
  $td_title.appendTo($new_tr);
  var $td_menu = $('<td class="text-center contains-form-control"  />');
  var $delete_button = $('<button type="button" class="btn btn-sm deletelocation btn-danger"><i class="fa fa-trash"></i></button>')
  $delete_button.appendTo($td_menu);
  $td_menu.appendTo($new_tr);
  
  $new_tr.prependTo($table_tbody);
}

for(var form_name in forms_data){
  var form_data = forms_data[form_name];
  if(['departure_locations','arival_locations'].indexOf(form_name) > -1){
    var $table_tbody = $('form[name=' + form_name + '] table > tbody');
    for(var element in form_data){
      var element_data = form_data[element];
      var ids_arr = element.split('-');
      var data = {
        country_id: ids_arr[0],
        city_id: ids_arr[1],
        location_id: ids_arr[2],
        country_name: element_data.country,
        city_name: element_data.city,
        location_name: element_data.location,
        text: element_data.text,
        menu: element_data.menu ? true : false
      };
      data.full_location_name = (data.location_id>0 ? data.location_name + ', ' : '') + data.city_name;
      console.log(data);
      addElement(data,$table_tbody);
    }
  }
}
$(document).on('click', 'button.addlocation', function(){
  form_change = true;
  var $button = $(this);
  $button.prop('disabled', true);
  var $input = $('input.location', $(this).closest('.input-group'));
  $input.val(null);
  var $table_tbody = $('table>tbody', $(this).closest('.form-group'));
  addElement($button.data(),$table_tbody);
  $.removeData($button);
});
$(document).on('click', 'button.deletelocation', function(){
  form_change = true;
  $(this).closest('tr').remove();
});
var submitCityBreakSettings = function(e){
  console.log('submitting form');
  var departure_data = $('form[name=departure_locations]').serializeArray();
  var arival_data = $('form[name=arival_locations]').serializeArray();
  var data = {};
  for (var i = 0; i < departure_data.length; i++){
    data['departure_' + departure_data[i]['name']] = departure_data[i]['value'];
  }
  for (var i = 0; i < arival_data.length; i++){
    data['arival_' + arival_data[i]['name']] = arival_data[i]['value'];
  }
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  data['<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>'] = "<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>";
  <?php } ?>
  $.ajax({
    url: '<?php echo site_url('backend/sitemap/citybreak/save'); ?>',
    method: 'POST',
    dataType: 'json',
    data: data
  }).done(function(response){
    form_change = false;
    console.log(response);
    showMessage($err_container,'Informatiile au fost salvate','success');
  });
}
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>