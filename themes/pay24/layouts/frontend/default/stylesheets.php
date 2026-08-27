<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<link href="https://fonts.googleapis.com/css?family=Mukta:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css">
<?php /*<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Mukta:normal,300,300i,400,400i,600,600i,800,800i,900,900i&display=swap"></link> */ ?>
<link href="https://cdn.jsdelivr.net/npm/quasar@2.11.6/dist/quasar.prod.css" rel="stylesheet" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/vue3-q-tel-input@latest/dist/vue3-q-tel-input.esm.css" rel="stylesheet" type="text/css">
<style type="text/css">
  body {
    font-family: Mukta, Roboto,-apple-system,Helvetica Neue,Helvetica,Arial,sans-serif;
  }
    html,
    body,
    #page-content,
    main {
      color:#000;
        height: 100%;
        -webkit-overflow-scrolling: touch;
        overflow-y: scroll;
    }
    .justify-stretch{
      justify-content: stretch !important;
    }
    main {
        display: flex;
        flex-direction: column;
    }

    .prevent-select {
        -webkit-user-select: none;
        /* Safari */
        -ms-user-select: none;
        /* IE 10 and IE 11 */
        user-select: none;
        /* Standard syntax */
    }

    .border-color-dark-light {
      color: #8593A2;
    }
    .color-dark-light {
      color: #8593A2;
    }

    .q-tree__children{
      padding-left: 20px;
    }
    .q-tree__node{
      padding-left:0;
    }
    .q-tree__node-header:before{
      width: 15px;
      left: -13px;
    }

    .q-tree__node--parent>.q-tree__node-header:before{
      left: -13px;
    }

    h5,.text-h5 {
      font-size: 1.2rem;
      line-height: 1.4rem;
    }
    .text-h5 {
      font-size: 1.2rem !important;
    }
    
    .v-btn.min-content{
      width: min-content;
      
    }
    .v-btn.min-content .v-btn__content{
      white-space: normal;
    }

    .border-theme {
        border-color: rgba(var(--v-theme-on-surface), var(--v-disabled-opacity)) !important;
    }
    div.mdi-border-bottom::before{
      content: none;
    }
    .q-field--outlined .q-field__control{
      border-radius: 12px;
      color: inherit;
    }
    .q-field{
      font:inherit;
      font-size:16px;
    }
    .ondark .q-field__label,
    .ondark .q-field__input, .ondark .q-field__native, .ondark .q-field__prefix, .ondark .q-field__suffix{
      color: #fff;
      font-weight: 300;
    }
    .q-field--labeled .q-field__native, .q-field--labeled .q-field__prefix, .q-field--labeled .q-field__suffix{
      padding-top: 14px;
      padding-bottom: 14px;
      line-height: 27px;
    }
    .q-field__marginal,
    .q-field__control{
      color: inherit;
      height: 61px;
    }
    .ondark .q-field__label{
      top: 21px;
    }
    .q-field--outlined.q-field--float .q-field__label{
      transform: translateY(-125%) scale(0.75);
      opacity: 1;
      z-index:1;
      padding: 0 5px;
    }
    .onlight.q-field--outlined.q-field--float .q-field__label{
      background: #fff !important;
      color: #8593A2;
    }
    .ondark.q-field--outlined.q-field--float .q-field__label{
      background: rgb(var(--v-theme-background)) !important;
      color: #8593A2;
    }
    .phone-with-prefix.q-field--outlined.q-field--float .q-field__label{
      transform: translateY(-125%) scale(0.75) translateX(-80px);
    }
    .q-field--outlined .q-field__control:before{
      border-radius: 12px;
      opacity: 0.7;
    }
    .ondark.q-field--outlined .q-field__control:before{
      border: 1px solid #fff;
    }
    .onlight.q-field--outlined .q-field__control:before{
      border: 1px solid rgba(0,0,0,0.5);
    }
    .q-field__bottom{
      padding-top:0;
    }
    .q-field--error .q-field__bottom,
    .text-negative{
      color: rgb(207, 102, 121) !important;
    }
    .ondark.q-field--outlined .q-field__control:hover:before{
      border-color: #fff;
    }
    .onlight.q-field--outlined .q-field__control:hover:before{
      border-color: rgba(0,0,0,0.87);
    }
    .rounded-theme {
        border-radius: 12px !important;
    }
    .rounded-theme .v-field--variant-outlined .v-field__outline__start.v-locale--is-ltr, 
    .v-locale--is-ltr .rounded-theme .v-field--variant-outlined .v-field__outline__start{
      border-radius: 12px 0 0 12px;
    }
    .rounded-theme .v-field--variant-outlined .v-field--variant-outlined .v-field__outline__end.v-locale--is-ltr, 
    .v-locale--is-ltr .rounded-theme .v-field--variant-outlined .v-field__outline__end{
      border-radius: 0 12px 12px 0;
    }
    .rounded-theme .v-field--variant-outlined .v-field__outline__start{
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
    }
    .rounded-theme .v-field--variant-outlined .v-field__outline__end{
      border-top-right-radius: 10px;
      border-bottom-right-radius: 10px;
    }
    .v-field{
        border-radius: 12px;
    }
    .square-but-inp {
        height: 60px !important;
        width: 100% !important;
        width: 60px !important;
		padding: 0 !important;
        flex:1;
    }
    .current-search::before {
      content: "";
      width: 100%;
      position: absolute;
      left: 0;
      right: 0;
      height: 3px;
      background-color: rgb(var(--v-theme-surface));
      top: -3px;
  }
  .escale-timeline.v-timeline--horizontal .v-timeline-item.nodot .v-timeline-divider__dot{
    display: none !important;
  }
  .escale-timeline.v-timeline--horizontal .v-timeline-item:last-child .v-timeline-divider__after{
    right:-5px;
  }
  .escale-timeline .v-timeline-item__body,
  .escale-timeline .v-timeline-item__opposite{
    display: none;
  }
  
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.flight_item .v-list-item__content{
	overflow:visible;
}

.open-fare-detail:before{
	font-family: "Font Awesome 6 Free";
    content: "\f078";
    font-weight: 900;
    padding-right: 3px;;
}
    
    @media (min-width:301px) and (max-width:413px) {
        .square-but-inp {
            height: 44px !important;
            width: 44px !important;
            min-width: 44px !important;
        }
    }
    @media (min-width:414px){
        .square-but-inp {
            height: 60px !important;
            width: 60px !important;
            min-width: 60px !important;
        }
    }

    .dp__calendar_item[aria-disabled="true"],
    .dp__cell_disabled{
        pointer-events: none;
    }

    .ranged-picker:not(.range-picked) .dp__range_start{
      pointer-events: none;
      /* opacity: var(--v-disabled-opacity); */
    } 

    .dp__range_start{}

    .v-overlay__content>.v-card--variant-elevated,
    .v-overlay__content>.v-card--variant-flat {
        overflow: hidden;
        background-color: rgba(var(--v-theme-surface), 0.9);
        /* padding-bottom:20px; */
    }

    /* .v-overlay__content > .v-card--variant-elevated::after, .v-overlay__content > .v-card--variant-flat:after{
        content:"";
        width: 148px;
        height:5px;
        background-color: #ffffff;
        position:absolute;
        bottom:0;
        margin-left: calc(50% - 74px);
        border-radius: 24px;
        bottom: 8px;
    } */
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
    .euro-calendar .dp__selection_preview{
      width:100%;
    }
    .euro-calendar .dp__selection_preview:before{
      content:"\00a0 "
    }

    .v-switch .v-selection-control{
        flex-flow: row-reverse;
    }
    .v-switch .v-label{
        padding-left:0;
    }
    .bg-highlight{
      background-color: #F2F2F2;
    }
    .bg-background2{
      background-color: #2F3741;
    }
    .bg-none{
      background:none !important;
    }

    .font-weight-large {
        font-weight: 600 !important;
    }

    .v-selection-control__wrapper .v-switch__track {
        background-color: black !important
    }

    .v-selection-control__wrapper .v-switch__thumb {
        color: rgba(var(--v-theme-on-surface), var(--v-disabled-opacity)) !important
    }
    .v-selection-control--dirty .v-selection-control__wrapper .v-switch__thumb {
        color: rgba(var(--v-theme-primary), var(--v-high-emphasis-opacity)) !important
    }

    .to_move~.to_be_moved {
        transition: margin-bottom 0.3s linear;
    }

    .to_move_down~.to_be_moved {
        margin-bottom: -25vh;
    }


    
  .v-btn__append .mdi-chevron-down{
    opacity: var(--v-medium-emphasis-opacity);
  }
.pax {
    min-width: 100px;
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

    .dp__calendar {
        width: 100%;
        display: flex;
        flex-direction: column;
        margin: 0;
    }
    .dp__cell_inner{
        width: 100%;
        border-radius:0;
        padding:0 !important;
        height:30px;
		font-weight:300;
    }
    .dp__menu{
        border-color: #ffffff !important;
        min-width: auto;
    }
    .dp__calendar_item{
        flex:1;
    }
    .dp__today{
        background-color: #efefef;
        border-color: #efefef;
        font-weight:300;
    }
    .dp__calendar_header{
        font-weight:300;
    }
    .dp__range_end, .dp__range_start, .dp__active_date{
        background: rgb(var(--v-theme-primary));
        color: rgb(var(--v-theme-on-primary));
        font-weight:300;
    }
    .dp__calendar_header{
        width: 100%;
    }
	.dp__range_between{
		background: rgba(var(--v-theme-primary), 0.24);
		border-top: 1px solid rgba(var(--v-theme-primary), 0.24);
		border-bottom: 1px solid rgba(var(--v-theme-primary), 0.24);
	}
	.dp__instance_calendar:first-child > .dp__month_year_row{
		position: absolute;
		margin-top: 48px;
		z-index:1;
	}
	.dp__calendar_header_separator{
		display:none;
	}
	.dp__calendar_header:first-child {
		margin-bottom: 50px;
		margin-top: 0px;
	}
	
	.dp__calendar_header {
		border-bottom: 1px solid var(--dp-border-color);
	}
    /* .dp__instance_calendar + .dp__instance_calendar .dp__calendar_header{
        visibility: hidden;
    } */
    .dp__calendar_row{
        margin:0;
    }
    @media (max-width: 601px){
        .dp__instance_calendar + .dp__instance_calendar .dp__calendar_header{
            display: none;
        }
    }
    .dp__month_year_wrap{
        font-weight: 500;
    }
	.btn-preference{
		border-radius: 7px !important;
	}
	.line-before{
		margin-left:30px; 
		padding-left:10px;
		border-left:1px solid rgb(136 146 161 / 50%);
	}
    @media (min-width: 602px){
		.dp__instance_calendar > .dp__month_year_row{
			position: absolute;
			margin-top: 48px;
			z-index:1;
		}
		.dp__calendar_header {
			margin-bottom: 50px;
			margin-top: 0px;
		}
        .dp__instance_calendar + .dp__instance_calendar .dp__month_year_wrap{
            justify-content: end;
        }
    }
	button.cancel-button{
		border-color: #fff !important;
		border-width: 2px;
		background-color: rgba(24,29,35,0.9) !important
	}
    .dp__instance_calendar>.dp__flex_display{
        gap:15px;
    }
    
    .dp__calendar_header_item{
        text-transform: capitalize;
    }
    .dp__menu{
        width: 100%;
    }
    .dp__instance_calendar{
        flex:1;
    }

    .dp__month_year_select{
        text-transform: capitalize;
        width:auto;
        padding-left:10px;
    }

    .modal-button{
        height:auto !important;
        color: rgba(var(--v-theme-on-surface), var(--v-disabled-opacity));
    }
    .modal-button .v-btn__content{
        white-space: normal;
        color: #fff;
        flex-grow: 1;
        justify-content: start;
    }

    @media (min-width: 300px) {
  .d-xs-none {
    display: none !important;
  }

  .d-xs-inline {
    display: inline !important;
  }

  .d-xs-inline-block {
    display: inline-block !important;
  }

  .d-xs-block {
    display: block !important;
  }

  .d-xs-table {
    display: table !important;
  }

  .d-xs-table-row {
    display: table-row !important;
  }

  .d-xs-table-cell {
    display: table-cell !important;
  }

  .d-xs-flex {
    display: flex !important;
  }

  .d-xs-inline-flex {
    display: inline-flex !important;
  }

  .float-xs-none {
    float: none !important;
  }

  .float-xs-left {
    float: left !important;
  }

  .float-xs-right {
    float: right !important;
  }

  .v-locale--is-rtl .float-xs-end {
    float: left !important;
  }

  .v-locale--is-rtl .float-xs-start {
    float: right !important;
  }

  .v-locale--is-ltr .float-xs-end {
    float: right !important;
  }

  .v-locale--is-ltr .float-xs-start {
    float: left !important;
  }

  .flex-xs-fill {
    flex: 1 1 auto !important;
  }

  .flex-xs-row {
    flex-direction: row !important;
  }

  .flex-xs-column {
    flex-direction: column !important;
  }

  .flex-xs-row-reverse {
    flex-direction: row-reverse !important;
  }

  .flex-xs-column-reverse {
    flex-direction: column-reverse !important;
  }

  .flex-xs-grow-0 {
    flex-grow: 0 !important;
  }

  .flex-xs-grow-1 {
    flex-grow: 1 !important;
  }

  .flex-xs-shrink-0 {
    flex-shrink: 0 !important;
  }

  .flex-xs-shrink-1 {
    flex-shrink: 1 !important;
  }

  .flex-xs-wrap {
    flex-wrap: wrap !important;
  }

  .flex-xs-nowrap {
    flex-wrap: nowrap !important;
  }

  .flex-xs-wrap-reverse {
    flex-wrap: wrap-reverse !important;
  }

  .justify-xs-start {
    justify-content: flex-start !important;
  }

  .justify-xs-end {
    justify-content: flex-end !important;
  }

  .justify-xs-center {
    justify-content: center !important;
  }

  .justify-xs-space-between {
    justify-content: space-between !important;
  }

  .justify-xs-space-around {
    justify-content: space-around !important;
  }

  .justify-xs-space-evenly {
    justify-content: space-evenly !important;
  }

  .align-xs-start {
    align-items: flex-start !important;
  }

  .align-xs-end {
    align-items: flex-end !important;
  }

  .align-xs-center {
    align-items: center !important;
  }

  .align-xs-baseline {
    align-items: baseline !important;
  }

  .align-xs-stretch {
    align-items: stretch !important;
  }

  .align-content-xs-start {
    align-content: flex-start !important;
  }

  .align-content-xs-end {
    align-content: flex-end !important;
  }

  .align-content-xs-center {
    align-content: center !important;
  }

  .align-content-xs-space-between {
    align-content: space-between !important;
  }

  .align-content-xs-space-around {
    align-content: space-around !important;
  }

  .align-content-xs-space-evenly {
    align-content: space-evenly !important;
  }

  .align-content-xs-stretch {
    align-content: stretch !important;
  }

  .align-self-xs-auto {
    align-self: auto !important;
  }

  .align-self-xs-start {
    align-self: flex-start !important;
  }

  .align-self-xs-end {
    align-self: flex-end !important;
  }

  .align-self-xs-center {
    align-self: center !important;
  }

  .align-self-xs-baseline {
    align-self: baseline !important;
  }

  .align-self-xs-stretch {
    align-self: stretch !important;
  }

  .order-xs-first {
    order: -1 !important;
  }

  .order-xs-0 {
    order: 0 !important;
  }

  .order-xs-1 {
    order: 1 !important;
  }

  .order-xs-2 {
    order: 2 !important;
  }

  .order-xs-3 {
    order: 3 !important;
  }

  .order-xs-4 {
    order: 4 !important;
  }

  .order-xs-5 {
    order: 5 !important;
  }

  .order-xs-6 {
    order: 6 !important;
  }

  .order-xs-7 {
    order: 7 !important;
  }

  .order-xs-8 {
    order: 8 !important;
  }

  .order-xs-9 {
    order: 9 !important;
  }

  .order-xs-10 {
    order: 10 !important;
  }

  .order-xs-11 {
    order: 11 !important;
  }

  .order-xs-12 {
    order: 12 !important;
  }

  .order-xs-last {
    order: 13 !important;
  }

  .ma-xs-0 {
    margin: 0px !important;
  }

  .ma-xs-1 {
    margin: 4px !important;
  }

  .ma-xs-2 {
    margin: 8px !important;
  }

  .ma-xs-3 {
    margin: 12px !important;
  }

  .ma-xs-4 {
    margin: 16px !important;
  }

  .ma-xs-5 {
    margin: 20px !important;
  }

  .ma-xs-6 {
    margin: 24px !important;
  }

  .ma-xs-7 {
    margin: 28px !important;
  }

  .ma-xs-8 {
    margin: 32px !important;
  }

  .ma-xs-9 {
    margin: 36px !important;
  }

  .ma-xs-10 {
    margin: 40px !important;
  }

  .ma-xs-11 {
    margin: 44px !important;
  }

  .ma-xs-12 {
    margin: 48px !important;
  }

  .ma-xs-13 {
    margin: 52px !important;
  }

  .ma-xs-14 {
    margin: 56px !important;
  }

  .ma-xs-15 {
    margin: 60px !important;
  }

  .ma-xs-16 {
    margin: 64px !important;
  }

  .ma-xs-auto {
    margin: auto !important;
  }

  .mx-xs-0 {
    margin-right: 0px !important;
    margin-left: 0px !important;
  }

  .mx-xs-1 {
    margin-right: 4px !important;
    margin-left: 4px !important;
  }

  .mx-xs-2 {
    margin-right: 8px !important;
    margin-left: 8px !important;
  }

  .mx-xs-3 {
    margin-right: 12px !important;
    margin-left: 12px !important;
  }

  .mx-xs-4 {
    margin-right: 16px !important;
    margin-left: 16px !important;
  }

  .mx-xs-5 {
    margin-right: 20px !important;
    margin-left: 20px !important;
  }

  .mx-xs-6 {
    margin-right: 24px !important;
    margin-left: 24px !important;
  }

  .mx-xs-7 {
    margin-right: 28px !important;
    margin-left: 28px !important;
  }

  .mx-xs-8 {
    margin-right: 32px !important;
    margin-left: 32px !important;
  }

  .mx-xs-9 {
    margin-right: 36px !important;
    margin-left: 36px !important;
  }

  .mx-xs-10 {
    margin-right: 40px !important;
    margin-left: 40px !important;
  }

  .mx-xs-11 {
    margin-right: 44px !important;
    margin-left: 44px !important;
  }

  .mx-xs-12 {
    margin-right: 48px !important;
    margin-left: 48px !important;
  }

  .mx-xs-13 {
    margin-right: 52px !important;
    margin-left: 52px !important;
  }

  .mx-xs-14 {
    margin-right: 56px !important;
    margin-left: 56px !important;
  }

  .mx-xs-15 {
    margin-right: 60px !important;
    margin-left: 60px !important;
  }

  .mx-xs-16 {
    margin-right: 64px !important;
    margin-left: 64px !important;
  }

  .mx-xs-auto {
    margin-right: auto !important;
    margin-left: auto !important;
  }

  .my-xs-0 {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
  }

  .my-xs-1 {
    margin-top: 4px !important;
    margin-bottom: 4px !important;
  }

  .my-xs-2 {
    margin-top: 8px !important;
    margin-bottom: 8px !important;
  }

  .my-xs-3 {
    margin-top: 12px !important;
    margin-bottom: 12px !important;
  }

  .my-xs-4 {
    margin-top: 16px !important;
    margin-bottom: 16px !important;
  }

  .my-xs-5 {
    margin-top: 20px !important;
    margin-bottom: 20px !important;
  }

  .my-xs-6 {
    margin-top: 24px !important;
    margin-bottom: 24px !important;
  }

  .my-xs-7 {
    margin-top: 28px !important;
    margin-bottom: 28px !important;
  }

  .my-xs-8 {
    margin-top: 32px !important;
    margin-bottom: 32px !important;
  }

  .my-xs-9 {
    margin-top: 36px !important;
    margin-bottom: 36px !important;
  }

  .my-xs-10 {
    margin-top: 40px !important;
    margin-bottom: 40px !important;
  }

  .my-xs-11 {
    margin-top: 44px !important;
    margin-bottom: 44px !important;
  }

  .my-xs-12 {
    margin-top: 48px !important;
    margin-bottom: 48px !important;
  }

  .my-xs-13 {
    margin-top: 52px !important;
    margin-bottom: 52px !important;
  }

  .my-xs-14 {
    margin-top: 56px !important;
    margin-bottom: 56px !important;
  }

  .my-xs-15 {
    margin-top: 60px !important;
    margin-bottom: 60px !important;
  }

  .my-xs-16 {
    margin-top: 64px !important;
    margin-bottom: 64px !important;
  }

  .my-xs-auto {
    margin-top: auto !important;
    margin-bottom: auto !important;
  }

  .mt-xs-0 {
    margin-top: 0px !important;
  }

  .mt-xs-1 {
    margin-top: 4px !important;
  }

  .mt-xs-2 {
    margin-top: 8px !important;
  }

  .mt-xs-3 {
    margin-top: 12px !important;
  }

  .mt-xs-4 {
    margin-top: 16px !important;
  }

  .mt-xs-5 {
    margin-top: 20px !important;
  }

  .mt-xs-6 {
    margin-top: 24px !important;
  }

  .mt-xs-7 {
    margin-top: 28px !important;
  }

  .mt-xs-8 {
    margin-top: 32px !important;
  }

  .mt-xs-9 {
    margin-top: 36px !important;
  }

  .mt-xs-10 {
    margin-top: 40px !important;
  }

  .mt-xs-11 {
    margin-top: 44px !important;
  }

  .mt-xs-12 {
    margin-top: 48px !important;
  }

  .mt-xs-13 {
    margin-top: 52px !important;
  }

  .mt-xs-14 {
    margin-top: 56px !important;
  }

  .mt-xs-15 {
    margin-top: 60px !important;
  }

  .mt-xs-16 {
    margin-top: 64px !important;
  }

  .mt-xs-auto {
    margin-top: auto !important;
  }

  .mr-xs-0 {
    margin-right: 0px !important;
  }

  .mr-xs-1 {
    margin-right: 4px !important;
  }

  .mr-xs-2 {
    margin-right: 8px !important;
  }

  .mr-xs-3 {
    margin-right: 12px !important;
  }

  .mr-xs-4 {
    margin-right: 16px !important;
  }

  .mr-xs-5 {
    margin-right: 20px !important;
  }

  .mr-xs-6 {
    margin-right: 24px !important;
  }

  .mr-xs-7 {
    margin-right: 28px !important;
  }

  .mr-xs-8 {
    margin-right: 32px !important;
  }

  .mr-xs-9 {
    margin-right: 36px !important;
  }

  .mr-xs-10 {
    margin-right: 40px !important;
  }

  .mr-xs-11 {
    margin-right: 44px !important;
  }

  .mr-xs-12 {
    margin-right: 48px !important;
  }

  .mr-xs-13 {
    margin-right: 52px !important;
  }

  .mr-xs-14 {
    margin-right: 56px !important;
  }

  .mr-xs-15 {
    margin-right: 60px !important;
  }

  .mr-xs-16 {
    margin-right: 64px !important;
  }

  .mr-xs-auto {
    margin-right: auto !important;
  }

  .mb-xs-0 {
    margin-bottom: 0px !important;
  }

  .mb-xs-1 {
    margin-bottom: 4px !important;
  }

  .mb-xs-2 {
    margin-bottom: 8px !important;
  }

  .mb-xs-3 {
    margin-bottom: 12px !important;
  }

  .mb-xs-4 {
    margin-bottom: 16px !important;
  }

  .mb-xs-5 {
    margin-bottom: 20px !important;
  }

  .mb-xs-6 {
    margin-bottom: 24px !important;
  }

  .mb-xs-7 {
    margin-bottom: 28px !important;
  }

  .mb-xs-8 {
    margin-bottom: 32px !important;
  }

  .mb-xs-9 {
    margin-bottom: 36px !important;
  }

  .mb-xs-10 {
    margin-bottom: 40px !important;
  }

  .mb-xs-11 {
    margin-bottom: 44px !important;
  }

  .mb-xs-12 {
    margin-bottom: 48px !important;
  }

  .mb-xs-13 {
    margin-bottom: 52px !important;
  }

  .mb-xs-14 {
    margin-bottom: 56px !important;
  }

  .mb-xs-15 {
    margin-bottom: 60px !important;
  }

  .mb-xs-16 {
    margin-bottom: 64px !important;
  }

  .mb-xs-auto {
    margin-bottom: auto !important;
  }

  .ml-xs-0 {
    margin-left: 0px !important;
  }

  .ml-xs-1 {
    margin-left: 4px !important;
  }

  .ml-xs-2 {
    margin-left: 8px !important;
  }

  .ml-xs-3 {
    margin-left: 12px !important;
  }

  .ml-xs-4 {
    margin-left: 16px !important;
  }

  .ml-xs-5 {
    margin-left: 20px !important;
  }

  .ml-xs-6 {
    margin-left: 24px !important;
  }

  .ml-xs-7 {
    margin-left: 28px !important;
  }

  .ml-xs-8 {
    margin-left: 32px !important;
  }

  .ml-xs-9 {
    margin-left: 36px !important;
  }

  .ml-xs-10 {
    margin-left: 40px !important;
  }

  .ml-xs-11 {
    margin-left: 44px !important;
  }

  .ml-xs-12 {
    margin-left: 48px !important;
  }

  .ml-xs-13 {
    margin-left: 52px !important;
  }

  .ml-xs-14 {
    margin-left: 56px !important;
  }

  .ml-xs-15 {
    margin-left: 60px !important;
  }

  .ml-xs-16 {
    margin-left: 64px !important;
  }

  .ml-xs-auto {
    margin-left: auto !important;
  }

  .ms-xs-0 {
    margin-inline-start: 0px !important;
  }

  .ms-xs-1 {
    margin-inline-start: 4px !important;
  }

  .ms-xs-2 {
    margin-inline-start: 8px !important;
  }

  .ms-xs-3 {
    margin-inline-start: 12px !important;
  }

  .ms-xs-4 {
    margin-inline-start: 16px !important;
  }

  .ms-xs-5 {
    margin-inline-start: 20px !important;
  }

  .ms-xs-6 {
    margin-inline-start: 24px !important;
  }

  .ms-xs-7 {
    margin-inline-start: 28px !important;
  }

  .ms-xs-8 {
    margin-inline-start: 32px !important;
  }

  .ms-xs-9 {
    margin-inline-start: 36px !important;
  }

  .ms-xs-10 {
    margin-inline-start: 40px !important;
  }

  .ms-xs-11 {
    margin-inline-start: 44px !important;
  }

  .ms-xs-12 {
    margin-inline-start: 48px !important;
  }

  .ms-xs-13 {
    margin-inline-start: 52px !important;
  }

  .ms-xs-14 {
    margin-inline-start: 56px !important;
  }

  .ms-xs-15 {
    margin-inline-start: 60px !important;
  }

  .ms-xs-16 {
    margin-inline-start: 64px !important;
  }

  .ms-xs-auto {
    margin-inline-start: auto !important;
  }

  .me-xs-0 {
    margin-inline-end: 0px !important;
  }

  .me-xs-1 {
    margin-inline-end: 4px !important;
  }

  .me-xs-2 {
    margin-inline-end: 8px !important;
  }

  .me-xs-3 {
    margin-inline-end: 12px !important;
  }

  .me-xs-4 {
    margin-inline-end: 16px !important;
  }

  .me-xs-5 {
    margin-inline-end: 20px !important;
  }

  .me-xs-6 {
    margin-inline-end: 24px !important;
  }

  .me-xs-7 {
    margin-inline-end: 28px !important;
  }

  .me-xs-8 {
    margin-inline-end: 32px !important;
  }

  .me-xs-9 {
    margin-inline-end: 36px !important;
  }

  .me-xs-10 {
    margin-inline-end: 40px !important;
  }

  .me-xs-11 {
    margin-inline-end: 44px !important;
  }

  .me-xs-12 {
    margin-inline-end: 48px !important;
  }

  .me-xs-13 {
    margin-inline-end: 52px !important;
  }

  .me-xs-14 {
    margin-inline-end: 56px !important;
  }

  .me-xs-15 {
    margin-inline-end: 60px !important;
  }

  .me-xs-16 {
    margin-inline-end: 64px !important;
  }

  .me-xs-auto {
    margin-inline-end: auto !important;
  }

  .ma-xs-n1 {
    margin: -4px !important;
  }

  .ma-xs-n2 {
    margin: -8px !important;
  }

  .ma-xs-n3 {
    margin: -12px !important;
  }

  .ma-xs-n4 {
    margin: -16px !important;
  }

  .ma-xs-n5 {
    margin: -20px !important;
  }

  .ma-xs-n6 {
    margin: -24px !important;
  }

  .ma-xs-n7 {
    margin: -28px !important;
  }

  .ma-xs-n8 {
    margin: -32px !important;
  }

  .ma-xs-n9 {
    margin: -36px !important;
  }

  .ma-xs-n10 {
    margin: -40px !important;
  }

  .ma-xs-n11 {
    margin: -44px !important;
  }

  .ma-xs-n12 {
    margin: -48px !important;
  }

  .ma-xs-n13 {
    margin: -52px !important;
  }

  .ma-xs-n14 {
    margin: -56px !important;
  }

  .ma-xs-n15 {
    margin: -60px !important;
  }

  .ma-xs-n16 {
    margin: -64px !important;
  }

  .mx-xs-n1 {
    margin-right: -4px !important;
    margin-left: -4px !important;
  }

  .mx-xs-n2 {
    margin-right: -8px !important;
    margin-left: -8px !important;
  }

  .mx-xs-n3 {
    margin-right: -12px !important;
    margin-left: -12px !important;
  }

  .mx-xs-n4 {
    margin-right: -16px !important;
    margin-left: -16px !important;
  }

  .mx-xs-n5 {
    margin-right: -20px !important;
    margin-left: -20px !important;
  }

  .mx-xs-n6 {
    margin-right: -24px !important;
    margin-left: -24px !important;
  }

  .mx-xs-n7 {
    margin-right: -28px !important;
    margin-left: -28px !important;
  }

  .mx-xs-n8 {
    margin-right: -32px !important;
    margin-left: -32px !important;
  }

  .mx-xs-n9 {
    margin-right: -36px !important;
    margin-left: -36px !important;
  }

  .mx-xs-n10 {
    margin-right: -40px !important;
    margin-left: -40px !important;
  }

  .mx-xs-n11 {
    margin-right: -44px !important;
    margin-left: -44px !important;
  }

  .mx-xs-n12 {
    margin-right: -48px !important;
    margin-left: -48px !important;
  }

  .mx-xs-n13 {
    margin-right: -52px !important;
    margin-left: -52px !important;
  }

  .mx-xs-n14 {
    margin-right: -56px !important;
    margin-left: -56px !important;
  }

  .mx-xs-n15 {
    margin-right: -60px !important;
    margin-left: -60px !important;
  }

  .mx-xs-n16 {
    margin-right: -64px !important;
    margin-left: -64px !important;
  }

  .my-xs-n1 {
    margin-top: -4px !important;
    margin-bottom: -4px !important;
  }

  .my-xs-n2 {
    margin-top: -8px !important;
    margin-bottom: -8px !important;
  }

  .my-xs-n3 {
    margin-top: -12px !important;
    margin-bottom: -12px !important;
  }

  .my-xs-n4 {
    margin-top: -16px !important;
    margin-bottom: -16px !important;
  }

  .my-xs-n5 {
    margin-top: -20px !important;
    margin-bottom: -20px !important;
  }

  .my-xs-n6 {
    margin-top: -24px !important;
    margin-bottom: -24px !important;
  }

  .my-xs-n7 {
    margin-top: -28px !important;
    margin-bottom: -28px !important;
  }

  .my-xs-n8 {
    margin-top: -32px !important;
    margin-bottom: -32px !important;
  }

  .my-xs-n9 {
    margin-top: -36px !important;
    margin-bottom: -36px !important;
  }

  .my-xs-n10 {
    margin-top: -40px !important;
    margin-bottom: -40px !important;
  }

  .my-xs-n11 {
    margin-top: -44px !important;
    margin-bottom: -44px !important;
  }

  .my-xs-n12 {
    margin-top: -48px !important;
    margin-bottom: -48px !important;
  }

  .my-xs-n13 {
    margin-top: -52px !important;
    margin-bottom: -52px !important;
  }

  .my-xs-n14 {
    margin-top: -56px !important;
    margin-bottom: -56px !important;
  }

  .my-xs-n15 {
    margin-top: -60px !important;
    margin-bottom: -60px !important;
  }

  .my-xs-n16 {
    margin-top: -64px !important;
    margin-bottom: -64px !important;
  }

  .mt-xs-n1 {
    margin-top: -4px !important;
  }

  .mt-xs-n2 {
    margin-top: -8px !important;
  }

  .mt-xs-n3 {
    margin-top: -12px !important;
  }

  .mt-xs-n4 {
    margin-top: -16px !important;
  }

  .mt-xs-n5 {
    margin-top: -20px !important;
  }

  .mt-xs-n6 {
    margin-top: -24px !important;
  }

  .mt-xs-n7 {
    margin-top: -28px !important;
  }

  .mt-xs-n8 {
    margin-top: -32px !important;
  }

  .mt-xs-n9 {
    margin-top: -36px !important;
  }

  .mt-xs-n10 {
    margin-top: -40px !important;
  }

  .mt-xs-n11 {
    margin-top: -44px !important;
  }

  .mt-xs-n12 {
    margin-top: -48px !important;
  }

  .mt-xs-n13 {
    margin-top: -52px !important;
  }

  .mt-xs-n14 {
    margin-top: -56px !important;
  }

  .mt-xs-n15 {
    margin-top: -60px !important;
  }

  .mt-xs-n16 {
    margin-top: -64px !important;
  }

  .mr-xs-n1 {
    margin-right: -4px !important;
  }

  .mr-xs-n2 {
    margin-right: -8px !important;
  }

  .mr-xs-n3 {
    margin-right: -12px !important;
  }

  .mr-xs-n4 {
    margin-right: -16px !important;
  }

  .mr-xs-n5 {
    margin-right: -20px !important;
  }

  .mr-xs-n6 {
    margin-right: -24px !important;
  }

  .mr-xs-n7 {
    margin-right: -28px !important;
  }

  .mr-xs-n8 {
    margin-right: -32px !important;
  }

  .mr-xs-n9 {
    margin-right: -36px !important;
  }

  .mr-xs-n10 {
    margin-right: -40px !important;
  }

  .mr-xs-n11 {
    margin-right: -44px !important;
  }

  .mr-xs-n12 {
    margin-right: -48px !important;
  }

  .mr-xs-n13 {
    margin-right: -52px !important;
  }

  .mr-xs-n14 {
    margin-right: -56px !important;
  }

  .mr-xs-n15 {
    margin-right: -60px !important;
  }

  .mr-xs-n16 {
    margin-right: -64px !important;
  }

  .mb-xs-n1 {
    margin-bottom: -4px !important;
  }

  .mb-xs-n2 {
    margin-bottom: -8px !important;
  }

  .mb-xs-n3 {
    margin-bottom: -12px !important;
  }

  .mb-xs-n4 {
    margin-bottom: -16px !important;
  }

  .mb-xs-n5 {
    margin-bottom: -20px !important;
  }

  .mb-xs-n6 {
    margin-bottom: -24px !important;
  }

  .mb-xs-n7 {
    margin-bottom: -28px !important;
  }

  .mb-xs-n8 {
    margin-bottom: -32px !important;
  }

  .mb-xs-n9 {
    margin-bottom: -36px !important;
  }

  .mb-xs-n10 {
    margin-bottom: -40px !important;
  }

  .mb-xs-n11 {
    margin-bottom: -44px !important;
  }

  .mb-xs-n12 {
    margin-bottom: -48px !important;
  }

  .mb-xs-n13 {
    margin-bottom: -52px !important;
  }

  .mb-xs-n14 {
    margin-bottom: -56px !important;
  }

  .mb-xs-n15 {
    margin-bottom: -60px !important;
  }

  .mb-xs-n16 {
    margin-bottom: -64px !important;
  }

  .ml-xs-n1 {
    margin-left: -4px !important;
  }

  .ml-xs-n2 {
    margin-left: -8px !important;
  }

  .ml-xs-n3 {
    margin-left: -12px !important;
  }

  .ml-xs-n4 {
    margin-left: -16px !important;
  }

  .ml-xs-n5 {
    margin-left: -20px !important;
  }

  .ml-xs-n6 {
    margin-left: -24px !important;
  }

  .ml-xs-n7 {
    margin-left: -28px !important;
  }

  .ml-xs-n8 {
    margin-left: -32px !important;
  }

  .ml-xs-n9 {
    margin-left: -36px !important;
  }

  .ml-xs-n10 {
    margin-left: -40px !important;
  }

  .ml-xs-n11 {
    margin-left: -44px !important;
  }

  .ml-xs-n12 {
    margin-left: -48px !important;
  }

  .ml-xs-n13 {
    margin-left: -52px !important;
  }

  .ml-xs-n14 {
    margin-left: -56px !important;
  }

  .ml-xs-n15 {
    margin-left: -60px !important;
  }

  .ml-xs-n16 {
    margin-left: -64px !important;
  }

  .ms-xs-n1 {
    margin-inline-start: -4px !important;
  }

  .ms-xs-n2 {
    margin-inline-start: -8px !important;
  }

  .ms-xs-n3 {
    margin-inline-start: -12px !important;
  }

  .ms-xs-n4 {
    margin-inline-start: -16px !important;
  }

  .ms-xs-n5 {
    margin-inline-start: -20px !important;
  }

  .ms-xs-n6 {
    margin-inline-start: -24px !important;
  }

  .ms-xs-n7 {
    margin-inline-start: -28px !important;
  }

  .ms-xs-n8 {
    margin-inline-start: -32px !important;
  }

  .ms-xs-n9 {
    margin-inline-start: -36px !important;
  }

  .ms-xs-n10 {
    margin-inline-start: -40px !important;
  }

  .ms-xs-n11 {
    margin-inline-start: -44px !important;
  }

  .ms-xs-n12 {
    margin-inline-start: -48px !important;
  }

  .ms-xs-n13 {
    margin-inline-start: -52px !important;
  }

  .ms-xs-n14 {
    margin-inline-start: -56px !important;
  }

  .ms-xs-n15 {
    margin-inline-start: -60px !important;
  }

  .ms-xs-n16 {
    margin-inline-start: -64px !important;
  }

  .me-xs-n1 {
    margin-inline-end: -4px !important;
  }

  .me-xs-n2 {
    margin-inline-end: -8px !important;
  }

  .me-xs-n3 {
    margin-inline-end: -12px !important;
  }

  .me-xs-n4 {
    margin-inline-end: -16px !important;
  }

  .me-xs-n5 {
    margin-inline-end: -20px !important;
  }

  .me-xs-n6 {
    margin-inline-end: -24px !important;
  }

  .me-xs-n7 {
    margin-inline-end: -28px !important;
  }

  .me-xs-n8 {
    margin-inline-end: -32px !important;
  }

  .me-xs-n9 {
    margin-inline-end: -36px !important;
  }

  .me-xs-n10 {
    margin-inline-end: -40px !important;
  }

  .me-xs-n11 {
    margin-inline-end: -44px !important;
  }

  .me-xs-n12 {
    margin-inline-end: -48px !important;
  }

  .me-xs-n13 {
    margin-inline-end: -52px !important;
  }

  .me-xs-n14 {
    margin-inline-end: -56px !important;
  }

  .me-xs-n15 {
    margin-inline-end: -60px !important;
  }

  .me-xs-n16 {
    margin-inline-end: -64px !important;
  }

  .pa-xs-0 {
    padding: 0px !important;
  }

  .pa-xs-1 {
    padding: 4px !important;
  }

  .pa-xs-2 {
    padding: 8px !important;
  }

  .pa-xs-3 {
    padding: 12px !important;
  }

  .pa-xs-4 {
    padding: 16px !important;
  }

  .pa-xs-5 {
    padding: 20px !important;
  }

  .pa-xs-6 {
    padding: 24px !important;
  }

  .pa-xs-7 {
    padding: 28px !important;
  }

  .pa-xs-8 {
    padding: 32px !important;
  }

  .pa-xs-9 {
    padding: 36px !important;
  }

  .pa-xs-10 {
    padding: 40px !important;
  }

  .pa-xs-11 {
    padding: 44px !important;
  }

  .pa-xs-12 {
    padding: 48px !important;
  }

  .pa-xs-13 {
    padding: 52px !important;
  }

  .pa-xs-14 {
    padding: 56px !important;
  }

  .pa-xs-15 {
    padding: 60px !important;
  }

  .pa-xs-16 {
    padding: 64px !important;
  }

  .px-xs-0 {
    padding-right: 0px !important;
    padding-left: 0px !important;
  }

  .px-xs-1 {
    padding-right: 4px !important;
    padding-left: 4px !important;
  }

  .px-xs-2 {
    padding-right: 8px !important;
    padding-left: 8px !important;
  }

  .px-xs-3 {
    padding-right: 12px !important;
    padding-left: 12px !important;
  }

  .px-xs-4 {
    padding-right: 16px !important;
    padding-left: 16px !important;
  }

  .px-xs-5 {
    padding-right: 20px !important;
    padding-left: 20px !important;
  }

  .px-xs-6 {
    padding-right: 24px !important;
    padding-left: 24px !important;
  }

  .px-xs-7 {
    padding-right: 28px !important;
    padding-left: 28px !important;
  }

  .px-xs-8 {
    padding-right: 32px !important;
    padding-left: 32px !important;
  }

  .px-xs-9 {
    padding-right: 36px !important;
    padding-left: 36px !important;
  }

  .px-xs-10 {
    padding-right: 40px !important;
    padding-left: 40px !important;
  }

  .px-xs-11 {
    padding-right: 44px !important;
    padding-left: 44px !important;
  }

  .px-xs-12 {
    padding-right: 48px !important;
    padding-left: 48px !important;
  }

  .px-xs-13 {
    padding-right: 52px !important;
    padding-left: 52px !important;
  }

  .px-xs-14 {
    padding-right: 56px !important;
    padding-left: 56px !important;
  }

  .px-xs-15 {
    padding-right: 60px !important;
    padding-left: 60px !important;
  }

  .px-xs-16 {
    padding-right: 64px !important;
    padding-left: 64px !important;
  }

  .py-xs-0 {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
  }

  .py-xs-1 {
    padding-top: 4px !important;
    padding-bottom: 4px !important;
  }

  .py-xs-2 {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
  }

  .py-xs-3 {
    padding-top: 12px !important;
    padding-bottom: 12px !important;
  }

  .py-xs-4 {
    padding-top: 16px !important;
    padding-bottom: 16px !important;
  }

  .py-xs-5 {
    padding-top: 20px !important;
    padding-bottom: 20px !important;
  }

  .py-xs-6 {
    padding-top: 24px !important;
    padding-bottom: 24px !important;
  }

  .py-xs-7 {
    padding-top: 28px !important;
    padding-bottom: 28px !important;
  }

  .py-xs-8 {
    padding-top: 32px !important;
    padding-bottom: 32px !important;
  }

  .py-xs-9 {
    padding-top: 36px !important;
    padding-bottom: 36px !important;
  }

  .py-xs-10 {
    padding-top: 40px !important;
    padding-bottom: 40px !important;
  }

  .py-xs-11 {
    padding-top: 44px !important;
    padding-bottom: 44px !important;
  }

  .py-xs-12 {
    padding-top: 48px !important;
    padding-bottom: 48px !important;
  }

  .py-xs-13 {
    padding-top: 52px !important;
    padding-bottom: 52px !important;
  }

  .py-xs-14 {
    padding-top: 56px !important;
    padding-bottom: 56px !important;
  }

  .py-xs-15 {
    padding-top: 60px !important;
    padding-bottom: 60px !important;
  }

  .py-xs-16 {
    padding-top: 64px !important;
    padding-bottom: 64px !important;
  }

  .pt-xs-0 {
    padding-top: 0px !important;
  }

  .pt-xs-1 {
    padding-top: 4px !important;
  }

  .pt-xs-2 {
    padding-top: 8px !important;
  }

  .pt-xs-3 {
    padding-top: 12px !important;
  }

  .pt-xs-4 {
    padding-top: 16px !important;
  }

  .pt-xs-5 {
    padding-top: 20px !important;
  }

  .pt-xs-6 {
    padding-top: 24px !important;
  }

  .pt-xs-7 {
    padding-top: 28px !important;
  }

  .pt-xs-8 {
    padding-top: 32px !important;
  }

  .pt-xs-9 {
    padding-top: 36px !important;
  }

  .pt-xs-10 {
    padding-top: 40px !important;
  }

  .pt-xs-11 {
    padding-top: 44px !important;
  }

  .pt-xs-12 {
    padding-top: 48px !important;
  }

  .pt-xs-13 {
    padding-top: 52px !important;
  }

  .pt-xs-14 {
    padding-top: 56px !important;
  }

  .pt-xs-15 {
    padding-top: 60px !important;
  }

  .pt-xs-16 {
    padding-top: 64px !important;
  }

  .pr-xs-0 {
    padding-right: 0px !important;
  }

  .pr-xs-1 {
    padding-right: 4px !important;
  }

  .pr-xs-2 {
    padding-right: 8px !important;
  }

  .pr-xs-3 {
    padding-right: 12px !important;
  }

  .pr-xs-4 {
    padding-right: 16px !important;
  }

  .pr-xs-5 {
    padding-right: 20px !important;
  }

  .pr-xs-6 {
    padding-right: 24px !important;
  }

  .pr-xs-7 {
    padding-right: 28px !important;
  }

  .pr-xs-8 {
    padding-right: 32px !important;
  }

  .pr-xs-9 {
    padding-right: 36px !important;
  }

  .pr-xs-10 {
    padding-right: 40px !important;
  }

  .pr-xs-11 {
    padding-right: 44px !important;
  }

  .pr-xs-12 {
    padding-right: 48px !important;
  }

  .pr-xs-13 {
    padding-right: 52px !important;
  }

  .pr-xs-14 {
    padding-right: 56px !important;
  }

  .pr-xs-15 {
    padding-right: 60px !important;
  }

  .pr-xs-16 {
    padding-right: 64px !important;
  }

  .pb-xs-0 {
    padding-bottom: 0px !important;
  }

  .pb-xs-1 {
    padding-bottom: 4px !important;
  }

  .pb-xs-2 {
    padding-bottom: 8px !important;
  }

  .pb-xs-3 {
    padding-bottom: 12px !important;
  }

  .pb-xs-4 {
    padding-bottom: 16px !important;
  }

  .pb-xs-5 {
    padding-bottom: 20px !important;
  }

  .pb-xs-6 {
    padding-bottom: 24px !important;
  }

  .pb-xs-7 {
    padding-bottom: 28px !important;
  }

  .pb-xs-8 {
    padding-bottom: 32px !important;
  }

  .pb-xs-9 {
    padding-bottom: 36px !important;
  }

  .pb-xs-10 {
    padding-bottom: 40px !important;
  }

  .pb-xs-11 {
    padding-bottom: 44px !important;
  }

  .pb-xs-12 {
    padding-bottom: 48px !important;
  }

  .pb-xs-13 {
    padding-bottom: 52px !important;
  }

  .pb-xs-14 {
    padding-bottom: 56px !important;
  }

  .pb-xs-15 {
    padding-bottom: 60px !important;
  }

  .pb-xs-16 {
    padding-bottom: 64px !important;
  }

  .pl-xs-0 {
    padding-left: 0px !important;
  }

  .pl-xs-1 {
    padding-left: 4px !important;
  }

  .pl-xs-2 {
    padding-left: 8px !important;
  }

  .pl-xs-3 {
    padding-left: 12px !important;
  }

  .pl-xs-4 {
    padding-left: 16px !important;
  }

  .pl-xs-5 {
    padding-left: 20px !important;
  }

  .pl-xs-6 {
    padding-left: 24px !important;
  }

  .pl-xs-7 {
    padding-left: 28px !important;
  }

  .pl-xs-8 {
    padding-left: 32px !important;
  }

  .pl-xs-9 {
    padding-left: 36px !important;
  }

  .pl-xs-10 {
    padding-left: 40px !important;
  }

  .pl-xs-11 {
    padding-left: 44px !important;
  }

  .pl-xs-12 {
    padding-left: 48px !important;
  }

  .pl-xs-13 {
    padding-left: 52px !important;
  }

  .pl-xs-14 {
    padding-left: 56px !important;
  }

  .pl-xs-15 {
    padding-left: 60px !important;
  }

  .pl-xs-16 {
    padding-left: 64px !important;
  }

  .ps-xs-0 {
    padding-inline-start: 0px !important;
  }

  .ps-xs-1 {
    padding-inline-start: 4px !important;
  }

  .ps-xs-2 {
    padding-inline-start: 8px !important;
  }

  .ps-xs-3 {
    padding-inline-start: 12px !important;
  }

  .ps-xs-4 {
    padding-inline-start: 16px !important;
  }

  .ps-xs-5 {
    padding-inline-start: 20px !important;
  }

  .ps-xs-6 {
    padding-inline-start: 24px !important;
  }

  .ps-xs-7 {
    padding-inline-start: 28px !important;
  }

  .ps-xs-8 {
    padding-inline-start: 32px !important;
  }

  .ps-xs-9 {
    padding-inline-start: 36px !important;
  }

  .ps-xs-10 {
    padding-inline-start: 40px !important;
  }

  .ps-xs-11 {
    padding-inline-start: 44px !important;
  }

  .ps-xs-12 {
    padding-inline-start: 48px !important;
  }

  .ps-xs-13 {
    padding-inline-start: 52px !important;
  }

  .ps-xs-14 {
    padding-inline-start: 56px !important;
  }

  .ps-xs-15 {
    padding-inline-start: 60px !important;
  }

  .ps-xs-16 {
    padding-inline-start: 64px !important;
  }

  .pe-xs-0 {
    padding-inline-end: 0px !important;
  }

  .pe-xs-1 {
    padding-inline-end: 4px !important;
  }

  .pe-xs-2 {
    padding-inline-end: 8px !important;
  }

  .pe-xs-3 {
    padding-inline-end: 12px !important;
  }

  .pe-xs-4 {
    padding-inline-end: 16px !important;
  }

  .pe-xs-5 {
    padding-inline-end: 20px !important;
  }

  .pe-xs-6 {
    padding-inline-end: 24px !important;
  }

  .pe-xs-7 {
    padding-inline-end: 28px !important;
  }

  .pe-xs-8 {
    padding-inline-end: 32px !important;
  }

  .pe-xs-9 {
    padding-inline-end: 36px !important;
  }

  .pe-xs-10 {
    padding-inline-end: 40px !important;
  }

  .pe-xs-11 {
    padding-inline-end: 44px !important;
  }

  .pe-xs-12 {
    padding-inline-end: 48px !important;
  }

  .pe-xs-13 {
    padding-inline-end: 52px !important;
  }

  .pe-xs-14 {
    padding-inline-end: 56px !important;
  }

  .pe-xs-15 {
    padding-inline-end: 60px !important;
  }

  .pe-xs-16 {
    padding-inline-end: 64px !important;
  }

  .text-xs-left {
    text-align: left !important;
  }

  .text-xs-right {
    text-align: right !important;
  }

  .text-xs-center {
    text-align: center !important;
  }

  .text-xs-justify {
    text-align: justify !important;
  }

  .text-xs-start {
    text-align: start !important;
  }

  .text-xs-end {
    text-align: end !important;
  }

  .text-xs-h1 {
    font-size: 6rem !important;
    font-weight: 300;
    line-height: 6rem;
    letter-spacing: -0.015625em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-h2 {
    font-size: 3.75rem !important;
    font-weight: 300;
    line-height: 3.75rem;
    letter-spacing: -0.0083333333em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-h3 {
    font-size: 3rem !important;
    font-weight: 400;
    line-height: 3.125rem;
    letter-spacing: normal !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-h4 {
    font-size: 2.125rem !important;
    font-weight: 400;
    line-height: 2.5rem;
    letter-spacing: 0.0073529412em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-h5 {
    font-size: 1.5rem !important;
    font-weight: 400;
    line-height: 2rem;
    letter-spacing: normal !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-h6 {
    font-size: 1.25rem !important;
    font-weight: 500;
    line-height: 2rem;
    letter-spacing: 0.0125em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-subtitle-1 {
    font-size: 1rem !important;
    font-weight: normal;
    line-height: 1.75rem;
    letter-spacing: 0.009375em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-subtitle-2 {
    font-size: 0.875rem !important;
    font-weight: 500;
    line-height: 1.375rem;
    letter-spacing: 0.0071428571em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-body-1 {
    font-size: 1rem !important;
    font-weight: 400;
    line-height: 1.5rem;
    letter-spacing: 0.03125em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-body-2 {
    font-size: 0.875rem !important;
    font-weight: 400;
    line-height: 1.25rem;
    letter-spacing: 0.0178571429em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-button {
    font-size: 0.875rem !important;
    font-weight: 500;
    line-height: 2.25rem;
    letter-spacing: 0.0892857143em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: uppercase !important;
  }

  .text-xs-caption {
    font-size: 0.75rem !important;
    font-weight: 400;
    line-height: 1.25rem;
    letter-spacing: 0.0333333333em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
  }

  .text-xs-overline {
    font-size: 0.75rem !important;
    font-weight: 500;
    line-height: 2rem;
    letter-spacing: 0.1666666667em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: uppercase !important;
  }
}
.dialog-bottom-transition-enter-active,
  .dialog-bottom-transition-leave-active {
    transition: transform .2s ease-in-out;
  }
  .date-selector:not(:hover) > div{
	  pointer-events:none;
  }
  .date-selector:not(:hover) .highlight{
	  opacity:0.5;
  }
</style>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>