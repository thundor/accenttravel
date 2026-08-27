<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$product = $this->view_data['product_info'];
$title = ucfirst($product->ProductType) . ' ' . $product->ProductName . ', ' . $product->CityName . ', ' . $product->CountryName;
$total_rooms = $this->view_data['total_rooms'];
$total_adults = $this->view_data['total_adults'];
$total_children = $this->view_data['total_children'];
$this->total_people = $total_adults + $total_children;
$offer = $this->view_data['offer'];
$checkin_date = DateTime::createFromFormat('Y-m-d', $offer->CheckIn);
$checkout_date = DateTime::createFromFormat('Y-m-d', $offer->CheckOut);
$start_date_formatted = $checkin_date->format('d.m.Y');
$end_date_formatted = $checkout_date->format('d.m.Y');
$this->reference_date = $this->view_data['checkout'];
$nights_formatted = $this->view_data['nights'];
$cancellation_policies = $this->view_data['cancellation_policies'];
$today = new DateTime();
$block_payments = false;
$because_weekend = false;

// sambata && duminica
if($today->format('N') >= 6){
  $block_payments = true;
  $because_weekend = true;
}
// ore nelucratoare
$because_no_working_hours = false;
$because_of_cancellation_policy = false;
// if((int)$today->format('H') < 6 || (int)$today->format('H') >= 18){
  // $block_payments = true;
  // $because_no_working_hours = true;
// }
$days_till_start = $today->diff($checkin_date);
$days_till_start_formatted = intval($days_till_start->format('%a'));
$because_too_early = false;
// checkin azi sau maine
if($days_till_start_formatted < 2){
  $block_payments = true;
  $because_too_early = true;
}
$block_online = false;
if($offer->Availability === 'OR'){
  $block_online = true;
  $because_on_request = true;
}
$mt = '4';
$service_icons = array(
  '2' => 'fa fa-cutlery',
  '5' => 'fa fa-cab',
  '6' => 'fa fa-cab',
  '7' => 'fa fa-cab',
  '7s' => 'fa fa-euro-sign',
);
?>
<div class="row">
  <div class="col-12 col-sm-6 mt-<?php echo $mt; ?>">
    <h3 class=" subTitleFilter pl-0">Detalii Rezervare <?php echo $product->ProductType; ?></h3>
    <ul class="list-unstyled">
      <li><i class="fa fa-building-o greenCTA"></i> <?php echo $title; ?></li>
      <?php 
      ?>
      <li><i class="fa fa-user-o greenCTA"></i> <?php echo $total_rooms == 1 ? '1 Camera' : ($total_rooms . ' Camere'); ?> x <?php echo $total_adults == 1 ? '1 adult' : ($total_adults . ' adulti'); ?><?php if($total_children) { echo ' + ' . ($total_children == 1 ? '1 copil' : ($total_children . ' copii')); } ?></li>
      <li><i class="fa fa-calendar-o greenCTA"></i> Perioada: <?php echo $start_date_formatted; ?> - <?php echo $end_date_formatted; ?></li>
      <li><i class="fa fa-moon-o greenCTA"></i> Numar Nopti: <?php echo $nights_formatted; ?></li>
      <?php 
  if($cancellation_policies){ 
    $min_cancellation_date_for_block = new DateTime(date('Y-m-d H:i:s',strtotime('+3 days')));
    ?>
    <br />
    <?php
    foreach($cancellation_policies as $room_cancellation_policies){
      foreach($room_cancellation_policies as $cancellation_policy){
        if(!isset($cancellation_policy['price']) || $cancellation_policy['price']<=0){
          continue;
        }
        $cancellation_date = DateTime::createFromFormat("Y-m-d", $cancellation_policy['from_date']);
        // if($min_cancellation_date_for_block > $cancellation_date){
          // $block_payments = true;
          // $because_of_cancellation_policy = true;
        // }
        if($cancellation_policy['percentage']){
          $cancellation_price_formatted = format_price($cancellation_policy['price'], '%');
        } else {
          $cancellation_price_formatted = format_price($cancellation_policy['price'], $this->view_data['currency_code']);
        }
        if($cancellation_policy['type'] == 'cancellation'){ ?>
      <li><i class="fa fa-ban text-danger"></i> Anularea dupa data <?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?> presupune o penalizare de <?php echo $cancellation_price_formatted; ?></li><?php 
        } else { ?>
      <li><i class="fa fa-ban text-danger"></i> Dupa data <?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?> se inpune o taxa aditionala de <?php echo $cancellation_price_formatted; ?></li><?php     
        }
      }
    }
  }
    if($block_payments){ ?>
      <li><i class="fa fa-ban text-danger"></i> Metodele de plata <b>direct la agentie</b> si prin <b>transfer bancar</b> au fost dezactivate din motivul urmator: <?php
      if($because_of_cancellation_policy){ ?>
      Data minima de anulare este inaintea datei <b><?php echo $min_cancellation_date_for_block->format('d.m.Y h:i:s A'); ?></b><?php
      } elseif($because_too_early){ ?>
      Pentru rezervari cu data de checkin astazi sau maine se poate plati doar online.<?php
      } elseif($because_weekend){ ?>
      Pentru rezervari efectuate in weekend se poate plati doar online.<?php
      } elseif($because_no_working_hours){ ?>
      Pentru rezervari efectuate in intervalul orar 18:00 - 06:00 se poate plati doar online.<?php
      } ?>
      </li><?php
      themeFunctions::blockModule('booking/payment_methods/agency', true);
      themeFunctions::blockModule('booking/payment_methods/bank', true);
    }
    if($block_online){ ?>
      <li><i class="fa fa-ban text-danger"></i> Metoda de plata <b>online</b> a fost dezactivata deoarece <?php
      if($because_on_request){ ?>
      camerele au disponibilitate: <b>La cerere</b><?php
      } ?>
      </li><?php
      themeFunctions::blockModule('booking/payment_methods/online', true);
    }

    ?>
    </ul>   
  </div>
  <div class="col-12 col-sm-6 mt-<?php echo $mt; ?>"><?php 
  if($offer->Services || $offer->Meals) { ?>
    <h3 class=" subTitleFilter pl-0">Servicii Incluse <?php echo $product->ProductType; ?></h3>
    <ul class="list-unstyled"><?php 
    if($offer->Services) { 
      foreach($offer->Services as $service){
        if(!in_array($service->Type, array('2', '5', '6', '7', '7s'))){
          continue;
        }
        $service_name = $service->Name;
        $service_type = $service->Type;
        if(!strlen($service_name)){
          continue;
        }
        $icon = isset($service_icons[$service_type]) ? $service_icons[$service_type] : 'fa fa-star'; ?>
        <li><i class="<?php echo $icon; ?> greenCTA"></i> <?php echo $service_name; ?></li><?php 
      }
    }
    if($offer->Meals) {
      foreach($offer->Meals as $service){
        $service_name = $service->Name;
        $service_type = $service->Type;
        if(!strlen($service_name)){
          continue;
        }
        $icon = isset($service_icons[$service_type]) ? $service_icons[$service_type] : 'fa fa-star'; ?>
        <li><i class="<?php echo $icon; ?> greenCTA"></i> <?php echo $service_name; ?></li><?php 
      }
    } ?>
    </ul><?php
  } ?>
  </div>
  <div class="col-12 col-sm-12 mt-<?php echo $mt; ?>"><?php 
  if($this->view_data['extra_services']) { ?>
    <h3 class=" subTitleFilter pl-0">Alegeti din serviciile optionale disponibile</h3>
    <div class="row"><?php 
    foreach($this->view_data['extra_services'] as $service){
      $service_name = $service->Name;
      $service_type = $service->Type;
      if(!strlen($service_name)){
        continue;
      }
      $icon = isset($service_icons[$service_type]) ? $service_icons[$service_type] : 'fa fa-star'; ?>
      <label class="col-sm-12 col-lg-6"><input type="checkbox" form="bookingCheckout" class="offer-extra-service" name="extra_services[]" value="<?php echo $service->Type . '-' . $service->Code . '-' . $service->CharterId; ?>" data-price="<?php echo !empty($service->price) ? $service->price->Gross : ''; ?>" /> <i class="<?php echo $icon; ?> greenCTA"></i> <?php echo $service_name; ?></label><?php 
    } ?>
    </div><?php
  } ?>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>