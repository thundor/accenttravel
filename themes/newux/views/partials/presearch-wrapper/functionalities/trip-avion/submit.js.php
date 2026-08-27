import BaseFunctionality from '../common/submit.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data(){
		return {
			key: '<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>',
		}
	},
	computed: {
		fetch_object() {
			var obj = {
				Type: (this.data['<?php echo basename(dirname($a)); ?>/type'] || {}).Id || '1',
				Departure: (this.data['<?php echo basename(dirname($a)); ?>/departure-city'] || {}).Id || '',
				Destination: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '',
				CheckIn: (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id || '',
			};
			
			if(1 === parseInt(obj.Type)){
				obj['CheckOut'] = (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id || '';
			}
			obj['Travellers'] = this.data['<?php echo basename(dirname($a)); ?>/travellers'] || '';
			
			
			// console.warn('fetch_object', obj, '<?php echo basename(dirname($a)); ?>');
			return obj;
		},
		first_empty_key() {
			var obj = this.fetch_object;
			var objs = {
				'Type': 'type',
				'Departure': 'departure-city',
				'Destination': 'destination-city',
				'CheckIn': 'check-in',
			};
			if(1 === parseInt(obj.Type)){
				objs['CheckOut'] = 'check-out';
			}
			objs['Travellers'] = 'travellers';
			return objs[Object.keys(obj).find(k => '' === obj[k])];
		},
	},
	mounted() {
	},
	watch: {
	},
	methods: {
		<?php /* initiatePreSearch(){
			console.warn('initiatePreSearch', this.data);
			return;
		} */ ?>
	},
}
