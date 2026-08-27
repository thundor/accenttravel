export default {
  emits: ['save', 'update:modelValue'],
  props: {
      base_flight_data: {
          type: Object,
          default: () => ({}),
      },
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
	data: () => ({
    step: 0,
    dialog: false,
    errors: [],
    texts: Object.freeze({
    }),
    validations:Object.freeze([
    ]),
  }),
	template : `<Modal v-model="dialog">
  <template v-slot:activator="{ props }">
      <v-list class="bg-background pa-4 rounded-theme mb-2">
        <h4 class="text-left mb-4" style="
            font-weight: normal;
        ">Optiuni upgrade</h4>
        <v-list-item :active="item.Code == modelValue" v-for="(item, itemIndex) in upsells.upsell" :value="item.Code" @click="$emit('update:modelValue', item.Code)" class="text-left rounded-theme">
          <template v-slot:prepend>
            <v-btn icon="mdi-eye-outline" class="mr-4" v-bind="props" @click.stop="step=itemIndex"></v-btn>
          </template>
          <template v-slot:title>{{ getObjectDotPathValue(item,'FareDetails.BrandedFare.BrandDetails.0.Name') }}</template>
          <template v-slot:subtitle>
            <div class="d-flex justify-space-between">
              <small>{{ format_price(getObjectDotPathValue(item,'Price.Amount',0), getObjectDotPathValue(item,'Price.Currency')) }}</small>
              <small v-if="modelValue != item.Code && !loading[item.Code]">{{ ((s1 = getObjectDotPathValue(item,'Price.Amount',0) - getObjectDotPathValue(base_flight_data,'Price',0)), (s1 >= 0 && '+' || '') + format_price(s1, getObjectDotPathValue(item,'Price.Currency'))) }}</small>
            </div>
          </template>
          <template v-slot:append v-if="modelValue == item.Code || loading[item.Code]">
            <v-progress-circular  v-if="loading[item.Code]"
              indeterminate
              :color="modelValue == item.Code ? 'primary' : 'default'"
            ></v-progress-circular>
            <v-icon v-else class="mr-auto" color="primary" icon="mdi-check-outline"></v-icon>
          </template>
        </v-list-item>
      </v-list>
  </template>
      <v-window v-model="step" class="w-100 d-table fill-height">
        <v-window-item v-for="(item, itemIndex) in upsells.upsell" :value="itemIndex" class="fill-height">
          <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4 d-flex flex-column" rounded="theme">
            <h3 class="text-h6 font-weight-light mb-2 ps-4">
              {{ getObjectDotPathValue(item,'FareDetails.BrandedFare.BrandDetails.0.Name') }}
            </h3>
            <div class="overflow-y-auto px-4" style="height:100vh !important">
              <div v-for="type in ['included', 'chargeable']" class="mb-4">
                <h5>{{ type }}</h5>
                <div v-for="service in (getObjectDotPathValue(item,'FareDetails.BrandedFare.BrandDetails.0.Services') || []).filter((v) => v.ChargeType == type)">
                  {{ service.Name }}
                </div>
              </div>
            </div>
          </v-list>
        </v-window-item>
      </v-window>
      <template v-slot:footer="{ props }">
        <v-btn class="d-flex text-capitalize font-weight-normal cancel-button" size="x-large" :color="'secondary'" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
        <v-btn class="d-flex text-capitalize font-weight-normal" size="x-large" style="flex:1;" :color="'primary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Alege'"></v-btn>
      </template>
  </Modal>
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
