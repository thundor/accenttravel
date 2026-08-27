<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<input type="hidden" name="prices" />
<div class="form-group row">
  <div class="col-12">
    <table class="table table-bordered table-hover ac prices-table">
      <thead>
        <tr>
          <th class="text-center" width="1%">#</th>
          <th>Interval</th>
          <th>Pret</th>
          <th class="text-center contains-form-control" width="1%">
            <div class="btn-group btn-block">
              <button type="button" class="add-price btn btn-primary btn-block"><i class="fa fa-plus"></i></button>
            </div>
          </th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>