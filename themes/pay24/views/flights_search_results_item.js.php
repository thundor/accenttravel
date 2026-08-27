export default {
  emits: ['show_details','select_flight'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      company_images: {
          type: Object,
          default: () => ({}),
      },
      filters: {
          type: Object,
          default: () => ({}),
      },
      passenger_count: {
          type: Number,
          default: () => (0),
      },
  },
	data: () => ({}),
	template : `
  <v-card
    class="w-100 rounded-theme bg-background mb-4"
    @click="$emit('show_details', modelValue)"
  >
    <v-card-text class="pa-4 pb-0">
      <template v-for="(route, routeIndex) in modelValue.Combination||[]">
        <hr v-if="routeIndex" class="my-4" style="border-color: transparent;
    margin: 0 -15px;
    border-bottom-width: 0;
    height: 1px !important;
    box-shadow: 0px 0px 2px rgb(var(--v-theme-surface)) inset;
    border-left-width: 0;
    border-right-width: 0;"/>
        <div class="d-flex justify-space-between mb-4">
          <div class="d-flex flex-row align-center justify-start" style="gap:15px;">
            <img v-if="company_images[route.Segment[0].Carrier.Marketing.Code]" :src="company_images[route.Segment[0].Carrier.Marketing.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
            <span class="color-dark-light" v-text="route.Segment[0].Carrier.Marketing._"></span>
          </div>
          <v-icon class="color-dark-light" :icon="!routeIndex ? 'mdi-airplane-takeoff' : 'mdi-airplane-landing'"></v-icon>
        </div>
        <div class="d-flex justify-space-between">
          <div class="d-flex flex-column pe-2">
            <span class="text-h5" style="font-weight: 300;">{{ route.Segment[0].Origin.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</span>
            <small class="color-dark-light">{{ route.Segment[0].Origin.Airport.CityCode }} - {{ formatDateDM(route.Segment[0].Origin.Date) }}</small>
          </div>
          <div class="d-flex flex-column flex-grow-1 text-center pe-2">
            <span class="color-dark-light">{{ durationToFormatted(route.Duration) }}</span>
            <v-timeline class="escale-timeline" line-thickness="1" direction="horizontal" style="--v-border-color:133,147,162;--v-border-opacity:1;--v-theme-on-surface-variant: 133,147,162;max-width: 140px;margin: 0 auto;">
              <v-timeline-item size="10px" class="nodot" v-if="route.Segment.length == 1">
              </v-timeline-item>
              <v-timeline-item v-for="bulina in route.Segment.length-1" size="6px">
              </v-timeline-item>
            </v-timeline>
            <span :class="route.Segment.length == 1 ? 'color-dark-light' : 'text-primary'">{{ route.Segment.length == 1 ? 'direct' : ((route.Segment.length - 1) + ' ' + (route.Segment.length == 2 ? 'escala' : 'escale')) }}</span>
          </div>
          <div class="d-flex flex-column ps-2">
            <span class="text-h5" style="font-weight: 300;">{{ route.Segment[route.Segment.length-1].Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</span>
            <small class="color-dark-light">{{ route.Segment[route.Segment.length-1].Destination.Airport.CityCode }} - {{ formatDateDM(route.Segment[route.Segment.length-1].Destination.Date) }}</small>
          </div>
        </div>
		<small class="d-flex flex-column" v-if="modelValue.Flight.BrandedFare && modelValue.Flight.BrandedFare.BrandDetails && modelValue.Flight.BrandedFare.BrandDetails[routeIndex]">
			<span v-if="modelValue.Flight.BrandedFare.BrandDetails[routeIndex].Cabin"><b>Clasa:</b> {{ modelValue.Flight.BrandedFare.BrandDetails[routeIndex].Cabin }}</span>
			<span v-if="modelValue.Flight.BrandedFare.BrandDetails[routeIndex].Code"><b>Fare Family:</b> {{ modelValue.Flight.BrandedFare.BrandDetails[routeIndex].Code }}</span>
			<span v-if="modelValue.Flight.BrandedFare.BrandDetails[routeIndex].Description">{{ modelValue.Flight.BrandedFare.BrandDetails[routeIndex].Description }}</span>
		</small>
      </template>
    </v-card-text>
    <v-card-actions class="bg-background2 pa-4 align-center justify-space-between mt-4">
      <div class="d-flex flex-column">
        <span class="text-h5">{{ format_price(modelValue.Flight.PriceDetail.Amount, modelValue.Flight.PriceDetail.Currency) }}</span>
        <small class="color-dark-light" style="line-height:1.2">Tarif <b>final</b> pentru {{ passenger_count }} {{ passenger_count == 1 ? 'pasager' : 'pasageri' }}, <b class="d-inline-block">toate taxele incluse</b></small>
      </div>
      <v-btn
        variant="outlined"
        class="text-none px-4"
        size="large"
        @click.stop="$emit('select_flight', modelValue)"
      >
        Selecteaza
      </v-btn>
    </v-card-actions>
  </v-card>
	`,
  methods: {
  },
  computed: {
  },
  watch:{
    'modelValue': {
      handler(newValue, oldValue){
        // console.warn('mv', newValue);
      },
      immediate: true
    },
  }
}
