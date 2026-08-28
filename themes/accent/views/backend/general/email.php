<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('general/options'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/email/scripts.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/email/meta.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/email/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/email/page_title.php'); ?>
<?php
$label_size = array();
$label_size['xl'] = 3;
$label_size['lg'] = 4;
$label_size['md'] = 3;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach ($label_size as $k => $v) {
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12 - $v : 12);
}
$can_write = $this->_ci->user->can('backend-config-save');
$data = $this->view_data;
$driver = !empty($data['email_driver']) ? $data['email_driver'] : 'o365';
?>
<section class="forms">
  <div class="col-12">
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Setări email Office 365</h2>
          </div>
          <div class="card-block">
            <div id="result_emailForm" class="form-group"></div>
            <form id="emailForm" name="emailForm" class="email_settings_form" action="<?php echo site_url('backend/general/email/save'); ?>" method="POST">
              <?php if ($this->_ci->config->item('csrf_protection') === TRUE) { ?>
              <input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>

              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Metodă de trimitere</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if ($can_write) { ?>
                  <div class="i-checks">
                    <input id="driver_o365" type="radio" value="o365" name="email_driver" <?php echo $driver === 'o365' ? 'checked' : ''; ?> class="form-control-custom radio-custom js-email-driver">
                    <label for="driver_o365">Microsoft Graph (OAuth2)</label>
                  </div>
                  <div class="i-checks">
                    <input id="driver_smtp" type="radio" value="smtp" name="email_driver" <?php echo $driver === 'smtp' ? 'checked' : ''; ?> class="form-control-custom radio-custom js-email-driver">
                    <label for="driver_smtp">SMTP (Office 365 / relay)</label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($driver); ?></div>
                  <?php } ?>
                </div>
              </div>

              <div class="js-panel-o365" <?php echo $driver === 'o365' ? '' : 'style="display:none"'; ?>>
                <h4 class="h6 mt-3">A) Microsoft Graph — OAuth2</h4>
                <p class="text-muted small">
                  Necesită App Registration în Entra ID, permisiune <code>Mail.Send</code> (Application) și admin consent.
                  <strong>Redirect URI nu este necesar</strong> (client credentials).
                </p>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Stare configurație</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if (!empty($data['o365_configured'])) { ?>
                    <span class="badge badge-success">Credențiale complete</span>
                    <?php } else { ?>
                    <span class="badge badge-warning">Lipsesc Tenant ID / Client ID / Client Secret</span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Tenant ID</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <input type="text" name="o365_tenant_id" class="form-control" value="<?php echo htmlspecialchars($data['o365_tenant_id']); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($data['o365_tenant_id']); ?>&nbsp;</div>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Client ID</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <input type="text" name="o365_client_id" class="form-control" value="<?php echo htmlspecialchars($data['o365_client_id']); ?>" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($data['o365_client_id']); ?>&nbsp;</div>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Client Secret</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <input type="password" name="o365_client_secret" class="form-control" value="" autocomplete="new-password" placeholder="<?php echo !empty($data['has_client_secret']) ? '•••••••• (lăsați gol pentru a păstra secretul actual)' : 'Client Secret din Azure'; ?>" />
                    <?php if (!empty($data['has_client_secret'])) { ?>
                    <small class="text-muted">Secret salvat în baza de date. Completați doar dacă doriți să îl înlocuiți.</small>
                    <?php } ?>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo !empty($data['has_client_secret']) ? '••••••••' : '(nesetat)'; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <div class="js-panel-smtp" <?php echo $driver === 'smtp' ? '' : 'style="display:none"'; ?>>
                <h4 class="h6 mt-3">B) SMTP — Office 365 / relay</h4>
                <p class="text-muted small">
                  Variante uzuale:
                  <br />• Autentificat: <code>smtp.office365.com</code>, port <code>587</code>, TLS, utilizator + parolă (sau app password).
                  <br />• Relay / Direct send: ex. <code>domeniu.mail.protection.outlook.com</code>, port <code>25</code>, fără autentificare (IP pe allowlist / connector).
                </p>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Stare configurație</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if (!empty($data['smtp_configured'])) { ?>
                    <span class="badge badge-success">Host și port setate</span>
                    <?php } else { ?>
                    <span class="badge badge-warning">Completați hostul și portul SMTP</span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Host SMTP</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <input type="text" name="smtp_host" class="form-control" value="<?php echo htmlspecialchars($data['smtp_host']); ?>" placeholder="smtp.office365.com sau *.mail.protection.outlook.com" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($data['smtp_host']); ?>&nbsp;</div>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Port SMTP</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <input type="number" name="smtp_port" class="form-control" value="<?php echo htmlspecialchars($data['smtp_port']); ?>" placeholder="587 sau 25" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($data['smtp_port']); ?>&nbsp;</div>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Criptare</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <select name="smtp_crypto" class="form-control">
                      <option value="tls" <?php echo $data['smtp_crypto'] === 'tls' ? 'selected' : ''; ?>>TLS (recomandat pentru port 587)</option>
                      <option value="ssl" <?php echo $data['smtp_crypto'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                      <option value="none" <?php echo ($data['smtp_crypto'] === 'none' || $data['smtp_crypto'] === '') ? 'selected' : ''; ?>>Fără (uzual pentru port 25 / relay)</option>
                    </select>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($data['smtp_crypto']); ?>&nbsp;</div>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Utilizator SMTP</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <input type="text" name="smtp_user" class="form-control" value="<?php echo htmlspecialchars($data['smtp_user']); ?>" placeholder="opțional — lăsați gol pentru relay fără autentificare" autocomplete="off" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($data['smtp_user']); ?>&nbsp;</div>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="<?php echo $label_class; ?>">Parolă SMTP</label>
                  <div class="<?php echo $value_class; ?>">
                    <?php if ($can_write) { ?>
                    <input type="password" name="smtp_pass" class="form-control" value="" autocomplete="new-password" placeholder="<?php echo !empty($data['has_smtp_pass']) ? '•••••••• (lăsați gol pentru a păstra parola actuală)' : 'opțional'; ?>" />
                    <?php if (!empty($data['has_smtp_pass'])) { ?>
                    <small class="text-muted">Parolă salvată în baza de date. Completați doar dacă doriți să o înlocuiți.</small>
                    <?php } ?>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo !empty($data['has_smtp_pass']) ? '••••••••' : '(nesetată)'; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <hr />
              <h4 class="h6">Expeditor comun</h4>

              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Email expeditor</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if ($can_write) { ?>
                  <input type="email" name="email_default_from" class="form-control" value="<?php echo htmlspecialchars($data['email_default_from']); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['email_default_from']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Nume expeditor</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if ($can_write) { ?>
                  <input type="text" name="email_default_from_name" class="form-control" value="<?php echo htmlspecialchars($data['email_default_from_name']); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['email_default_from_name']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">BCC implicit</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if ($can_write) { ?>
                  <input type="text" name="email_default_bcc" class="form-control" value="<?php echo htmlspecialchars($data['email_default_bcc']); ?>" placeholder="email1@..., email2@..." />
                  <small class="text-muted">Adrese separate prin virgulă.</small>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['email_default_bcc']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group row">
                <label class="<?php echo $label_class; ?>">Email de test</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if ($can_write) { ?>
                  <input type="email" name="email_dev_to" class="form-control" value="<?php echo htmlspecialchars($data['email_dev_to']); ?>" />
                  <small class="text-muted">Folosit ca destinatar implicit pentru teste și ca redirect în mediul non-production.</small>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($data['email_dev_to']); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Test integrare</h2>
          </div>
          <div class="card-block">
            <div id="result_emailTest" class="form-group"></div>
            <p class="text-muted">1) Testați conexiunea (token Graph sau banner SMTP). 2) Trimiteți un email real de test.</p>

            <?php if ($can_write) { ?>
            <form id="emailTestConnectionForm" class="email_test_form mb-3" action="<?php echo site_url('backend/general/email/test_connection'); ?>" method="POST">
              <?php if ($this->_ci->config->item('csrf_protection') === TRUE) { ?>
              <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <button type="submit" class="btn btn-info btn-block">
                <i class="fa fa-plug"></i> Testează conexiunea
              </button>
            </form>

            <form id="emailTestSendForm" class="email_test_form" action="<?php echo site_url('backend/general/email/test_send'); ?>" method="POST">
              <?php if ($this->_ci->config->item('csrf_protection') === TRUE) { ?>
              <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
              <?php } ?>
              <div class="form-group">
                <label>Destinatar test</label>
                <input type="email" name="test_to" class="form-control" required value="<?php echo htmlspecialchars($data['email_dev_to']); ?>" />
              </div>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-paper-plane"></i> Trimite email de test
              </button>
            </form>
            <?php } else { ?>
            <p class="text-warning">Aveți nevoie de permisiunea de salvare setări pentru teste.</p>
            <?php } ?>

            <hr />
            <p class="small text-muted mb-0">
              Salvați setările înainte de test. Testele folosesc metoda selectată (Graph sau SMTP).
            </p>
          </div>
        </div>

        <div class="card mt-3">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display">Documentație pe scurt</h2>
          </div>
          <div class="card-block small">
            <p><strong>Graph (OAuth2)</strong> — modern, fără parolă de mailbox în aplicație. Cere Tenant ID, Client ID, Client Secret.</p>
            <p><strong>SMTP autenticat</strong> — <code>smtp.office365.com:587</code> + TLS + utilizator/parolă.</p>
            <p class="mb-0"><strong>SMTP relay</strong> — host tip <code>*.mail.protection.outlook.com</code>, port 25, fără user/parolă; IP-ul serverului trebuie permis în connectorul Exchange.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>
