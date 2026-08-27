<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="package_models" style="display:none;">
  <form id="package_entry_model" class="package-entry mt-1" action="<?php echo site_url('trip/package/booking'); ?>" method="POST" onsubmit="return false;">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <input type="hidden" name="package_id" />
    <input type="hidden" name="code" />
    <input type="hidden" name="entry_id" />
    <input type="hidden" name="rate_group_id" />
    <div class="hidden-inputs" style="display:none;">
    </div>
    <div class="package-entry-rooms">
    </div>
    <div class="row package-reservation">
      <div class="col-12 pr-0 pl-0 pt-3 pb-3 text-center">
        <p><h5>Pret total: <strong class="total-package-price"></strong> <button type="submit" name="task" disabled value="price_update" role="button" class="btn btn-primary rounded"><i class="fa fa-reload"></i> Actualizare pret</button></h5></p>
        <hr />
        <p class="booking_button_wrapper" style="display:none;">
          <button type="submit" name="task" value="submit" role="button" class="btn btn-success rounded">Rezerva acum</button><br/>
          Dureaza maxim 2 minute<br>
          <i class="fa fa-clock-o"></i>
        </p> 
      </div>
    </div>
  </form>
  <div id="package_entry_room_model" class="package-entry-room">
    <div class="row chooseHead">
      <div class="col-12">
        <h2>Detalii <span class="room-number"></span> <i class="fa fa-angle-down"></i></h2>
        <p><i class="fa fa-info-circle"></i> <em>Serviciile suplimentare se bifeaza pentru fiecare turist (adult sau copil)</em></p>
      </div>
      <?php /* <p class="col-12 col-sm-4 text-center"><!--Acest hotel a fost ales de 34 de turisti din Romania--></p> */ ?>
      <?php /* <div class="col-12 col-sm-4 text-right"><a href="#" role="button" class="addFav">Adauga la Favorite <i class="fa fa-heart-o"></i></a></div> */ ?>
    </div>
    <div class="row roomShow">
      <div class="thUp col-12 col-sm-6 col-lg-4 pr-0 pl-0">
        <h3>Tip camera</h3>
        <p class="package-room-package-service-room-name pl-0 pt-2 pb-1">
          <select class="form-control package-entry-room-option"></select>
        </p>
      </div>
      <div class="thUp col-12 col-sm-6 col-lg-2 pl-0 pr-0">
       <h3>Numar turisti</h3>
       <p>
         <span class="package-room-occupancy">
          <span class="package-room-occupancy-adults">
            <span class="package-room-occupancy-adults-number"></span> <span class="plural">Adulti</span><span class="singular">Adult</span>
          </span>
          <span class="package-room-occupancy-children">
            <span class="package-room-occupancy-children-number"></span> <span class="plural">Copii</span><span class="singular">Copil</span><span class="child-ages"></span>
          </span>
        </span>
      </p>
      </div>
      <div class="thUp col-12 col-sm-4 col-lg-2 pl-0 pr-0">
       <h3 class="text-center">Disponibilitate</h3>
       <p class="text-center"><span class="package-room-availability"></span></p>
      </div>
      <div class="thUp col-12 col-sm-4 col-lg-2 pl-0 pr-0">
       <h3 class="text-center">Perioada selectata</h3>
       <p class="text-center"><span class="package-room-entry-interval"></span></p>
      </div>
      <div class="thUp col-12 col-sm-4 col-lg-2 pl-0 pr-0">
       <h3 class="text-center">Pret / Vacanta</h3>
       <p class="text-center familyBold"><span class="package-room-price"></span></p>
      </div>
      <div class="thUp col-12 col-sm-6 col-lg-2 pl-0 pr-0" style="display:none;"></div>
    </div>
    <div class="row people package-entry-extra">
    </div>
  </div>
  <div id="package_entry_accommodation_room_package_occupancy_model" class="package-entry-accomodation-room-package-occupancy mt-1 col-12">
    <div class="pl-0 pr-0">
      <h5 class="blue familyBold p-3 peoplePack">
      <span class="package-room-occupancy-adult">Adult</span>
      <span class="package-room-occupancy-child for-children">Copil</span>
      <span class="type-index-wrapper"># <span class="type-index"></span></span>
      <small class="child-age for-children"></small>
      <em class="small familyLight">(servicii extra disponibile)</em></h5> 
    </div>
    <div class="package-entry-accommodation-package-extra-services ml-0 mr-0 pt-0 pl-1 pr-1 pb-1 row">
    </div>
  </div>
  <div id="package_extra_service_model" class="col-12">
    <p class="subTitleFilterXS"><label data-toggle="tooltip" title="Dupa ce bifezi, actualizeaza pretul apasand pe butonul Actualizare pret de mai jos"><input type="checkbox" name="option" class="float-left ml-1 mr-2 borderedCH" value="1"> <span class="extra-service-name"></span></label></p><br>
    <p class="extra-service-description"></p>
  </div>
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>