<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 textExp">
      <div class="container">
        <div class="row">
          <div class="col-0 col-sm-0 col-md-2 col-lg-3"></div>
          <h2 class="col-sm-12 col-md-8 col-lg-6  rounded-bottom">Calatorii sigure si autentice. De <?php echo (date('Y') - 1999) ?> ani</h2>
          <div class="col-0 col-sm-0 col-md-2 col-lg-3"></div>
          <div class="col-12 col-sm-4 brandV">
            <p><i class="fa fa-mortar-board"></i> <br /><?php echo (date('Y') - 1999) ?> ani de excelenta in calatorii</p>
          </div>
          <div class="col-12 col-sm-4 brandV">   
            <p><img src="<?php echo $this->theme_url; ?>assets/images/egencia.png" alt="partener Egencia Romania" class="mb-2" /> </p>
          </div>
          <div class="col-12 col-sm-4 brandV">       
            <p><i class="fa fa-sign-language"></i>  <br />expertiza considerabila pentru turismul intern</p>
          </div>
          <div class="col-12 col-sm-4 brandV">
            <p><i class="fa fa-users"></i> <br />peste 1.000.000 de clienti fericiti</p>
          </div>
          <div class="col-12 col-sm-4 brandV">   
            <p><i class="fa fa-hotel"></i> <br />peste 400.000 de hoteluri ofertate in fiecare an</p>
          </div>
          <div class="col-12 col-sm-4 brandV">       
            <p><i class="fa fa-send-o"></i>  <br />peste 30.000 de bilete de avion vandute anual</p>
          </div>
        </div>
      </div>
    </div>
  </div>              
</div>
<?php /*
<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/y68WLQEHzhk?autoplay=1&loop=1&showinfo=0&rel=0&mode=opaque&autohide=1&wmode=transparent&controls=0&playlist=y68WLQEHzhk' frameborder='0' allow="" allowfullscreen></iframe></div>
*/ ?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>