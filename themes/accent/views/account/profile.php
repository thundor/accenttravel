<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('frontend/account_profile'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/profile/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/profile/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/profile/meta.php'); ?>
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
$user = $this->view_data['user'];
$can_write = $this->_ci->user->canAny('frontend-account-profile-save','backend-account-profile-save');
?>
<div class="container mt-1 mb-5">
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <h1>Setari cont</h1>
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <h2 class="h5 display">Detaliile mele de calatorie</h2>
        </div>
        <div class="card-block">
          <h5>Informatii personale</h5>
          <hr />
          <form id="profileForm" name="profileForm" class="profile_form" method="POST" onsubmit="return false;">
            <div class="form-group row">
              <label for="profile_title" class="<?php echo $label_class; ?>">Titlu</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                  <select id="profile_title" name="title" class="form-control">
                    <option value="">Alege</option>
                    <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/profile_title_options', array('selected'=>$user->title)); ?>
                    <?php themeFunctions::loadAddons(__FILE__ . '/profile_title_options'); ?>
                  </select>
                <?php } else { ?>
                <div id="profile_title" class="form-control" readonly>
                  <?php themeFunctions::loadModule('helpers/titles/selected_title',__FILE__ . '/profile_title_options', array('selected'=>$user->title)); ?>
                  <?php themeFunctions::loadAddons(__FILE__ . '/profile_title_options'); ?>
                  &nbsp;
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="profile_firstname" class="<?php echo $label_class; ?>"><?php echo lang('firstname_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="profile_firstname" type="text" maxlength="255" name="firstname" placeholder="<?php echo lang('firstname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->firstname); ?>" />
                <?php } else { ?>
                <div id="profile_firstname" class="form-control" readonly><?php echo htmlspecialchars($user->firstname); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="profile_lastname" class="<?php echo $label_class; ?>"><?php echo lang('lastname_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="profile_lastname" maxlength="255" type="text" name="lastname" placeholder="<?php echo lang('lastname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->lastname); ?>" required />
                <?php } else { ?>
                <div id="profile_lastname" class="form-control" readonly><?php echo htmlspecialchars($user->lastname); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <?php /*
            <div class="form-group row">
              <label for="gender" class="<?php echo $label_class; ?>"><?php echo lang('gender_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <?php themeFunctions::loadModule('helpers/genders/radios',__FILE__ . '/profile_gender', array('selected'=>$user->gender)); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/profile_gender'); ?>
                <?php } else { ?>
                <div id="gender" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/genders/selected_gender',__FILE__ . '/profile_gender', array('selected'=>$user->gender)); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/profile_gender'); ?>
                &nbsp;
                </div>
                <?php } ?>
              </div>
            </div>
            */ ?>
            <div class="form-group row">
              <label for="profile_birth_date" class="<?php echo $label_class; ?>"><?php echo lang('birth_date_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php $birth_date = $user->getBirthDate(); ?>
                <?php if($can_write){ ?>
                <input id="profile_birth_date" maxlength="10" type="text" name="birth_date" placeholder="<?php echo lang('birth_date_field_placeholder'); ?>" class="form-control input-birth_date" value="<?php echo htmlspecialchars($birth_date); ?>" required />
                <?php } else { ?>
                <div id="profile_birth_date" class="form-control" readonly><?php echo htmlspecialchars($birth_date); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="profile_country" class="<?php echo $label_class; ?>">Nationalitate</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <select id="profile_country" name="country" class="form-control">
                  <option value="">Alege</option>
                  <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/profile_country_options', array('selected'=>$user->country)); ?>
                  <?php themeFunctions::loadAddons(__FILE__ . '/profile_country_options'); ?>
                </select>
                <?php } else { ?>
                <div id="profile_country" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/profile_country_options', array('selected'=>$user->country,'default'=>'-')); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/profile_country_options'); ?>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="profile_city" class="<?php echo $label_class; ?>"><?php echo lang('city_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="profile_city" maxlength="255" type="text" name="city" placeholder="<?php echo lang('city_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->city); ?>" />
                <?php } else { ?>
                <div id="profile_city" class="form-control" readonly><?php echo htmlspecialchars($user->city); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="profile_phone_prefix" class="<?php echo $label_class; ?>">Prefix telefon</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <select id="profile_phone_prefix" name="phone_prefix" class="form-control">
                  <option value="">Alege</option>
                  <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/profile_phone_prefix_options', array('selected'=>$user->phone_prefix, 'with_prefix' => true)); ?>
                  <?php themeFunctions::loadAddons(__FILE__ . '/profile_phone_prefix_options'); ?>
                </select>
                <?php } else { ?>
                <div id="profile_phone_prefix" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/profile_phone_prefix_options', array('selected'=>$user->phone_prefix,'default'=>'-', 'with_prefix' => true)); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/profile_phone_prefix_options'); ?>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="profile_phone" class="<?php echo $label_class; ?>"><?php echo lang('phone_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="profile_phone" maxlength="100" type="tel" name="phone" placeholder="<?php echo lang('phone_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->phone); ?>" />
                <?php } else { ?>
                <div id="profile_phone" class="form-control" readonly><?php echo htmlspecialchars($user->phone); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="profile_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="profile_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <div id="result_profileForm" class="form-group" ></div>
          <hr />
          <form id="contactForm" name="contactForm" class="profile_form" method="POST" onsubmit="return false;">
            <h5>Contact Urgente</h5>
            <hr />
            <div class="form-group row">
              <label for="contact_firstname" class="<?php echo $label_class; ?>"><?php echo lang('firstname_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="contact_firstname" maxlength="255" type="text" name="contact_firstname" placeholder="<?php echo lang('firstname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->contact_firstname); ?>" />
                <?php } else { ?>
                <div id="contact_firstname" class="form-control" readonly><?php echo htmlspecialchars($user->contact_firstname); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="contact_lastname" class="<?php echo $label_class; ?>"><?php echo lang('lastname_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="contact_lastname" maxlength="255" type="text" name="contact_lastname" placeholder="<?php echo lang('lastname_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->contact_lastname); ?>" />
                <?php } else { ?>
                <div id="contact_lastname" class="form-control" readonly><?php echo htmlspecialchars($user->contact_lastname); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="contact_phone_prefix" class="<?php echo $label_class; ?>">Prefix telefon</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <select id="contact_phone_prefix" name="contact_phone_prefix" class="form-control">
                  <option value="">Alege</option>
                  <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/contact_phone_prefix_options', array('selected'=>$user->contact_phone_prefix, 'with_prefix' => true)); ?>
                  <?php themeFunctions::loadAddons(__FILE__ . '/contact_phone_prefix_options'); ?>
                </select>
                <?php } else { ?>
                <div id="contact_phone_prefix" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/contact_phone_prefix_options', array('selected'=>$user->contact_phone_prefix,'default'=>'-', 'with_prefix' => true)); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/contact_phone_prefix_options'); ?>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="contact_phone" class="<?php echo $label_class; ?>"><?php echo lang('phone_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="contact_phone" maxlength="100" type="tel" name="contact_phone" placeholder="<?php echo lang('phone_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->contact_phone); ?>" />
                <?php } else { ?>
                <div id="contact_phone" class="form-control" readonly><?php echo htmlspecialchars($user->contact_phone); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="contact_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="contact_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <div id="result_contactForm" class="form-group"></div>
          <hr />
          <h5>Preferinte zbor</h5>
          <hr />
          <form id="profileFlightPreferencesForm" name="profileFlightPreferencesForm" class="profile_form" method="POST" onsubmit="return false;">
            <div class="form-group row">
              <?php $flight_departure_airport = $user->getFlightDepartureAirport();
              $flight_departure_airport_full_location_name = $flight_departure_airport->city_id > 0 ? ($flight_departure_airport->location_id > 0 ? $flight_departure_airport->location_name . ', ' : '' ) . $flight_departure_airport->city_name : '';
              ?>
              <input id="flight_departure_airport_location_id" type="hidden" name="flight_departure_airport[location_id]" value="<?php echo htmlspecialchars($flight_departure_airport->location_id); ?>"/>
              <input id="flight_departure_airport_location_code" type="hidden" name="flight_departure_airport[location_code]" value="<?php echo htmlspecialchars($flight_departure_airport->location_code); ?>" />
              <input id="flight_departure_airport_location_name" type="hidden" name="flight_departure_airport[location_name]" value="<?php echo htmlspecialchars($flight_departure_airport->location_name); ?>" />
              <input id="flight_departure_airport_full_location_name" type="hidden" value="<?php echo htmlspecialchars($flight_departure_airport_full_location_name); ?>" />
              <input id="flight_departure_airport_city_id" type="hidden" name="flight_departure_airport[city_id]" value="<?php echo htmlspecialchars($flight_departure_airport->city_id); ?>" />
              <input id="flight_departure_airport_city_code" type="hidden" name="flight_departure_airport[city_code]" value="<?php echo htmlspecialchars($flight_departure_airport->city_code); ?>" />
              <input id="flight_departure_airport_city_name" type="hidden" name="flight_departure_airport[city_name]" value="<?php echo htmlspecialchars($flight_departure_airport->city_name); ?>" />
              <input id="flight_departure_airport_country_id" type="hidden" name="flight_departure_airport[country_id]" value="<?php echo htmlspecialchars($flight_departure_airport->country_id); ?>" />
              <input id="flight_departure_airport_country_name" type="hidden" name="flight_departure_airport[country_name]" value="<?php echo htmlspecialchars($flight_departure_airport->country_name); ?>" />
              <label for="flight_departure_airport" class="<?php echo $label_class; ?>">Aeroport plecare</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="flight_departure_airport" type="text" placeholder="Aeroport plecare" class="form-control" value="<?php echo htmlspecialchars($flight_departure_airport_full_location_name); ?>" />
                <?php } else { ?>
                <div id="flight_departure_airport" class="form-control" readonly><?php echo htmlspecialchars($flight_departure_airport_full_location_name); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="flight_prefered_spot" class="<?php echo $label_class; ?>">Preferinte loc</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <select id="flight_prefered_spot" name="flight_prefered_spot" class="form-control">
                  <option value="">Nicio preferinta</option>
                  <?php themeFunctions::loadModule('helpers/flights/preferred_spots/select_options',__FILE__ . '/flight_prefered_spot', array('selected'=>$user->flight_prefered_spot)); ?>
                  <?php themeFunctions::loadAddons(__FILE__ . '/flight_prefered_spot'); ?>
                </select>
                <?php } else { ?>
                <div id="flight_prefered_spot" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/flights/preferred_spots/selected_preferred_spot',__FILE__ . '/flight_prefered_spot', array('selected'=>$user->flight_prefered_spot,'default'=>'Nicio preferinta')); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/flight_prefered_spot'); ?>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="flight_special_assistance" class="<?php echo $label_class; ?>">Asistenta speciala</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input type="hidden" name="flight_special_assistance" />
                <select id="flight_special_assistance" name="flight_special_assistance[]" multiple class="form-control">
                  <?php themeFunctions::loadModule('helpers/flights/special_assistance/multiselect_options',__FILE__ . '/flight_special_assistance', array('selected'=>$user->getFlightSpecialAssistance())); ?>
                  <?php themeFunctions::loadAddons(__FILE__ . '/flight_special_assistance'); ?>
                </select>
                <?php } else { ?>
                <div id="flight_special_assistance" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/flights/special_assistance/selected_special_assistances',__FILE__ . '/flight_special_assistance', array('selected'=>$user->getFlightSpecialAssistance(),'default'=>'Nimic')); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/flight_special_assistance'); ?>
                </div>
                <?php } ?>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="flight_prefered_spot_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="flight_prefered_spot_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <div id="result_profileFlightPreferencesForm" class="form-group"></div>
          <hr />
          <h5>Pasaport</h5>
          <hr />
          <form id="profilePassportForm" name="profilePassportForm" class="profile_form" method="POST" onsubmit="return false;">
            <div class="form-group row">
              <label for="passport_country" class="<?php echo $label_class; ?>">Tara</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <select id="passport_country" name="passport_country" class="form-control">
                  <option value="">Alege</option>
                  <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/passport_country_options', array('selected'=>$user->passport_country)); ?>
                  <?php themeFunctions::loadAddons(__FILE__ . '/passport_country_options'); ?>
                </select>
                <?php } else { ?>
                <div id="passport_country" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/passport_country_options', array('selected'=>$user->passport_country,'default'=>'-')); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/passport_country_options'); ?>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="passport_number" class="<?php echo $label_class; ?>">Numar pasaport</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="passport_number" maxlength="255" type="text" name="passport_number" placeholder="Numar pasaport" class="form-control" value="<?php echo htmlspecialchars($user->passport_number); ?>" />
                <?php } else { ?>
                <div id="passport_number" class="form-control" readonly><?php echo htmlspecialchars($user->passport_number); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="profile_passport_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="profile_passport_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <div id="result_profilePassportForm" class="form-group"></div>
          <hr />
          <?php /*
          <h5>Istoric rezervari <strong>TODO</strong></h5>
          <hr />
          <div id="profileBookingHistory">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th class="text-center" width="1%" style="vertical-align:middle;">Data rezervare</th>
                  <th class="text-center" style="vertical-align:middle;">Tip rezervare</th>
                  <th class="text-center" style="vertical-align:middle;">Total</th>
                  <th class="text-center" width="1%" style="vertical-align:middle;">Puncte</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-center">10.06.2015</td>
                  <td>
                    <strong>Pachet</strong>
                    <div>
                      7 nopți / demipensiune Hotel Perla, Mamaia
                    </div>
                  </td>
                  <td class="text-center">2850 Lei</br>/ 2 adulți</td>
                  <td class="text-center" style="vertical-align:middle;">57</td>
                </tr>
                <tr>
                  <td class="text-center">23.10.2016<br/>28.10.2016</td>
                  <td>
                    <strong>Bilete avion</strong>
                    <div>
                      Bucuresti > Londra (dus-intors)
                    </div>
                  </td>
                  <td class="text-center">1450 Lei</br>/2 adulți</td>
                  <td class="text-center" style="vertical-align:middle;">29</td>
                </tr>
              </tbody>
            </table>
          </div>
          <hr />
          <h5>Puncte de recompensa <strong>TODO</strong></h5>
          <hr />
          <div id="profilePoints">
            <p class="text-danger">(Punctele de recompensă reprezintă o valoare de 2% din prețul total al pachetului turistic achiziționat și pot fi folosite 18 luni din momentul acordării. Dupa 18 luni acestea se șterg).</p>
            <h4><strong>Total</strong> 86</h4>
            Foloseste punctele:
            <ol>
              <li>Pentru următoarea vacanță</li>
              <li>Pentru achiziția de produse pentru călătorie</li>
            </ol>
          </div>
          */ ?>
          <?php if($user->type === 'customer'){ ?>
          <hr />
          <h5>Conectare prin retele sociale</h5>
          <hr />
          <form id="socialForm" name="socialForm" method="POST" class="profile_form" onsubmit="return false;">
            <div class="form-group row">
              <label for="social_login_fb" class="<?php echo $label_class; ?>">Conectare prin</label>
              <div class="<?php echo $value_class; ?>">
                <input type="hidden" name="social_login" />
                <?php if($can_write){ ?>
                <?php themeFunctions::loadModule('helpers/social_networks/checkboxes',__FILE__ . '/social_login', array('id_prefix'=>'social_login_','name'=>'social_login[]','selected'=>$user->getSocialLoginNetworks())); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/social_login'); ?>
                <?php } else { ?>
                <div id="social_login_fb" class="form-control" readonly>
                <?php themeFunctions::loadModule('helpers/social_networks/selected_social_networks',__FILE__ . '/social_login', array('selected'=>$user->getSocialLoginNetworks(),'default'=>'Niciuna')); ?>
                <?php themeFunctions::loadAddons(__FILE__ . '/social_login'); ?>
                &nbsp;
                </div>
                <?php } ?>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="social_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="social_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <div id="result_socialForm" class="form-group"></div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <h2 class="h5 display">Informatii cont</h2>
        </div>
        <div class="card-block">
          <h5>Email si parola</h5>
          <hr />
          <div class="form-group row">
            <label class="<?php echo $label_class; ?>"><?php echo lang('email_field_label/html'); ?></label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div id="current_email_div" class="form-control" readonly><?php echo htmlspecialchars($user->email); ?></div>
                <?php if($can_write){ ?>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-primary btn-toggle emailtoggler collapsed" aria-expanded="false" data-toggle="collapse" data-target="#profileEmailForm"><i class="fa fa-pencil"></i> <span class="hidden-xs-down">Modifica</span></button>
                </span>
                <?php } ?>
              </div>
            </div>
          </div>
          <div id="result_profileEmailForm" class="form-group"></div>
          <?php if($can_write){ ?>
          <form id="profileEmailForm" name="profileEmailForm" class="collapse profile_form" method="POST" onsubmit="return false;">
            <div class="form-group row">
              <label for="email" class="<?php echo $label_class; ?>"><?php echo lang('new_email_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <input id="email" maxlength="255" type="email" name="email" placeholder="<?php echo lang('new_email_field_placeholder'); ?>" class="form-control" value="" required />
              </div>
            </div>
            <div class="form-group row">
              <label for="email_confirm" class="<?php echo $label_class; ?>"><?php echo lang('confirm_new_email_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <input id="email_confirm" type="email" name="email_confirm" placeholder="<?php echo lang('confirm_new_email_field_placeholder'); ?>" class="form-control" value="" required />
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="email_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="email_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
            <hr />
          </form>
          <?php } ?>
          
          <?php if($user->type !=='customer') { ?>
          <div class="form-group row">
            <label class="<?php echo $label_class; ?>"><?php echo lang('username_field_label/html'); ?></label>
            <div class="<?php echo $value_class; ?>">
              <div class="input-group">
                <div id="current_username_div" class="form-control" readonly><?php echo htmlspecialchars($user->username); ?></div>
                <?php if($can_write){ ?>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-primary btn-toggle usernametoggler collapsed" aria-expanded="false" data-toggle="collapse" data-target="#profileUsernameForm"><i class="fa fa-pencil"></i> <span class="hidden-xs-down">Modifica</span></button>
                </span>
                <?php } ?>
              </div>
            </div>
          </div>
          <div id="result_profileUsernameForm" class="form-group"></div>
          <form id="profileUsernameForm" name="profileUsernameForm" class="collapse profile_form" method="POST" onsubmit="return false;">
            <div class="form-group row">
              <label for="username" class="<?php echo $label_class; ?>"><?php echo lang('new_username_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="username" type="text" name="username" placeholder="<?php echo lang('new_username_field_placeholder'); ?>" class="form-control" value="" required />
                <small class="text-muted"><?php echo lang('username_field_help/html'); ?></small>
                <?php } else { ?>
                <div class="form-control"><?php echo htmlspecialchars($user->username); ?></div>
                <?php } ?>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="username_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="username_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
            <hr />
          </form>
          <?php } ?>
          <?php if($can_write){ ?>
          <div class="form-group row">
            <label for="password" class="<?php echo $label_class; ?>"><?php echo lang('password_field_label/html'); ?></label>
            <div class="<?php echo $value_class; ?>">
              <span class="btn-group">
                <button type="button" class="btn btn-primary btn-toggle passwordtoggler collapsed" aria-expanded="false" data-toggle="collapse" data-target="#profilePasswordForm"><i class="fa fa-pencil"></i> <span class="hidden-xs-down">Modifica</span></button>
              </span>
            </div>
          </div>
          <div id="result_profilePasswordForm" class="form-group"></div>
          <form id="profilePasswordForm" name="profilePasswordForm" class="collapse profile_form" method="POST" onsubmit="return false;">
            <input type="text" id="current_email_input" style="display:none;" value="<?php echo htmlspecialchars($user->type=='customer' ? $user->email : $user->username, ENT_QUOTES, false); ?>" />
            <div class="form-group row">
              <label for="password" class="<?php echo $label_class; ?>"><?php echo lang('current_password_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <div class="input-group">
                  <input id="password" type="password" name="password" placeholder="<?php echo lang('current_password_field_placeholder'); ?>" class="form-control"/>
                  <span class="input-group-addon">
                    <input type="checkbox" title="<?php echo lang('password_field_show'); ?>" onchange="jQuery('#password', this.form).attr('type', $(this).is(':checked') ? 'text' : 'password');"/>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="new_password" class="<?php echo $label_class; ?>"><?php echo lang('new_password_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <div class="input-group">
                  <input id="new_password" type="password" name="new_password" placeholder="<?php echo lang('new_password_field_placeholder'); ?>" class="form-control"/>
                  <span class="input-group-addon">
                    <input type="checkbox" title="<?php echo lang('password_field_show'); ?>" onchange="jQuery('#new_password', this.form).attr('type', $(this).is(':checked') ? 'text' : 'password');"/>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="confirm_new_password" class="<?php echo $label_class; ?>"><?php echo lang('confirm_new_password_field_label/html'); ?></label>
              <div class="<?php echo $value_class; ?>">
                <div class="input-group">
                  <input id="confirm_new_password" type="password" name="confirm_new_password" placeholder="<?php echo lang('confirm_new_password_field_placeholder'); ?>" class="form-control"/>
                  <span class="input-group-addon">
                    <input type="checkbox" title="<?php echo lang('password_field_show'); ?>" onchange="jQuery('#confirm_new_password', this.form).attr('type', $(this).is(':checked') ? 'text' : 'password');"/>
                  </span>
                </div>
              </div>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="password_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <p class="text-muted"><?php echo lang('password_field_help/html'); ?></p>
                <button type="submit" id="password_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <?php } ?>
          <hr />
          <h5>Informatii facturare</h5>
          <hr />
          <div class="card">
            <div class="card-header">
              <ul class="nav nav-tabs card-header-tabs nav-justified">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#invoice_tab_pf" role="tab" aria-controls="invoice_tab_pf">Persoana fizica</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#invoice_tab_pj" role="tab" aria-controls="invoice_tab_pj">Persoana juridica</a>
                </li>
              </ul>
            </div>
            <form id="invoiceForm" name="invoiceForm" class="profile_form" method="POST" onsubmit="return false;">
              <div class="tab-content card-block">
                <div class="tab-pane active" id="invoice_tab_pf" role="tabpanel">
                  <div class="form-group row">
                    <label for="invoice_pf_lastname" class="<?php echo $label_class; ?>">Nume</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_lastname" maxlength="255" type="text" name="pf_lastname" placeholder="Nume" class="form-control" value="<?php echo htmlspecialchars($user->pf_lastname); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_lastname" class="form-control" readonly><?php echo htmlspecialchars($user->pf_lastname); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_firstname" class="<?php echo $label_class; ?>">Prenume</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_firstname" maxlength="255" type="text" name="pf_firstname" placeholder="Prenume" class="form-control" value="<?php echo htmlspecialchars($user->pf_firstname); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_firstname" class="form-control" readonly><?php echo htmlspecialchars($user->pf_firstname); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_country" class="<?php echo $label_class; ?>">Tara</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <select id="invoice_pf_country" name="pf_country" class="form-control">
                        <option value="">Alege</option>
                        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/invoice_pf_country_options', array('selected'=>$user->pf_country)); ?>
                        <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pf_country_options'); ?>
                      </select>
                      <?php } else { ?>
                      <div id="invoice_pf_country" class="form-control" readonly>
                      <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/invoice_pf_country_options', array('selected'=>$user->pf_country,'default'=>'-')); ?>
                      <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pf_country_options'); ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_city" class="<?php echo $label_class; ?>">Oras</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_city" maxlength="255" type="text" name="pf_city" placeholder="Oras" class="form-control" value="<?php echo htmlspecialchars($user->pf_city); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_city" class="form-control" readonly><?php echo htmlspecialchars($user->pf_city); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_street" class="<?php echo $label_class; ?>">Strada</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_street" maxlength="255" type="text" name="pf_street" placeholder="Strada" class="form-control" value="<?php echo htmlspecialchars($user->pf_street); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_street" class="form-control" readonly><?php echo htmlspecialchars($user->pf_street); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_street_no" class="<?php echo $label_class; ?>">Numar strada</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_street_no" maxlength="20" type="text" name="pf_street_no" placeholder="Numar strada" class="form-control" value="<?php echo htmlspecialchars($user->pf_street_no); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_street_no" class="form-control" readonly><?php echo htmlspecialchars($user->pf_street_no); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_phone_prefix" class="<?php echo $label_class; ?>">Prefix telefon</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <select id="invoice_pf_phone_prefix" name="pf_phone_prefix" class="form-control">
                        <option value="">Alege</option>
                        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/invoice_pf_phone_prefix_options', array('selected'=>$user->pf_phone_prefix, 'with_prefix' => true)); ?>
                        <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pf_phone_prefix_options'); ?>
                      </select>
                      <?php } else { ?>
                      <div id="invoice_pf_phone_prefix" class="form-control" readonly>
                      <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/invoice_pf_phone_prefix_options', array('selected'=>$user->pf_phone_prefix,'default'=>'-', 'with_prefix' => true)); ?>
                      <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pf_phone_prefix_options'); ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_phone" class="<?php echo $label_class; ?>"><?php echo lang('phone_field_label/html'); ?></label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_phone" maxlength="100" type="tel" name="pf_phone" placeholder="<?php echo lang('phone_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->pf_phone); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_phone" class="form-control" readonly><?php echo htmlspecialchars($user->pf_phone); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_email" class="<?php echo $label_class; ?>"><?php echo lang('email_field_label/html'); ?></label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_email" maxlength="255" type="email" name="pf_email" placeholder="<?php echo lang('email_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->pf_email); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_email" class="form-control" readonly><?php echo htmlspecialchars($user->pf_email); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_address" class="<?php echo $label_class; ?>">Adresa facturare</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_address" maxlength="255" type="text" name="pf_address" placeholder="Adresa facturare" class="form-control" value="<?php echo htmlspecialchars($user->pf_address); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_address" class="form-control" readonly><?php echo htmlspecialchars($user->pf_address); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pf_postal_code" class="<?php echo $label_class; ?>">Cod postal</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pf_postal_code" maxlength="50" type="text" name="pf_postal_code" placeholder="Cod postal" class="form-control" value="<?php echo htmlspecialchars($user->pf_postal_code); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pf_postal_code" class="form-control" readonly><?php echo htmlspecialchars($user->pf_postal_code); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="invoice_tab_pj" role="tabpanel">
                  <div class="form-group row">
                    <label for="invoice_pj_lastname" class="<?php echo $label_class; ?>">Nume</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_lastname" maxlength="255" type="text" name="pj_lastname" placeholder="Nume" class="form-control" value="<?php echo htmlspecialchars($user->pj_lastname); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_lastname" class="form-control" readonly><?php echo htmlspecialchars($user->pj_lastname); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_firstname" class="<?php echo $label_class; ?>">Prenume</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_firstname" maxlength="255" type="text" name="pj_firstname" placeholder="Prenume" class="form-control" value="<?php echo htmlspecialchars($user->pj_firstname); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_firstname" class="form-control" readonly><?php echo htmlspecialchars($user->pj_firstname); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_company_name" class="<?php echo $label_class; ?>">Nume companie</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_company_name" maxlength="255" type="text" name="pj_company_name" placeholder="Nume companie" class="form-control" value="<?php echo htmlspecialchars($user->pj_company_name); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_company_name" class="form-control" readonly><?php echo htmlspecialchars($user->pj_company_name); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_cui" class="<?php echo $label_class; ?>">CUI</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_cui" maxlength="50" type="text" name="pj_cui" placeholder="CUI" class="form-control" value="<?php echo htmlspecialchars($user->pj_cui); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_cui" class="form-control" readonly><?php echo htmlspecialchars($user->pj_cui); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_bank" class="<?php echo $label_class; ?>">Banca</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_bank" maxlength="255" type="text" name="pj_bank" placeholder="Banca" class="form-control" value="<?php echo htmlspecialchars($user->pj_bank); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_bank" class="form-control" readonly><?php echo htmlspecialchars($user->pj_bank); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_iban" class="<?php echo $label_class; ?>">IBAN</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_iban" maxlength="50" type="text" name="pj_iban" placeholder="IBAN" class="form-control" value="<?php echo htmlspecialchars($user->pj_iban); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_iban" class="form-control" readonly><?php echo htmlspecialchars($user->pj_iban); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_regcom" class="<?php echo $label_class; ?>">Nr.Reg.Com.</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_regcom" maxlength="255" type="text" name="pj_regcom" placeholder="Nr.Reg.Com." class="form-control" value="<?php echo htmlspecialchars($user->pj_regcom); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_regcom" class="form-control" readonly><?php echo htmlspecialchars($user->pj_regcom); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_country" class="<?php echo $label_class; ?>">Tara</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <select id="invoice_pj_country" name="pj_country" class="form-control">
                        <option value="">Alege</option>
                        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/invoice_pj_country_options', array('selected'=>$user->pj_country)); ?>
                        <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pj_country_options'); ?>
                      </select>
                      <?php } else { ?>
                      <div id="invoice_pj_country" class="form-control" readonly>
                      <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/invoice_pj_country_options', array('selected'=>$user->pj_country,'default'=>'-')); ?>
                      <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pj_country_options'); ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_city" class="<?php echo $label_class; ?>">Oras</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_city" maxlength="255" type="text" name="pj_city" placeholder="Oras" class="form-control" value="<?php echo htmlspecialchars($user->pj_city); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_city" class="form-control" readonly><?php echo htmlspecialchars($user->pj_city); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_street" class="<?php echo $label_class; ?>">Strada</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_street" maxlength="255" type="text" name="pj_street" placeholder="Strada" class="form-control" value="<?php echo htmlspecialchars($user->pj_street); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_street" class="form-control" readonly><?php echo htmlspecialchars($user->pj_street); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_street_no" class="<?php echo $label_class; ?>">Numar strada</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_street_no" maxlength="20" type="text" name="pj_street_no" placeholder="Numar strada" class="form-control" value="<?php echo htmlspecialchars($user->pj_street_no); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_street_no" class="form-control" readonly><?php echo htmlspecialchars($user->pj_street_no); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_phone_prefix" class="<?php echo $label_class; ?>">Prefix telefon</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <select id="invoice_pj_phone_prefix" name="pj_phone_prefix" class="form-control">
                        <option value="">Alege</option>
                        <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/invoice_pj_phone_prefix_options', array('selected'=>$user->pj_phone_prefix, 'with_prefix' => true)); ?>
                        <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pj_phone_prefix_options'); ?>
                      </select>
                      <?php } else { ?>
                      <div id="invoice_pj_phone_prefix" class="form-control" readonly>
                      <?php themeFunctions::loadModule('helpers/countries/selected_country',__FILE__ . '/invoice_pj_phone_prefix_options', array('selected'=>$user->pj_phone_prefix,'default'=>'-', 'with_prefix' => true)); ?>
                      <?php themeFunctions::loadAddons(__FILE__ . '/invoice_pj_phone_prefix_options'); ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_phone" class="<?php echo $label_class; ?>"><?php echo lang('phone_field_label/html'); ?></label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_phone" maxlength="100" type="tel" name="pj_phone" placeholder="<?php echo lang('phone_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->pj_phone); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_phone" class="form-control" readonly><?php echo htmlspecialchars($user->pj_phone); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_email" class="<?php echo $label_class; ?>"><?php echo lang('email_field_label/html'); ?></label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_email" maxlength="255" type="email" name="pj_email" placeholder="<?php echo lang('email_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($user->pj_email); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_email" class="form-control" readonly><?php echo htmlspecialchars($user->pj_email); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_address" class="<?php echo $label_class; ?>">Adresa facturare</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_address" maxlength="255" type="text" name="pj_address" placeholder="Adresa facturare" class="form-control" value="<?php echo htmlspecialchars($user->pj_address); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_address" class="form-control" readonly><?php echo htmlspecialchars($user->pj_address); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="invoice_pj_postal_code" class="<?php echo $label_class; ?>">Cod postal</label>
                    <div class="<?php echo $value_class; ?>">
                      <?php if($can_write){ ?>
                      <input id="invoice_pj_postal_code" maxlength="50" type="text" name="pj_postal_code" placeholder="Cod postal" class="form-control" value="<?php echo htmlspecialchars($user->pj_postal_code); ?>" />
                      <?php } else { ?>
                      <div id="invoice_pj_postal_code" class="form-control" readonly><?php echo htmlspecialchars($user->pj_postal_code); ?>&nbsp;</div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <hr />
                <div class="form-group">
                  <?php if($can_write){ ?>
                  Doresc facturare implicita pe 
                  <div class="custom-controls-stacked d-inline-block ml-3">
                    <label class="custom-control custom-radio">
                      <input id="invoice_pf" type="radio" name="invoice" value="pf" class="custom-control-input" <?php echo (!$user->invoice || $user->invoice == 'pf') ? 'checked' : ''; ?>>
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">Persoana fizica</span>
                    </label>
                    <label class="custom-control custom-radio">
                      <input id="invoice_pj" type="radio" name="invoice" value="pj" class="custom-control-input" <?php echo $user->invoice == 'pj' ? 'checked' : ''; ?>>
                      <span class="custom-control-indicator"></span>
                      <span class="custom-control-description">Persoana juridica</span>
                    </label>
                  </div>
                  <?php } else { ?>
                  <div id="invoice_pf_pj" class="form-control" readonly>Doresc facturare implicita pe <?php echo $user->invoice == 'pj' ? 'persoana juridica' : 'persoana fizica'; ?>&nbsp;</div>
                  <?php } ?>
                </div>
                <?php if($can_write){ ?>
                <div class="form-group row">
                  <label for="invoice_submit" class="<?php echo $label_class; ?>"></label>
                  <div class="<?php echo $value_class; ?>">
                    <button type="submit" id="invoice_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
                  </div>
                </div>
                <?php } ?>
              </div>
            </form>
          </div>
          <div id="result_invoiceForm" class="form-group"></div>
          <hr />
          <?php /*
          <h5>Cupoane de reducere <strong>TODO</strong></h5>
          <hr />
          <div id="profileCoupons">
            <ul class="list-group">
              <li class="list-group-item">
                <div style="width:100%">
                  <h5>Cupon discount "1 Martie" (2017): 1MARTIEACC17</h5>
                  <div class="text-right">
                    <small>Valabil până la 31.12.2017</small>
                  </div>
                </div>
              </li>
              <li class="list-group-item">
                <div style="width:100%">
                  <h5>Cupon discount "Vara ta" (2017): VaraACC17</h5>
                  <div class="text-right">
                    <small>Valabil până la 31.03.2018</small>
                  </div>
                </div>
              </li>
            </ul>
            <br />
            <h5>Cupon discount companie</h5>
            <p>Dacă angajatorul tău are un contract cu Accent Travel & Events, cere și setează aici codul de discount pentru a beneficia de reduceri la orice vacanță!</p>
            <br />
            <p><strong>ATENȚIE:</strong> Cupoanele de reducere nu se cumulează și nu pot fi folosite cu puncte de recompensă.</p>
          </div>
          <hr />
          */ ?>
          <h5>Preferinte email</h5>
          <hr />
          <form id="profileEmailPreferences" name="profileEmailPreferences" class="profile_form" method="POST" onsubmit="return false;">
            <div class="form-group">
              <?php if($can_write){ ?>
              <?php if($user->newsletter){ ?>
              <input type="hidden" name="newsletter" value="1" />
              <label class="custom-control custom-checkbox">
                <input id="newsletter_disable" type="checkbox" name="newsletter" value="0" class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Dezabonare Newsletter</span>
              </label>
              <?php } else { ?>
              <input type="hidden" name="newsletter" value="0" />
              <label class="custom-control custom-checkbox">
                <input id="newsletter_enable" type="checkbox" name="newsletter" value="1" class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Abonare Newsletter</span>
              </label>
              <?php } ?>
              <?php } else { ?>
              <div id="newsletter_enable_disable" class="form-control" readonly><?php echo $user->pj_newsletter ? 'Abonat' : 'Neabonat'; ?>&nbsp;</div>
              <?php } ?>
            </div>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="email_preferences_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="email_preferences_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <div id="result_profileEmailPreferences" class="form-group"></div>
          <hr />
          <h5>Organizare calatorii (adaugare insotitori)</h5>
          <hr />
          <form id="profileFellows" name="profileFellows" class="profile_form" method="POST" onsubmit="return false;">
            <table id="profileFellowsTable" class="table table-bordered table-hover ac">
              <thead>
                <tr>
                  <th class="text-center" width="1%">#</th>
                  <th>Insotitor</th>
                  <?php if($can_write){ ?>
                  <th class="text-center" width="1%">Actiuni</th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr><td colspan="<?php echo $can_write ? 3 : 2; ?>" class="text-center">Niciun insotitor adaugat</td></tr>
              </tfoot>
            </table>
            <?php if($can_write){ ?>
            <div class="form-group row">
              <label for="fellows_submit" class="<?php echo $label_class; ?>"></label>
              <div class="<?php echo $value_class; ?>">
                <button type="submit" id="fellows_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
              </div>
            </div>
            <?php } ?>
          </form>
          <div id="fellowEditModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    <?php if($can_write){ ?>
                    Editare Insotitor
                    <?php } else { ?>
                    Vizualizare Insotitor
                    <?php } ?>
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="profileFellowEdit" name="profileFellowEdit" method="POST" onsubmit="return false;">
                    <div class="form-group row">
                      <label for="fellow_edit_title" class="<?php echo $label_class; ?>">Titlu</label>
                      <div class="<?php echo $value_class; ?>">
                        <?php if($can_write){ ?>
                        <select id="fellow_edit_title" name="fellow_title" class="form-control">
                          <option value="">Alege</option>
                          <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/passenger_title_options'); ?>
                          <?php themeFunctions::loadAddons(__FILE__ . '/passenger_title_options'); ?>
                        </select>
                        <?php } else { ?>
                        <div id="fellow_edit_title" class="form-control" readonly>
                          &nbsp;
                        </div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_edit_firstname" class="<?php echo $label_class; ?>"><?php echo lang('firstname_field_label/html'); ?></label>
                      <div class="<?php echo $value_class; ?>">
                        <?php if($can_write){ ?>
                        <input id="fellow_edit_firstname" maxlength="255" type="text" name="fellow_firstname" placeholder="<?php echo lang('firstname_field_placeholder'); ?>" class="form-control" value="" required />
                        <?php } else { ?>
                        <div id="fellow_edit_firstname" class="form-control" readonly>&nbsp;</div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_edit_lastname" class="<?php echo $label_class; ?>"><?php echo lang('lastname_field_label/html'); ?></label>
                      <div class="<?php echo $value_class; ?>">
                        <?php if($can_write){ ?>
                        <input id="fellow_edit_lastname" maxlength="255" type="text" name="fellow_lastname" placeholder="<?php echo lang('lastname_field_placeholder'); ?>" class="form-control" value="" />
                        <?php } else { ?>
                        <div id="fellow_edit_lastname" class="form-control" readonly>&nbsp;</div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_edit_birth_date" class="<?php echo $label_class; ?>"><?php echo lang('birth_date_field_label/html'); ?></label>
                      <div class="<?php echo $value_class; ?>">
                        <?php if($can_write){ ?>
                        <input id="fellow_edit_birth_date" maxlength="10" type="text" name="fellow_birth_date" placeholder="<?php echo lang('birth_date_field_placeholder'); ?>" class="form-control input-birth_date" value="" required />
                        <?php } else { ?>
                        <div id="fellow_edit_birth_date" class="form-control" readonly>&nbsp;</div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_edit_country" class="<?php echo $label_class; ?>">Nationalitate</label>
                      <div class="<?php echo $value_class; ?>">
                        <?php if($can_write){ ?>
                        <select id="fellow_edit_country" name="fellow_country" class="form-control">
                          <option value="">Alege</option>
                          <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/fellow_country_options'); ?>
                          <?php themeFunctions::loadAddons(__FILE__ . '/fellow_country_options'); ?>
                        </select>
                        <?php } else { ?>
                        <div id="fellow_edit_country" class="form-control" readonly>&nbsp;</div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_edit_passport_number" class="<?php echo $label_class; ?>">Numar pasaport</label>
                      <div class="<?php echo $value_class; ?>">
                        <?php if($can_write){ ?>
                        <input id="fellow_edit_passport_number" maxlength="255" type="text" name="fellow_passport_number" placeholder="Numar pasaport" class="form-control" value="" />
                        <?php } else { ?>
                        <div id="fellow_edit_passport_number" class="form-control" readonly>&nbsp;</div>
                        <?php } ?>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <?php if($can_write){ ?>
                  <button type="submit" form="profileFellowEdit" class="btn btn-success">Salvare</button>
                  <?php } ?>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
                </div>
              </div>
            </div>
          </div>
          <div id="result_profileFellows" class="form-group"></div>
          <?php if($can_write){ ?>
          <div id="fellowAddModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Adaugare Insotitor</h5>
                </div>
                <div class="modal-body">
                  <form id="profileFellowAdd" name="profileFellowAdd" method="POST" onsubmit="return false;">
                    <div class="form-group row">
                      <label for="fellow_add_title" class="<?php echo $label_class; ?>">Titlu</label>
                      <div class="<?php echo $value_class; ?>">
                        <select id="fellow_add_title" name="fellow_title" class="form-control">
                          <option value="">Alege</option>
                          <?php themeFunctions::loadModule('helpers/titles/select_option',__FILE__ . '/passenger_title_options'); ?>
                          <?php themeFunctions::loadAddons(__FILE__ . '/passenger_title_options'); ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_add_firstname" class="<?php echo $label_class; ?>"><?php echo lang('firstname_field_label/html'); ?></label>
                      <div class="<?php echo $value_class; ?>">
                        <input id="fellow_add_firstname" maxlength="255" type="text" name="fellow_firstname" placeholder="<?php echo lang('firstname_field_placeholder'); ?>" class="form-control" value="" required />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_add_lastname" class="<?php echo $label_class; ?>"><?php echo lang('lastname_field_label/html'); ?></label>
                      <div class="<?php echo $value_class; ?>">
                        <input id="fellow_add_lastname" maxlength="255" type="text" name="fellow_lastname" placeholder="<?php echo lang('lastname_field_placeholder'); ?>" class="form-control" value="" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_add_birth_date" class="<?php echo $label_class; ?>"><?php echo lang('birth_date_field_label/html'); ?></label>
                      <div class="<?php echo $value_class; ?>">
                        <input id="fellow_add_birth_date" maxlength="10" type="text" name="fellow_birth_date" placeholder="<?php echo lang('birth_date_field_placeholder'); ?>" class="form-control input-birth_date" value="" required />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_add_country" class="<?php echo $label_class; ?>">Nationalitate</label>
                      <div class="<?php echo $value_class; ?>">
                        <select id="fellow_add_country" name="fellow_country" class="form-control">
                          <option value="">Alege</option>
                          <?php themeFunctions::loadModule('helpers/countries/select_option',__FILE__ . '/fellow_add_country_options', array('selected'=>'RO')); ?>
                          <?php themeFunctions::loadAddons(__FILE__ . '/fellow_add_country_options'); ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fellow_add_passport_number" class="<?php echo $label_class; ?>">Numar pasaport</label>
                      <div class="<?php echo $value_class; ?>">
                        <input id="fellow_add_passport_number" maxlength="255" type="text" name="fellow_passport_number" placeholder="Numar pasaport" class="form-control" value="" />
                      </div>
                    </div>
                  </form>
                  <p class="text-danger">Dupa adaugare, nu uitati sa dati clic pe butonul de Salvare</p>
                </div>
                <div class="modal-footer">
                  <button type="submit" form="profileFellowAdd" class="btn btn-success"><i class="fa fa-plus"></i> Adaugare</button>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>