import ExtendTemplate from './templates/grid.js?newux=1';
export default {
	extends: ExtendTemplate,
	name: '<?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>',
	data: () => ({
		name: '<?php echo basename(__FILE__,'.js.php'); ?>',
		data: {
    "children": [
        {
            "children": [
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "text": "",
                                    "mod_template": "section-title",
                                    "mod_data": {
                                        "title": "<h2 style=\"font-weight: bold;font-size: 28px !important;color: #010832;line-height: 1.2 !important; margin-bottom:20px\">Ce fel de vacanta ti-ar fi pe plac? </h2>",
                                        "html": "<span><img src=\"/resources/images/Tema/imagine_vacanta.png\" class=\"gif-vacanta\" alt=\"\"></span>\n<style type=\"text/css\">img.gif-vacanta {\npadding: 20px 80px; \nwidth: 532px; height: \n122px;\n}\n@media screen and (max-width: 786px) {\nimg.gif-vacanta {\n        padding: 0 10px;\n        height: auto;\n        width: max-content !important\n}\n}\n</style>\n"
                                    }
                                }
                            ],
                            "props": {
                                "class": "pt-10 pb-10"
                            }
                        }
                    ]
                }
            ],
            "status": true,
            "default_mod_template": "card-image-horizontal",
            "default_mod_data": null,
            "carousel": false,
            "props": {
                "style": "",
                "class": "pt-10"
            }
        },
        {
            "name": "vacante-pe-placul-tau"
        },
        {
            "name": "euroweekend"
        },
        {
            "children": [
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "section-title",
                                    "mod_data": {
                                        "title": "Bilete de avion",
                                        "html": "<p>cele mai tentante oferte de zboruri in acest weekend</p>\n"
                                    }
                                }
                            ],
                            "props": {
                                "class": "pt-10"
                            }
                        }
                    ]
                },
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "destinatii-avion"
                                },
                                {}
                            ]
                        }
                    ]
                },
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "html",
                                    "text": "text + buton",
                                    "mod_data": {
                                        "html": "<div style=\"display: inline-flex; gap:60px;align-items: center;\" class=\"flex-wrap\">\n<div style=\"display: inline-flex; gap:10px;\"><span style=\"font-size:24px;color:#02A0FF;\" class=\"fa fa-star\">‌</span>\n<p style=\"font-size:18px; font-weight:500;\">CAUTI O ALTA DESTINATIE?</p>\n</div>\n\n<p><a href=\"/bilete-avion\" class=\"btn btn-primary bg-primary text-white rounded-lg\">Vezi ofertele</a></p>\n</div>\n\n<p>&nbsp;</p>\n",
                                        "props": {}
                                    }
                                }
                            ],
                            "props": {
                                "style": ""
                            }
                        }
                    ]
                }
            ],
            "text": "Bilete de avion",
            "status": true,
            "props": {
                "class": "pt-10 pb-10"
            }
        },
        {
            "children": [
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_data": {
                                        "name": "vacante-chartere-grid"
                                    },
                                    "mod_template": "grid"
                                }
                            ]
                        }
                    ]
                }
            ],
            "default_mod_template": "card-image-vertical",
            "default_mod_data": {
                "image": {
                    "props": {
                        "src": "/resources/images/balvanyos.jpg",
                        "height": "250",
                        "cover": true,
                        "aspectRatio": "16/9",
                        "rounded": "lg"
                    }
                },
                "props": {
                    "tag": "a",
                    "href": "https://accenttravel.ro/hoteluri",
                    "class": "text-decoration-none mx-auto",
                    "maxWidth": "400",
                    "rounded": "lg",
                    "minWidth": "250"
                },
                "title": "Turcia - Antalya",
                "subtitle": "",
                "button_title": "Vezi ofertele",
                "header_title": ""
            },
            "status": true,
            "props": {
                "fluid": true,
                "style": "",
                "class": "evadeaza-in-stil"
            },
            "text": "Evadeaza in stil"
        },
        {
            "children": [
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "grid",
                                    "mod_data": {
                                        "title": "VList Module title",
                                        "children": [
                                            {
                                                "children": [
                                                    {
                                                        "children": [
                                                            {
                                                                "children": [
                                                                    {
                                                                        "mod_template": "grid",
                                                                        "mod_data": {
                                                                            "title": "VList Module title",
                                                                            "children": [
                                                                                {
                                                                                    "text": "Demo section",
                                                                                    "status": 1,
                                                                                    "children": [
                                                                                        {
                                                                                            "children": [
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_template": "html",
                                                                                                            "mod_data": {
                                                                                                                "html": "<h2>Daruieste o vacanta!</h2>\n\n<div class=\"w-lg-75 w-100\">\n<p>Un cadou pe care îl poți dărui celor dragi, cu orice ocazie, în orice moment. Travel Gift Card este un produs Accent Travel &amp; Events și &nbsp;poate fi achizițioanat din rețeaua de Centre Comerciale Carrefour din toată țara sau îl poți comanda aici:</p>\n\n<div style=\"gap:10px;\" class=\"d-inline-flex align-center pt-5 mt-5\"><a style=\"background-color: #02A0FF; padding:13px 30px; border:1px solid #02A0FF\" href=\"/gift-card\" class=\"btn text-white rounded-lg butoane-daruieste\">Cumpara Gift Card</a> <a style=\"background-color: transparent; color:#000 !important; padding:13px 30px; border:1px solid #02A0FF\" href=\"/giftcard\" class=\"btn text-white rounded-lg butoane-daruieste\">Afla mai mult</a></div>\n</div>\n",
                                                                                                                "props": {
                                                                                                                    "class": "d-flex flex-column pa-10 pb-0 pl-0 pr-0 flex-fill  justify-content-center"
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    ]
                                                                                                }
                                                                                            ]
                                                                                        }
                                                                                    ]
                                                                                },
                                                                                {
                                                                                    "children": [
                                                                                        {
                                                                                            "children": [
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_data": {
                                                                                                                "props": {
                                                                                                                    "class": "pb-0"
                                                                                                                },
                                                                                                                "html": "<p><strong><span style=\"color:#3498db;\">Parteneri:</span></strong></p>\n"
                                                                                                            },
                                                                                                            "mod_template": "html"
                                                                                                        }
                                                                                                    ]
                                                                                                }
                                                                                            ],
                                                                                            "status": true
                                                                                        },
                                                                                        {
                                                                                            "children": [
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_data": {
                                                                                                                "props": {
                                                                                                                    "src": "/resources/images/epay_a_euronet_worldwide_company_seeklogo.png",
                                                                                                                    "aspectRatio": "16/9",
                                                                                                                    "cover": true
                                                                                                                },
                                                                                                                "a_props": {}
                                                                                                            }
                                                                                                        }
                                                                                                    ]
                                                                                                },
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_data": {
                                                                                                                "props": {
                                                                                                                    "src": "/resources/images/logo_xeSIM_01.png",
                                                                                                                    "aspectRatio": "16/9",
                                                                                                                    "cover": true
                                                                                                                },
                                                                                                                "a_props": {}
                                                                                                            }
                                                                                                        }
                                                                                                    ]
                                                                                                },
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_data": {
                                                                                                                "props": {
                                                                                                                    "src": "/resources/images/logo_Accent_DMC.png",
                                                                                                                    "aspectRatio": "16/9",
                                                                                                                    "cover": true
                                                                                                                },
                                                                                                                "a_props": {}
                                                                                                            }
                                                                                                        }
                                                                                                    ]
                                                                                                }
                                                                                            ],
                                                                                            "props": {
                                                                                                "class": "w-50 mt-0 pt-0"
                                                                                            }
                                                                                        }
                                                                                    ],
                                                                                    "default_mod_template": "image",
                                                                                    "default_mod_data": null,
                                                                                    "status": true,
                                                                                    "props": {
                                                                                        "class": ""
                                                                                    }
                                                                                }
                                                                            ]
                                                                        }
                                                                    }
                                                                ],
                                                                "props": {
                                                                    "class": "v-col-12 v-col-md-6 d-flex flex-column euro-weekend-g-r-col1"
                                                                }
                                                            },
                                                            {
                                                                "children": [
                                                                    {
                                                                        "mod_template": "image",
                                                                        "mod_data": {
                                                                            "props": {
                                                                                "src": "/resources/images/Tema/daruieste_o_vacanta.png",
                                                                                "aspectRatio": "16/9",
                                                                                "cover": true,
                                                                                "width": "600"
                                                                            },
                                                                            "a_props": {}
                                                                        }
                                                                    }
                                                                ],
                                                                "props": {
                                                                    "class": " d-flex justify-center align-center"
                                                                }
                                                            }
                                                        ],
                                                        "props": {
                                                            "class": "euro-weekend-g-r"
                                                        }
                                                    }
                                                ],
                                                "status": true,
                                                "props": {
                                                    "class": "px-md-16 euro-weekend-g"
                                                }
                                            }
                                        ]
                                    }
                                }
                            ]
                        }
                    ]
                }
            ],
            "default_mod_template": "card-image-vertical",
            "default_mod_data": {
                "image": {
                    "props": {
                        "src": "/resources/images/balvanyos.jpg",
                        "height": "250",
                        "cover": true,
                        "aspectRatio": "16/9",
                        "rounded": "lg"
                    }
                },
                "props": {
                    "tag": "a",
                    "href": "https://accenttravel.ro/hoteluri",
                    "class": "text-decoration-none mx-auto",
                    "maxWidth": "400",
                    "rounded": "lg",
                    "minWidth": "250"
                },
                "title": "Turcia - Antalya",
                "subtitle": "",
                "button_title": "Vezi ofertele",
                "header_title": ""
            },
            "status": true,
            "props": {
                "fluid": true,
                "style": "background: transparent linear-gradient(117deg, #FFE0E9 0%, #D8F0FF 100%) 0% 0% no-repeat padding-box;\nmargin-bottom:30px;",
                "class": "euro-weekend"
            },
            "text": "Euro weekend"
        },
        {
            "children": [
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "section-title",
                                    "mod_data": {
                                        "title": "Hai la munte!",
                                        "html": "<p>Alege un hotel la munte si bucura-te de miscare si natura</p>\n"
                                    }
                                }
                            ],
                            "props": {
                                "class": "pb-17"
                            }
                        }
                    ]
                },
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "status": true,
                                    "mod_template": "destinatii-hotel"
                                }
                            ]
                        }
                    ],
                    "props": {
                        "class": "",
                        "style": "padding-bottom:30px;"
                    }
                },
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "html",
                                    "mod_data": {
                                        "html": "<div style=\"display: inline-flex; gap:10px;\">\n<div style=\"display:flex; gap:10px;\"><span style=\"font-size:24px;color:#02A0FF;\" class=\"fa fa-star\">‌</span></div>\n\n<div>\n<p style=\"font-size:18px; font-weight:500; margin:0;\">VREI CAZARE INTR-O ALTA STATIUNE MONTANĂ?</p>\n\n<p>Rezerva acum camera de hotel din statiunea preferata din Romania si bucura-te de miscare si natura!</p>\n</div>\n</div>\n",
                                        "props": {}
                                    }
                                }
                            ]
                        }
                    ]
                },
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "grid",
                                    "status": true,
                                    "mod_data": {
                                        "title": "VList Module title",
                                        "children": [
                                            {
                                                "text": "Demo section",
                                                "status": 1,
                                                "children": [
                                                    {
                                                        "children": [
                                                            {
                                                                "children": [
                                                                    {
                                                                        "status": true,
                                                                        "mod_template": "grid",
                                                                        "mod_data": {
                                                                            "props": {
                                                                                "src": "/resources/images/Tema/oferte_de_iarna.png",
                                                                                "aspectRatio": "16/9",
                                                                                "cover": true
                                                                            },
                                                                            "children": [
                                                                                {
                                                                                    "children": [
                                                                                        {
                                                                                            "children": [
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_template": "html",
                                                                                                            "mod_data": {
                                                                                                                "html": "<h2 style=\"font-size:16px; margin-bottom:0;\">Oferte cazare la munte in Romania</h2>\n",
                                                                                                                "props": {
                                                                                                                    "class": "pa-0 mb-0"
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    ]
                                                                                                }
                                                                                            ]
                                                                                        }
                                                                                    ],
                                                                                    "status": true,
                                                                                    "props": {
                                                                                        "style": "background: #02A0FF 0% 0% no-repeat padding-box;\nborder-radius: 10px 10px 0px 0px;\ncolor:#ffffff;",
                                                                                        "class": "pt-0 pb-0"
                                                                                    }
                                                                                },
                                                                                {
                                                                                    "children": [
                                                                                        {
                                                                                            "children": [
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_template": "vertical-list",
                                                                                                            "mod_data": {
                                                                                                                "props": {
                                                                                                                    "class": "bg-transparent"
                                                                                                                },
                                                                                                                "items": [
                                                                                                                    {
                                                                                                                        "text": "Azuga",
                                                                                                                        "href": "/hoteluri-azuga"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Busteni",
                                                                                                                        "href": "/hoteluri-busteni"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Bran-Moeciu",
                                                                                                                        "href": "/hoteluri-bran-moeciu"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Brasov",
                                                                                                                        "href": "/hoteluri-brasov"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Campulung Moldovenesc",
                                                                                                                        "href": "/hoteluri-campulung-moldovenesc"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Paltinis",
                                                                                                                        "href": "/hoteluri-paltinis"
                                                                                                                    }
                                                                                                                ],
                                                                                                                "prepend_icon": true,
                                                                                                                "item_props": {
                                                                                                                    "class": ""
                                                                                                                },
                                                                                                                "title": ""
                                                                                                            }
                                                                                                        }
                                                                                                    ]
                                                                                                },
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_template": "vertical-list",
                                                                                                            "mod_data": {
                                                                                                                "title": "",
                                                                                                                "items": [
                                                                                                                    {
                                                                                                                        "text": "Poiana Brasov",
                                                                                                                        "href": "/hoteluri-poiana-brasov"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Predeal",
                                                                                                                        "href": "/hoteluri-predeal"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Stana de Vale",
                                                                                                                        "href": "/hoteluri-stana-de-vale"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Sinaia",
                                                                                                                        "href": "/hoteluri-sinaia"
                                                                                                                    },
                                                                                                                    {
                                                                                                                        "text": "Vatra Dornei",
                                                                                                                        "href": "/hoteluri-vatra-dornei"
                                                                                                                    }
                                                                                                                ],
                                                                                                                "props": {
                                                                                                                    "class": "bg-transparent"
                                                                                                                },
                                                                                                                "prepend_icon": true,
                                                                                                                "item_props": {
                                                                                                                    "class": ""
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    ]
                                                                                                }
                                                                                            ],
                                                                                            "props": {
                                                                                                "class": "links-oferte"
                                                                                            }
                                                                                        }
                                                                                    ],
                                                                                    "status": true,
                                                                                    "props": {
                                                                                        "class": "pa-10"
                                                                                    }
                                                                                },
                                                                                {
                                                                                    "children": [
                                                                                        {
                                                                                            "children": [
                                                                                                {
                                                                                                    "children": [
                                                                                                        {
                                                                                                            "mod_template": "button",
                                                                                                            "status": true
                                                                                                        }
                                                                                                    ]
                                                                                                }
                                                                                            ]
                                                                                        }
                                                                                    ]
                                                                                }
                                                                            ]
                                                                        }
                                                                    }
                                                                ],
                                                                "props": {
                                                                    "style": "box-shadow: 0px 3px 6px #00000029;\nborder-radius: 10px;\nmargin-top: 15px;\nmargin-bottom: 15px;",
                                                                    "class": "pr-0 pl-0"
                                                                }
                                                            },
                                                            {
                                                                "children": [
                                                                    {
                                                                        "mod_template": "image",
                                                                        "mod_data": {
                                                                            "props": {
                                                                                "src": "https://accenttravel.ro/resources/images/brasov_munte.png",
                                                                                "aspectRatio": "16/9",
                                                                                "cover": true,
                                                                                "rounded": "lg"
                                                                            },
                                                                            "a_props": {}
                                                                        }
                                                                    }
                                                                ],
                                                                "props": {
                                                                    "class": ""
                                                                }
                                                            }
                                                        ]
                                                    }
                                                ],
                                                "props": {
                                                    "class": "liste-links"
                                                }
                                            }
                                        ]
                                    }
                                }
                            ]
                        }
                    ]
                }
            ],
            "text": "Hai la munte",
            "status": true
        }
    ]
}	}),
}