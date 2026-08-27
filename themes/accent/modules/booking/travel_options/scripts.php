<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('helpers/countries/json', __FILE__ . '/json_selections'); ?>
<?php themeFunctions::loadModule('helpers/titles/json', __FILE__ . '/json_selections'); ?>
<?php themeFunctions::loadAddons(__FILE__ . '/json_selections'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$u0 = $this->_ci->uri->segment(0);
$u1 = $this->_ci->uri->segment(1);
?>
<?php $user = $this->_ci->user; ?>
<?php 
$testing = $u0 == 'paralela45' && ENVIRONMENT !== 'production'; // TODO - Remove
if($testing){
  $fellows = array();
  $total_adults = $this->view_data['total_adults'];
  $children_ages = $this->view_data['children_ages'];
  $user->firstname='TEST';
  $user->lastname='TEST';
  $user->title='mr';
  $user->country='RO';
  $total_adults--;
  $adult_birthdate = new DateTime('30 years ago');
  for($i=1; $i<=$total_adults; $i++){
    $fellow = new stdClass;
    $fellow->title='mr';
    $fellow->firstname='TEST';
    $fellow->lastname='TEST';
    $fellow->country='RO';
    $fellow->birth_date = $adult_birthdate->format('d.m.Y');
    $fellows[] = $fellow;
  }
  foreach($children_ages as $child_age){
    $fellow = new stdClass;
    $fellow->title='mr';
    $fellow->firstname='TEST';
    $fellow->lastname='TEST';
    $fellow->country='RO';
    $child_birthdate = new DateTime(($child_age) . ' years ago');
    $fellow->birth_date = $child_birthdate->format('d.m.Y');
    $fellows[] = $fellow;
  }
} else {
  $fellows = $user->getFellows();
}
?>
<script type="text/javascript">
;(function($){
var fellows = <?php echo json_encode($fellows); ?>;
var total_passengers = 0;
var reference_moment = moment();
<?php if($u0 == 'trip' && $u1 == 'flight') { ?>
for(var i = 0; i<flight_data.FareDetails.PaxFare.length; i++){
  var pax_item = flight_data.FareDetails.PaxFare[i];
  var pax_count = parseInt(pax_item.Count);
  total_passengers += pax_count;
}
<?php } elseif(($u0 == 'trip' && in_array($u1, array('hotel','citybreak','package'))) || ($u0 == 'paralela45' && in_array($u1, array('strainatate','circuit')))) { ?>
total_passengers = <?php echo $this->total_people; ?>;
reference_moment = moment('<?php echo $this->reference_date; ?>','Y-MM-DD');
<?php } ?>
var min_adult_moment = moment([parseInt(reference_moment.format('Y')) - 150]);
var min_child_moment = reference_moment.subtract(18,'years');
var index = 0;
function addPassenger(data, $after, focus){
  // var is_first = typeof $after !== 'undefined' && $after.length;
  if(typeof data === 'undefined' || data.empty){
    data = {
      empty:true
    };
  }
  var $form = $('#infoPasagerForm');
  var $form_rows = $('>div.passenger-row', $form);
  var $row_model = $('#passenger-model > .passenger-row').first().clone();
  if($form_rows.length==total_passengers){
    return false;
  }
  
  $passenger_title = $("select.passenger-title",$row_model);
  $passenger_title.attr({
    'id': 'passenger_title_' + index,
    'form': 'bookingCheckout',
    'name': 'passenger[title][]'
  });
  $passenger_title.parent().children('label:first').attr('for', $passenger_title.attr('id'));
  
  $passenger_lastname = $("input.passenger-lastname",$row_model);
  $passenger_lastname.attr({
    'id': 'passenger_lastname_' + index,
    'form': 'bookingCheckout',
    'name': 'passenger[lastname][]'
  });
  if(data.lastname){
    $passenger_lastname.val(data.lastname);
  }
  $passenger_lastname.parent().children('label:first').attr('for', $passenger_lastname.attr('id'));
  
  $passenger_firstname = $("input.passenger-firstname",$row_model);
  $passenger_firstname.attr({
    'id': 'passenger_firstname_' + index,
    'form': 'bookingCheckout',
    'name': 'passenger[firstname][]'
  });
  if(data.firstname){
    $passenger_firstname.val(data.firstname);
  }
  $passenger_firstname.parent().children('label:first').attr('for', $passenger_firstname.attr('id'));
  
  $passenger_country = $("select.passenger-country",$row_model);
  $passenger_country.attr({
    'id': 'passenger_country_' + index,
    'form': 'bookingCheckout',
    'name': 'passenger[country][]'
  });
  $passenger_country.parent().children('label:first').attr('for', $passenger_country.attr('id'));
  
  $passenger_birth_date = $("input.passenger-birth_date",$row_model);
  $passenger_birth_date.attr({
    'id': 'passenger_birth_date_' + index,
    'form': 'bookingCheckout',
    'name': 'passenger[birth_date][]'
  });
  if(data.birth_date){
    $passenger_birth_date.val(data.birth_date);
  }
  $passenger_birth_date.parent().children('label:first').attr('for', $passenger_birth_date.attr('id'));
  if(typeof $after !== 'undefined' && $after.length){
    $('.addPasager',$after).hide();
    $('.removePasager',$after).show();
    $('.addPasager',$row_model).show();
    $('.removePasager',$row_model).hide();
    $row_model.insertAfter($after);
    if(focus){
      $('select.passenger-title', $row_model).focus();
    }
  } else {
    $('.addPasager',$form).hide();
    $('.addPasager',$row_model).show();
    $('.removePasager',$row_model).hide();
    $row_model.appendTo($form);
  }
  if($form_rows.length + 1==total_passengers){
    $('.addPasager:visible',$form).prop('disabled', true);
  }
  $passenger_birth_date.formatter({
    pattern: '{{99}}.{{99}}.{{9999}}'
  });
  $passenger_birth_date.caleran({
    startEmpty: true,
    singleDate: true,
    locale: 'ro',
    enableKeyboard: false,
    autoCloseOnSelect: true,
    format: 'DD.MM.Y'
  });
  $passenger_title.select2_4({theme:'bootstrap',placeholder:'Alegeti', minimumResultsForSearch:10, data: select2_titles_prefix_selections, width: '100%'});
  if(data.title){
    $passenger_title.val(data.title).trigger('change.select2_4');
  }
  $passenger_country.select2_4({theme:'bootstrap',placeholder:'Alege', data: select2_countries_selections, width: '100%'});
  if(data.country){
    $passenger_country.val(data.country).trigger('change.select2_4');
  }
  $row_model.trigger('update-passenger-data');
  $passenger_birth_date.on('blur',function(){
    var $passenger_birth_date_caleran = $passenger_birth_date.data("caleran");
    if(this.value.length){
      $passenger_birth_date_caleran.globals.firstValueSelected = true;
      $passenger_birth_date_caleran.globals.startEmpty = false;
    } else {
      $passenger_birth_date_caleran.globals.firstValueSelected = false;
      $passenger_birth_date_caleran.globals.startEmpty = true;
    }
    $row_model.trigger('update-passenger-data');
  }).on('keyup',function(e){
    var $passenger_birth_date_caleran = $passenger_birth_date.data("caleran");
    $passenger_birth_date_caleran.hideDropdown();
  });
  index++;
  return true;
};
$('#infoPasagerForm').on('update-passenger-data','div.passenger-row',function(){
  console.log('triggering update-passenger-data');
  var $this = $(this);
  var $passenger_title = $("select.passenger-title",$this);
  var passenger_title = $passenger_title.val();
  var $passenger_birth_date = $("input.passenger-birth_date",$this);
  var passenger_birth_date = $passenger_birth_date.val();
  
  var $passenger_birth_date_caleran = $("input.passenger-birth_date",$this).data("caleran");
  console.log($passenger_birth_date_caleran, $this);
  var min_birth_date = min_adult_moment;
  var max_birth_date = moment();
  var start_birth_date;
  /* console.log(passenger_title);
  if(passenger_title == 'chd'){
    min_birth_date = min_child_moment;
  } else if(passenger_title == '') {
    min_birth_date = min_adult_moment;
  } else {
    min_birth_date = min_adult_moment;
    max_birth_date = min_child_moment.subtract(1,'days');
  } */
  start_birth_date = max_birth_date;
  $passenger_birth_date_caleran.fetchInputs();
  $passenger_birth_date_caleran.config.minDate = min_birth_date;
  $passenger_birth_date_caleran.config.maxDate = max_birth_date;
  if(!passenger_birth_date.length){
    $passenger_birth_date_caleran.config.startEmpty = true;
    $passenger_birth_date_caleran.config.startDate = start_birth_date;
  } else {
    $passenger_birth_date_caleran.config.startEmpty = false;
    console.log('date is not empty');
    var passenger_birth_date_moment = moment(passenger_birth_date,'DD.MM.Y');
    var clear_input = true;
    if(passenger_birth_date_moment.isValid()){
      clear_input = false;
      if(passenger_birth_date_moment.isBefore(min_birth_date)){
        console.log('before min');
        clear_input = true;
      } else if(passenger_birth_date_moment.isAfter(max_birth_date)){
        console.log('after max');
        clear_input = true;
      }
    } else {
      console.log('not valid');
    }
    $passenger_birth_date_caleran.config.startDate = start_birth_date;
    if(clear_input){
      console.log('clearing input');
      $passenger_birth_date_caleran.globals.firstValueSelected = false;
      $passenger_birth_date_caleran.globals.startEmpty = true;
      $passenger_birth_date_caleran.config.target.val('');
    }
  }
});
$('#infoPasagerForm').on('change','select.passenger-title',function(){
  console.log('changed_title');
  $(this).closest('div.passenger-row').trigger('update-passenger-data');
});
function removePassenger($row){
  if(!$row || !$row.length){
    return;
  }
  var $form = $('#infoPasagerForm');
  var $form_rows = $('>div.passenger-row', $form);
  if($form_rows.length<=1){
    return;
  }
  $('.addPasager:visible',$form).prop('disabled', false);
  $($row).remove();
};
$(document).on('click','.addPasager',function(){
  addPassenger({
    title: 'mr',
    country: user_passenger.country,
    lastname: '',
    firstname: '',
    birth_date: ''
  },$(this).closest('div.passenger-row'),true);
});
$(document).on('click','.removePasager',function(){
  removePassenger($(this).closest('div.passenger-row'));
});
<?php $user_passenger = new stdClass; 
$user_passenger->title = $user->title;
$user_passenger->lastname = $user->lastname;
$user_passenger->firstname = $user->firstname;
$user_passenger->country = $user->country;
$user_passenger->birth_date = $user->getBirthDate();
?>
var user_passenger = <?php echo json_encode($user_passenger); ?>;
addPassenger(user_passenger);
var $form = $('#infoPasagerForm');
var $form_rows = $('>div.passenger-row', $form);
if(fellows && fellows.length){
  for(var i=0;i<fellows.length;i++){
    var added_passenger = addPassenger(fellows[i],$('>div.passenger-row:last-child', $form));
    if(!added_passenger){
      break;
    }
  }
}
function addRestOfPassengers(){
  if($form_rows.length<total_passengers){
    for(var i=0;i<total_passengers-$form_rows.length;i++){
      addPassenger({
        title: 'mr',
        country: user_passenger.country,
        lastname: '',
        firstname: '',
        birth_date: ''
      },$('>div.passenger-row:last-child', $form));
    }
  }
}
window.addRestOfPassengers = addRestOfPassengers;
})(jQuery);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>