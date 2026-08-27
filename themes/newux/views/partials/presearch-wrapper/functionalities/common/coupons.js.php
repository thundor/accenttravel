import FormLegend from '../../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	emits: ['update:modelValue', 'coupon_object'],
	props: {
		modelValue: {
			type: Number,
			default: () => (0),
		},
		totalObject: {
			type: Object,
			default: () => (null),
		},
		currency: {
			type: String,
			default: () => ('EUR'),
		},
		total: {
			type: Number,
			default: () => (5123),
		},
		type: {
			type: String,
			default: () => (''),
		},
		phone: {
			type: String,
			default: () => (''),
		},
	},
	components:{
		'FormLegend': FormLegend,
	},
	data: () => ({
		coupon_currency: 'EUR',
		coupon_total: 0,
		coupon_type: '',
		coupon_phone: '',
		loading: {
			code: false,
		},
		code: '',
		discounted_total: 0,
		coupons: [],
		errors: [],
	}),
	template : `
	<div class="coupon-wrapper" v-if="coupon_type">
		<hr />
		<v-row>
			<v-col cols="7">
				<FormLegend title="Cupon Promotional" subtitle="Ati primit un cupon promotional? Introduceti-l în acest camp și dati click pe butonul “Activare cupon” pentru a beneficia de reducere. Un singur cupon poate fi utilizat per comanda."></FormLegend>
				<div v-if="!!errors && !!errors.length" v-text="errors.join(', ')" class="text-error text-center">
				</div>
			</v-col>
			<v-col>
				<v-text-field ref="code"
				:disable="loading.code"
				:eager-validate="false"
				class="rounded-theme ondark"
				v-model="code"
				label="Cod cupon"
				:hide-details="!(errors && errors.length)"
				:error="!!errors && !!errors.length"
				:error-message="errors.join(', ')"
				outlined >
				<template v-slot:prepend>
					<v-icon icon="mdi-sale" color="warning"></v-icon>
				</template>
				<template v-slot:append>
				  <v-btn :disabled="!code || loading.code" class="text-none font-weight-normal text-white" size="x-large" variant="outlined" rounded="theme" :loading="loading.code" icon="mdi-check" @click="addCoupon()" />
				</template>
			</v-text-field>
			</v-col>
		</v-row>
	
		<v-card class="rounded-theme bg-background mt-4" v-for="coupon in couponsFormatted">
			<div class="pa-4">
				<div class="d-flex justify-space-between">
					<div class="d-flex justify-space-between flex-wrap flex-fill">
						<div class="d-flex flex-column align-start flex-fill justify-start flex-wrap" style="gap:5px;">
							<div class="v-list-item-title text-h5">Cupon reducere: <span v-text="coupon.title" class="text-pre text-primary"></span></div>
							<span class="color-dark-light">Cod cupon: <strong class="text-pre" v-text="coupon.code"></strong></span>
							<span class="color-dark-light" v-if="coupon.lost">Valoare neutilizata cupon: <strong class="text-pre" v-text="format_price(coupon.lost, coupon_currency)"></strong></span>
							<span class="color-dark-light">Reducere aplicata: <strong class="" v-text="'-' + format_price(coupon.discounted, coupon_currency)"></strong></span>
						</div>
						<div class="d-flex flex-column align-start flex-fill justify-start ms-auto flex-wrap pt-1" style="gap:5px;">
							<span class="color-dark-light">Total anterior: <strong class="text-pre" v-text="format_price(coupon.priceBeforeDiscount, coupon_currency)" style="text-decoration: line-through;"></strong></span>
							<div class="v-list-item-title text-h5">Rest de plata: <span v-text="format_price(coupon.priceAfterDiscount, coupon_currency)" class="text-pre text-primary"></span></div>
						</div>
					</div>
					<div class="d-flex flex-column align-end justify-start ms-auto flex-wrap" style="gap:5px;">
						<v-icon color="primary" icon="mdi-delete-forever" class="ms-2" @click.stop="removeCoupon(coupon.code)"></v-icon>
					</div>
				</div>
			</div>
			
		</v-card>
	</div>
	`,
	computed: {
		orderedCoupons:{
			get() { 
				return [...(this.coupons || [])].sort((a,b) => {
					if(a.discount_type == b.discount_type){
						if(a.discount_type == 'P'){
							return a.discount - b.discount;
						} else {
							if(this.coupon_currency == 'RON'){
								return a.amount_ron - b.amount_ron;
							}
							return a.amount_eur - b.amount_eur;
						}
					}
					return a.discount_type == 'P' ? -1 : 1;
				})
			}
		},
		couponsFormatted:{
		  get() { 
			var discounted_total = this.coupon_total;
			var coupons = [];
			console.warn('orderedCoupons', this.orderedCoupons);
			this.orderedCoupons.forEach(c => {
				var coupon = {...c};
				coupon.priceBeforeDiscount = parseFloat(discounted_total);
				var discount = 0;
				var coupon_discount_type = coupon.discount_type;
				var coupon_discount = parseFloat(coupon.discount);
				var coupon_amount_eur = parseFloat(coupon.amount_eur);
				var coupon_amount_ron = parseFloat(coupon.amount_ron);
				var coupon_code = coupon.code;
				var coupon_title = "";
				if(coupon_discount_type == 'P'){
					coupon_title += " " + coupon_discount + "%";
					var discount = (discounted_total * coupon_discount) / 100;
					
				} else if(coupon_discount_type == 'F'){
					if(this.coupon_currency == 'EUR'){
						discount = coupon_amount_eur;
						coupon_title += " " + format_price(coupon_amount_eur, this.coupon_currency);
					} else if(this.coupon_currency == 'RON'){
						discount = coupon_amount_ron;
						coupon_title += " " + format_price(coupon_amount_ron, this.coupon_currency);
					}
				}
				coupon.title = coupon_title;
				coupon.lost = 0;
				if(discount > discounted_total){
					coupon.lost = discount - discounted_total;
					discounted_total = discount;
				}
				coupon.discounted = discount;
				discounted_total -= discount;
				coupon.priceAfterDiscount = parseFloat(discounted_total);
				coupons.push(coupon);
			});
			this.discounted_total = discounted_total;
			
			console.warn('couponsFormatted', coupons);
			return coupons;
		  },
		},
	},
	mounted: function() {
		this.reset();
		/* for(var i = 1; i<=20; i++){
			this.coupons.push({
				discount_type: this.randomItem(['P','F']),
				discount: this.randomItem([10,15,20,30,35,40,50]),
				amount_eur: this.randomItem([100,150,200,300,350,400,500]),
				amount_ron: '1000',
				code: 'GIFTCARD47007ATE98',
				name: 'Cupon test ' + (this.coupons.length + 1),
			});
		} */
	},
	methods: {
		reset(){
			this.removeCoupon(); // This is just to reload Coupons
		},
		removeCoupon(code){
			if(this.loading.code) return;
			this.errors = [];
			this.loading.code = true;
			var url = '<?php echo site_url('trip/checkout/remove_coupon');?>';
			var s_params = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
			};
			if(code){
				s_params['coupon_code'] = code;
			}
			
			fetch(url, {
				method: 'POST',
				headers: {
				  'Accept': 'application/json'
				},
				body: new URLSearchParams(objToSerialize(s_params))
			}).then((response) => {
				if (!response.ok) {
					if(response.status == 403){
						// CSRF
						window.location = window.location.href.replace(/#.*/, '');
						throw new Error("Network response failed. Redirecting to self", {cause: response });
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).then((response) => response.json()).then((response) => {
				if(response.data.coupons){
					this.coupons = response.data.coupons;
				} else if(response.status == 'error') {
					this.errors.push(response.message.replace(/(<([^>]+)>)/gi, ""));
				}
			}).catch((e) => {
				this.errors.push("Eroare server");
				console.error("Failed to remove Coupon", e);
				// Do nothing
			}).finally(() => {
				this.loading.code = false;
			})
		},
		randomItem(items){
			return items[Math.floor(Math.random()*items.length)]
		},
		addCouponOld(){
			if(this.loading.code) return;
			
			this.loading.code = true;
			setTimeout(()=>{
				this.coupons.push({
					discount_type: this.randomItem(['P','F']),
					discount: this.randomItem([10,15,20,30,35,40,50]),
					amount_eur: this.randomItem([100,150,200,300,350,400,500]),
					amount_ron: '1000',
					code: this.code,
					name: 'Cupon test ' + (this.coupons.length + 1),
				});
				this.code = '';
				this.loading.code = false;
			}, 500)
			
		  // Should remove coupon
		},
		addCoupon(){
			if(this.loading.code) return;
			this.errors = [];
			this.loading.code = true;
			var url = '<?php echo site_url('trip/checkout/validate_coupon');?>';
			var s_params = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				coupon_code: this.code,
				coupon_type: this.coupon_type,
				coupon_phone: this.coupon_phone,
			};
			
			fetch(url, {
				method: 'POST',
				headers: {
				  'Accept': 'application/json'
				},
				body: new URLSearchParams(objToSerialize(s_params))
			}).then((response) => {
				if (!response.ok) {
					if(response.status == 403){
						// CSRF
						window.location = window.location.href.replace(/#.*/, '');
						throw new Error("Network response failed. Redirecting to self", {cause: response });
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).then((response) => response.json()).then((response) => {
				if(response.data.coupons){
					this.code = '';
					this.coupons = response.data.coupons;
				} else if(response.status == 'error') {
					this.errors.push(response.message.replace(/(<([^>]+)>)/gi, ""));
				}
			}).catch((e) => {
				this.errors.push("Eroare server");
				console.error("Failed to add Coupon", e);
				// Do nothing
			}).finally(() => {
				this.loading.code = false;
			})
		},
	},
	watch: {
		'type':{
			handler(newValue, oldValue){
				newValue && (this.coupon_type = newValue);
			},
		},
		'phone':{
			handler(newValue, oldValue){
				console.warn('PHONE', newValue);
				newValue && (this.coupon_phone = (newValue || '').trim().replace(/.*? /, ''));
			},
			immediate: true,
		},
		'totalObject':{
			handler(newValue, oldValue){
				newValue && ((this.coupon_total = newValue.Amount), (this.coupon_currency = newValue.Currency));
			},
		},
		'total':{
			handler(newValue, oldValue){
				newValue && (this.coupon_total = newValue);
			},
		},
		'currency':{
			handler(newValue, oldValue){
				newValue && (this.coupon_currency = newValue);
			},
		},
		'discounted_total':{
			handler(newValue, oldValue){
				console.warn('discounted_total', newValue, this.coupon_total - this.discounted_total)
				this.$emit('coupon_object', {Amount: this.coupon_total - this.discounted_total, Currency: this.coupon_currency});
				this.$emit('update:modelValue', this.coupon_total - this.discounted_total);
			},
			// immediate: true,
		},
	},
}
