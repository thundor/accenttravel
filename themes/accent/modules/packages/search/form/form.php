<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php 
$data = &$this->packages_search_data;
$nights = isset($_GET['nights']) ? (int)$_GET['nights'] : 0;
if($nights < 1 || $nights > 10){
  $nights = 0;
}
$special_layout = $this->_controller=='Packages';
$p45_module_status = !$special_layout && true;
if($p45_module_status){
  $p45_strainatate_enabled = true;
  $p45_circuite_enabled = true;
}
$p45_any_enabled = $p45_module_status && ($p45_strainatate_enabled || $p45_circuite_enabled); ?>
<div class="tab-pane <?php echo $special_layout ? 'active' : ''; ?>" id="package" role="tabpanel">
<?php if($p45_any_enabled){ ?>
  <ul class="nav nav-tabs flex-column flex-sm-row" id="optionsPax" role="tablist">
  <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#romaniaPax" role="tab" aria-controls="romaniaPax">Romania</a></li>
<?php if($p45_strainatate_enabled){ ?>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#abroadPax" role="tab" aria-controls="abroadPax">Strainatate</a></li>
<?php } ?>
<?php if($p45_circuite_enabled){ ?>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#circuitPax" role="tab" aria-controls="circuitPax">Circuite</a></li>
<?php } ?>
  </ul>
  <div class="row">
    <div class="tab-content">
      <div class="tab-pane active" id="romaniaPax" role="tabpanel">
<?php } ?>
        <form action="#" method="post" class="package-search">
          <div class="row">
            <div class="form-group  col-sm-12 col-md-6 col-xl-3">
              <select id="categoriePax" class="form-control form-control-lg" name="categoriePax">
                <option value="" selected>Categorie</option>
                <?php foreach($this->package_categories as $package_category) { ?>
                <option value="<?php echo htmlspecialchars($package_category->Code); ?>"><?php echo htmlspecialchars($package_category->Name); ?></option>
                <?php } ?>
              </select>
            </div>
            <?php /* <div class="form-group  col-sm-12 col-md-6 col-xl-3">
              <select id="categoriePax" class="form-control form-control-lg" name="categoriePax">
                <option value="" selected>Categorie</option>
                <?php foreach($package_projects as $package_project_id => $package_project_name) { ?>
                <option value="<?php echo htmlspecialchars($package_project_id); ?>"><?php echo htmlspecialchars($package_project_name); ?></option>
                <?php } ?>
              </select>
            </div> */ ?>
            <div class="form-group col-sm-12 col-md-6 col-xl-3"> 
              <select id="destinatiePax" class="form-control form-control-lg" name="destinatiePax">
                <option value="" selected>Destinatie </option>
                <?php foreach($this->package_destinations as $package_destination) { ?>
                <option value="<?php echo htmlspecialchars($package_destination->Id); ?>"><?php echo htmlspecialchars($package_destination->Name); ?></option>
                <?php } ?>
              </select>
            </div>
            <?php
            if($this->_ci->user->can('backend-access')){ ?>
            <div class="form-group col-sm-12 col-md-6 col-xl-6"><input type="text" class="form-control form-control-lg" id="numeHotelPax" placeholder="Nume hotel (optional)"></div><?php
            } else { ?>
            <input type="hidden" id="numeHotelPax" placeholder="Nume hotel (optional)" value="" /><?php
            } ?>
            <div class="form-group col-sm-12 col-md-6 col-xl-3">
              <input type="text" class="form-control form-control-lg" id="datePax" placeholder="Data incepand cu" required autocomplete="off">
            </div>
            <div class="form-group col-sm-12 col-md-6 col-xl-3">
              <select class="form-control form-control-lg" id="categPax">
                <option value="">Numar nopti</option>
                <?php if($nights){ ?>
                <option value="<?php echo $nights; ?>"><?php echo $nights; ?> nopti</option>
                <?php } ?>
                <?php /* <option value="2">2 nopti</option> */ ?>
                <?php /* <option value="3">3 nopti</option> */ ?>
                <?php /* <option value="4">4 nopti</option> */ ?>
                <?php /* <option value="5">5 nopti</option> */ ?>
                <?php /* <option value="6">6 nopti</option> */ ?>
                <?php /* <option value="7">7 nopti</option> */ ?>
                <?php /* <option value="8">8 nopti</option> */ ?>
                <?php /* <option value="9">9 nopti</option> */ ?>
                <?php /* <option value="10">10 nopti</option> */ ?>
                <option value="0">ORICATE nopti</option>
              </select>
            </div>

            <div class="col-sm-12" id="cam1Pax">
              <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-1"><p class="roomHotel">Camera 1</p></div>
                <div class="form-group col-sm-12 col-md-3 col-lg-2">
                  <select class="form-control form-control-lg" id="adultiCam1Pax">
                    <option value="1">1 Adult</option>
                    <option value="2" selected="">2 Adulti</option>
                    <option value="3">3 Adulti</option>
                    <option value="4">4 Adulti</option>
                    <option value="5">5 Adulti</option>
                    <option value="6">6 Adulti</option>
                  </select>
                </div>
                <div class="form-group col-sm-12 col-md-3 col-lg-2">
                  <select class="form-control form-control-lg" id="copiiCam1Pax">
                    <option value="1">0 Copii</option>
                    <option value="2">1 Copil</option>
                    <option value="3">2 Copii</option>
                  </select>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-7 varsteCopii">
                  <div class="form-group float-left">
                    <p id="v1Pax1">Varsta Copil 1</p>
                    <select class="form-control form-control-lg" id="varstaCop1Cam1Pax">
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
                    <p id="v2Pax1">Varsta Copil 2</p>
                    <select class="form-control form-control-lg" id="varstaCop2Cam1Pax">
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
                <div class="form-group col-sm-12 col-md-12 col-lg-6">
                  <p id="addCam2Pax"><i class="fa fa-plus"></i> Adauga camera</p>
                </div>
              </div>
            </div>

            <div class="col-sm-12" id="cam2Pax">
              <div class="row">

                <div class="col-sm-12 col-md-2 col-lg-1"><p class="roomHotel">Camera 2</p></div>

                <div class="form-group col-sm-12 col-md-3 col-lg-2">

                  <select class="form-control form-control-lg" id="adultiCam2Pax">
                    <option value="1">1 Adult</option>
                    <option value="2" selected="">2 Adulti</option>
                    <option value="3">3 Adulti</option>
                    <option value="4">4 Adulti</option>
                    <option value="5">5 Adulti</option>
                    <option value="6">6 Adulti</option>
                  </select>

                </div>

                <div class="form-group col-sm-12 col-md-3 col-lg-2">
                  <select class="form-control form-control-lg" id="copiiCam2Pax">
                    <option value="1">0 Copii</option>
                    <option value="2">1 Copil</option>
                    <option value="3">2 Copii</option>
                  </select>

                </div>
                <div class=" col-sm-12 col-md-12 col-lg-7 varsteCopii">
                  <div class="form-group float-left">
                    <p id="v1Pax2">Varsta Copil 1</p>
                    <select class="form-control form-control-lg" id="varstaCop1Cam2Pax">
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
                    <p id="v2Pax2">Varsta Copil 2</p>
                    <select class="form-control form-control-lg" id="varstaCop2Cam2Pax">
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

                <div class="form-group col-sm-12 col-md-3 col-lg-2"><p id="addCam3Pax"><i class="fa fa-plus"></i> Adauga camera</p></div>
                <div class="form-group col-sm-12 col-md-3 col-lg-2"><p id="remCam2Pax"><i class="fa fa-minus"></i> Sterge camera</p></div>
              </div>
            </div>

            <div class="col-sm-12" id="cam3Pax">
              <div class="row">

                <div class="col-sm-12 col-md-2 col-lg-1"><p class="roomHotel">Camera 3</p></div>

                <div class="form-group col-sm-12 col-md-3 col-lg-2">

                  <select class="form-control form-control-lg" id="adultiCam3Pax">
                    <option value="1">1 Adult</option>
                    <option value="2" selected="">2 Adulti</option>
                    <option value="3">3 Adulti</option>
                    <option value="4">4 Adulti</option>
                    <option value="5">5 Adulti</option>
                    <option value="6">6 Adulti</option>
                  </select>

                </div>

                <div class="form-group col-sm-12 col-md-3 col-lg-2">
                  <select class="form-control form-control-lg" id="copiiCam3Pax">
                    <option value="1">0 Copii</option>
                    <option value="2">1 Copil</option>
                    <option value="3">2 Copii</option>
                  </select>

                </div>

                <div class="col-sm-12 col-md-12 col-lg-7 varsteCopii">
                  <div class="form-group float-left">
                    <p id="v1Pax3">Varsta Copil 1</p>
                    <select class="form-control form-control-lg" id="varstaCop1Cam3Pax">
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
                    <p id="v2Pax3">Varsta Copil 2</p>
                    <select class="form-control form-control-lg" id="varstaCop2Cam3Pax">
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
                <div class="form-group col-sm-12 col-md-12 col-lg-6"><p id="remCam3Pax"><i class="fa fa-minus"></i> Sterge camera</p></div>
              </div>
            </div>

            <?php
            if($this->_ci->user->can('backend-access')){ ?>
            <div class="form-group col-12 col-md-8">
              <input type="text" class="form-control trip_package_search_link" readonly />
            </div>
            <?php
            }
            ?>
            <div class="form-group col-12 col-md-4<?php echo $this->_ci->user->can('backend-access') ? '' : ' col-sm-8 offset-sm-4 offset-md-8'; ?>">
              <button class="btn  btn-block btn-lg bg-primary" id="cautaPax" name="cautaPax"><i class="fa fa-search"></i> Cauta vacante</button>
            </div>
          </div>
        </form>
<?php if($p45_any_enabled){ ?> 
      </div>
<?php if($p45_strainatate_enabled){ ?>
      <?php themeFunctions::loadModule('paralela45/strainatate/search/form',__FILE__ . 'paralela45/strainatate'); ?>
      <?php themeFunctions::loadAddons(__FILE__ . 'paralela45/strainatate'); ?>
<?php } ?>
<?php if($p45_circuite_enabled){ ?>
      <?php themeFunctions::loadModule('paralela45/circuit/search/form',__FILE__ . 'paralela45/circuit'); ?>
      <?php themeFunctions::loadAddons(__FILE__ . 'paralela45/circuit'); ?>
<?php } ?>
    </div>
  </div>
<?php } ?>
</div>
<div id="packages-loading-screen" class="loading-screen inactive">
  <div class="loading-screen-content"><?php
    if($special_layout){ ?>
    <div class="custom-spinner-wrapper">
      <h4>Cautam toate ofertele pentru aceasta destinatie</h4>
      <span class="custom-spinner">
        <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
      </span>
    </div><?php
    } else { ?>
      <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i><?php
    } ?>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>