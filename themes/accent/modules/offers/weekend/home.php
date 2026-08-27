<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$offers_settings = $this->_ci->Options_model->get('offers_weekend_settings');
if(!$offers_settings){
  $offers_settings = array();
}
$offer_order = array();
foreach ($offers_settings as $key => $value){
  $offer_order[$key] = $value['order'];
}
array_multisort($offer_order, SORT_ASC, $offers_settings);
?>
<h3 class="ofWeek">Oferte de weekend </h3>
<ul class="nav nav-tabs flex-sm-row" id="oferteWeekend" role="tablist">
  <?php 
  $first_found = false;
  foreach($offers_settings as $item_key => $item_value){ 
    $enabled = isset($item_value['enabled']) && $item_value['enabled'];
    if(!$enabled){
      continue;
    }
    $text = isset($item_value['text']) && strlen(trim($item_value['text'])) ? trim($item_value['text']) : trim($item_value['name']);
  ?>
  <li class="nav-item"><a class="nav-link<?php echo $first_found ? '' : ' active'; ?>" data-toggle="tab" href="#weekend_offers_<?php echo $item_key; ?>" role="tab" aria-controls="weekend_offers_<?php echo $item_key; ?>"><?php echo $text; ?></a></li>
  <?php 
    $first_found = true;
  } ?>
</ul>
<div class="tab-content">
  <?php 
  $first_found = false;
  $placeholder_image = $this->theme_url . 'assets/images/placeholder.png';
  
  $time = time();
  $yesterday = strtotime('-1 month', $time);
  $n_friday = strtotime('next friday', $time);
  $nn_friday = strtotime('next friday', $n_friday);
  $nnn_friday = strtotime('next friday', $nn_friday);
  $departures = array(
    date('d.m.Y', $n_friday),
    date('d.m.Y', $nn_friday),
    date('d.m.Y', $nnn_friday),
  );
  $departures_str = implode(' | ', $departures);
  $this->_ci->load->model('Trip/Offer_weekend_model');
  foreach($offers_settings as $item_key => $item_value){ 
    $enabled = isset($item_value['enabled']) && $item_value['enabled'];
    if(!$enabled){
      continue;
    }
    $text = isset($item_value['text']) && strlen(trim($item_value['text'])) ? trim($item_value['text']) : trim($item_value['name']);
    
    // $this->_ci->db->where('`time_modified` >= "' . date('Y-m-d H:i:s', $yesterday) . '"');
    $item_key = preg_replace("/[^A-Z_]/", '', $item_key);
    if(strpos($item_key,'_') === false){
      $this->_ci->db->where('`zone` LIKE "' . $item_key . '\_%"');
    } else {
      $this->_ci->db->where('`zone` = "' . $item_key . '"');
    }
    $this->_ci->db->order_by("SUBSTRING_INDEX(`time_modified`, ' ', 1) DESC");
    $this->_ci->db->order_by('price ASC');
    $this->_ci->db->where('LENGTH(data)>0');
    $offers = $this->_ci->Offer_weekend_model->getOffers(array('limit'=>3));
    
  ?>
  <div class="tab-pane<?php echo $first_found ? '' : ' active'; ?>" id="weekend_offers_<?php echo $item_key; ?>" role="tabpanel">
    <div class="recommended-offers-home">
    <?php foreach($offers as $offer){
      $offer_data = unserialize($offer->data);
      $image = $placeholder_image;
      if(!empty($offer_data->Image)){
        $image = $offer_data->Image;
      }
    ?>
      <?php if($offer->type == 'hotel'){ ?>
      <div class="hoteluriWeek">
        <div class="row">
          <div class="col-sm-4 col-lg-3">
            <a href="<?php echo site_url('trip/hotel/' . $offer->type_id . '?type=offer'); ?>" class="hotel-image lazy" data-src="<?php echo $image; ?>" style="background-image:url(<?php echo $image; ?>);padding-bottom:100%;background-position: center;background-size: cover;"></a>
          </div>
          <div class="col-sm-8 col-lg-9">
            <?php if(!empty($offer_data->MinPrice)) { ?>
            <p class="float-right pretWeek">de la <span><?php echo ceil($offer_data->MinPrice); ?> <?php echo $this->_ci->currency_symbol; ?></span></p>
            <?php } ?>
            <h4><?php echo htmlspecialchars(html_entity_decode($offer_data->Name,ENT_QUOTES)); ?></h4>
            <p class="mb-0"><?php echo htmlspecialchars($offer->city_name /* . (strlen($offer_data->Address) ? ', ' . html_entity_decode($offer_data->Address,ENT_QUOTES) : '') */); ?> | <?php echo str_repeat('<i class="fa fa-star"></i>', $offer->stars); ?>
              <br />
              <i class="fa fa-user-o"></i><i class="fa fa-user-o"></i> pret pentru doua persoane / 2 nopti / camera </p>
            <p><strong>Plecari</strong><br />Vineri: <?php echo $departures_str; ?> </p>
            <a href="<?php echo site_url('trip/hotel/' . $offer->type_id . '?type=offer'); ?>" class="float-right btn btn btn-primary">REZERVA</a>
          </div>
        </div>
      </div>
      <?php } elseif($offer->type == 'package') { ?>
      <div class="hoteluriWeek">
        <div class="row">
          <div class="col-sm-4 col-lg-3">
            <a href="<?php echo site_url('trip/package/' . $offer->type_id . '?type=offer'); ?>" class="hotel-image lazy" data-src="<?php echo $image; ?>" style="background-image:url(<?php echo $image; ?>);padding-bottom:100%;background-position: center;background-size: cover;"></a>
          </div>
          <div class="col-sm-8 col-lg-9">
            <?php if(!empty($offer_data->MinPrice)) { ?>
            <p class="float-right pretWeek">de la <span><?php echo ceil($offer_data->MinPrice); ?> <?php echo $this->_ci->currency_symbol; ?></span></p>
            <?php } ?>
            <h4><?php echo htmlspecialchars(html_entity_decode($offer_data->Name,ENT_QUOTES)); ?></h4>
            <p class="mb-0"><?php echo htmlspecialchars(html_entity_decode($offer_data->Category,ENT_QUOTES)); ?> | <?php echo str_repeat('<i class="fa fa-star"></i>', $offer->stars); ?>
              <br />
              <i class="fa fa-user-o"></i><i class="fa fa-user-o"></i> pret pentru doua persoane / <?php echo !empty($offer_data->Nights) ? $offer_data->Nights : 7; ?> nopti </p>
            <p><strong>Plecari</strong><br />Vineri: <?php echo $departures_str; ?> </p>
            <a href="<?php echo site_url('trip/package/' . $offer->type_id . '?type=offer'); ?>" class="float-right btn btn btn-primary">REZERVA</a>
          </div>
        </div>
      </div>
      <?php } ?>
    <?php } ?>
    </div>
    <a href="<?php echo site_url('trip/offers/weekend/' . $item_key);?>" class="btn btn-block btn-secondary">Vezi toate ofertele din <?php echo $text; ?> </a>
  </div>
  <?php 
    $first_found = true;
  } ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>