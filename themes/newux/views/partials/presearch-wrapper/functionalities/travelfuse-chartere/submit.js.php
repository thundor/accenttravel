import BaseFunctionality from '../common/submit.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data(){
		return {
			initiate_on_start: false,
			key: '<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>',
		}
	},
	computed: {
		fetch_object() {
			var HotelId = this.data['<?php echo basename(dirname($a)); ?>/hotel-id'] || '';
			var obj = {
				...(HotelId 
					? { HotelId: HotelId } 
					: {}),
				<?php switch (basename(dirname($a))){ 
				case 'travelfuse-circuite':
				?>
				// Transport: (this.data['<?php echo basename(dirname($a)); ?>/transport'] || {}).Id || (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Transport || '',
				<?php 
					break;
				default:
				?>
				// Transport: (this.data['<?php echo basename(dirname($a)); ?>/transport'] || {}).Id || (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Transport || '',
				CheckOut: (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id || '',
				<?php 
					break;
				} ?>
				Destination: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '',
				DestinationType: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).type || '',
				DepCityCode: (this.data['<?php echo basename(dirname($a)); ?>/departure-city'] || {}).Id || '',
				CheckIn: (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id || '',
				ADT: parseInt((this.data['<?php echo basename(dirname($a)); ?>/travellers'] || {}).ADT) || '',
			}
			// console.warn('chart_fetchobj', obj)
			return obj;
		},
		first_empty_key() {
			var obj = this.fetch_object;
			var objs = {
				// 'Transport': 'transport',
				'Destination': 'destination-city',
				'DestinationType': 'destination-city',
				'DepCityCode': 'departure-city',
				'CheckIn': 'check-in',
				'CheckOut': 'check-out',
				'ADT': 'travellers',
			};
			// console.warn('chart_first_em', objs)
			return objs[Object.keys(obj).find(k => '' === obj[k])];
		},
	},
	mounted() {
		if(this.fetch_object.HotelId){
			// console.error('1', this.fetch_object);
			this.initiate_on_start = true;
		}
	},
	watch: {
	},
	methods: {
	},
}
