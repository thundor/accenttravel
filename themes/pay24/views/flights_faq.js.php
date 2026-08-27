export default {
  emits: ['save'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      single: {
          type: Boolean,
          default: false,
      },
  },
	data: () => ({
    kept: undefined,
    saved: {},
    date2: {},
    ddate: {},
    dialog: false,
    errors: [],
    items: [
    {
        "title": "Rezervare si plata",
        "children": [
            {
                "title": "Greșeală în prenumele sau numele de pe bilet",
                "text": "<p>Prenumele si numele de familie trebuie sa fie exact ca in documentul de identitate pe care il vei folosi pentru check-in si cu care te vei identifica la aeroport. Daca observi ca ai gresit completarea prenumelui sau/si numelui, contacteaza serviciul Suport Clienti.</p>"
            },
            {
                "title": "Transmiterea biletului electronic",
                "text": "<p>E-mail-ul de confirmare a biletului este generat automat. După ce plata (prin card de credit ) a fost finalizată, pe adresa de e-mail furnizată este trimis biletul electronic. Daca nu ai primit biletul va rugam sa contactezi Suport Clienti.</p>"
            },
            {
                "title": "Modificare in itinerariu de zbor",
                "text": "<p>Daca biletul este platit si confirmat vei putea sa schimbi itinerariul doar dupa ce vei plati taxa de modificare + diferenta de tarif intre zborul confirmat si cel nou. Pentru astfel de situatii contacteaza serviciul Suport Clienti.</p>"
            },
            {
                "title": "Pot modifica datele de pe factura?",
                "text": "<p>Datele de pe factura nu se pot modifica. Daca te confrunti cu o asemenea situatie, te rugam sa contactezi serviciul Suport Clienti</p>"
            }
        ]
    },
    {
        "title": "Modificari anulari",
        "children": [
            {
                "title": "Zborul meu a fost anulat",
                "text": "<p>Informatiile referitoare la modificarea orarului de zbor si/sau anularea zborului vor fi transmise sub forma unui sms pe numarul de telefon mobil mentionat in rezervare si pe adresa de email cu care te-ai logat in aplicatie. Todata in cazul companiilor aeriene de linie, aceste informatii vor fi receptionate in aplicatia de rezervari si emiteri bilete de avion detinuta de Accent Travel & Events, care la randul sau le va transmite corect si complet pe adresa de email inserata in rezervare. De aceea va rugam sa introduceti cu maxima atentie numarul de telefon si adresa de email corecta, cu scopul de a fi informat in timp real asupra oricaror modificari sau anulari ce pot surveni din initiativa companiei aeriene, pana la data inceperii calatoriei. Recomandari suplimentare: vizitati  periodic site-ul web al companiei aeriene anteror datei de incepere a calatoriei pentru a vedea statusul zborului rezervat. In cazul in care compania aeriana a anulat zborul din propria initiativa ai dreptul de a alege între o nouă dată a zborului, un voucher valoric sub forma de puncte pe care il poti utiliza in orice alt zbor operat de compania aeriana pe o perioada determinata de timp sau rambursarea biletului, conform politicilor proprii ale fiecarei companii aeriene.</p>"
            },
            {
                "title": "Rambursare pentru un zbor anulat",
                "text": "<p>În conformitate cu practica adoptată de companiile aeriene, rambursarea unui zbor anulat include costul biletului și al serviciilor suplimentare achiziționate direct de la compania aeriană. Tipul rambursării depinde în totalitate de compania aeriană.</p><p>Pentru rambursarea contravalorii biletelor de avion emise de compania low cost RYANAIR va rog sa urmati instructiunile din sectiunea “6. Conditii speciale pentru operatiunile postbooking (modificare, rambursare in cazul anularii zborului din initiativa companiei aeriene) in cazul biletelor emise pentru tranportul aerian operat de compania Ryanair” din formularul Termeni si Conditii existent in platforma.</p>"
            },
            {
                "title": "Suma rambursării este mai mică decât valoarea totală a rezervării.",
                "text": "<p>Valoarea rambursarii depinde de serviciile aditionale achiziționate ȋmpreună cu biletul și de decizia luata de compania aeriană cu privire la suma rambursată. Aceasta valoare nu include, de exemplu, taxa pentru serviciu și serviciile suplimentare achiziționate de pe 24pay.</p>"
            },
            {
                "title": "Doresc să anulez sau să reprogramez zborul",
                "text": "<p>Dacă doresti sa modifici datele calatoriei sau să anulezi zborul, contacteaza direct Serviciul Clienti. Conditiile de anulare si modificare depind de politica fiecarei companii aeriane si de conditiile de emitere ale biletului de avion.</p><p>Majoritatea companiilor aeriene permit anularea călătoriei doar in conditii de penalizari ce pot varia intre 50% si 100% din valoarea biletului. Modificarea datelor de zbor se poate realiza doar prin intermediul companiei aeriene emitente si doar conform termenilor agreati de aceasta; intotdeauna pentru modificarea datelor de zbor se percepe o taxa de modificare plus diferenta de tarif, intre valoarea biletului pentru noul zbor si valoarea biletului initial.</p>"
            }
        ]
    },
    {
        "title": "Bagaje",
        "children": [
            {
                "title": "Ce tip de bagaj am inclus?",
                "text": "<p>Numarul si dimensiunea bagajelor depinde de optiunea exercitata la momentul rezervarii respectiv cu/fara bagaj de cala. Tariful pentru biletul rezervat cu bagaj de cala este intodeauna superior. Bagajul de cala poate fi achizitionat la momentului emiterii biletului sau poate fi adaugat ulterior contracost.</p>"
            },
            {
                "title": "Ce dimensiuni și ce greutate trebuie să aibă bagajul de cală?",
                "text": "<p>Bagajul de cală trebuie să respecte cu strictețe dimensiunile și greutatea impusă de către operatorul de zbor. Recomandam verificarea prealabila a informatiilor publicate pe site-ul companiei aeriene cu care urmeaza sa calatoresti.</p>"
            },
            {
                "title": "Ce dimensiuni și ce greutate trebuie să aibă bagajul de cabina?",
                "text": "<p>Bagajul de cabina trebuie să respecte cu strictețe dimensiunile și greutatea impusă de către operatorul de zbor. Aproape toate companiile aeriene impun aceeași politică în ceea ce privește bagajul de cabină. Dimensiunea standard pentru bagajul de mână este de 40x30x20cm. Recomandam verificarea prealabila a informatiilor publicate pe site-ul companiei aeriene cu care urmeaza sa calatoresti.</p>"
            },
            {
                "title": "Ce se întâmplă dacă bagajul de cabina depașește dimensiunile impuse de catre operatorul de zbor?",
                "text": "<p>În cazul în care bagajul de cabina depășește regulile impuse de compania aeriana, la momentul îmbarcării, va fi considerat bagaj de cala pentru care urmeaza sa platesti o taxă aferentă surplusului.</p>"
            },
            {
                "title": "Pierderea bagajelor in timpul transportului aerian din motive neimputabile pasagerului",
                "text": "<p>In cazul in care bagajul de cala a fost pierdut din motive ce nu tin de culpa pasagerului, contacteaza de urgență compania aeriană cu care ai calatorit. De regulă, fiecare aeroport are un serviciul specializat in evidenta si recuperarea bagajelor pierdute in timpul transportului aerian.</p><p>Acest departament gestioneaza doar bagajele pierdute dupa predarea acestora in custodia companiei aeriene in vederea imbarcarii, debarcarii sau pentru tranzit. In cazul in care bagajul a fost piedut in asemenea conditii, depune imediat o reclamatie care va contine datele tale de identificare, ale zborului și ale bagajului înregistrat, precum și datele necesare identificării acestuia (tipul geamantanului, culoare, dimensiuni)</p>"
            },
            {
                "title": "Ce nu ai voie să trasporti în bagajul de cabina (obiecte interzise)",
                "text": "<p>Enumerea este cu titlu de exemplu, nu este limitativa - aparate si lame de ras; cutite, foarfece, briceag; seringi, ace; tirbuson; unelte (topor, funie, cabluri, ciocan, bare metalice, unelte universale, cutit cu deschidere automata, masina de gaurit, surubelnita); explozibili si substante inflamabile; alcool si lichide care nu au fost cumparate din duty free; substante toxice si chimice; echipamente sportive si de camping: skateboard, sulita, arc cu sageti, crose de golf sau de hochei, schiuri, patine cu rotile, lansete, placa de surf, echipament de snowboard sau de schi; recipiente ce contin lichide cu o capacitate mai mari de 100 ml</p>"
            },
            {
                "title": "Cum pot adauga bagaje suplimentare la un bilet cumparat?",
                "text": "<p>Opțiunile posibile sunt in functie de fiecare companie aeriana.  Bagajele se pot cumpara de pe site-ul companiei aeriene sau apeland Serviciul Suport Clienti;</p>"
            },
            {
                "title": "Zbor cu escală",
                "text": "<p>In cazul zborurilor cu mai multe segmente, bagajele sunt de regulă tranzitate sde serviciul de handling al aeroportului, fără ca pasagerul să fie nevoit să se ocupe de acest aspect.</p>"
            },
            {
                "title": "Zboruri combinate",
                "text": "<p>Pentru zborurile combinate, bagajul de cala nu se transfera automat, el trebuie ridicat de catre pasager si inregistrat din nou la ghiseul companiei aeriene pentru urmatorul zbor de conexiune.</p>"
            }
        ]
    },
    {
        "title": "Conditii de calatorie",
        "children": [
            {
                "title": "Care sunt conditiile de calatorie in destinatia mea?",
                "text": "<p>Avand in vedere ca in contextul actual, conditiile de calatorie au fost actualizate de fiecare stat in parte, este necesar ca inainte de a finaliza o rezervare si inainte de a efectua calatoria sa verifici daca indeplinesti conditiile pentru a intra pe teritoriul tarii de destinatie. Informatii oficiale le poti gasi pe site-ul MAE, dand click pe tara de destinatie sau pe orice alt site official al tarii de destinatie: https://www.mae.ro/travel-conditions</p>"
            },
            {
                "title": "Pentru ce țări este necesară viza turistică?",
                "text": "<p>Lista țărilor pentru care viza este necesară o gasesti pe site-ul MAE. Te rugam sa retii că timpul de așteptare pentru viză variază de la țară la țară și poate dura de la câteva zile la câteva săptămâni. Pentru a fi nu avea probleme, obtine mai întâi viza turistică și apoi achizitioneaza biletul.</p>"
            }
        ]
    },
    {
        "title": "Situatii speciale",
        "children": [
            {
                "title": "Călătoria cu avionul pe timpul sarcinii",
                "text": "<p>Pasagerele însărcinate pot călători cu avionul până în săptămâna a 28-a de sarcină. După ce sarcina fără complicaţii ajunge în săptămâna a 28-a, se solicita pasagerelor însărcinate să prezinte o scrisoare „aptă pentru zbor” din partea medicului</p>"
            },
            {
                "title": "Conditii de calatorie infanti si copii",
                "text": "<p>Infantii, copii cu varsta cuprinsa intre 0 si 1.99 ani calatoresc gratuit dar nu beneficieaza de loc separat in aeronava. Adulții care însoțesc un infant trebuie să achite o taxă fixă ce variază de la un operator la altul. Pentru copii de depaseste 1.99 ani trebuie achizitionat bilet de avion separat in baza caruia beneficieaza de un loc de sine statator in aeronava.</p>"
            },
            {
                "title": "Cum poate calatori un minor cu avionul?",
                "text": "<p>Potrivit legislatiei romane (legea nr. 248 din 2005 referitoare la libera circulatie a cetatenilor romani in strainatate), cetatenii romani sub 18 ani pot calatori in strainatate numai insotiti de catre ambii parinti sau de catre un singur parinte/adult, si avand asupra lor documentele necesare (carte de identitate sau pasaport, declaratie notariala din partea parintelui ce nu va efectua calatoria, precum si cazierul judiciar la adultului insotitor, in cazul in care acesta nu este unul dintre parinti). Pentru mai multe detalii consultati site-ul www.politiadefrontiera.ro</p>"
            },
            {
                "title": "Cărucioare și echipamente pentru copii",
                "text": "<p>În cazul în care calatoresti cu un bebeluș, de obicei poti lua cu tine un cărucior și anumite accesori de transport pentru copii. Regulile exacte de transport depind de fiecare operator aerian. Verifica condițiile și limitele exacte la operatorul aerian in prealabil. Vei găsi dimensiunile căruciorului și detalii despre accesoriile permise  pentru copii pe site-ul fiecarei companii aeriene.</p>"
            },
            {
                "title": "Călătoria cu animalul de companie",
                "text": "<p>Verificați normele detaliate aplicabile atunci când călătoriți cu animalul de companie. Transportul unui animal de companie trebuie precizat inca din momentul in care iti rezervi biletul de avion, astfel va rugam sa ne contactati inainte de a efectua rezervarea.</p><p>Unele companii low cost nu permit transportul animalelor la bord, cu exceptia celor cu rol de ghizi pentru persoane cu dizabilitati.</p>"
            }
        ]
    },
    {
        "title": "Check in online",
        "children": [
            {
                "title": "Ce inseamna check in online",
                "text": "<p>Check-in online înseamnă că vei putea genera cartea de îmbarcare direct online, înainte de ziua zborului. Bajajele de cala trebuie predate in mod obligatoriu la biroul check in dedicat din aeroport.</p>"
            },
            {
                "title": "Cu cat timp inainte se face check in onli",
                "text": "<p>Check-in-ul online se poate realiza incepand cu maxim 24 ore pana la 6 ore inainte de zbor, in functie de regulile companiilor aeriene.</p>"
            },
            {
                "title": "Cum se face check-in online la RyanAir",
                "text": "<p>Pentru check-in online trebuie sa creezi un cont individual pe adresa de web www.ryanair.com avand nevoie de introducerea dateleor referitoare la nume, prenume si adresa personala de email.</p><p>In etapa a doua, accesati platforma Ryan Air disponibila de desktop sau mobil, secțiunea „rezervarile mele/numar rezervare”. Pentru check in online, introduceti adresa de email depticketing@accenttravel.ro si numar-ul de rezervare pe care il regasiti inscris in biletul de avion. Dupa ce platforma confirma “Am gasit rezervarea ta”, continui logarea prin adaugarea adresei personale de email si parola aferenta (parola este cea setata la momentul crearii contului). Dupa accesul in platforma se urmeaza succesiv toate etapele efectuarii check in-ului online pana la eliberarea cartilor de imbacare.</p>"
            },
            {
                "title": "Eroare check-in online",
                "text": "<p>Daca a apărut o eroare în timp ce efectuezi check-in-ul online, reia cautarea in cateva minute si daca eroarea persista, contacteaza serviciul Suport Clienti.</p>"
            },
            {
                "title": "Check-in online pentru zboruri combinate",
                "text": "<p>Pentru zborurile combinate, check-in-ul online trebuie efectuat separat, pentru fiecare segment de zbor, din itinerariul tau.</p>"
            },
            {
                "title": "Cum se poate efectua check in online",
                "text": "<p>Acceseaza site-ul companiei aeriene pe stocul careia s-a emis biletul de avion, selecteaza opțiunea \"Check-in online\" și acceseaza rezervarea ta utilizând numărul de rezervare sau numărul biletului de avion și numele pasagerului. Este recomandat sa printezi cartile de imbarcare  si sa te prezinti cu acestea in aeroport.</p>"
            },
            {
                "title": "Check-in pentru companiile low cost",
                "text": "<p>In cazul companiilor low cost check-in-ul online este obligatoriu. In caz contrar, companiile aeriene percep o taxa suplimentara pentru formalitatile de check-in la aeroport.</p>"
            },
            {
                "title": "Cu cat timp inainte trebuie sa fiu la aeroport",
                "text": "<p>De regulă, companiile aeriene recomandă sosirea la aeroport cu 2-3 ore înainte de ora zborului.</p>"
            },
            {
                "title": "Care este codul rezervării a companiei aeriene și numărul biletului electronic",
                "text": "<p>Aceste informații pot fi găsite în itinerariu. Exemplu de cod rezervarea a companiei aeriene- 17FTYO si al unui număr de bilet electronic: 123-4546790212.</p>"
            }
        ]
    },
    {
        "title": "Status zbor",
        "children": [
            {
                "title": "Status zbor",
                "text": "<p>Cea mai corecta si actualizata informatie este direct la operatorul de zbor. Majoritatea companiilor aeriene au o sectiune speciala \"Status zbor\" unde poti sa verifici statutul rutei tale dupa itinerariu sau numar zbor.</p>"
            }
        ]
    },
    {
        "title": "Metode de plata",
        "children": [
            {
                "title": "Metode de plata",
                "text": `<p>Pentru platile efectuate cu cardul online, ai optiunea de a alege ce moneda de tranzactionare doresti sa fie folosita, EUR sau RON.</p>
				<p>Platile in RON se efectueaza la cursul BNR din ziua platii la care se adauga un comision de risc valutar de 3%.</p>
				<p>Recomandam sa alegi ca moneda de tranzactionare moneda in care este emis cardul cu care doresti sa faci plata.</p>
				<p>Exemple:</p>
				<ol class="pl-4">
					<li>Daca ai un card de EUR, este indicat sa alegi ca moneda de tranzactionare tot EUR. Valoarea ce urmeaza a fi achitata este valoarea mentionata in confirmarea rezervarii.  Daca ai alege RON, atunci banca emitenta a cardului va calcula suma in RON pornind de la cursul de schimb al bancii.</li>
					<li>Daca ai un card de RON, este indicat sa alegi ca moneda de tranzactionare tot RON. In acest caz, conversia se face pornind de la valoarea in EUR afisata in aplicatia 24Pay inmultit cu cursul BNR din ziua platii + 3% comision de risc valutar.Daca totusi doresti sa alegi EUR si ai un card de RON, atunci banca emitenta a cardului va face conversia la cursul sau de schimb care, de regula, este mai mare decat BNR+ 3%.</li>
				</ol>
				`
            }
        ]
    }
],
    texts: Object.freeze({
      no_end_date: "Alegeti data de intoarcere",
    }),
    validations:Object.freeze([
      function(){ return !this.single && !this.saved.days ? 'no_end_date' : null },
    ]),
  }),
	template : `<Modal v-model="dialog" title="Intrebari frecvente">
	<template v-slot:activator="{ props }">
      <v-icon v-bind="props" icon="mdi-frequently-asked-questions"></v-icon>
	</template>
	<div class="fill-height max-height overflow-y-auto">
	  
		<?php /*<a style="color:transparent;" href="https://accenttravel.ro/pay24/test_order_details/617"><v-icon icon="mdi-frequently-asked-questions"></v-icon></a> */ ?>
	<template v-for="(group, groupIndex) in items">
		<v-list-item-title class="pa-4 pb-2 text-h5 text-primary" v-text="group.title"></v-list-item-title>
        <v-expansion-panels>
		  <v-expansion-panel
			v-for="(item, itemIndex) in group.children"
			:key="groupIndex + '-' + itemIndex"
		  >
			<v-expansion-panel-title>
				{{item.title}}
			  <template v-slot:actions="{ expanded }">
				<v-icon color="primary" :icon="expanded ? 'mdi-chevron-up-box' : 'mdi-chevron-down'"></v-icon>
			  </template>
			</v-expansion-panel-title>
			<v-expansion-panel-text><div class="v-list-item-subtitle" v-html="item.text"></div></v-expansion-panel-text>
		  </v-expansion-panel>
		</v-expansion-panels>
      </template>
	  <button type="button" style="float:right;width:50%;color:red; background-color:blue;height:25px;opacity:0;clear:both;" onclick="this.nextElementSibling.style.display='';"></button>
	  <pre style="display:none;" v-html="pay24Account"></pre>
	</div>
	<template v-slot:footer="{ props }">
		<v-btn class="d-flex text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
	</template>
  </Modal>
	`,

  methods: {
    changed2(a){
      console.log('changed2', a);
      this.ddate = a;
      return true;
    },
    changed(a){
      console.log('changed', a);
      this.ddate = a;
      return true;
    },
    clearValidations(){
      this.errors = [];
    },
    save(){
      this.kept = Object.assign({}, this.saved);
      this.$emit('save', this.saved);
      return true;
      // emit
    },
    validate(){
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
        v && this.errors.push(this.texts[v]);
        return !v;
      })
      return !this.errors.length;
    }
  },
  computed: {
    selectedDatesAreValid:{
      get() { return this.ddate ? (this.single || this.ddate.length==2 && this.ddate[1] && this.ddate[1] > this.ddate[0]) : false; },
    },
    button_text:{
      get() { 
        if(this.kept && this.kept.date){
          return dateIntervalFormatted(this.kept.date, !this.single && this.kept.days)
        }
        return !this.single ? 'Plecare - Sosire' : 'Plecare';
      },
    },
    button_text2:{
      get() { 
        if(this.saved){
          var txts = [];

          if(this.saved.date){
            /*
            txts.push(this.saved.date.toLocaleDateString('ro', {
              month: "short",
              day: "numeric"
            }));
            */
            txts.push('<b style="line-height: 0.7">' + dateIntervalFormatted(this.saved.date, !this.single && this.saved.days) + '</b>')
          }
          /*
          if(!this.single && this.saved.days){
            txts.push('+ ' + this.saved.days);
          }
          */
          if(txts.length) {
            txts.unshift('Alege')
            return txts.join(' ');
          }
        }
        return !this.single ? 'Plecare - Sosire' : 'Plecare';
      },
    },
    year_range:{
      get() { 
        var currentYear = new Date().getFullYear();
        return [currentYear, currentYear+2]
      },
    },
    multicalendars:{
      get() { return (!this.single && (window.innerHeight > 650) || (window.innerWidth > 650)) },
    },
    date:{
      get() { var d; var r = !this.saved.date ? undefined : (this.single ? this.saved.date : [this.saved.date, (d = new Date(this.saved.date), d.setDate(d.getDate() + (this.saved.days||0)), d)]);  return r || this.date2;},
      set(newValue){console.warn('asdf2', newValue); this.single ? (this.saved.date = newValue || undefined,this.saved.days=0) : (!newValue || !newValue.length ? (this.saved.date = undefined,this.saved.days=0) : (this.saved.date = new Date(!newValue[1] ? newValue[0] : Math.min(newValue[0], newValue[1])), this.saved.days = !newValue[1] ? 0 : Math.floor((Math.max(newValue[0], newValue[1]) - this.saved.date) / 86400000)));},
    },
  },
	beforeCreate: function(){
		// console.warn('created', this);
	},
  watch:{
    'dialog': {
      handler(newValue, oldValue){
        if(!this.kept){
          this.saved = {}
        } else {
          this.saved = Object.assign({}, this.kept);
        }
        if(newValue && !oldValue){
          this.touchmoveY = 0;
        }
      },
    },
    'modelValue': {
      handler(newValue, oldValue){
        // console.warn('date', newValue);
        if(!newValue) return;
        var types = ['date','days'];
        for(var i in types){
          var type = types[i];
          this.saved[type] = newValue[type] || undefined;
        }
        this.validate() && this.save()
      }
    },
    'date': {
      handler(newValue, oldValue){
        console.warn('date', newValue);
      },
      immediate: true,
      deep: true,
    },
    /* 'single': {
      handler(newValue, oldValue){
        if(this.saved.date){
          if(newValue){
            this.saved.days = 0;
          } else {
            this.saved.days = 1;
          }
          this.save();
        }
      },
      deep: true
    },*/
    'saved': {
      handler(newValue, oldValue){
        this.clearValidations();
      },
      deep: true
    }
  }
}
