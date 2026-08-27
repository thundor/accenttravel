<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$this->_ci->load->model('Options_model');
$flights_settings = $this->_ci->Options_model->get('trip_flights_settings');
$travel_prices = isset($flights_settings['travel_prices']) ? (array)$flights_settings['travel_prices'] : array();
$storno_prices = isset($flights_settings['storno_prices']) ? (array)$flights_settings['storno_prices'] : array();
$ins_travel_index = 0;
$ins_storno_index = 0;
$flights_info_settings = $this->_ci->Options_model->get('trip_flight_info',null,array(
  'status'=>0,
  // 'title'=>'',
  'description'=>'',
  // 'insurance1_title'=>'',
  'insurance1_desc'=>'',
  // 'insurance2_title'=>'',
  'insurance2_desc'=>'',
));
?>
<h3 class="subSecTicket col-12">Informatii Asigurare</h3>
<div class="insuranceTBL col-12">
  <div class="row">
    <div class="bbB col-12 col-sm-6 turist">
		<?php echo $flights_info_settings['insurance1_desc']; ?>
      <div class="pretINS firstP">
        <p><i class="fa fa-check-circle-o"></i>  Pret / pasager</p>
        <select name="insurance_travel" id="asigurareCalatorie" class="form-control">
          <?php foreach($travel_prices as $k=>$travel_price){ ?>
          <option value="<?php echo $k; ?>" <?php echo $k == $ins_travel_index ? 'selected="selected"' : ''; ?> ><?php echo htmlspecialchars($travel_price['interval'] . ': ' . $travel_price['price'] . ' €'); ?></option>
          <?php } ?>
        </select>
      </div>
      <label for="asigCal" id="asigCLB"><input type="checkbox" value="travel" id="asigCal" /> Doresc asigurare calatorie</label>
    </div>

    <div class="bbB col-12 col-sm-6 turistplus">
		<?php echo $flights_info_settings['insurance2_desc']; ?>
      <div class="pretINS secondP">

        <p><i class="fa fa-check-circle-o"></i>  Pret / pasager</p>
        <select name="insurance_storno" id="asigurareStorno" class="form-control">
          <?php foreach($storno_prices as $k=>$storno_price){ ?>
          <option value="<?php echo $k; ?>" <?php echo $k == $ins_storno_index ? 'selected="selected"' : ''; ?> ><?php echo htmlspecialchars($storno_price['interval'] . ': ' . $storno_price['price'] . ' €'); ?></option>
          <?php } ?>
        </select>
      </div>
      <label for="asigSto" id="asigSLB"><input type="checkbox" id="asigSto"  /> Doresc asigurare premium plus</label>

    </div>
    <div id="rowIns" class="col-12 mt-3 mb-4">
      <p class="alert alert-danger mb-4"><i class="fa fa-exclamation-circle red"></i> * Persoanele care calatoresc catre tara de origine nu pot beneficia de asigurarea de calatorie! * Asigurarea de calatorie are o perioada maxima de 35 de zile. In cazul in care calatoria ta depaseste 35 de zile te rugam sa iei legatura cu unul din consultantii nostri. Tarifele se majoreaza cu 50% pentru persoanele cu varsta cuprinsa intre 65 si 70 de ani, 100% pentru persoane intre 70 si 74 de ani si 200% persoane intre 75 si 79 ani.</p>
      <label for="accordIns"><input type="checkbox" name="accordIns" id="accordIns"  checked="" /> Sunt de acord cu <a target="_BLANK" href="/termeni-si-conditii">termenii si conditiile</a> companiei de asigurare</label>
    </div>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>