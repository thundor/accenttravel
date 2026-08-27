<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="hotel-filter hotel-stars-filter">
  <h5 class="subTitleFilter"><i class="fa fa-star-o"></i> Nume vacanta</h5>
  <div class="hotel-filters-content mb-2">
    <form onsubmit="return false;">
      <div class="input-group">
        <input type="text" maxlength="255" id="package_filter_by_name" name="Name" placeholder="cautare..." class="form-control"/>
        <span class="input-group-btn">
          <button type="submit" id="package_filter_by_name_button" class="btn btn-success"><i class="fa fa fa-search"></i></button>
        </span>
      </div>
    </form>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>