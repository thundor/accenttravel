<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Trip/Flights_model');
$data = $this->_ci->Flights_model->getSearchData();
?>
<script>
var flight_search_data = <?php echo json_encode($data); ?>;
function interpretNoFlightsResponse(response){
  $('#searchSearching').hide();
  $('#searchNoResults').show();
  setTimeout(function(){
    history.back();
  },2000);
}
function initiateSearch(){
  $.ajax({
    url: '<?php echo site_url('trip/flights/setSearchAndInitiate'); ?>',
    method: 'post',
    dataType: 'json',
    data: flight_search_data,
    async: true,
    success: function(result,status,xhr){
      if(!result.status || result.status !== 'success' || result.response.total_items < 1){
        interpretNoFlightsResponse(result);
        return;
      }
      window.location.href="<?php echo site_url('trip/flights'); ?>";
    },
    error: function(jqXHR,textStatus,error){
      console.log(jqXHR, textStatus, error);
      // setSearchStatus(true);
      alert('Eroare in cautare');
      interpretNoFlightsResponse(result);
    }
  });
}
$(document).ready(function(){
  initiateSearch();
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>