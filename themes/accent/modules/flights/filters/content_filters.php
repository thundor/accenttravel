<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="allFiltersT" class="hiddenFiltT rounded mt-2">
  <div class="row">
    <div class="col-12 col-sm-6">
      <h5 class="subTitleFilter"> Tarif</h5>
      <input type="text" id="amount" class="border-0 mb-1" readonly><div id="slider-range"></div>
      <h5 class="subTitleFilter mt-3">Numar escale</h5>
      <div class="flights-filter flights-filter-stops">
      <?php /*
        <div class="checkWrapper">
          <input type="checkbox" id="zborDirect">
          <label for="zborDirect">Zbor Direct</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="oEscala">
          <label for="oEscala"> 1 Escala</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="douaEscale">
          <label for="douaEscale">2 Escale</label>
        </div>
        */ ?>
      </div>
      <?php /*<h5 class="subTitleFilter mt-3"> Clasa de Zbor</h5>
      <div class="flights-filter flights-filter-class">  
        <div class="checkWrapper">
          <input type="checkbox" id="eco">
          <label for="eco">Economic</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="business">
          <label for="business"> Business</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="first">
          <label for="first">First</label>
        </div>
      </div>
      */ ?>
    </div>
    <div class="col-12 col-sm-6">
      <h5 class="subTitleFilter">Companii aeriene</h5>
      <div class="flights-filter flights-filter-companies">
      <?php /*
        <div class="checkWrapper">
          <input type="checkbox" id="airfrance" name="airfrance" />
          <label for="airfrance"><img src="<?php echo $this->theme_url; ?>assets/images/icons/airfrance-icon.jpg" />Air France</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="alitalia" name="alitalia" />
          <label for="alitalia"><img src="<?php echo $this->theme_url; ?>assets/images/icons/alitalia-icon.jpg" />Alitalia</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="american" name="american" />
          <label for="american"><img src="<?php echo $this->theme_url; ?>assets/images/icons/american-icon.jpg" />American Airlines</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="austrian" name="austrian" />
          <label for="austrian"><img src="<?php echo $this->theme_url; ?>assets/images/icons/austrian-icon.jpg" />Austrian Airlines</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="blueair" name="blueair" />
          <label for="blueair"><img src="<?php echo $this->theme_url; ?>assets/images/icons/blueair-icon.jpg" />Blue Air</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="british" name="british" />
          <label for="british"><img src="<?php echo $this->theme_url; ?>assets/images/icons/british-icon.jpg" />British Airways</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="brussels" name="brussels" />
          <label for="brussels"><img src="<?php echo $this->theme_url; ?>assets/images/icons/brussels-icon.jpg" />Brussels Airways</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="klm" name="klm" />
          <label for="klm"><img src="<?php echo $this->theme_url; ?>assets/images/icons/klm-icon.jpg" />KLM</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="lufthansa" name="lufthansa" />
          <label for="lufthansa"><img src="<?php echo $this->theme_url; ?>assets/images/icons/lufth-icon.jpg" />Lufthansa</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="swiss" name="swiss" />
          <label for="swiss"><img src="<?php echo $this->theme_url; ?>assets/images/icons/swiss-icon.jpg" /> TAROM</label>
        </div>
        <div class="checkWrapper">
          <input type="checkbox" id="tarom" name="tarom" />
          <label for="tarom"><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" /> TAROM</label>
        </div>
        */ ?>
      </div>
    </div>
    <div class="col-1 col-sm-2 col-md-4"></div>
    <div class="col-10 col-sm-6 col-md-4">
      <button name="applyFilters" id="applyFilters" class="btn btn-block btn-primary" style="display:none;" type="submit">Aplica Filtre</button>
      <button name="resetFilters" id="resetFilters" class="btn btn-block btn-warning" type="submit">Sterge Filtre</button>
    </div>
    <div class="col-1 col-sm-2 col-md-4"></div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>