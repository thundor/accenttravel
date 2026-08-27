<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="form-group row">
  <label class="<?php echo $label_class; ?>">Locatie:</label>
  <div class="<?php echo $value_class; ?>">
    <div class="input-group">
      <input type="text" placeholder="Introduceti locatia apoi dati click pe + Adauga" class="location form-control" />
      <span class="input-group-btn">
        <button type="button" disabled class="addlocation btn btn-success"><i class="fa fa-plus"></i> Adauga</button>
      </span>
    </div>
    <small class="text-muted">Locatiile adaugate vor fi listate mai jos.</small>
  </div>
  <div class="col-12">
    <table class="table table-bordered table-hover ac">
      <thead>
        <tr>
          <th class="text-center" width="1%">#</th>
          <th>Locatie</th>
          <th class="text-center" width="1%">Actiuni</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>