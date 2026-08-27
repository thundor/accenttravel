import BaseFunctionality from '../base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	props: {
	},
	data(){
		return {
			abortController: new AbortController(),
			initiate_on_start: false,
			loadtimer: undefined,
			autoSubmitTimer: undefined,
			loading: false,
			presearch_valid: false,
			key: '<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>',
		}
	},
	computed: {
		fetch_object() {
			return {};
		},
		fetch_data() {
			var obj = this.fetch_object;
			var k = Object.values(obj).findIndex(v => '' === v);
			// console.warn('fetch_data', k, obj);
			if(-1 !== k) return false;
			return obj;
		},
		
		first_empty_key() {
			var obj = this.fetch_object;
			return Object.keys(obj).find(k => '' === obj[k]);
		},
	},
	beforeUnmount() {
		this.abortController.abort();
	},
	mounted() {
		if(this.content_type == 'menu'){
			if(this.getObjectDotPathValue(this.data?.defaults, this.key.replace(/\//g, '.'))){
			console.warn(JSON.stringify(this.data?.defaults), this.key.replace(/\//g, '.'), this.initiate_on_start);
				this.data.defaults[this.key.replace(/\/.*/g, '')].submit = false;
				this.initiate_on_start = true;
			}
			return;
		}
	},
	watch: {
		'fetch_data': {
			handler: function(nv,ov){
				this.presearch_valid = !!nv;
			},
			immediate: true
		},
		'presearch_valid': {
			handler: function(nv,ov){
				if(this.content_type != 'menu'){ 
					return;
				}
				clearTimeout(this.autoSubmitTimer);
				if(this.initiate_on_start){
					this.autoSubmitTimer = setTimeout(() => {
						if(this.presearch_valid){
							if(this.initiate_on_start){
								this.initiate_on_start = false;
								// console.warn('this.presearch_valid', this.presearch_valid, this.initiate_on_start);
								this.initiatePreSearch()
							}
						}
					}, 200)
				}
			},
			immediate: true
		},
		/* 'loading': {
			handler: function(nv,ov){
				// console.warn('loading', nv);
			},
			immediate: true
		}, */
	},
	methods: {
		initiatePreSearch(){
			if(!this.presearch_valid) {
				var k = this.first_empty_key;
				var i = document.getElementsByClassName('menu-' + this.$options.name.replace(/.*?functionalities\//, '').replace(/\/[^\/]+\.js$/, '') + '/' + k + '-wrapper'); 
				if(i && i[0]){
					i[0].click()
				} else {
					// console.warn('menu-' + this.$options.name.replace(/.*?functionalities\//, '').replace(/\/[^\/]+\.js$/, '') + '/' + k + '-wrapper');
				}
				return;
			}
			// console.warn('initiatePreSearch', {[this.key]: v});
			this.setValue(true);
			this.$emit('set-value', {step: 0});
		}
	},
	template : `
<template v-if="content_type == 'menu'">
<li class="search-type-ul-close pt-5 pb-4 bg-info shown" :class="{'disabled': !presearch_valid}" @click="initiatePreSearch()" :style="{'z-index': 1}">
	<v-icon v-if="loading" class="mdi-spin position-absolute" icon="mdi-loading" size="28" style="margin-left: -4px;margin-top: -6px;"></v-icon>
	<v-icon icon="mdi-magnify" size="22"></v-icon>
</li>
</template>
	`,
}
