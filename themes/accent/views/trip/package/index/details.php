<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$data = &$this->package_search_data;
$package_details = $this->view_data['package_details'];
$extra_description = !empty($package_details->ExtraDescriptions) ? $package_details->ExtraDescriptions[0] : null;
?>
<div class="row">
  <div class="col-12 col-sm-6 col-md-7 mt-4">
    <h1 class="hotelTitle"><?php echo lang($package_details->Type) . ' ' . $package_details->Name; ?></h1>
    <p><span id="package_hotel_stars"><i class="fa fa-spinner fa-spin fa-pulse"></i></span> | <span id="package_hotel_address"><i class="fa fa-spinner fa-spin fa-pulse"></i></span></p>
    <p><?php echo $package_details->ProjectName; ?></p>
    <div id="myCarousel" class="carousel slide hotelCarousel" data-ride="carousel">
      <ol class="carousel-indicators"><?php
        foreach($package_details->Gallery as $k=>$image) { ?>
        <li data-target="#myCarousel" data-slide-to="<?php echo $k; ?>" class="<?php echo $k?'':'active'; ?>"></li><?php
        } ?>
      </ol>
      <div class="carousel-inner"><?php
        foreach($package_details->Gallery as $k=>$image) { ?>
        <div class="carousel-item <?php echo $k?'':'active'; ?>">
          <div class="lazy" style="background-image:url('<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>')" data-src="<?php echo $image; ?>"></div>
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
  <div class="col-12 col-sm-6 col-md-5 mt-4 pt-sm-5">
    <?php /*
    <div class="row">
      <div class="col-sm-12">
        <h3 class="colBlueDark familyBold"><i class="fa fa-smile-o"></i> Foarte bun!</h3>
        <h5><strong>92%</strong> dintre vizitatori recomanda <br /><strong>3.456</strong> recenzii </h5>
        <hr />
        <h4 class="colBlueDark">Scor: <strong>4.3</strong> din 5</h4>
        <hr />
        <p class="pretHotPag" style="display:none;">
          <strong></strong>
          <span data-toggle="tooltip" class="pretFullHotPag"  title="Devino membru si beneficiaza de reduceri permanente! Inregistreaza-te acum!"></span>
          <button type="submit" form="Package-1" class="btn btn-success">REZERVA</button>
        </p>
      </div>
    </div> 
    */ ?>
    <div class="pt-sm-5 pb-sm-3"></div>
    <?php include 'search.php'; ?>
    <hr />
    <h4 class="btn-primary p-3">Schimba perioada</h4>
    <select id="package_period_select" class="form-control"></select>
  </div>
</div>
<?php if(!empty($extra_description)){ ?>
<h2 class="chooseRoom mt-2" id="descrierePachet">Descriere <i class="fa fa-angle-down"></i></h2>
<p><?php echo nl2br($extra_description->Description); ?></p>
<?php } ?>
<?php include 'entries.php'; ?>
<div class="row">
  <div id="package_message_container" class="col-12 mt-3"></div>
  <div class="col-12 mt-2 mb-2 text-center">
    <?php themeFunctions::loadModule('trip/request_offer',__FILE__ . 'end'); ?>
    <?php themeFunctions::loadAddons(__FILE__ . 'end'); ?>
  </div>
  <div class="col-12 mt-1">
    <h2 class="chooseRoom" id="descriereHotel">Descriere Hotel <?php echo lang($package_details->Type) . ' ' . $package_details->Name; ?> <i class="fa fa-angle-down"></i></h2>
    <p><?php echo isset($package_details->Description) ? nl2br($package_details->Description) : ''; ?></p>
  </div>
</div>
<?php include 'models.php'; ?>
<br />
<?php themeFunctions::debugFileLine('end'); ?>