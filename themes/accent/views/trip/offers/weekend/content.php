<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
themeFunctions::includeAddon('lazy-loading');
$this->_ci->load->model('Options_model');
$offers_settings = $this->_ci->Options_model->get('offers_weekend_settings');
if(!$offers_settings){
  $offers_settings = array();
}
$zone = $this->view_data['zone'];
$search = $this->view_data['search'];
$placeholder_image = $this->theme_url . 'assets/images/placeholder.png';
$time = time();
$yesterday = strtotime('yesterday', $time);
$n_friday = strtotime('next friday', $time);
$nn_friday = strtotime('next friday', $n_friday);
$nnn_friday = strtotime('next friday', $nn_friday);
$departures = array(
  date('d.m.Y', $n_friday),
  date('d.m.Y', $nn_friday),
  date('d.m.Y', $nnn_friday),
);
$departures_str = implode(' | ', $departures);
?>
<div class="tematice">
  <div class="row">
    <div class="col-sm-0 col-md-3 col-lg-4 borderTitle"></div>
    <h1 class="col-sm-12 col-md-6 col-lg-4">Alege tara si orasul/statiunea</h1>
    <div class=" col-sm-0 col-md-3 col-lg-4 borderTitle"></div>
    <form id="offers_weekend_search_form" name="offers_weekend_search_form" action="<?php echo site_url('trip/offers/weekend'); ?>" method="POST" class="container cautaInt" onsubmit="return false;">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <div class="row">
        <div class="col-12 col-sm-6 mb-5">
          <label for="hotelWeekP">Destinatia</label>
          <select name="zone" id="taraWeekP" class="form-control">
          <?php 
          $opened_optgroup = false;
          foreach($offers_settings as $item_key => $item_value){
            $text = isset($item_value['text']) && strlen(trim($item_value['text'])) ? trim($item_value['text']) : trim($item_value['name']);
            if(strpos($item_key,'_') === false){
              if($opened_optgroup){ ?>
                </optgroup><?php
              }
              $opened_optgroup = true;
              ?>
              <optgroup label="<?php echo htmlspecialchars($text); ?>">
              <?php
              continue;
            } ?>
              <option value="<?php echo htmlspecialchars($item_key); ?>" <?php echo $zone == $item_key ? 'selected' : ''; ?>><?php echo htmlspecialchars($text); ?></option>
            <?php
          }
          if($opened_optgroup){ ?>
            </optgroup><?php
          }
          ?>
          </select>                          
        </div>
        <div class="col-12 col-sm-6 mb-5">
          <label for="orasWeekP">Oras / Statiune</label><input id="orasWeekP" name="search" class="form-control" type="text" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-0 col-sm-1 col-md-3 col-lg-4"></div>
        <div class="col-12 col-sm-10 col-md-6 col-lg-4"><button type="submit" class="btn btn-block btn-lg btn-success" id="searchInt"><i class="fa fa-search"></i> Cauta</button></div>
        <div class="col-0 col-sm-1 col-md-3 col-lg-4"></div>
        <div class="col-12 mt-5 mb-5">
          <div id="result_offers_weekend_search_form" class="col-12"></div>
          <hr />
          <p>*Pe aceasta pagina vor fi afisate toate hotelurile din tara si statiunea selectata cu ofertele de pret / persoana / noapte sau camera / noapte in functie de hotel. In urma afisarii acestora aveti posibilitatea filtrarii rezultatelor. Dupa alegerea unui anumit hotel, se va efectua o interogare live a preturilor pentru a vedea daca exista modificari ale acestuia. Toate preturile afisate in paginile hotelului sunt finale, nu exista taxe sau costuri ascunse.</p>
        </div>
      </div>
    </form>
    <hr />
    <div id="offers_weekend_search_results" class="col-12" style="display:none;">
    </div>
  </div> 
</div>
<div id="offers_weekend_search_models" style="display:none;">
  <div id="offers_weekend_hotel_model" class="hoteluriWeek">
    <div class="row">
      <div class="col-sm-4 col-lg-3">
        <a href="" class="offer-weekend-link hotel-image" style="background-image:url(<?php echo $placeholder_image; ?>);padding-bottom:70%;background-position: center;background-size: cover;"></a>
      </div>
      <div class="col-sm-8 col-lg-9">
        <p class="float-right pretWeek">de la <span class="offer-weekend-price"></span></p>
        <h4 class="offer-weekend-name"></h4>
        <p class="mb-0"><span class="offer-weekend-address"></span> | <span class="offer-weekend-stars"></span>
          <br />
          <i class="fa fa-user-o"></i><i class="fa fa-user-o"></i> pret pentru doua persoane / <span class="offer-weekend-nights">2</span> nopti / camera </p>
        <p><strong>Plecari</strong><br />Vineri: <span class="offer-weekend-departures"><?php echo $departures_str; ?></span></p>
        <a href="" class="offer-weekend-link float-right btn btn btn-primary">REZERVA</a>
      </div>
    </div>
  </div>
  <div id="offers_weekend_package_model" class="hoteluriWeek">
    <div class="row">
      <div class="col-sm-4 col-lg-3">
        <a href="" class="offer-weekend-link hotel-image" style="background-image:url(<?php echo $placeholder_image; ?>);padding-bottom:70%;background-position: center;background-size: cover;"></a>
      </div>
      <div class="col-sm-8 col-lg-9">
        <p class="float-right pretWeek">de la <span class="offer-weekend-price"></span></p>
        <h4 class="offer-weekend-name"></h4>
        <p class="mb-0"><span class="offer-weekend-address"></span> <span class="offer-weekend-category"></span> | <span class="offer-weekend-stars"></span>
          <br />
          <i class="fa fa-user-o"></i><i class="fa fa-user-o"></i> pret pentru doua persoane / <span class="offer-weekend-nights">2</span> nopti </p>
        <p><strong>Plecari</strong><br />Vineri: <span class="offer-weekend-departures"><?php echo $departures_str; ?></span></p>
        <a href="" class="offer-weekend-link float-right btn btn btn-primary">REZERVA</a>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>