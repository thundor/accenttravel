export default {
  emits: ['save'],
  props: {
      modelValue: {
          type: Object,
          default: () => ([]),
      },
  },
	data: () => ({
    selected_location_index: undefined
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
        :item-props="(item) => {
            return {
              active: item.index == selected_location_index,
              class: 'rounded-theme mb-2',
            }
          }"
      >
        <template v-slot:prepend="{ item }">
          <v-icon icon="mdi-history"></v-icon>
        </template>
        <template v-slot:title="{ item }">
          <div v-if="item">
            <strong v-html="item.CityName"></strong>
            <small v-if="item.LocationId" class="pl-4" v-html="item.LocationName"></small>
          </div>
        </template>
      </v-list>
  </div>
	`,
  methods: {
    save(){
      // console.warn('save', this.selected_location);
      this.$emit('save', {...this.selected_location});
      this.selected_location_index = undefined;
      return true;
    },
    validate(){
      return true;
    },
  },
  computed: {
    selected_location:{
      get() { return undefined === this.selected_location_index ? undefined : this.groupedSortedResults[this.selected_location_index] },
    },
    groupedSortedResults:{
      get() {
        var r = {
          grouped: {},
          results: [],
        };
        if(this.modelValue && this.modelValue){
          this.modelValue.forEach((item) => {
            var i = item.origin;
            if(i && undefined === r.grouped[i.CountryId + '-' + i.CityId + '-' + i.LocationId]){
              r.grouped[i.CountryId + '-' + i.CityId + '-' + i.LocationId] = 1;
              r.results.push(Object.assign(item.origin, {date: new Date(item.date), index: r.results.length}));
            }
          })
        }
        return r.results;
      },
    },
  },
	beforeCreate: function() {
    // console.log('recent', this);
	}
}
