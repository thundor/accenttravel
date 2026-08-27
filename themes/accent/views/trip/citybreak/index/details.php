<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$data = &$this->citybreak_search_data;
// echo '<pre>';
// print_R($data);
// die;
?>
<div class="row">
  <div class="col-12 col-sm-6 col-md-7 mt-4">
    <h1 class="hotelTitle"><?php echo $hotel_details->Type . ' ' . $hotel_details->Name; ?></h1>
    <p><?php 
      for ($i=1; $i<=$hotel_details->Stars; $i++) { ?>
      <i class="fa fa-star"></i><?php
      }
      ?> | <?php
      echo $hotel_details->Address . ', ' . $hotel_details->CityName . ', ' . $hotel_details->CountryName; ?>
    </p>
    <div id="myCarousel" class="carousel slide hotelCarousel" data-ride="carousel">
      <ol class="carousel-indicators"><?php
        foreach($hotel_details->Gallery as $k=>$image) { ?>
        <li data-target="#myCarousel" data-slide-to="<?php echo $k; ?>" class="<?php echo $k?'':'active'; ?>"></li><?php
        } ?>
      </ol>
      <div class="carousel-inner"><?php
        foreach($hotel_details->Gallery as $k=>$image) { ?>
        <div class="carousel-item <?php echo $k?'':'active'; ?>">
          <div class="lazy" style="background-image:url('<?php echo htmlspecialchars($this->theme_url . 'assets/images/placeholder.png'); ?>')" data-src="<?php echo htmlspecialchars($image); ?>"></div>
        </div><?php
        } ?>
      </div>
      <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
        <span class="carousel-control-icon carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
        <span class="carousel-control-icon carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-md-5 mt-4">
    <div class="row">
      <div class="col-sm-12 col-md-6">
        <?php /* 
        <h3 class="colBlueDark familyBold"><i class="fa fa-smile-o"></i> Foarte bun!</h3>
        <h5><strong>92%</strong> dintre vizitatori recomanda <br /><strong>3.456</strong> recenzii </h5>
        <hr />
        <h4 class="colBlueDark">Scor: <strong>4.3</strong> din 5</h4>
        <hr />
        */ ?>
        <p class="pretHotPag pt-sm-4 text-right" style="visibility:hidden;">
          <strong></strong>
          <span data-toggle="tooltip" class="pretFullHotPag"  title="Devino membru si beneficiaza de reduceri permanente! Inregistreaza-te acum!"></span>
          <button type="submit" form="Package-1" class="btn btn-success">REZERVA</button>
        </p>
      </div>
      <div class="col-12">
        <div id="googleMap" class="col-12"  style="height:200px;"></div>
      </div>
    </div>   
    <hr />
    <?php include 'search.php'; ?>
    </div>
</div>
<div class="row packages-title" style="display:none;">
  <h2 class="col-12 col-sm-4">Oferte disponibile <span class="room-number"></span> <i class="fa fa-angle-down"></i></h2>
</div>
<div id="package_selector_wrapper" class="pt-3" style="display:none;">
  <div class="input-group">
    <span class="input-group-addon">
      Alege alta oferta
    </span>
    <select id="package_selector" class="form-control"><option>Se incarca...</option></select>
  </div>
</div>
<div id="hotel-packages">
</div>
<form id="package-model" class="hotel-package form-validate card" action="<?php echo site_url('trip/citybreak/booking/' . $data['hotel_id']); ?>" method="POST" style="display:none;">
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <input name="package_start_date" value="" type="hidden" />
  <input name="package_end_date" value="" type="hidden" />
  <input name="package_code" value="" type="hidden" />
  <input name="package_name" value="" type="hidden" />
  <div class="chooseHead card-header">
    <h2 class="col-12 col-sm-4">Oferta #<span class="package-number"></span> <i class="fa fa-angle-down"></i></h2>
  </div>
  <div class="room-options card-block">
    <div class="room-option">
      <div class="row chooseHead">
        <div class="col-12 col-sm-8">
          <h2><span class="choose-room-name">Optiuni camera <span class="room-number"></span></span> <i class="fa fa-angle-down"></i></h2>
        </div>
        <?php /* <div class="col-12 col-sm-4 text-right"><a href="#" role="button" class="addFav">Adauga la Favorite <i class="fa fa-heart-o"></i></a></div> */ ?>
      </div>
      <div class="package-rooms">
        <div class="row roomShow">
          <div class="thUp col-12 col-sm-6 col-lg-3 pr-0 pl-0">
            <h3>Nume</h3>
            <p>
              <strong></strong><br />
              <small></small>
            </p>
          </div>
          <?php /*
          <div class="thUp col-12 col-sm-6 col-lg-2 pl-0 pr-0">
            <h3>Puncte fidelitate</h3>
            <p></p>
          </div>
          */ ?>
          <div class="thUp col-12 col-sm-6 col-lg-2 pl-0 pr-0">
            <h3>Disponibilitate</h3>
            <p></p>
          </div>
          <div class="thUp col-12 col-sm-6 col-lg-3 pl-0 pr-0">
            <h3>Informatii</h3>
            <p></p>
          </div>
          <div class="thUp col-12 col-sm-6 col-lg-2 pl-0 pr-0">
            <h3 class="text-center">Pret</h3>
            <p class="text-center"></p>
          </div>
          <div class="thUp col-12 col-sm-12 col-lg-2 pr-0 pl-0">
            <h3>&nbsp;</h3>
            <p><input type="radio" name="p"/> <label class="btn btn-success"> Alege</label></p>
            <p>
              <button type="submit" role="button" class="btn btn-success rounded-0" onclick="jQuery('input[type=radio]', jQuery(this).parent().parent()).prop('checked',true); return true;">Rezerva acum</button>
              <br />
              Dureaza maxim 2 minute
              <br>
              <i class="fa fa-clock-o"></i>
            </p>
          </div>
        </div>
        <input type="hidden" name="adt" value="">
        <input type="hidden" name="chdages" value="">
      </div>
    </div>
  </div>
  <div class="card-footer">
    <div class="row">
      <div class="thUp col-12 col-sm-12 col-lg-10 pr-0 pl-0 text-center text-lg-right">
        <h2>Total <strong class="total-price"></strong></h2>
      </div>
      <div class="thUp col-12 col-sm-12 col-lg-2 pr-0 pl-0 text-center">
        <p class="reserve-total">
          <button type="submit" role="button" class="btn btn-success rounded-0">Rezerva acum</button>
          <br />
          Dureaza maxim 2 minute
          <br>
          <i class="fa fa-clock-o"></i>
        </p>
      </div>
    </div>
  </div>
</form>
<div class="row" id="request_offer_wrapper" style="display:none;">
  <div class="col-12 mt-2 mb-2 text-center">
    <?php themeFunctions::loadModule('trip/request_offer',__FILE__ . 'end'); ?>
    <?php themeFunctions::loadAddons(__FILE__ . 'end'); ?>
  </div>
</div>
<div class="row mt-4">
  <div class="col-12">
    <h2 class="chooseRoom" id="descriereHotel">Descriere Hotel <?php echo $hotel_details->Name . ' ' . $hotel_details->CityName . ' ' . $hotel_details->CountryName; ?> <i class="fa fa-angle-down"></i></h2>
    <p><?php echo $hotel_details->ShortDesc; ?></p> 
    <div>
      <p>Hotel <?php echo $hotel_details->Name . ' ' . $hotel_details->CityName . ' ' . $hotel_details->CountryName; ?></p>
      <?php if($hotel_details->Address) : ?>
      <p><?php echo $hotel_details->Address; ?></p>
      <?php endif; ?>
    </div>
    <div>
      <?php /* if($hotel_details->Phone) : ?>
      <p><a href="tel:<?php echo $hotel_details->Phone; ?>"><i class="fa fa-phone"></i> Telefon: <?php echo $hotel_details->Phone; ?></a></p>
      <?php endif; ?>
      <?php if($hotel_details->Fax) : ?>
      <p><a href="fax:<?php echo $hotel_details->Fax; ?>"><i class="fa fa-fax"></i> Fax: <?php echo $hotel_details->Fax; ?></a></p>
      <?php endif; ?>
      <?php if($hotel_details->Email) : ?>
      <p><a href="mailto:<?php echo $hotel_details->Email; ?>"><i class="fa fa-envelope"></i> E-Mail: <?php echo $hotel_details->Email; ?></a></p>
      <?php endif; */ ?>
    </div>
  </div>
  <div class="col-12 mb-3" id="iconsFacilitati">
    <h2 class="subTitleFilter" id="facilitatiHotel">Facilitati hotel &amp; camere <i class="fa fa-angle-down"></i></h2>
    <p id="facilitatiHotelDesc" class="text-hide"><?php 
      // echo str_replace(',', ', ',$hotel_details->FacilitiesDesc); 
      echo empty($hotel_details->FacilitiesDetail) ? '' : implode(', ', $hotel_details->FacilitiesDetail); 
    ?></p>
  </div>
  <?php /* <div class="col-12 col-sm-6 col-lg-5">
    <h2 class="chooseRoom" id="facilitatiCamere">Facilitati camere <i class="fa fa-angle-down"></i></h2>
    <p id="facilitatiCamereDesc">Air Conditioning, TV, Heating, </p>
  </div> */ ?>
</div>
<?php themeFunctions::loadModule('citybreaks/flight_details',__FILE__ . '/flight_details'); ?>
<?php themeFunctions::loadAddons(__FILE__ . '/flight_details'); ?>
<?php themeFunctions::debugFileLine('end'); ?>