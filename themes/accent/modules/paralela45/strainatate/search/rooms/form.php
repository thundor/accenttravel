<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<form action="#" method="post" class="pachete-strainatate-search">
  <input type="hidden" id="destinatiePax1" value="" />
  <h5 class="mb-4 greyCol text-uppercase familyBold">Modificare date cautare</h5>
  <div class="row">
    <div class="form-group col-sm-12">
      <select id="categoriePax1" class="form-control form-control-lg" required name="categoriePax1">
        <option value="" selected>Plecare din</option>
      </select>
    </div>
    <div class="form-group col-sm-12 col-lg-8">
      <select id="datePax1" class="form-control form-control-lg" required name="datePax1">
        <option value="" selected>Alege data</option>
      </select>
    </div>
    <div class="form-group col-sm-12 col-lg-4">
      <select class="form-control form-control-lg" id="categPax1">
        <option value="">Numar nopti</option>
      </select>
    </div>
    <div class="col-sm-12" id="cam1Pax1">
      <div class="row">
        <div class="col-sm-12 col-md-4"><p class="roomHotel">Camera 1</p></div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="adultiCam1Pax1">
            <option value="1">1 Adult</option>
            <option value="2" selected>2 Adulti</option>
            <option value="3">3 Adulti</option>
            <option value="4">4 Adulti</option>
            <option value="5">5 Adulti</option>
            <option value="6">6 Adulti</option>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="copiiCam1Pax1">
            <option value="1">0 Copii</option>
            <option value="2">1 Copil</option>
            <option value="3">2 Copii</option>
          </select>
        </div>
        <div class="form-group col-sm-12 varsteCopii">
          <div class="form-group float-left">
            <p id="v1Pax11">Varsta Copil 1</p>
            <select class="form-control form-control-lg" id="varstaCop1Cam1Pax1">
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
            <p id="v2Pax11">Varsta Copil 2</p>
            <select class="form-control form-control-lg" id="varstaCop2Cam1Pax1">
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
          <p id="addCam2Pax1"><i class="fa fa-plus"></i> Adauga camera</p>
        </div>
      </div>
    </div>
    <div class="col-sm-12" id="cam2Pax1">
      <div class="row">
        <div class="col-sm-12 col-md-4"><p class="roomHotel">Camera 2</p></div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="adultiCam2Pax1">
            <option value="1">1 Adult</option>
            <option value="2" selected>2 Adulti</option>
            <option value="3">3 Adulti</option>
            <option value="4">4 Adulti</option>
            <option value="5">5 Adulti</option>
            <option value="6">6 Adulti</option>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="copiiCam2Pax1">
            <option value="1">0 Copii</option>
            <option value="2">1 Copil</option>
            <option value="3">2 Copii</option>
          </select>
        </div>
        <div class="form-group col-sm-12 varsteCopii">
          <div class="form-group float-left">
            <p id="v1Pax12">Varsta Copil 1</p>
            <select class="form-control form-control-lg" id="varstaCop1Cam2Pax1">
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
            <p id="v2Pax12">Varsta Copil 2</p>
            <select class="form-control form-control-lg" id="varstaCop2Cam2Pax1">
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
        <div class="form-group col-sm-6 col-md-4"><p id="addCam3Pax1"><i class="fa fa-plus"></i> Adauga camera</p></div>
        <div class="form-group col-sm-6 col-md-4"><p id="remCam2Pax1"><i class="fa fa-minus"></i> Sterge camera</p></div>
      </div>
    </div>
    <div class="col-sm-12" id="cam3Pax1">
      <div class="row">
        <div class="col-sm-12 col-md-4"><p class="roomHotel">Camera 3</p></div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="adultiCam3Pax1">
            <option value="1">1 Adult</option>
            <option value="2" selected>2 Adulti</option>
            <option value="3">3 Adulti</option>
            <option value="4">4 Adulti</option>
            <option value="5">5 Adulti</option>
            <option value="6">6 Adulti</option>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-4">
          <select class="form-control form-control-lg" id="copiiCam3Pax1">
            <option value="1">0 Copii</option>
            <option value="2">1 Copil</option>
            <option value="3">2 Copii</option>
          </select>
        </div>
        <div class="form-group col-sm-12 varsteCopii">
          <div class="form-group float-left">
            <p id="v1Pax13">Varsta Copil 1</p>
            <select class="form-control form-control-lg" id="varstaCop1Cam3Pax1">
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
            <p id="v2Pax13">Varsta Copil 2</p>
            <select class="form-control form-control-lg" id="varstaCop2Cam3Pax1">
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
        <div class="form-group col-sm-12 col-md-12 col-lg-6"><p id="remCam3Pax1"><i class="fa fa-minus"></i> Sterge camera</p></div>
      </div>
    </div>
    <div class="col-12 col-md-6 offset-md-6 btnCaut">
      <button type="submit" class="btn btn-block btn-lg bg-primary fontSize12" id="cautaHotel" name="cautaPax" role="button"><i class="fa fa-refresh"></i> Actualizeaza date</button>
    </div>
  </div>
</form>
<div id="paralela45-strainatate-loading-screen" class="loading-screen inactive">
  <div class="loading-screen-content">
    <i class="fa fa-spinner fa-spin fa-pulse blueLight"></i>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>