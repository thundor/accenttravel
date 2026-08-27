<?php include __DIR__ . "/../" . str_replace('circuite', 'chartere', basename(dirname(__FILE__))) . '/' . basename(__FILE__); ?>
<?php /*
import BaseFunctionality from '../common/submit.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data(){
		return {
			key: '<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>',
		}
	},
	computed: {
		fetch_data() {
			
			var obj = {
				Transport: (this.data['<?php echo basename(dirname(__FILE__)); ?>/transport'] || {}).Id || '',
				TourCountryCode: (this.data['<?php echo basename(dirname(__FILE__)); ?>/destination-city'] || {}).Id || '',
				DestinationType: (this.data['<?php echo basename(dirname(__FILE__)); ?>/destination-city'] || {}).type || '',
				DepCityCode: (this.data['<?php echo basename(dirname(__FILE__)); ?>/departure-city'] || {}).Id || '',
				CheckIn: (this.data['<?php echo basename(dirname(__FILE__)); ?>/check-in'] || {}).Id || '',
				ADT: parseInt((this.data['<?php echo basename(dirname(__FILE__)); ?>/travellers'] || {}).ADT) || '',
			}
			
			// console.warn('submit', obj);
			if(-1 !== Object.values(obj).findIndex(v => '' === v)) return false;
			
			return obj;
		},
	},
	mounted() {
	},
	watch: {
		'fetch_data': {
			handler: function(nv,ov){
				this.presearch_valid = !!nv;
			},
			immediate: true
		},
	},
	methods: {
	},
}
*/ ?>