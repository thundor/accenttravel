<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$order = $this->view_data['order'];
$can_write = $this->_method !='view';
if($can_write){
themeFunctions::jsLang('warning_form_not_ready');
themeFunctions::loadModule('helpers/countries/json', __FILE__ . '/json_selections');
themeFunctions::loadModule('helpers/titles/json', __FILE__ . '/json_selections');
?>
<?php themeFunctions::loadAddons(__FILE__ . '/json_selections'); ?>
<script>
(function($){
  $('#client_invoice').on('change', function(){
    var $juridic_elements = $('#client_company_name, #client_cui, #client_iban, #client_bank, #client_regcom');
    if(this.value == 'pj'){
      $juridic_elements.prop('required', true).closest('.form-group.juridic').show('slow');
    } else {
      $juridic_elements.prop('required', false).closest('.form-group.juridic').hide('slow');
    }
  });
  $('#client_id_invoice').select2_4({theme:'bootstrap',placeholder:'Persoana',allowClear:true,minimumResultsForSearch:10, width: '100%'});
  $('#client_title').select2_4({theme:'bootstrap',placeholder:'Alegeti',minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
  $('#client_phone_prefix').select2_4({language:'ro',theme:'bootstrap',placeholder:'Alegeti', allowClear:true, data: select2_countries_prefix_selections, width: '100%'});
  $('#client_country').select2_4({language:'ro',theme:'bootstrap',placeholder:'Alege', data: select2_countries_selections, width: '100%'});
  $('#client_invoice').select2_4({language:'ro',theme:'bootstrap',placeholder:'Alege', width: '100%',minimumResultsForSearch:10});
  $('#previous_order_id').select2_4({language:'ro',theme:'bootstrap', allowClear:true,placeholder:'Alege', width: '100%',minimumResultsForSearch:10});
  
  var previous_params=null;
  var previous_data=[];
  var default_params=null;
  var default_data=null;
  var resetPrevious = function(){
    previous_params=default_params;
    previous_data=default_data;
  }
  $('#client_id').select2_4({
    theme:'bootstrap',
    placeholder:'Alege', 
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      url: '<?php echo site_url('backend/accounts/customer/getlist'); ?>',
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
            '`user_id` AS "id"',
            'CONCAT_WS(", ",`user_lastname`, `user_firstname`,`user_email`,`phone`) AS "text"',
            'IF(`user_status`<>1,true,false) AS "disabled"',
          ],
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
          if(response.status != 'success'){
            
          }
          var accounts = response.data.accounts;
          var results = $.map(response.data.accounts, function(group) {
            return {
              id: group.id,
              text: group.text,
              disabled: parseInt(group.disabled) ? true : false,
              children: typeof group.children === 'undefined' ? group.children :  $.map(group.children, function(item){
                return {
                  id: item.id,
                  text: item.text,
                  group: item.group,
                  disabled: parseInt(item.disabled) ? true : false
                }
              })
            };
          });
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
  });
  var reference_moment = moment().startOf('day');
  var min_adult_moment = moment([parseInt(reference_moment.format('Y')) - 150]).startOf('day');
  var max_adult_moment = moment([parseInt(reference_moment.format('Y')) - 18, parseInt(reference_moment.format('M'))-1, parseInt(reference_moment.format('D'))]).startOf('day');
    
  $('#client_birth_date').makeCaleranDatepicker({
    startEmpty: true,
    minDate: min_adult_moment,
    maxDate: max_adult_moment,
    startDate: max_adult_moment
  }).makeInputmaskDate();
  $('#updateUserInfo').on('click', function(){
    var $field = $('#client_id');
    removeFieldMessages($field);
    var user_id = parseInt($field.val());
    var custom_invoice_selection = $('#client_id_invoice').val();
    var selections = [
      '`invoice` AS "user_invoice"',
      'IF(`invoice`="pj",`pj_company_name`,"") AS "user_company_name"',
      'IF(`invoice`="pj",`pj_cui`,"") AS "user_cui"',
      'IF(`invoice`="pj",`pj_iban`,"") AS "user_iban"',
      'IF(`invoice`="pj",`pj_bank`,"") AS "user_bank"',
      'IF(`invoice`="pj",`pj_regcom`,"") AS "user_regcom"',
      '`birth_date` AS "user_birth_date"',
      '`title` AS "user_title"',
      'IFNULL(IF(`invoice`="pj",`pj_lastname`,`pf_lastname`),`user_lastname`) AS "user_lastname"',
      'IFNULL(IF(`invoice`="pj",`pj_firstname`,`pf_firstname`),`user_firstname`) AS "user_firstname"',
      'IFNULL(IF(`invoice`="pj",`pj_country`,`pf_country`),`country`) AS "user_country"',
      'IFNULL(IF(`invoice`="pj",`pj_city`,`pf_city`),`city`) AS "user_city"',
      'IF(`invoice`="pj",`pj_address`,`pf_address`) AS "user_address"',
      'IF(`invoice`="pj",`pj_street`,`pf_street`) AS "user_street"',
      'IF(`invoice`="pj",`pj_street_no`,`pf_street_no`) AS "user_street_no"',
      'IF(`invoice`="pj",`pj_postal_code`,`pf_postal_code`) AS "user_postal_code"',
      'IFNULL(IF(`invoice`="pj",`pj_phone_prefix`,`pf_phone_prefix`),`phone_prefix`) AS "user_phone_prefix"',
      'IFNULL(IF(`invoice`="pj",`pj_phone`,`pf_phone`),`phone`) AS "user_phone"',
      'IFNULL(IF(`invoice`="pj",`pj_email`,`pf_email`),`user_email`) AS "user_email"'
    ];
    if(custom_invoice_selection == 'pf'){
      selections = [
        '"pf" AS "user_invoice"',
        '"" AS "user_company_name"',
        '"" AS "user_cui"',
        '"" AS "user_iban"',
        '"" AS "user_bank"',
        '"" AS "user_regcom"',
        '`birth_date` AS "user_birth_date"',
        '`title` AS "user_title"',
        'IFNULL(`pf_lastname`,`user_lastname`) AS "user_lastname"',
        'IFNULL(`pf_firstname`,`user_firstname`) AS "user_firstname"',
        'IFNULL(`pf_country`,`country`) AS "user_country"',
        'IFNULL(`pf_city`,`city`) AS "user_city"',
        '`pf_address` AS "user_address"',
        '`pf_street` AS "user_street"',
        '`pf_street_no` AS "user_street_no"',
        '`pf_postal_code` AS "user_postal_code"',
        'IFNULL(`pf_phone_prefix`,`phone_prefix`) AS "user_phone_prefix"',
        'IFNULL(`pf_phone`,`phone`) AS "user_phone"',
        'IFNULL(`pf_email`,`user_email`) AS "user_email"'
      ];
      
    } else if(custom_invoice_selection == 'pj'){
      selections = [
        '"pj" AS "user_invoice"',
        '`pj_company_name` AS "user_company_name"',
        '`pj_cui` AS "user_cui"',
        '`pj_iban` AS "user_iban"',
        '`pj_bank` AS "user_bank"',
        '`pj_regcom` AS "user_regcom"',
        '`birth_date` AS "user_birth_date"',
        '`title` AS "user_title"',
        'IFNULL(`pj_lastname`,`user_lastname`) AS "user_lastname"',
        'IFNULL(`pj_firstname`,`user_firstname`) AS "user_firstname"',
        'IFNULL(`pj_country`,`country`) AS "user_country"',
        'IFNULL(`pj_city`,`city`) AS "user_city"',
        '`pj_address` AS "user_address"',
        '`pj_street` AS "user_street"',
        '`pj_street_no` AS "user_street_no"',
        '`pj_postal_code` AS "user_postal_code"',
        'IFNULL(`pj_phone_prefix`,`phone_prefix`) AS "user_phone_prefix"',
        'IFNULL(`pj_phone`,`phone`) AS "user_phone"',
        'IFNULL(`pj_email`,`user_email`) AS "user_email"'
      ];
    }
    if(user_id > 0){
      var form_data = {
        id: user_id,
        <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
        '<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': '<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>',
        <?php } ?>
        select: selections
      };
      $.ajax({
        url: "<?php echo site_url('backend/accounts/customer/getinfo'); ?>",
        method: 'POST',
        dataType: 'json',
        data: form_data
      }).done(function(response){
        if(response.status !== 'success' || !response.data || !response.data.account){
          fieldMessage($field,'Eroare intalnita la preluare informatiilor','warning');
        } else {
          fieldMessage($field,'Informatiile au fost preluate','success');
          for(var field_name in response.data.account){
            if(!response.data.account.hasOwnProperty(field_name)) {
              continue;
            }
            var value = response.data.account[field_name];
            if(field_name=='user_birth_date' && value){
              value = moment(value,'Y-MM-DD').format('DD.MM.Y');
            }
            var form_field = $field[0].form[field_name];
            var $form_field = $(form_field);
            $form_field.val(value);
            if($form_field.data('select2_4')){
              $form_field.trigger('change.select2_4');
            }
          }
        }
        $('#client_invoice').trigger('change');
      }).fail(function(){
        fieldMessage($field,'Eroare intalnita la preluare informatiilor','warning');
      });
    } else {
      fieldMessage($field,'Nu ati ales niciun utilizator','warning');
    }
  });
  function clientFormSubmitCallback($form,resp,$error_container){
    console.log(resp);
    if(resp.status !== 'success'){
      return true;
    }
    <?php if($order->id) { ?>
    $form[0].submit();
    <?php } else { ?>
    showMessage($error_container,'Informatiile despre client au fost salvate in comanda','success');
    <?php } ?>
  }
  $('#clientForm').on('submit',function(){
    basicFormPostSubmit(this,this.action,clientFormSubmitCallback);
  });
})(jQuery);
</script>
<?php
}
?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  