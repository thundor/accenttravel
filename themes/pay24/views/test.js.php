<?php $this->load->library('encryption');  ?>
let search_axios, search_axios_timer, search_axios_cancel;
const CancelToken = axios.CancelToken; 
export default {
	components : {
		'CheckLoaded' : {
			emits: ['mounted', 'unmounted'],
			props: {
				slot: {
					type: String,
				},
			},
			template : `<slot />`,
			mounted: function() {
				this.$emit('mounted', this.slot)
			},
			unmounted: function() {
				this.$emit('unmounted', this.slot)
			},
		},
		'Subview' : flight_data ? loadViewAsync(subview) : {
			template : `<h5>Nu s-au putut incarca informatiile zborului</h5>`,
		},
	},
	data: () => ({
		step: 1,
		loaded: {},
    }),
	template : `
<v-card class="w-100 fill-height d-flex flex-column">
	<v-card-title class="d-flex text-h6 font-weight-regular justify-space-between align-center ps-2">
		<template v-if="step == 1">
			<?php /* <router-link style="text-decoration: none; color: inherit;" :to="{ name: 'home'}" class="d-flex align-center">
				<v-icon :icon="'mdi-chevron-left'"></v-icon> <span>{{ currentTitle }}</span>
			</router-link> */ ?>
			<a class="d-flex align-center mr-auto" style="text-decoration: none; color: inherit;" href="/" @click.prevent="communicateWithPay24('close')">
				<v-icon :icon="'mdi-chevron-left'"></v-icon> <span>{{ currentTitle }}</span>
			</a>
		</template>
		<template v-else>
			<a class="d-flex align-center mr-auto" style="text-decoration: none; color: inherit;" href="/" @click.prevent="step == 8 ? step = 1 : step--">
				<v-icon :icon="'mdi-chevron-left'"></v-icon> <span>{{ currentTitle }}</span>
			</a>
		</template>
		<v-avatar v-if="false" color="primary" size="24" v-text="step"></v-avatar>
	</v-card-title>
	<v-window ref="flights_window" v-model="step" class="w-100 fill-height" :touch="{left: allowTouchLeft, right: allowTouchRight}">
		<v-window-item :value="1" class="fill-height">
			<v-card-text class="fill-height overflow-y-auto">
				
				<Subview />
				
			</v-card-text>
		</v-window-item>
	</v-window>
</v-card>
	`,

	computed: {
		currentTitle () {
			switch (this.step) {
				case 1: return 'Detalii zbor';
				default: return 'TODO';
			}
		},
	},
	methods: {
		allowTouchRight () {
			if(this.step > 1){
				this.step --;
			}
		},
		allowTouchLeft () {
			if(this.step == 1){
				this.step ++;
				return true;
			}
			return false;
		},
		can_step(cnt){
			var wanted_step = this.step + cnt;
			return true;
		},
	},
	beforeCreate: () => {
		// console.warn('router', router);
	},
	mounted: function() {
		// console.warn(flight_data);
	},
	watch: {
		'step':{
			handler(newValue, oldValue){
			}
		},
	}
}
