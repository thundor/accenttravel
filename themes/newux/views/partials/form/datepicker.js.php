import { components } from 'vuetify';
export default {
  name: "CustomVDatePicker",
  extends: components.VDatePicker,
  props: {
    allowedDatesList: {
      type: [Array],
      default: undefined
    }
  },
  data() {
    return {
		rerendering: true,
		changeHandlerTimer: null,
		forceViewMode: null,
		savedValue: null,
		firstValidMonth: null,
		firstValidYear: null,
    };
  },
  computed: {
    minDate() {
		if(this.allowedDatesList && this.allowedDatesList[0] && (!this.min || this.min <= this.allowedDatesList[0])){
			return this.allowedDatesList[0];
		}
		return this.min;
    },
    maxDate() {
		if(this.allowedDatesList && this.allowedDatesList[0] && (!this.max || this.max >= this.allowedDatesList[this.allowedDatesList.length - 1])){
			return this.allowedDatesList[this.allowedDatesList.length - 1];
		}
		return this.max;
    },
    validMonthsAndYears() {
	  var validMonths = null;
      (this.allowedDatesList || []).forEach(date => {
        var [year, month] = date.split('-');
		year = parseInt(year);
		validMonths = validMonths || {};
		validMonths[year] = validMonths[year] || [];
		validMonths[year].push(parseInt(month))
      });
      return validMonths;
    }
  },
  mounted() {
	  this.changeHandler();
  },
  watch: {
    modelValue: {
      immediate: true,
      handler(nv) {
          this.changeHandler();
      }
    },
    allowedDatesList: {
		immediate: true,
      handler(nv) {
		  this.changeHandler()
      }
    },
    min: {
		immediate: true,
      handler(nv) {
		  this.changeHandler()
      }
    },
    max: {
		immediate: true,
      handler(nv) {
		  this.changeHandler()
      }
    }
  },
  methods: {
    changeHandler() {
		clearTimeout(this.changeHandlerTimer);
		this.changeHandlerTimer = setTimeout(() => {
			console.warn('changeHandler', this.modelValue)
			var minDate = this.minDate;
			var maxDate = this.maxDate;
			if(this.modelValue){
				  var d = new Date(this.modelValue);
				  var mv = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate())).toISOString().replace(/T.*/,'');
				  if((minDate && (minDate > mv)) || (maxDate && (maxDate < mv)) || (this.allowedDatesList && -1 == this.allowedDatesList.indexOf(mv))){
					  this.savedValue = new Date(this.modelValue);
					  this.$emit('update:modelValue', null)
					  // console.error('this.savedValue', this.savedValue, '' + minDate, '' + maxDate, JSON.parse(JSON.stringify(this.allowedDatesList)));
					  return;
				  }
				  
			} else if(this.savedValue){
				  var d = new Date(this.savedValue);
				  var mv = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate())).toISOString().replace(/T.*/,'');
				  // console.log('this.savedValue', mv, this.savedValue, '' + minDate, '' + maxDate, JSON.parse(JSON.stringify(this.allowedDatesList)));
				  if(!((minDate && (minDate > mv)) || (maxDate && (maxDate < mv)) || (this.allowedDatesList && -1 == this.allowedDatesList.indexOf(mv)))){
					  this.$emit('update:modelValue', new Date(this.savedValue))
					  // console.warn('this.savedValue', mv);
					  this.savedValue = null;
				  }
			}
			this.navigateToFirstAvailableMonth();
		}, 100);
    },
    allowedDatesPredicate(val) {
		var date = (new Date(Date.UTC(val.getFullYear(), val.getMonth(), val.getDate()))).toISOString().replace(/T.*/,'');
		return (!this.allowedDates && true || this.allowedDates(val)) && (!this.allowedDatesList && true || this.allowedDatesList.includes(date));
    },
    handleActivePickerChange(activePicker) {
	  this.forceViewMode = activePicker;
      if (activePicker === 'year' || activePicker === 'months') {
        this.$nextTick(() => {
          this.disableInvalidMonthsAndYears(activePicker);
        });
      }
	  return true;
    },
    disableInvalidMonthsAndYears(activePicker) {
		if(!this.validMonthsAndYears) return;
      const pickerItems = this.$refs.datePicker.$el.querySelectorAll(
        activePicker === 'year' ? '.v-date-picker-years__content > .v-btn' : '.v-date-picker-months__content > .v-btn'
      );
	  // console.warn('pickerItems', pickerItems, this.$refs.datePicker.$el);
      pickerItems.forEach((item, index) => {
        var value = ('' + item.innerText).trim();
		value = parseInt(value);
        const isYear = activePicker === 'year';
		var disabled = true;
		if(isYear){
			if(this.validMonthsAndYears[value]){
				disabled = false;
			}
		} else {
			var value = index+1;
			var year = parseInt(this.$refs.datePicker.year);
			if(this.validMonthsAndYears[year]){
				if(-1 !== this.validMonthsAndYears[year].indexOf(value)){
					disabled = false;
				}
			}
		}

        if (disabled) {
          item.classList.add('v-btn--disabled');
          item.setAttribute('disabled', 'true');
        }
      });
    },
    navigateToFirstAvailableMonth() {
	  if(this.minDate || this.modelValue) {
		  var min = new Date(this.modelValue || this.minDate);
			this.firstValidYear = min.getFullYear();
			this.firstValidMonth = min.getMonth();
	  }
	  if(this.firstValidYear){
		this.rerendering = false;
		this.$nextTick(() => {
			// console.error('should month', this.firstValidMonth, this.firstValidYear)
			this.rerendering = true;
		});
	  }
    }
  },
  template: `
	<pre v-if="0" v-html="JSON.stringify({
		minDate: minDate,
		maxDate: maxDate,
		forceViewMode: forceViewMode,
		modelValue: modelValue,
	}, null, 2)"></pre>
	<v-date-picker v-if="rerendering"
		ref="datePicker"
		v-bind="{...$props, ...$attrs}"
		:model-value="modelValue"
		:allowed-dates="allowedDatesPredicate"
		:min="minDate"
		:max="maxDate"
		:month="firstValidMonth"
		:year="firstValidYear"
		:view-mode="forceViewMode || 'month'"
		@update:viewMode="handleActivePickerChange"
		@update:modelValue="$.emit('update:modelValue', $event)"
	>
	<?php /* 
	<template v-slot:header>
		<input v-model="firstValidMonth" type="number" step="1" min="0" max="11" />
	</template>
	*/ ?>
	</v-date-picker>
  `
};