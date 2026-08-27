<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php $user = $this->_ci->user; ?>
<h3 class="subSecTicket col-12 mt-4">DATE DE CONTACT SI INFORMATII DE PLATA</h3>
<p class="infoPas col-12 mt-4 mb-4">
  <i class="fa fa-info-circle"></i> Te rugam sa introduci datele persoanei care va efectua plata </p>
<div class="col-sm-6 col-12">
  <label for="copyFirstPas" id="copyFirstPasL"><input type="checkbox" name="copyFirstPas" id="copyFirstPas" /> Copiaza datele primului pasager</label>
</div>
<div class="col-sm-6 col-12">
  <input form="bookingCheckout" type="hidden" name="invoice" value="pf" />
  <label for="facturaPJ" id="facturaPJL"><input type="checkbox" name="invoice" form="bookingCheckout" value="pj" id="facturaPJ" <?php echo $user->invoice=='pj' ? 'checked': ''; ?> /> Doresc factura pe persoana juridica</label>
</div>
<div id="infoPlataPers" class="col-12" style="display:<?php echo $user->invoice=='pj' ? 'none': 'block'; ?>;">
  <hr />
  <div id="invoicePJForm" class="row">
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_lastname">Nume</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" id="contact_pf_lastname" class="form-control" name="contact_lastname" placeholder="Conform Pasaport/CI" required  value="<?php echo htmlspecialchars($user->pf_lastname); ?>" />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_first_name">Prenume</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" id="contact_pf_firstname" class="form-control" name="contact_firstname" placeholder="Conform Pasaport/CI" required  value="<?php echo htmlspecialchars($user->pf_firstname); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">                                                           
      <label for="contact_pf_country">Tara</label><br />
      <select <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> required class="form-control" id="contact_pf_country" name="contact_country">
        <option value="">Alege</option>
        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/contact_pf_country_options', array('selected'=>$user->pf_country)); ?>
        <?php themeFunctions::loadAddons(__FILE__ . '/contact_pf_country_options'); ?>
      </select>
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_city">Oras</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" class="form-control" id="contact_pf_city" name="contact_city" required  value="<?php echo htmlspecialchars($user->pf_city); ?>" />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_street">Strada</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" class="form-control" id="contact_pf_street" name="contact_street" placeholder="Conform Pasaport/CI" required   value="<?php echo htmlspecialchars($user->pf_street); ?>" />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_street_no">Numar strada</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="20" class="form-control" id="contact_pf_street_no" name="contact_street_no" value="<?php echo htmlspecialchars($user->pf_street_no); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_phone_prefix">Telefon (prefix tara)</label><br />
      <select <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> id="contact_pf_phone_prefix" class="form-control" name="contact_phone_prefix">
        <option value="">Alege</option>
        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/contact_pf_countries_phone_prefixes_options', array('selected'=>$user->pf_phone_prefix, 'with_prefix' => true)); ?>
        <?php themeFunctions::loadAddons(__FILE__ . '/contact_pf_countries_phone_prefixes_options'); ?>
      </select>
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_postal_code">Cod postal</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="100" class="form-control" id="contact_pf_postal_code" name="contact_postal_code" value="<?php echo htmlspecialchars($user->pf_postal_code); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_phone">Telefon</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="100" class="form-control" id="contact_pf_phone" placeholder="doar cifre, minim 10" pattern="[0-9]{10,}" name="contact_phone" value="<?php echo htmlspecialchars($user->pf_phone); ?>"  />
      <div class="infoTEL">
        <i class="fa fa-exclamation-circle blue"></i> De preferat un numar de telefon mobil pentru a va putea notifica in cazul modificarilor / anularilor.  </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pf_email">E-mail</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="email" maxlength="255" class="form-control" id="contact_pf_email" name="contact_email" required value="<?php echo htmlspecialchars($user->pf_email); ?>"  />
    </div>
  </div>
</div>
<div  id="infoPlataFirma" class="col-12" style="display:<?php echo $user->invoice=='pj' ? 'block': 'none'; ?>;">
  <hr />      
  <div id="invoicePFForm" class="row">
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_lastname">Nume</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" id="contact_pj_lastname" class="form-control" name="contact_lastname" placeholder="Conform Pasaport/CI" required  value="<?php echo htmlspecialchars($user->pj_lastname); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_firstname">Prenume</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" id="contact_pj_firstname" class="form-control" name="contact_firstname" placeholder="Conform Pasaport/CI" required  value="<?php echo htmlspecialchars($user->pj_firstname); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_company_name">Nume Companie</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" class="form-control" name="contact_company_name" id="contact_pj_company_name" required  value="<?php echo htmlspecialchars($user->pj_company_name); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_cui">CUI</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="50" class="form-control" name="contact_cui" id="contact_pj_cui" value="<?php echo htmlspecialchars($user->pj_cui); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_bank">Banca</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" class="form-control" name="contact_bank" id="contact_pj_bank" required value="<?php echo htmlspecialchars($user->pj_bank); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_iban">IBAN</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="50" class="form-control" name="contact_iban" id="contact_pj_iban" value="<?php echo htmlspecialchars($user->pj_iban); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_regcom">Nr.Reg.Com.</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" class="form-control" name="contact_regcom" id="contact_pj_regcom" required value="<?php echo htmlspecialchars($user->pj_regcom); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">                                                                
      <label for="contact_pj_country">Tara</label><br />
      <select <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> required  id="contact_pj_country" name="contact_country" class="form-control">
        <option value="">Alege</option>
        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/contact_pj_country_options', array('selected'=>$user->pj_country)); ?>
        <?php themeFunctions::loadAddons(__FILE__ . '/contact_pj_country_options'); ?>
      </select>
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_city">Oras</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" class="form-control" id="contact_pj_city" name="contact_city" required  value="<?php echo htmlspecialchars($user->pj_city); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_street">Strada</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="255" class="form-control" id="contact_pj_street" name="contact_street" required  value="<?php echo htmlspecialchars($user->pj_street); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_street_no">Numar strada</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="20" class="form-control" id="contact_pj_street_no" name="contact_street_no"  value="<?php echo htmlspecialchars($user->pj_street_no); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_postal_code">Cod postal</label><br />
      <input <?php echo $user->invoice!='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="100" class="form-control" id="contact_pj_postal_code" name="contact_postal_code" value="<?php echo htmlspecialchars($user->pj_postal_code); ?>"  />
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_phone_prefix">Telefon (prefix tara)</label><br />
      <select <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> class="valid form-control" name="contact_phone_prefix" id="contact_pj_phone_prefix" >
        <option value="">Alege</option>
        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/contact_countries_phone_prefixes_options', array('selected'=>$user->pj_phone_prefix, 'with_prefix' => true)); ?>
        <?php themeFunctions::loadAddons(__FILE__ . '/contact_countries_phone_prefixes_options'); ?>
      </select>
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_phone">Telefon</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="text" maxlength="100" class="form-control" id="contact_pj_phone" placeholder="doar cifre, minim 10" pattern="[0-9]{10,}" name="contact_phone"  value="<?php echo htmlspecialchars($user->pj_phone); ?>"  />
      <div class="infoTEL">
        <i class="fa fa-exclamation-circle blue"></i> De preferat un numar de telefon mobil pentru a va putea notifica in cazul modificarilor / anularilor.  
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-2 form-group">
      <label for="contact_pj_email">E-mail</label><br />
      <input <?php echo $user->invoice=='pj' ? ' form="bookingCheckout"': ''; ?> type="email" maxlength="255" class="form-control" id="contact_pj_email" name="contact_email" required  value="<?php echo htmlspecialchars($user->pj_email); ?>"  />
    </div>
  </div>
</div>
<?php if(!$user->id) { ?>
<div class="col-12">
<div class="row contact-account">
  <div class="col-12 col-sm-6 col-lg-2 form-group mt-1 text-center" title="Trebuie sa alegeti o optiune de rezervare, fara cont sau cu inregistrare cont">
    <label for="contact_create_account_no">Rezervare fara Cont</label>
    <div class="custom-controls-stacked d-block">
      <label class="custom-control custom-radio">
        <input form="bookingCheckout" id="contact_create_account_no" type="radio" name="create_account" value="0" class="custom-control-input" required checked>
        <span class="custom-control-indicator"></span>
      </label>
    </div>
  </div>  
  <div class="col-12 col-sm-6 col-lg-2 form-group mt-1 text-center" title="Trebuie sa alegeti o optiune de rezervare, fara cont sau cu inregistrare cont">
    <label for="contact_create_account_yes">Inregistrare Cont Nou</label>
    <div class="custom-controls-stacked d-block">
      <label class="custom-control custom-radio">
        <input form="bookingCheckout" id="contact_create_account_yes" type="radio" name="create_account" value="1" class="custom-control-input" required>
        <span class="custom-control-indicator"></span>
      </label>
    </div>
  </div>  
  <div class="col-12 col-sm-6 col-lg-3 form-group passSet">
    <label for="contact_account_password">Parola</label><br />
    <input type="password" class="form-control" id="contact_account_password" name="password" />
  </div>
  <div class="col-12 col-sm-6 col-lg-3 form-group passConf">
    <label for="contact_account_password_confirm">Confirma Parola</label><br />
    <input type="password" class="form-control" id="contact_account_password_confirm" name="confirm_password" />
  </div>
</div>
</div>
<?php } ?>
<?php themeFunctions::debugFileLine('end'); ?>