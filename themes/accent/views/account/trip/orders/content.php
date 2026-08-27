<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="container mt-3 mb-5">
  <h3>Istoric comenzi</h3>
  <table class="table table-bordered table-hover table-striped">
    <thead>
      <tr>
        <th class="text-center">ID</th>
        <th class="text-center">Status</th>
        <th class="text-center">Data</th>
        <th class="text-center">Tip</th>
        <th class="text-center">Plata</th>
        <th class="text-center">Discount</th>
        <th class="text-center">Total</th>
      </tr>
    </thead>
    <tbody id="orders_list">
      <tr id="order_list_message">
        <td colspan="6" class="text-center">
          <h3 class="mb-0"><i class="fa fa-spinner fa-spin fa-pulse"></i> Se incarca ...</h3>
        </td>
      </tr>
    </tbody>
  </table>
  <nav aria-label="Pagination">
    <ul class="pagination justify-content-center justify-content-md-end"></ul>
  </nav>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>