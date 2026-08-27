<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="flightsInfo">
  <a class="float-right mb-2 mt-2 mapInfo" href="#"><i class="fa fa-info-circle"></i> INFO <span class="destinationCity">BARCELONA</span></a>
  <h1 class="filterTitle">Am gasit 46 de oferte de zbor spre Barcelona</h1>
  <div class="row">
    <div class="col-12 col-sm-4 mr-0 pr-0">
      <p><img src="<?php echo $this->theme_url; ?>assets/images/plane.png" class="float-left mr-1">PLECARE: <span id="flightsInfoDepartureDate">Vineri, 29 Septembrie 2018</span></p>
    </div>
    <div class="col-12 col-sm-4 ml-0 pl-0 mr-0 pr-0" ><p> <span id="flightsInfoReturn">RETUR:</span> <span id="flightsInfoReturnDate">Miercuri, 04 Octombrie 2018</span></p></div>
    <div class="col-12 col-sm-4 ml-0 pl-0"><p><i class="fa fa-users"></i> <span id="flightsInfoPassengers">2 calatori</span><span class="location-details">, <span id="flightsInfoOriginLocation">Bucuresti</span> <i class="fa fa-exchange"></i> <span id="flightsInfoDestinationLocation">Barcelona</span></span></p></div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>