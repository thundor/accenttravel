export default {
  emits: ['save'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      single: {
          type: Boolean,
          default: false,
      },
  },
	data: () => ({
    kept: undefined,
    saved: {},
    date2: {},
    ddate: {},
    dialog: false,
    errors: [],
    texts: Object.freeze({
      no_start_date: "Alegeti data de intoarcere",
      no_end_date: "Alegeti data de intoarcere",
    }),
    validations:Object.freeze([
      function(){ return !this.saved.date ? 'no_start_date' : null },
    ]),
  }),
	template : `<Modal v-model="dialog">
  <template v-slot:activator="{ props }">
    <v-btn v-bind="props" class="rounded-theme w-100 w-100 justify-space-between d-flex py-4 modal-button text-capitalize" append-icon="mdi-calendar" variant="outlined">
      {{ button_text }}
    </v-btn>
  </template>
      <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
        <v-list-item-title class="pa-4 pb-2 pt-0 text-h5" v-text="!single ? 'Plecare - Sosire' : 'Plecare'"></v-list-item-title>
        <Datepicker @internal-model-change="changed" @update:model-value="changed2" v-if="date" :class="{'ranged-picker': !single, 'range-picked': !!this.saved.date}" v-model="date" no-disabled-range :year-range="year_range" month-name-format="long" locale="ro" inline auto-apply :range="!single" :multi-calendars="multicalendars" :min-date="new Date()" calendar-class-name="v-theme--dark" month-change-on-scroll class="px-4 w-100" :enable-time-picker="false"></Datepicker>
        <v-messages class="pl-4" color="error" :active="!!errors.length" :messages="errors"></v-messages>
      </v-list>
      <template v-slot:footer="{ props }">
		<v-btn class="d-flex text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
        <v-btn class="d-flex text-capitalize font-weight-normal px-0 py-0" size="x-large" style="flex:1;gap:5px;" :color="selectedDatesAreValid ? 'primary' : 'secondary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="selectedDatesAreValid ? button_text2 : (!single ? 'Alege perioada' : 'Alege data')"></v-btn>
      </template>
  </Modal>
	`,

  methods: {
    changed2(a){
      console.log('changed2', a);
      this.ddate = a;
      return true;
    },
    changed(a){
      console.log('changed', a);
      this.ddate = a;
      return true;
    },
    clearValidations(){
      this.errors = [];
    },
    save(){
      this.kept = Object.assign({}, this.saved);
      this.$emit('save', this.saved);
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
    selectedDatesAreValid:{
      get() { return this.ddate ? (this.ddate.length >= 1 && (this.single || this.ddate.length==2 && this.ddate[1] && this.ddate[1] >= this.ddate[0])) : false; },
    },
    button_text:{
      get() { 
        if(this.kept && this.kept.date){
          return dateIntervalFormatted(this.kept.date, !this.single && this.kept.days)
        }
        return !this.single ? 'Plecare - Sosire' : 'Plecare';
      },
    },
    button_text2:{
      get() { 
        if(this.saved){
          var txts = [];

          if(this.saved.date){
            /*
            txts.push(this.saved.date.toLocaleDateString('ro', {
              month: "short",
              day: "numeric"
            }));
            */
            txts.push('<b style="line-height: 0.7">' + dateIntervalFormatted(this.saved.date, !this.single && this.saved.days) + '</b>')
          }
          /*
          if(!this.single && this.saved.days){
            txts.push('+ ' + this.saved.days);
          }
          */
          if(txts.length) {
            txts.unshift('Alege')
            return txts.join(' ');
          }
        }
        return !this.single ? 'Plecare - Sosire' : 'Plecare';
      },
    },
    year_range:{
      get() { 
        var currentYear = new Date().getFullYear();
        return [currentYear, currentYear+2]
      },
    },
    multicalendars:{
      get() { return (!this.single && (window.innerHeight > 650) || (window.innerWidth > 650)) },
    },
    date:{
      get() { var d; var r = !this.saved.date ? undefined : (this.single ? this.saved.date : [this.saved.date, (d = new Date(this.saved.date), d.setDate(d.getDate() + (this.saved.days||0)), d)]);  return r || this.date2;},
      set(newValue){console.warn('asdf2', newValue); this.single ? (this.saved.date = newValue || undefined,this.saved.days=0) : (!newValue || !newValue.length ? (this.saved.date = undefined,this.saved.days=0) : (this.saved.date = new Date(!newValue[1] ? newValue[0] : Math.min(newValue[0], newValue[1])), this.saved.days = !newValue[1] ? 0 : Math.floor((Math.max(newValue[0], newValue[1]) - this.saved.date) / 86400000)));},
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
        // console.warn('date', newValue);
        if(!newValue) return;
        var types = ['date','days'];
        for(var i in types){
          var type = types[i];
          this.saved[type] = newValue[type] || undefined;
        }
        this.validate() && this.save()
      }
    },
    'date': {
      handler(newValue, oldValue){
        console.warn('date', newValue);
      },
      immediate: true,
      deep: true,
    },
    /* 'single': {
      handler(newValue, oldValue){
        if(this.saved.date){
          if(newValue){
            this.saved.days = 0;
          } else {
            this.saved.days = 1;
          }
          this.save();
        }
      },
      deep: true
    },*/
    'saved': {
      handler(newValue, oldValue){
        this.clearValidations();
      },
      deep: true
    }
  }
}
