<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/unsubscribe/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/unsubscribe/scripts.php'); ?>
<div class="container">
  <br />
  <br />
  <br />
  <section class="text-center">
    <div class="intro_title">
      <h1 class="animated fadeInDown">Dezabonare efectuata cu succes.</h1>
      <p class="animated fadeInDown">Ne pare rau ca ati optat sa va dezabonati de la newsletterul nostru. Nu va vom mai trimite informari cu privire la vacantele si ofertele noastre turistice, la stiri si noutati din lumea calatoriilor in cele mai frumoase si dorite destinatii.</p>
      <p class="animated fadeInDown">In cateva momente veti fi redirectionat automat catre prima pagina. Alternativ, puteti da click pe linkul de mai jos.</p>
      <a href="<?php echo base_url(); ?>" class="animated fadeInUp btn iosbtn">Înapoi la prima pagină</a>
    </div>
  </section>
  <br />
  <br />
  <br />
  <br />
  <br />
</div>
<?php themeFunctions::debugFileLine('end'); ?>