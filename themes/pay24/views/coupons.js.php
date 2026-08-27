export default {
	emits: ['update:modelValue'],
	props: {
		modelValue: {
			type: Number,
			default: () => (0),
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
			default: () => ('flight'),
		},
	},
	data: () => ({
		loading: {
			code: false,
		},
		code: '',
		discounted_total: 0,
		coupons: [],
		errors: [],
	}),
	template : `
	<div class="coupons">
	<v-card class="rounded-theme bg-background mt-4">
		<div class="px-4 pt-3">
			<q-input ref="code"
			 :disable="loading.code"
			:eager-validate="false"
			class="rounded-theme ondark pb-4"
			v-model="code"
			label="Cod cupon"
			:error="!!errors && !!errors.length"
			:error-message="errors.join(', ')"
			outlined >
				<template v-slot:after>
				  <v-btn :disabled="!code || loading.code" class="text-none font-weight-normal" size="x-large" color="primary" rounded="theme" variant="flat" v-text="loading.code ? 'Procesare...' : 'Aplica'" @click="addCoupon()" />
				</template>
			</q-input>
		</div>
	</v-card>
	
	<v-card class="rounded-theme bg-background mt-4" v-for="coupon in couponsFormatted">
		<div class="pa-4">
			<div class="d-flex justify-space-between">
				<div class="d-flex justify-space-between flex-wrap flex-fill">
					<div class="d-flex flex-column align-start flex-fill justify-start flex-wrap" style="gap:5px;">
						<div class="v-list-item-title text-h5">Cupon reducere: <span v-text="coupon.title" class="text-pre text-primary"></span></div>
						<span class="color-dark-light">Cod cupon: <strong class="text-pre" v-text="coupon.code"></strong></span>
						<span class="color-dark-light" v-if="coupon.lost">Valoare neutilizata cupon: <strong class="text-white text-pre" v-text="format_price(coupon.lost, currency)"></strong></span>
						<span class="color-dark-light">Reducere aplicata: <strong class="text-white" v-text="'-' + format_price(coupon.discounted, currency)"></strong></span>
					</div>
					<div class="d-flex flex-column align-start flex-fill justify-start ms-auto flex-wrap pt-1" style="gap:5px;">
						<span class="color-dark-light">Total anterior: <strong class="text-pre" v-text="format_price(coupon.priceBeforeDiscount, currency)" style="text-decoration: line-through;"></strong></span>
						<div class="v-list-item-title text-h5">Rest de plata: <span v-text="format_price(coupon.priceAfterDiscount, currency)" class="text-pre text-primary"></span></div>
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
							if(this.currency == 'RON'){
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
			var discounted_total = this.total;
			var coupons = [];
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
					if(this.currency == 'EUR'){
						discount = coupon_amount_eur;
						coupon_title += " " + format_price(coupon_amount_eur, this.currency);
					} else if(this.currency == 'RON'){
						discount = coupon_amount_ron;
						coupon_title += " " + format_price(coupon_amount_ron, this.currency);
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
			};
			if(code){
				s_params['coupon_code'] = code;
			}
			axios.post(url,objToSerialize({
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				...s_params
				}), {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded charset=UTF-8',
					'X-Requested-With': '<?php echo $_SERVER['HTTP_X_REQUESTED_WITH']; ?>',
				},
				validateStatus: function (status) {return status == 200}
			}).then((response) => {
				var result = response && response.data || {};
				if(result.status){
					if(result.data.coupons){
						this.coupons = result.data.coupons;
					} else if(result.status == 'error') {
						this.errors.push(result.message.replace(/(<([^>]+)>)/gi, ""));
					}
				} else {
					this.errors.push("Eroare server");
				}
			}).finally(() => {
				this.loading.code = false;
			});
		  // Should remove coupon
		},
		removeCouponOld(code){
			if(this.loading.code) return;
			this.loading.code = true;
			setTimeout(()=>{
				var index = this.coupons.findIndex(c => c.code == code);
				if(index < 0) return;
				this.coupons.splice(index,1);
				this.loading.code = false;
			}, 500);
		  // Should remove coupon
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
				coupon_code: this.code,
				coupon_type: this.type,
			};
			axios.post(url,objToSerialize({
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				...s_params
				}), {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded charset=UTF-8',
					'X-Requested-With': '<?php echo $_SERVER['HTTP_X_REQUESTED_WITH']; ?>',
				},
				validateStatus: function (status) {return status == 200}
			}).then((response) => {
				var result = response && response.data || {};
				if(result.status){
					if(result.data.coupons){
						this.code = '';
						this.coupons = result.data.coupons;
					} else if(result.status == 'error') {
						this.errors.push(result.message.replace(/(<([^>]+)>)/gi, ""));
					}
				} else {
					this.errors.push("Eroare server");
				}
			}).finally(() => {
				this.loading.code = false;
			});
		  // Should remove coupon
		},
	},
	watch: {
		'discounted_total':{
			handler(newValue, oldValue){
				console.warn('discounted_total', newValue, this.total - this.discounted_total)
				this.$emit('update:modelValue', this.total - this.discounted_total);
			},
			// immediate: true,
		},
	},
}
