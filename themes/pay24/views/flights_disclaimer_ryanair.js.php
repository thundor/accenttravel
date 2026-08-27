export default {
	emits: [],
	props: {
		modelValue: {
			type: Boolean,
			default: (true),
		},
	},
	data: () => ({
		isdisabled: true,
		dialog: false,
		waited: false,
		waittime: '(7)',
	}),
	template : `<Modal v-model="dialog" :allowClose="!isdisabled">
  <template v-slot:activator="{ props }">
      <v-btn class="mr-4 ms-4 mb-4" size="x-large" :color="'secondary'" rounded="theme" v-bind="props">Politica Ryanair</v-btn>
  </template>
		<v-list subheader theme="light" class="ma-4 mt-0 max-height mb-4 pe-0" rounded="theme">
        <v-list-item-title class="pa-4 pb-2 text-h5">Important!</v-list-item-title>
      <div class="pa-4">
		<p>Conform politicii adoptate de compania aeriena <b class="text-error">Ryanair</b>, orice operatiune post-booking, voluntara sau involuntara, trebuie efectuata exclusiv de catre pasager pe site-ul companiei aeriene.</p>
		<p>Agentiile de turism, prin intermediul carora ati achizitionat biletele on-line sau off-line, nu au abilitatea de a procesa operatiuni de modificare a zborului si/sau de procesare a cererii de anulare si rambursarea sumelor avansate pentru achizitionarea biletelor de avion, in cazul zborururilor anulate din initiativa companiei, indiferent de cauza care a stat la baza anularii.</p>
		<p><span>Asadar, sumele de bani avansate pentru cumpararea biletelor in cazul zborurilor anulate din initiativa companiei aeriene Ryanair nu pot fi recuperate de la agentia de turism.</span></p>
		<p>Pentru mai multe informatii despre termenii si politica Ryanair va rugam sa accesati <a href="https://www.ryanair.com" target="_BLANK">www.ryanair.com.</a></p>
	  </div>
	  </v-list>
      <template v-slot:footer="{ props }">
        <v-btn :disabled="isdisabled" class="d-flex text-capitalize font-weight-normal cancel-button" size="x-large" :color="'secondary'" rounded="theme" @click="!isdisabled && (dialog = false)">Am inteles <span v-text="waittime"></span></v-btn>
      </template>
  </Modal>
	`,

  methods: {
  },
  computed: {
  },
	beforeCreate: function(){
		// console.warn('created', this);
	},
  watch:{
	  modelValue: {
		handler(newValue, oldValue){
			setTimeout(() => this.dialog = newValue, 1000);
		},
		immediate:true
    },
	'dialog': {
      handler(newValue, oldValue){
		  if(newValue){
			  if(!this.waited){
				  var modal_flight_ryanair_timer = 7;
					var modal_flight_ryanair_timer_interval = setInterval(() => {
						modal_flight_ryanair_timer--;
						this.waittime = '(' + modal_flight_ryanair_timer + ')';
						if(!modal_flight_ryanair_timer){
							this.waited = true;
							this.isdisabled = false;
							this.waittime = '';
							clearInterval(modal_flight_ryanair_timer_interval);
						}
					}, 1000)
			  }
		  }
        console.warn('openend Ryanair', newValue, this.dialog);
      },
		immediate:true
    },
  }
}
