<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$coupons = $this->_ci->session->userdata('trip/checkout/coupons');
if(!$coupons){
	$coupons = array();
}
$coupon_discount = $this->_ci->session->userdata('trip/checkout/coupon_discount'); 
$coupon_discount_type = $this->_ci->session->userdata('trip/checkout/coupon_discount_type'); 
$coupon_amount_ron = $this->_ci->session->userdata('trip/checkout/coupon_amount_ron'); 
$coupon_amount_eur = $this->_ci->session->userdata('trip/checkout/coupon_amount_eur'); 
if(!$coupon_discount_type){
	$coupon_discount_type = 'P';
}
$coupon_code = $this->_ci->session->userdata('trip/checkout/coupon_code'); 
if(!strlen($coupon_code)){
  $coupon_code = '';
}
if($coupon_discount <=0){
  $coupon_discount = 0;
}
?>
<script type="text/javascript">
var applyCoupon;
;(function($){
	var coupons = <?php echo json_encode($coupons, JSON_PRETTY_PRINT); ?>;
  
  applyCoupon = function(amount, currency, $element, price){
	  if(currency == '€'){
		  currency = 'EUR';
	  }
	  $('.coupon-discount-total').remove();
    if(typeof price === 'undefined'){
      price = amount;
    }
	var discounted_price = price;
	
	var full_discount = 0;
	  for (var i = 0; i<coupons.length;i++){
		  var coupon = coupons[i];
		var discount = 0;
		  var coupon_discount_type = coupon.discount_type;
		  var coupon_discount = parseFloat(coupon.discount);
		  var coupon_amount_eur = parseFloat(coupon.amount_eur);
		  var coupon_amount_ron = parseFloat(coupon.amount_ron);
		  var coupon_code = coupon.code;
		  
		if(coupon_discount_type == 'P'){
			var coupon_title = "Cupon reducere " + coupon_discount + "%";
			var discount = (discounted_price * coupon_discount) / 100;
			
		} else if(coupon_discount_type == 'F'){
			if(currency == 'EUR'){
				discount = coupon_amount_eur;
				var coupon_title = "Cupon reducere " + format_price(coupon_amount_eur, currency);
			} else if(currency == 'RON'){
				discount = coupon_amount_ron;
				var coupon_title = "Cupon reducere " + format_price(coupon_amount_ron, currency);
			}
		}
		discounted_price -= discount;
		full_discount+= discount;
		var formatted_discount = format_price(Math.ceil(discount * 100) / 100, currency);
		var html = '<div class="rowDet coupon-discount-total">\
		  <p><strong class="remove_coupon btn btn-danger btn-xs" data-code="' + coupon_code + '"><i class="fa fa-trash"></i></strong> ' + coupon_title + ' <small>' + coupon_code + '</small> <span class="coupon-discount-item-total">-' + formatted_discount + '</span></p>\
		</div>';
		$element.before(html);
	  }
	
	if(full_discount <= 0){
		return amount;
	}
    
    // var discounted_price = amount - full_discount;
	var is_free = discounted_price <= 0;
	if(is_free){
		$('#payment_methods_nav > li').addClass('d-none');
		$('#free-checkout-tab-link').removeClass('d-none');
		$('#free-checkout-tab-link > a:first').tab('show');
		$('#payment_method_free').removeAttr('disabled');
		$('#payment_method_free').prop('checked', true);
		return 0;
	} else {
		$('#payment_methods_nav > li').removeClass('d-none');
		$('#payment_methods_nav > li > a:first').tab('show');
		$('#free-checkout-tab-link').addClass('d-none');
		$('#payment_method_free').prop('checked', false);
		$('#payment_method_free').attr('disabled','disabled');
	}
	
    return discounted_price;
  };
  $('body').on('click', '.remove_coupon', function(){
	  var $button = $(this);
	  var $wrapper = $button.closest('.coupon-discount-item-total');
	  var coupon_code = $(this).attr('data-code');
	  $.ajax({
		  url : '<?php echo site_url('trip/checkout/remove_coupon');?>',
		  data : {
			<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
            '<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>':'<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>',
            <?php } ?>
			coupon_code : coupon_code
		  },
		  method: 'POST',
		  dataType: 'json'
	  }).then(function(resp){
		  coupons = [];
		  if(resp.data && resp.data.coupons){
			coupons = resp.data.coupons;
		  }
		  calculateTotal();
	  });
  });
  function couponSubmitFormSubmitCallback($form,resp,$error_container){
    submitting_form = false;
    if(resp.status !== 'success'){
      return true;
    }
    coupons = resp.data.coupons;
    calculateTotal();
    return true;
  }
  var submitting_form = false;
  $(document).on('submit', 'form#couponForm', function(e){
    e.preventDefault();
    if(submitting_form){
      return false;
    }
	$('#coupon_phone').val($('#contact_pj_phone:visible, #contact_pf_phone:visible').first().val());
    submitting_form = true;
    basicFormPostSubmit(this,this.action,couponSubmitFormSubmitCallback,true);
  });
})(jQuery);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>