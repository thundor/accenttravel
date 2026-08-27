<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php $paralela45_special_layout = $this->_ci->uri->segment(0) === 'paralela45'; ?>
<div class="tab-pane<?php echo $paralela45_special_layout ? ' active' : ''?>" id="circuitPax" role="tabpanel">
  <form action="#" method="post" class="pachete-circuit-search">
    <div class="row">
      <div class="form-group col-sm-12 col-md-12 col-lg-4"> 
        <select id="taraPax2" class="form-control form-control-lg" name="taraPax2" required>
          <option value="" selected>Tara</option>
        </select>
      </div>
      <div class="form-group col-sm-12 col-md-12 col-lg-4"> 
        <select id="destinatiePax2" class="form-control form-control-lg" name="destinatiePax2">
          <option value="" selected>Destinatie</option>
        </select>
      </div>
      <div class="form-group  col-sm-12 col-md-12  col-lg-4">
        <select id="categoriePax2" class="form-control form-control-lg" name="categoriePax2">
          <option value="" selected>Plecare din</option>
        </select>
      </div>
      <div class="form-group col-sm-12 col-md-4 col-lg-4">
        <select id="datePax2" class="form-control form-control-lg" name="datePax2">
          <option value="" selected>Alege data</option>
        </select>
      </div>
      <div class="form-group col-sm-12 col-md-4 col-lg-3">
        <select class="form-control form-control-lg" id="categPax2">
          <option value="">Numar zile</option>
        </select>
      </div>
      <div class="form-group col-sm-12 col-md-4 col-lg-3">
        <input type="hidden" class="form-control form-control-lg" id="numeHotelPaxAbr" placeholder="Nume hotel (optional)">
      </div>
      <div class="col-sm-12" id="cam1Pax2">
        <div class="row">
          <div class="col-sm-12 col-md-3 col-lg-1"><p class="roomHotel">Camera 1</p></div>
          <div class="form-group col-sm-12 col-md-3 col-lg-2">
            <select class="form-control form-control-lg" id="adultiCam1Pax2">
              <option value="1">1 Adult</option>
              <option value="2" selected>2 Adulti</option>
              <option value="3">3 Adulti</option>
              <option value="4">4 Adulti</option>
              <option value="5">5 Adulti</option>
              <option value="6">6 Adulti</option>
            </select>
          </div>
          <div class="form-group col-sm-12 col-md-3 col-lg-2">
            <select class="form-control form-control-lg" id="copiiCam1Pax2">
              <option value="1">0 Copii</option>
              <option value="2">1 Copil</option>
              <option value="3">2 Copii</option>
            </select>
          </div>
          <div class=" col-sm-12 col-md-12 col-lg-7 varsteCopii">
            <div class="form-group float-left">
              <p id="v1Pax21">Varsta Copil 1</p>
              <select class="form-control form-control-lg" id="varstaCop1Cam1Pax2">
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
              <p id="v2Pax21">Varsta Copil 2</p>
              <select class="form-control form-control-lg" id="varstaCop2Cam1Pax2">
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
            <p id="addCam2Pax2"><i class="fa fa-plus"></i> Adauga camera</p>
          </div>
        </div>
      </div>
      <div class="col-sm-12" id="cam2Pax2">
        <div class="row">
          <div class="col-sm-12 col-md-2 col-lg-1"><p class="roomHotel">Camera 2</p></div>
          <div class="form-group col-sm-12 col-md-3 col-lg-2">
            <select class="form-control form-control-lg" id="adultiCam2Pax2">
              <option value="1">1 Adult</option>
              <option value="2" selected>2 Adulti</option>
              <option value="3">3 Adulti</option>
              <option value="4">4 Adulti</option>
              <option value="5">5 Adulti</option>
              <option value="6">6 Adulti</option>
            </select>
          </div>
          <div class="form-group col-sm-12 col-md-3 col-lg-2">
            <select class="form-control form-control-lg" id="copiiCam2Pax2">
              <option value="1">0 Copii</option>
              <option value="2">1 Copil</option>
              <option value="3">2 Copii</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-12 col-lg-7 varsteCopii">
            <div class="form-group float-left">
              <p id="v1Pax22">Varsta Copil 1</p>
              <select class="form-control form-control-lg" id="varstaCop1Cam2Pax2">
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
              <p id="v2Pax22">Varsta Copil 2</p>
              <select class="form-control form-control-lg" id="varstaCop2Cam2Pax2">
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
          <div class="form-group col-sm-6 col-md-4 col-lg-2"><p id="addCam3Pax2"><i class="fa fa-plus"></i> Adauga camera</p></div>
          <div class="form-group col-sm-6 col-md-4 col-lg-2"><p id="remCam2Pax2"><i class="fa fa-minus"></i> Sterge camera</p></div>
        </div>
      </div>
      <div class="col-sm-12" id="cam3Pax2">
        <div class="row">
          <div class="col-sm-12 col-md-2 col-lg-1"><p class="roomHotel">Camera 3</p></div>
          <div class="form-group col-sm-12 col-md-3 col-lg-2">
            <select class="form-control form-control-lg" id="adultiCam3Pax2">
              <option value="1">1 Adult</option>
              <option value="2" selected>2 Adulti</option>
              <option value="3">3 Adulti</option>
              <option value="4">4 Adulti</option>
              <option value="5">5 Adulti</option>
              <option value="6">6 Adulti</option>
            </select>
          </div>
          <div class="form-group col-sm-12 col-md-3 col-lg-2">
            <select class="form-control form-control-lg" id="copiiCam3Pax2">
              <option value="1">0 Copii</option>
              <option value="2">1 Copil</option>
              <option value="3">2 Copii</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-12 col-lg-7 varsteCopii">
            <div class="form-group float-left">
              <p id="v1Pax23">Varsta Copil 1</p>
              <select class="form-control form-control-lg" id="varstaCop1Cam3Pax2">
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
              <p id="v2Pax23">Varsta Copil 2</p>
              <select class="form-control form-control-lg" id="varstaCop2Cam3Pax2">
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
          <div class="form-group col-sm-12 col-md-12 col-lg-6"><p id="remCam3Pax2"><i class="fa fa-minus"></i> Sterge camera</p></div>
        </div>
      </div>
      <div class="form-group col-0 col-sm-4 col-md-8"></div>
      <?php
      if($this->_ci->user->can('backend-access')){ ?>
      <div class="form-group col-12 col-md-8">
        <input type="text" class="form-control paralela45_circuit_search_link" readonly />
      </div>
      <?php
      }
      ?>
      <div class="form-group col-12 col-sm-8 col-md-4">
        <button class="btn  btn-block btn-lg bg-primary" id="cautaPax2" name="cautaCircuite"><i class="fa fa-search"></i> Cauta Circuite</button>
      </div>
    </div>
  </form>
</div>
<div id="paralela45-circuit-loading-screen" class="loading-screen inactive">
  <div class="loading-screen-content"><?php
    if($paralela45_special_layout){ ?>
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