<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="layout-css-accordion" role="tablist" aria-multiselectable="true">
  <div class="card">
    <div class="card-header" role="tab" id="heading-layout-css-links">
      <h5 class="mb-0">
        <a data-toggle="collapse" data-parent="#layout-css-accordion" href="#collapse-layout-css-links" aria-expanded="true" aria-controls="collapse-layout-css-links">
          Links
        </a>
      </h5>
    </div>
    <div id="collapse-layout-css-links" class="collapse show" role="tabpanel" aria-labelledby="heading-layout-css-links">
      <div class="card-block">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" role="tab" id="heading-layout-css-custom">
      <h5 class="mb-0">
        <a class="collapsed" data-toggle="collapse" data-parent="#layout-css-accordion" href="#collapse-layout-css-custom" aria-expanded="false" aria-controls="collapse-layout-css-custom">
          custom
        </a>
      </h5>
    </div>
    <div id="collapse-layout-css-custom" class="collapse" role="tabpanel" aria-labelledby="heading-layout-css-custom">
      <div class="card-block">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>