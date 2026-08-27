<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$can_write = $this->_method !='view';
if($can_write){ ?>
<script>
(function($){
  var previous_params=null;
  var previous_data=[];
  var cookie_previous_params = $.cookie('backend/packages/list/previous_params');
  var cookie_previous_data = $.cookie('backend/packages/list/previous_data');
  if(cookie_previous_params && cookie_previous_data){
    try{
      previous_params = JSON.parse(cookie_previous_params);
      previous_data = JSON.parse(cookie_previous_data);
    } catch(e) {
      previous_params = null;
      previous_data = [];
    }
  }
  var default_params=null;
  var default_data=null;
  var resetPrevious = function(){
    previous_params=default_params;
    previous_data=default_data;
  }
  $('#discount_type_id_selector').select2_4({
    theme:'bootstrap',
    placeholder:'Alege vacanta', 
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      url: '<?php echo site_url('backend/trip/packages/loadAidaPackages'); ?>',
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
          <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
          '<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': '<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>',
          <?php } ?>
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
          $.cookie('backend/packages/list/previous_params', JSON.stringify(previous_params));
        }
        if(default_data && (typeof params.data.search === 'undefined' || params.data.search === '') && params.data.search === previous_params.data.search){
          default_params = previous_params;
          if(params.data.page === 1){
            previous_data = default_data;
            $.cookie('backend/packages/list/previous_data', JSON.stringify(previous_data));
            success(default_data);
            return false;
          }
        }
        var $request = $.ajax(params);
        $request.then(function(response){
          var results = [];
          if(response.status != 'success'){
            
          }
          var packages = response.data._embedded.packages;
          var results = $.map(packages, function(group) {
            return {
              id: group.Id,
              text: group.Name
            };
          });
          success_data = {
            results: results,
            pagination: {
              more: (previous_params.data.page < response.data.page_count)
            }
          };
          if(params.data.page===1){
            previous_data = success_data;
            $.cookie('backend/packages/list/previous_data', JSON.stringify(previous_data));
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
  }).on("select2_4:opening", function() {
    if(previous_params){
      var previous_selection = previous_params.data.search;
      if(typeof previous_selection === 'string'){
        var $el = $(this);
        var $search = $el.data('select2_4').dropdown.$search || $el.data('select2_4').selection.$search;
        $search.val(previous_selection);
      }
    }
  })
  .on("select2_4:selecting", function() { 
    if(previous_params && (typeof previous_params.data.search !== 'undefined' && previous_params.data.search !== '')){
      resetPrevious();
    }
  }).on("change", function(){
    $('#discount_type_id').val(this.value);
    $('#discount_name').val($(this).select2_4('data')[0].text);
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  