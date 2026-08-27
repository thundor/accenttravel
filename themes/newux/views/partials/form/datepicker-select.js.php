import { components } from 'vuetify';
export default {
  name: "DatePickerSelect",
  extends: components.VInput,
  props: {
    referenceDate: {
      type: [String, Date],
      default: () => null,
    },
    maxDate: {
      type: [String, Date],
      default: () => null,
    },
    minDate: {
      type: [String, Date],
      default: () => null,
    },
    value: {
      type: [String, Date],
      default: () => null,
    },
    age: {
      type: [String, Array, Number],
      default: () => null,
    },
    returnType: {
      type: [String],
      default: () => null,
    },
  },
  computed: {
	internalReferenceDate() {
		var referenceDate = this.referenceDate;
		return 'string' === typeof referenceDate && new Date(referenceDate) || (referenceDate ? referenceDate : (new Date()));
	},
	internalMinDate() {
		var d;
		var minDate = this.minDate;
		var min_age = null === this.age ? null : ('object' != typeof this.age ? parseInt(this.age) : parseInt(this.age[1]));
		if(null != min_age && !isNaN(min_age) && min_age >= 0 && !this.minDate){
			minDate = (d = new Date(this.internalReferenceDate), d.setFullYear(d.getFullYear() - (min_age + 1)), d.setDate(d.getDate() + 1), d);
		}
		console.warn('minDate', min_age, minDate, this.internalReferenceDate, this);
		return 'string' === typeof minDate && new Date(minDate) || (minDate ? minDate : (d = new Date(this.internalReferenceDate), d.setFullYear(d.getFullYear() - 100), d));
	},
	internalMaxDate() {
		var d;
		var maxDate = this.maxDate;
		var max_age = null === this.age ? null : ('object' != typeof this.age ? parseInt(this.age) : parseInt(this.age[0]));
		if(null != max_age && !isNaN(max_age) && max_age >= 0 && !this.maxDate){
			maxDate = (d = new Date(this.internalReferenceDate), d.setFullYear(d.getFullYear() - (max_age)), d);
		}
		console.warn('maxDate', max_age, maxDate, this.internalReferenceDate);
		return 'string' === typeof maxDate && new Date(maxDate) || (maxDate ? maxDate : new Date(this.internalReferenceDate));
	},
	years() {
      const maxYear = this.internalMaxDate.getFullYear();
      const minYear = this.internalMinDate.getFullYear();
	  
	  // console.warn('minMaxYear', minYear, maxYear);
      const years = [];
      for (let i = maxYear; i >= minYear; i--) {
        years.push(i);
      }
	  if(years.length == 1){
		  this.selectedYear = years[0];
	  } else if(!years.length || this.selectedYear < years[years.length - 1] || this.selectedYear > years[0]){
		  this.selectedYear = null;
	  }
      return years;
	},
	months() {
      var months = [
        { title: '01 Ianuarie', value: 1 },
        { title: '02 Februarie', value: 2 },
        { title: '03 Martie', value: 3 },
        { title: '04 Aprilie', value: 4 },
        { title: '05 Mai', value: 5 },
        { title: '06 Iunie', value: 6 },
        { title: '07 Iulie', value: 7 },
        { title: '08 August', value: 8 },
        { title: '09 Septembrie', value: 9 },
        { title: '10 Octombrie', value: 10 },
        { title: '11 Noiembrie', value: 11 },
        { title: '12 Decembrie', value: 12 }
      ];
	  if(!this.selectedYear){
		  return months;
	  }
	  var ml = months.length;
	  if(this.internalMinDate){
		  const minDate = this.internalMinDate;
		  if(this.selectedYear == minDate.getFullYear()){
			  months = months.slice(minDate.getMonth());
		  }
	  }
	  if(this.internalMaxDate){
		  const maxDate = this.internalMaxDate;
		  if(this.selectedYear == maxDate.getFullYear()){
			  months = months.slice(0, maxDate.getMonth() - (ml - 1 - months.length));
		  }
	  }
	  if(months.length == 1){
		  this.selectedMonth = months[0].value;
	  } else if(!months.length || this.selectedMonth < months[0].value || this.selectedMonth > months[months.length - 1].value){
		  this.selectedMonth = null;
	  }
      return months;
	},
	days() {
		var days = Array.from({ length: (this.selectedYear && this.selectedMonth) && (new Date(this.selectedYear, this.selectedMonth, 0).getDate()) || 31 }, (_, i) => i + 1);
	  if(!this.selectedYear || !this.selectedMonth){
		  return days;
	  }
	  var dl = days.length;
	  if(this.internalMinDate){
		  const minDate = this.internalMinDate;
		  if(this.selectedYear == minDate.getFullYear() && this.selectedMonth == minDate.getMonth() + 1){
			  days = days.slice(minDate.getDate() - 1);
		  }
	  }
	  if(this.internalMaxDate){
		  const maxDate = this.internalMaxDate;
		  if(this.selectedYear == maxDate.getFullYear() && this.selectedMonth == maxDate.getMonth() + 1){
			  days = days.slice(0, maxDate.getDate() - (dl - days.length));
		  }
	  }
	  if(days.length == 1){
		  this.selectedDay = days[0];
	  } else if(!days.length || this.selectedDay < days[0] || this.selectedDay > days[days.length - 1]){
		  this.selectedDay = null;
	  }
      return days;
	},
  },
  data() {
    return {
      selectedYear: null,
      selectedMonth: null,
      selectedDay: null,
      internalValue: this.value,
    };
  },
  watch: {
    // If value changes externally, sync with the component's internal values
    value: {
      handler(val) {
        this.updateSelectorsFromValue(val);
      },
      immediate: true,
    },
    modelValue: {
      handler(val) {
        this.updateSelectorsFromValue(val);
      },
      immediate: true,
    },
    selectedDay:{
		handler: function(newVal) {
		  this.onDateChange();
		},
		immediate: true,
    },
    selectedYear:{
		handler: function(newVal) {
		  this.onDateChange();
		},
		immediate: true,
    },
    selectedMonth:{
		handler: function(newVal) {
		  this.onDateChange();
		},
		immediate: true,
    }
  },
  mounted() {
	  // console.warn(this.$attrs, this);
	},
  methods: {
    onFocus(name) {
		setTimeout(() => {
			if(this.$refs[name].isFocused){
				this.$refs[name].$el.querySelector('input').select();
				// console.warn('selecting');
			}
		}, 0)
    },
    autoSelectFirst(name){
		if(this.$refs[name] && this.$refs[name].search && this.$refs[name].filteredItems.length && (this.$refs[name].filteredItems.length != this.$refs[name].items.length)){
			this[name] = this.$refs[name].filteredItems[0].value
		}
	},
    openNext(a,name) {
		if(!a) return;
		var to_focus;
		if(null === this.selectedYear){
			to_focus = 'selectedYear';
		} else if(null === this.selectedMonth){
			to_focus = 'selectedMonth';
		} else if(null === this.selectedDay){
			to_focus = 'selectedDay';
		} else {
			if(name == 'selectedYear'){
				to_focus = 'selectedMonth';
			} else if(name == 'selectedMonth'){
				to_focus = 'selectedDay';
			}
		}
		if(to_focus){
			setTimeout(() => {
				if(this.$refs[to_focus].items.length > 1 && !this.$refs[to_focus].isFocused){
					// console.warn('focusing');
					this.$refs[name].menu = false;
					this.$refs[to_focus].focus();
					this.$refs[to_focus].isFocused = true;
					this.$refs[to_focus].menu = true;
				}
			}, 0)
		}
	},
    updateSelectorsFromValue(value) {
      if (value) {
        const date = new Date(value);
		if(!isNaN(date)){
			this.selectedYear = date.getFullYear();
			this.selectedMonth = date.getMonth() + 1;
			this.selectedDay = date.getDate();
		}
      }
    },
    onDateChange() {
		setTimeout(() => {
			let selectedDate;
		  if (null !== this.selectedYear && null !== this.selectedMonth && null !== this.selectedDay) {
			selectedDate = new Date(Date.UTC(this.selectedYear, this.selectedMonth - 1, this.selectedDay)).toISOString().replace(/T.*/,'');
		  } else {
			  selectedDate = null;
		  }
		  this.internalValue = selectedDate;
		  if(selectedDate && this.returnType && 'Date' === this.returnType){
			this.$emit("update:modelValue",  new Date(selectedDate + ' 00:00:00')); // Emit the selected date as YYYY-MM-DD
		  } else {
			this.$emit("update:modelValue",  selectedDate); // Emit the selected date as YYYY-MM-DD
		  }
		}, 0)
    }
  },
  template: `
  <v-input
    v-bind="$props"
    v-model="internalValue"
    :error-messages="errorMessages"
    :rules="rules"
	class="datepicker-select"
  >
    <template v-slot:default="{ isFocused, setTextFieldRef, inputValue, isDisabled }">
      <v-row>
        <!-- Year Selector -->
        <v-col cols="4">
          <v-autocomplete
            ref="selectedYear"
            v-model="selectedYear"
            :items="years"
            label="An"
            :disabled="isDisabled.value"
			@update:modelValue="v => openNext(v, 'selectedYear')"
			hide-details
			
			@focus="onFocus('selectedYear')"
			@blur="autoSelectFirst('selectedYear')"
          ></v-autocomplete>
        </v-col>

        <!-- Month Selector -->
        <v-col cols="4">
          <v-autocomplete
            ref="selectedMonth"
            v-model="selectedMonth"
            :items="months"
            label="Luna"
            :disabled="isDisabled.value"
			@update:modelValue="v => openNext(v, 'selectedMonth')"
			hide-details
			
			@focus="onFocus('selectedMonth')"
			@blur="autoSelectFirst('selectedMonth')"
          ></v-autocomplete>
        </v-col>

        <!-- Day Selector -->
        <v-col cols="4">
          <v-autocomplete
            ref="selectedDay"
            v-model="selectedDay"
            :items="days"
            label="Zi"
            :disabled="isDisabled.value"
			@update:modelValue="v => openNext(v, 'selectedDay')"
			hide-details
			
			@blur="autoSelectFirst('selectedDay')"
			@focus="onFocus('selectedDay')"
          ></v-autocomplete>
        </v-col>
      </v-row>
    </template>
  </v-input>
`,
};