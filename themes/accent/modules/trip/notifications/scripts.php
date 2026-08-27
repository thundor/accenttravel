<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
?>
<div class="modal fade" id="modal_alerta_pret" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="d-flex flex-column justify-content-center" style="height:100%;pointer-events:none;">
    <div class="modal-dialog modal-lg" role="document" style="max-height: 90%;pointer-events:auto;">
      <div class="modal-content">
        <div class="modal-header pt-2 pl-3 pb-2 pr-3">
          <h4 class="mb-0 mr-3">Alerta pret <small id="alerta_pret_title" style="font-weight:bold;"></small></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Inchide">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-2 pr-3 pl-3 pb-0">
          <form id="notificationForm" name="notificationForm" action="<?php echo site_url('trip/notifications/add');?>" class="notification_form" method="POST">
            <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
            <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
            <?php } ?>
            <input type="hidden" name="currency" value=""/>
            <input type="hidden" name="amount" value=""/>
            <input type="hidden" name="amount_hotel" value=""/>
            <input type="hidden" name="amount_flight" value=""/>
            <input type="hidden" name="ref_id" value=""/>
            <input type="hidden" name="itinerary_code" value=""/>
            <input type="hidden" name="type" value=""/>
            <input type="hidden" name="title" value=""/>
            <input type="hidden" name="data" value=""/>
            <div class="form-group row">
              <label for="notification_fullname" class="<?php echo $label_class; ?>">Numele dvs.</label>
              <div class="<?php echo $value_class; ?>">
                <input id="notification_fullname" required type="text" maxlength="255" name="fullname" placeholder="" class="form-control" value="<?php echo htmlspecialchars(trim($this->_ci->user->firstname . ' ' . $this->_ci->user->lastname)); ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="notification_email" class="<?php echo $label_class; ?>">Adresa email</label>
              <div class="<?php echo $value_class; ?>">
                <input id="notification_email" required type="email" maxlength="255" name="email" placeholder="" class="form-control" value="<?php echo htmlspecialchars($this->_ci->user->email); ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="notification_phone" class="<?php echo $label_class; ?>">Numar telefon</label>
              <div class="<?php echo $value_class; ?>">
                <input id="notification_phone" required type="text" maxlength="100" name="phone" placeholder="" class="form-control" value="<?php echo htmlspecialchars($this->_ci->user->phone); ?>" />
              </div>
            </div>
            <p class="text-primary">Veti fi notificat cand pretul acestei oferte scade cu cel putin 10%</p>
			
			<?php themeFunctions::loadAddons('captcha'); ?>
            <div class="form-group row">
              <label for="notification_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?> text-sm-right">
                <button type="submit" id="notification_submit" class="btn btn-success"><i class="fa fa-bell"></i> Trimite</button>
              </div>
            </div>
          </form>
          <div id="result_notificationForm" class="form-group mb-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
;(function($){
var notification_id;
var submitting_form;
window.openNotificationModal = function (obj){
  notification_id = obj.id;
  var $this = $(this);
  $('#result_notificationForm').empty();
  var $form = $('form#notificationForm');
  var form = $form[0];
  form.ref_id.value = obj.type != 'flight' ? obj.ref_id : '';
  form.ref_id.value = obj.ref_id;
  form.type.value = obj.type;
  form.itinerary_code.value = ((obj.type == 'flight') || (obj.type == 'citybreak')) ? obj.itinerary_code : '';
  form.title.value = obj.title;
  form.amount.value = obj.amount;
  form.amount_hotel.value = obj.type == 'citybreak' ? obj.amount_hotel : '';
  form.amount_flight.value = obj.type == 'citybreak' ? obj.amount_flight : '';
  form.currency.value = obj.currency;
  form.data.value = obj.data;
  
  $('#alerta_pret_title').text(form.title.value);
  $('#modal_alerta_pret').modal('show');
};
function notificationSubmitFormSubmitCallback($form,resp,$error_container){
  submitting_form = false;
  if(resp.status !== 'success'){
    return true;
  }
  setTimeout(function(){
    $('#modal_alerta_pret').modal('hide');
  },1000);
  $('#' + notification_id).prop('disabled', true).addClass('disabled');
  return true;
}
$(document).on('submit', 'form#notificationForm', function(e){
  e.preventDefault();
  if(submitting_form){
    return false;
  }
  if(!this.name || !this.name.length){
    return false;
  }
  submitting_form = true;
  basicFormPostSubmit(this,this.action,notificationSubmitFormSubmitCallback,true);
});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>