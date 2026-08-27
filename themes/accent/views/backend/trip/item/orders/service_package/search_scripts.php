<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if($can_write){ ?>
<script>
(function($){
  var package_search_data = <?php echo json_encode($this->package_search_data); ?>, package_results;
  var $error_container = $('#result_service_package_form');
  var $fellow_info_container = $('#service_package_form_fellows');
  var $fellow_info_wrapper = $('#service_package_form_fellows_form_wrapper');
  var $room_packages_loading = $('#service_package_room_packages_loading');
  var $room_packages = $('#service_package_room_packages');
  var $package_details = $('#service_package_package_details');
  var $service_package_tab = $('#service_package_tab');
  var $navigation = $('#service_package_results_navigation');
  
  var today_moment = moment().startOf('day');
  var tomorrow_moment = moment(today_moment).add(1, 'days');
  $('#service_package_search_checkout').makeCaleranDatepicker({
    startEmpty: false,
    minDate: tomorrow_moment,
    endDate: tomorrow_moment,
    startDate: tomorrow_moment
  }).makeInputmaskDate();
  $('#service_package_search_checkin').makeCaleranDatepicker({
    startEmpty: false,
    minDate: today_moment,
    startDate: today_moment
  }).makeInputmaskDate().on('change', function(){
    var val_moment = moment(this.value, 'DD.MM.Y');
    if(!val_moment.isValid()){
      return;
    }
    var val_tomorrow_moment = val_moment.add(1,'day');
    var $checkout_caleran = $('#service_package_search_checkout').data("caleran");
    $checkout_caleran.config.minDate = val_tomorrow_moment;
    var checkout_val = $('#service_package_search_checkout').val();
    var checkout_val_moment = moment(checkout_val, 'DD.MM.Y');
    if(!checkout_val_moment.isValid() || checkout_val_moment.isBefore(val_tomorrow_moment)){
      $checkout_caleran.config.startDate = val_tomorrow_moment;
      $checkout_caleran.config.endDate = val_tomorrow_moment;
      checkout_val_moment = val_tomorrow_moment;
      checkout_val = checkout_val_moment.format('DD.MM.Y');
      $('#service_package_search_checkout').val(checkout_val);
      $('#service_package_search_checkout').focus();
    }
  });
  $('#service_package_search_checkout').on('change blur', function(){
    $('#service_package_search_rooms_table .child-birth_date').trigger('update-child-birth_date');
  });
  function fixPackageSearchDates(){
    var service_package_search_checkin = $('#service_package_search_checkin').val();
    var service_package_search_checkout = $('#service_package_search_checkout').val();
    var start_date = moment(service_package_search_checkin,'DD.MM.Y');
    var end_date = moment(service_package_search_checkout,'DD.MM.Y');
    if(end_date && start_date){
      if(end_date.isBefore(start_date)){
        $('#service_package_search_checkin').val(service_package_search_checkout);
        $('#service_package_search_checkout').val(service_package_search_checkin);
      }
    }
  }
  var package_room_index = 0;
  $('#service_package_form').on('click','.btn-add-room', function(){
    var $tbody = $(this).closest('table').children('tbody');
    var $new_tr = $('#hotel-room-model').clone().removeAttr('id').data('index',package_room_index);
    $('>td:nth-child(3)>div', $new_tr).remove();
    $('input', $new_tr).val(1).attr('name','occupancy[' + package_room_index + '][adt]');
    package_room_index++;
    $new_tr.appendTo($tbody);
    // if(!first_room){
      // $('input', $new_tr).select();
    // }
    first_room = false;
  }).on('click','.btn-delete-room', function(){
    var $tbody = $(this).closest('tbody');
    if($tbody.children('tr').length == 1){
      var $new_tr = $('tr:first-child',$tbody);
      $('>td:nth-child(3)>div', $new_tr).remove();
      $('input', $new_tr).val(1);
    } else {
      $(this).closest('tr').remove();
    }
  }).on('click','.btn-add-child', function(){
    fixPackageSearchDates();
    var $tr = $(this).closest('tr');
    var index = $tr.data('index')
    var $td = $(this).closest('td');
    var $new_child = $('#hotel-room-child-model').clone().removeAttr('id');
    $('input.child-age', $new_child).attr('name','occupancy[' + index + '][chd][age][]');
    $('input.child-birth_date', $new_child).attr('name','occupancy[' + index + '][chd][birth_date][]');
    
    var service_package_search_checkout = $('#service_package_search_checkout').val();
    if(service_package_search_checkout && service_package_search_checkout !== ''){
      var reference_moment = moment(service_package_search_checkout,'DD.MM.Y').startOf('day');
    } else {
      var reference_moment = moment().startOf('day');
    }
    var min_child_moment = moment([parseInt(reference_moment.format('Y')) - 18, parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).add(1,'days').startOf('day');
    var max_child_moment = moment().startOf('day');
    $('input.child-birth_date', $new_child).makeCaleranDatepicker({
      startEmpty: true,
      minDate: min_child_moment,
      maxDate: max_child_moment,
      startDate: max_child_moment
    }).makeInputmaskDate().on('change blur', function(e){
      $(this).trigger('update-child-birth_date');
    }).on('update-child-birth_date', function(e){
      console.log('updating-child-value', this, this.value);
      if(!this.value || this.value===''){
        return;
      }
      var service_package_search_checkout = $('#service_package_search_checkout').val();
      if(service_package_search_checkout && service_package_search_checkout !== ''){
        var reference_moment = moment(service_package_search_checkout,'DD.MM.Y').startOf('day');
      } else {
        var reference_moment = moment().startOf('day');
      }
      var val_moment = moment(this.value,'DD.MM.Y').startOf('day');
      var age_in_years = reference_moment.diff(val_moment,'years');
      $('input.child-age',$(this).closest('.package-room-child')).val(age_in_years);
    });
    $new_child.appendTo($td);
    // $('input.child-birth_date', $new_child).select();
  }).on('click','.btn-remove-child', function(){
    var $child = $(this).closest('div.input-group');
    $child.remove();
  });
  
  var first_room = true;
  $('#service_package_form .btn-add-room')[0].click();
  function interpretNoPackagesResponse(result){
    var $search_container_header = $('a[href="#service_hotel_form_container"].collapsed');
    if($search_container_header.length){
      $search_container_header[0].click();
    }
    $fellow_info_wrapper.hide();
    setSearchStatus(true);
    $error_container.empty();
    // clearFilters();
    if(result.status == 'fail'){
      showMessage($error_container,'Eroare in cautarea packetelor','warning');
    } else {
      showMessage($error_container,'Nu au fost gasite rezultate','warning');
    }
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    $('#service_package_results').empty();
  }
  function setSearchAndInitiate(){
    if(!search_is_over){
      console.log('setSearchAndInitiate','Search is not over, aborting');
      return false;
    }
    // clearFilters();
    // $('.package-sort-by', $service_package_tab).prop('disabled', true);
    if($navigation.data("twbs-pagination")){
      $navigation.twbsPagination('destroy');
    }
    $('#result_service_package_form_fellows_form').empty();
    $('#service_package_results').empty();
    $('#service_package_reserve_submit').prop('disabled',true);
    // $('#service_package_search_sort_price').val('1');
    // $('#service_package_search_sort_stars').val('0');
    setSearchStatus(false);
    $error_container.empty();
    $fellow_info_wrapper.hide();
    $package_details.empty();
    
    showMessage($error_container,'Se cauta packetele <i class="fa fa-spinner fa-spin"></i>','warning');
    
    $.ajax({
      url: '<?php echo site_url('trip/Packages/setSearchAndInitiate'); ?>',
      method: 'post',
      dataType: 'json',
      data: package_search_data
    }).done(function(resp, textStatus, jqXHR){
      console.log('setpackageSearchAndInitiate',resp);
      $error_container.empty();
      if(!resp.status || resp.status !== 'success' || !resp.results.total_items){
        interpretNoPackagesResponse(resp);
        return;
      }
      package_search_data = resp.data;
      package_results = resp;
      setSearchStatus(true);
      // loadFilters();
      // interpretResults();
      
      $('#service_package_reserve_submit').prop('disabled',false);
      setSearchStatus(true);
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('setpackageSearchAndInitiate',jqXHR, textStatus, errorThrown);
      var resp = {status:'fail',message:errorThrown,textStatus:textStatus,jqXHR:jqXHR};
      interpretNoPackagesResponse(resp);
    });
  }
  function servicePackageFormSubmitCallback($form,resp,$err_container){
    if(resp.status !== 'success'){
      return true;
    }
    package_search_data = resp.data;
    console.log('servicePackageFormSubmitCallback',package_search_data);
    setSearchAndInitiate();
  }
  var search_is_over = true;  
  function setSearchStatus(search_status){
    $('#service_package_search_submit', $service_package_tab).prop('disabled',!search_status);
    $('#service_package_reserve_submit').prop('readonly',!search_status);
    $('.package-sort-by', $service_package_tab).prop('readonly',!search_status);
    search_is_over = search_status;
  }
  $('#service_package_form').on('submit',function(){
    if(!search_is_over){
      console.log('service_package_form', 'submit','Search is not over, aborting');
      return false;
    }
    basicFormPostSubmit(this,this.action,servicePackageFormSubmitCallback);
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  