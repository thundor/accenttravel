<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$flight_details = $this->flight_details;

$auto_ticketable = false;
if(isset($flight_details->FareDetails,$flight_details->FareDetails->IsAutoTicketable) && filter_var($flight_details->FareDetails->IsAutoTicketable,FILTER_VALIDATE_BOOLEAN)){
  $auto_ticketable = true;
}
/* if(!$auto_ticketable){
  $supplier = '';
  if($supplier == ''){
    $auto_ticketable = true;
  }
} */


$routes_count = count($flight_details->Routes);
$last_route = $flight_details->Routes[$routes_count-1];
$last_route_segments_count = count($last_route->Segment);
$last_route_segment = $last_route->Segment[$last_route_segments_count-1];
$start_date = $flight_details->Routes[0]->Segment[0]->Origin->Date;
$reference_date = $start_date;
$this->reference_date = $reference_date;

$block_payments = false;
$because_of_cancellation_policy = false;
$because_auto_ticketable = false;
if($auto_ticketable){
  $block_payments = true;
  $because_auto_ticketable = true;
}
$today = new DateTime();
$because_weekend = false;
// sambata && duminica
if($today->format('N') >= 6){
  $block_payments = true;
  $because_weekend = true;
}
// ore nelucratoare
$because_no_working_hours = false;
// if((int)$today->format('H') < 6 || (int)$today->format('H') >= 18){
  // $block_payments = true;
  // $because_no_working_hours = true;
// }
$date_start_date = DateTime::createFromFormat('Y-m-d', $start_date);
$days_till_start = $today->diff($date_start_date);
$days_till_start_formatted = intval($days_till_start->format('%a'));
$because_too_early = false;
// checkin azi sau maine
if($days_till_start_formatted < 2){
  $block_payments = true;
  $because_too_early = true;
}
if($block_payments){ ?>
<div class="col-12 p-0">
  <hr />
  <ul class="list-unstyled mt-3">
    <li><i class="fa fa-ban text-danger"></i> Metodele de plata <b>direct la agentie</b> si prin <b>transfer bancar</b> au fost dezactivate din motivul urmator: <?php
    if($because_of_cancellation_policy){ ?>
    Data minima de anulare este inaintea datei <b><?php echo $min_cancellation_date_for_block->format('d.m.Y h:i:s A'); ?></b><?php
    } elseif($because_too_early){ ?>
    Pentru rezervari cu data de plecare astazi sau maine se poate plati doar online.<?php
    } elseif($because_weekend){ ?>
    Pentru rezervari efectuate in weekend se poate plati doar online.<?php
    } elseif($because_no_working_hours){ ?>
    Pentru rezervari efectuate in intervalul orar (18:00 - 06:00) se poate plati doar online.<?php
    } elseif($because_auto_ticketable){ ?>
    Pentru aceasta rezervare se poate plati doar online.<?php
    } ?>
    </li><?php
    themeFunctions::blockModule('booking/payment_methods/agency', true);
    themeFunctions::blockModule('booking/payment_methods/bank', true); ?>
  </ul>
  <hr />
</div><?php
} ?>
<?php themeFunctions::debugFileLine('end'); ?>