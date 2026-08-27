<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$product = $this->view_data['product'];
$pictures = array(
  (object)array(
    '_' => $this->theme_url . 'assets/images/placeholder.png',
    'Name' => 'Fara imagine',
  )
);
if(!empty($product->Pictures)){
  $pictures = $product->Pictures->Picture;
}
$facilities = array();
if(!empty($product->Facilities)){
  $facilities = $product->Facilities->Facility;
}
?>
<div class="row">
  <div class="col-12 col-sm-6 col-md-7 mt-4">
    <h1 class="hotelTitle"><?php echo $product->ProductName; ?></h1>
    <p><?php 
      for ($i=1; $i<=$product->ProductCategory; $i++) { ?>
      <i class="fa fa-star"></i><?php
      }
      ?> | <?php
      echo (!empty($product->Adress) ? $product->Adress . ', ' : '') . $product->CityName . ', ' . $product->CountryName; ?>
    </p>
    <div id="myCarousel" class="carousel slide hotelCarousel" data-ride="carousel">
      <ol class="carousel-indicators"><?php
        foreach($pictures as $k=>$image) { ?>
        <li data-target="#myCarousel" data-slide-to="<?php echo $k; ?>" class="<?php echo $k?'':'active'; ?>"></li><?php
        } ?>
      </ol>
      <div class="carousel-inner"><?php
        foreach($pictures as $k=>$image) { ?>
        <div class="carousel-item <?php echo $k?'':'active'; ?>">
          <div class="lazy" style="background-image:url('<?php echo $this->theme_url . 'assets/images/placeholder.png'; ?>')" data-src="<?php echo $image->_; ?>" title="<?php echo htmlspecialchars($image->Name); ?>"></div>
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
    <div class="pt-sm-5 pb-sm-3"></div>
    <?php include 'search.php'; ?>
  </div>
</div>
<?php include 'entries.php'; ?>
<div class="row">
  <div id="package_message_container" class="col-12 mt-3"></div>
  <div class="col-12 mt-1">
    <h2 class="chooseRoom" id="descriereHotel">Descriere <i class="fa fa-angle-down"></i></h2>
    <div><?php echo isset($product->Description) ? html_entity_decode(preg_replace("/[\r\n]+(<br\s*\/?>)?/",'<br />',$product->Description)) : ''; ?></div>
  </div>
  <div class="col-12 mb-3" id="iconsFacilitati">
    <h2 class="subTitleFilter" id="facilitatiHotel">Facilitati hotel &amp; camere <i class="fa fa-angle-down"></i></h2>
    <p id="facilitatiHotelDesc" class="text-hide"><?php 
      echo implode(', ', $facilities); 
    ?></p>
  </div>
</div>
<br />
<?php themeFunctions::debugFileLine('end'); ?>