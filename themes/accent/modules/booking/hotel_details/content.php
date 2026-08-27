<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$hotel_details = $this->hotel_details;
$title = $hotel_details->Type . ' ' . $hotel_details->Name . ', ' . $hotel_details->CityName . ', ' . $hotel_details->CountryName;
$rooms_for_package = $this->rooms_for_package;
$cancellation_policies = $rooms_for_package->CancellationPolicy->Policy;
$auto_ticketable = false;
if(isset($this->flight_details)){
  $flight_details = $this->flight_details;

  $auto_ticketable = false;
  if(isset($flight_details->FareDetails,$flight_details->FareDetails->IsAutoTicketable) && filter_var($flight_details->FareDetails->IsAutoTicketable,FILTER_VALIDATE_BOOLEAN)){
    $auto_ticketable = true;
  }
}
/* if(!$auto_ticketable){
  $supplier = '';
  if($supplier == ''){
    $auto_ticketable = true;
  }
} */

$room_objects = $this->room_objects;
$room_codes = $this->room_codes;
$total_rooms = count($room_codes);

$block_payments = false;
$because_auto_ticketable = false;
if($auto_ticketable){
  $block_payments = true;
  $because_auto_ticketable = true;
}
$block_online = false;
$because_on_request = false;
foreach($room_objects as $room_object){
  if($room_object->Status == 'RQ'){
    $block_online = true;
    $because_on_request = true;
    break;
  }
}
$today = new DateTime();
$because_weekend = false;
// sambata && duminica
if($today->format('N') >= 6){
  $block_payments = true;
  $because_weekend = true;
}
// ore nelucratoare
$because_of_cancellation_policy = false;
$because_no_working_hours = false;
// if((int)$today->format('H') < 6 || (int)$today->format('H') >= 18){
  // $block_payments = true;
  // $because_no_working_hours = true;
// }

$start_date = $rooms_for_package->AccommodationPeriod->StartDate;
$date_start_date = DateTime::createFromFormat('Y-m-d', $start_date);
$days_till_start = $today->diff($date_start_date);
$days_till_start_formatted = intval($days_till_start->format('%a'));
$because_too_early = false;
// checkin azi sau maine
if($days_till_start_formatted < 2){
  $block_payments = true;
  $because_too_early = true;
}
$start_date_formatted = $date_start_date->format('d.m.Y');
$end_date = $rooms_for_package->AccommodationPeriod->EndDate;
$this->reference_date = $end_date;
$date_end_date = DateTime::createFromFormat('Y-m-d', $end_date);
$end_date_formatted = $date_end_date->format('d.m.Y');
$nights = $date_end_date->diff($date_start_date);
$nights_formatted = $nights->format('%a');
$total_adults = 0;
$total_children = 0;
foreach($room_objects as $room){
  $adults = $room->Occupancy->Adults;
  $total_adults += $adults;
  $children = $room->Occupancy->Children;
  $total_children += $children;
}
$total_people = $total_adults + $total_children;
$this->total_adults = $total_adults;
$this->total_children = $total_children;
$this->total_people = $total_people;
$service_icons = array(
  'Name' => 'fa fa-hotel',
  'Board' => 'fa fa-cutlery',
  'Info' => 'fa fa-info',
);
$common_services = array(
  'Name' => '',
  'Board' => '',
  'Info' => '',
);
$has_uncommon_services = false;
foreach($room_codes as $k=>$room_code){
  $room = $room_objects[$room_code];
  foreach($common_services as $service_key => $service_name){
    if(empty($common_services[$service_key])){
      $common_services[$service_key] = trim($room->$service_key);
    } elseif($common_services[$service_key] !== trim($room->$service_key)){
      $has_uncommon_services = true;
      unset($common_services[$service_key]);
    }
  }
}
$hotel = $this->_controller == 'Hotel';
$mt = $hotel ? '4' : '1';
?>
<div class="row">
  <div class="col-12 col-sm-6 mt-<?php echo $mt; ?>">
    <h3 class=" subTitleFilter pl-0">Detalii Rezervare<?php echo $hotel ? '' : ' Hotel'; ?></h3>
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
      $because_of_cancellation_policy = false;
      foreach($cancellation_policies as $cancellation_policy){
        if(!isset($cancellation_policy->Charge, $cancellation_policy->Charge->Amount)){
          continue;
        }
        $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:sP", $cancellation_policy->Limit);
        if($min_cancellation_date_for_block > $cancellation_date){
          $block_payments = true;
          $because_of_cancellation_policy = true;
        }
        $cancellation_price = floatval($cancellation_policy->Charge->Amount);
        $currency_symbol = $this->_ci->currency_symbol;
        if($cancellation_policy->Charge->Currency === 'RON'){
          $currency_symbol = 'Lei';
        }
        $cancellation_price_formatted = number_format($cancellation_price,2,',','.');
      ?>
      <li><i class="fa fa-ban text-danger"></i> Anularea dupa data <?php echo $cancellation_date->format('d.m.Y h:i:s A'); ?> presupune o penalizare de <?php echo $cancellation_price_formatted; ?> <?php echo $currency_symbol; ?></li>
      <?php } 
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
        } elseif($because_auto_ticketable){ ?>
        Pentru aceasta rezervare se poate plati doar online.<?php
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
  <div class="col-12 col-sm-6 mt-<?php echo $mt; ?>">           
    <h3 class=" subTitleFilter pl-0">Servicii Incluse<?php echo $hotel ? '' : ' Hotel'; ?></h3>
    <ul class="list-unstyled">
    <?php if($common_services) { ?>
      <li>
        <?php /* if($has_uncommon_services) { ?>
        <h5>Comune</h5>
        <?php } */ ?>
        <ul class="list-unstyled">
        <?php foreach($common_services as $service_key => $service_name){
          if(!strlen($service_name)){
            continue;
          }
          $icon = $service_icons[$service_key];
        ?>
          <li><i class="<?php echo $icon; ?> greenCTA"></i> <?php echo $service_name; ?></li>
        <?php } ?>
        </ul>
        <br />
      </li>
    <?php } ?>
    <?php foreach($room_codes as $k=>$room_code){
      $room = $room_objects[$room_code];
      $own_services = array();
      foreach($service_icons as $service_key => $service_icon){
        if(!isset($common_services[$service_key]) && strlen(trim($room->$service_key))){
          $own_services[] = $service_key;
        }
      }
      if($own_services){ ?>
        <li>
        <?php if($total_rooms > 1) { ?>
          <h5>Camera <?php echo $k+1; ?></h5>
        <?php } ?>
          <ul class="list-unstyled">
            <?php foreach($own_services as $service_key){
              $service_name = $room->$service_key;
              if(!strlen($service_name)){
                continue;
              }
              $icon = $service_icons[$service_key];
            ?>
              <li><i class="<?php echo $icon; ?> greenCTA"></i> <?php echo $service_name; ?></li>
            <?php } ?>
          </ul>
          <br />
        <?php if($total_rooms > 1) { ?>
        <?php } ?>
        </li>   
      <?php } ?>
    <?php } ?>
    </ul>   
  </div>
</div>
<?php /*
<script>
var _rooms_for_package = <?php echo json_encode($rooms_for_package); ?>;
</script>
<?php themeFunctions::debugFileLine('end'); ?>
*/ ?>