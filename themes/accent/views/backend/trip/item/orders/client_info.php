<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/client_info/scripts.php'); ?>
<?php 
$client_label_size = array();
$client_label_size['xl'] = 3;
$client_label_size['lg'] = 4;
$client_label_size['md'] = 4;
$client_label_size['sm'] = 4;
$client_label_size[''] = 12;
$client_label_class = 'pt-1 text-sm-right';
$client_value_class = '';
foreach($client_label_size as $k=>$v){
  $client_label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $client_value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
?>
<form id="clientForm" name="clientForm" action="<?php echo site_url('backend/trip/orders/save_client'); ?>" class="mt-3 ml-3 mr-3" method="POST" <?php echo $order->id ? ' onsubmit="return false;"' : '';?>>
  <?php if($can_write){ ?>
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
  <input type="hidden" name="provider" value="<?php echo $order->provider; ?>" />
  <?php } ?>
  <?php if($can_write){ ?>
  <div class="form-group row">
    <label for="previous_order_id" class="<?php echo $client_label_class; ?>">Comanda anterioara</label>
    <div class="<?php echo $client_value_class; ?>">
      <div class="input-group">
        <select class="valid form-control" name="previous_order_id" id="previous_order_id" >
          <option value="">Alege</option>
        </select>
        <div class="input-group-append">
          <button id="updateUserInfoFromOrder" type="button" class="btn btn-primary"><i class="fa fa-arrow-down"></i> <span class="hidden-sm-down">Auto-actualizare</span></button>
        </div>
      </div>
    </div>
  </div>
  <p class="text-info"><i class="fa fa-info-circle"></i> Folositi butonul de <strong>Auto-actualizare</strong> pentru a prelua informatiile curente ale utilizatorului selectat sau informatiile folosite intr-una din comenzile anterioare</p>
  <p class="text-danger"><i class="fa fa-warning"></i> Informatiile clientului din acest formular modifica doar comanda curenta nu si informatiile utilizatorului</p>
  <?php } ?>
  <div class="form-group row">
    <label for="client_id" class="<?php echo $client_label_class; ?>">Client</label>
    <div class="<?php echo $client_value_class; ?>">
      <?php if($can_write){ ?>
      <div class="input-group d-block d-lg-flex">
        <select class="valid form-control" name="user_id" id="client_id" >
          <option value="">Alege</option>
          <?php themeFunctions::loadModule('helpers/clients/select_option',__FILE__ . '/clients_options', array('selected'=>$order->user_id)); ?>
          <?php themeFunctions::loadAddons(__FILE__ . '/clients_options'); ?>
        </select>
        <div class="input-group">
          <select id="client_id_invoice" class="form-control" style="min-width:50px;">
            <option value="">-</option>
            <option value="pf">Fizica</option>
            <option value="pj">Juridica</option>
          </select>
          <div class="input-group-append">
            <button id="updateUserInfo" type="button" class="btn btn-primary"><i class="fa fa-arrow-down"></i> <span class="hidden-sm-down">Auto-actualizare</span></button>
          </div>
        </div>
      </div>
      <?php } else { ?>
      <div id="client_id" class="form-control" readonly>
      <?php themeFunctions::loadModule('helpers/clients/selected_client',__FILE__ . '/clients_options', array('selected'=>$order->user_id,'default'=>'-')); ?>
      <?php themeFunctions::loadAddons(__FILE__ . '/clients_options'); ?>
      &nbsp;
      </div>
      <?php } ?>
    </div>
  </div>
  <hr />
  <div class="row">
    <div class="col-xl-6">
      <div class="form-group row has-danger">
        <label for="client_invoice" class="<?php echo $client_label_class; ?>">Facturare</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <select name="user_invoice" id="client_invoice" class="form-control" required>
            <option value="pf" <?php echo $order->user_invoice != 'pj' ? 'selected' : ''; ?>>Persoana Fizica</option>
            <option value="pj" <?php echo $order->user_invoice == 'pj' ? 'selected' : ''; ?>>Persoana Juridica</option>
          </select>
          <?php } else { ?>
          <div id="client_invoice" class="form-control" readonly><?php echo htmlspecialchars($order->user_invoice == 'pj' ? 'Persoana Juridica' : 'Persoana Fizica'); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row juridic has-danger" <?php echo $order->user_invoice != 'pj' ? 'style="display:none;"' : ''; ?>>
        <label for="client_company_name" class="<?php echo $client_label_class; ?>">Companie</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="255" class="form-control" name="user_company_name" id="client_company_name" <?php echo $order->user_invoice == 'pj' ? 'required' : ''; ?> value="<?php echo htmlspecialchars($order->user_company_name); ?>"  />
          <?php } else { ?>
          <div id="client_company_name" class="form-control" readonly><?php echo htmlspecialchars($order->user_company_name); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row juridic" <?php echo $order->user_invoice != 'pj' ? 'style="display:none;"' : ''; ?> class="<?php echo $client_label_class; ?>">
        <label for="client_cui" class="<?php echo $client_label_class; ?>">CUI</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="50" class="form-control" name="user_cui" id="client_cui"  value="<?php echo htmlspecialchars($order->user_cui); ?>"  />
          <?php } else { ?>
          <div id="client_cui" class="form-control" readonly><?php echo htmlspecialchars($order->user_cui); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row juridic has-danger" <?php echo $order->user_invoice != 'pj' ? 'style="display:none;"' : ''; ?>>
        <label for="client_bank" class="<?php echo $client_label_class; ?>">Banca</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="255" class="form-control" name="user_bank" id="client_bank" <?php echo $order->user_invoice == 'pj' ? 'required' : ''; ?> value="<?php echo htmlspecialchars($order->user_bank); ?>"  />
          <?php } else { ?>
          <div id="client_bank" class="form-control" readonly><?php echo htmlspecialchars($order->user_bank); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row juridic has-danger" <?php echo $order->user_invoice != 'pj' ? 'style="display:none;"' : ''; ?> class="<?php echo $client_label_class; ?>">
        <label for="client_iban" class="<?php echo $client_label_class; ?>">IBAN</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="50" class="form-control" name="user_iban" id="client_iban" <?php echo $order->user_invoice == 'pj' ? 'required' : ''; ?> value="<?php echo htmlspecialchars($order->user_iban); ?>"  />
          <?php } else { ?>
          <div id="client_iban" class="form-control" readonly><?php echo htmlspecialchars($order->user_iban); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row juridic has-danger" <?php echo $order->user_invoice != 'pj' ? 'style="display:none;"' : ''; ?>>
        <label for="client_regcom" class="<?php echo $client_label_class; ?>">Nr.Reg.Com.</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="255" class="form-control" name="user_regcom" id="client_regcom" <?php echo $order->user_invoice == 'pj' ? 'required' : ''; ?> value="<?php echo htmlspecialchars($order->user_regcom); ?>"  />
          <?php } else { ?>
          <div id="client_regcom" class="form-control" readonly><?php echo htmlspecialchars($order->user_regcom); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row has-danger">
        <label for="client_title" class="<?php echo $client_label_class; ?>">Titlu</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
            <select id="client_title" name="user_title" class="form-control" required>
              <option value="">Alege</option>
              <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/client_title_options', array('selected'=>$order->user_title)); ?>
              <?php themeFunctions::loadAddons(__FILE__ . '/client_title_options'); ?>
            </select>
          <?php } else { ?>
          <div id="client_title" class="form-control" readonly>
            <?php themeFunctions::loadModule('helpers/titles/selected_title',__FILE__ . '/client_title_options', array('selected'=>$order->user_title)); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/client_title_options'); ?>
            &nbsp;
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row has-danger">
        <label for="client_lastname" class="<?php echo $client_label_class; ?>">Nume</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="255" id="client_lastname" class="form-control" name="user_lastname" placeholder="" required value="<?php echo htmlspecialchars($order->user_lastname); ?>"  />
          <?php } else { ?>
          <div id="client_lastname" class="form-control" readonly><?php echo htmlspecialchars($order->user_lastname); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row has-danger">
        <label for="client_firstname" class="<?php echo $client_label_class; ?>">Prenume</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="255" id="client_firstname" class="form-control" name="user_firstname" placeholder="" required value="<?php echo htmlspecialchars($order->user_firstname); ?>"  />
          <?php } else { ?>
          <div id="client_firstname" class="form-control" readonly><?php echo htmlspecialchars($order->user_firstname); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row">
        <label for="client_birth_date" class="<?php echo $client_label_class; ?>">Data nastere</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php
          $birth_date = '';
          if(isset($order->user_birth_date) && strlen($order->user_birth_date)){
            $date = DateTime::createFromFormat('Y-m-d', $order->user_birth_date);
            $birth_date = $date ? $date->format('d.m.Y') : null;
          }
          ?>
          <?php if($can_write){ ?>
          <div class="input-group">
            <input type="text" maxlength="10" id="client_birth_date" class="form-control" name="user_birth_date" placeholder=""  value="<?php echo htmlspecialchars($birth_date); ?>"  />
            <label class="input-group-addon" for="client_birth_date">
              <i class="fa fa-calendar"></i>
            </label>
          </div>
          <?php } else { ?>
          <div id="client_birth_date" class="form-control" readonly><?php echo htmlspecialchars($birth_date); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="form-group row has-danger">
        <label for="client_country" class="<?php echo $client_label_class; ?>">Tara</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <select id="client_country" name="user_country" class="form-control" required>
            <option value="">Alege</option>
            <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/client_country_options', array('selected'=>$order->user_country)); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/client_country_options'); ?>
          </select>
          <?php } else { ?>
          <div id="client_country" class="form-control" readonly>
          <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/client_country_options', array('selected'=>$order->user_country,'default'=>'-')); ?>
          <?php themeFunctions::loadAddons(__FILE__ . '/client_country_options'); ?>
          &nbsp;
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row has-danger">
        <label for="client_city" class="<?php echo $client_label_class; ?>">Oras</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="255" class="form-control" id="client_city" name="user_city" required value="<?php echo htmlspecialchars($order->user_city); ?>"  />
          <?php } else { ?>
          <div id="client_city" class="form-control" readonly><?php echo htmlspecialchars($order->user_city); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row">
        <label for="client_address" class="<?php echo $client_label_class; ?>">Adresa facturare</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input id="client_address" maxlength="255" type="text" name="user_address" placeholder="Adresa facturare" class="form-control" value="<?php echo htmlspecialchars($order->user_address); ?>" />
          <?php } else { ?>
          <div id="client_address" class="form-control" readonly><?php echo htmlspecialchars($order->user_address); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row has-danger">
        <label for="client_street" class="<?php echo $client_label_class; ?>">Strada</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="255" class="form-control" id="client_street" name="user_street" required value="<?php echo htmlspecialchars($order->user_street); ?>"  />
          <?php } else { ?>
          <div id="client_street" class="form-control" readonly><?php echo htmlspecialchars($order->user_street); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row">
        <label for="client_street_no" class="<?php echo $client_label_class; ?>">Nr. strada</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="20" class="form-control" id="client_street_no" name="user_street_no" value="<?php echo htmlspecialchars($order->user_street_no); ?>"  />
          <?php } else { ?>
          <div id="client_street_no" class="form-control" readonly><?php echo htmlspecialchars($order->user_street_no); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row">
        <label for="client_postal_code" class="<?php echo $client_label_class; ?>">Cod postal</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input id="client_postal_code" maxlength="50" type="text" name="user_postal_code" placeholder="Cod postal" class="form-control" value="<?php echo htmlspecialchars($order->user_postal_code); ?>" />
          <?php } else { ?>
          <div id="client_postal_code" class="form-control" readonly><?php echo htmlspecialchars($order->user_postal_code); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row">
        <label for="client_phone_prefix" class="<?php echo $client_label_class; ?>">Prefix tel</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <select class="valid form-control" name="user_phone_prefix" id="client_phone_prefix" >
            <option value="">Alege</option>
            <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/countries_phone_prefixes_options', array('selected'=>$order->user_phone_prefix, 'with_prefix' => true)); ?>
            <?php themeFunctions::loadAddons(__FILE__ . '/countries_phone_prefixes_options'); ?>
          </select>
          <?php } else { ?>
          <div id="client_phone_prefix" class="form-control" readonly>
          <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/countries_phone_prefixes_options', array('selected'=>$order->user_phone_prefix,'default'=>'-')); ?>
          <?php themeFunctions::loadAddons(__FILE__ . '/countries_phone_prefixes_options'); ?>
          &nbsp;
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row">
        <label for="client_phone" class="<?php echo $client_label_class; ?>">Telefon</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="text" maxlength="100" class="form-control" id="client_phone" name="user_phone" value="<?php echo htmlspecialchars($order->user_phone); ?>"  />
          <?php } else { ?>
          <div id="client_phone" class="form-control" readonly><?php echo htmlspecialchars($order->user_phone); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row has-danger">
        <label for="client_email" class="<?php echo $client_label_class; ?>">E-mail</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <input type="email" maxlength="255" class="form-control" id="client_email" name="user_email" required value="<?php echo htmlspecialchars($order->user_email); ?>"  />
          <?php } else { ?>
          <div id="client_email" class="form-control" readonly><?php echo htmlspecialchars($order->user_email); ?>&nbsp;</div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <?php if($can_write){ ?>
  <div class="form-group row">
    <label for="client_submit" class="<?php echo $client_label_class; ?>"></label>
    <div class="<?php echo $client_value_class; ?>">
      <button type="submit" id="client_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
    </div>
  </div>
  <?php } ?>
</form>
<div id="result_clientForm" class="form-group"></div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>