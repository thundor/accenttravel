export default {
  emits: ['save'],
  props: {
      modelValue: {
          type: String,
          default: () => (undefined),
      },
  },
	data: () => ({
    classes: {
		'1': 'Economy',
		'2': 'First class',
		'3': 'Business',
		'4': 'Premium',
	},
    class: undefined,
    kept: undefined,
    saved: {
		class: '1',
	},
    dialog: false,
    errors: [],
    texts: Object.freeze({
      no_end_date: "Alegeti data de intoarcere",
    }),
    validations:Object.freeze([
      // function(){ return !this.single && !this.saved.days ? 'no_end_date' : null },
    ]),
  }),
	template : `<Modal v-model="dialog">
  <template v-slot:activator="{ props }">
    <v-btn v-bind="props" class="rounded-theme w-100 w-100 justify-space-between d-flex py-4 modal-button text-capitalize" append-icon="mdi-poll" variant="outlined">
      {{ button_text }}
    </v-btn>
  </template>
      <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
        <v-list-item-title class="pa-4 pb-2 pt-0 text-h5" v-text="'Clasa zbor'"></v-list-item-title>
			<v-radio-group color="primary" v-model="saved.class" class="ms-2 mt-2 mb-3 v-theme--dark" hide-details="true" >
				<v-radio v-for="(cls, val) in classes" :label="cls" :value="val"></v-radio>
			</v-radio-group>
        <v-messages class="pl-4" color="error" :active="!!errors.length" :messages="errors"></v-messages>
      </v-list>
      <template v-slot:footer="{ props }">
		<v-btn class="d-flex text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
        <v-btn class="d-flex text-capitalize font-weight-normal px-0 py-0" size="x-large" style="flex:1;gap:5px;" :color="'primary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Confirma'"></v-btn>
      </template>
  </Modal>
	`,

  methods: {
    clearValidations(){
      this.errors = [];
    },
    save(){
      this.kept = Object.assign({}, this.saved);
	  
	  console.warn('save flight class', this.saved.class);
	  
      this.$emit('save', this.saved.class);
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
        if(this.kept && this.kept.class && undefined !== this.classes[this.kept.class]){
          return this.classes[this.kept.class]
        }
        return "Clasa zbor";
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
        if(newValue && !oldValue){
          this.touchmoveY = 0;
        }
      },
    },
    'modelValue': {
      handler(newValue, oldValue){
        console.warn('flight class', newValue);
		this.saved.class = newValue;
        this.validate() && this.save()
      },
	  immediate:true
    },
    'saved': {
      handler(newValue, oldValue){
        this.clearValidations();
      },
      deep: true
    }
  }
}
