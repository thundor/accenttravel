<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::includeAddon('lazy-loading'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/index/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/index/stylesheets.php'); ?>
<?php 

// $this->_ci->load->model('Travelfuse/TravelFuseCircuit_model');
$search_data = [];
$special_layout = $this->_controller=='Circuite';
$n = $this->_ci->input->get('n');
if($special_layout){
  $origin = $this->_ci->uri->segment(3);
  if($origin){
    $_GET['origin'] = str_replace('_', ' ',$origin);
    $destination = $this->_ci->uri->segment(4);
    if($destination){
      $_GET['destination'] = str_replace('_', ' ',$destination);
    }
  }
  $filters = array();
	$start_date = $this->_ci->input->get('sdate');
	$go_only = null;
  $d2 = null;
  if(isset($start_date)){
		try{
			$d2 = new DateTime($start_date);
			$search_data['check-in'] = $d2->format('Y-m-d');
			$go_only = true;
		} catch(Exception $e){}
  }
	$end_date = $this->_ci->input->get('edate');
  if(isset($end_date)){
    if(!isset($d2) || !$d2){
      $d2 = new DateTime('today midnight');
    }
		try{
			$d2->modify($end_date);
			$search_data['check-out'] = $d2->format('Y-m-d');
      $go_only = false;
		} catch(Exception $e){}
  }
	$a = $this->_ci->input->get('a');
  if(isset($a) && is_numeric($a)){
    $search_data['travellers']['ADT'] = (int)$a;
  }
	$s = $this->_ci->input->get('s');
  if(isset($s) && is_numeric($s)){
	  $search_data['travellers']['ADT'] = $search_data['travellers']['ADT'] ?? 0;
	  $search_data['travellers']['ADT'] += (int)$s;
  }
  $c = $this->_ci->input->get('c');
  
  // $search_data['varste_copii'] = array();
  // $search_data['passengers_infant_lap'] = 0;
  // $search_data['passengers_infant_seat'] = 0;
  // $search_data['passengers_youth'] = 0;
  // $search_data['passengers_child'] = 0;
  if(isset($c)){
    $varste_copii = preg_replace('/[^\d,]/', '', $c);
    $varste_copii = explode(',', $varste_copii);
	$total_adults = $search_data['travellers']['ADT'] ?? 0;
    foreach($varste_copii as $varsta_copil){
      $varsta_copil = (int)$varsta_copil;
      if($varsta_copil > 17){
        $varsta_copil = 17;
      }
      if(true){
		$search_data['travellers']['CHD'] = $search_data['travellers']['CHD'] ?? [];
		$search_data['travellers']['CHD'][] = $varsta_copil;
        if($varsta_copil < 3){
          if($total_adults){
            $total_adults --;
			$search_data['travellers']['INS'] = $search_data['travellers']['INS'] ?? 0;
            $search_data['travellers']['INS']++;
          }
        }
      }
    }
  }
  foreach(array('origin', 'destination') as $param){
    $g_param = $this->_ci->input->get($param);
	$param2 = $param;
	if($param === 'origin'){
		$param2 = 'departure';
	}
    if(isset($g_param)){
		$search_data[$param2 . '-' . 'city'] = $g_param;
    }
  }
}
$submit = !empty($this->_ci->input->get('n'));
if($submit){
	$search_data['submit'] = true;
}
// dd($search_data);
 ?>
<v-container class="pa-0" fluid>
<v-window class="" :touch="false">
	<v-window-item :value="0" class="w-100 fill-height">
		<v-card
			class="w-100 fill-height d-flex flex-column"
		>
			<component :is="loadViewAsync('partials/search-wrapper')" activate_menu="travelfuse-circuite" :defaults="<?php echo htmlspecialchars(json_encode(['travelfuse-circuite' => $search_data]), ENT_QUOTES); ?>">
				<module id="search-wrapper-inner-module"></module>
			</component>
		</v-card>
		
	</v-window-item>
	<v-window-item :value="1" class="w-100 fill-height">
		
	</v-window-item>
</v-window>
</v-container>
<?php themeFunctions::debugFileLine('end'); ?>