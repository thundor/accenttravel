<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Trip/Packages_model');
$data = $this->_ci->Packages_model->getSearchData();
?>
<script>
var package_search_data = <?php echo json_encode($data); ?>;
function interpretNoPackagesResponse(response){
  $('#searchSearching').hide();
  $('#searchNoResults').show();
  setTimeout(function(){
    history.back();
  },2000);
}
function initiateSearch(){
  $.ajax({
    url: '<?php echo site_url('trip/packages/setSearchAndInitiate'); ?>',
    method: 'post',
    dataType: 'json',
    data: package_search_data,
    async: true,
    success: function(result,status,xhr){
      // console.log(result);
      // return false;
      if(!result.status || result.status !== 'success'){
        interpretNoPackagesResponse(result);
        return;
      }
      window.location.href="<?php echo site_url('trip/packages'); ?>";
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      interpretNoPackagesResponse();
      alert('Eroare in cautare');
      setSearchStatus(true);
    }
  });
}
$(document).ready(function(){
  initiateSearch();
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>