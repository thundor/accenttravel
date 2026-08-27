<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="modal fade" id="modal_home_popup" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="d-flex flex-column justify-content-center" style="height:100%;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body p-0">
          <button type="button" class="close" data-dismiss="modal" aria-label="Inchide">
            <span aria-hidden="true">&times;</span>
          </button>
          <?php /* <a href="<?php echo site_url('/inregistrare-concurs'); ?>"> */ ?>
          <?php /* <a id="imagine_home_popup_link" target="_BLANK" href="http://voting.expremio.com/excellenceawards/publicVote">
            <img data-src="<?php echo base_url(''); ?>resources/bannere/banner_homepage.png" alt="Votați-ne aici până pe 5 Mai" id="imagine_home_popup" class="img-fluid">
          </a> */ ?>
          <a id="imagine_home_popup_link" target="_BLANK" href="<?php echo site_url('/inregistrare-concurs-vacanta') ?>">
            <img data-src="/resources/bannere/Accenttravel_concurs_iulie_2019_popup.png" alt="Inregistrare la concurs" id="imagine_home_popup" class="img-fluid">
          </a>
          <?php /* </a> */ ?>
        </div>
        <div class="modal-footer p-0">
          <label class="input-group m-0">
            <span class="input-group-addon">
              <input type="checkbox" id="imagine_home_popup_dont_show" />
            </span>
            <span class="form-control">
              Nu mai afisa acest popup
            </span>
          </label>
        </div>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>