export default {
	emits: [],
	props: {
		modelValue: {
			type: Boolean,
			default: (false),
		},
	},
	data: () => ({
		isdisabled: false,
		terms: false,
		dialog: false,
		waited: false,
		waittime: '',
		termsRules: Object.freeze([
		  v => !!v && true === v || 'Bifa este necesara',
		]),

	}),
	template : `<Modal v-model="dialog" :allowClose="!isdisabled">
  <template v-slot:activator="{ props }">
	<v-form  ref="termscheck">
	<v-checkbox color='primary' density="compact" v-model="terms" :rules="termsRules" class="px-4">
		<template v-slot:label>
			<span class="ps-3 text-subtitle-2">Am citit și sunt de acord cu <span class="d-inline text-primary" v-bind="props">Condițiile</span> generale de rezervare bilete de avion</span>
		</template>
	</v-checkbox>
	</v-form>
  </template>
		<v-list subheader theme="light" class="ma-4 mt-0 max-height mb-4 pe-0" rounded="theme">
			<v-list-item-title class="pa-4 pb-2 text-h5 text-wrap">CONDITII  GENERALE DE REZERVARE – VÂNZARE A BILETELOR DE AVION</v-list-item-title>
      <div class="pa-4">
		<div>
<p><b><span>CONDITII
<span> </span>GENERALE DE REZERVARE – VÂNZARE A
BILETELOR DE AVION<br>
DE C&#258;TRE AGENTIA ACCENT TRAVEL &amp; EVENTS </span></b><span>
</span></p>
<p><span>Societatea
ACCENT TRAVEL &amp;EVENTSR SRL este o AGENTIE DE TURISM detinatoare a licentei
IATA nr 69210525 care are în cadrul activitatilor autorizate <span> </span>de CAEN-7911 Activitati ale agentiilor
turistice –comercializeaza bilete de avion pentru zboruri interne &#351;i
interna&#355;ionale, curse de linie sau low cost, in mod direct sau prin
intermediul agen&#539;iilor revânzatoare, cu care detine contracte.</span></p>
<p><span><br>
În scopul comercializarii acestui tip de serviciu din obiectul s&#259;u de
activitate, societatea opereaza ca intermediar intre companiile aeriene si
pasageri &#351;i se supune termenilor si conditiilor comerciale impuse de
c&#259;tre companiile aeriene, în calitate de prestatori direc&#355;i.</span></p>
<p><span><br>
În continuare, vom denumi pe scurt:</span></p>
<p><span><br>
Societatea <span> </span>Accent Travel &amp;Events AGEN&#354;IA/Agen&#539;ia
de turism Accent Travel &amp;Events</span></p>
<p><span><br>
Compania aerian&#259; denumita in cele ce urmeaza COMPANIA/Compania
Aerian&#259;/Companie transportatoare/Transportator<br>
Clientul care rezerv&#259; &#351;i/sau achizitioneaza biletul de avion, denumit
in continuare PASAGERUL<br>
Documentul în baza c&#259;ruia se efectueaz&#259; rezervare denumit BILETUL DE
AVION<br>
Agen&#539;ia revânzatoare-agentia care preia spre revanzare catre clientii propria,
servicii de transport aerian, denumit in cele ce urmeaza REVÂNZ&#258;TOR;</span></p>
<p><span>
Rezervarea – vânzarea biletelor de avion se realizeaza cu respectarea
urmatorilor termeni si condi&#355;ii:
<br>
</span></p>
<p><span>
</span><b><span>I. REZERV&#258;RI DE
LOCURI</span></b></p>
<p><span><span>1.1.<span> </span></span></span><span>Rezerv&#259;rile de bilete de avion pentru
zboruri interne si internationale se realizeaza cu ajutorul sistemelor
interna&#355;ionale computerizate de rezerv&#259;ri, si/sau ale Companiilor
Aeriene care permit rezervarea efectiv&#259; a locurilor pentru o anumit&#259;
curs&#259; în favoarea poten&#355;ialilor Pasageri.</span></p>
<p><span><span>1.2.<span> </span></span></span><span>Rezerv&#259;rile sunt gratuite &#351;i se fac
înaintea datei de plecare.</span></p>
<p><span><span>1.3.<span> </span></span></span><span>Odat&#259; cu efectuarea rezerv&#259;rii,
Pasagerul va primi de la Agen&#355;ie informa&#355;ii referitoare la
pre&#355;ul de vânzare al biletului (valabil la data rezerv&#259;rii incluzând
&#351;i taxele de aeroport, taxe combustibil, alte taxe similare, tarifele de
servicii ale Accent Travel &amp;Events), precum &#351;i informa&#355;iile
referitoare la condi&#355;ii, restric&#355;ii &#351;i reguli aplicabile
biletului de avion ce poate fi cump&#259;rat în baza respectivei
rezerv&#259;ri.</span></p>
<p><span>1.4.Toate
condi&#355;iile, regulile &#351;i restric&#355;iile aplicabile unui bilet de
avion sunt cele stabilite de Compania Aerian&#259;, de politica de tarife de
serviciu a Accent Travel &amp;Events, precum &#351;i de al&#355;i factori
externi (de ex. obliga&#355;ia de&#355;inerii vizei), independent de
voin&#355;a Agen&#355;iei.</span><span> Aceste reguli, precum
&#351;i pre&#355;ul biletului de avion, se pot modifica, prin voin&#355;a celor
ce le-au impus, pân&#259; în momentul cump&#259;r&#259;rii efective a
biletului, f&#259;r&#259; ca agen&#355;ia s&#259; poat&#259; fi
f&#259;cut&#259; în vreun fel raspunz&#259;toare.</span></p>
<p><b><span>
</span></b></p>
<p><b><span>
</span></b></p>
<p><b><span>II.
EMITEREA BILETELOR DE AVION. MODALIT&#258;&#354;I DE PLAT&#258;</span></b></p>
<p><span>2.1.
Emiterea biletelor de avion rezervate &#351;i plata acestora se efectueaza
conform condi&#355;iilor stabilite de c&#259;tre fiecare Companie <span> </span>aeriana. Agen&#539;ia informeaz&#259;
Pasagerul despre termenul pân&#259; la care rezervarea sa este valabil&#259;
pentru ca acesta s&#259; se poat&#259; prezenta în termen pentru achitarea biletului
si a intra în posesia acestuia. Termenele pot fi schimbate oricând de Companie,
situa&#355;ie în care Agen&#355;ia va anun&#355;a Pasagerul despre modificarea
survenita, f&#259;r&#259; a putea fi îns&#259; f&#259;cut&#259;
r&#259;spunzatoare în vreun fel de c&#259;tre acesta pentru o eventual&#259;
anulare.
2.2. </span><span>Raportul contractului între agen&#355;ie
&#351;i pasager apare în momentul în care pasagerul a achitat biletul de avion
&#351;i a intrat în posesia acestuia</span><span> inainte de a achita efectiv biletul, Pasagerul are
obliga&#355;ia s&#259; se informeze la Agen&#355;ie despre toate condi&#355;iile
&#351;i restric&#355;iile aplicabile acelui bilet &#351;i s&#259; citeasc&#259;
prezentele condi&#355;ii generale.</span><span> </span></p>
<p><span>2.3.
Pasagerul poate pl&#259;ti biletul de avion în LEI, USD, EUR, în func&#539;ie
de valuta în care a fost emis biletul de avion &#537;i/sau în care se
efectueaz&#259; plata c&#259;tre Companiile Aeriene.
</span></p>
<p><b><span>
</span></b></p>
<p><b><span>III.
PRE&#354;URILE BILETELOR DE AVION</span></b></p>
<p><span>3.1.
Pre&#355;urile biletelor de avion comercializate prin Agen&#355;ie sunt
rezultanta unui sistem complex de reguli tarifare, tarife contractuale, tarife
publicate în sistemele centralizate de rezerv&#259;ri, taxe ale Companiilor
Aeriene, tarife speciale, tarif de serviciu al Accent Travel &amp; Events, etc.
În aceste condi&#355;ii, fiecare zbor în parte presupune calcularea de
c&#259;tre agentul specializat al pre&#355;ului aplicabil biletului de avion
respectiv. Acela&#351;i zbor, cu aceea&#351;i Companie poate genera
pre&#355;uri diferite în func&#539;ie de clasa de confort sau de momentul
emiterii biletului.</span></p>
<p><span><br>
3.2.Dat&#259; fiind multitudinea de pre&#355;uri aplicabile biletelor de avion,
acestea, ca &#351;i regulile aplicabile lor, nu pot fi afi&#351;ate sau
publicate. De asemenea, informa&#355;iile referitoare la reguli, care provin
din comunic&#259;rile scrise sau transmise computerizat de c&#259;tre Companie,
prin sistemele de rezervare centralizate sau direct la Agen&#355;ie, au un
caracter continuu, modific&#259;rile pot fi permanente, drept pentru care ele
nu pot fi traduse &#351;i publicate în vreun înscris.</span></p>
<p><span>3.3.Pre&#355;urile de
vânzare nu sunt neap&#259;rat înscrise pe biletele de avion. Pre&#355;urile de
vânzare decurg din regulile de emitere a biletelor de avion stabilite de
companie, singura în m&#259;sura sa conteste modul de înscriere a unui
pre&#355; pe un anumit bilet de avion.</span></p>
<p><span><br>
3.4. Pre&#539;ul de vânzare al biletului de avion este ferm dup&#259; ce
clientul l-a comandat &#537;i Agen&#539;ia a emis biletul, iar contravaloarea
acestuia este înscris&#259; în factura fiscal&#259; emis&#259; de Agen&#539;ie.</span></p>
<p><span>3.5.Biletele de avion emise de agen&#355;ie
sunt proprietatea&nbsp;IATA-BSP Romania – în calitate de reprezentant al
companiilor aeriene membre, încas&#259;rile aferente biletelor vândute se
deconteaz&#259; c&#259;tre IATA-BSP Romania, drept pentru care agen&#355;ia nu
poate fi f&#259;cut&#259; r&#259;spunz&#259;toare despre modul în care a
stabilit pre&#355;ul unui bilet de avion decât de c&#259;tre acest organism sau
de c&#259;tre compania pentru care a emis biletul.</span></p>
<p><span>3.6.Pre&#355;ul oric&#259;rui bilet de
avion presupune condi&#355;ii, reguli &#351;i restric&#355;ii aplicabile
c&#259;l&#259;toriei la care se refer&#259; documentul respectiv în mod
special. Pasagerul are obliga&#355;ia s&#259; se informeze despre aceste reguli
înainte de a cump&#259;ra respectivul bilet. Dup&#259;
cump&#259;rarea&nbsp;biletului se consider&#259; c&#259; pasagerul a luat
cuno&#351;tin&#355;&#259; despre toate aceste reguli, el nu mai poate pretinde
c&#259; nu a fost informat, o dat&#259; ce a intrat în posesia biletului de
avion.</span></p>
<p><span>3.7.Pre&#355;ul biletului de avion include
taxele de aeroport. Exist&#259; îns&#259; situa&#355;ii, independente de
voin&#355;a agen&#355;iei, în care, pe parcursul c&#259;l&#259;toriei i se pot
solicita pasagerului &#351;i alte taxe, care nu au fost percepute ini&#355;ial,
situa&#355;ie în care agen&#355;ia nu poate fi f&#259;cut&#259;
r&#259;spunz&#259;toare.</span></p>
<p><span>
</span></p>
<p><b><span>IV.
BILETUL DE AVION</span></b></p>
<p><span>4.1.
Biletul de avion este documentul, înscrisul sau conven&#355;ia electronic&#259;
în baza c&#259;reia se efectueaz&#259; zborul &#537;i reprezint&#259;
contractul dintre Pasager &#537;i Compania Aerian&#259; în baza c&#259;ruia se
efectueaz&#259; transportul. Biletul de avion este emis de Agen&#355;ie dar
este proprietatea I.A.T.A. – BSP Romania sau a Companiei Aeriene pentru care a
fost emis.</span></p>
<p><span>4.2.Pasagerul posesor al biletului
cump&#259;rat are obliga&#355;ia de a-l p&#259;stra în stare bun&#259; &#351;i
de a-l prezenta la îmbarcare în vederea admiterii sale la zbor. Pierderea sau
deteriorarea biletului de avion nu îl îndrept&#259;&#355;e&#351;te pe acesta la
primirea unui nou bilet, neefectuarea zborului din acest motiv cade în sarcina
exclusiv&#259; a pasagerului, f&#259;r&#259; ca acesta s&#259; poat&#259;
pretinde agen&#355;iei sau companiei nici un fel de desp&#259;gubiri.</span></p>
<p><span>4.3.
Condi&#355;iile contractului &#351;i informa&#355;iile înscrise pe biletul de
avion (în form&#259; scris&#259; sau cu acces electronic la acestea)
completeaz&#259; prezentele condi&#355;ii generale. Se consider&#259; c&#259;
Pasagerul care a intrat în posesia biletului de avion a luat la
cuno&#351;tin&#355;&#259; de toate aceste informa&#355;ii.
</span></p>
<p><a><b><span>V. DREPTURILE &#350;I OBLIGA&#354;IILE
P&#258;R&#354;ILOR. </span></b></a><span><b><span>R&#258;SPUNDERE. DELIMITAREA R&#258;SPUNDERII</span></b></span><span></span></p>
<p><span>5.1.
Agen&#355;iaeste r&#259;spunz&#259;toare de emiterea corect&#259; a biletelor
de avion, în baza informa&#539;iilor/datelor puse la dispozi&#539;ie de
Pasager, &#351;i de informarea cu bun&#259; credin&#355;&#259; a Pasagerului asupra
condi&#355;iilor aplicabile biletului de avion vândut.</span></p>
<p><span><br>
5.2. Informarea Pasagerilor se va realiza, f&#259;r&#259; a fi necesar&#259; o
semnatur&#259; de luare la cuno&#537;tin&#539;&#259;, astfel:
</span></p>
<p><span>5.2.1.
prin afi&#351;area la loc vizibil în Agen&#355;ie a prezentelor condi&#355;ii
generale de vânzare a biletelor de&nbsp;avion precum &#351;i prin comunicarea
de c&#259;tre reprezentantul Agen&#539;iei a condi&#539;iilor,
penaliz&#259;rilor &#537;i restric&#539;iilor de folosire, modificare sau
anulare a biletului de avion &#537;i/sau a rezerv&#259;rilor efectuate.
5.2.2.&nbsp; în cazul în care Pasagerul comunic&#259; reprezentantului
Agen&#539;iei c&#259; inten&#539;ioneaz&#259; s&#259; transporte: animale, p&#259;s&#259;ri
&#537;i/sau obiecte cu regim special (instrumente, echipament sportiv, arme,
muni&#539;ie, substan&#539;e explozive, corozive, etc.) reprezentantul
Agen&#539;iei va informa Pasagerul asupra condi&#539;iilor speciale &#537;i
restric&#539;iilor de transport a acestora, în conformitate cu prevederile
legale în vigoare &#537;i în conformitate cu reglement&#259;rile Companiei
Aeriene.</span></p>
<p><span>
</span></p>
<p><span>5.3.
Reprezentantul Agen&#539;iei va comunica Pasagerului c&#259; are obliga&#539;ia
s&#259; citeasc&#259; prezentele condi&#355;ii generale afi&#351;ate în
Agen&#355;ie înainte de a cump&#259;ra biletul de avion. Biletul de avion
odat&#259; cump&#259;rat se presupune c&#259; Pasagerul a luat la
cuno&#351;tin&#355;&#259; despre toate informa&#355;iile legate de biletul
respectiv, el nemaiputând s&#259; invoce ulterior necunoa&#351;terea acestora
&#351;i s&#259; pretind&#259; eventuale desp&#259;gubiri Agen&#355;iei.
5.4. Pasagerul este obligat &#537;i este singurul responsabil pentru furnizarea
corect&#259; &#537;i complet&#259; a informa&#539;iilor/datelor necesare
Agen&#539;iei pentru rezervarea &#537;i/sau emiterea biletului de avion.
Agen&#539;ia nu poart&#259; nici o r&#259;spundere, în cazul în care a efectuat
rezervarea &#537;i/sau a emis biletul de avion în baza unor
informa&#539;ii/date gre&#537;ite &#537;i/sau incomplete, furnizate de Pasager.</span></p>
<p><span>
5.5. Pasagerul are obliga&#539;ia s&#259; se prezinte la ghi&#537;eele din
aeroport ale Companiei pentru înregistrarea &#537;i predarea bagajelor
(check-in) cu cel pu&#539;in 1 or&#259; &#351;i 30 minute înainte de ora
plec&#259;rii, urmând ca ulterior s&#259; îndeplineasc&#259; toate celelalte
formalit&#259;&#355;i necesare. Unele Companii Aeriene sau aeroporturi pot, în
anumite situa&#539;ii, s&#259; solicite prezen&#539;a la aeroport mai devreme,
drept pentru care Pasagerul are obliga&#539;ia s&#259; solicite, cu 24 ore
înainte de plecare, informa&#539;ii la Companie sau la aeroport referitoare la
timpul de prezentare la check-in.
5.6. Agen&#539;ia, in calitatea sa de intermediar, raspunde fata de Pasager,
doar in situatia in care biletul de avion a fost emis con&#539;inând
informa&#539;ii/date gre&#537;ite &#537;i/sau incomplete din vina
exclusiv&#259; a Agen&#539;iei. În toate celelalte cazuri, Agen&#539;ia este
exonerata de orice raspundere fata de Pasager.</span></p>
<p><span>
5.7. Agen&#355;ia nu este r&#259;spunz&#259;toare &#351;i nu i se pot pretinde
desp&#259;gubiri pentru c&#259; Pasagerul nu a zburat, în urm&#259;toarele
condi&#355;ii (individuale sau cumulate), enumerarea nefiind exhaustiv&#259;:</span></p>
<p><span>
</span></p>
<p><span>5.7.1.
aceast&#259; m&#259;sur&#259; este necesar&#259; pentru a respecta orice
reglementare na&#355;ional&#259; sau interna&#355;ional&#259; valabil&#259;,</span></p>
<p><span><br>
5.7.2. transportul Pasagerului sau al bagajelor Pasagerului ar putea pune în
pericol sau afecta securitatea, s&#259;n&#259;tatea sau starea material&#259;,
respectiv confortul altor pasageri sau a echipajului,</span></p>
<p><span><br>
5.7.3. starea mental&#259; sau psihic&#259; a Pasagerului, inclusiv faptul
c&#259; se afla sub influen&#355;a alcoolului sau a drogurilor, reprezint&#259;
un risc pentru Pasager, pasageri, echipaj sau propriet&#259;&#355;i,</span></p>
<p><span><br>
5.7.4. Pasagerul a avut o conduit&#259; necorespunz&#259;toare pe parcursul
unui alt zbor &#351;i este posibil ca un asemenea comportament s&#259; se
poat&#259; repeta,</span></p>
<p><span><br>
5.7.5. Pasagerul a refuzat s&#259; se supun&#259; verific&#259;rii de
securitate,</span></p>
<p><span><br>
5.7.6. Pasagerul nu achitat pre&#355;ul, taxele sau cheltuielile aplicabile,</span></p>
<p><span><br>
5.7.7. Pasagerul nu de&#539;ine documente de c&#259;l&#259;torie valabile,
Pasagerul încearca s&#259; intre într-o &#355;ar&#259; pe care are doar dreptul
s&#259; o tranziteze sau pentru care nu are documente de c&#259;l&#259;torie
valabile, Pasagerul distruge documentele de c&#259;l&#259;torie în timpul
zborului sau refuz&#259; s&#259; predea documentele de c&#259;l&#259;torie
echipajului de zbor – contra unei confirm&#259;ri scrise – atunci când i se
cere acest lucru,</span></p>
<p><span>
5.7.8. Pasagerul prezint&#259; un bilet care a fost achizi&#355;ionat ilegal, a
fost cump&#259;rat de la o entitate, alta decât noi sau agentul nostru
autorizat, sau a fost declarat pierdut sau furat sau dac&#259; nu poate dovedi
c&#259; este persoana men&#355;ionat&#259; pe bilet,</span></p>
<p><span><br>
5.7.9. Pasagerul nu respect&#259; reglement&#259;rile &#537;i
instruc&#355;iunile în vigoare cu privire la siguran&#355;&#259; &#351;i
securitate,</span></p>
<p><span><br>
5.7.10. Pasagerul nu s-a prezentat la timp la îmbarcare (1 or&#259; &#351;i 30
minute înainte de ora plec&#259;rii înscris&#259; pe bilet), conform art. 5.5
de mai sus,</span></p>
<p><span><br>
5.7.11. Pasagerul este refuzat la îmbarcare datorit&#259; unor probleme legate
de documentele sale personale (inclusiv lipsa viza de intrare pentru &#355;ara
de destina&#355;ie, viza fals&#259;, pa&#351;aport cu interdic&#355;ie, etc)
sau în situa&#355;ia oricarui refuz nejustificat al autorit&#259;&#355;ilor de
a permite c&#259;l&#259;toria,</span></p>
<p><span>
5.7.12. alte împrejur&#259;ri sau cazuri de for&#355;&#259; major&#259;, pe
care Agen&#355;ia nu le putea prevedea sau evita (ex. insolvabilitatea,
insolventa &#537;i/sau falimentul Companiei Aeriene).
</span></p>
<p><span>5.8.
Agen&#355;ia nu este r&#259;spunz&#259;toare pentru întîrzieri sau
modific&#259;ri de orar ale Companiei Aeriene pentru care s-a emis biletul,
pentru calitatea zborului sau pentru servicii aferente zborului (ex – pierderi
de bagaje), obliga&#355;ii care cad direct în sarcina Companiei. In astfel de
situa&#355;ii, Pasagerul se va adresa direct Companiei transportatoare, care va
desp&#259;gubi Pasagerul în conformitate cu reglement&#259;rile
interna&#355;ionale.</span></p>
<p><span><br>
<a>5.9. Dac&#259; Pasagerul renun&#355;&#259; la biletul
de avion cump&#259;rat, înainte sau dup&#259; începerea c&#259;l&#259;torei,
acesta are dreptul la rambursarea par&#539;ial&#259; a pre&#539;ului
pl&#259;tit, dup&#259; ce se deduc taxele de penalizare, inclusiv TS &#537;i
dac&#259; tariful &#537;i condi&#539;iile acestuia, stabilite de Companie,
permit rambursarea. Pasagerul poate modifica biletul ini&#355;ial cump&#259;rat
în condi&#355;iile stabilite de Compania pentru care a fost emis biletul cu
plata unor diferen&#539;e de pre&#539; stabilite de Compania Aerian&#259;,
inclusiv a TS pentru modificarea rezerv&#259;rii.</a></span></p>
<p><span><br>
5.10. Pasagerul nu poate utiliza numai p&#259;r&#355;i componente ale zborului
aferent biletului cump&#259;rat. Neprezentarea Pasagerului la primul zbor se
consider&#259; renun&#355;are la bilet &#351;i i se aplic&#259; prevederile din
prezentele condi&#355;ii generale. Biletul este netransferabil unei alte
persoane.</span></p>
<p><span><br>
5.11. Neprezentarea la zbor presupune pierderea c&#259;l&#259;toriei, cu o
penalizare de pân&#259; la 100% din valoarea biletului, în conformitate cu
reglement&#259;rile Companiei &#537;i ale prezentelor Condi&#539;ii Generale.</span></p>
<p><span><br>
5.12. În situa&#355;ia în care Pasagerul, din vina sa, utilizeaz&#259; doar
par&#355;ial zborurile înscrise în biletul de avion cump&#259;rat, el nu va putea
pretinde desp&#259;gubiri de nici un fel Agen&#355;iei sau Companiei pentru
care s-a emis biletul.</span></p>
<p><span><br>
5.13. Pasagerul are obliga&#355;ia s&#259; se asigure c&#259; este în
regul&#259; din punct de vedere al tuturor formalit&#259;&#355;ilor necesare
c&#259;l&#259;toriei (poli&#355;ie, vam&#259;, s&#259;n&#259;tate, pa&#351;aport,
viz&#259;, etc), Agen&#355;ia neavând nici o r&#259;spundere în fa&#355;a
Pasagerului din acest punct de vedere. Refuzul la îmbarcare dintr-unul din
aceste motive cade în sarcina exclusiv&#259; a Pasagerului, f&#259;r&#259; ca
acesta s&#259; poat&#259; pretinde desp&#259;gubiri Agen&#355;iei.</span></p>
<p><span><br>
5.14. Agen&#355;ia va informa cu bun&#259; credin&#355;&#259; Pasagerul asupra
formalit&#259;&#539;ilor necesare c&#259;l&#259;toriei solicitate, conform
informa&#355;iilor furnizate de I.A.T.A. sau de Compania Aerian&#259; pentru
care se emite biletul, dar nu poate fi f&#259;cut&#259; responsabil&#259;
asupra veridicit&#259;&#355;ii acestor informa&#355;ii &#351;i nici tras&#259;
la r&#259;spundere pentru a le fi oferit sau nu, deoarece serviciul de informare
este unul oferit suplimentar &#351;i nu are leg&#259;tur&#259; cu serviciul de
emitere a biletului de avion.
5.15. Agen&#355;ia nu este r&#259;spunz&#259;toare pentru pagube sau înconveniente
produse Pasagerului în timpul zborului sau datorit&#259; unor modific&#259;ri
de orar, întârzieri ale zborurilor, etc, împrejur&#259;ri care se afl&#259;
dincolo de controlul &#351;i voin&#355;a Agen&#355;iei.</span></p>
<p><span><br>
5.16. Datorit&#259; situa&#355;iei create prin cererile masive de azil politic
sau r&#259;mânerile ilegale, autorit&#259;&#355;ile tuturor statelor, chiar în
prezen&#355;a vizei de intrare eliberat&#259; de ambasadele &#355;&#259;rilor
respective în România, pot refuza f&#259;r&#259; explica&#355;ii s&#259;
permit&#259; trecerea frontierei, respectiv îmbarcarea în avion a Pasagerului.</span></p>
<p><span><br>
În astfel de situa&#355;ii, Agen&#355;iei nu i se pot solicita
desp&#259;gubiri, deoarece imposibilitatea efectu&#259;rii transportului se
datoreaz&#259; faptei unui ter&#355;.</span></p>
<p><span><br>
5.17. În cazul în care Pasagerului i se fur&#259; biletul de avion
cump&#259;rat (în cazul biletelor de hârtie), pe baza de declara&#355;ie pe
proprie r&#259;spundere &#351;i acte doveditoare de la Poli&#355;ie, el poate
solicita Agen&#355;iei emiterea unui duplicat dup&#259; biletul respectiv, în
schimbul pl&#259;&#355;ii unei taxe stabilite de c&#259;tre Compania
Aerian&#259;.</span></p>
<p><span><br>
5.18. Agen&#355;ia se oblig&#259; s&#259; ofere Pasagerilor care
achizi&#355;ioneaz&#259; bilete de avion cel pu&#355;in informa&#355;ii despre
(nu în form&#259; scris&#259;):</span></p>
<p><span><br>
Natura zborului: Regulat/charter/low fare (rezervare efectuat&#259; în
alt&#259; parte decât în CRS)/ Costul biletului defalcat: pre&#355; de
baz&#259;, taxe aeroport, taxa de serviciu etc./ Condi&#355;iile tarifare ale
biletului/ Posibilitatea de a se modifica datele de c&#259;l&#259;torie &#351;i
în ce condi&#355;ii/ Posibilitatea de a se rambursa par&#355;ial sau total
contravaloarea biletului &#351;i în ce condi&#355;ii/ Condi&#355;iile de
emitere a biletului cu un minim de timp în avans/ Tip bilet emis: electronic/
paper/ Cantitatea de bagaje pe care Pasagerul are dreptul s&#259; o transporte
gratuit, atât ca bagaj de mân&#259;, cât &#351;i ca bagaj de cal&#259;, precum
&#351;i costul/kg a bagajului ce dep&#259;&#351;e&#351;te greutatea
admis&#259;/ Orele de operare, decolare, aterizare, escal&#259;/Itinerariul de
zbor/ Compania Aerian&#259; care operez&#259; pe ruta solicitat&#259;/ Timpul
minim necesar de prezentare la ghiseul de îmbarcare/ Informa&#355;ii în cazul
pierderii biletului/ Moneda de emitere a biletului: RON/EURO/ Modalitatea de
plat&#259;: cash, card, factur&#259;/ Nivelul taxelor de serviciu pentru
Emitere a biletului, Reemitere, Schimbare dat&#259; de rezervare, Rambursare
total&#259; sau par&#355;ial&#259; a biletului, alocarea locului în avion.<br>
Agen&#539;ia se oblig&#259; s&#259; aduc&#259; la cuno&#537;tin&#539;a
Pasagerului identitatea Transportatorului iar în cazul modific&#259;ri/
schimb&#259;rii acestuia se oblig&#259; s&#259; informeze Pasagerul, dac&#259;
a primit informatii corespunz&#259;toare despre respectiva
modificare/schimbare.</span></p>
<p><span><br>
Agen&#539;ia se oblig&#259; s&#259; aduc&#259; la cuno&#537;tin&#539;&#259;
preciz&#259;ri cu privire la transportul copiilor neînso&#355;i&#355;i, a
persoanelor cu handicap, femeilor gravide, persoanelor bolnave sau altor
persoane care necesit&#259; asisten&#355;&#259; special&#259;.</span></p>
<p><span>Dup&#259;
emiterea biletului de c&#259;l&#259;torie, toat&#259; responsabilitatea privind
efectuarea c&#259;l&#259;toriei, inclusiv în ceea ce prive&#537;te transportul,
întârzierea livr&#259;rii, deteriorarea sau pierderea bagajelor care
apar&#539;in Pasagerilor, revine Companiei Aeriene în conformitate cu
reglement&#259;rile &#537;i legisla&#539;ia în vigoare. </span></p>
<p><span>Responsabilitatea
pentru pierderea, întârzierea sau deteriorarea bagajelor este limitat&#259;, cu
excep&#539;ia cazurilor în care, s-a facut în prealabil o declara&#539;ie
special&#259; de valoare &#537;i s-au pl&#259;tit taxele aferente. În
conformitate cu Conven&#539;ia de la Var&#537;ovia, pentru majoritatea
c&#259;l&#259;toriilor interna&#539;ionale (inclusiv tronsoanele interne ale
acestora) obliga&#539;ia Transportatorului este limitat&#259; la suma de
aproximativ 20USD/kg pentru bagajele înregistrate (de cal&#259;) &#537;i
400USD/pasager pentru bagajele neînregistrate (bagaje de mân&#259;). </span></p>
<p><span>Pentru
Pasagerii care c&#259;l&#259;toresc numai în interiorul SUA, legile federale
prev&#259;d ca suma s&#259; fie de cel pu&#539;in 2.800 USD/pasager.</span></p>
<p><span>Atunci
când se aplic&#259; Conven&#539;ia de la Montreal (în cazul Transportatorilor
din Comunitatea European&#259;), în cazul distrugerii, pierderii, deterior&#259;rii
sau întârzierii bagajelor, în majoritatea cazurilor se aplic&#259; cele “1000
de Drepturi Speciale de Tragere” (aproximativ 1.200 EUR, respectiv 1.470 USD
per pasager).</span></p>
<p><span>Anumi&#539;i
Transportatori nu î&#537;i asum&#259; r&#259;spunderea pentru transportul
obiectelor fragile, de valoare sau perisabile. Informa&#539;ii suplimentare pot
fi ob&#539;inute de la Transportator.</span></p>
<p><span><br>
Se recomand&#259; Pasagerilor verificarea condi&#355;iilor de
c&#259;l&#259;torie cu 24 de ore înainte de data de operare. (call center
aeroport, site Companie transportatoare, Agen&#355;ie de turism).</span></p>
<p><b><span>6. Conditii speciale pentru operatiunile postbooking
(modificare, rambursare in cazul anularii zborului din initiativa companiei
aeriene) in cazul biletelor emise pentru tranportul aerian operat de compania
Ryanair;</span></b></p>
<p><b><span>&nbsp;</span></b></p>
<p><b><span>6.1 Procesul de rambursare si anulare Ryanair</span></b></p>
<p><span>Conform politicii adoptate de compania aeriena Ryanair,
orice operatiune post-booking, voluntara sau involuntara, trebuie efectuata exclusiv
de catre pasager pe site-ul companiei aeriene. Agentiile de turism, prin
intermediul carora ati achizitionat biletele on -line sau off-line, nu au
abilitatea de a procesa operatiuni de modificare a zborului si/sau de procesare
a cererii de anulare si rambursarea sumelor avansate pentru achizitionarea
biletelor de avion, in cazul zborururilor anulate din initiativa companiei,
indiferent de cauza care a stat la baza anularii. Pentru mai multe informatii
despre termenii si politica Ryanair, regasiti pe paginal oficiala a companiei
Ryanair <a>www.ryanair.com</a> </span></p>
<p><b><span>&nbsp;</span></b></p>
<p><b><span>6.2.Pentru recuperarea contravalorii unui zbor Ryanair
anulat sau modificat, trebuie sa depuneti cererea direct la compania Ryanair.
Procesul de depunere al cererii este detaliat mai jos :</span></b></p>
<ul>
 <li><span>Se acceseaza
     site-ul companiei Ryanair https//ryanair.com in scopul descarcarii
     “formularului de verificare a clientilor Ryanair;</span></li>
 <li><span>Pentru
     descarcarea “Formularului de verificare a clientilor Ryanair”) se
     acceseaza succesiv, urmatoareleor Sectiuni: “Informatii utile” “Centru de
     restituiri” ‘Rambursari generale” “Cum primesc o rambursare daca am
     rezervat prin intermediul unei agentii de turism online” <span> </span>cu urmatoarele detalii: </span></li>
 <li><span>Se completeaza
     formularul cu urmatoarele detalii:</span></li>
 <li><span>numele
     agentiei de la care a fost achizitionat biletul de avion - Accent Travel
     &amp;Events;</span></li>
 <li><strong><span>num&#259;rul
     de referin&#539;&#259; al rezerv&#259;rii (PNR) -</span></strong><span>&nbsp;acesta
     este num&#259;rul rezerv&#259;rii la compania aeriana, care se regaseste
     in sec&#539;iunea “Rezumatul c&#259;l&#259;toriei” din&nbsp;</span><span><a><span>pagina c&#259;l&#259;toriei </span></a></span><span><span> </span>din Ryanair sau in confirmarea rezervarii;</span></li>
 <li><strong><span>numele &#537;i
     adresa dvs. de e-mail; </span></strong><span>copie a c&#259;r&#539;ii de
     identitate/pasaport &#537;i a semn&#259;turii; dovada detaliilor bancare
     cu afi&#537;area codului IBAN/BIC; confirmarea rezerv&#259;rii </span></li>
 <li><span>Formularul
     astfel completat se incarca pe site-ul compnaiei aeriene Ryanair.com</span></li>
</ul>
<p><strong><span>6.3. </span></strong><span>Ryanair va gestiona solicitarea &#537;i v&#259; va
contacta direct. Dac&#259; trebuie s&#259; contacta&#539;i compania Ryanair,
pute&#539;i g&#259;si detaliile de contact&nbsp;pe <a>www.ryanair.com</a>. </span></p>
<p><strong><span>6.4. </span></strong><b><span>Ryanair nu colaboreaz&#259;
cu furnizorii de servicii de voiaj, în prezent compania ofer&#259;
ramburs&#259;ri doar pasagerilor în mod direct astfel incat are nevoie de
detaliile dvs. pentru a v&#259; verifica identitatea.</span></b><span>
</span><b></b></p>
<p><b><span>7.
PROTEC&#538;IA DATELOR CU CARACTER PERSONAL</span></b></p>
<p><span>7.1.
Datele cu caracter personal ale Pasagerului vor fi prelucrate în conformitate
cu dispozi&#539;iile Regulamentului (UE) nr. 679/2016 privind protec&#355;ia
persoanelor fizice în ceea ce prive&#351;te prelucrarea datelor cu caracter
personal &#351;i libera circula&#355;ie a acestor date. Pasagerul va furniza Agen&#355;iei
datele personale solicitate &#351;i este de acord cu prelucrarea acestora în
scopul derul&#259;rii &#351;i monitoriz&#259;rii Contractului (biletului de
avion) de c&#259;tre Agen&#539;ie. De asemenea, Pasagerul, declar&#259; c&#259;
este titular al drepturilor p&#259;rinte&#537;ti &#537;i este de acord cu
prelucrarea datelor personale ale copiilor minori sub 16 ani care îi
înso&#355;esc, dup&#259; caz.</span></p>
<p><span>
7.2. Pasagerul are dreptul de acces &#351;i de informare privind datele
personale &#351;i dreptul de a corecta/modifica orice astfel de date, dreptul
de opozi&#355;ie, precum &#537;i dreptul de a solicita portarea (mutarea) sau
&#537;tergerea acestora. Dac&#259; are întreb&#259;ri sau cereri cu privire la
prelucrarea datelor sale personale, acesta se poate adresa Responsabilului
pentru protec&#539;ia datelor cu caracter personal al Agen&#539;iei. </span></p>
<p><span>
7.3. Prelucrarea datelor personale de c&#259;tre Agen&#539;ie se va face doar
prin personalul propriu &#537;i se va limita accesul la acele persoane care
îndeplinesc, gestioneaz&#259; &#537;i monitorizeaz&#259; obliga&#539;iile
prev&#259;zute în prezentul contract.
7.4. În vederea îndeplinirii obliga&#539;iilor contractuale ale Agen&#539;iei
unele date cu caracter personal ale Pasagerului pot fi transferate c&#259;tre
ter&#539;e persoane care presteaz&#259; serviciile&nbsp; de transport sau alte
Servicii de calatorie necesare, inclusiv în afara Uniunii Europene, dup&#259;
caz, situa&#539;ie în care vor fi luate toate m&#259;surile tehnice rezonabile
pentru protejarea acestora. De asemenea, datele cu caracter personal pot fi
puse la dispozi&#539;ia autorit&#259;&#539;ilor statului român
îndrept&#259;&#539;ite s&#259; solicite &#537;i, respectiv, s&#259;
primeasc&#259; astfel de informa&#539;ii.</span></p>
<p><span>
7.5. Agen&#539;ia se angajeaz&#259; s&#259; adopte m&#259;suri de securitate
tehnice &#351;i organizatorice adecvate pentru a:<br>
a) împiedica orice persoan&#259; neautorizat&#259; s&#259; aib&#259; acces la
sistemele informatice de prelucrare date cu caracter personal proprii, cum ar
fi:</span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><b><span>Controlul accesului:
</span></b><span>evitarea accesului neautorizat
în zona facilit&#259;&#539;ilor unde se prelucreaz&#259; date personale;</span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span>Evitarea utiliz&#259;rii neautorizate a sistemelor;</span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span>Evitarea citirii, copierii, modific&#259;rii sau
&#537;tergerii neautorizate a datelor;</span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span><span>Imposibilitatea</span></span><span> <span>citirii</span>, <span>copierii</span>,
<span>modific&#259;rii</span> <span>sau</span> <span>&#537;tergerii</span> <span>în</span> <span>timpul</span> <span>transferului</span> <span>electronic</span> <span>sau</span> <span>transportului</span></span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span><span>Determinarea</span></span><span> <span>faptului</span> <span>dac&#259;</span>
<span>&#537;i</span> de <span>c&#259;tre</span> <span>cine</span> au <span>fost</span> <span>introduse</span>,
<span>modificate</span> <span>sau</span> <span>&#537;terse</span> date <span>cu</span> <span>caracter</span> <span>personal</span> <span>în</span> <span>sistemul</span> de <span>prelucrare</span> a <span>datelor</span></span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span>Protec&#539;ia împotriva distrugerii inten&#539;ionate
(din neglijent&#259; &#537;i / sau inten&#539;ie) sau a pierderii</span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span><b><span>Recuperare</span></b></span><b><span> <span>rapid&#259;</span> a <span>datelor</span> <span>personale</span></span></b></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><b><span>Managementul
protec&#539;iei datelor personale</span></b><span>,
inclusiv cursuri sistematice pentru angaja&#539;i</span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span><b><span>Managementul</span></b></span><b><span> <span>incidentelor</span></span></b><span>:</span></p>
<p><span><span>·<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><b><span>Protec&#539;ia
datelor începând cu momentul conceperii aplica&#539;iei/proiectului</span></b></p>
<p><span>
</span></p>
<p><span>7.6.
Datele cu caracter personal vor fi stocate pentru o perioad&#259; de maximum 5
ani în bazele de date sau pentru perioade mai mari, doar în situa&#355;ia în care
acest lucu este obligatoriu potrivit unor dispozi&#355;ii legale.
7.7. Politica Agen&#539;iei privind protec&#539;ia datelor cu character
personal poate fi consultat&#259; la adresa web: https://accenttravel.ro/protectia-datelor-personale.</span></p>
<p><span>
</span></p>
<p><b><span>VIII.
RECLAMA&#354;II</span></b></p>
<p><span>8.1.
Orice reclama&#355;ie cu privire la biletul de avion cump&#259;rat se va face
în scris, în termen de maxim 7 zile de la data producerii evenimentului
reclamat, anexând toate documentele pe care le are &#537;i sunt relevante.
8.2.Reclama&#355;iile care nu sunt f&#259;cute în scris nu vor fi luate în
considerare.</span></p>
<p><span>8.3.
Reclama&#539;iile se vor formula &#537;i depune de c&#259;tre Pasager direct
c&#259;tre Compania Aerian&#259;. Cu toate acestea, în cazul în care Pasagerul
formuleaz&#259; reclama&#539;ia &#537;i o depune direct la Agen&#539;ie,
Agen&#355;ia va transmite reclama&#355;ia respectiv&#259; Companiei Aeriene
pentru care a fost emis biletul iar solu&#355;ia dat&#259; va fi aceea
dat&#259; de Compania Aerian&#259;, în calitate de proprietar&#259; a
biletului.</span></p>
<p><span><br>
În cazul în care r&#259;spunsul Companiei Aeriene, va fi comunicat
Agen&#539;iei &#537;i nu direct Pasagerului, Agen&#355;ia va informa Pasagerul
despre solu&#355;ia dat&#259; de Companie reclama&#355;iei, în scris, în termen
de 3 zile lucr&#259;toare de la primirea r&#259;spunsului de la Companie.</span></p>
<p><span><br>
În situa&#355;ia în care Pasagerul nu este mul&#355;umit cu r&#259;spunsul
primit, el se va adresa direct Companiei Aeriene care a efectuat zborul sau
dup&#259; caz va ac&#539;iona Compania Aerian&#259; în cazul în care aceasta nu
&#537;i-a îndeplinit corespunz&#259;tor obliga&#539;iile asumate. În nici un
caz Agen&#539;ia nu va putea fi &#539;inut&#259; r&#259;spunz&#259;toare
fa&#539;&#259; de Pasager privind neîndeplinirea obliga&#539;iilor asumate de
c&#259;tre Compania Aerian&#259;, Agen&#539;ia ac&#539;ionând doar ca &#537;i
un intermediar între Pasager &#537;i Compania Aerian&#259;.</span></p>
<p><span><br>
8.4. Pasagerul va fi desp&#259;gubit direct de c&#259;tre Compania Aerian&#259;
în toate cazurile în care aceasta decide acest lucru, Agen&#355;ia nefiind
obligat&#259; s&#259; intermedieze astfel de desp&#259;gubiri.</span></p>
<p><span><br>
8.5. Orice reclama&#355;ie se va face de c&#259;tre Pasager în conformitate cu
prezentele condi&#355;ii generale de vânzare a biletelor de avion &#537;i/sau
în conformitate cu condi&#539;iile generale de transport &#537;i/sau alte
proceduri interne ale Companiei Aeriene.</span></p>
<p><b><span>
</span></b></p>
<p><b><span>IX.
NOI REGULI ALE UNIUNII EUROPENE PRIVIND SECURITATEA PE AEROPORTURI</span></b></p>
<p><span>9.1.Pentru
a v&#259; proteja împotriva noilor amenin&#355;&#259;ri cu explozibili lichizi,
Uniunea European&#259; (UE) a adoptat o serie de reguli care
restric&#355;ioneaz&#259; cantitatea de lichide pe care le pute&#355;i trece
prin punctele de control de securitate. Aceste reguli se aplic&#259; la
to&#355;i pasagerii care pleac&#259; de pe aeroporturi din UE indiferent de
destina&#355;ia acestora.<br>
9.2.Aceasta înseamn&#259; c&#259; la punctele de control de securitate se vor
face verific&#259;ri legate de substan&#355;ele lichide pe care dori&#355;i
s&#259; le transporta&#355;i, suplimentar fa&#355;&#259; de alte obiecte
interzise. Totu&#351;i, noile reguli nu limiteaz&#259; cantitatea de lichide pe
care le pute&#355;i cump&#259;ra din magazinele aflate dincolo de locul unde
prezenta&#355;i taloanele de îmbarcare sau de la bordul aeronavelor operate de
o Companie Aerian&#259; din UE.</span></p>
<p><span>9.3.Noile
reguli se aplic&#259; începand din 6 Noiembrie 2006, pe toate aeroporturile din
UE, precum &#351;i din Norvegia, Islanda &#351;i Elve&#355;ia, &#351;i sunt
valabile pân&#259; la o în&#351;tiin&#355;are în prealabil cu toate
modific&#259;rile prezente la aceast&#259; dat&#259; &#537;i cele viitoare.</span></p>
<p><span><br>
<b>CE ESTE NOU?</b></span></p>
<p><b><i><span>Când
împacheta&#355;i</span></i></b></p>
<p><span>9.4.Vi
se va permite s&#259; lua&#355;i doar cantit&#259;&#355;i mici de lichide în
bagajul de mân&#259;. Aceste lichide trebuie sa fie în recipiente individuale
de maximum 100 mililitri fiecare. Trebuie s&#259; pune&#355;i aceste recipiente
într-un ambalaj din material plastic transparent, care se poate reînchide, dar
nu mai mult de un litru per pasager.<br>
&nbsp;</span></p>
<p><b><i><span>La
aeroport</span></i></b></p>
<p><span>Pentru
a veni în ajutorul operatorilor de control de securitate, trebuie:
</span></p>
<ul>
 <li><span>s&#259; prezenta&#355;i
     operatorului pentru examinare toate lichidele pe care le ave&#355;i, în
     punctele de control;</span></li>
 <li><span>s&#259; dezbr&#259;ca&#355;i
     jacheta &#351;i/sau pardesiul. Acestea vor fi controlate separat;</span></li>
 <li><span>s&#259; scoate&#355;i computerele
     portabile &#351;i alte echipamente electrice mari din bagajul de
     mân&#259;. </span><span><span>Acestea</span></span><span> <span>vor</span> fi <span>scanate</span> <span>separat</span>.</span></li>
</ul>
<p><span><span>Lichidele</span></span><span> <span>includ</span>:</span></p>
<p><span>
</span></p>
<ul>
 <li><span>ap&#259; &#351;i
     alte b&#259;uturi, supe, siropuri</span></li>
 <li><span>creme, <span>lo&#355;iuni</span> <span>&#351;i</span> <span>uleiuri</span></span></li>
 <li><span><span>parfumuri</span></span></li>
 <li><span>spray-<span>uri</span></span></li>
 <li><span>geluri,
     inclusiv geluri de p&#259;r &#351;i de baie</span></li>
 <li><span>con&#355;inutul
     ambalajelor presurizate, inclusiv spuma de ras, alte spume &#351;i
     deodorante</span></li>
 <li><span>paste, <span>inclusiv</span> pasta de <span>din&#355;i</span></span></li>
 <li><span><span>amestecuri</span></span><span> <span>lichid-solide</span></span></li>
 <li><span>mascara</span></li>
 <li><span>orice alt
     articol cu consisten&#355;&#259; similar&#259;</span></li>
</ul>
<p><span><br>
<b>9.5.CE NU SE SCHIMB&#258;?</b></span></p>
<p><b><i><span>Dumneavoastr&#259;
înc&#259; pute&#355;i:</span></i></b></p>
<ul>
 <li><span>s&#259; împacheta&#355;i lichide
     în bagajele de cal&#259; – noile reguli se aplic&#259; doar pentru
     bagajele de mân&#259;;</span></li>
 <li><span>s&#259; lua&#355;i în bagajul de mân&#259;
     medicamente &#351;i substan&#355;e pentru diet&#259;, inclusiv hran&#259;
     pentru copii, pentru folosin&#355;&#259; în timpul c&#259;l&#259;toriei. Vi
     se va cere re&#355;eta;s&#259; cump&#259;ra&#355;i lichide cum ar fi
     b&#259;uturi sau parfumuri ori dintr-un magazin de pe un aeroport din UE, situat
     înainte de locul unde prezenta&#355;i talonul de îmbarcare sau de la
     bordul aeronavelor operate de o Companie Aerian&#259; din UE.</span></li>
 <li><span>Dac&#259; sunt vândute în ambalaje
     speciale, sigilate, nu le deschide&#355;i înainte de a vi se face
     controlul de securitate altfel, con&#355;inutul acestora poate fi
     confiscat. (Dac&#259; sunteti în tranzit printr-un aeroport, nu
     deschide&#355;i ambalajul înainte de controlul de securitate la aeroportul
     de transfer, sau la ultimul, dac&#259; tranzita&#355;i mai mult decât
     odat&#259;).</span></li>
 <li><span>Este foarte important ca atunci
     când face&#539;i cump&#259;r&#259;turi în tranzit sau la bordul avionului,
     s&#259; v&#259; asigura&#539;i c&#259; respecta&#539;i prevederile vamale
     ale aeroportului de destina&#539;ie final&#259;. Nerespectând aceste
     prevederi pute&#539;i fi amendat sau judecat sau s&#259; nu vi se
     permit&#259; intrarea în &#539;ara respectiv&#259;, iar Agen&#539;ia sau
     Compania nu au nici o responsabilitate.</span></li>
</ul>
<p><span>9.6.Dac&#259;
ave&#355;i orice îndoial&#259;, v&#259; rug&#259;m s&#259; solicita&#539;i
informa&#539;ii de la Compania Aerian&#259; sau de la Agen&#539;ia de turism
înainte de începerea c&#259;l&#259;toriei.</span></p>
<p><span><br>
9.7.De asemenea, v&#259; rug&#259;m s&#259; coopera&#355;i cu personalul
Companiei, cât &#351;i cu cel de securitate din cadrul aeroportului.<br>
<b>
<br>
</b></span></p>
<p><b><span>X.
DISPOZI&#354;II FINALE</span></b></p>
<p><span>10.1.
Prezentele condi&#355;ii generale sunt realizate în conformitate &#351;i se
completeaz&#259; cu prevederile CONVEN&#354;IEI DE LA VAR&#350;OVIA, în care
România este parte semnatar&#259;, în conformitate cu prevederile art. 11,
aliniatul 2 din CONSTITU&#354;IA ROMÂNIEI si cu reglementarile UE în vigoare,
precum &#537;i cu reglement&#259;rile interna&#539;ionale &#537;i UE privind
vânzarea biletelor de avion &#537;i asigurarea transportului pasagerilor,
REGULAMENTUL (CE) NR. 261/2004 AL PARLAMENTULUI EUROPEAN &#350;I AL CONSILIULUI
din 11 februarie 2004 &#537;i Hot&#259;rârea Guvernului nr. 1912/2006 privind
stabilirea unor m&#259;suri pentru asigurarea aplic&#259;rii Regulamentului
(CE) nr. 261/2004 al Parlamentului European.</span></p>
<p><span>
10.2. Afi&#351;area în Agen&#355;ie a prezentelor condi&#355;ii generale
presupune obligativitatea Pasagerului de a le citi. Se consider&#259; c&#259;
Pasagerul care a cump&#259;rat biletul de avion a luat la
cuno&#351;tin&#355;&#259; despre con&#355;inutul acestor condi&#355;ii &#351;i
el nu va mai putea invoca ulterior necunoa&#351;terea lor sau lipsa de informare.
10.3. Prezentele condi&#355;ii generale vor fi aplicabile &#351;i opozabile
ter&#355;ilor în toate situa&#355;iile stipulate în cuprinsul lor.</span></p>
</div>
	  </div>
	  </v-list>
      <template v-slot:footer="{ props }">
        <v-btn :disabled="isdisabled" class="d-flex text-capitalize font-weight-normal cancel-button" size="x-large" :color="'secondary'" rounded="theme" @click="!isdisabled && (dialog = false)"><v-icon icon="mdi-arrow-left"></v-icon><span v-text="waittime"></span></v-btn>
      </template>
  </Modal>
	`,
  methods: {
	isValid(){
      this.$refs.termscheck.validate();
	  return !!this.terms;
    },
  },
  computed: {
  },
	beforeCreate: function(){
		// console.warn('created', this);
	},
  watch:{
	modelValue: {
		handler(newValue, oldValue){
			// setTimeout(() => this.dialog = newValue, 1000);
		},
		immediate:true
    },
	'dialog': {
      handler(newValue, oldValue){
		  /*if(newValue){
			  if(!this.waited){
				  var modal_flight_ryanair_timer = 7;
					var modal_flight_ryanair_timer_interval = setInterval(() => {
						modal_flight_ryanair_timer--;
						this.waittime = '(' + modal_flight_ryanair_timer + ')';
						if(!modal_flight_ryanair_timer){
							this.waited = true;
							this.isdisabled = false;
							this.waittime = '';
							clearInterval(modal_flight_ryanair_timer_interval);
						}
					}, 1000)
			  }
		  }
        console.warn('openend Ryanair', newValue, this.dialog);
		*/
      },
		immediate:true
    },
  }
}
