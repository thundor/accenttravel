<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$zones = 0;
if(isset($data['status']) && is_array($data['status'])){
  $zones = count($data['status']);
}
$locations = array(
  'departure' => array(),
  'arrival' => array(),
);
if(isset($data['locations']) && is_array($data['locations'])){
  if(isset($data['locations']['departure']) && is_array($data['locations']['departure'])){
    $locations['departure'] = $data['locations']['departure'];
  }
  if(isset($data['locations']['arrival']) && is_array($data['locations']['arrival'])){
    $locations['arrival'] = $data['locations']['arrival'];
  }
}
$this->_ci->load->model('Trip/Flights_airlines_model');
$airlines = $this->_ci->Flights_airlines_model->getAirlines(array(
  'select' => array(
    '`code` as "id"',
    '`name` as "text"',
    '`image`',
  ),
));

/* echo '<ul class="list-group">';
if ($handle = opendir($this->theme_path . 'assets/images/icons/')) {
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'jpg')
        {
          $filename = $this->theme_path . 'assets/images/icons/' . $file;
          echo  '<li class="list-group-item"><a href="'.$filename.'">'.$filename.'</a></li>';
          $extension = pathinfo($filename, PATHINFO_EXTENSION); 
          $img = getimagesize($filename);
          
          $new_filename = $this->theme_path . 'assets/images/icons/' . basename($file, '.' . $extension) . '.png';
          if(file_exists($new_filename)){
            unlink($filename);
            continue;
          }
          if($img['mime'] == 'image/png'){
            rename($filename, $new_filename);
            continue;
          }
          $image = null;
          switch ($extension) {
            case 'jpg':
            case 'jpeg':
               $image = imagecreatefromjpeg($filename);
            break;
            case 'gif':
               $image = imagecreatefromgif($filename);
            break;
            case 'png':
               $image = imagecreatefrompng($filename);
            break;
          }
          if($image){
            imagepng($image, $new_filename);
          } else {
            // echo  '<li class="list-group-item"><a href="'.$file.'">'.$file.'</a></li>';
          }
        }
    }
    closedir($handle);
}
echo '</ul>'; */
?>
<script>
;(function($){
  var zone_index = <?php echo $zones; ?>;
  var $offers_popular_form = $('#offers_popular_form');
  
  var airlines = <?php echo json_encode($airlines); ?>;
  var forms_data = <?php echo json_encode($locations); ?>;
  $('input.location', $offers_popular_form).autocomplete({
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
                location_code: item.LocationCode,
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
      $button.data('location_code', ui.item.location_code ? ui.item.location_code : '');
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
    var location_id = data.country_id + '-' + data.city_id + '-' + data.location_id + '-' + data.location_code;
    var formname = $table_tbody.closest('.offer-locations').data('type');
    var $new_tr = $('<tr class="ac_inc" />');
    var $td_crt = $('<td class="ac_dis text-center" />');
    $td_crt.html('&nbsp;');
    $td_crt.appendTo($new_tr);
    
    var $td_title = $('<td/>');
    var $country_hidden = $('<input type="hidden" name="locations[' + formname + '][' + location_id + '][country]" />').val(data.country_name);
    $country_hidden.appendTo($td_title);
    var $city_hidden = $('<input type="hidden" name="locations[' + formname + '][' + location_id + '][city]" />').val(data.city_name);
    $city_hidden.appendTo($td_title);
    var $location_hidden = $('<input type="hidden" name="locations[' + formname + '][' + location_id + '][location]" />').val(data.location_name);
    $location_hidden.appendTo($td_title);
    var $details = $('<div class="text-muted"><a href="javascript:void(0);" data-toggle="collapse" data-target="#details' + formname +location_id+ '"><span>' + data.country_name + '</span>/<span>' + data.city_name + '</span>/<span>' + data.location_name + '</span> <i class="fa fa-pencil"></i></a></div>');
    var $details_details = $('<div id="details' + formname +location_id+ '" class="collapse" />');
    var $title_input = $('<input title="Titlu personalizat tara" class="form-control" type="text" name="locations[' + formname + '][' + location_id + '][country_text]" />').attr('placeholder', data.country_name).val(data.country_text ? data.country_text : '');
    $title_input.appendTo($details_details);
    var $title_input = $('<input title="Titlu personalizat oras" class="form-control" type="text" name="locations[' + formname + '][' + location_id + '][city_text]" />').attr('placeholder', data.city_name).val(data.city_text ? data.city_text : '');
    $title_input.appendTo($details_details);
    if(data.location_id > 0){
      var $title_input = $('<input title="Titlu personalizat aeroport" class="form-control" type="text" name="locations[' + formname + '][' + location_id + '][location_text]" />').attr('placeholder', data.location_name).val(data.location_text ? data.location_text : '');
      $title_input.appendTo($details_details);
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
    if(['departure','arrival'].indexOf(form_name) > -1){
      var $table_tbody = $('#' + form_name + '_locations table > tbody');
      for(var element in form_data){
        var element_data = form_data[element];
        var ids_arr = element.split('-');
        var data = {
          country_id: ids_arr[0],
          city_id: ids_arr[1],
          location_id: ids_arr[2],
          location_code: ids_arr[3] ? ids_arr[3] : '',
          country_name: element_data.country,
          city_name: element_data.city,
          location_name: element_data.location,
          country_text: element_data.country_text ? element_data.country_text : '',
          city_text: element_data.city_text ? element_data.city_text : '',
          location_text: element_data.location_text ? element_data.location_text : ''
        };
        data.full_location_name = (data.location_id>0 ? data.location_name + ', ' : '') + data.city_name;
        console.log(data);
        addElement(data,$table_tbody);
      }
    }
  }
  $offers_popular_form.on('click', 'button.addlocation', function(){
    form_change = true;
    var $button = $(this);
    $button.prop('disabled', true);
    var $input = $('input.location', $(this).closest('.input-group'));
    $input.val(null);
    var $table_tbody = $('table>tbody', $(this).closest('.form-group'));
    addElement($button.data(),$table_tbody);
    $.removeData($button);
  }).on('click', 'button.deletelocation', function(){
    form_change = true;
    $(this).closest('tr').remove();
  });
  
  function applyZoneEffects($container){
    $('[name^="data[company_code]"]', $container).select2_4({
      placeholder:'Alegeti compania',
      theme:'bootstrap',
      width: '100%',
      data: airlines,
      templateSelection: function(item){
        var image = item.image;
        if(!image || image === ''){
          image = 'placeholder_companie.png';
        } else {
          image = image;
        }
        return '<img src="<?php echo $this->theme_url; ?>assets/images/' + image + '" style="max-width:50px;max-height:24px;"/> ' + item.text;
      },
      templateResult: function(item){
        var image = item.image;
        if(!image || image === ''){
          image = 'placeholder_companie.png';
        } else {
          image = image;
        }
        return '<img src="<?php echo $this->theme_url; ?>assets/images/' + image + '" style="max-width:50px;max-height:24px;"/> ' + item.text;
      },
      escapeMarkup: function(markup) {
        return markup;
      }
    }).on('change', function(){
      var selected_items = $(this).select2_4('data');
      if(selected_items && selected_items.length){
        var item = selected_items[0];
        var image = item.image;
        if(!image || image === ''){
          image = 'placeholder_companie.png';
        } else {
          image = image;
        }
        var title = item.text;
        var $parent = $(this).closest('.card-block');
        $('[name^="data[image]"]', $parent).val(image);
        $('[name^="data[title]"]', $parent).val(title);
      }
    });
  }
  $('.offers-sortable').sortable({
    revert: true,
    items: ">.offers-sortable-item",
    handle: ".move-offer",
    start: function(e, ui){
      ui.placeholder.width(ui.item.width());
      ui.placeholder.height(ui.item.height());
    }
  });
  applyZoneEffects($offers_popular_form);
  $offers_popular_form.on('click', '#popular_add_zone', function(e){
    e.preventDefault();
    console.log('click', this);
    var $new_tr = $('#offers_popular_form_models > div').clone();
    $('>.card', $new_tr).addClass('active-zone');
    $('>.card>.card-header>h2>strong', $new_tr).text(zone_index+1);
    $('[id*="_0_"]', $new_tr).each(function(){
      $(this).attr('id',$(this).attr('id').replace('_0_','_' + (zone_index + 1) + '_'));
    });
    $('[for*="_0_"]', $new_tr).each(function(){
      $(this).attr('for',$(this).attr('for').replace('_0_','_' + (zone_index + 1) + '_'));
    });
    $('[name$="[-1]"]', $new_tr).each(function(){
      $(this).attr('name',$(this).attr('name').replace('[-1]','[' + zone_index + ']'));
    });
    zone_index++;
    $new_tr.insertBefore($(this).parent());
    applyZoneEffects($new_tr);
    return false;
  }).on('click', '.popular-remove-zone', function(e){
    e.preventDefault();
    console.log('click', this);
    $(this).closest('.card').parent().remove();
    return false;
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>