<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$offers_settings = $this->_ci->Options_model->get('offers_popular_settings');
if(!$offers_settings){
  $offers_settings = array();
}
$items = isset($offers_settings['status']) ? $offers_settings['status'] : array();
$items = array_intersect($items, array(1));
$this->_ci->load->model('Trip/Offer_popular_model');
?>
<div id="offers_popular">
<h3 class="titleBilete"><img src="<?php echo $this->theme_url; ?>assets/images/plane.png" alt="bilete de avion" width="35" /> Bilete de avion catre destinatii populare </h3>
<ul class="nav nav-tabs flex-sm-row" id="companiiAir" role="tablist">
  <?php 
  $ik = -1;
  foreach($items as $item_key => $item_value){
	$ik++;
    $title = isset($offers_settings['title'], $offers_settings['title'][$item_key]) ? '' . $offers_settings['title'][$item_key] : '';
    $image = isset($offers_settings['image'], $offers_settings['image'][$item_key]) ? '' . $offers_settings['image'][$item_key] : '';
    $code = isset($offers_settings['company_code'], $offers_settings['company_code'][$item_key]) ? '' . $offers_settings['company_code'][$item_key] : '';
    if(!strlen($image)){
      $image = 'placeholder_companie.png';
    }
  ?>
  <li class="nav-item">
    <a class="nav-link<?php echo $ik ? '' : ' active'; ?>" data-toggle="tab" href="#bilete_avion_<?php echo $code; ?>" role="tab" aria-controls="bilete_avion_<?php echo $code; ?>">
      <?php echo htmlspecialchars($title); ?>
      <br />
      <img src="<?php echo $this->theme_url; ?>assets/images/<?php echo htmlspecialchars($image); ?>" alt="Bilete de avion <?php echo htmlspecialchars($title); ?>" />
    </a>
  </li>
  <?php } ?>
</ul>
<div class="tab-content">
  <?php 
  $ik = -1;
  foreach($items as $item_key => $item_value){
	$ik++;
    $title = isset($offers_settings['title'], $offers_settings['title'][$item_key]) ? '' . $offers_settings['title'][$item_key] : '';
    $image = isset($offers_settings['image'], $offers_settings['image'][$item_key]) ? '' . $offers_settings['image'][$item_key] : '';
    $code = isset($offers_settings['company_code'], $offers_settings['company_code'][$item_key]) ? '' . $offers_settings['company_code'][$item_key] : '';
    if(!strlen($image)){
      $image = 'placeholder_companie.png';
    }
    
    $this->_ci->db->where(array(
      'status' => 1,
      'code' => $code,
    ));
    $this->_ci->db->order_by('price ASC');
    $this->_ci->db->group_by('flight_code');
    $offers = $this->_ci->Offer_popular_model->getOffers(array('limit'=>4));
  ?>
  <div class="tab-pane<?php echo $ik ? '' : ' active'; ?>" id="bilete_avion_<?php echo $code; ?>" role="tabpanel">
    <div class="flight-offers-home">
    <?php foreach($offers as $offer){
      $offer_data = unserialize($offer->data);
      $routes_count = count($offer_data->flight->Routes);
      
      $departure_city = trim($offer_data->departure['data']['city_text']);
      $departure_segment = $offer_data->flight->Routes[0]->Segment[0];
      if(!strlen($departure_city)){
        $departure_city = $departure_segment->Origin->Airport->City;
      }
      $departure_date = new DateTime($departure_segment->Origin->Date . ' ' . $departure_segment->Origin->Time);
      $departure_airport = '';
      if($offer_data->departure['location_id']){
        $departure_airport = trim($offer_data->departure['data']['location_text']);
      }
      if(!strlen($departure_airport)){
        $departure_airport = $departure_segment->Origin->Airport->_;
      }
      $total_arrival_segments = count($offer_data->flight->Routes[0]->Segment);
      $arrival_segment = $offer_data->flight->Routes[0]->Segment[$total_arrival_segments-1];
      $arrival_city = trim($offer_data->arrival['data']['city_text']);
      if(!strlen($arrival_city)){
        $arrival_city = $arrival_segment->Destination->Airport->City;
      }
      $arrival_date = new DateTime($arrival_segment->Destination->Date . ' ' . $arrival_segment->Destination->Time);
      $arrival_airport = '';
      if($offer_data->arrival['location_id']){
        $arrival_airport = trim($offer_data->arrival['data']['location_text']);
      }
      if(!strlen($arrival_airport)){
        $arrival_airport = $arrival_segment->Destination->Airport->_;
      }
      $link = site_url('trip/flight/booking?code=' . $offer->flight_code . '&itinerary_code=' . $offer->itinerary_code);
    ?>
    <div class="bileteHome" data-code="<?php echo htmlspecialchars($offer->flight_code); ?>" data-itinerary_code="<?php echo htmlspecialchars($offer->flight_code); ?>">
      <div class="row">
        <div class="col-8 col-sm-9">
          <h5>
            <a href="<?php echo $link; ?>" class="offers-popular-book-flight">
              <span>
                <?php echo $departure_city; ?> <i class="fa fa-angle-right"></i> <?php echo $arrival_city; ?>
              </span> 
              <i class="fa fa-calendar"></i> <?php echo ucfirst(lang(strtolower($departure_date->format('l')))); ?> <?php echo $departure_date->format('d.m.Y'); ?>
            </a>
            <br />
            <span>
              <?php echo $departure_airport; ?> 
              <i class="fa fa-clock-o"></i> <?php echo $departure_date->format('H:i'); ?>
              <i class="fa fa-angle-right"></i> <?php echo $arrival_airport; ?> <i class="fa fa-clock-o"></i> 
              <?php echo $arrival_date->format('H:i'); ?>
            </span>
          </h5>
          <?php if($routes_count > 1){ ?>
          <?php 
          $departure_city = trim($offer_data->arrival['data']['city_text']);
          $departure_segment = $offer_data->flight->Routes[1]->Segment[0];
          if(!strlen($departure_city)){
            $departure_city = $departure_segment->Origin->Airport->City;
          }
          $departure_date = new DateTime($departure_segment->Origin->Date . ' ' . $departure_segment->Origin->Time);
          $departure_airport = '';
          if($offer_data->arrival['location_id']){
            $departure_airport = trim($offer_data->arrival['data']['location_text']);
          }
          if(!strlen($departure_airport)){
            $departure_airport = $departure_segment->Origin->Airport->_;
          }
          $total_arrival_segments = count($offer_data->flight->Routes[1]->Segment);
          $arrival_segment = $offer_data->flight->Routes[1]->Segment[$total_arrival_segments-1];
          $arrival_city = trim($offer_data->departure['data']['city_text']);
          if(!strlen($arrival_city)){
            $arrival_city = $arrival_segment->Destination->Airport->City;
          }
          $arrival_date = new DateTime($arrival_segment->Destination->Date . ' ' . $arrival_segment->Destination->Time);
          $arrival_airport = '';
          if($offer_data->departure['location_id']){
            $arrival_airport = trim($offer_data->departure['data']['location_text']);
          }
          if(!strlen($arrival_airport)){
            $arrival_airport = $arrival_segment->Destination->Airport->_;
          }
          ?>
          <h5>
            <a href="<?php echo $link; ?>" class="offers-popular-book-flight">
              <span>
                <?php echo $departure_city; ?> <i class="fa fa-angle-right"></i> <?php echo $arrival_city; ?>
              </span> 
              <i class="fa fa-calendar"></i> <?php echo ucfirst(lang(strtolower($departure_date->format('l')))); ?> <?php echo $departure_date->format('d.m.Y'); ?>
            </a>
            <br />
            <span>
              <?php echo $departure_airport; ?> 
              <i class="fa fa-clock-o"></i> <?php echo $departure_date->format('H:i'); ?>
              <i class="fa fa-angle-right"></i> <?php echo $arrival_airport; ?> <i class="fa fa-clock-o"></i> 
              <?php echo $arrival_date->format('H:i'); ?>
            </span>
          </h5>
          <?php } ?>
        </div>
        <div class="col-4 col-sm-3"><p>de la <span><?php echo ceil($offer_data->flight->Price); ?></span> <?php echo $this->_ci->currency_symbol; ?></p></div>
      </div>
    </div>
    <?php } ?>
    </div>
    <a href="<?php echo site_url('trip/flights/offers/' . $offer->code); ?>" class="btn btn-block btn-secondary">Vezi bilete de avion <?php echo htmlspecialchars($title); ?></a>
  </div>
  <?php } ?>
</div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>