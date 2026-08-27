export default {
  components : {
  },
	data: () => ({
		documents: [],
	}),
	template : `
  <div class="general-order order-details" v-if="order_data">
    <v-list-subheader class="pl-4">Sumar comanda</v-list-subheader>
	<div class="summary bg-background rounded-theme pa-4">
		<div class="v-list-item-title d-flex justify-space-between"><strong id="datetoch">Data:</strong><strong class="text-primary">{{ formatDateFull(order_data.time_created) }}</strong></div>
		<div class="v-list-item-title d-flex justify-space-between"><strong>ID comanda:</strong><strong class="text-primary">#{{ order_data.id }}</strong></div>
		<div class="v-list-item-title d-flex justify-space-between"><strong>Tip serviciu:</strong><strong class="text-primary">{{ order_data.type }}</strong></div>
		<div class="v-list-item-title d-flex justify-space-between"><strong>Pret:</strong><strong class="text-primary">{{ format_price(order_data.amount, order_data.currency) }}</strong></div>
	</div>
	
	<v-list-subheader class="pl-4">Documente</v-list-subheader>
	<div class="documents bg-background rounded-theme pa-4">
		<div v-for="document in documents">
			<a :href="'/pay24/order_details/' + order_data.id_hashed + '/download/' + document.type + '/' + document.Name" download @click="ob" class="mb-2 text-primary d-inline-block">{{ document.title || document.Name}}</a>
		</div>
		<div class="v-list-item-title d-flex justify-space-between"><small>Aici apar documente pe masura ce se emit...</small></div>
	</div>
  </div>
	`,
  methods: {
	  
  },
  mounted: function() {
	  this.documents = [];
	  if(order_data.invoice){
		this.documents.push({
			type: 'order'
			,title: 'Descarca factura'
			,Name: 'factura.pdf'
		});
	  }
	  if(order_data.trip_order){
		order_data.trip_order.Services.forEach((service) => {
			if(service.Documents && service.Documents.length){
				service.Documents.forEach((doc) => {
					var title = undefined;
					if(service.Type == 'flight'){
						title = 'Descarca bilet avion';
					}
					this.documents.push({
						...doc
						,type: service.Type
						,title: title
					});
				})
			}
		})
	  }
  }
}
