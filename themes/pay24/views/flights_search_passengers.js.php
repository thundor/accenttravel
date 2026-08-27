export default {
  emits: ['save'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
  },
	data: () => ({
    kept: undefined,
    saved: {},
    dialog: false,
    errors: [],
    texts: Object.freeze({
      at_least_one_sen_adt: "Calatoria trebuie sa contina cel putin un adult sau un senior",
    }),
    validations:Object.freeze([
      function(){ return this.adt+this.sen < 1 ? 'at_least_one_sen_adt' : null },
    ]),
  }),
	template : `
<Modal v-model="dialog">
  <template v-slot:activator="{ props }">
    <v-btn v-bind="props" class="rounded-theme w-100 w-100 justify-space-between d-flex py-4 modal-button text-none" append-icon="mdi-chevron-right" variant="outlined">{{ button_text }}</v-btn>
  </template>
      <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height" rounded="theme">
        <v-list-item-title class="pa-4 pb-2 text-h5">Cine zboara?</v-list-item-title>
        <v-messages class="pl-4" color="error" :active="!!errors.length" :messages="errors"></v-messages>
        
        <v-list-item class="pa-0" theme="dark">
          <div class="d-flex pa-2 ps-4 pe-xs-0 pe-4 align-center flex-column flex-xs-row">
            <div class="d-flex w-100 order-1" style="gap:5px;flex-basis:0;">
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="adt--">
              <v-icon>
                mdi-minus
              </v-icon>
            </v-btn>
            <input type="number" readonly class="rounded-theme border-sm square-but-inp text-black text-center" in="0" v-model="adt" max="10" step="1" style="border-color: rgb(var(--v-theme-secondary)) !important; outline:none;" />
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="adt++">
              <v-icon>
                mdi-plus
              </v-icon>
            </v-btn>
            </div>
            <div class="ps-xs-3 w-100 order-0 order-xs-1 pb-4 pb-xs-0">
              <strong>{{ translate_ptc['ADT'][1] }}</strong><br />
              <v-list-item-subtitle>Varsta intre 18 si 60 ani</v-list-item-subtitle>
            </div>
          </div>
        </v-list-item>
        <v-list-item class="pa-0" theme="dark">
          <div class="d-flex pa-2 ps-4 pe-xs-0 pe-4 align-center flex-column flex-xs-row">
            <div class="d-flex w-100 order-1" style="gap:5px;flex-basis:0;">
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="sen--">
              <v-icon>
                mdi-minus
              </v-icon>
            </v-btn>
            <input type="number" readonly class="rounded-theme border-sm square-but-inp text-black text-center" min="0" v-model="sen" max="10" step="1" style="border-color: rgb(var(--v-theme-secondary)) !important; outline:none;" />
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="sen++">
              <v-icon>
                mdi-plus
              </v-icon>
            </v-btn>
            </div>
            <div class="ps-xs-3 w-100 order-0 order-xs-1 pb-4 pb-xs-0">
              <strong>{{ translate_ptc['SEN'][1] }}</strong><br />
              <v-list-item-subtitle>Varsta peste 60 ani</v-list-item-subtitle>
            </div>
          </div>
        </v-list-item>
        <v-list-item class="pa-0" theme="dark">
          <div class="d-flex pa-2 ps-4 pe-xs-0 pe-4 align-center flex-column flex-xs-row">
           <div class="d-flex w-100 order-1" style="gap:5px;flex-basis:0;">
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="chd--">
              <v-icon>
                mdi-minus
              </v-icon>
            </v-btn>
            <input type="number" readonly class="rounded-theme border-sm square-but-inp text-black text-center" min="0" v-model="chd" max="10" step="1" style="border-color: rgb(var(--v-theme-secondary)) !important; outline:none;" />
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="chd++">
              <v-icon>
                mdi-plus
              </v-icon>
            </v-btn>
            </div>
            <div class="ps-xs-3 w-100 order-0 order-xs-1 pb-4 pb-xs-0">
              <strong>{{ translate_ptc['CHD'][1] }}</strong><br />
              <v-list-item-subtitle>Varsta intre 2 si 12 ani</v-list-item-subtitle>
            </div>
          </div>
        </v-list-item>
        <v-list-item class="pa-0" theme="dark">
          <div class="d-flex pa-2 ps-4 pe-xs-0 pe-4 align-center flex-column flex-xs-row">
           <div class="d-flex w-100 order-1" style="gap:5px;flex-basis:0;">
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="inf--">
              <v-icon>
                mdi-minus
              </v-icon>
            </v-btn>
            <input type="number" readonly class="rounded-theme border-sm square-but-inp text-black text-center" min="0" v-model="inf" max="10" step="1" style="border-color: rgb(var(--v-theme-secondary)) !important; outline:none;" />
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="inf++">
              <v-icon>
                mdi-plus
              </v-icon>
            </v-btn>
            </div>
            <div class="ps-xs-3 w-100 order-0 order-xs-1 pb-4 pb-xs-0">
              <strong>{{ translate_ptc['INF'][1] }}</strong><br />
              <v-list-item-subtitle>Varsta intre 0 si 2 ani</v-list-item-subtitle>
            </div>
          </div>
        </v-list-item>
        <v-list-item class="pa-0" theme="dark">
          <div class="d-flex pa-2 ps-4 pe-xs-0 pe-4 align-center flex-column flex-xs-row">
           <div class="d-flex w-100 order-1" style="gap:5px;flex-basis:0;">
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="ins--">
              <v-icon>
                mdi-minus
              </v-icon>
            </v-btn>
            <input type="number" readonly class="rounded-theme border-sm square-but-inp text-black text-center" min="0" v-model="ins" max="10" step="1" style="border-color: rgb(var(--v-theme-secondary)) !important; outline:none;" />
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="ins++">
              <v-icon>
                mdi-plus
              </v-icon>
            </v-btn>
            </div>
            <div class="ps-xs-3 w-100 order-0 order-xs-1 pb-4 pb-xs-0">
              <strong>{{ translate_ptc['INS'][1] }}</strong><br />
              <v-list-item-subtitle>Varsta intre 0 si 2 ani</v-list-item-subtitle>
            </div>
          </div>
        </v-list-item>
        <v-list-item class="pa-0" theme="dark">
          <div class="d-flex pa-2 ps-4 pe-xs-0 pe-4 align-center flex-column flex-xs-row">
           <div class="d-flex w-100 order-1" style="gap:5px;flex-basis:0;">
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="yth--">
              <v-icon>
                mdi-minus
              </v-icon>
            </v-btn>
            <input type="number" readonly class="rounded-theme border-sm square-but-inp text-black text-center" min="0" v-model="yth" max="10" step="1" style="border-color: rgb(var(--v-theme-secondary)) !important; outline:none;" />
            <v-btn rounded="theme" color="secondary" class="square-but-inp" @click="yth++">
              <v-icon>
                mdi-plus
              </v-icon>
            </v-btn>
            </div>
            <div class="ps-xs-3 w-100 order-0 order-xs-1 pb-4 pb-xs-0">
              <strong>{{ translate_ptc['YTH'][1] }}</strong><br />
              <v-list-item-subtitle>Varsta intre 12 si 18 ani</v-list-item-subtitle>
            </div>
          </div>
        </v-list-item>
      </v-list>


      <template v-slot:footer="{ props }">
        <v-btn class="text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
        <v-btn class="text-none font-weight-normal" size="x-large" style="flex:1;" color="primary" rounded="theme" @click="validate() && save() && (dialog = false)">
          Confirma
        </v-btn>
      </template>
</Modal>
	`,

  methods: {
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
    button_text:{
      get() { 
        if(this.kept){
          var txts = [];

          if(this.kept.adt){
            txts.push(this.kept.adt + ' ' + translate_ptc['ADT'][this.kept.adt == 1 ? 0 : 1]);
          }
          if(this.kept.sen){
            txts.push(this.kept.sen + ' ' + translate_ptc['SEN'][this.kept.sen == 1 ? 0 : 1]);
          }
          if(this.kept.yth){
            txts.push(this.kept.yth + ' ' + translate_ptc['YTH'][this.kept.yth == 1 ? 0 : 1]);
          }
          if(this.kept.chd){
            txts.push(this.kept.chd + ' ' + translate_ptc['CHD'][this.kept.chd == 1 ? 0 : 1]);
          }
          if(this.kept.inf + this.kept.ins){
            txts.push((this.kept.inf + this.kept.ins) + ' ' + general_translate_ptc['INF'][this.kept.inf + this.kept.ins == 1 ? 0 : 1]);
          }
          if(txts.length) return txts.join(', ');
        }
        return "Pasageri";
      },
    },
    inf:{
      get() { return this.saved.inf || 0 },
      set(newValue){ isNaN(newValue) || ('' + parseInt(newValue) !== '' + newValue) || newValue < 0 ? 0 : (newValue > 6 ? 6 : this.saved.inf = newValue) },
    },
    ins:{
      get() { return this.saved.ins || 0 },
      set(newValue){ isNaN(newValue) || ('' + parseInt(newValue) !== '' + newValue) || newValue < 0 ? 0 : (newValue > this.sen + this.adt ? this.sen + this.adt : this.saved.ins = newValue) },
    },
    chd:{
      get() { return this.saved.chd || 0 },
      set(newValue){ isNaN(newValue) || ('' + parseInt(newValue) !== '' + newValue) || newValue < 0 ? 0 : (newValue > 6 ? 6 : this.saved.chd = newValue) },
    },
    yth:{
      get() { return this.saved.yth || 0 },
      set(newValue){ isNaN(newValue) || ('' + parseInt(newValue) !== '' + newValue) || newValue < 0 ? 0 : (newValue > 6 ? 6 : this.saved.yth = newValue) },
    },
    sen:{
      get() { return this.saved.sen || 0 },
      set(newValue){ isNaN(newValue) || ('' + parseInt(newValue) !== '' + newValue) || newValue < 0 ? 0 : (newValue > 6 ? 6 : this.saved.sen = newValue) },
    },
    adt:{
      get() { return this.saved.adt || 0 },
      set(newValue){ isNaN(newValue) || ('' + parseInt(newValue) !== '' + newValue) || newValue < 0 ? 0 : (newValue > 6 ? 6 : this.saved.adt = newValue) },
    },
  },
  watch:{
    'dialog': {
      handler(newValue, oldValue){
        if(newValue && !oldValue){
          if(!this.kept){
            this.saved = {adt: 1}
          } else {
            this.saved = Object.assign({}, this.kept);
          }
          this.touchmoveY = 0;
        }
      },
    },
    'adt': {
      handler(newValue, oldValue){
        if(this.ins){
          var i = 0 + this.ins;
          this.ins = 0;
          this.ins = i;
        }
      },
    },
    'sen': {
      handler(newValue, oldValue){
        if(this.ins){
          var i = 0 + this.ins;
          this.ins = 0;
          this.ins = i;
        }
      },
    },
    'modelValue': {
      handler(newValue, oldValue){
        if(!newValue) return;
        var types = ['adt', 'chd', 'sen', 'inf', 'ins', 'yth'];
        for(var i in types){
          var type = types[i];
          this[type] = newValue[type] || 0;
        }
        this.validate() && this.save()
      },
    },
    'saved': {
      handler(newValue, oldValue){
        this.clearValidations();
      },
      deep: true
    }
  }
}
