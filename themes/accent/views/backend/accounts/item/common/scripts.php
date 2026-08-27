<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$can_write = $this->_method !='view';
$user = $this->view_data['user'];
themeFunctions::loadModule('helpers/countries/json', __FILE__ . '/json_selections');
themeFunctions::loadModule('helpers/titles/json', __FILE__ . '/json_selections');
?>
<form id="profileFellowAdd" name="profileFellowAdd" method="POST" onsubmit="return false;"></form>
<form id="profileFellowEdit" name="profileFellowEdit" method="POST" onsubmit="return false;"></form>
<?php themeFunctions::loadAddons(__FILE__ . '/json_selections'); ?>
<script>
(function($){
<?php if($can_write){ ?>
  var reference_moment = moment().startOf('day');
  var min_adult_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
  var max_adult_moment = moment([parseInt(reference_moment.format('Y')) - 18, parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).startOf('day');
    
  $('input[type=text].input-birth_date').makeCaleranDatepicker({
    minDate: min_adult_moment,
    maxDate: max_adult_moment,
    startDate: max_adult_moment
  }).makeInputmaskDate();
  $('#profile_phone_prefix').select2_4({theme:'bootstrap',placeholder:'Alegeti', allowClear:true, data: select2_countries_prefix_selections, width: '100%'});
  $('#contact_phone_prefix').select2_4({theme:'bootstrap',placeholder:'Alegeti', allowClear:true, data: select2_countries_prefix_selections, width: '100%'});
  $('#invoice_pf_phone_prefix').select2_4({theme:'bootstrap',placeholder:'Alegeti', allowClear:true, data: select2_countries_prefix_selections, width: '100%'});
  $('#invoice_pj_phone_prefix').select2_4({theme:'bootstrap',placeholder:'Alegeti', allowClear:true, data: select2_countries_prefix_selections, width: '100%'});
  
  $('#profile_title').select2_4({theme:'bootstrap',placeholder:'Alegeti',minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
  $('#fellow_add_title').select2_4({theme:'bootstrap',placeholder:'Alegeti', allowClear:true,minimumResultsForSearch:10, data: select2_titles_prefix_selections, width: '100%'});
  $('#fellow_edit_title').select2_4({theme:'bootstrap',placeholder:'Alegeti', allowClear:true,minimumResultsForSearch:10, data: select2_titles_prefix_selections, width: '100%'});
  
  $('#flight_special_assistance').select2_4({theme:'bootstrap',placeholder:'Alegeti', allowClear:true,minimumResultsForSearch:10, width: '100%'});
  $('#flight_prefered_spot').select2_4({theme:'bootstrap',placeholder:'Alege', allowClear:true,minimumResultsForSearch:10, width: '100%'});
  
  $('#profile_country').select2_4({theme:'bootstrap',placeholder:'Alege', allowClear:true, data: select2_countries_selections, width: '100%'});
  $('#passport_country').select2_4({theme:'bootstrap',placeholder:'Alege', allowClear:true, data: select2_countries_selections, width: '100%'});
  $('#fellow_add_country').select2_4({theme:'bootstrap',placeholder:'Alege', allowClear:true, data: select2_countries_selections, width: '100%'});
  $('#fellow_edit_country').select2_4({theme:'bootstrap',width:'100%', allowClear:true,placeholder:'Alege', data: select2_countries_selections, width: '100%'});
  $('#invoice_pf_country').select2_4({theme:'bootstrap',placeholder:'Alege', allowClear:true, data: select2_countries_selections, width: '100%'});
  $('#invoice_pj_country').select2_4({theme:'bootstrap',placeholder:'Alege', allowClear:true, data: select2_countries_selections, width: '100%'});
  
  
  $('#flight_departure_airport').autocomplete({
    source: function(request, response){
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
                location_code: item.LocationCode,
                city_id: item.CityId,
                city_code: item.CityCode,
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
      }).fail(function(){
        $error_container.empty();
        showMessage($error_container,'Operatia a esuat, va rugam sa reincarcati pagina','danger');
      });
    },
    minLength: 2,
    select: function( event, ui ) {
      $('#flight_departure_airport_location_id').val(ui.item.location_id);
      $('#flight_departure_airport_location_code').val(ui.item.location_code);
      $('#flight_departure_airport_location_name').val(ui.item.location_name);
      $('#flight_departure_airport_full_location_name').val(ui.item.value);
      $('#flight_departure_airport_city_id').val(ui.item.city_id);
      $('#flight_departure_airport_city_code').val(ui.item.city_code);
      $('#flight_departure_airport_city_name').val(ui.item.city_name);
      $('#flight_departure_airport_country_id').val(ui.item.country_id);
      $('#flight_departure_airport_country_name').val(ui.item.country_name);
    }
  }).blur(function(){
    if(!this.value.length || this.value !== $('#flight_departure_airport_full_location_name').val()){
      this.value = '';
      $('#flight_departure_airport_location_id').val(0);
      $('#flight_departure_airport_location_code').val('');
      $('#flight_departure_airport_location_name').val('');
      $('#flight_departure_airport_full_location_name').val('');
      $('#flight_departure_airport_city_id').val(0);
      $('#flight_departure_airport_city_code').val('');
      $('#flight_departure_airport_city_name').val('');
      $('#flight_departure_airport_country_id').val(0);
      $('#flight_departure_airport_country_name').val('');
    }
  });
<?php } ?>
  var $fellow_edit_row;
  $('#fellowEditModal').on('hide.bs.modal', function (event) {
    $fellow_edit_row = null;
  });
  $('#fellowEditModal').on('show.bs.modal', function (event) {
    var $button = $(event.relatedTarget);
    $fellow_edit_row = $button.closest('tr');
    var title = $button.data('title');
    var firstname = $button.data('firstname');
    var lastname = $button.data('lastname');
    var birth_date = $button.data('birth_date');
    var country = $button.data('country');
    var passport_number = $button.data('passport_number');
    <?php if($can_write){ ?>
    $('#fellow_edit_title').val(title).trigger('change.select2_4');
    $('#fellow_edit_firstname').val(firstname);
    $('#fellow_edit_lastname').val(lastname);
    $('#fellow_edit_birth_date').val(birth_date);
    $('#fellow_edit_country').val(country).trigger('change.select2_4');
    $('#fellow_edit_passport_number').val(passport_number);
    <?php } else { ?>
    if(!countries_selections[country]){
      country = false;
    }
    if(!titles_selections[title]){
      title = false;
    }
    $('#fellow_edit_title').text(title ? titles_selections[title] : '');
    $('#fellow_edit_firstname').text(firstname);
    $('#fellow_edit_lastname').text(lastname);
    $('#fellow_edit_birth_date').text(birth_date);
    $('#fellow_edit_country').text(country ? countries_selections[country].text : '');
    $('#fellow_edit_passport_number').text(passport_number);
    <?php } ?>
  });
  var max_fellows = 30;
  function addFellow(data,$replace_row){
    var $tfoot = $('#profileFellowsTable > tfoot');
    $tfoot.hide();
    var $tbody = $('#profileFellowsTable > tbody');
    var $new_tr = $('<tr class="ac_inc" />');
    if($replace_row && $replace_row.length){
      $replace_row.replaceWith($new_tr);
    } else {
      $new_tr.appendTo($tbody);
    }
    var $td_crt = $('<td class="ac_dis text-center" />');
    $td_crt.appendTo($new_tr);
    var $title = $('<input type="hidden" name="fellows[title][]" />').val(data.title);
    $title.appendTo($td_crt);
    var $firstname = $('<input type="hidden" name="fellows[firstname][]" />').val(data.firstname);
    $firstname.appendTo($td_crt);
    var $lastname = $('<input type="hidden" name="fellows[lastname][]" />').val(data.lastname);
    $lastname.appendTo($td_crt);
    var $birth_date = $('<input type="hidden" name="fellows[birth_date][]" />').val(data.birth_date);
    $birth_date.appendTo($td_crt);
    var $country = $('<input type="hidden" name="fellows[country][]" />').val(data.country);
    $country.appendTo($td_crt);
    var $passport_number = $('<input type="hidden" name="fellows[passport_number][]" />').val(data.passport_number);
    $passport_number.appendTo($td_crt);
    var $td_title = $('<td/>');
    $td_title.appendTo($new_tr);
    var $modal_link = $('<a href="javascript:void(0);" data-toggle="modal" data-target="#fellowEditModal" />');
    $modal_link.data('title', data.title);
    $modal_link.data('firstname', data.firstname);
    $modal_link.data('lastname', data.lastname);
    $modal_link.data('birth_date', data.birth_date);
    $modal_link.data('country', data.country);
    $modal_link.data('passport_number', data.passport_number);
    $row_title = $('<span />').text((titles_selections[data.title] ? titles_selections[data.title] + ' ' : '') + data.lastname + ' ' + data.firstname);
    $row_title.appendTo($modal_link);
    <?php if($can_write){ ?>
    $modal_link.append(' <i class="fa fa-pencil"></i>');
    <?php } else { ?>
    $modal_link.append(' <i class="fa fa-eye"></i>');
    <?php } ?>
    $modal_link.appendTo($td_title);
    <?php if($can_write){ ?>
    var $td_action = $('<td class="contains-form-control"/>');
    $td_action.appendTo($new_tr);
    var $action_group = $('<div class="btn-group" />');
    $action_group.appendTo($td_action);
    $action_group.append('<span class="btn btn-info move-fellow"><i class="fa fa-arrows-v"></i></span>');
    $action_group.append('<button type="button" class="btn btn-danger delete-fellow"><i class="fa fa-trash"></i></button>');
    <?php } ?>
    if($tbody.children('tr').length >= max_fellows){
      $('#fellowAddModal').hide('slow');
    }
  }
  <?php if($can_write){ ?>
  $('#profileFellowAdd').on('submit',function(){
    var data = {};
    data.title = $('#fellow_add_title').val();
    data.firstname = $('#fellow_add_firstname').val();
    data.lastname = $('#fellow_add_lastname').val();
    data.birth_date = $('#fellow_add_birth_date').val();
    data.country = $('#fellow_add_country').val();
    data.passport_number = $('#fellow_add_passport_number').val();
    
    addFellow(data);
    $('#profileFellowAdd')[0].reset();
    $('#fellow_add_country').trigger('change.select2_4');
    $('#fellow_add_title').trigger('change.select2_4');
  });
  $('#profileFellowEdit').on('submit',function(){
    var data = {};
    data.title = $('#fellow_edit_title').val();
    data.firstname = $('#fellow_edit_firstname').val();
    data.lastname = $('#fellow_edit_lastname').val();
    data.birth_date = $('#fellow_edit_birth_date').val();
    data.country = $('#fellow_edit_country').val();
    data.passport_number = $('#fellow_edit_passport_number').val();
    
    addFellow(data,$fellow_edit_row);
    $('#profileFellowEdit')[0].reset();
    $('#fellow_edit_country').trigger('change.select2_4');
    $('#fellow_edit_title').trigger('change.select2_4');
    $('#fellowEditModal').modal('hide');
  });
  $(document).on('click', 'button.delete-fellow', function(){
    $(this).closest('tr').remove();
    var $tbody = $('#profileFellowsTable > tbody');
    $('#fellowAddModal').show('slow');
    if(!$tbody.children('tr').length){
      var $tfoot = $('#profileFellowsTable > tfoot');
      $tfoot.show();
    }
  });
  $('#profileFellowsTable > tbody').sortable({
    revert: true,
    items: ">tr",
    handle: ".move-fellow",
    start: function(e, ui){
      ui.placeholder.width(ui.item.width());
      ui.placeholder.height(ui.item.height());
    }
  });
  <?php } ?>
  <?php 
  $fellows = $user->getFellows();
  ?>
  var fellows = <?php echo json_encode($fellows, JSON_UNESCAPED_UNICODE); ?>;
  if(fellows && fellows.length){
    for(var i=0;i<fellows.length;i++){
      addFellow(fellows[i]);
    }
  }
  $('#profileFellows').prepend('<input type="hidden" name="fellows" />');
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  