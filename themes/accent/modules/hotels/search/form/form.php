<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = &$this->hotel_search_data;
$data['hotel_id'] = '';
?>
<div class="tab-pane active" id="hotel" role="tabpanel">
  <form action="#" method="post" class="hotel-search">
    <div class="row">
      <input type="hidden" id="hotelId" value="<?php echo $data['hotel_id']; ?>" />
      <input type="hidden" id="hotelSearchCityId" value="<?php echo $data['city_id']; ?>" />
      <input type="hidden" id="hotelSearchStartDate" value="<?php echo $data['start_date']; ?>" />
      <input type="hidden" id="hotelSearchEndDate" value="<?php echo $data['end_date']; ?>" />
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="destinatie" placeholder="Destinatie / Oras" required value="<?php echo htmlspecialchars($data['city_name']); ?>" />
      </div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="numeHotel" placeholder="Nume hotel (optional)" value="<?php echo htmlspecialchars($data['hotel_name']); ?>" />
      </div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <input type="text" class="form-control form-control-lg" id="dateHotel" placeholder="Durata" required autocomplete="off" />
      </div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <select class="form-control form-control-lg" id="categHotel">
          <option value="0" <?php echo $data['min_stars'] == 0 ? 'selected' : ''; ?>>Categorie (optional)</option>
          <option value="1" <?php echo $data['min_stars'] == 1 ? 'selected' : ''; ?>>minim 2 stele</option>
          <option value="2" <?php echo $data['min_stars'] == 2 ? 'selected' : ''; ?>>minim 3 stele</option>
          <option value="3" <?php echo $data['min_stars'] == 3 ? 'selected' : ''; ?>>minim 4 stele</option>
          <option value="4" <?php echo $data['min_stars'] == 4 ? 'selected' : ''; ?>>minim 5 stele</option>
        </select>
      </div>                             
      <?php include 'form/rooms.php'; ?>
      <?php /*
      <div class="col-sm-12 col-md-6 col-lg-3">
        <input type="checkbox" class="float-left m-1" id="weekendSearch" name="weekendSearch" <?php echo $data['weekend'] ? 'checked' : ''; ?>/>
        <p><label for="weekendSearch">Cautare de weekend</label></p>
      </div>
      <div class="col-sm-12 col-md-6 col-lg-3">
        <input type="checkbox" class="float-left m-1" id="addAvionHotel" name="addAvionHotel" <?php echo $data['add_flight'] ? 'checked' : ''; ?>/>
        <p><label for="addAvionHotel">Adauga Zbor</label></p>
      </div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3 extraZbor">
        <input type="text" class="form-control form-control-lg" id="inpZborHot" name="inpZborHot" placeholder="Oras plecare" <?php echo $data['add_flight'] ? 'style="display:block;"' : ''; ?> value="<?php echo htmlspecialchars($data['depart_city']); ?>"/> 
      </div>
      */ ?>
      
      <?php
      if($this->_ci->user->can('backend-access')){ ?>
      <div class="form-group col-12 col-md-8">
        <input type="text" class="form-control trip_hotel_search_link" readonly />
      </div>
      <?php
      }
      ?>
      <div class="form-group col-12<?php echo $this->_ci->user->can('backend-access') ? ' col-md-4' : ' col-md-6 col-lg-3 offset-md-6 offset-lg-9'; ?> btnCaut">
        <button class="btn btn-block btn-lg bg-primary" id="cautaHotel" name="cautaHotel"><i class="fa fa-search"></i> Cauta hotel</button>
			</div>
    </div>
  </form>
</div>
<div id="loading-screen">
  <div class="loading-screen-content">
    <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>