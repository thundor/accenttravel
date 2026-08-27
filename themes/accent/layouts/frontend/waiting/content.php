<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="container">
  <div class="row">
    <div class="col-12 mt-5 mb-3 text-center" id="searchS">
      <div id="searchSearching">
        <h3 class="familyLight">Căutăm printre mii de oferte pentru tine!</h3>
        <p class="mt-5"><i class="fa fa-spinner fa-pulse fa-5x fa-fw blueLight"></i></p>
        <p class="mt-5 mb-0 subTitleFilter text-center"><strong>NOTĂ</strong></p>
      </div>
      <div id="searchNoResults" style="display:none;">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <hr />
              <h2 class="flight-warning text-center">Ne pare rau, nu au fost găsite rezultate.</h2>
              <hr />
              <h4 class="text-center">In cateva secunde veti fi redirectionat automat inapoi catre pagina de cautare sau dati <a href="javascript:history.back()">clic aici</a></h4>
            </div>
          </div>
        </div>
      </div>
      <p class="blueBackW">Vacantele turistice care au disponibilitate confirmata de hotel pot fi platite online, prin card bancar, rezervarea realizandu-se pe loc.<br> Vacantele turistice care nu au disponibilitate confirmata trebuie verificate de catre un agent Accent Travel &amp; Event si acesta va confirma disponibilitatea si rezervarea. Pentru cererile realizate pana in ora 15:00 confirmarea va avea loc chiar in aceeasi zi, iar pentru cererile inregistrate dupa ora 15:00, acestea vor fi confirmate in prima zi lucratoare dupa inregistrarea cererii.</p>
    </div>
  </div>
</div>
<div id="page-content">
  <?php echo $this->content(); ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>