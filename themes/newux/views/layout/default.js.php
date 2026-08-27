export default {
	data: () => {
		return {
			custom: {
				consent_show: false,
			},
			loading: true,
			drawer: false,
			menu: [
				{text:'Oferte speciale', icon:'mdi-sale-outline'},
				{text:'Travel Gift Card',icon:'mdi-card-account-details-star'},
				{text:'Corporate', icon:'mdi-office-building-cog-outline'},
				{text:'Acces B2B',icon:'mdi-handshake'},
				{text:'Contact',icon:'mdi-card-account-phone-outline'},
			]
		}
	},
	watch:{
		'custom.consent_show': {
			handler(newValue, oldValue){
				// console.warn('custom.consent_show', newValue);
				if(!newValue){
					saveStorage('newux', Date.now(), 'consent')
				}
			},
		},
	},
	template : `
		<component :is="loadViewAsync('partials/module/top-menu')" eager></component>
		<?php /* 
		<slot name="header.before"></slot>
		<header>
			<slot name="header.inner.before"></slot>
			<slot name="header.inner.after"></slot>
		</header>
		<slot name="header.after"></slot>
		*/ ?>
		<v-container id="pos-stick-t-b" class="top-content py-0 text-center">
		</v-container>
		
		<v-main v-show="!loading">
			<slot />
		</v-main>
		
		<v-container id="pos-stick-b-t" class="footer-content py-0 text-center">
		</v-container>
		
		<v-footer class="pa-0 d-flex flex-column">
			<component :is="loadViewAsync('partials/module/footer-copyright')"></component>
		</v-footer>
		
		<v-expand-transition>
			<v-footer class="pa-0 flex-column" :class="{' position-sticky bottom-0 left-0 right-0': !designGlobal}" style="z-index:1" v-show="designGlobal || custom.consent_show">
				<component :is="loadViewAsync('partials/module/footer-consent')" :custom="custom" @custom="(a) => {(console.error('consent_show = a', a))}"></component>
			</v-footer>
		</v-expand-transition>
		
		
		<?php if($this->theme->_can_edit) { ?>
		<div class="position-sticky bottom-0" style="z-index:1;pointer-events: none;">
			<div class="d-flex flex-wrap" style="pointer-events: all;width: fit-content;">
			<v-switch v-model="loadingPage" color="primary" hide-details density="compact">
				<template v-slot:label>
				  Loading
				</template>
			</v-switch>
			<v-switch v-model="designGlobal" color="primary" hide-details density="compact">
				<template v-slot:label>
				  EDITMODE
				</template>
			</v-switch>
			</div>
		</div>
		<?php } ?>
	`,
	beforeCreate() {
	},
	mounted() {
		this.$nextTick(() => {
			this.loading = false;
		});
		var consent = parseInt(getStorage('newux', 'consent', 0));
		if(isNaN(consent) || consent < (Date.now() - 86400000)){
			setTimeout(() => {
				// console.warn('consent', consent - (Date.now() - 86400))
				this.custom.consent_show = true;
			}, 1000)
		}
	},
	computed: {
	}
}
