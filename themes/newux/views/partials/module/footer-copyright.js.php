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
                                    "mod_template": "grid",
                                    "mod_data": {
                                        "title": "VList Module title",
                                        "children": [
                                            {
                                                "name": "footer-1"
                                            }
                                        ]
                                    }
                                }
                            ]
                        }
                    ]
                }
            ],
            "status": true,
            "props": {
                "fluid": true,
                "style": "border-top: 2px solid #02A0FF;\nmargin-top:60px;",
                "class": "py-0"
            }
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
                                                "name": "footer-2"
                                            }
                                        ]
                                    }
                                }
                            ]
                        }
                    ]
                }
            ],
            "status": true,
            "props": {
                "fluid": true,
                "style": "background-color:#F5F4F4;\nborder-top: 2px solid #FD9600;"
            }
        },
        {
            "children": [
                {
                    "children": [
                        {
                            "children": [
                                {
                                    "mod_template": "html",
                                    "mod_data": {
                                        "html": "<p>© 1999 - 2024 Accent Travel &amp; Events SRL. Toate drepturile rezervate.<br>\n<a href=\"http://www.anpc.gov.ro/\">ANPC Romania</a> | <a href=\"http://ec.europa.eu/consumers/odr/\">Solutionarea Litigiilor</a><br>\n<a href=\"/resources/licenta-accent-travel-xs.pdf\">Licenta turism: Nr. 354/18.12.2018</a> | <a href=\"/resources/files/I59317_ACCENT_TRAVEL_2025_sd.pdf semnat - Copy 1.pdf\">Polita de asigurare nr. 59317 (valabila pana la 16.11.2026)</a> | <a href=\"/resources/files/ISO_2024.pdf\">Certificat ISO 9001</a> | Pentru veridicitatea informatiilor click <a href=\"https://www.dnvgl.com/assurance/certificates-in-the-blockchain.html\">AICI</a>.</p>\n",
                                        "props": {
                                            "class": "licente-footer"
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            "children": [
                                {
                                    "mod_template": "code",
                                    "mod_data": {
                                        "props": {},
                                        "code": "<div class=\"d-flex justify-center justify-md-end ga-4 flex-wrap\">\n\t\t\t\t\t<v-img\n\t\t\t\t\t  :height=\"55\"\n\t\t\t\t\t  :width=\"55\"\n\t\t\t\t\t  contain\n\t\t\t\t\t  inline\n\t\t\t\t\t  src=\"https://accenttravel.ro/themes/newux/assets/images/mastercard-logo.png\"\n\t\t\t\t\t></v-img>\n\t\t\t\t\t<v-img\n\t\t\t\t\t  :height=\"55\"\n\t\t\t\t\t  :width=\"55\"\n\t\t\t\t\t  contain\n\t\t\t\t\t  inline\n\t\t\t\t\t  src=\"https://accenttravel.ro/themes/newux/assets/images/visa-logo.png\"\n\t\t\t\t\t></v-img>\n\t\t\t\t\t<v-img\n\t\t\t\t\t  :height=\"55\"\n\t\t\t\t\t  :width=\"55\"\n\t\t\t\t\t  contain\n\t\t\t\t\t  inline\n\t\t\t\t\t  src=\"https://accenttravel.ro/themes/newux/assets/images/paypal-logo.png\"\n\t\t\t\t\t></v-img>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"align-bottom d-flex justify-center justify-md-end ga-4 flex-wrap\">\n\t\t\t\t\t<v-img\n\t\t\t\t\t  :height=\"55\"\n\t\t\t\t\t  :width=\"83\"\n\t\t\t\t\t  contain\n\t\t\t\t\t  inline\n\t\t\t\t\t  src=\"https://accenttravel.ro/themes/newux/assets/images/anat-logo.png\"\n\t\t\t\t\t></v-img>\n\t\t\t\t\t<v-img\n\t\t\t\t\t  :height=\"55\"\n\t\t\t\t\t  :width=\"50\"\n\t\t\t\t\t  contain\n\t\t\t\t\t  inline\n\t\t\t\t\t  src=\"https://accenttravel.ro/themes/newux/assets/images/iata-logo.webp\"\n\t\t\t\t\t></v-img>\n\t\t\t\t\t<v-img\n\t\t\t\t\t  :height=\"55\"\n\t\t\t\t\t  :width=\"222\"\n\t\t\t\t\t  contain\n\t\t\t\t\t  inline\n\t\t\t\t\t  src=\"https://accenttravel.ro/themes/newux/assets/images/anpc-sol.png\"\n\t\t\t\t\t></v-img>\n\t\t\t\t\t<v-img\n\t\t\t\t\t  :height=\"55\"\n\t\t\t\t\t  :width=\"222\"\n\t\t\t\t\t  contain\n\t\t\t\t\t  inline\n\t\t\t\t\t  src=\"https://accenttravel.ro/themes/newux/assets/images/anpc-soal.png\"\n\t\t\t\t\t></v-img>\n\t\t\t\t</div>"
                                    }
                                }
                            ]
                        }
                    ],
                    "props": {
                        "class": "flex-column flex-md-row"
                    }
                }
            ],
            "status": true,
            "text": "Footer copyright"
        }
    ],
    "template": "vacante-chartere-grid"
}	}),
}