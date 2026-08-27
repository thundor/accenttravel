<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php 
$special_layout = $this->_controller=='Packages';
$package_details = $this->view_data['package_details'];

$this->_ci->load->model('Options_model');
$settings = $this->_ci->Options_model->get('trip_packages_settings');
if(!$settings){
  $settings = array();
}
$this->_ci->load->model('Trip/Packages_model');
/* $package_projects = array(
  106 => "Balneo ~ 2018",
  107 => "Litoral Costinesti ~ 2018",
  108 => "Proiect Test 2018",
  109 => "Litoral Venus ~ 2018",
  110 => "Litoral Mamaia ~ 2018",
  111 => "Litoral Neptun ~ 2018",
  112 => "Litoral Olimp ~ 2018",
  113 => "Litoral Eforie Nord ~ 2018",
  114 => "Litoral Eforie Sud ~ 2018",
  115 => "Litoral Jupiter ~ 2018",
  116 => "Litoral Saturn ~ 2018",
  117 => "Litoral Cap Aurora ~ 2018",
  118 => "Techirghiol ~ 2018",
  119 => "Litoral Navodari ~ 2018",
  120 => "Litoral Mangalia ~ 2018",
  123 => "Munte ~ 2018",
); */
/* $include_package_projects = array();
if(isset($settings['projects']) && !empty($settings['projects'])){
  $include_package_projects = explode(',', $settings['projects']);
}
$package_projects_result = $this->_ci->Packages_model->loadPackageProjects();
if($package_projects_result){
  foreach($package_projects_result->_embedded->projects as $package_project){
    if(strpos($package_project->Name,'!') !== false){
      continue;
    }
    if($include_package_projects && !in_array($package_project->Id, $include_package_projects)){
      continue;
    }
    $package_projects[] = $package_project;
  }
} */
/* $package_categories = array();
$include_package_categories = array();
if(isset($settings['categories']) && !empty($settings['categories'])){
  $include_package_categories = explode(',', $settings['categories']);
}
$package_categories_result = $this->_ci->Packages_model->loadPackageCategories();
if($package_categories_result){
  foreach($package_categories_result->_embedded->categories as $package_category){
    if(strpos($package_category->Name,'!') !== false){
      continue;
    }
    if($include_package_categories && !in_array($package_category->Id, $include_package_categories)){
      continue;
    }
    $package_categories[] = $package_category;
  }
} */
$include_package_destinations = array();
if(isset($settings['destinations']) && !empty($settings['destinations'])){
  $include_package_destinations = explode(',', $settings['destinations']);
}
$package_destinations = array();
$package_destinations_result = $this->_ci->Packages_model->loadPackageDestinations();

if($package_destinations_result){
  foreach($package_destinations_result->_embedded->cities as $package_destination){
    if($include_package_destinations && !in_array($package_destination->Id, $include_package_destinations)){
      continue;
    }
    $package_destinations[] = $package_destination;
  }
}
?>
<form action="#" method="post" class="package-search">
  <input type="hidden" id="packageId" value="<?php echo $package_details->Id; ?>" />
  <input type="hidden" id="destinatiePax" value="" />
  <input type="hidden" id="categoriePax" value="" />
  <h5 class="mb-4 greyCol text-uppercase familyBold">Modificare date cautare</h5>
  <div class="row">
    <?php /* <div class="form-group  col-sm-12 col-md-6 col-xl-3">
      <select id="categoriePax" class="form-control form-control-lg" name="categoriePax">
        <option value="" selected>Categorie</option>
        <?php foreach($package_categories as $package_category) { ?>
        <option value="<?php echo htmlspecialchars($package_category->Code); ?>"><?php echo htmlspecialchars($package_category->Name); ?></option>
        <?php } ?>
      </select>
    </div> */ ?>
    <?php /* <div class="form-group col-sm-12 col-md-4"><input type="text" class="form-control form-control-lg" id="numeHotelPax" placeholder="Nume hotel (optional)"></div> */ ?>
    <div class="form-group col-sm-12 col-md-6">
      <input type="text" class="form-control form-control-lg" id="datePax" placeholder="Data incepand cu" required>
    </div>
    <div class="form-group col-sm-12 col-md-6">
      <select class="form-control form-control-lg" id="categPax">
        <option value="">Numar nopti</option>
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
        <div class="col-sm-12 col-md-4"><p class="roomHotel">Camera 1</p></div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="adultiCam1Pax">
            <option value="1">1 Adult</option>
            <option value="2" selected="">2 Adulti</option>
            <option value="3">3 Adulti</option>
            <option value="4">4 Adulti</option>
            <option value="5">5 Adulti</option>
            <option value="6">6 Adulti</option>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="copiiCam1Pax">
            <option value="1">0 Copii</option>
            <option value="2">1 Copil</option>
            <option value="3">2 Copii</option>
          </select>
        </div>
        <div class="form-group col-sm-12 varsteCopii">
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

        <div class="col-sm-12 col-md-4"><p class="roomHotel">Camera 2</p></div>

        <div class="form-group col-sm-6 col-md-4">

          <select class="form-control form-control-lg" id="adultiCam2Pax">
            <option value="1">1 Adult</option>
            <option value="2" selected="">2 Adulti</option>
            <option value="3">3 Adulti</option>
            <option value="4">4 Adulti</option>
            <option value="5">5 Adulti</option>
            <option value="6">6 Adulti</option>
          </select>

        </div>

        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="copiiCam2Pax">
            <option value="1">0 Copii</option>
            <option value="2">1 Copil</option>
            <option value="3">2 Copii</option>
          </select>

        </div>
        <div class=" form-group col-sm-12 varsteCopii">
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

        <div class="form-group col-sm-6 col-md-4"><p id="addCam3Pax"><i class="fa fa-plus"></i> Adauga camera</p></div>
        <div class="form-group col-sm-6 col-md-4"><p id="remCam2Pax"><i class="fa fa-minus"></i> Sterge camera</p></div>
      </div>
    </div>

    <div class="col-sm-12" id="cam3Pax">
      <div class="row">

        <div class="col-sm-12 col-md-4"><p class="roomHotel">Camera 3</p></div>

        <div class="form-group col-sm-6 col-md-4">

          <select class="form-control form-control-lg" id="adultiCam3Pax">
            <option value="1">1 Adult</option>
            <option value="2" selected="">2 Adulti</option>
            <option value="3">3 Adulti</option>
            <option value="4">4 Adulti</option>
            <option value="5">5 Adulti</option>
            <option value="6">6 Adulti</option>
          </select>

        </div>

        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="copiiCam3Pax">
            <option value="1">0 Copii</option>
            <option value="2">1 Copil</option>
            <option value="3">2 Copii</option>
          </select>

        </div>

        <div class="form-group col-sm-12 varsteCopii">
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
    <div class="col-12 col-md-6 offset-md-6 btnCaut">
      <button type="submit" class="btn btn-block btn-lg bg-primary fontSize12" id="cautaHotel" name="cautaPax" role="button"><i class="fa fa-refresh"></i> Actualizeaza date</button>
    </div>
  </div>
</form>
<div id="packages-loading-screen" class="loading-screen inactive">
  <div class="loading-screen-content">
    <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>