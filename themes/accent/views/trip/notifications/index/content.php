<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="container mt-3 mb-5">
  <h3>Notificari active</h3>
  <table class="table table-bordered table-hover table-striped">
    <thead>
      <tr>
        <th class="text-center">Titlu</th>
        <th class="text-center">Tip</th>
        <th class="text-center">Pret</th>
        <?php /* <th class="text-center">Pret nou</th>
        <th class="text-center">Reducere</th> */ ?>
        <th class="text-center">Ultima verificare</th>
      </tr>
    </thead>
    <tbody id="notifications_list">
      <tr id="notification_list_message">
        <td colspan="6" class="text-center">
          <h3 class="mb-0"><i class="fa fa-spinner fa-spin fa-pulse"></i> Se incarca ...</h3>
        </td>
      </tr>
    </tbody>
  </table>
  <nav aria-label="Pagination">
    <ul class="pagination justify-content-center justify-content-md-end"></ul>
  </nav>
  <div class="d-flex flex-row-reverse">
    <a class="btn btn-danger" href="<?php echo $this->view_data['delete_all_link']; ?>"><i class="fa fa-trash"></i> Elimina toate notificarile</a>
  </div>
  <p>Pentru rezultatele afisate vi se va trimite email daca pretul acestora va scadea cu cel putin 10%.</p>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>