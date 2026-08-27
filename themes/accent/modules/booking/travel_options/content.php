<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$u0 = $this->_ci->uri->segment(0);
$u1 = $this->_ci->uri->segment(1);
$travel_is_involved = ($u0 == 'trip' && $u1 == 'flight') || ($u0 == 'paralela45' && $u1 == 'strainatate');
?>
<h3 class="subSecTicket col-12 mb-4"> Optiuni si detalii de calatorie</h3>
<p class="infoPas ml-3"><i class="fa fa-info-circle"></i> Te rugam sa introduci datele <?php echo $travel_is_involved ? 'pasagerului / pasagerilor' : 'calatorilor'; ?></p>
<div id="infoPasager" class="col-12">
  <hr />
  <div id="infoPasagerForm" name="infoPasagerForm" action="#" method="post"  class="form-horizontal" onsubmit="return false;">
  </div>
  <div id="passenger-model" style="display:none;">
    <div class="row passenger-row">
      <div class="col-lg-2 col-sm-6 col-12 form-group">
        <label>Titlu</label><br />
        <select class="form-control passenger-title" required>
          <option value="">Alege</option>
          <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/passenger_title_options',array('selected'=>'mr')); ?>
          <?php themeFunctions::loadAddons(__FILE__ . '/passenger_title_options'); ?>
        </select>
      </div>
      <div class="col-12 col-sm-6 col-lg-2 form-group">
        <label>Nume</label><br />
        <input type="text" maxlength="255" class="form-control passenger-lastname" placeholder="Identic Pasaport/CI"  required  />
      </div>
      <div class="form-group col-12 col-sm-6 col-lg-2 form-group">
        <label>Prenume</label><br />
        <input type="text" maxlength="255" class="form-control passenger-firstname" placeholder="Identic Pasaport/CI"  required  />
      </div>
      <div class="col-12 col-sm-6 col-lg-2 form-group">                                                                 
        <label>Nationalitate</label><br />
        <select required="" class="form-control passenger-country" >
          <option value="">Alege</option>
          <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/passenger-country', array('selected'=>$this->_ci->user->country)); ?>
          <?php themeFunctions::loadAddons(__FILE__ . '/passenger-country'); ?>
        </select>
      </div>
      <div class="col-12 col-sm-6 col-lg-2 form-group">
        <label>Data nasterii</label><br />
        <input type="text" maxlength="10" class="form-control passenger-birth_date" placeholder="ZZ.LL.AAAA"  required  />
      </div>
      <div class="col-12 col-sm-6 col-lg-2 form-group pt-3">                                                                 
        <button type="button" class="addPasager btn btn-block btn-success  mt-3">Adauga <?php echo $travel_is_involved ? 'Pasager' : 'Calator'; ?></button>
        <button type="button" class="removePasager btn btn-block btn-danger  mt-3">Elimina <?php echo $travel_is_involved ? 'Pasager' : 'Calator'; ?></button>
      </div>
    </div>
  </div>
  
</div>
<?php themeFunctions::debugFileLine('end'); ?>