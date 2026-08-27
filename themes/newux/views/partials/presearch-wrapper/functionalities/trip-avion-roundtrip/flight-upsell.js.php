import FormLegend from '../../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
  emits: ['save', 'update:modelValue'],
  props: {
      flight_data: {
          type: Object,
          default: () => ({}),
      },
      loading: {
          type: Object,
          default: () => ({}),
      },
      upsells: {
          type: Object,
          default: () => ({}),
      },
      single: {
          type: Boolean,
          default: false,
      },
      modelValue: {
          type: String,
          default: '',
      },
  },
  components:{
		'FormLegend': FormLegend,
	},
	data: () => ({
		view_offer_count: 10,
    step: 0,
    dialog: false,
    errors: [],
    texts: Object.freeze({
    }),
    validations:Object.freeze([
    ]),
  }),
	template : `<div class="flight-upsell">
	<v-expansion-panels>
		<v-expansion-panel>
		<v-expansion-panel-title>
			<FormLegend title="Optiuni upgrade" subtitle="Personalizeaza-ti călătoria: alege optiunile de upgrade pentru un zbor mai confortabil!"></FormLegend>
		</v-expansion-panel-title>
		<v-expansion-panel-text>
			<v-list class="bg-background pa-4 rounded-theme mb-2 d-flex flex-wrap ga-3">
				<v-list-item :active="item.Code == modelValue" v-for="(item, itemIndex) in upsells.slice(0, view_offer_count)" :value="item.Code" @click="(step=itemIndex,$emit('update:modelValue', item.Code))" class="text-left rounded-theme">
				  <template v-slot:title>{{ getObjectDotPathValue(item,'FareDetails.BrandedFare.BrandDetails.*.Name', []).filter((name, index, arr) => index == arr.indexOf(name)).join(' | ') }}</template>
				  <template v-slot:subtitle>
					<div class="d-flex justify-space-between">
					  <small>{{ format_price(getObjectDotPathValue(item,'Price.Amount',0), getObjectDotPathValue(item,'Price.Currency'), 1) }}</small>
					  <small v-if="modelValue != item.Code && !loading[item.Code]">{{ ((s1 = getObjectDotPathValue(item,'Price.Amount',0) - getObjectDotPathValue(flight_data,'Price',0)), (s1 >= 0 && '+' || '') + format_price(s1, getObjectDotPathValue(item,'Price.Currency'), 1)) }}</small>
					</div>
				  </template>
				  <template v-slot:append  v-if="modelValue == item.Code || loading[item.Code]">
					<span class="d-inline-block overflow-hidden">
					<v-progress-circular  v-if="loading[item.Code]"
					  indeterminate
					  :color="modelValue == item.Code ? 'primary' : 'default'"
					></v-progress-circular>
					<v-icon v-else class="mr-auto" color="primary" icon="mdi-check-outline"></v-icon>
					</span>
				  </template>
				</v-list-item>
			  </v-list>
			  <template v-if="upsells && upsells[view_offer_count]">
					<div class="text-center my-3">
					<v-btn @click="view_offer_count+=5"
						class="ms-2 see-more-offers-button"
						size="large"
						text="Vezi mai multe optiuni"
						variant="outlined"
					></v-btn>
					</div>
				  </template>
			  <v-window v-model="step" class="w-100 d-table">
				<v-window-item v-for="(item, itemIndex) in upsells" :value="itemIndex" class="">
				  <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4 d-flex flex-column" rounded="theme">
					<h3 class="text-h6 font-weight-light mb-2 ps-4">
					  {{ getObjectDotPathValue(item,'FareDetails.BrandedFare.BrandDetails.0.Name') }}
					</h3>
					<div class="overflow-y-auto px-4">
					  <div v-for="type in ['included', 'chargeable', 'unknown']" class="mb-4">
						<h5>{{ type }}</h5>
						<div v-for="service in (getObjectDotPathValue(item,'FareDetails.BrandedFare.BrandDetails.0.Services') || []).filter((v) => v.ChargeType == type)">
						  {{ service.Name }}
						</div>
					  </div>
					</div>
				  </v-list>
				</v-window-item>
			  </v-window>
		</v-expansion-panel-text>
		</v-expansion-panel>
	</v-expansion-panels>
  </div>
	`,

  methods: {
    clearValidations(){
      this.errors = [];
    },
    save(){
      //this.kept = Object.assign({}, this.saved);
      this.$emit('update:modelValue', getObjectDotPathValue(this.upsells,'upsell.' + this.step + '.Code'));
      return true;
      // emit
    },
    validate(){
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
        v && this.errors.push(this.texts[v]);
        return !v;
      })
      return !this.errors.length;
    }
  },
  computed: {
    button_text:{
      get() {
        return 'test';
      },
    },
  },
	beforeCreate: function(){
		// console.warn('created', this);
	},
  watch:{
    'dialog': {
      handler(newValue, oldValue){
        if(!this.kept){
          this.saved = {}
        } else {
          this.saved = Object.assign({}, this.kept);
        }
      },
    },
    'modelValue': {
      handler(newValue, oldValue){
        console.log('upsell', newValue)
      },
      immediate: true,
    },
  }
}
