<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = &$this->flights_search_data;
$special_layout = $this->_controller=='Flights';
?>
<div class="tab-pane <?php echo $special_layout ? 'active' : ''; ?>" id="avion" role="tabpanel">
  <form class="flight-search" action="#" method="post">
    <div class="row">
      <div class="form-group  col-sm-12 col-md-6 col-lg-3"><input type="text" class="form-control form-control-lg" id="plecare" placeholder="Plecare din" required></div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3"><input type="text" class="form-control form-control-lg" id="sosire" placeholder="Sosire in" required></div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3"><input type="text" class="form-control form-control-lg" id="dateZborAvion" placeholder="Data plecare" autocomplete="off"></div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3"><input type="text" class="form-control form-control-lg" id="dateZborAvion2" placeholder="Data retur" autocomplete="off"></div>
      <div class="form-group col-sm-12 col-md-6 col-lg-3">
        <select class="form-control form-control-lg" id="clasaZbor" required>
          <option value="">Clasa de zbor</option>
          <option value="1">Economy</option>
          <option value="2">First class</option>
          <option value="3">Business</option>
          <option value="4">Premium</option>
        </select>
      </div>
      <div class="form-group col-sm-12 col-md-12 col-lg-3">
        <select class="form-control form-control-lg" id="adultiCam1Av">
            <option value="1" selected>1 Adult</option>
            <option value="2">2 Adulti</option>
            <option value="3">3 Adulti</option>
            <option value="4">4 Adulti</option>
            <option value="5">5 Adulti</option>
            <option value="6">6 Adulti</option>
          </select>
      </div> 
      <div class="col-sm-12 col-md-12 col-lg-6" id="copiiZborArea">
        <div class="row">   
          <div class="form-group col-sm-12 col-md-6 col-lg-6">
            <select class="form-control form-control-lg" id="adultisenioriCam1Av">
              <option value="0" selected>Seniori</option>
              <option value="1">1 Senior (> 60 ani)</option>
              <option value="2">2 Seniori (> 60 ani)</option>
              <option value="3">3 Seniori (> 60 ani)</option>
              <option value="4">4 Seniori (> 60 ani)</option>
              <option value="5">5 Seniori (> 60 ani)</option>
              <option value="6">6 Seniori (> 60 ani)</option>
            </select>                                                    
          </div>
          <div class="form-group col-sm-12 col-md-6 col-lg-6">
            <select class="form-control form-control-lg" id="copiiZbor">
              <option value="1">0 Copii</option>
              <option value="2">1 Copil</option>
              <option value="3">2 Copii</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-12 col-lg-12 varsteCopii">
            <div class="form-group float-left">
              <p id="v1Z">Varsta Copil 1</p>
              <select class="form-control form-control-lg" id="aniCop1Zbor">
                <option value="1">&lt; 1 an</option>
                <option value="2">1 an</option>
                <option value="3">2 ani</option>
                <option value="4">3 ani</option>
                <option value="5">4 ani</option>
                <option value="6">5 ani</option>
                <option value="7">6 ani</option>
                <option value="8">7 ani</option>
                <option value="9">8 ani</option>
                <option value="10">9 ani</option>
                <option value="11">10 ani</option>
                <option value="12">11 ani</option>
                <option value="13">12 ani</option>
                <option value="14">13 ani</option>
                <option value="15">14 ani</option>
                <option value="16">15 ani</option>
                <option value="17">16 ani</option>
                <option value="18">17 ani</option>
              </select>
            </div>
            <div class="form-group float-left">
              <p id="v2Z">Varsta Copil 2</p>
              <select class="form-control form-control-lg" id="aniCop2Zbor">
                <option value="1">&lt; 1 an</option>
                <option value="2">1 an</option>
                <option value="3">2 ani</option>
                <option value="4">3 ani</option>
                <option value="5">4 ani</option>
                <option value="6">5 ani</option>
                <option value="7">6 ani</option>
                <option value="8">7 ani</option>
                <option value="9">8 ani</option>
                <option value="10">9 ani</option>
                <option value="11">10 ani</option>
                <option value="12">11 ani</option>
                <option value="13">12 ani</option>
                <option value="14">13 ani</option>
                <option value="15">14 ani</option>
                <option value="16">15 ani</option>
                <option value="17">16 ani</option>
                <option value="18">17 ani</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 <?php echo $special_layout ? 'col-md-4' : 'col-md-6'; ?>"><input type="checkbox" class="float-left m-1" id="doarDirect" name="doarDirect" /> <p><label for="doarDirect">Doar zbor direct</label></p></div>
      <div class="col-sm-12 <?php echo $special_layout ? 'col-md-4' : 'col-md-6'; ?>"><input type="checkbox" class="float-left m-1" id="doarDus" name="doarDus" /> <p><label for="doarDus">Doar dus</label></p></div>
      <?php /*<div class="form-group col-sm-12 <?php echo $special_layout ? 'col-md-4' : 'col-md-8'; ?>">
        <input type="checkbox" class="float-left m-1" id="dateFlexZbor" name="dateFlexZbor" /> <p><label for="dateFlexZbor">Date flexibile <?php echo $special_layout ? '(+/- 3 zile)' : ''; ?></label></p>
      </div>*/?>
      <?php
			if($this->_ci->user->can('backend-access')){ ?>
			<div class="form-group col-12 col-md-8">
				<input type="text" class="form-control trip_flight_search_link" readonly />
			</div>
			<?php
			}
			?>
      <div class="form-group col-12 col-md-4 <?php echo $special_layout ? ($this->_ci->user->can('backend-access') ? '' : 'offset-md-4') : ''; ?>">
        <button class="btn  btn-block btn-lg bg-primary" id="cautaZbor" name="cautaZbor"><i class="fa fa-search"></i> Cauta zbor</button>
      </div>
    </div>
  </form>
</div>
<div id="flights-loading-screen" class="loading-screen inactive">
  <div class="loading-screen-content">
    <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>