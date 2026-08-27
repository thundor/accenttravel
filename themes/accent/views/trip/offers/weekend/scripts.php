<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$zone = $this->view_data['zone'];
?>
<script>
;(function($){
  var $offers_weekend_search_results = $('#offers_weekend_search_results');
  var $offers_weekend_hotel_model = $('#offers_weekend_hotel_model');
  var $offers_weekend_package_model = $('#offers_weekend_package_model');
  function offersWeekendFormSubmitCallback($form,resp,$error_container){
    if(resp.status !== 'success'){
      return true;
    }
    $offers_weekend_search_results.empty();
    var offers = resp.data.offers;
    for (var i=0; i<offers.length; i++){
      var offer = offers[i];
      if(offer.type == 'hotel'){
        $offer_model = $offers_weekend_hotel_model.clone().removeAttr('id');
        $('.offer-weekend-address', $offer_model).html((offer.city_name !== null ? (offer.city_name + ', ') : '') + offer.address);
      } else {
        $offer_model = $offers_weekend_package_model.clone().removeAttr('id');
        $('.offer-weekend-address', $offer_model).html((offer.city_name  !== null ? (offer.city_name) : ''));
        $('.offer-weekend-category', $offer_model).html(offer.category);
      }
      var currency_code = offer.currency;
      var currency_symbol = '<?php echo $this->_ci->currency_symbol; ?>';
      if(currency_code === 'RON'){
        currency_symbol = 'Lei';
      } else if(currency_code === 'EUR'){
        currency_symbol = '€';
      }
      $('.offer-weekend-link', $offer_model).attr({
        'href' : offer.link
      });
      if(offer.price){
        $('.offer-weekend-price', $offer_model).html(Math.ceil(offer.price).toLocaleString('ro') + ' ' + currency_symbol);
      } else {
        $('.offer-weekend-price', $offer_model).parent().remove();
      }
      $('.offer-weekend-name', $offer_model).html(offer.name);
      $('.hotel-image', $offer_model).attr({
        'data-src' : offer.image
      }).addClass('lazy');
      $('.offer-weekend-stars', $offer_model).html(" " + Array(parseInt(offer.stars) + 1).join('<i class="fa fa-star"></i>'));
      $('.offer-weekend-stars', $offer_model).addClass('text-warning');
      
      $offer_model.appendTo($offers_weekend_search_results);
      $offers_weekend_search_results.show();
    }
    $('.lazy', $offers_weekend_search_results).lazy();
    return true;
  }
  var $offers_weekend_search_form = $('#offers_weekend_search_form');
  $offers_weekend_search_form.on('submit', function(){
    basicFormPostSubmit(this,this.action,offersWeekendFormSubmitCallback, true);
  });
  
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>