import { components } from 'vuetify';
export default {
  name: "FormLegend",
  extends: components.VListItem,
  template: `
	<v-list-item v-bind="$props" class="form-legend flex-fill">
		<template v-slot:prepend>
            <v-icon icon="mdi-circle" class="pe-6" color="warning" size="14"></v-icon>
        </template>
		<slot name="default"></slot>
		<template v-slot:subtitle v-if="$slots.subtitle"><slot name="subtitle"></slot></template>
	</v-list-item>
  `
};