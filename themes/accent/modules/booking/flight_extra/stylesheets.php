<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
  .pax {
    min-width: 100px;
}
:root {
    --checkbox-bg: #2a3650;
    --checkbox-color: #72d2e3;
    --checkbox-selected: #4d358c;
    --purple: #2a3650;
    --input-bg: rgba(37, 46, 68, 70%);
    --input-bg-readonly: rgba(37, 46, 68, 80%);
    --voilet: #2a3650;
    --yellow-hotel: #72d2e3;
    --white: #fff;
    --primary-color: #72d2e3;
    --primary-color-hover: #66549b;
    --secondary-color: #2a3650;
    --blue-text: rgb(39, 101, 214);
    --btn-color: linear-gradient(90deg, #1999ff 0, #19ddff 100%), #1999ff;
    --star-color: #f9b234;
    --body-bg: #f8fafb;
    --body-text: #333333;
    --head-bg: #eeeeee;
    --head-text: #2a3650;
    --head-text-hover: #2a3650;
    --head-divider: #e5e5e5;
    --head-user-text: #72d2e3;
    --head-cart-count: #f1005b;
    --footer-bg: #2a3650;
    --light-pink: #998bbc;
}
:root {
    --bs-blue: #4582ec;
    --bs-indigo: #6610f2;
    --bs-purple: #6f42c1;
    --bs-pink: #e83e8c;
    --bs-red: #d9534f;
    --bs-orange: #fd7e14;
    --bs-yellow: #f0ad4e;
    --bs-green: #02b875;
    --bs-teal: #20c997;
    --bs-cyan: #17a2b8;
    --bs-white: #fff;
    --bs-gray: #868e96;
    --bs-gray-dark: #343a40;
    --bs-gray-100: #f8f9fa;
    --bs-gray-200: #e9ecef;
    --bs-gray-300: #ddd;
    --bs-gray-400: #ced4da;
    --bs-gray-500: #adb5bd;
    --bs-gray-600: #868e96;
    --bs-gray-700: #495057;
    --bs-gray-800: #343a40;
    --bs-gray-900: #212529;
    --bs-primary: #4582ec;
    --bs-secondary: #adb5bd;
    --bs-success: #02b875;
    --bs-info: #17a2b8;
    --bs-warning: #f0ad4e;
    --bs-danger: #d9534f;
    --bs-light: #f8f9fa;
    --bs-dark: #343a40;
    --bs-primary-rgb: 69,130,236;
    --bs-secondary-rgb: 173,181,189;
    --bs-success-rgb: 2,184,117;
    --bs-info-rgb: 23,162,184;
    --bs-warning-rgb: 240,173,78;
    --bs-danger-rgb: 217,83,79;
    --bs-light-rgb: 248,249,250;
    --bs-dark-rgb: 52,58,64;
    --bs-white-rgb: 255,255,255;
    --bs-black-rgb: 0,0,0;
    --bs-body-color-rgb: 52,58,64;
    --bs-body-bg-rgb: 255,255,255;
    --bs-font-sans-serif: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    --bs-font-monospace: SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
    --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    --bs-body-font-family: var(--bs-font-sans-serif);
    --bs-body-font-size: 1.1rem;
    --bs-body-font-weight: 400;
    --bs-body-line-height: 1.5;
    --bs-body-color: #343a40;
    --bs-body-bg: #fff
}

:root {
  --seat-paid: #167dc5;
  --seat-avail: #356915;
  --seat-selected: var(--purple);
  --seat-prefer: var(--light-pink);
  --seat-disable: #c21807;
  --seat-unavail: #eee;
  --plane-body: #fff;
  --plane-cockpit: #99bee3;
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
  margin-left: -1.5rem;
  margin-right: -1.5rem;
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
  background: #fff;
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
  content: "";
  position: absolute;
  width: 10px;
  height: 100%;
  left: 0;
  right: 0;
  margin: auto;
  background-color: #f0f0f0;
  height: 50px;
  left: -60px;
  pointer-events:none;
}

.seat-grid {
  background-color: var(--plane-body);
  border-color: var(--purple);
  border-style: solid;
  border-width: 0px 1px 0px 1px;
  position: relative;
}

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
  padding: 0 25px;
}

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
}

.seat-avail {
  border-color: var(--seat-avail);
  color: var(--seat-avail);
  cursor: pointer;
}

.seat-paid {
  border-color: var(--seat-paid);
  color: var(--seat-paid);
}

.seat-prefer {
  border-color: var(--seat-prefer);
  color: var(--seat-prefer);
}

.seat-door {
  border: 0;
}

.seat-disable {
  border-color: var(--seat-disable);
  color: var(--seat-disable);
}

.seat-selected {
  position: relative;
}

.seat-row .seat-selected {
  border-color: var(--bs-primary);
  color: #fff;
  background-color: var(--bs-primary);
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
}

.plane-cockpit {
  text-align: center;
  border-radius: 60% 60% 0% 0%/80% 80% 0% 0%;
  border-color: var(--purple);
  border-style: solid;
  border-width: 10px 1px 0px 1px;
  background-color: var(--plane-body);
  height: 300px;
  padding: 70px 0 0 0;
  margin-bottom: -100px;
}

.plane-cockpit span {
  display: inline-block;
  height: 30px;
  width: 37%;
  background-color: var(--plane-cockpit);
}

.plane-cockpit span:before {
  content: "";
  position: absolute;
  height: 0;
  width: 0;
  border-style: solid;
  border-width: 10px 100px;
  top: -11px;
}

.plane-cockpit span:first-child {
  -webkit-transform: matrix(1, -0.2, 0, 1, 0, 0);
  transform: matrix(1, -0.2, 0, 1, 0, 0);
}

.plane-cockpit span:first-child:before {
  border-color: transparent transparent transparent var(--plane-body);
  left: -1px;
}

.plane-cockpit span:last-child {
  -webkit-transform: matrix(1, 0.2, 0, 1, 0, 0);
  transform: matrix(1, 0.2, 0, 1, 0, 0);
}

.plane-cockpit span:last-child:before {
  border-color: transparent var(--plane-body) transparent transparent;
  right: -1px;
}

.plane-end {
  border-radius: 0% 0% 10% 10%/0% 0% 100% 100%;
  border-color: var(--purple);
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
  border-color: transparent;
  color: var(--seat-unavail);
  cursor: not-allowed;
}

.seat-noseat {
  color:transparent;
  border-color:transparent;
  pointer-events: none;
}
.seat-block i.handicap-friendly{
  position: absolute;
  right: -1px;
  top: 1px;
}
.seat-block i.paid-seat{
  left: 3px;
  top: 3px;
  position: absolute;
}
.service-icon i {
    text-align: center;
    width: 1.25em;
    display: inline-block;
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>