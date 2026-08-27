export default {
  components : {
	'Flight' : loadViewAsync('order_' + order_data.provider + '_flight'),
  },
	data: () => ({
	}),
	template : `
  <div class="flight-order order-details">
    <v-list-subheader class="pl-4 mt-2">Citybreak</v-list-subheader>
	  <Flight />
  </div>
	`,
  methods: {
  },
}
