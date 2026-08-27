<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$hotel_search_data = [];
$ndate = new DateTime('now');
$start_date = $this->_ci->input->get('sdate');
$dstart = null;
if(isset($start_date)){ try{$dstart = new DateTime($start_date); if($dstart < $ndate) $dstart = false; } catch(Exception $e){} }
if(!$dstart){ $dstart = new DateTime('next friday'); }

$end_date = $this->_ci->input->get('edate');
$dend = null;
if(isset($end_date)){ try{$dend = new DateTime($end_date); if($dend < $ndate) $dend = false; } catch(Exception $e){} }
if(!$dend){ $dend = clone $dstart; $dend->modify('next sunday'); }

$hotel_search_data['check-in'] = $dstart->format('Y-m-d');
$hotel_search_data['check-out'] = $dend->format('Y-m-d');

$occupancy = $this->_ci->input->get('o');
if(isset($occupancy) && is_array($occupancy)){
	$ri = -1;
	  foreach($occupancy as $room_index => $room){
		$ri++;
		foreach($room as $k => $v){
		  $k = strtoupper($k);
		  if('ADT' === $k){
			  $hotel_search_data['travellers'][$ri][$k] = intval($v);
		  } elseif('CHD' === $k){
			  $chds = array_values(array_map('intval', array_values($v)));
			  $hotel_search_data['travellers'][$ri][$k] = $chds;
		  }
		}
	}
}
$submit = !empty($this->_ci->input->get('n'));
if($submit){
	$hotel_search_data['submit'] = true;
}
?>
<v-container class="pa-0" fluid v-if="(mydata.mystep = (mydata.step < 3 ? 2 : mydata.step) || 2)">
<v-window class="" :touch="false">
	<v-window-item :value="0" class="w-100 fill-height">
		<v-card
			class="w-100 fill-height d-flex flex-column"
		>
		<v-window id="search-wrapper-windows" v-model="mydata.mystep" :touch="false">
			<v-window-item :value="2">
				<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/travelfuse-chartere/hotel')" :data="mydata" :defaults="<?php echo htmlspecialchars(json_encode(['travelfuse-chartere' => $hotel_search_data]), ENT_QUOTES); ?>" :hotel="<?php echo htmlspecialchars(json_encode($this->view_data['hotel_details']), ENT_QUOTES); ?>"></component>
			</v-window-item>
			<v-window-item ref="window-3" id="search-wrapper-item-checkout" eager :value="3">
				<button v-if="0" @click="mydata.step--">--</button>
			</v-window-item>
			<v-window-item ref="window-4" id="search-wrapper-item-success" :value="4">
				<v-container>
				<v-card>
					<v-card-text class="text-center">
					<div class="text-h3">Comanda finalizata</div>
					<br />
					<hr />
					<br />
					<div class="text-h4">Va multumim pentru utilizarea serviciilor noastre</div>
					<div class="text-h5">Veti fi contactat de un operator pentru detalii suplimentare.</div>
					
					Detalii comanda:
					<div id="checkout-success-data"></div>
					</v-card-text>
					
					
				</v-card>
				</v-container>
			</v-window-item>
		</v-window>
		</v-card>
		
	</v-window-item>
	<v-window-item :value="1" class="w-100 fill-height">
		
	</v-window-item>
</v-window>
</v-container>
<?php themeFunctions::debugFileLine('end'); ?>