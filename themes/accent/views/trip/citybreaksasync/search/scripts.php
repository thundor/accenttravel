<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Trip/Citybreaks_model');
$data = $this->_ci->Citybreaks_model->getSearchData();
// echo '<pre>';
// print_r($data);
// die;
?>
<script>
var hotel_search_data = <?php echo json_encode($data); ?>;
function interpretNoHotelsResponse(response){
  $('#searchSearching').hide();
  $('#searchNoResults').show();
  setTimeout(function(){
    history.back();
  },2000);
}
function initiateSearch(){
  $.ajax({
    url: '<?php echo site_url('trip/citybreaksasync/setSearchAndInitiate'); ?>',
    method: 'post',
    dataType: 'json',
    data: hotel_search_data,
    async: true,
    success: function(result,status,xhr){
      console.log(result);
      if(!result.status || result.status !== 'success' || result.response.total_items < 1){
        interpretNoHotelsResponse(result);
        return;
      }
      window.location.href="<?php echo site_url('trip/citybreaksasync'); ?>";
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      alert('Eroare in cautare');
      interpretNoHotelsResponse();
    }
  });
}
$(document).ready(function(){
  initiateSearch();
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>