<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/subscribe/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/subscribe/scripts.php'); ?>
<div class="container">
  <br />
  <br />
  <br />
  <section class="text-center">
    <div class="intro_title">
      <h1 class="animated fadeInDown">Abonare efectuata cu succes!</h1>
      <p class="animated fadeInDown">Va multumim pentru abonarea la newsletterul nostru. Astfel, acum vei fi informat cu privire la vacantele si ofertele noastre turistice, la stiri si noutati din lumea calatoriilor in cele mai frumoase si dorite destinatii.</p>
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