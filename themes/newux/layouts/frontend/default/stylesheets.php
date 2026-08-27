<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<link href="https://fonts.googleapis.com/css?family=Mukta:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/vue3-q-tel-input@latest/dist/vue3-q-tel-input.esm.css" rel="stylesheet" type="text/css" />
<style type="text/css">
html{
	font-size: 0.9rem;
}
.v-list-item--none-line{
	min-height: auto;
}
template{
	display: none !important;
}
.container{
	max-width:100%;
}
.v-footer,
.v-app-bar.v-toolbar {
    background: rgb(var(--v-theme-background));
}
.v-menu > .v-overlay__content{
	border-radius: inherit;
}
.search-type-ul-root{
	-webkit-user-select: none; /* Safari */
	  -ms-user-select: none; /* IE 10 and IE 11 */
	  user-select: none; /* Standard syntax */
	--search-type-button-radius-base: 30px;
	--search-type-button-padding-base: 20px;
	max-width: 100%;
	width:800px;
	margin:0 auto;
	/* box-shadow: 0px 2px 5px #ccc; */
	margin-bottom: 15px;
	border-radius: var(--search-type-button-radius-base);
	padding-left:2px;
	padding-right:calc(var(--search-type-button-radius-base) + var(--search-type-button-padding-base) + 2px);
	row-gap: 15px;
}
.search-type-ul{
	display:flex;
	/* flex-direction: row-reverse; */
	justify-content: center;
	overflow:visible;
	position: relative;
	cursor: pointer;
	flex-wrap: wrap;
}
.search-type-ul .search-type-ul{
	margin-top: calc(0px - var(--search-type-button-padding-base));
	margin-bottom: calc(0px - var(--search-type-button-padding-base));
	opacity:0;
	pointer-events: none;
	width: 0;
	left:0;
	right:0;
	position: absolute;
	overflow:hidden;
	white-space: nowrap;
	
	
	width: calc(100% + 2 * var(--search-type-button-radius-base) - 58px);
	padding-right: calc(3 * var(--search-type-button-radius-base) + var(--search-type-button-padding-base) - 58px);
}
.search-type-ul .search-type-ul > li:first-child{
	/* padding-left: calc(var(--search-type-button-padding-base)); */
}
.search-type-ul .search-type-ul > li:not(:first-child){
	position:relative;
}
.search-type-ul .v-label,
.search-type-ul .v-field__input,
.search-type-ul .v-field__input > input {
	opacity:1 !important;
	cursor:pointer;
}
/* .search-type-ul .search-type-ul > li:not(:first-child):after{
	content: "";
    left: 50px;
    position: absolute;
    background: white;
    top: 15px;
    bottom: 15px;
    width: 1px;
}
.search-type-ul .search-type-ul > li:not(:first-child):hover:after,
.search-type-ul .search-type-ul > li:not(:first-child):hover + li:after{
	display:none;
} */
.search-type-ul li {
	background-color: var(--bg-color, #fff);
	/* border: 1px solid var(--bg-color); */
	transition: 0.3s ease background-color;
	display: inline-flex;
	padding: var(--search-type-button-padding-base) 0;
	border-radius: var(--search-type-button-radius-base);
	margin-right: calc(0px - var(--search-type-button-padding-base) - var(--search-type-button-radius-base));
	/*max-width:100%;*/
	flex:1;
	justify-content: center;
	border:1px solid white;
	box-shadow: 0 0 1px white;
}
.search-type-ul li:not(.search-type-ul-close):not(:first-child) {
}
.search-type-ul li:not(.search-type-ul-close):not(:first-child) > div.menu-item{
	white-space: nowrap;
	text-indent: 40px;
}
.search-type-ul li:not(.search-type-ul-close) > div.menu-item > i{
	position: absolute;
}
.search-type-ul li:not(.search-type-ul-close) > div.menu-item > i + span{
	padding-left: 25px;
}
.search-type-ul li:hover > div > ul > li,
.search-type-ul-root:not(:hover) li:not(.has-children).active,
.search-type-ul li:not(.has-children):hover{
	--v-theme-overlay-multiplier: var(--v-theme-info-overlay-multiplier);
    background-color: rgb(var(--v-theme-info)) !important;
    color: rgb(var(--v-theme-on-info)) !important;
	--bg-color: rgb(var(--v-theme-info)) !important;
	border-color: #fff;
	box-shadow: 0 0 1px white inset;
}
.search-type-ul li > div > ul > li:first-child{
	--v-theme-overlay-multiplier: var(--v-theme-info-overlay-multiplier);
    background-color: rgb(var(--v-theme-info)) !important;
	--bg-color: rgb(var(--v-theme-info)) !important;
    color: rgb(var(--v-theme-on-info)) !important;
	border-color:#fff;
}
.search-type-ul li > div > ul > li:not(:first-child):hover{
	--v-theme-overlay-multiplier: var(--v-theme-background-overlay-multiplier);
    background-color: rgb(var(--v-theme-background)) !important;
	--bg-color: rgb(var(--v-theme-background)) !important;
    color: rgb(var(--v-theme-on-background)) !important;
	border-color:#fff;
}
/* .search-type-ul-root .search-type-ul-close{
	visibility: hidden;
	position: absolute;
	top: 0;
	height:100%;
	right: -10px;
    background-color: #fff;
    width: 110px;
    padding-left: 60px !important;
	box-shadow: inherit;
} */
.search-type-ul-root li{
	box-shadow: 0px 2px 5px #ccc !important;
}
.search-type-ul-root .search-type-ul-close{
	/* transition: 0.3s ease all; */
	flex: 0 0 0;
	padding-right: 0px;
    padding-left: 25px;
}
.search-type-ul-root .search-type-ul-close.bg-info:hover{
	--v-theme-overlay-multiplier: var(--v-theme-warning-overlay-multiplier);
    background-color: rgb(var(--v-theme-warning)) !important;
    color: rgb(var(--v-theme-on-warning)) !important;
}
.search-type-ul-close.shown,
.search-type-ul-root:not(:hover) li.active.has-children ~ .search-type-ul-close,
li.has-children:hover ~ .search-type-ul-close{
	/* visibility: visible; */
	flex: 0 0 110px;
	padding-right: 0px;
    padding-left: 40px;
}
.search-type-ul-root > li.active > .search-type-ul-wrapper > .search-type-ul,
.search-type-ul > li:hover > .search-type-ul-wrapper > .search-type-ul{
	opacity:1;
	pointer-events:all;
	width: calc(100% + 2 * var(--search-type-button-radius-base) - 60px - 60px);
	margin-left: 0;
}
.search-type-ul li.active > span{
	text-decoration: underline;
}
.search-type-ul-root:hover > li.active:not(:hover) > .search-type-ul-wrapper > .search-type-ul{
	opacity:0 !important;
	pointer-events:none !important;
}
.search-type-ul > li.disabled{
	--bg-color: #aaa !important;
	color: #fff;
}
.search-type-ul li.pa-0:not(.search-type-ul-close) {
    flex-basis: 180px;
}
.v-window .fixed-fwh > .v-window__container{
	width:100%;
}
.offer-gallery-chunk .v-img{
	transform: scale(1);
	cursor: pointer;
	transition: transform 0.5s ease;
}
.offer-gallery-chunk .v-img:hover{
	transform: scale(1.05);
}
.form-legend > .v-list-item__content > .v-list-item-title {
	overflow: initial;
	white-space: initial;
}
.form-legend > .v-list-item__prepend {
	align-self: start;
	padding-top:8px;
}
.form-legend > .v-list-item__prepend > .v-list-item__spacer {
	display: none;
}
#search-wrapper-menu-before .form-legend{
	max-width: 1000px; 
	margin: auto;
}
.v-phone-input__country__icon.fi{
	color:transparent;
}
.v-select__selection .v-phone-input__country__icon.fi{
	color: inherit;
    font-size: 12px;
    white-space: pre;
    text-indent: 30px;
}
.results-sort{
    display: flex;
    justify-content: end;
    align-items: center;
    gap: 5px;
}
.results-header{
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}
.v-container:not(.v-container--fluid) {
	max-width: 1252px;
}
.loading-section{
	opacity: 0.7;
	position: relative;
}
.loading-section > *{
	pointer-events: none;
}
.loading-section:after{
	content:"";
	position: absolute;
	/* top:-10px;
	left:-10px;
	right:-10px;
	bottom:-10px; */
	/* background-color: rgba(0,0,0,0.3); */
	box-shadow: 0,0,5px rgba(0,0,0,0.3);
	z-index: 1;
	border-radius: 5px;
	cursor: progress;
}
.search-type-ul-wrapper{
	display: block;
	margin: 0 auto;
}
.search-type-ul-wrapper .search-type-ul-wrapper{
	margin: initial;
}
.slider-inversed .v-slider-track .v-slider-track__fill{
	background-color: #fff !important;
}
.slider-inversed .v-slider-track .v-slider-track__fill::after{
	content: "";
	display:block;
	background-color: rgb(var(--v-theme-info)) !important;
	opacity: 0.3;
	height: var(--v-slider-track-size);
	width:100%;
}
.slider-inversed .v-slider-track .v-slider-track__background{
	height: inherit;
	opacity: 1;
}
:root {
  --seat-paid: #3481CE;
  --seat-avail: #A5A7AA;
  --seat-selected: #E8D433;
  --seat-prefer: var(--light-pink);
  
  --seat-active: #E8D433;
  --seat-disable: #c21807;
  --seat-unavail: #D95252;
  --plane-body: #E9E9EA;
  --plane-cockpit: #99bee3;
  --plane-border: #A5A7AA;
}

.route-column {
  width: 120px;
}

.route-seat-dd {
  width: 150px;
}

/* .updagradeSeviceWrap.show-map .book-form-section, .updagradeSeviceWrap.show-map .priceoption {
  display: none !important;
} */

.selectedSeat {
  display: none;
}

.seat-selected.selected .selectedSeat {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
}

.seat-selected.selected .chooseSeat {
  display: none;
}

.updagradeSeviceWrap.show-map .flight-book-seatMap-wrapper {
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  right: 0;
  left: 0;
  bottom: 0;
  background: rgba(255,255,255,0.95);
  z-index: 99999;
}
.seatmap-container-wrapper{
  flex:1;
  position: relative;
}
.modal-open #back-to-top{
  display: none !important;
}
.seatmap-container-wrapper > div{
  height: 100%;
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}
.position-relative{
  position: relative;
}
.plane-wrapper{
  height: 100%;
  overflow: auto;
}

.flight-book-seatMap-wrapper {
  overflow: hidden;
  display: none;
}

@media screen and (min-width: 577px) {
  .travelerOptions {
    width: 100%;
    max-width: 200px;
  }
}

.seat-legend > span {
  margin: 0 2px 8px 2px;
  vertical-align: middle;
  font-size: 12px;
}

.seat-legend > span em {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 100%;
  margin: -0.1em 0 0 -0.3em;
  vertical-align: middle;
}

.seat-legend-paid {
  color: var(--seat-paid);
}

.seat-legend-paid em {
  background-color: var(--seat-paid);
}

.seat-legend-avail {
  color: var(--seat-avail);
}

.seat-legend-avail em {
  background-color: var(--seat-avail);
}

.seat-legend-prefer {
  color: var(--seat-prefer);
}

.seat-legend-prefer em {
  background-color: var(--seat-prefer);
}

.seat-legend-selected {
  color: var(--seat-selected);
}

.seat-legend-selected em {
  background-color: var(--seat-selected);
}

.seat-legend-disable {
  color: var(--seat-disable);
}

.seat-legend-disable em {
  background-color: var(--seat-disable);
}

.seat-legend-unavail {
  color: var(--seat-unavail);
}

.seat-legend-unavail em {
  background-color: var(--seat-unavail);
}

.seat-block:not(:first-child){
  margin-left:1px;
}
.seat-block {
  display: inline-block;
  width: 30px;
  height: 30px;
  line-height: 30px;
  border: 1px solid;
  vertical-align: top;
  font-size: 0.8em;
  font-weight: bold;
  border-radius: 5px;
}

.seat-block:before {
  font-family: 'Font Awesome 6 Free';
  font-weight: 600;
}

.seat-column {
  border: 0;
  font-weight: bold;
  font-size: 1em;
}

.seat-aisle + .seat-aisle {
  position: relative;
  margin-left: 30px;
}

.seat-aisle + .seat-aisle::before {
  content: attr(data-row-number);
  color: #000;
  position: absolute;
  width: 30px;
  height: 100%;
  left: 0;
  right: 0;
  margin: auto;
  /* background-color: #f0f0f0; */
  height: 50px;
  left: -60px;
  pointer-events:none;
  white-space: pre;
}

.seat-grid {
  background-color: var(--plane-body);
  border-color: var(--plane-border);
  border-style: solid;
  border-width: 0px 1px 0px 1px;
  position: relative;
}

/* .seat-grid:before {
    content: "";
    background: red;
    width: 360px;
    height: 360px;
    left: -360px;
    position: absolute;
    border-radius: 230% 0% 30% 0%;
} */

.seat-grid.minGrid {
  min-height: 50vh;
}

@media screen and (max-width: 767px) {
  .seat-grid.minGrid {
    min-height: 40vh;
  }
}

.seat-row {
  margin-bottom: 10px;
  position: relative;
}
/*
.seat-row:before, .seat-row:after {
  content: attr(data-number);
  position: absolute;
  top: 10px;
  width: 15px;
  font-size: 0.7em;
  text-align: center;
}
.seat-row:before {
  left: 3px;
}
.seat-row:after {
  right: 3px;
}*/

.seat-avail {
  border-color: var(--seat-avail);
  background-color: var(--seat-avail);
  color: #fff;
  cursor: pointer;
}

.seat-paid {
  border-color: var(--seat-paid);
  background-color: var(--seat-paid);
  color: #fff;
}
.seat-row.column-names{
  position: sticky;
  top: 0;
  z-index: 1;
  background: var(--plane-body);
}
/* .seat-prefer {
  border-color: var(--seat-prefer);
  background-color: var(--seat-prefer);
  color: #fff;
} */

.seat-door {
  border: 0;
}

/* .seat-disable {
  border-color: var(--seat-disable);
  background-color: var(--seat-disable);
  color: #fff;
} */

.seat-selected {
  position: relative;
}

.seat-active {
  border-color: var(--seat-active);
  background-color: var(--seat-active);
  color: #fff;
}

.seat-row .seat-selected {
  border-color: var(--seat-selected);
  color: #fff;
  background-color: var(--seat-selected);
}

.seat-selected.active:after {
  content: "";
  width: 0;
  height: 0;
  position: absolute;
  border: 7px;
  border-style: solid;
  border-color: var(--purple) transparent transparent transparent;
  top: -7px;
  left: 0;
  right: 0;
  margin: auto;
}

.seat-tooltip {
  padding: 0.5em;
  background-color: #fff;
  border: 2px solid var(--purple);
  border-radius: 4px;
  -webkit-box-shadow: 0px -2px 10px 4px rgba(0, 0, 0, 0.3);
  box-shadow: 0px -2px 10px 4px rgba(0, 0, 0, 0.3);
}

.seat-tooltip-wrapper {
  opacity: 0;
  position: absolute;
  top: 0;
  width: 100%;
  z-index: -1;
  padding: 0 0.5em;
}
.seat-tooltip-wrapper.active{
  z-index: 2;
  opacity: 1;
}

.seat-tooltip-wrapper .remove {
  display: none;
}

.seat-tooltip-wrapper.preselected .remove {
  display: inline;
}

.seat-tooltip-wrapper.preselected .add {
  display: none;
}

.seat-tooltip-wrapper.preselected .icon-select:after, .seat-tooltip-wrapper.preselected .icon-select:before {
  display: none;
}

@media screen and (min-width: 768px) {
  .seat-map-details {
    position: absolute;
    width: 100%;
  }
}

.exit-row {
  color: var(--seat-disable);
  padding: 0;
  background-color: var(--plane-body);
}

.plane-wrapper {
  text-align: center;
}

.plane-body {
  border-radius: 0% 0% 5% 5%/50% 50% 30% 30%;
  margin: 1em auto 0 auto;
  display: inline-block;
  white-space: nowrap;
}

.plane-cockpit {
  text-align: center;
  /* border-radius: 60% 60% 0% 0%/80% 80% 0% 0%; */
  border-radius: 50% 50% 0% 0%/100% 100% 0% 0%;
  border-color: var(--plane-border);
  border-style: solid;
  border-width: 10px 1px 0px 1px;
  background-color: var(--plane-body);
  padding: 70px 0 0 0;
}

.plane-cockpit > span {
  display: inline-block;
  height: 30px;
  width: 33%;
  background-color: var(--plane-cockpit);
}

.plane-cockpit > span:before {
  content: "";
  position: absolute;
  height: 0;
  width: 0;
  border-style: solid;
  border-width: 10px 100px;
  top: -11px;
}

.plane-cockpit > span:first-of-type {
  -webkit-transform: matrix(1, -0.2, 0, 1, 0, 0);
  transform: matrix(1, -0.2, 0, 1, 0, 0);
}

.plane-cockpit > span:first-of-type:before {
  border-color: transparent transparent transparent var(--plane-body);
  left: -1px;
}

.plane-cockpit > span:last-of-type {
  -webkit-transform: matrix(1, 0.2, 0, 1, 0, 0);
  transform: matrix(1, 0.2, 0, 1, 0, 0);
}

.plane-cockpit > span:last-of-type:before {
  border-color: transparent var(--plane-body) transparent transparent;
  right: -1px;
}

.plane-end {
  border-radius: 0% 0% 10% 10%/0% 0% 100% 100%;
  border-color: var(--plane-border);
  border-style: ridge;
  border-width: 0px 1px 10px 1px;
  background-color: var(--plane-body);
  height: 80px;
  margin-top: -10px;
}

.wings-wrapper {
  position: relative;
}

.wings-wrapper:after, .wings-wrapper:before {
  content: "";
  position: absolute;
  top: 0;
  background-color: var(--purple);
  height: 150%;
  color: var(--purple);
  width: 100%;
  min-width: 300px;
  -webkit-clip-path: polygon(70% 55%, 88% 70%, 100% 80%, 100% 100%, -120% 27%, 0% 0%, 0% 0%);
  clip-path: polygon(70% 55%, 88% 70%, 100% 80%, 100% 100%, -120% 27%, 0% 0%, 0% 0%);
  opacity: 0.7;
}

.wings-wrapper:after {
  left: 100%;
}

.wings-wrapper:before {
  -webkit-transform: scaleX(-1);
  transform: scaleX(-1);
  right: 100%;
}

.pax {
  min-width: 100px;
}

.pax-detail {
  border-left: 2px solid #fff;
}

.pax-detail-wrap {
  display: none;
}

.pax-detail > small {
  display: block;
}

.pax-type.hasValue .pax-detail-wrap {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
}

.prefer-text {
  margin-bottom: 10px;
  bottom: 100%;
  background-color: #fff;
  padding: 0.5em;
  background-color: #fff;
  width: 100%;
  border: 2px solid var(--purple);
  left: 0;
  text-align: left;
  right: 0;
  margin: auto;
  border-radius: 4px 4px 0 0;
  display: none;
}

.selected-card {
  border-color: #e2e2e2;
  border-style: solid;
}

.bg-yellow {
  background-color: #333;
}

.cardImg span {
  display: none;
}

.selected-card img {
  max-height: 25px;
  max-width: 150px;
  margin: 10px;
  min-height: 26px;
  max-width: 80px;
  -o-object-fit: contain;
     object-fit: contain;
}

#SelectedFlightCount {
  display: none;
}

.summary-wrapper {
  width: 300px;
}

.route-border {
  height: 50px;
}

.route-border em {
  width: 1px;
  margin: 0 5px;
  background-color: #e6e1e1;
}

.route-border span {
  background-color: #333;
  position: relative;
  font-size: 0.8em;
  -ms-flex-item-align: center;
  -ms-grid-row-align: center;
      align-self: center;
  display: inline-block;
  width: 50%;
}

.upsell-options {
  overflow-x: auto;
  width: calc(100% - 40px);
}

@media screen and (min-width: 768px) {
  .upsell-options {
    width: calc(100% - 200px);
  }
}

.upsell-options-wrapper {
  color: #132B4F;
  max-width:100%;
  width:100%;
}

.upsell-options-group {
  vertical-align: top;
}
.upsell-options-group {
  border: 1px solid #fff;
}
.upsell-options-group.active-upgrade {
  border: 1px solid #333;
}

@media screen and (min-width: 768px) {
  .upsell-options-group.fixed {
    width: 200px;
  }
}

.upsell-options-group li li {
  padding: 0.37em 0.6em;
}

.upsell-options-groupItem li {
  list-style: none;
}

.borderBox{
  /* border: 1px solid #0275d8; */
}

.upsell-options-groupItem li.head {
  background-color: #0275d8;
  padding: 0 10px;
  height:80px;
}

.upsell-options-groupItem li.head .badge {
  text-transform: capitalize;
}

.upsell-options-groupItem ul {
  width: 100%;
}

.upsell-options-services li {
  padding: 0.4em;
  text-transform: capitalize;
}

@media screen and (max-width: 767px) {
  .upsell-options-services li {
    height: 31px;
  }
}

.upsell-options-services li .disabled {
  opacity: 0.2;
}

.upsell-options-services li:nth-child(odd) {
  background-color: #f2f2f2;
}

.upsell-options-services li:nth-child(even) {
  background-color: #e2e2e2;
}

.upsell-options-servicesName {
  position: relative;
  cursor: pointer;
}

.upsell-options-servicesName span[class*='count-'] {
  white-space: normal;
  background-color: #333;
  color: #fff;
  display: none;
  position: absolute;
  left: calc(100% + 20px);
  /* min-width: 100px; */
  z-index: 1;
  font-size: 0.8em;
  text-align: left;
  padding: 5px 5px;
  pointer-events: none;
}
.plane-body .upsell-options-servicesName span[class*='count-'] {
  left: 51px;
}

.upsell-options-servicesName span[class*='count-']:before {
  content: "";
  border-width: 7px;
  border-style: solid;
  border-color: transparent #333 transparent transparent;
  width: 0;
  height: 0;
  display: inline-block;
  position: absolute;
  right: 100%;
  top: 7px;
}
/* 
.upsell-options-servicesName span.count-1:before {
  top: 6px;
} */

.upsell-options-servicesName:hover span[class*='count-'] {
  display: inline-block;
}

.upsell-options-servicesName:not(.tooltip-top) span[class*='count-'] {
  width:max-content;
  line-height:18px;
}
.upsell-options-servicesName.tooltip-top span[class*='count-'] {
  bottom: calc(100% + 10px);
    top: auto;
    left: auto;
    right: auto;
    /* max-width: 100%; */
}
.upsell-options-servicesName.tooltip-top span[class*='count-']:before{
  right:calc(50% - 7px);
  top:auto;
  bottom:-14px;
  border-color: #333 transparent  transparent transparent;
}

.upsell-options .upsell-options-group {
  min-width: 80px;
}
.icons-defination div {
    margin-right: 30px;
   
}

.icons-defination::-webkit-scrollbar-track,.upsell-options::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
	background-color: #F5F5F5;
}

.icons-defination::-webkit-scrollbar, .upsell-options::-webkit-scrollbar
{
	width: 20px;
	background-color: #F5F5F5;
}

.icons-defination::-webkit-scrollbar-thumb, .upsell-options::-webkit-scrollbar-thumb
{
	background-color: #000000;
}


@media screen and (min-width: 780px) {
    .finalPrice {
            position: fixed;
            z-index: 9999;
            right: 20;
            top: 20;
            background: var(--bs-primary);
            border: 1px solid #e6e6e7;
            padding: 8px 16px;
            font-size: 16px;
            display:none;
            color:#fff;
        }
    .priceoption.static{
        display:flex;
        flex-direction: column;
        margin-top: 1rem;
    }
    .priceoptionFixed{
        display:flex;
        flex-direction: row;
        justify-content: flex-end;
        position:fixed;
        top: 0px;
        right: 0px;
        z-index:99;
        width: 100%;
    }
}
@media screen and (max-width: 780px) {
     .finalPrice {
            position: fixed;
            z-index: 9999;
            right: 0;
            top: 0;
            background: var(--bs-primary);
            border: 1px solid #e6e6e7;
            padding: 8px 16px;
            font-size: 16px;
            display:none;
            color:#fff;
            text-align:Center;
            width:100%;
        }
}
#fpFloaiting.active {
    display:block;
}

.btnChoose-seat {
    width: auto;
    position: absolute;
    right: 32px;
    margin-top: 0px;
}
.custom.icon-select::before {
    content: "";
    position: absolute;
    right: 0;
    top: 0;
    width: 18px;
    height: 100%;
    border-radius: 0 4px 4px 0;
    display:none !important;
}
.custom.icon-select::after {
    display:none !important;
}


.seat-unavail {
  border-color: var(--seat-unavail);
  background-color: var(--seat-unavail);
  color: #fff;
  cursor: not-allowed;
}

.seat-selected {
  pointer-events: none;
}
.seat-noseat {
	color: transparent !important;
	border-color: transparent !important;
	background-color: transparent;
	pointer-events: none;
}
.seat-block i.handicap-friendly{
  position: absolute;
  right: -1px;
  top: 1px;
}
/*
.seat-block i.paid-seat{
  left: 3px;
  top: 3px;
  position: absolute;
}
*/
.seat-block i.preferential-seat{
  left: 3px;
  top: 3px;
  position: absolute;
}
.service-icon i {
    text-align: center;
    width: 1.25em;
    display: inline-block;
}

.select-wrap {
  position: relative;
  height: 100%;
  text-align: center;
  overflow: hidden;
  font-size: 20px;
  color: #ddd;
}
.select-wrap:before, .select-wrap:after {
  position: absolute;
  z-index: 1;
  display: block;
  content: "";
  width: 100%;
  height: 50%;
}
.select-wrap:before {
  top: 0;
  background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.5), rgba(1, 1, 1, 0));
}
.select-wrap:after {
  bottom: 0;
  background-image: linear-gradient(to top, rgba(255, 255, 255, 0.5), rgba(1, 1, 1, 0));
}
.select-wrap .select-options {
  position: absolute;
  top: 50%;
  left: 0;
  width: 100%;
  height: 0;
  transform-style: preserve-3d;
  margin: 0 auto;
  display: block;
  transform: translateZ(-150px) rotateX(0deg);
  -webkit-font-smoothing: subpixel-antialiased;
  color: #666;
}
.select-wrap .select-options .select-option {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 50px;
  -webkit-font-smoothing: subpixel-antialiased;
}
.select-wrap .select-options .select-option:nth-child(1) {
  transform: rotateX(0deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(2) {
  transform: rotateX(-18deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(3) {
  transform: rotateX(-36deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(4) {
  transform: rotateX(-54deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(5) {
  transform: rotateX(-72deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(6) {
  transform: rotateX(-90deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(7) {
  transform: rotateX(-108deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(8) {
  transform: rotateX(-126deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(9) {
  transform: rotateX(-144deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(10) {
  transform: rotateX(-162deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(11) {
  transform: rotateX(-180deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(12) {
  transform: rotateX(-198deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(13) {
  transform: rotateX(-216deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(14) {
  transform: rotateX(-234deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(15) {
  transform: rotateX(-252deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(16) {
  transform: rotateX(-270deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(17) {
  transform: rotateX(-288deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(18) {
  transform: rotateX(-306deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(19) {
  transform: rotateX(-324deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(20) {
  transform: rotateX(-342deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(21) {
  transform: rotateX(-360deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(22) {
  transform: rotateX(-378deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(23) {
  transform: rotateX(-396deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(24) {
  transform: rotateX(-414deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(25) {
  transform: rotateX(-432deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(26) {
  transform: rotateX(-450deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(27) {
  transform: rotateX(-468deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(28) {
  transform: rotateX(-486deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(29) {
  transform: rotateX(-504deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(30) {
  transform: rotateX(-522deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(31) {
  transform: rotateX(-540deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(32) {
  transform: rotateX(-558deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(33) {
  transform: rotateX(-576deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(34) {
  transform: rotateX(-594deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(35) {
  transform: rotateX(-612deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(36) {
  transform: rotateX(-630deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(37) {
  transform: rotateX(-648deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(38) {
  transform: rotateX(-666deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(39) {
  transform: rotateX(-684deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(40) {
  transform: rotateX(-702deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(41) {
  transform: rotateX(-720deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(42) {
  transform: rotateX(-738deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(43) {
  transform: rotateX(-756deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(44) {
  transform: rotateX(-774deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(45) {
  transform: rotateX(-792deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(46) {
  transform: rotateX(-810deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(47) {
  transform: rotateX(-828deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(48) {
  transform: rotateX(-846deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(49) {
  transform: rotateX(-864deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(50) {
  transform: rotateX(-882deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(51) {
  transform: rotateX(-900deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(52) {
  transform: rotateX(-918deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(53) {
  transform: rotateX(-936deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(54) {
  transform: rotateX(-954deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(55) {
  transform: rotateX(-972deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(56) {
  transform: rotateX(-990deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(57) {
  transform: rotateX(-1008deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(58) {
  transform: rotateX(-1026deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(59) {
  transform: rotateX(-1044deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(60) {
  transform: rotateX(-1062deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(61) {
  transform: rotateX(-1080deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(62) {
  transform: rotateX(-1098deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(63) {
  transform: rotateX(-1116deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(64) {
  transform: rotateX(-1134deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(65) {
  transform: rotateX(-1152deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(66) {
  transform: rotateX(-1170deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(67) {
  transform: rotateX(-1188deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(68) {
  transform: rotateX(-1206deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(69) {
  transform: rotateX(-1224deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(70) {
  transform: rotateX(-1242deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(71) {
  transform: rotateX(-1260deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(72) {
  transform: rotateX(-1278deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(73) {
  transform: rotateX(-1296deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(74) {
  transform: rotateX(-1314deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(75) {
  transform: rotateX(-1332deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(76) {
  transform: rotateX(-1350deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(77) {
  transform: rotateX(-1368deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(78) {
  transform: rotateX(-1386deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(79) {
  transform: rotateX(-1404deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(80) {
  transform: rotateX(-1422deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(81) {
  transform: rotateX(-1440deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(82) {
  transform: rotateX(-1458deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(83) {
  transform: rotateX(-1476deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(84) {
  transform: rotateX(-1494deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(85) {
  transform: rotateX(-1512deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(86) {
  transform: rotateX(-1530deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(87) {
  transform: rotateX(-1548deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(88) {
  transform: rotateX(-1566deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(89) {
  transform: rotateX(-1584deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(90) {
  transform: rotateX(-1602deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(91) {
  transform: rotateX(-1620deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(92) {
  transform: rotateX(-1638deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(93) {
  transform: rotateX(-1656deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(94) {
  transform: rotateX(-1674deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(95) {
  transform: rotateX(-1692deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(96) {
  transform: rotateX(-1710deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(97) {
  transform: rotateX(-1728deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(98) {
  transform: rotateX(-1746deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(99) {
  transform: rotateX(-1764deg) translateZ(150px);
}
.select-wrap .select-options .select-option:nth-child(100) {
  transform: rotateX(-1782deg) translateZ(150px);
}

.highlight {
  position: absolute;
  top: 50%;
  transform: translate(0, -50%);
  width: 100%;
  background-color: #000;
  border-top: 1px solid #333;
  border-bottom: 1px solid #333;
  font-size: 24px;
  overflow: hidden;
}

.highlight-list {
  position: absolute;
  width: 100%;
}

/* date */
.date-selector {
  /* position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  perspective: 2000px; */
  display: flex;
  align-items: stretch;
  justify-content: space-between;
  width: 600px;
  height: 200px;
}
.date-selector > div {
  flex: 1;
}
.date-selector .select-wrap {
  font-size: 1em;
}
.date-selector .highlight {
  font-size: 1em;
}

.popup-closer {
        position: relative;
    }

    .popup-closer:after {
        content: "";
        width: 54px;
        height: 5px;
        background-color: #ffffff;
        position: absolute;
        left: 0;
        bottom: 15px;
        margin-left: calc(50% - 27px);
        border-radius: 24px;
        margin-bottom: 0;
    }

    .popup-closer {
        flex-basis: 100%;
    }

    .popup-closer-down {
        min-height:35px;
        flex-basis: 35px;
    }
    
    .to_be_moved{
        transition: transform 0.2s linear;
    }

    .max-height{
        max-height: calc(100vh - 170px);
    }

    .popup-closer-down:after {
        width: 148px;
        margin-left: calc(50% - 74px);
        top: 15px;
        bottom: auto;
    }
.to_move~.to_be_moved {
	transition: margin-bottom 0.3s linear;
}

.to_move_down~.to_be_moved {
	margin-bottom: -25vh;
}
.v-dialog:not(.v-dialog--fullscreen)>.v-overlay__content{
	width:auto;
	margin: auto;
}
.outline-0 {
    outline-color: rgba(var(--v-border-color),var(--v-border-opacity))!important;
    outline-style: solid!important;
    outline-width: 0!important
}

.outline,.outline-thin {
    outline-color: rgba(var(--v-border-color),var(--v-border-opacity))!important;
    outline-style: solid!important;
    outline-width: thin!important
}

.outline-sm {
    outline-color: rgba(var(--v-border-color),var(--v-border-opacity))!important;
    outline-style: solid!important;
    outline-width: 1px!important
}

.outline-md {
    outline-color: rgba(var(--v-border-color),var(--v-border-opacity))!important;
    outline-style: solid!important;
    outline-width: 2px!important
}

.outline-lg {
    outline-color: rgba(var(--v-border-color),var(--v-border-opacity))!important;
    outline-style: solid!important;
    outline-width: 4px!important
}

.outline-xl {
    outline-color: rgba(var(--v-border-color),var(--v-border-opacity))!important;
    outline-style: solid!important;
    outline-width: 8px!important
}
.cke_panel {
	position: fixed !important;
}
</style>
<style type="text/css" id="vuetify-theme-stylesheet"></style>

<link type="text/css" rel="stylesheet" href="<?php echo site_url('newux/assets/css.css?newux=' . (!empty($_GET['newux']) ? $_GET['newux'] : NEWUX_VERSION) . '&name=ada'); ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo site_url('newux/assets/css.css?newux=' . (!empty($_GET['newux']) ? $_GET['newux'] : NEWUX_VERSION) . '&name=tudor'); ?>" />
<?php if(!empty($_GET['ada'])){ ?>
<?php } ?>
<?php if(!empty($_GET['tudor'])){ ?>
<?php } ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>