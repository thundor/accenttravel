<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$max_rooms = 3;
for ($room=1; $room<=$max_rooms; $room++){
  $room_index = $room-1;
  $room_data = isset($data['occupancy'][$room_index]) ? $data['occupancy'][$room_index] : array();
  $adults = isset($room_data['adt']) ? $room_data['adt'] : 2;
  $chd = isset($room_data['chd']) && is_array($room_data['chd']) ? $room_data['chd'] : array();
  $ages= isset($chd['age']) && is_array($chd['age']) ? $chd['age'] : array();
  $children = count($ages);
  $first_child_age = isset($ages[0]) ? $ages[0] : 0;
  $second_child_age = isset($ages[1]) ? $ages[1] : 0;
  $has_next_room = ($room != $max_rooms) && isset($data['occupancy'][$room_index + 1]) 
        && isset($data['occupancy'][$room_index + 1]['adt']);
  include 'room.php';
}
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>