export default {
  emits: ['update:modelValue'],
  props: {
      modelValue: {
          type: Boolean,
          default: false,
      },
      title: {
          type: String,
          default: undefined,
      },
      allowClose: {
          type: Boolean,
          default: true,
      },
  },
	data: () => ({
    dialog: false,
    persistent: true,
    touchmoveY: 0,
    mover: 0,
    swipe: '',
  }),
	template : `
<v-dialog :persistent="persistent" v-model="dialog" fullscreen :scrim="false" transition="dialog-bottom-transition" v-on:click:outside.stop="(e) => {e.preventDefault();e.stopPropagation();e.stopImmediatePropagation();console.warn('updatedModelValue', e); return false;}">
  <template v-slot:activator="{ props }">
      <slot
          name="activator"
          :props="props"></slot>
    </template>
  <v-card style="overflow:hidden;">
  <div class="to_be_moved fill-height d-flex flex-column" :style="translateme">
    <div class="fill-height popup-closer to_move d-flex align-top justify-start flex-column" @click="allowClose && (dialog = false)" :class="{to_move_down: to_move_down}"
    v-touch="{
      down: (a) => swipeDown(a,'down'),
      move: (a) => swipeDown(a,'move',0),
      end: (a) => swipeDown(a,'end'),
    }">
	  <template v-if="title"><h5 v-html="title" class="mt-2 mx-auto"></h5></template>
      <h6 v-show="!mover && to_move_down" class="mb-5 pb-2 mx-auto mt-auto"><v-icon icon="mdi-close"></v-icon></h6>
    </div>
    <div class="popup-closer popup-closer-down to_move d-flex align-start justify-center order-1" @click="allowClose && (dialog = false)" :class="{to_move_up: to_move_up}"
    v-touch="{
      up: (a) => swipeDown(a,'up'),
      move: (a) => swipeDown(a,'move',1),
      end: (a) => swipeDown(a,'end'),
    }">
      <h6 v-show="mover && to_move_up" class="mt-5 pt-2"><v-icon icon="mdi-close"></v-icon></h6>
    </div>
    <div class="d-flex flex-column">
      <slot></slot>
      <div class="mt-auto justify-start d-flex pa-4 pt-0 pb-0" style="gap:15px;">
        <slot name="footer" :props="{close: () => allowClose && (dialog = false)}"></slot>
      </div>
    </div>
    </div>
  </v-card>
</v-dialog>
	`,

  methods: {
    swipeDown(event,type, mover){
      switch(type){
        case 'start':
          this.swipe = 'started';
        break;
        case 'move':
          this.mover = mover;
          this.touchmoveY = event.touchmoveY - event.touchstartY;
          this.move = 'ended';
        break;
        case 'end':
          this.swipe = 'ended';
          this.touchmoveY = 0;
        break;
        case 'up':
        case 'down':
			if(this.allowClose){
				this.touchmoveY = event.touchmoveY - event.touchstartY;
				this.dialog = false;
				this.$emit('update:modelValue', this.dialog)
				this.swipe = type;
			}
        break;
      }
      // console.log(this.swipe);
    },
  },
  computed: {
    translateme:{
      get() { var d = `transform: translate3d(0,${ (this.mover == 0 && this.touchmoveY>0 ? 150 : (this.mover && this.touchmoveY<0 ? -150 : 0)) }px, 0);`; return d; },
    },
    to_move_down:{
      get() { return this.touchmoveY > 0 },
    },
    to_move_up:{
      get() { return this.touchmoveY < 0 },
    },
  },
  mounted: function() {
      // console.log(this)
  },
  watch:{
    'dialog': {
      handler(newValue, oldValue){
        console.warn('showing dialog', newValue);
        this.touchmoveY = 0;
        this.mover = 0;
        this.swipe = '';
        this.$emit('update:modelValue', newValue)
      },
    },
    'modelValue': {
      handler(newValue, oldValue){
        console.warn('showing dialog def', newValue);
        this.dialog = newValue;
      },
    },
  }
}
