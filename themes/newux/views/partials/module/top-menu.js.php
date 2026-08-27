import ExtendTemplate from './templates/menu.js?newux=1';
export default {
	extends: ExtendTemplate,
	name: '<?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>',
	data: () => ({
		name: '<?php echo basename(__FILE__,'.js.php'); ?>',
		data: {
    "logo": {
        "props": {
            "src": "/themes/newux/assets/images/logo.svg",
            "width": "176",
            "height": "70",
            "class": "mx-auto",
            "cover": false,
            "maxWidth": "100%",
            "minWidth": ""
        },
        "a_props": {}
    },
    "list": {
        "title": "top menu",
        "items": [
            {
                "text": "Travel Gift Card",
                "href": "/gift-card",
                "list": {
                    "items": [],
                    "children": []
                },
                "mod_template": null,
                "mega_menu": false
            },
            {
                "text": "Corporate",
                "href": "https://www.accenttravel.biz/"
            },
            {
                "text": "Blog",
                "href": "/blog"
            },
            {
                "text": "Contact",
                "href": "/contact"
            },
            {
                "text": "xeSIM",
                "href": "xesim-date-mobile-travel"
            }
        ],
        "props": {
            "class": null,
            "style": null
        },
        "prepend_icon": null
    },
    "props": {},
    "list2": {
        "props": {
            "class": "meniu top-meniu top-meniu-dreapta"
        },
        "items": [
            {
                "text": "021 314 1980",
                "href": "tel://0213141980",
                "icon": "mdi-phone-outline"
            },
            {
                "text": "Contul meu",
                "href": "",
                "icon": "mdi-account-outline",
                "list": {
                    "title": "",
                    "items": [
                        {
                            "text": "Setari cont",
                            "href": "/contul-meu",
                            "icon": "mdi-account",
                            "auth": "logged-in"
                        },
                        {
                            "text": "Istoric comenzi",
                            "href": "/account/trip/orders",
                            "icon": "mdi-history",
                            "auth": "logged-in"
                        },
                        {
                            "text": "Alerte oferte",
                            "href": "/notifications",
                            "icon": "mdi-bell",
                            "auth": "logged-in"
                        },
                        {
                            "text": "Autentificare",
                            "href": "javascript:authPopup('login')",
                            "icon": "mdi-lock",
                            "auth": "logged-out"
                        },
                        {
                            "text": "Inregistrare",
                            "href": "javascript:authPopup('register')",
                            "icon": "mdi-key",
                            "auth": "logged-out"
                        },
                        {
                            "text": "Delogare",
                            "href": "/account/logout",
                            "icon": "mdi-lock-open-outline",
                            "auth": "logged-in"
                        }
                    ]
                }
            }
        ],
        "prepend_icon": true,
        "title": "",
        "item_props": {}
    },
    "list3": {
        "props": {
            "class": null,
            "style": null
        },
        "items": [
            {
                "text": "Destinatii",
                "href": "",
                "mega_menu": true,
                "mod_template": "grid",
                "list": {
                    "children": [
                        {
                            "children": [
                                {
                                    "children": [
                                        {
                                            "children": [
                                                {
                                                    "mod_data": {
                                                        "title": "<b style=\"font-size:14px;\">Destinații populare</b>",
                                                        "items": [
                                                            {
                                                                "text": "Romania",
                                                                "href": "",
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Litoral",
                                                                            "menu": true,
                                                                            "list": {
                                                                                "title": "",
                                                                                "items": [
                                                                                    {
                                                                                        "text": "Cap Aurora",
                                                                                        "href": "/hoteluri-cap-aurora"
                                                                                    },
                                                                                    {
                                                                                        "text": "Costinesti",
                                                                                        "href": "/hoteluri-costinesti"
                                                                                    },
                                                                                    {
                                                                                        "text": "Eforie Nord",
                                                                                        "href": "/hoteluri-eforie-nord"
                                                                                    },
                                                                                    {
                                                                                        "text": "Eforie Sud",
                                                                                        "href": "/hoteluri-eforie-sud"
                                                                                    },
                                                                                    {
                                                                                        "text": "Jupiter",
                                                                                        "href": "/hoteluri-jupiter"
                                                                                    },
                                                                                    {
                                                                                        "text": "Mamaia",
                                                                                        "href": "/hoteluri-mamaia"
                                                                                    },
                                                                                    {
                                                                                        "text": "Mamaia Nord",
                                                                                        "href": "/hoteluri-mamaia-nord"
                                                                                    },
                                                                                    {
                                                                                        "text": "Mangalia",
                                                                                        "href": "/hoteluri-mangalia"
                                                                                    },
                                                                                    {
                                                                                        "text": "Neptun",
                                                                                        "href": "/hoteluri-neptun"
                                                                                    },
                                                                                    {
                                                                                        "text": "Olimp",
                                                                                        "href": "/hoteluri-olimp"
                                                                                    },
                                                                                    {
                                                                                        "text": "Saturn",
                                                                                        "href": "/hoteluri-saturn"
                                                                                    },
                                                                                    {
                                                                                        "text": "Navodari",
                                                                                        "href": "/hoteluri-navodari"
                                                                                    },
                                                                                    {
                                                                                        "text": "Techirghiol",
                                                                                        "href": "/hoteluri-techirghiol"
                                                                                    },
                                                                                    {
                                                                                        "text": "Sulina",
                                                                                        "href": "/hoteluri-sulina"
                                                                                    },
                                                                                    {
                                                                                        "text": "Venus",
                                                                                        "href": "/hoteluri-venus"
                                                                                    },
                                                                                    {
                                                                                        "text": "2 Mai",
                                                                                        "href": "/hoteluri-2-mai"
                                                                                    },
                                                                                    {
                                                                                        "text": "Vama Veche",
                                                                                        "href": "/hoteluri-vama-veche"
                                                                                    }
                                                                                ]
                                                                            }
                                                                        },
                                                                        {
                                                                            "text": "Munte",
                                                                            "list": {
                                                                                "items": [
                                                                                    {
                                                                                        "text": "Azuga",
                                                                                        "href": "/hoteluri-azuga"
                                                                                    },
                                                                                    {
                                                                                        "text": "Busteni",
                                                                                        "href": "/busteni"
                                                                                    },
                                                                                    {
                                                                                        "href": "/hoteluri-cristian",
                                                                                        "text": "Cristian"
                                                                                    },
                                                                                    {
                                                                                        "text": "Bran - Moeciu",
                                                                                        "href": "/hoteluri-bran-moeciu"
                                                                                    },
                                                                                    {
                                                                                        "href": "/hoteluri-durau",
                                                                                        "text": "Durau"
                                                                                    },
                                                                                    {
                                                                                        "href": "/hoteluri-fundata",
                                                                                        "text": "Fundata"
                                                                                    },
                                                                                    {
                                                                                        "text": "Gura Humorului",
                                                                                        "href": "/hoteluri-gura-humorului"
                                                                                    },
                                                                                    {
                                                                                        "text": "Poiana Brasov",
                                                                                        "href": "/hoteluri-poiana-brasov"
                                                                                    },
                                                                                    {
                                                                                        "text": "Predeal",
                                                                                        "href": "/hoteluri-predeal"
                                                                                    },
                                                                                    {
                                                                                        "text": "Sighetul Marmatiei",
                                                                                        "href": "/hoteluri-sighetul-marmatiei"
                                                                                    },
                                                                                    {
                                                                                        "text": "Sinaia",
                                                                                        "href": "/hoteluri-sinaia"
                                                                                    },
                                                                                    {
                                                                                        "text": "Vatra Dornei",
                                                                                        "href": "/hoteluri-vatra-dornei"
                                                                                    }
                                                                                ]
                                                                            },
                                                                            "menu": true
                                                                        },
                                                                        {
                                                                            "text": "Spa & Wellness / Balneo",
                                                                            "menu": true,
                                                                            "list": {
                                                                                "items": [
                                                                                    {
                                                                                        "text": "Baile Felix",
                                                                                        "href": "/hoteluri-baile-felix"
                                                                                    },
                                                                                    {
                                                                                        "text": "Baile Govora",
                                                                                        "href": "/hoteluri-baile-govora"
                                                                                    },
                                                                                    {
                                                                                        "text": "Baile Herculane",
                                                                                        "href": "/hoteluri-baile-herculane"
                                                                                    },
                                                                                    {
                                                                                        "text": "Baile Olanesti",
                                                                                        "href": "/hoteluri-baile-olanesti"
                                                                                    },
                                                                                    {
                                                                                        "text": "Baile Tusnad",
                                                                                        "href": "/hoteluri-baile-tusnad"
                                                                                    },
                                                                                    {
                                                                                        "text": "Borsec",
                                                                                        "href": "/hoteluri-borsec"
                                                                                    },
                                                                                    {
                                                                                        "text": "Calimanesti Caciulata",
                                                                                        "href": "/hoteluri-calimanesti-caciulata"
                                                                                    },
                                                                                    {
                                                                                        "text": "Covasna",
                                                                                        "href": "/hoteluri-covasna"
                                                                                    },
                                                                                    {
                                                                                        "text": "Sangeorgiu de Mures",
                                                                                        "href": "/hoteluri-sangeorgiu-de-mures"
                                                                                    },
                                                                                    {
                                                                                        "href": "/hoteluri-balvanyos",
                                                                                        "text": "Balvanyos"
                                                                                    },
                                                                                    {
                                                                                        "text": "Slanic Moldova",
                                                                                        "href": "/hoteluri-slanic-moldova"
                                                                                    },
                                                                                    {
                                                                                        "text": "Praid",
                                                                                        "href": "/hoteluri-praid"
                                                                                    }
                                                                                ]
                                                                            }
                                                                        },
                                                                        {
                                                                            "text": "Delta",
                                                                            "href": ""
                                                                        }
                                                                    ],
                                                                    "title": "",
                                                                    "props": {
                                                                        "style": ""
                                                                    },
                                                                    "item_props": {
                                                                        "style": ""
                                                                    }
                                                                },
                                                                "menu": true
                                                            },
                                                            {
                                                                "text": "Bulgaria",
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Albena",
                                                                            "href": "/trip/hotelsasync?city_name=ALBENA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Balchik",
                                                                            "href": "/trip/hotelsasync?city_name=BALCHIK&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Duni",
                                                                            "href": "/trip/hotelsasync?city_name=DUNI&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Sunny Beach",
                                                                            "href": "/trip/hotelsasync?city_name=SUNNY%20BEACH&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Nisipurile de aur",
                                                                            "href": "/trip/hotelsasync?city_name=GOLDEN%20SANDS%20(VARNA)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Obzor",
                                                                            "href": "/trip/hotelsasync?city_name=OBZOR&sdate=moday&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=SUNNY%20DAY&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Sunny Day"
                                                                        },
                                                                        {
                                                                            "text": "Varna",
                                                                            "href": "/trip/hotelsasync?city_name=VARNA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ],
                                                                    "props": {},
                                                                    "item_props": {
                                                                        "style": "line-hight: 10px;"
                                                                    }
                                                                },
                                                                "menu": true
                                                            },
                                                            {
                                                                "text": "Albania",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Borsh",
                                                                            "href": "/trip/hotelsasync?city_name=BORSH&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Dhermi",
                                                                            "href": "/trip/hotelsasync?city_name=DHERMI&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Durres",
                                                                            "href": "/trip/hotelsasync?city_name=DURRES&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=KSAMIL&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Ksamil"
                                                                        },
                                                                        {
                                                                            "text": "Himare - plaja Gjipe",
                                                                            "href": "/trip/hotelsasync?city_name=HIMARE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=HIMARE&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Himare - Porto Palermo"
                                                                        },
                                                                        {
                                                                            "text": "Saranda",
                                                                            "href": "/trip/hotelsasync?city_name=SARANDE&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "icon": null,
                                                                            "menu": null
                                                                        },
                                                                        {
                                                                            "text": "Shkoder",
                                                                            "href": "/trip/hotelsasync?city_name=SHKODER&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                },
                                                                "href": ""
                                                            },
                                                            {
                                                                "text": "Austria",
                                                                "menu": true,
                                                                "list": {
                                                                    "title": "",
                                                                    "items": [
                                                                        {
                                                                            "text": "Innsbruck",
                                                                            "href": "/trip/hotelsasync?city_name=INNSBRUCK&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Salzburg",
                                                                            "href": "/trip/hotelsasync?city_name=SALZBURG&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Tirol",
                                                                            "href": "/trip/hotelsasync?city_name=TIROL%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Zell Am See",
                                                                            "href": "/trip/hotelsasync?city_name=ZELL%20AM%20SEE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Viena",
                                                                            "href": "/trip/hotelsasync?city_name=VIENNA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                }
                                                            },
                                                            {
                                                                "text": "Croatia",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Dubrovnik",
                                                                            "href": "/trip/hotelsasync?city_name=DUBROVNIK&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=KRK%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Krk"
                                                                        },
                                                                        {
                                                                            "text": "Insula Rab",
                                                                            "href": "/trip/hotelsasync?city_name=RAB%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=MAKARSKA&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Makarska"
                                                                        },
                                                                        {
                                                                            "text": "Split",
                                                                            "href": "/trip/hotelsasync?city_name=SPLIT&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Rijeka",
                                                                            "href": "/trip/hotelsasync?city_name=RIJEKA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Zadar",
                                                                            "href": "/trip/hotelsasync?city_name=ZADAR&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                },
                                                                "href": ""
                                                            },
                                                            {
                                                                "text": "Franta",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Dinan - Bretania",
                                                                            "href": "/trip/hotelsasync?city_name=DINAN&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Coasta de azur",
                                                                            "href": "/trip/hotelsasync?city_name=COTE%20D'OR%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Provence",
                                                                            "href": "/trip/hotelsasync?city_name=PROVENCE%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Calais",
                                                                            "href": "/trip/hotelsasync?city_name=CALAIS&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Cannes",
                                                                            "href": "/trip/hotelsasync?city_name=CANNES&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Nisa",
                                                                            "href": "/trip/hotelsasync?city_name=NICE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Marsilia",
                                                                            "href": "/trip/hotelsasync?city_name=MARSEILLE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Normandia",
                                                                            "href": "/trip/hotelsasync?city_name=NORMANDY%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Paris",
                                                                            "href": "/trip/hotelsasync?city_name=PARIS&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Saint Tropez",
                                                                            "href": "/trip/hotelsasync?city_name=SAINT-TROPEZ&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Strasbourg",
                                                                            "href": "/trip/hotelsasync?city_name=STRASBOURG&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                }
                                                            },
                                                            {
                                                                "text": "Grecia",
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Atena",
                                                                            "href": "/trip/hotelsasync?city_name=ATHENS&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Creta",
                                                                            "href": "/trip/hotelsasync?city_name=CRETE%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Corfu",
                                                                            "href": "/trip/hotelsasync?city_name=CORFU%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=CHALKIDIKI%20REGION&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Halkidiki"
                                                                        },
                                                                        {
                                                                            "text": "Lefkada",
                                                                            "href": "/trip/hotelsasync?city_name=LEFKADA%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Kos",
                                                                            "href": "/trip/hotelsasync?city_name=KOS%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Mykonos",
                                                                            "href": "/trip/hotelsasync?city_name=MYKONOS%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Rodos",
                                                                            "href": "/trip/hotelsasync?city_name=RHODES%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=THESSALONIKI&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Salonic"
                                                                        },
                                                                        {
                                                                            "text": "Santorini",
                                                                            "href": "/trip/hotelsasync?city_name=SANTORINI%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Thassos",
                                                                            "href": "/trip/hotelsasync?city_name=THASSOS&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Zakynthos",
                                                                            "href": "/trip/hotelsasync?city_name=ZAKYNTHOS%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                },
                                                                "menu": true
                                                            },
                                                            {
                                                                "text": "Italia",
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Bologna",
                                                                            "href": "/trip/hotelsasync?city_name=BOLOGNA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Coasta Amalfi",
                                                                            "href": "/trip/hotelsasync?city_name=AMALFI%20COAST%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Florenta",
                                                                            "href": "/trip/hotelsasync?city_name=FLORENCE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Genova",
                                                                            "href": "/trip/hotelsasync?city_name=GENOA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Milano",
                                                                            "href": "/trip/hotelsasync?city_name=MILAN&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "icon": null,
                                                                            "menu": null
                                                                        },
                                                                        {
                                                                            "text": "Napoli",
                                                                            "href": "/trip/hotelsasync?city_name=NAPLES&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Parma",
                                                                            "href": "/trip/hotelsasync?city_name=PARMA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Pisa",
                                                                            "href": "/trip/hotelsasync?city_name=PISA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Puglia",
                                                                            "href": "/trip/hotelsasync?city_name=PUGLIA%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Roma",
                                                                            "href": "/trip/hotelsasync?city_name=ROME&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Rimini",
                                                                            "href": "/trip/hotelsasync?city_name=RIMINI&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Sardinia",
                                                                            "href": "/trip/hotelsasync?city_name=SARDINIA%20ISLAND&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Sicilia",
                                                                            "href": "/trip/hotelsasync?city_name=SCILLA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Toscana",
                                                                            "href": "/trip/hotelsasync?city_name=TUSCANY%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Cinque Terre",
                                                                            "href": "/trip/hotelsasync?city_name=CINQUE%20TERRE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Venetia",
                                                                            "href": "/trip/hotelsasync?city_name=VENICE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Verona",
                                                                            "href": "/trip/hotelsasync?city_name=VERONA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                },
                                                                "menu": true
                                                            },
                                                            {
                                                                "text": "Muntenegru",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=BUDVA&sdate=monday&edate=5days&o[0][ADT]=2&n=1",
                                                                            "text": "Budva"
                                                                        },
                                                                        {
                                                                            "text": "Becici",
                                                                            "href": "/trip/hotelsasync?city_name=BECICI&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Herceg Novi",
                                                                            "href": "/trip/hotelsasync?city_name=HERCEG%20NOVI&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Kotor",
                                                                            "href": "/trip/hotelsasync?city_name=KOTOR&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Perast",
                                                                            "href": "/trip/hotelsasync?city_name=PERAST&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Petrovac",
                                                                            "href": "/trip/hotelsasync?city_name=PETROVAC&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Potgorica",
                                                                            "href": "/trip/hotelsasync?city_name=PODGORICA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Skadar",
                                                                            "href": "/trip/hotelsasync?city_name=SHKODER&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Sutomore",
                                                                            "href": "/trip/hotelsasync?city_name=SUTOMORE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Sveti Stefan",
                                                                            "href": "/trip/hotelsasync?city_name=SVETI%20STEFAN&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Ulcinj",
                                                                            "href": "/trip/hotelsasync?city_name=ULCINJ&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                }
                                                            },
                                                            {
                                                                "text": "Portugalia",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Lisabona",
                                                                            "href": "/trip/hotelsasync?city_name=LISBON&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Almada",
                                                                            "href": "/trip/hotelsasync?city_name=ALMADA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Algarve",
                                                                            "href": "/trip/hotelsasync?city_name=ALGARVE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Braga",
                                                                            "href": "/trip/hotelsasync?city_name=BRAGA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Coimbra",
                                                                            "href": "/trip/hotelsasync?city_name=COIMBRA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Faro",
                                                                            "href": "/trip/hotelsasync?city_name=FARO%20(ALGARVE)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Figueira da Foz",
                                                                            "href": "/trip/hotelsasync?city_name=FIGUEIRA%20DA%20FOZ&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Madeira",
                                                                            "href": "/trip/hotelsasync?city_name=MADEIRA%20ISLAND%20(VARIOUS%20LOCATIONS)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Nazare",
                                                                            "href": "/trip/hotelsasync?city_name=NAZARE&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Porto",
                                                                            "href": "/trip/hotelsasync?city_name=PORTO&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                },
                                                                "href": ""
                                                            },
                                                            {
                                                                "text": "Spania",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Barcelona",
                                                                            "href": "/trip/hotelsasync?city_name=BARCELONA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Costa Brava",
                                                                            "href": "/trip/hotelsasync?city_name=COSTA%20BRAVA%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Costa del Sol",
                                                                            "href": "/trip/hotelsasync?city_name=COSTA%20DEL%20SOL%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Gran Canaria",
                                                                            "href": "/trip/hotelsasync?city_name=GRAN%20CANARIA%20ISLAND%20(CANARY%20ISLANDS)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Madrid",
                                                                            "href": "/trip/hotelsasync?city_name=MADRID&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Mallorca",
                                                                            "href": "/trip/hotelsasync?city_name=MALLORCA%20ISLAND%20(BALEARIC%20ISLANDS)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Marbella",
                                                                            "href": "/trip/hotelsasync?city_name=MARBELLA%20(COSTA%20DEL%20SOL)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Santander",
                                                                            "href": "/trip/hotelsasync?city_name=SANTANDER%20(SURROUNDINGS)&&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "San Sebastian",
                                                                            "href": "/trip/hotelsasync?city_name=SAN%20SEBASTIAN&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Tenerife",
                                                                            "href": "/trip/hotelsasync?city_name=TENERIFE%20ISLAND%20(CANARY%20ISLANDS)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Valencia",
                                                                            "href": "/trip/hotelsasync?city_name=VALENCIA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                }
                                                            },
                                                            {
                                                                "text": "Turcia",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Ankara",
                                                                            "href": "/trip/hotelsasync?city_name=ANKARA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Antalya",
                                                                            "href": "/trip/hotelsasync?city_name=ANTALYA%20REGION&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Bodrum",
                                                                            "href": "/trip/hotelsasync?city_name=BODRUM&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Cappadocia",
                                                                            "href": "/trip/hotelsasync?city_name=CAPPADOCIA%20(REGION)&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Didim",
                                                                            "href": "/trip/hotelsasync?city_name=DIDIM&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Kusadasi",
                                                                            "href": "/trip/hotelsasync?city_name=KUSADASI&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Istanbul",
                                                                            "href": "/trip/hotelsasync?city_name=ISTANBUL&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Izmir",
                                                                            "href": "/trip/hotelsasync?city_name=IZMIR&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Marmaris",
                                                                            "href": "/trip/hotelsasync?city_name=MARMARIS&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Trabzon",
                                                                            "href": "/trip/hotelsasync?city_name=TRABZON&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                }
                                                            },
                                                            {
                                                                "text": "Egipt",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Hurghada",
                                                                            "href": "/trip/hotelsasync?city_name=HURGHADA&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Sharm el Sheikh",
                                                                            "href": "/trip/hotelsasync?city_name=SHARM%20EL%20SHEIKH&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Cairo",
                                                                            "href": "/trip/hotelsasync?city_name=CAIRO&sdate=monday&edate=5days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                }
                                                            }
                                                        ],
                                                        "props": {
                                                            "class": "mega-menu-custom-1",
                                                            "style": ""
                                                        },
                                                        "item_props": {
                                                            "style": "",
                                                            "class": "mega-menu-custom-1-item"
                                                        }
                                                    }
                                                }
                                            ]
                                        },
                                        {
                                            "children": [
                                                {
                                                    "mod_data": {
                                                        "title": "<b style=\"font-size:14px;\">Exotice si Circuite</b>",
                                                        "items": [
                                                            {
                                                                "text": "Destinatii exotice",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Maldive",
                                                                            "href": "/trip/hotelsasync?city_name=MALDIVES-MV&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Seychelles",
                                                                            "href": "/trip/hotelsasync?city_name=SEYCHELLES%20(VARIOUS%20LOCATIONS)-SC&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Bora Bora",
                                                                            "href": "/trip/hotelsasync?city_name=BORA%20BORA%20(ALL%20LOCATIONS)-PF&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Marrakech",
                                                                            "href": "/trip/hotelsasync?city_name=MARRAKECH-MA&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Bhutan",
                                                                            "href": "/trip/hotelsasync?city_name=BUMTHANG-BT&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Zanzibar",
                                                                            "href": "/trip/hotelsasync?city_name=ZANZIBAR%20ISLAND-TZ&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "href": "/trip/hotelsasync?city_name=GALAPAGOS%20ISLAND-EC&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1",
                                                                            "text": "Insulele Galapagos"
                                                                        },
                                                                        {
                                                                            "text": "Borneo",
                                                                            "href": "/trip/hotelsasync?city_name=BORNEO%20ISLAND-ID&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        },
                                                                        {
                                                                            "text": "Kyoto",
                                                                            "href": "/trip/hotelsasync?city_name=KYOTO-JP&sdate=monday+1weeks&edate=7days&o[0][ADT]=2&n=1"
                                                                        }
                                                                    ]
                                                                },
                                                                "href": ""
                                                            },
                                                            {
                                                                "text": "Circuite Europa",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "Bulgaria",
                                                                            "href": "/circuite-bulgaria"
                                                                        },
                                                                        {
                                                                            "text": "Croatia",
                                                                            "href": "/circuite-croatia"
                                                                        },
                                                                        {
                                                                            "text": "Italia",
                                                                            "href": "/circuite-italia"
                                                                        },
                                                                        {
                                                                            "text": "Elvetia",
                                                                            "href": "/circuite-elvetia"
                                                                        },
                                                                        {
                                                                            "text": "Austria",
                                                                            "href": "/circuite-austria"
                                                                        },
                                                                        {
                                                                            "text": "Cehia",
                                                                            "href": "/circuite-cehia"
                                                                        },
                                                                        {
                                                                            "text": "Grecia",
                                                                            "href": "/circuite-grecia"
                                                                        },
                                                                        {
                                                                            "text": "Ungaria",
                                                                            "href": "/circuite-ungaria"
                                                                        },
                                                                        {
                                                                            "text": "Franta",
                                                                            "href": "/circuite-franta"
                                                                        }
                                                                    ]
                                                                }
                                                            },
                                                            {
                                                                "text": "Circuite exotice",
                                                                "href": "",
                                                                "menu": true,
                                                                "list": {
                                                                    "items": [
                                                                        {
                                                                            "text": "China",
                                                                            "href": "/circuite-china"
                                                                        },
                                                                        {
                                                                            "text": "Japonia",
                                                                            "href": "/circuite-japonia"
                                                                        },
                                                                        {
                                                                            "text": "Thailanda",
                                                                            "href": "/circuite-thailanda"
                                                                        },
                                                                        {
                                                                            "text": "Vietnam",
                                                                            "href": "/circuite-vietnam"
                                                                        },
                                                                        {
                                                                            "text": "Brazilia",
                                                                            "href": "/circuite-brazilia"
                                                                        },
                                                                        {
                                                                            "text": "India",
                                                                            "href": "/circuite-india"
                                                                        },
                                                                        {
                                                                            "text": "Sri Lanka",
                                                                            "href": "/circuite-sri-lanka"
                                                                        },
                                                                        {
                                                                            "text": "Costa Rica",
                                                                            "href": "/circuite-costa-rica"
                                                                        },
                                                                        {
                                                                            "text": "Columbia",
                                                                            "href": "/circuite-columbia"
                                                                        },
                                                                        {
                                                                            "text": "Coreea de Sud",
                                                                            "href": "/circuite-coreea-de-sud"
                                                                        },
                                                                        {
                                                                            "text": "Argentina",
                                                                            "href": "/circuite-argentina"
                                                                        },
                                                                        {
                                                                            "text": "Malaezia",
                                                                            "href": "/circuite-malaezia"
                                                                        },
                                                                        {
                                                                            "text": "Australia",
                                                                            "href": "/circuite-australia"
                                                                        },
                                                                        {
                                                                            "text": "Nepal",
                                                                            "href": "/circuite-nepal"
                                                                        },
                                                                        {
                                                                            "text": "Mongolia",
                                                                            "href": "/circuite-mongolia"
                                                                        },
                                                                        {
                                                                            "text": "Cuba",
                                                                            "href": "/circuite-cuba"
                                                                        }
                                                                    ]
                                                                },
                                                                "icon": ""
                                                            }
                                                        ],
                                                        "props": {
                                                            "style": "",
                                                            "class": "mega-menu-custom-1"
                                                        },
                                                        "item_props": {
                                                            "style": "",
                                                            "class": "mega-menu-custom-1-item"
                                                        }
                                                    }
                                                }
                                            ]
                                        },
                                        {
                                            "children": [
                                                {
                                                    "mod_data": {
                                                        "title": "<b style=\"font-size:14px;\">Escapade de weekend!</b>",
                                                        "items": [
                                                            {
                                                                "text": "City Break Praga",
                                                                "href": "/city-break-praga"
                                                            },
                                                            {
                                                                "text": "City Break Paris",
                                                                "href": "/city-break-paris"
                                                            },
                                                            {
                                                                "text": "City Break Londra",
                                                                "href": "/city-break-londra"
                                                            },
                                                            {
                                                                "text": "City Break Roma",
                                                                "href": "/city-break-roma"
                                                            },
                                                            {
                                                                "text": "City Break Zagreb",
                                                                "href": "/city-break-zagreb"
                                                            },
                                                            {
                                                                "href": "/city-break-bologna",
                                                                "text": "City Break Bologna"
                                                            },
                                                            {
                                                                "href": "/city-break-amsterdam",
                                                                "text": "City Break Amsterdam"
                                                            },
                                                            {
                                                                "text": "City Break Viena",
                                                                "href": "/city-break-viena"
                                                            },
                                                            {
                                                                "text": "City Break Berlin",
                                                                "href": "/city-break-berlin"
                                                            },
                                                            {
                                                                "text": "City Break Milano",
                                                                "href": "/city-break-milano"
                                                            },
                                                            {
                                                                "text": "City Break Lisabona",
                                                                "href": "/city-break-lisabona"
                                                            },
                                                            {
                                                                "text": "City Break Varsovia",
                                                                "href": "/city-break-varsovia"
                                                            },
                                                            {
                                                                "text": "City Break Atena",
                                                                "href": "/city-break-atena"
                                                            }
                                                        ],
                                                        "props": {
                                                            "style": "",
                                                            "class": "mega-menu-custom-1"
                                                        },
                                                        "item_props": {
                                                            "style": "",
                                                            "class": "mega-menu-custom-1-item"
                                                        }
                                                    }
                                                }
                                            ]
                                        },
                                        {
                                            "children": [
                                                {
                                                    "mod_data": {
                                                        "title": "<b style=\"font-size:14px;\">Revelion 2026!</b>",
                                                        "items": [
                                                            {
                                                                "text": "Romania"
                                                            },
                                                            {
                                                                "text": "Austria"
                                                            },
                                                            {
                                                                "text": "Ungaria"
                                                            }
                                                        ],
                                                        "props": {
                                                            "class": "mega-menu-custom-1"
                                                        }
                                                    }
                                                }
                                            ]
                                        }
                                    ],
                                    "props": {
                                        "class": "flex-wrap",
                                        "noGutters": true
                                    }
                                }
                            ],
                            "default_mod_template": "vertical-list",
                            "status": true,
                            "default_mod_data": {
                                "title": "VList Module title",
                                "items": [
                                    {
                                        "text": "Demo list item",
                                        "href": "//google.ro"
                                    }
                                ],
                                "props": {
                                    "style": "min-width: 200px;"
                                },
                                "item_props": {}
                            },
                            "props": {
                                "class": "pa-0"
                            }
                        }
                    ]
                }
            },
            {
                "text": "Hoteluri",
                "href": "/hoteluri"
            },
            {
                "text": "Avion",
                "href": "/bilete-avion"
            },
            {
                "text": "Vacante",
                "href": "/vacante",
                "list": {
                    "title": "",
                    "items": []
                },
                "mega_menu": true
            },
            {
                "text": "Circuite",
                "href": "/circuite"
            },
            {
                "text": "City Breaks",
                "mega_menu": false,
                "mod_template": null,
                "list": {
                    "children": []
                },
                "href": "/oferte-city-break"
            },
            {
                "text": "Spa & Balneo",
                "href": "/oferte-spa-balneo"
            },
            {
                "text": "Litoral",
                "href": "/oferte-litoral-romania"
            },
            {
                "text": "Munte",
                "href": "/oferte-munte"
            }
        ],
        "title": "",
        "prepend_icon": null,
        "item_props": {}
    }
}	}),
}