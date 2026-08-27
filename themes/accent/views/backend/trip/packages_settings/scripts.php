<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;

$this->_ci->load->model('Trip/Packages_model');
$package_categories = array();
$package_categories_result = $this->_ci->Packages_model->loadPackageCategories();

if($package_categories_result){
  foreach($package_categories_result->_embedded->categories as $package_category){
    if(strpos($package_category->Name,'!') !== false){
      continue;
    }
    $package_category->id = $package_category->Id;
    $package_category->text = $package_category->Name;
    $package_categories[] = $package_category;
  }
}
$package_destinations = array();
$package_destinations_result = $this->_ci->Packages_model->loadPackageDestinations();

if($package_destinations_result){
  foreach($package_destinations_result->_embedded->cities as $package_destination){
    $package_destination->id = $package_destination->Id;
    $package_destination->text = $package_destination->Name;
    $package_destinations[] = $package_destination;
  }
}
?>
<script>
var forms_data = <?php echo json_encode($data); ?>;
var package_categories = <?php echo json_encode($package_categories); ?>;
var package_destinations = <?php echo json_encode($package_destinations); ?>;
var $packages_settings_form = $('#packages_settings_form');
$('#packages_categories', $packages_settings_form).select2_4({
  placeholder:'Alegeti categoriile',
  multiple: true,
  theme:'bootstrap',
  width: '100%',
  data: package_categories,
  escapeMarkup: function(markup) {
    return markup;
  }
});
$('#packages_destinations', $packages_settings_form).select2_4({
  placeholder:'Alegeti categoriile',
  multiple: true,
  theme:'bootstrap',
  width: '100%',
  data: package_destinations,
  escapeMarkup: function(markup) {
    return markup;
  }
});

</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>