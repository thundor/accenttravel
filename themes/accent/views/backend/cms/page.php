<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('cms_page'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('jquery-ui'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/page/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/page/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/page/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/page/meta.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
$value_offset_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$page = $this->view_data['page'];

$editing = $page->page_id !== 0;
$can_write = $this->_method !='view';
$languages = array('ro');
?>
<section class="forms">
  <div class="col-12">
    <div id="result_pageForm"></div>
    <form id="pageForm" name="pageForm" action="<?php echo site_url('backend/cms/pages/save'); ?>" method="POST" onsubmit="return false;">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="task" name="task" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="id" value="<?php echo $page->page_id; ?>" />
      <?php } ?>
      <?php } ?>
      <div class="card">
        <div class="card-header pt-1 pr-3 pl-3">
          <ul class="nav nav-tabs card-header-tabs nav-justified">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#general_tab" role="tab" aria-controls="general_tab">
                <strong><i class="fa fa-user"></i> Informatii pagina</strong>
              </a>
            </li>
          </ul>
        </div>
        <div class="tab-content card-block">
          <div class="tab-pane active" id="general_tab" role="tabpanel">
            <div class="form-group row">
              <label for="status" class="<?php echo $label_class; ?>">Status pagina</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <div class="i-checks">
                  <input id="status_active" type="radio" value="1" name="status" <?php echo $page->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                  <label for="status_active"><?php echo lang('option_active'); ?></label>
                </div>
                <div class="i-checks">
                  <input id="status_inactive" type="radio" value="0" name="status" <?php echo !$page->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                  <label for="status_inactive"><?php echo lang('option_inactive'); ?></label>
                </div>
                <?php } else { ?>
                <div class="form-control" readonly><?php echo $page->status == 1 ? lang('option_active') : lang('option_inactive'); ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="blog" class="<?php echo $label_class; ?>">Pagina blog</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <div class="i-checks">
                  <input id="blog_active" type="radio" value="1" name="blog" <?php echo $page->blog ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                  <label for="blog_active"><?php echo lang('option_active'); ?></label>
                </div>
                <div class="i-checks">
                  <input id="blog_inactive" type="radio" value="0" name="blog" <?php echo !$page->blog ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                  <label for="blog_inactive"><?php echo lang('option_inactive'); ?></label>
                </div>
                <?php } else { ?>
                <div class="form-control" readonly><?php echo $page->blog == 1 ? lang('option_active') : lang('option_inactive'); ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="sort_order" class="<?php echo $label_class; ?>">Ordine afisare</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="sort_order" type="number" step="1" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($page->sort_order); ?>" />
                <?php } else { ?>
                <div class="form-control" readonly><?php echo $page->sort_order; ?></div>
                <?php } ?>
              </div>
            </div>
            <?php foreach($languages as $language){ 
              $language_data = isset($page->languages[$language]) ? $page->languages[$language] : (object)array(
                'title' => '',
                'layout' => '',
                'slug' => '',
                'description' => '',
                'keywords' => '',
                'content' => '',
                'route' => null,
                'params' => null,
              );
            ?>
            <div class="form-group row">
              <label for="page_title" class="<?php echo $label_class; ?>">Nume pagina (Titlu tab)</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="page_title" type="text" maxlength="255" name="languages[<?php echo $language; ?>][title]" class="form-control" value="<?php echo htmlspecialchars($language_data->title); ?>" />
                <?php } else { ?>
                <div id="page_title" class="form-control" readonly><?php echo htmlspecialchars($language_data->title); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="page_slug" class="<?php echo $label_class; ?>">SEO Url (Alias)</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="page_slug" type="text" maxlength="255" name="languages[<?php echo $language; ?>][slug]" class="form-control" value="<?php echo htmlspecialchars($language_data->slug); ?>" />
                <?php } else { ?>
                <div id="page_slug" class="form-control" readonly><?php echo htmlspecialchars($language_data->slug); ?>&nbsp;</div>
                <?php } ?>
				  <?php if($editing) { ?>
				  <button type="button" class="btn btn-primary" onclick="window.open('/' + $('#page_slug').val() + '?newux=1', '_blank').focus();">Catre pagina frontend</button>
				  <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="page_keywords" class="<?php echo $label_class; ?>">META cuvinte cheie</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <textarea id="page_keywords" name="languages[<?php echo $language; ?>][keywords]" class="form-control" ><?php echo htmlspecialchars($language_data->keywords); ?></textarea>
                <?php } else { ?>
                <div id="page_keywords" class="form-control" readonly><?php echo htmlspecialchars($language_data->keywords); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="page_description" class="<?php echo $label_class; ?>">META Descriere</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <textarea id="page_description" name="languages[<?php echo $language; ?>][description]" class="form-control" ><?php echo htmlspecialchars($language_data->description); ?></textarea>
                <?php } else { ?>
                <div id="page_description" class="form-control" readonly><?php echo htmlspecialchars($language_data->description); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="page_layout" class="<?php echo $label_class; ?>">Sablon</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="page_layout" type="text" maxlength="255" name="languages[<?php echo $language; ?>][layout]" class="form-control" value="<?php echo htmlspecialchars($language_data->layout); ?>" />
                <?php } else { ?>
                <div id="page_layout" class="form-control" readonly><?php echo htmlspecialchars($language_data->layout); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="page_route" class="<?php echo $label_class; ?>">URL implicit aplicatie</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <input id="page_route" type="text" maxlength="255" name="languages[<?php echo $language; ?>][route]" class="form-control" value="<?php echo htmlspecialchars($language_data->route . (isset($language_data->params) ? '?' . $language_data->params : '')); ?>" />
                <?php } else { ?>
                <div id="page_route" class="form-control" readonly><?php echo htmlspecialchars($language_data->route); ?>&nbsp;</div>
                <?php } ?>
                <p class="mb-0">Pentru pagini statice obisnuite, nu este necesara completarea acestui camp. <a href="#modal_info_route" data-toggle="modal">Mai multe informatii</a></p>
                
                <div class="modal fade" id="modal_info_route" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="d-flex flex-column justify-content-center" style="height:100%;pointer-events:none;">
                    <div class="modal-dialog modal-lg" role="document" style="max-height: 90%;pointer-events:auto;">
                      <div class="modal-content">
                        <div class="modal-body">
                          <button type="button" class="close custom-close" data-dismiss="modal" aria-label="Inchide">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <p class="mb-0">Tipul paginii (static/dinamic/implicit) este determinat automat dupa valoarea introdusa in campul <b>URL implicit aplicatie</b>:</p>
                          <ul class="ml-5 mb-3">
                            <li>Necompletat: Pagina <b>statica</b></i>
                            <li>Cale fara parametri (Ex: trip/flight): Pagina <b>implicita</b></i>
                            <li>Cale cu parametri (Ex: trip/flight?n=1): Pagina <b>dinamica</b></i>
                          </ul>
                          <p>Pentru a modifica URL-ul unei pagini interne a aplicatiei, completati acest camp cu <b>calea URL</b> de dupa domeniu (Ex: pentru URL-ul <span class="text-muted"><?php echo base_url(''); ?></span><strong>trip/flights</strong> completati acest camp cu <strong>trip/flights</strong>).</p>
                          <p>Pentru a pastra parametrii de cautare in vederea auto-precompletarii la accesarea paginii, accesati pagina cu motorul de cautare (Ex: <em>trip/hotelsasync</em>, <em>trip/flights</em>, <em>trip/citybreaks</em>, <em>trip/packages</em>), stabiliti toate campurile necesare pentru realizarea unei cautari valide, apoi copiati in acest camp tot continutul casetei speciale situata langa butonul de cautare.</p>
                          <p>Exemplu: <b>trip/flights?<span class="text-danger">n=1</span>&amp;<span class="text-info">origin=LYON</span>&amp;<span class="text-warning">destination=BARCELONA</span>&amp;<span class="text-success">sdate=2018-03-22</span>&amp;<span class="text-primary">edate=2018-03-27</span>&amp;a=1&amp;class=1</b>. In acest exemplu, pagina va deschide formularul de cautare de zboruri, dinspre <b class="text-info">Lyon</b> catre <b class="text-warning">Barcelona</b> <b>dus-intors</b>, intre datele <b class="text-success">2018-03-22</b> si <b class="text-primary">2018-03-27</b>, <b>clasa Economy</b>, <b>1 adult</b>. Parametrul "<b class="text-danger">n</b>" forteaza inceperea cautarii la incarcarea paginii; daca nu doriti acest lucru, puteti sterge manual acest parametru.</p>
                          <p>Daca in parametrii de cautare exista date calendaristice (<b class="text-success">sdate</b>, <b class="text-primary">edate</b>), va vor fi afisate optiuni speciale pentru fiecare din acestea.</p>
                          <p>Puteti opta pentru data calendaristica exacta, sau dinamica (recurent). Parametrii <b class="text-success">sdate</b> si <b class="text-primary">edate</b> functioneaza in tandem cand valorile acestora sunt dinamice si anume: parametrul <b class="text-success">sdate</b> este relativ la ziua curenta, iar <b class="text-primary">edate</b> este relativ la parametrul <b class="text-success">sdate</b>.</p>
                          <p class="mb-0">Pentru paginile cu formular de cautare, parametrii <b class="text-info">origin</b> respectiv <b class="text-warning">destination</b> pot fi suprascrisi direct din url in ordinea in care au fost mentionate.</p>
                          <ul class="ml-5 mb-3">
                            <li>trip/hotelsasync/<b class="text-info">bucharest</b></i>
                            <li>trip/citybreaksasync/<b><span class="text-info">iasi</span>/<span class="text-warning">madrid</span></b></i>
                            <li>trip/flights/<b><span class="text-info">london</span>/<span class="text-warning">paris</span></b></i>
                          </ul>
                          <p class="mb-0">La vacante difera caile sunt de forma:</p>
                          <ul class="ml-5 mb-3">
                            <li>trip/packages/<b class="text-info">litoral-romania</b></i>
                            <li>trip/packages/<b>oras/<span class="text-warning">mamaia</span></b></i>
                          </ul>
                          <p>Aceasta structura este valabila si pentru paginile implicite/dinamice. Ex. hoteluri/brasov, zboruri/paris/london</p>
                          <p>Aceste exemple reflecta modul de generare automata de linkuri dinamice pentru hoteluri / citybreakuri / zboruri / vacante.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
							<div class="<?php echo $value_class . $value_offset_class; ?>" id="extra_page_params">
							</div>
						</div>
            <?php /* <div class="form-group row">
              <label for="page_params" class="<?php echo $label_class; ?>">Parametri</label>
              <div class="<?php echo $value_class; ?>">
                <?php if($can_write){ ?>
                <textarea id="page_params" name="languages[<?php echo $language; ?>][params]" class="form-control" ><?php echo htmlspecialchars($language_data->params); ?></textarea>
                <?php } else { ?>
                <div id="page_params" class="form-control" readonly><?php echo htmlspecialchars($language_data->params); ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div> */ ?>
            <div class="form-group row">
              <label for="page_content" class="col-12">Continut optional</label>
              <div class="col-12">
                <?php if($can_write){ ?>
                <textarea id="page_content" name="languages[<?php echo $language; ?>][content]" class="form-control make-htmleditor" ><?php echo htmlspecialchars($language_data->content); ?></textarea>
                <?php } else { ?>
                <div id="page_content" class="form-control" readonly><?php echo $language_data->content; ?>&nbsp;</div>
                <?php } ?>
              </div>
            </div>
            <?php } ?>
			<div class="form-group row">
                <label for="page_images" class="col-12 text-center">Imagini</label>
                <div class="col-12">
					<button type="button" class="btn btn-success add_image" data-pos="start">Adauga imagine</button>
				  <table class="table table-bordered table-hover crt">
					<thead>
						<tr>
							<th>#</th>
							<th>Image</th>
							<th>Alt</th>
							<th>Link</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="page-images-tbody">
						<?php 
						$image_counter = -1;
						if($page->images)
						foreach($page->images as $image => $image_detail){
						$image_counter++;
						?>
						<tr>
							<td>
								<span class="crt"></span>
								<input type="hidden" name="images[<?php echo $image_counter; ?>][name]" value="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>">
							<?php if(!empty($image_detail['custom']) ){ ?>
								<input type="hidden" name="images[<?php echo $image_counter; ?>][custom]" value="1">
							<?php } ?>
							</td>
							<td><a href="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>" target="_blank"><img src="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>" loading="lazy" class="page-image" /></a></td>
							<td><?php /* echo !empty($image_detail['custom']) ? 'Custom' : 'Travelfuse'; ?>
							<?php if(!empty($image_detail['missing'])){ ?>
							<i class="fa fa-warning" title="Missing"></i>
							<?php } */ ?>
								<input type="text" name="images[<?php echo $image_counter; ?>][alt]" value="<?php echo htmlspecialchars($image_detail['alt'] ?? '', ENT_QUOTES); ?>" class="form-control" placeholder="Alt">
							</td>
							<td><?php echo htmlspecialchars($image); ?></td>
							<td>
								<label class="i-checks on-off-show">
								  <input type="checkbox" value="1" name="images[<?php echo $image_counter; ?>][hide]" <?php echo !empty($image_detail['hide']) ? 'checked=""' : ''; ?> class="form-control-custom radio-custom">
								  <i class="fa fa-eye is-off text-success" title="Se afiseaza"></i>
								  <i class="fa fa-eye-slash is-on" title="Ascuns"></i>
								</label>
								<?php if(!empty($image_detail['missing']) || !empty($image_detail['custom']) ){ ?>
								<label class="i-checks on-off-show">
								  <input type="checkbox" value="1" <?php echo !empty($image_detail['hide']) ? 'checked=""' : ''; ?> class="form-control-custom radio-custom" onchange="$('input[name]', $(this).closest('tr').toggleClass('todelete', $(this).is(':checked'))).prop('disabled', $(this).is(':checked'))">
								  <i class="fa fa-check is-on text-success" title="Anuleaza stergerea"></i>
								  <i class="fa fa-trash is-off" title="Marcheaza pentru stergere"></i>
								</label>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				  </table>
				  <button type="button" class="btn btn-success add_image" data-pos="end">Adauga imagine</button>
                  <?php if($can_write){ ?>
                  <?php } else { ?>
                  
                  <?php } ?>
                </div>
              </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>