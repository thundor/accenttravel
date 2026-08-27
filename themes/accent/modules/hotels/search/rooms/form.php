<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = &$this->hotel_search_data;
$hotel_details = $this->view_data['hotel_details'];
?>
<form action="#" class="hotel-search">
  <div class="row">
    <h5 class="pl-3 mt- mb-4 greyCol text-uppercase familyBold">Actualizeaza date cautare</h5>
    <input type="hidden" id="hotelId" value="<?php echo $hotel_details->Id; ?>" />
    <input type="hidden" id="hotelSearchCityId" value="<?php echo $hotel_details->CityId; ?>" />
    <input type="hidden" id="hotelSearchStartDate" value="<?php echo $data['start_date']; ?>" />
    <input type="hidden" id="hotelSearchEndDate" value="<?php echo $data['end_date']; ?>" />
    <input type="hidden" id="numeHotel" value="" />
    <input type="hidden" id="categHotel" value="0" />
    <?php /*<input type="hidden" id="weekendSearch" value="" /> */?>
    <div class="form-group col-sm-12">
      <input type="text" class="form-control form-control-lg" id="dateHotel" placeholder="Schimba perioada" required>
    </div>
    <?php include 'form/rooms.php'; ?>
    <div class="col-sm-12 col-md-6">
      <?php /*
      <input type="checkbox" class="float-left m-1" id="addAvionHotel" name="addAvionHotel" <?php echo $data['add_flight'] ? 'checked' : ''; ?>/>
      <p><label for="addAvionHotel">Adauga Zbor</label></p>
      */ ?>
    </div>
    <div class="col-12 col-md-6 btnCaut">
      <button type="submit" class="btn btn-block btn-lg bg-primary fontSize12" id="cautaHotel" name="cautaHotel" role="button"><i class="fa fa-refresh"></i> Actualizeaza date</button>
    </div>
    <div class="form-group col-sm-12 extraZbor mt-2">
      <?php /*
      <input type="text" class="form-control form-control-lg" id="inpZborHot" name="inpZborHot" placeholder="Oras plecare" <?php echo $data['add_flight'] ? 'style="display:block;"' : ''; ?> value="<?php echo htmlspecialchars($data['depart_city']); ?>"/> 
      */ ?>
    </div>
  </div>
</form>
<div id="loading-screen">
  <div class="loading-screen-content">
    <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>