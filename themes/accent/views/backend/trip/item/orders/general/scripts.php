<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if($can_write){ ?>
<script>
function format_price(amount, currency) {
	var symbol = currency;
	var amount_float = parseFloat(amount);
	if (isNaN(amount_float)) {
		return '-';
	} else {
		var amount_formatted = amount_float.toLocaleString('ro', {
			minimumFractionDigits: 2
		});
	}
	if (currency === 'RON') {
		symbol = 'Lei';
	} else if (currency === 'EUR') {
		symbol = '€';
	}
	return amount_formatted + ' ' + symbol;
}
(function($){
  function generalFormSubmitCallback($form,resp,$error_container){
    console.log(resp);
    if(resp.status !== 'success'){
      return true;
    }
    if(typeof resp.data.results.coupon_code !== 'undefined'){
      var coupon_text = resp.data.results.coupon_code === null ? '-niciun cupon aplicat-' : (resp.data.results.coupon_code + ' (' + resp.data.results.coupon_percentage + '%)');
      $('#order_coupon_current').text(coupon_text);
    }
    return true;
  }
  $('#generalForm').on('submit',function(){
    basicFormPostSubmit(this,this.action,generalFormSubmitCallback);
  });
  // $('#order_coupon_apply').on('change', function(){
    // $('#order_coupon_code').prop('disabled', !$(this).is(':checked'));
  // });
  var previous_params=null;
  var previous_data=[];
  var default_params=null;
  var default_data=null;
  var resetPrevious = function(){
    previous_params=default_params;
    previous_data=default_data;
  }
  $('body').on('click','.remove-coupon', function(){
	$(this).closest('.input-group').remove();  
  });
  $('#order_coupon_code').select2_4({
    theme:'bootstrap',
    placeholder:'Alege cuponul', 
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:0,
    ajax: {
      url: '<?php echo site_url('backend/trip/coupons/getlist'); ?>',
      dataType: 'json',
      type: 'POST',
      delay: 400,
      data: function (params) {
        var limit = 10;
        var page = params.page > 1 ? params.page : 1;
        var term = params.term;
        if (typeof params.term === 'undefined'){
          term = "";
          if(previous_params && previous_params.data){
            if (typeof previous_params.data.search === 'string'){
              term = previous_params.data.search;
            }
          }
        }
        var form_data = {
          search: term,
          page: page,
          simple: true,
          <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
          '<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': '<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>',
          <?php } ?>
          select: [
            'pc.*',
            'c.code',
            'IF(c.`status`<>1 or pc.status<>1 or (pc.max_uses IS NOT NULL AND c.nr_uses >= pc.max_uses) or (pc.date_start IS NOT NULL AND pc.date_start > NOW()) or (pc.date_expire IS NOT NULL AND pc.date_expire < NOW()),true,false) AS "disabled"',
          ],
		  ordering: 'disabled DESC, code ASC',
		  join_child: 1,
          limit: limit
        };
        return form_data;
      },
      transport: function (params, success, failure) {
        if(typeof params.data.search === 'undefined'){
          if(previous_params){
            params.data.search = previous_params.data.search;
          }
        }
        if(previous_params && params.data.search === previous_params.data.search && params.data.page === 1){
          success(previous_data);
          return false;
        } else {
          previous_params = params;
        }
        if(default_data && (typeof params.data.search === 'undefined' || params.data.search === '') && params.data.search === previous_params.data.search){
          default_params = previous_params;
          if(params.data.page === 1){
            previous_data = default_data;
            success(default_data);
            return false;
          }
        }
        var $request = $.ajax(params);
        $request.then(function(response){
          var results = [];
          if(response.status == 'success'){
            var coupons = response.data.coupons;
            var results = $.map(response.data.coupons, function(group) {
              return {
                id: group.code,
                text: group.code + ' [' + (group.discount_type == 'P' ? (group.percentage + '%') : (group.fixed_ron + ' Lei / ' + group.fixed_eur + ' EUR')) + ']',
                code: group.code,
                percentage: group.percentage,
                type: group.type,
                discount_type: group.discount_type,
                fixed_ron: group.fixed_ron,
                fixed_eur: group.fixed_eur,
                disabled: parseInt(group.disabled) ? true : false
              };
            });
          }
          success_data = {
            results: results,
            pagination: {
              more: (previous_params.data.page < response.data.max_pages)
            }
          };
          if(params.data.page===1){
            previous_data = success_data;
            if(typeof params.data.search === 'undefined' || params.data.search === ''){
              default_data = previous_data;
            }
          }
          success(success_data);
        });
        $request.fail(function(){
          resetPrevious();
          failure();
        });
        return $request;
      },
      processResults: function (data, params) {
        return data;
      }
    }
  }).on('change', function(){
	  var $selector = $(this);
	  if('' !== $.trim($selector.val())){
		var $select2_data = $selector.data('select2_4');
		if($select2_data){
			var $select2_selected_data = $select2_data.data();
			if($select2_selected_data && $select2_selected_data.length){
				var coupon = $select2_selected_data.shift();
				var html = '<div class="input-group">' +
					'<input type="text" value="' + coupon.code + '" name="coupon_codes[]" class="form-control" />'+
					'<span class="input-group-addon">' +
						(coupon.discount_type == 'P' ? (coupon.percentage + '%') : (coupon.fixed_ron + ' Lei / ' + coupon.fixed_eur + ' EUR')) +
					'</span>' +
					'<div class="input-group-btn">' +
						'<button type="button" class="btn btn-danger remove-coupon"><i class="fa fa-trash"></i></button>' +
					'</div>' +
				'</div>';
				
				$('#coupons-wrapper').append(html);
				$selector.val(null).change();
			}
		}
	  }
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  