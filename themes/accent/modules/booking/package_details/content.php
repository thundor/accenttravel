<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$package_details = $this->package_details;
$package_availability = $this->package_availability;
$entry_details = $this->entry_details;
$block_payments = false;

$title = lang($package_details->Type) . ' ' . $package_details->Name . ', ' . $package_details->Category;

$cancellation_policies = $package_availability->CancelationPolicies;
$total_rooms = count($package_availability->Accommodation);
$total_adults = 0;
$total_children = 0;

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

$start_date = $entry_details->StartDate;
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
$end_date = $entry_details->EndDate;
$this->reference_date = $end_date;
$date_end_date = DateTime::createFromFormat('Y-m-d', $end_date);
$end_date_formatted = $date_end_date->format('d.m.Y');

$duration = $entry_details->Duration;
$duration_unit = $entry_details->DurationUnit;
$room_services = array();
$service_icons = array(
  'Name' => 'fa fa-hotel',
  'Info' => 'fa fa-info',
);
$common_services = array(
  'Name' => '',
  'Info' => '',
);
$has_uncommon_services = false;
$room_services_id = array();
$block_online = false;
$because_on_request = false;
foreach($entry_details->Accommodation as $room_index=>$entry_packages){
  foreach($entry_packages as $package_index=>$room_details){
    if($room_details->AvailabilityStatus == 'RQ'){
      $block_online = true;
      $because_on_request = true;
    }
    $room_price = $room_details->Price;
    foreach($room_details->Services as $room_service){
      $room_services_id[$room_service->RoomId] = $room_price;
    }
  }
}
$this->currency_symbol = $package_details->Currency == 'RON' ? 'Lei' : $this->_ci->currency_symbol;
foreach($package_availability->Accommodation as $room_index => $selected_packages){
  foreach($selected_packages as $selected_package){
    $selected_package->Price = $room_services_id[$selected_package->RoomID];
    $room_service = array(
      'Name' => $selected_package->RoomType->Description,
      'Info' => $selected_package->RoomFeature->Description,
    );
    $room_services[] = $room_service;
    foreach($common_services as $service_key => $service_name){
      if(empty($common_services[$service_key])){
        $common_services[$service_key] = trim($room_service[$service_key]);
      } elseif($common_services[$service_key] !== trim($room_service[$service_key])){
        $has_uncommon_services = true;
        unset($common_services[$service_key]);
      }
    }
    foreach($selected_package->RoomOccupancy->GuestCount as $guest){
      if($guest->AgeQualifyingCode == 'a'){
        $total_adults += $guest->Count;
      } else {
        $total_children += $guest->Count;
      }
    }
  }
}
$total_people = $total_adults + $total_children;
$this->total_adults = $total_adults;
$this->total_children = $total_children;
$this->total_people = $total_people;
$this->total_rooms = $total_rooms;

$entry_details_extra = $this->view_data['entry_details_extra'];
$extra_services = array();
$selected_extra_services = $this->view_data['selected_extra_services'];
$extra_service_occupations = array();
$extra_service_rooms = array();
foreach($this->view_data['occupations'] as $room_index => $room_occupation){
  if(!isset($room_occupation['extra-services'])){
    continue;
  }
  foreach($room_occupation['extra-services'] as $entry_id => $extra_occupants){
    if(!isset($extra_service_rooms[$entry_id])){
      $extra_service_rooms[$entry_id] = array();
    }
    $extra_service_rooms[$entry_id][$room_index] = $extra_occupants;
    if(!isset($extra_service_occupations[$entry_id])){
      $extra_service_occupations[$entry_id] = array(
        'a' => 0,
        'c' => array(),
      );
    }
    foreach($extra_occupants as $occupant_type=>$occupant_content){
      if($occupant_type == 'a'){
        $extra_service_occupations[$entry_id][$occupant_type] += $occupant_content;
      } else {
        foreach($occupant_content as $child_age){
          $extra_service_occupations[$entry_id][$occupant_type][] = $child_age;
        }
      }
    }
  }
}
foreach($entry_details_extra as $k=>$extra_service){
  if(!isset($selected_extra_services[$extra_service->Id])){
    continue;
  }
  if(!isset($selected_extra_services[$extra_service->Id]['entries'])){
    continue;
  }
  if(!isset($extra_service_occupations[$extra_service->Id])){
    continue;
  }
  $extra_service = (object)(array)$extra_service;
  foreach($extra_service->Entries as $l=>$extra_service_entry){
    unset($extra_service->Entries[$l]);
    if(in_array($extra_service_entry->ID, $selected_extra_services[$extra_service->Id]['entries'])){
      $extra_service->Entries[$extra_service_entry->ID] = $extra_service_entry;
      continue;
    }
  }
  $extra_service->Occupation = $extra_service_occupations[$extra_service->Id];
  $extra_services[$extra_service->Id] = $extra_service;
}
// echo '<pre>';
// print_r($this->view_data['entry_details_extra']);
// print_r($this->view_data['selected_extra_services']);
// print_r($this->view_data['occupations']);
// print_r($extra_services);
// print_r($selected_extra_services);
// print_r($package_details);
// print_r($entry_details);
// print_r($package_availability);
// echo '</pre>';
// die;
$mt = 4;
$extra_service_icons = array(
  'm' => 'fa fa-cutlery',
  'mtmp' => 'fa fa-cutlery',
  'mb' => 'fa fa-cutlery',
  // 'g' => 'fa fa-star',
);
?>
<div class="row">
  <div class="col-12 col-sm-6 mt-<?php echo $mt; ?>">
    <h3 class=" subTitleFilter pl-0">Detalii Rezervare <?php echo lang($package_details->Type); ?></h3>
    <ul class="list-unstyled">
      <li><i class="fa fa-building-o greenCTA"></i> <?php echo $title; ?></li>
      <?php 
      ?>
      <li><i class="fa fa-user-o greenCTA"></i> <?php echo $total_rooms == 1 ? '1 Camera' : ($total_rooms . ' Camere'); ?> x <?php echo $total_adults == 1 ? '1 adult' : ($total_adults . ' adulti'); ?><?php if($total_children) { echo ' + ' . ($total_children == 1 ? '1 copil' : ($total_children . ' copii')); } ?></li>
      <li><i class="fa fa-calendar-o greenCTA"></i> Perioada: <?php echo $start_date_formatted; ?> - <?php echo $end_date_formatted; ?></li>
      <li><i class="fa fa-moon-o greenCTA"></i> Numar <?php echo lang('plural_' . $duration_unit); ?>: <?php echo $duration; ?></li>
      
    <?php 
    $because_of_cancellation_policy = false;
    if($cancellation_policies){ 
      $min_cancellation_date_for_block = new DateTime(date('Y-m-d H:i:s',strtotime('+3 days')));
    ?>
    <br />
    <?php
      foreach($cancellation_policies as $cancellation_policy){
        $cancellation_date = DateTime::createFromFormat("Y-m-d\TH:i:s", $cancellation_policy->StartDate);
        if($min_cancellation_date_for_block > $cancellation_date){
          $block_payments = true;
          $because_of_cancellation_policy = true;
        }
        $cancellation_price = floatval($cancellation_policy->Amount);
        $currency_symbol = $this->_ci->currency_symbol;
        if($cancellation_policy->Currency === 'RON'){
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
        } ?>
        </li><?php
        themeFunctions::blockModule('booking/payment_methods/agency', true);
        themeFunctions::blockModule('booking/payment_methods/bank', true);
      }
      if($block_online){ ?>
        <li class="text-danger"><i class="fa fa-ban text-danger"></i> Metoda de plata <b>online</b> a fost dezactivata deoarece <?php
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
    <h3 class=" subTitleFilter pl-0">Servicii Incluse Vacanta</h3>
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
    <?php foreach($room_services as $k=>$room_service){
      $own_services = array();
      foreach($service_icons as $service_key => $service_icon){
        if(!isset($common_services[$service_key]) && strlen(trim($room_service[$service_key]))){
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
              $service_name = $room_service[$service_key];
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
    <?php } ?>
    <?php 
    $found_optional = false;
    foreach($extra_services as $extra_service_id=>$extra_service){
      if(filter_var($extra_service->Mandatory,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE) !== true){
        $found_optional = true;
        continue;
      }
      if(!empty($extra_service->BookingRules->MandatoryGuests)){
        $extra_service_mandatory_guests = array();
        foreach($extra_service->BookingRules->MandatoryGuests as $room_index => $mandatory_guests){
          foreach($mandatory_guests as $mandatory_guest){
            if(!isset($extra_service_mandatory_guests[$mandatory_guest->AgeQualifyingCode])){
              if($mandatory_guest->AgeQualifyingCode === 'a'){
                $extra_service_mandatory_guests[$mandatory_guest->AgeQualifyingCode] = 0;
              } elseif($mandatory_guest->Age){
                $extra_service_mandatory_guests[$mandatory_guest->AgeQualifyingCode] = array();
              }
            }
            if($mandatory_guest->AgeQualifyingCode === 'a'){
              $extra_service_mandatory_guests[$mandatory_guest->AgeQualifyingCode] += $mandatory_guest->Count;
            } elseif($mandatory_guest->Age){
              foreach($mandatory_guest->Age as $child_age){
                if(!isset($extra_service_mandatory_guests[$mandatory_guest->AgeQualifyingCode][(int)$child_age])){
                  $extra_service_mandatory_guests[$mandatory_guest->AgeQualifyingCode][(int)$child_age] = 0;
                }
                $extra_service_mandatory_guests[$mandatory_guest->AgeQualifyingCode][(int)$child_age]++;
              }
            }
          }
        }
        foreach($extra_service->Occupation as $occupant_type => $occupant_content){
          if(!$occupant_content){
            continue;
          }
          if(!isset($extra_service_mandatory_guests[$occupant_type]) || empty($extra_service_mandatory_guests[$occupant_type])){
            $found_optional = true;
            break;
          }
          if($occupant_type === 'a'){
            $extra_service_mandatory_guests[$occupant_type]--;
          } else {
            foreach($occupant_content as $child_age){
              if(!isset($extra_service_mandatory_guests[$occupant_type][(int)$child_age]) || empty($extra_service_mandatory_guests[$occupant_type][(int)$child_age])){
                $found_optional = true;
                break;
              }
              $extra_service_mandatory_guests[$occupant_type][(int)$child_age]--;
            }
          }
          if($found_optional){
            break;
          }
        }
      }
      if($found_optional){
        continue;
      }
      $service_name = $extra_service->Name;
      $service_name .= ' (';
      $service_name_occupation_arr = array();
      if($extra_service->Occupation['a']){
        $adulti = $extra_service->Occupation['a'];
        $service_name_occupation_arr[] = $adulti . ' ' . ($adulti == 1 ? 'adult' : 'adulti');
      }
      if($extra_service->Occupation['c']){
        $copii = count($extra_service->Occupation['c']);
        $service_name_occupation_arr[] = $copii . ' ' . ($copii == 1 ? 'copil' : 'copii');
      }
      $service_name .= implode(' + ', $service_name_occupation_arr);  
      $service_name .= ')';
      $service_type = !empty($extra_service->Type) && !empty($extra_service->Type->Code) ? $extra_service->Type->Code : '';
      
      ?>
      <li><i class="<?php echo isset($extra_service_icons[$service_type]) ? $extra_service_icons[$service_type] : 'fa fa-star'; ?> greenCTA"></i> <span class="hasTooltip" title="<?php echo htmlspecialchars($extra_service->Description); ?>"><?php echo $service_name; ?></span></li>
    <?php } ?>
    </ul>
    <?php 
    $this->package_has_extra_services = $found_optional;
    if($found_optional) { ?>
    <h3 class=" subTitleFilter pl-0">Servicii Alese Vacanta</h3>
    <ul class="list-unstyled">
    <?php 
    foreach($extra_services as $extra_service_id=>$extra_service){
      if(filter_var($extra_service->Mandatory,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE) === true){
        continue;
      }
      
      $service_name = $extra_service->Name;
      $service_name .= ' (';
      $service_name_occupation_arr = array();
      if($extra_service->Occupation['a']){
        $adulti = $extra_service->Occupation['a'];
        $service_name_occupation_arr[] = $adulti . ' ' . ($adulti == 1 ? 'adult' : 'adulti');
      }
      if($extra_service->Occupation['c']){
        $copii = count($extra_service->Occupation['c']);
        $service_name_occupation_arr[] = $copii . ' ' . ($copii == 1 ? 'copil' : 'copii');
      }
      $service_name .= implode(' + ', $service_name_occupation_arr);  
      $service_name .= ')';
      $service_type = !empty($extra_service->Type) && !empty($extra_service->Type->Code) ? $extra_service->Type->Code : '';
      ?>
      <li><i class="<?php echo isset($extra_service_icons[$service_type]) ? $extra_service_icons[$service_type] : 'fa fa-star-o'; ?> greenCTA"></i> <span class="hasTooltip" title="<?php echo htmlspecialchars($extra_service->Description); ?>"><?php echo $service_name; ?></span></li>
    <?php } ?>
    </ul>
    <?php } ?>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>