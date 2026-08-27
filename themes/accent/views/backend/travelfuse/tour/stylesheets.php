<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
.loading-state{
	opahotel:0.5;
	cursor:wait;
}
.loading-state *{
	pointer-events:none !important;
}
.hotel-image{
	position:relative;
	z-index: 1;
	max-height:50px;
	max-width:50px;
	transition: 0.5s ease transform;
	transform: scale(1);
}
.hotel-image:hover{
	z-index: 2;
	transform: scale(4);
}
.on-off-show > input[type=checkbox]:not(:checked) ~ .is-on,
.on-off-show > input[type=checkbox]:checked ~ .is-off{
	display:none;
}
.todelete{
	color: red;
}
.hotel-facilities-list{
	columns: 4;
	width:100%;
}
.modal-xl{
	max-width: 90vw;
}
@media (max-width: 1024px) {
	.hotel-facilities-list{
		columns: 3;
	}
}
@media (max-width: 768px) {
	.hotel-facilities-list{
		columns: 2;
	}
}
@media (max-width: 400px) {
	.hotel-facilities-list{
		columns: 1;
	}
}
.hotel-facilities-list > *{
	break-inside: avoid-column;
}

table.crt > tbody {
	counter-reset: crt;
}
table.crt > tbody > tr {
	counter-increment: crt;
}
table.crt span.crt:before {
	content: counter(crt);
}
</style>
<?php themeFunctions::debugFileLine('end'); ?>