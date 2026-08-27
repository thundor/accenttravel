<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php
$data = $this->package_search_data;
// echo '<pre>';
// echo '<code>';
// print_r($data);
// echo '</code>';
// echo '</pre>';
?>
<script type="text/javascript">
var package_search_data = <?php echo json_encode($data); ?>;
console.log('package_search_data', package_search_data);
var package_submit_function;
var search_is_over = true;
(function($){$(function() {
  <?php
  if($this->_ci->user->can('backend-access')){ ?>
  function setFiltersLink(){
    var my_obj = {};
    my_obj.n = 1;
    if(package_search_data.project_id){
      my_obj.project_id = package_search_data.project_id;
    }
    if(package_search_data.hotel_name){
      my_obj.hotel = package_search_data.hotel_name;
    }
    if(package_search_data.category){
      my_obj.origin = package_search_data.category;
    }
    if(parseInt(package_search_data.city_id)){
      my_obj.destination = $.trim($('#destinatiePax > option[value=' + parseInt(package_search_data.city_id) + ']').text());
    }
    if(package_search_data.start_date){
      my_obj.sdate = package_search_data.start_date;
    }
    if(package_search_data.nights){
      my_obj.nights = package_search_data.nights;
    }
    if(package_search_data.occupancy.length){
      my_obj.o = package_search_data.occupancy;
    }
    var recursiveDecoded = decodeURIComponent( $.param( my_obj ) );
    var filters_link = 'trip/packages?' + recursiveDecoded;
    $('.trip_package_search_link').val(filters_link);
  }
  <?php } ?>
  if(package_search_data){
    // $('#categoriePax').val(package_search_data.project_id);
    $('#categoriePax').val(package_search_data.category);
    $('#destinatiePax').val(package_search_data.city_id);
    $('#numeHotelPax').val(package_search_data.hotel_name);
    $('#datePax').val(moment(package_search_data.start_date,'Y-MM-DD').format('DD/MM/Y'));
    $('#categPax').val(package_search_data.nights);
    if(package_search_data.occupancy && package_search_data.occupancy.length){
      for (var i=0; i<package_search_data.occupancy.length; i++){
        var room_index = i+1;
        if(room_index > 3){
          break;
        }
        var occupancy = package_search_data.occupancy[i];
        $('#cam' + room_index + 'Pax').show();
        $('#adultiCam' + room_index + 'Pax').val(occupancy.adt);
        if(typeof package_search_data.occupancy[room_index] !== 'undefined'){
          if(room_index == 3 || room_index == package_search_data.occupancy){
            $('#addCam' + (room_index + 1) + 'Pax').parent().show();
            $('#remCam' + (room_index) + 'Pax').parent().show();
          } else {
            $('#addCam' + (room_index + 1) + 'Pax').parent().hide();
            $('#remCam' + (room_index) + 'Pax').parent().hide();
          }
          // console.log('#remCam' + (room_index) + 'Pax');
        }
        $('#cam' + room_index + 'Pax .varsteCopii').hide();
        $('#cam' + room_index + 'Pax .varsteCopii #varstaCop1Cam' + room_index + 'Pax').hide();
        $('#cam' + room_index + 'Pax .varsteCopii #varstaCop2Cam' + room_index + 'Pax').hide();
        $('#cam' + room_index + 'Pax .varsteCopii p#v1Pax' + room_index + '').hide();
        $('#cam' + room_index + 'Pax .varsteCopii p#v2Pax' + room_index + '').hide();
        if(occupancy.chd){
          $('#copiiCam' + room_index + 'Pax').val(occupancy.chd.length+1);
          $('#cam' + room_index + 'Pax .varsteCopii').show();
          for (var j=0; j<occupancy.chd.length; j++){
            var child_index = j+1;
            if(child_index>2){
              break;
            }
            var child_age = occupancy.chd[j];
            $('#v' + child_index + 'Pax' + room_index + '').show();
            $('#varstaCop' + child_index + 'Cam' + room_index + 'Pax').val(child_age).show();
          }
        }
      }
    }
  }
  function setSearchStatus(search_status){
    if(search_status){
      $('#packages-loading-screen').addClass('inactive');
      $('#cautaPax').bootstrap_button('loading');
    } else {
      $('#packages-loading-screen').removeClass('inactive');
      $('#cautaPax').bootstrap_button('reset');
    }
    search_is_over = search_status;
  }
  window.setPackageSearchStatus = setSearchStatus;
  function setData($form){
    console.log('setting data',$form);
    // package_search_data.project_id = $('#categoriePax', $form).val();
    package_search_data.category = $('#categoriePax', $form).val();
    package_search_data.package_id = $('#packageId', $form).val();
    package_search_data.occupancy = [];
    
    var ocuppancy_1 = {};
    ocuppancy_1.adt = $('#adultiCam1Pax').val();
    var children = $('#copiiCam1Pax').val();
    var ages = [];
    if(children>1){
      ocuppancy_1.chd = [];
      ocuppancy_1.chd.push($('#varstaCop1Cam1Pax').val());
      if(children>2){
        ocuppancy_1.chd.push($('#varstaCop2Cam1Pax').val());
      }
    }
    package_search_data.occupancy.push(ocuppancy_1);
    
    if($('#cam2Pax').is(':visible')){
      var ocuppancy_2 = {};
      ocuppancy_2.adt = $('#adultiCam2Pax').val();
      var children = $('#copiiCam2Pax').val();
      var ages = [];
      if(children>1){
        ocuppancy_2.chd = [];
        ocuppancy_2.chd.push($('#varstaCop1Cam2Pax').val());
        if(children>2){
          ocuppancy_2.chd.push($('#varstaCop2Cam2Pax').val());
        }
      }
      package_search_data.occupancy.push(ocuppancy_2);
    }
    if($('#cam3Pax').is(':visible')){
      var ocuppancy_3 = {};
      ocuppancy_3.adt = $('#adultiCam3Pax').val();
      var children = $('#copiiCam3Pax').val();
      var ages = [];
      if(children>1){
        ocuppancy_3.chd = [];
        ocuppancy_3.chd.push($('#varstaCop1Cam3Pax').val());
        if(children>2){
          ocuppancy_3.chd.push($('#varstaCop2Cam3Pax').val());
        }
      }
      package_search_data.occupancy.push(ocuppancy_3);
    }
    
    package_search_data.sort_by = 'MinPrice';
    package_search_data.sort_order = 0;
    // package_search_data.project_id = $('#categoriePax').val();
    package_search_data.category = $('#categoriePax').val();
    package_search_data.city_id = $('#destinatiePax').val();
    package_search_data.hotel_name = $('#numeHotelPax').val();
    package_search_data.nights = $('#categPax').val();
    package_search_data.start_date = null;
    package_search_data.end_date = null;
    var start_date_moment = moment($('#datePax').val(),'DD/MM/Y');
    if(start_date_moment.isValid()){
      package_search_data.start_date = start_date_moment.format('Y-MM-DD');
      package_search_data.end_date = moment(start_date_moment).add(1,'years').format('Y-MM-DD');
    }
    
    var sort_element = $('.package-sort-by').filter(function(){return $(this).val()>0;}).first();
    if(sort_element.length){
      package_search_data.sort_by = sort_element.attr('name');
      package_search_data.sort_order = parseInt(sort_element.val()) - 1;
    }
    <?php
    if($this->_ci->user->can('backend-access')){ ?>
    setFiltersLink();
    <?php } ?>
  }
  window.setPackageData = setData;
  $('#categoriePax').on('change',function(){
    $('#destinatiePax').val('');
  });
  $('#destinatiePax').on('change',function(){
    $('#categoriePax').val('');
  });
  function setSearchAndRedirect(){
    console.log(package_search_data);
    $.ajax({
      url: '<?php echo site_url('trip/packages/setSearch'); ?>',
      method: 'post',
      dataType: 'json',
      data: package_search_data,
      async: true,
      success: function(result,status,xhr){
        console.log(result);
        if(!result.status || result.status !== 'success'){
          setSearchStatus(true);
          return;
        }
        package_search_data = result.data;
        
        setSearchStatus(true);
        window.location.href="<?php echo site_url('trip/packages/search'); ?>";
      },
      error: function(jqXHR,textStatus,error){
        console.log(jqXHR, textStatus, error);
        setSearchStatus(true);
      }
    });
  }
  window.setPackageSearchAndRedirect = setSearchAndRedirect;
  $('#datePax').makeCaleranDatepicker({
    locale: 'ro',
    startEmpty: false,
    minDate: moment(),
    endDate: moment(),
    startDate: moment(),
    format: 'DD/MM/Y',
    onafterselect: function(caleran, startDate, endDate){
      if(!caleran.globals.firstValueSelected){
        return;
      }
      package_search_data.start_date = startDate.format("Y-MM-DD");
    }
  }).makeInputmaskDate2();
  $("#copiiCam1Pax").on("change", function () {
    $(this).find("option:selected").each(function () {
      var optionValue = $(this).attr("value");
      if (optionValue == 2) {
        $("#cam1Pax .varsteCopii").show();
        $("#varstaCop1Cam1Pax").show();
        $("#varstaCop2Cam1Pax").hide();
        $("#cam1Pax .varsteCopii p#v1Pax1").show();
        $("#cam1Pax .varsteCopii p#v2Pax1").hide();
      }
      if (optionValue == 3) {
        $("#cam1Pax .varsteCopii").show();
        $("#varstaCop2Cam1Pax").show();
        $("#varstaCop1Cam1Pax").show();
        $("#cam1Pax .varsteCopii p#v1Pax1").show();
        $("#cam1Pax .varsteCopii p#v2Pax1").show();
      }
      if (optionValue == 1) {
        $("#cam1Pax .varsteCopii").hide();
        $("#varstaCop1Cam1Pax").hide();
        $("#varstaCop2Cam1Pax").hide();
        $("#cam1Pax .varsteCopii p#v1Pax1").hide();
        $("#cam1Pax .varsteCopii p#v2Pax1").hide();
      }
    });
  });
  //adaugare & stergere camera 2 hotel
  $("#addCam2Pax").on("click", function () {
    $("#cam2Pax").show();
    $("#addCam2Pax").parent().hide();
    $("#addCam3Pax").parent().show();
    $("#remCam3Pax").parent().show();
  });
  $("#remCam2Pax").on("click", function () {
    $("#cam2Pax").hide();
    $("#addCam2Pax").parent().show();
    $("#cam2Pax .varsteCopii").hide();
    $('#copiiCam2Pax option').prop('selected', function () {
      return this.defaultSelected;
    });
  });
  $("#copiiCam2Pax").on("change", function () {
    $(this).find("option:selected").each(function () {
      var optionValue = $(this).attr("value");
      if (optionValue == 2) {
        $("#cam2Pax .varsteCopii").show();
        $("#varstaCop1Cam2Pax").show();
        $("#varstaCop2Cam2Pax").hide();
        $("#cam2Pax .varsteCopii p#v1Pax2").show();
        $("#cam2Pax .varsteCopii p#v2Pax2").hide();
      }
      if (optionValue == 3) {
        $("#cam2Pax .varsteCopii").show();
        $("#varstaCop2Cam2Pax").show();
        $("#varstaCop1Cam2Pax").show();
        $("#cam2Pax .varsteCopii p#v1Pax2").show();
        $("#cam2Pax .varsteCopii p#v2Pax2").show();
      }
      if (optionValue == 1) {
        $("#cam2Pax .varsteCopii").hide();
        $("#varstaCop1Cam2Pax").hide();
        $("#varstaCop2Cam2Pax").hide();
        $("#cam2Pax .varsteCopii p#v1Pax2").hide();
        $("#cam2Pax .varsteCopii p#v2Pax2").hide();
      }
    });
  });
  //adaugare & stergere camera 3 hotel
  $("#addCam3Pax").on("click", function () {
    $("#cam3Pax").show();
    $("#addCam3Pax").parent().hide();
    $("#remCam2Pax").parent().hide();
  });
  $("#remCam3Pax").on("click", function () {
    $("#cam3Pax").hide();
    $("#addCam3Pax").parent().show();
    $("#remCam2Pax").parent().show();
    $("#cam3Pax .varsteCopii").hide();
    $('#copiiCam3Pax option').prop('selected', function () {
      return this.defaultSelected;
    });
  });
  $("#copiiCam3Pax").on("change", function () {
    $(this).find("option:selected").each(function () {
      var optionValue = $(this).attr("value");
      if (optionValue == 2) {
        $("#cam3Pax .varsteCopii").show();
        $("#varstaCop1Cam3Pax").show();
        $("#varstaCop2Cam3Pax").hide();
        $("#cam3Pax .varsteCopii p#v1Pax3").show();
        $("#cam3Pax .varsteCopii p#v2Pax3").hide();
      }
      if (optionValue == 3) {
        $("#cam3Pax .varsteCopii").show();
        $("#varstaCop2Cam3Pax").show();
        $("#varstaCop1Cam3Pax").show();
        $("#cam3Pax .varsteCopii p#v1Pax3").show();
        $("#cam3Pax .varsteCopii p#v2Pax3").show();
      }
      if (optionValue == 1) {
        $("#cam3Pax .varsteCopii").hide();
        $("#varstaCop1Cam3Pax").hide();
        $("#varstaCop2Cam3Pax").hide();
        $("#cam3Pax .varsteCopii p#v1Pax3").hide();
        $("#cam3Pax .varsteCopii p#v2Pax3").hide();
      }
    });
  });
  setSearchStatus(true);
  setData();
  if(typeof package_submit_function === 'undefined'){
    window.package_submit_function = function (e){
      if(!search_is_over){
        console.log('A previous search is not complete. Ignoring request.');
        return;
      }
      setSearchStatus(false);
      setData();
      setSearchAndRedirect();
    };
  }
  $(document).on('change click', 'form.package-search', function(e){
    setData();
  });
  $(document).on('submit', 'form.package-search', function(e){
    e.preventDefault();
    $('#numeHotelPax').val('');
    package_submit_function.call(this, e);
  });
  $('#cautaPax').attr('data-loading-text','<i class="fa fa-spinner fa-spin"></i> Se incarca ...');
})
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>