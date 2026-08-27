export default {
  emits: ['save'],
  props: {
      modelValue: {
          type: Array,
          default: () => ([]),
      },
  },
	data: () => ({
    selected_location_index: undefined,
	classes: {
		'1': 'Economy',
		'2': 'First class',
		'3': 'Business',
		'4': 'Premium',
	},
  }),
	template : `
  <div class="recent-searches" v-if="modelValue && modelValue && modelValue.length">
    <v-list-subheader class="pl-4 mt-2">Cautari recente</v-list-subheader>
    <v-list
        :items="groupedSortedResults"
        item-value="index"
        @click:select="(item) => ((item.value = selected_location_index != item.id), selected_location_index = item.id, item.value && (validate() ? save() && (dialog = false) : null) )"
        density="compact"
        mandatory
        lines="2"
        :item-props="(item) => {
            return {
              active: item.index == selected_location_index,
              class: 'rounded-theme mb-4 bg-background py-4',
            }
          }"
      >
        <template v-slot:prepend="{ item }">
          <v-icon icon="mdi-history" size="small" class="me-4"></v-icon>
        </template>
        <template v-slot:title="{ item }">
          <div v-if="item.origin && item.destination" class="font-weight-thin">
            <strong v-html="item.origin.CityName + ' - ' + item.destination.CityName"></strong>
            <small v-if="item.direct" class="pl-2" v-html="'(Direct)'"></small>
          </div>
        </template>
        <template v-slot:subtitle="{ item }" class="font-weight-thin">
          <strong class="text-capitalize" v-html="dateInterval(item)"></strong>
          <v-icon class="ms-2" v-if="item.flex" icon="mdi-plus-minus-box"></v-icon>
          <span class="ms-2" v-html="passengers(item)"></span>
		  
			<span class="ms-2 text-primary" v-if="item.cabine && classes[item.cabine]" v-html="classes[item.cabine]"></span>
        </template>
      </v-list>
  </div>
	`,
  methods: {
    save(){
      // console.warn('save', this.selected_location);
      this.$emit('save', this.selected_location);
      this.selected_location_index = undefined;
      return true;
    },
    dateInterval(item){
      if(item.date){
        return dateIntervalFormatted(item.date, '0' != item.type && item.days);
      }
    },
    validate(){
      return true;
    },
    passengers(item){
      var txts = [];
      if(item.passengers.adt){
        txts.push(item.passengers.adt + ' ' + (item.passengers.adt == 1 ? 'Adult' : 'Adulti'));
      }
      if(item.passengers.sen){
        txts.push(item.passengers.sen + ' ' + (item.passengers.sen == 1 ? 'Senior' : 'Seniori'));
      }
      if(item.passengers.yth){
        txts.push(item.passengers.yth + ' ' + (item.passengers.yth == 1 ? 'Tanar' : 'Tineri'));
      }
      if(item.passengers.chd){
        txts.push(item.passengers.chd + ' ' + (item.passengers.chd == 1 ? 'Copil' : 'Copii'));
      }
      if(item.passengers.inf + item.passengers.ins){
        txts.push((item.passengers.inf + item.passengers.ins) + ' ' + (item.passengers.inf + item.passengers.ins == 1 ? 'Infant' : 'Infanti'));
      }
      if(txts.length) return txts.join(', ');
    },
  },
  computed: {
    selected_location:{
      get() { return undefined === this.selected_location_index ? undefined : this.groupedSortedResults[this.selected_location_index] },
    },
    groupedSortedResults:{
      get() {
        var r = [];
        if(this.modelValue && this.modelValue){
          this.modelValue.forEach((item, i) => {
            r.push(Object.assign({index: i}, item, {date: new Date(item.date)}));
          })
        }
        return r;
      },
    },
  },
	beforeCreate: function() {
    // console.log('recent', this);
	},
	mounted: function() {
    // console.log('recent', this);

    if(this.modelValue && this.modelValue.length){
		/*
      setTimeout(() => {
        this.selected_location_index = 0;
        this.save();
      },500)
	  */
    }
	}
}
