<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="col-sm-12 col-md-12" id="cam<?php echo $room; ?>CB" <?php echo $room_data ? 'style="display:block"' : ''; ?>>
  <div class="row">
    <div class="col-sm-12 col-md-4"><p class="roomHotel">Camera <?php echo $room; ?></p></div>
    <div class="form-group col-sm-6 col-md-4">
      <select class="form-control form-control-lg" id="adultiCam<?php echo $room; ?>CB">
        <option value="1" <?php echo $adults == 1 ? 'selected' : ''; ?>>1 Adult</option>
        <option value="2" <?php echo $adults == 2 ? 'selected' : ''; ?>>2 Adulti</option>
        <option value="3" <?php echo $adults == 3 ? 'selected' : ''; ?>>3 Adulti</option>
        <option value="4" <?php echo $adults == 4 ? 'selected' : ''; ?>>4 Adulti</option>
        <option value="5" <?php echo $adults == 5 ? 'selected' : ''; ?>>5 Adulti</option>
        <option value="6" <?php echo $adults == 6 ? 'selected' : ''; ?>>6 Adulti</option>
      </select>
    </div>
    <div class="form-group col-sm-6 col-md-4">
      <select class="form-control form-control-lg" id="copiiCam<?php echo $room; ?>CB">
        <option value="1" <?php echo $children == 0 ? 'selected' : ''; ?>>0 Copii</option>
        <option value="2" <?php echo $children == 1 ? 'selected' : ''; ?>>1 Copil</option>
        <option value="3" <?php echo $children == 2 ? 'selected' : ''; ?>>2 Copii</option>
      </select>
    </div>
    <div class="form-group col-sm-12 varsteCopii" <?php echo $children ? 'style="display:block;"' : ''; ?>>
      <div class="form-group float-left">
        <p id="v1CB<?php echo $room; ?>" <?php echo $children ? 'style="display:block;"' : ''; ?>>Varsta Copil 1</p>
        <select class="form-control form-control-lg" id="varstaCop1Cam<?php echo $room; ?>CB" <?php echo $children ? 'style="display:block;"' : ''; ?>>
          <option value="1" <?php echo $first_child_age == 1 ? 'selected' : ''; ?>>&lt; 1 an</option>
          <option value="2" <?php echo $first_child_age == 2 ? 'selected' : ''; ?>>1 an</option>
          <option value="3" <?php echo $first_child_age == 3 ? 'selected' : ''; ?>>2 ani</option>
          <option value="4" <?php echo $first_child_age == 4 ? 'selected' : ''; ?>>3 ani</option>
          <option value="5" <?php echo $first_child_age == 5 ? 'selected' : ''; ?>>4 ani</option>
          <option value="6" <?php echo $first_child_age == 6 ? 'selected' : ''; ?>>5 ani</option>
          <option value="7" <?php echo $first_child_age == 7 ? 'selected' : ''; ?>>6 ani</option>
          <option value="8" <?php echo $first_child_age == 8 ? 'selected' : ''; ?>>7 ani</option>
          <option value="9" <?php echo $first_child_age == 9 ? 'selected' : ''; ?>>8 ani</option>
          <option value="10" <?php echo $first_child_age == 10 ? 'selected' : ''; ?>>9 ani</option>
          <option value="11" <?php echo $first_child_age == 11 ? 'selected' : ''; ?>>10 ani</option>
          <option value="12" <?php echo $first_child_age == 12 ? 'selected' : ''; ?>>11 ani</option>
          <option value="13" <?php echo $first_child_age == 13 ? 'selected' : ''; ?>>12 ani</option>
          <option value="14" <?php echo $first_child_age == 14 ? 'selected' : ''; ?>>13 ani</option>
          <option value="15" <?php echo $first_child_age == 15 ? 'selected' : ''; ?>>14 ani</option>
          <option value="16" <?php echo $first_child_age == 16 ? 'selected' : ''; ?>>15 ani</option>
          <option value="17" <?php echo $first_child_age == 17 ? 'selected' : ''; ?>>16 ani</option>
          <option value="18" <?php echo $first_child_age == 18 ? 'selected' : ''; ?>>17 ani</option>
        </select>
      </div>
      <div class="form-group float-left">
        <p id="v2CB<?php echo $room; ?>" <?php echo $children==2 ? 'style="display:block;"' : 'style="display:none;"'; ?>>Varsta Copil 2</p>
        <select class="form-control form-control-lg" id="varstaCop2Cam<?php echo $room; ?>CB" <?php echo $children==2 ? 'style="display:block;"' : 'style="display:none;"'; ?>
          <option value="1" <?php echo $second_child_age == 1 ? 'selected' : ''; ?>>&lt; 1 an</option>
          <option value="2" <?php echo $second_child_age == 2 ? 'selected' : ''; ?>>1 an</option>
          <option value="3" <?php echo $second_child_age == 3 ? 'selected' : ''; ?>>2 ani</option>
          <option value="4" <?php echo $second_child_age == 4 ? 'selected' : ''; ?>>3 ani</option>
          <option value="5" <?php echo $second_child_age == 5 ? 'selected' : ''; ?>>4 ani</option>
          <option value="6" <?php echo $second_child_age == 6 ? 'selected' : ''; ?>>5 ani</option>
          <option value="7" <?php echo $second_child_age == 7 ? 'selected' : ''; ?>>6 ani</option>
          <option value="8" <?php echo $second_child_age == 8 ? 'selected' : ''; ?>>7 ani</option>
          <option value="9" <?php echo $second_child_age == 9 ? 'selected' : ''; ?>>8 ani</option>
          <option value="10" <?php echo $second_child_age == 10 ? 'selected' : ''; ?>>9 ani</option>
          <option value="11" <?php echo $second_child_age == 11 ? 'selected' : ''; ?>>10 ani</option>
          <option value="12" <?php echo $second_child_age == 12 ? 'selected' : ''; ?>>11 ani</option>
          <option value="13" <?php echo $second_child_age == 13 ? 'selected' : ''; ?>>12 ani</option>
          <option value="14" <?php echo $second_child_age == 14 ? 'selected' : ''; ?>>13 ani</option>
          <option value="15" <?php echo $second_child_age == 15 ? 'selected' : ''; ?>>14 ani</option>
          <option value="16" <?php echo $second_child_age == 16 ? 'selected' : ''; ?>>15 ani</option>
          <option value="17" <?php echo $second_child_age == 17 ? 'selected' : ''; ?>>16 ani</option>
          <option value="18" <?php echo $second_child_age == 18 ? 'selected' : ''; ?>>17 ani</option>
        </select>
      </div>
    </div>
    <?php if($room<$max_rooms) { ?>
    <div class="form-group col-sm-4" <?php echo $has_next_room ? 'style="display:none;"' : ''; ?>>
      <p id="addCam<?php echo $room+1; ?>CB" ><i class="fa fa-plus"></i> Adauga camera</p>
    </div>
    <?php } ?>
    <?php if($room>1) { ?>
    <div class="form-group col-sm-4" <?php echo $has_next_room ? 'style="display:none;"' : ''; ?>>
      <p id="remCam<?php echo $room; ?>CB"><i class="fa fa-minus"></i> Sterge camera</p>
    </div>
    <?php } ?>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>