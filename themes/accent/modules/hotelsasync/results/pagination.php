<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="text-center">
  <nav aria-label="Navigare pagini">
    <ul class="pagination">
      <?php /*
      <li class="page-item">
        <a class="page-link" href="#" aria-label="Inapoi">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Inapoi</span>
        </a>
      </li>
      <li class="page-item"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">3</a></li>
      <li class="page-item"><a class="page-link" href="#">4</a></li>
      <li class="page-item"><a class="page-link" href="#">5</a></li>
      <li class="page-item">
        <a class="page-link" href="#" aria-label="Inainte">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Inainte</span>
        </a>
      </li>
       */ ?>
    </ul>
  </nav>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>