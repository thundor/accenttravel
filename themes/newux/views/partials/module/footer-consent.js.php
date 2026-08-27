import ExtendTemplate from './templates/grid.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
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
                                                                            "html": "<p>Agentia ACCENT TRAVEL & EVENTS respecta Regulamentului (UE) 2016/679 privind protectia persoanelor fizice in ceea ce priveste prelucrarea datelor cu caracter personal si privind libera circulatie a acestor date si de abrogare a Directivei 95/46/CE.(Regulamentul general privind protectia datelor). Pentru mai multe detalii, click aici. Site-ul accenttravel.ro foloseste cookies pentru a optimiza continutul in permanenta. Prin continuarea navigarii acceptati implicit Politica de Cookie-uri Accent Travel & Events</p>",
                                                                            "props": {}
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
                                                                            "code": "<v-btn class=\"mt-2\" variant=\"outlined\" @click=\"custom.consent_show = false\">Sunt de acord</v-btn>"
                                                                        }
                                                                    }
                                                                ],
                                                                "props": {
                                                                    "class": "flex-0-0-0"
                                                                }
                                                            }
                                                        ],
                                                        "props": {
                                                            "class": "flex-column flex-md-row"
                                                        }
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                }
                            ]
                        }
                    ]
                }
            ],
            "status": false,
            "text": "Footer consent",
            "props": {
                "fluid": true,
                "style": "background-color: #02A0FF;\ncolor: #fff"
            }
        }
    ]
}	}),
}