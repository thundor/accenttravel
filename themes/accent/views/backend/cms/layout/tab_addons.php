<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="layout-addons-accordion" role="tablist" aria-multiselectable="true">
  <div class="card">
    <div class="card-header" role="tab" id="heading-layout-addons-links">
      <h5 class="mb-0">
        <a data-toggle="collapse" data-parent="#layout-addons-accordion" href="#collapse-layout-addons-links" aria-expanded="true" aria-controls="collapse-layout-addons-links">
          Links
        </a>
      </h5>
    </div>
    <div id="collapse-layout-addons-links" class="collapse show" role="tabpanel" aria-labelledby="heading-layout-addons-links">
      <div class="card-block">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" role="tab" id="heading-layout-addons-custom">
      <h5 class="mb-0">
        <a class="collapsed" data-toggle="collapse" data-parent="#layout-addons-accordion" href="#collapse-layout-addons-custom" aria-expanded="false" aria-controls="collapse-layout-addons-custom">
          custom
        </a>
      </h5>
    </div>
    <div id="collapse-layout-addons-custom" class="collapse" role="tabpanel" aria-labelledby="heading-layout-addons-custom">
      <div class="card-block">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>