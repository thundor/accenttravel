import ExtendTemplate from './templates/searcher.js?newux=1';
export default {
	extends: ExtendTemplate,
	name: '<?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>',
	data: () => ({
		name: '<?php echo basename(__FILE__,'.js.php'); ?>',
		data: {
    "items": [
        {
            "image": {
                "props": {
                    "src": "/resources/images/Tema/turcia.jpg",
                    "aspectRatio": "16/9",
                    "cover": true
                },
                "a_props": {
                    "href": "antalya-vacante-charter"
                }
            },
            "title": "Tot ce are Turcia mai spectaculos",
            "html": "<div>\n<h2 style=\"text-align: center;\"><span style=\"font-size:40px;\">Tot ce are Turcia mai spectaculos!</span></h2>\n\n<p style=\"text-align: center;\"><span style=\"font-size:18px;\">• plaje de vis • savori yummy • hoteluri de senzatie •</span></p>\n\n<p style=\"text-align: center;\"><a href=\"/travelfuse/chartere?destination=Antalya-TR&amp;origin=Bucuresti-RO/\" class=\"btn btn-primary btn-hero\">Oferte Antalya</a></p>\n</div>\n<style type=\"text/css\">a.btn.btn-primary.btn-hero {\n    border-color: #ffffff !important;\nbackground-color: transparent !important;\n}\n</style>\n"
        },
        {
            "image": {
                "a_props": {},
                "props": {
                    "src": "/resources/images/Hero_oferte_munte.png",
                    "cover": true
                }
            },
            "title": "Hai la munte",
            "html": "<div class=\"text-white\">\n<h2 style=\"text-align: center;\"><span style=\"font-size:40px;\">Hai la munte!</span></h2>\n\n<p style=\"text-align: center;\"><span style=\"font-size:18px;\">Toamna e mai fun la inaltime!</span></p>\n\n<p style=\"text-align: center;\"><a href=\"/oferte-munte\" class=\"btn btn-primary btn-hero\">Hoteluri la Munte</a></p>\n</div>\n<style type=\"text/css\">a.btn.btn-primary.btn-hero {\n    border-color: #ffffff !important;\nbackground-color: transparent !important;\n}\n</style>\n",
            "transition": "fade-transition"
        },
        {
            "title": "Grecia este destinatia",
            "html": "<div class=\"text-white\">\n<h2 style=\"text-align: center;\"><span style=\"font-size:40px;\">Grecia e destinatia</span></h2>\n\n<p style=\"text-align: center;\"><span style=\"font-size:18px;\">Nu uita palaria, ochelarii de soare si starea de bine!</span></p>\n\n<p style=\"text-align: center;\"><a href=\"/hoteluri-halkidiki-macedonia?step=1\" class=\"btn btn-primary btn-hero\">Oferte Halkidiki</a></p>\n</div>\n<style type=\"text/css\">a.btn.btn-primary.btn-hero {\n    border-color: #ffffff !important;\nbackground-color: transparent !important;\n}\n</style>\n",
            "image": {
                "props": {
                    "src": "/resources/images/Tema/barca_plutind.jpg",
                    "cover": true,
                    "aspectRatio": "16:9"
                },
                "a_props": {}
            },
            "status": true,
            "transition": "fade-transition"
        },
        {
            "image": {
                "a_props": {
                    "href": "https://accenttravel.ro/bilete-avion"
                },
                "props": {
                    "src": "/resources/images/Tema/KLM_PREMIUM_Comfort_Iunie_2026.png"
                }
            },
            "html": "<div class=\"text-white\">\n\n\n\n\n<p style=\"text-align: center;\"><a href=\"/bilete-avion\" class=\"btn btn-primary btn-hero\">Cauta zbor</a></p>\n</div>\n<style type=\"text/css\">a.btn.btn-primary.btn-hero {\n    border-color: #ffffff !important;\nbackground-color: transparent !important;\n}\n</style>"
        }
    ],
    "bg_props": {
        "class": null,
        "style": null
    },
    "bg_item_props": {
        "class": null,
        "style": null
    },
    "props": {
        "interval": null,
        "class": null,
        "style": null
    },
    "item_props": {
        "class": null,
        "style": null
    }
}	}),
}