<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="layout-js-accordion" role="tablist" aria-multiselectable="true">
  <div class="card">
    <div class="card-header" role="tab" id="heading-layout-js-links">
      <h5 class="mb-0">
        <a data-toggle="collapse" data-parent="#layout-js-accordion" href="#collapse-layout-js-links" aria-expanded="true" aria-controls="collapse-layout-js-links">
          Links
        </a>
      </h5>
    </div>
    <div id="collapse-layout-js-links" class="collapse show" role="tabpanel" aria-labelledby="heading-layout-js-links">
      <div class="card-block">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" role="tab" id="heading-layout-js-custom">
      <h5 class="mb-0">
        <a class="collapsed" data-toggle="collapse" data-parent="#layout-js-accordion" href="#collapse-layout-js-custom" aria-expanded="false" aria-controls="collapse-layout-js-custom">
          custom
        </a>
      </h5>
    </div>
    <div id="collapse-layout-js-custom" class="collapse" role="tabpanel" aria-labelledby="heading-layout-js-custom">
      <div class="card-block">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>