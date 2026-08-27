<?php
$this->load->model('Options_model');
$offers_settings = $this->Options_model->get('offers_popular_settings');
if(!$offers_settings){
  $offers_settings = array();
}
$items = isset($offers_settings['status']) ? $offers_settings['status'] : array();
$items = array_intersect($items, array(1));
$this->load->model('Trip/Offer_popular_model');

$this->db->select("Name, ISO");
$this->db->where(array(
  'status' => 1,
));
$q = $this->db->get('trip_countries');
$countries_rows = $q->result('array');
$countries = [];
foreach($countries_rows as $countries_row){
	$countries[$countries_row['Name']] = $countries_row['ISO'];
}
$items = array_reduce(array_keys($items), function($items, $item_key) use ($offers_settings, $countries){
	$title = isset($offers_settings['title'], $offers_settings['title'][$item_key]) ? '' . $offers_settings['title'][$item_key] : '';
    $image = isset($offers_settings['image'], $offers_settings['image'][$item_key]) ? '' . $offers_settings['image'][$item_key] : '';
    $code = isset($offers_settings['company_code'], $offers_settings['company_code'][$item_key]) ? '' . $offers_settings['company_code'][$item_key] : '';
    if(!strlen($image)){
      $image = 'placeholder_companie.png';
    }
    
    $this->db->where(array(
      'status' => 1,
      'code' => $code,
    ));
    $this->db->order_by('price ASC');
    $this->db->group_by('flight_code');
    $offers = $this->Offer_popular_model->getOffers(array('limit'=>6));
	
	$items[] = [
		'title' => $title,
		'image' => $this->theme->theme_url . 'assets/images/' . $image,
		'code' => $code,
		'children' => array_map(function($offer) use ($countries){
			$offer->data = unserialize($offer->data); 
			$departure = json_decode(json_encode($offer->data->departure ?? []), true);
			$arrival = json_decode(json_encode($offer->data->arrival ?? []), true);
			$flight = json_decode(json_encode($offer->data->flight ?? []), true);
			$offer->url = site_url('trip/flights' . '?' . 'destination=' . trim((($arrival['data']['location'] ?? '') ? $arrival['data']['location'] : '') . ' ' . ($arrival['data']['city'] ?? '') . '-' . ($countries[$arrival['data']['country'] ?? ''] ?? '')) . '&origin=' . trim((($departure['data']['location'] ?? '') ? $departure['data']['location'] : '') . ' ' . ($departure['data']['city'] ?? '') . '-' . ($countries[$departure['data']['country'] ?? ''] ?? '')) . '&sdate=' . ($flight['Routes'][0]['Segment'][0]['Origin']['Date'] ?? '') . '&edate=' . ($flight['Routes'][(count($flight['Routes'] ?? []) -1)]['Segment'][0]['Origin']['Date'] ?? '') . '&n=1');
			// $offer->url = site_url('trip/flights' . '/' . (($departure['data']['location'] ?? '') ? $departure['data']['location'] : ($departure['data']['city'] ?? '')) . '/' . (($arrival['data']['location'] ?? '') ? $arrival['data']['location'] : ($arrival['data']['city'] ?? '')) . '?sdate=' . ($flight['Routes'][0]['Segment'][0]['Origin']['Date'] ?? '') . '&edate=' . ($flight['Routes'][(count($flight['Routes'] ?? []) -1)]['Segment'][0]['Origin']['Date'] ?? '') . '&n=1');
			// dd($offer->data);
			return $offer;
		}, $offers),
	];
	
	return $items;
}, []);
?>
import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		card_index: 0,
		company_index: 0,
		data: {
			children: <?php echo json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>,
		}
	}),
	beforeMount() {
	},
	computed: {
	},
	watch:{},
	methods:{},
	template : `
	<v-sheet class="modul-destinatii-avion">
		<v-card>
			<div class="d-flex flex-wrap align-center mb-4 ga-4">
				Alege linia aeriana:
				<v-btn-toggle
					v-model="company_index"
					variant="plain"
					divided
					class="ga-4 flex-wrap"
				>
					<v-btn v-for="(child, childIndex) in output.children" class="bg-white border-thin" rounded="xl">
						<v-img :src="child.image" style="min-width: 80px;height: 100%;max-height:46px" :alt="child.title"></v-img>
					</v-btn>
				</v-btn-toggle>
			</div>
		<v-row>
		<v-window v-model="company_index">
			<v-window-item v-for="(child, childIndex) in output.children" :value="childIndex">
				<div class="d-flex flex-wrap ga-4 pa-4 justify-center">
					<v-card v-for="(offer, offerIndex) in child.children" class="d-flex force-default flex-fill text-decoration-none" :key="offerIndex" @click="card_index = offerIndex" rounded="lg" elevation="2" style="flex-basis: 410px !important;" tag="a" :href="offer.url">
						<v-card-text class="pa-0">
							<div class="d-flex h-100 flex-column flex-md-row">
								<div class="bg-white d-flex flex-column flex-fill justify-space-between pa-2">
									<div v-for="(route, routeIndex) in (offer?.data?.flight?.Routes)" class="d-flex flex-column flex-md-row ga-1">
									<template v-for="segments in ([route?.Segment || []])">
									<template v-for="departure_segment in [segments?.[0] || {}]">
									<template v-for="arrival_segment in [segments?.[segments.length-1] || {}]">
										<div class="d-none d-md-flex"><v-icon icon="mdi-airplane" size="28"></v-icon></div>
										
										<div class="d-flex flex-column justify-space-between flex-fill ga-1">
											<div class="text-h6 text-wrap pa-0" style="max-height:30px;overflow:hidden;">{{ departure_segment.Origin?.Airport?.City }} - {{ arrival_segment.Destination?.Airport?.City }}</div>
											<div class="text-body-2 text-wrap pa-0 d-flex ga-1 flex-column">
												<div style="max-height:20px;overflow:hidden;">{{ departure_segment.Origin?.Airport?._ }} » {{ arrival_segment.Destination?.Airport?._ }}</div>
												<div class="d-flex ga-4">
													<div><v-icon icon="mdi-clock-outline"></v-icon> {{ (departure_segment.Origin?.Time || '').replace(/^([0-9]+:[0-9]+).*/,'$1') }}</div>
													<v-icon icon="mdi-arrow-right-thin"></v-icon>
													<div><v-icon icon="mdi-clock-outline"></v-icon> {{ (arrival_segment.Destination?.Time || '').replace(/^([0-9]+:[0-9]+).*/,'$1') }}</div>
												</div>
											</div>
										</div>
										<div class="text-body-2 d-flex flex-row flex-md-column justify-md-space-around ga-1">
											<v-icon icon="mdi-calendar-blank-outline" size="22"></v-icon>
											<div>{{ titleCase(formatDate(departure_segment.Origin?.Date, {weekday:'long'})) }}</div>
											<div class="text-nowrap">{{ formatDate(departure_segment.Origin?.Date, {day:'numeric', month: 'numeric', year:'numeric'}) }}</div>
										</div>
									</template>
									</template>
									</template>
									</div>
								</div>
								<div class="pa-2 d-flex flex-row flex-md-column justify-space-between align-center" style="background-color: #FBFAF9">
									<div><v-img :src="child.image" style="width: 80px;" :alt="child.title"></v-img></div>
									<div>
										De la
										<div class="text-primary text-h5 text-bold text-end text-nowrap" v-text="format_price(offer?.data?.flight?.Price || 0, offer?.data?.flight?.Currency || 'EUR')"></div>
									</div>
								</div>
							</div>
						</v-card-text>
					</v-card>
				</div>
			</v-window-item>
		</v-window>
		</v-row>
		</v-card>
	</v-sheet>
	`,
}
