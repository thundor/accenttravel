<?php include __DIR__ . "/../" . str_replace('circuite', 'chartere', basename(dirname(__FILE__))) . '/' . basename(__FILE__); ?>
<?php /*
import BaseFunctionality from '../common/search.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
			fetch_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(__DIR__); ?>/offer-list.json?${append_url}`,
		}
	},
	mounted() {
		this.$emit('set-value', {'step': 1});
	},
	computed: {
		fetch_data() {
			var travellers = (this.data['<?php echo basename(dirname(__FILE__)); ?>/travellers'] || {});
			var obj = {
				Transport: (this.data['<?php echo basename(dirname(__FILE__)); ?>/transport'] || {}).Id || '',
				TourCountryCode: (this.data['<?php echo basename(dirname(__FILE__)); ?>/destination-city'] || {}).Id || '',
				DestinationType: (this.data['<?php echo basename(dirname(__FILE__)); ?>/destination-city'] || {}).type || '',
				DepCityCode: (this.data['<?php echo basename(dirname(__FILE__)); ?>/departure-city'] || {}).Id || '',
				CheckIn: (this.data['<?php echo basename(dirname(__FILE__)); ?>/check-in'] || {}).Id || '',
				Adults: [(!isNaN(parseInt(travellers.ADT)) && (parseInt(travellers.ADT) > 0) && parseInt(travellers.ADT) || 0)
						+ (!isNaN(parseInt(travellers.YTH)) && (parseInt(travellers.YTH) > 0) && parseInt(travellers.YTH) || 0)],
				Children: [travellers.CHD && Array.isArray(travellers.CHD) || []].filter(c => c && c.length),
			}
			return obj;
		},
		country() {
			return (this.data['<?php echo basename(dirname(__FILE__)); ?>/destination-city'] || {}).type == 'country' && (this.data['<?php echo basename(dirname(__FILE__)); ?>/destination-city'] || {});
		},
	},
}
*/ ?>