import { computed as r, defineComponent as z, h as J, ref as S, watchEffect as Tn, onBeforeMount as Ln, onBeforeUpdate as Pn, watch as g, onMounted as bn, nextTick as _, resolveComponent as Nn, openBlock as P, createElementBlock as q, mergeProps as H, createBlock as K, resolveDynamicComponent as F, createSlots as x, withCtx as A, renderSlot as B, toDisplayString as Z, createVNode as En, normalizeProps as k, guardReactiveProps as w, createCommentVNode as Rn, createElementVNode as nn, renderList as tn } from "vue";
import { getSupportedRegionCodes as Vn, getCountryCodeForRegionCode as Kn, getExample as Fn, parsePhoneNumber as On } from "awesome-phonenumber";
import { components } from "vuetify";
const Un = components.VListItem;
const Dn = components.VSelect;
const on = components.VTextField;
function an() {
  return {
    country: {
      required: !0,
      type: Object
    },
    decorative: {
      type: Boolean,
      default: !1
    }
  };
}
function sn({ props: n }) {
  const t = r(() => n.decorative ? void 0 : "img"), o = r(() => n.decorative ? void 0 : n.country.name);
  return { role: t, title: o };
}
const Hn = z({
  props: an(),
  setup(n) {
    const { role: t, title: o } = sn({ props: n });
    return () => J("span", {
      role: t.value,
      title: o.value,
      class: ["v-phone-input__country__icon", "f32"]
    }, J("span", { class: ["flag", n.country.iso2.toLowerCase()] }));
  }
}), Zn = z({
  props: an(),
  setup(n) {
    const { role: t, title: o } = sn({ props: n });
    return () => J("span", {
      role: t.value,
      title: o.value,
      class: [
        "v-phone-input__country__icon",
        "fi",
        `fi-${n.country.iso2.toLowerCase()}`
      ]
    }, `+${n.country.dialCode}`);
  }
});
function kn(n) {
  return "setPreference" in n;
}
function b(n) {
  return n && typeof n == "object" ? n.iso2 : (n || "").toUpperCase();
}
function wn({ props: n }) {
  const t = Vn(), o = S({}), a = S(0), u = S([]), C = S([]), $ = r(() => n.preferCountries.map(b)), s = r(() => n.includeCountries.map(b)), p = r(() => n.excludeCountries.map(b));
  Tn(() => {
    const y = {}, G = [], h = [];
    n.allCountries.forEach((I) => {
      const c = b(I.iso2);
      if (t.indexOf(c) === -1 || s.value.length && s.value.indexOf(c) === -1 || p.value.indexOf(c) !== -1)
        return;
      const d = { ...I, iso2: c };
      y[d.iso2] = d, $.value.indexOf(c) !== -1 ? G.push(d) : h.push(d);
    }), G.sort((I, c) => $.value.indexOf(I.iso2) - $.value.indexOf(c.iso2)), o.value = y, u.value = G, C.value = h, a.value = G.length + h.length, a.value || console.error('[v-phone-input] resulting countries from "allCountries", "includeCountries" and "excludeCountries" must be a non-empty list');
  });
  const m = r(() => {
    const y = u.value[0] ?? C.value[0];
    return y ? o.value[y.iso2] : void 0;
  }), T = (y) => o.value[b(y)], v = S(!1), N = r(() => !n.disableGuessLoading && v.value);
  return {
    countriesCount: a,
    preferredCountries: u,
    otherCountries: C,
    guessingCountry: N,
    findCountry: T,
    firstCountry: m,
    setCountryPreference: (y) => {
      v.value = !1, kn(n.countryGuesser) && n.countryGuesser.setPreference(y);
    },
    guessCountry: async () => {
      if (!n.guessCountry)
        return;
      v.value = !0;
      const y = T(await n.countryGuesser.guess());
      return v.value = !1, y == null ? void 0 : y.iso2;
    }
  };
}
function Wn({ props: n }) {
  return { countryIconComponent: r(() => {
    switch (n.countryIconMode) {
      case "svg":
        return Zn;
      case "sprite":
        return Hn;
      case "text":
        return;
      default:
        return n.countryIconMode;
    }
  }) };
}
function Yn({ props: n }) {
  return { countrySelectComponent: r(() => n.enableSearchingCountry ? {
    type: "VAutocomplete",
    props: {
      autocomplete: "new-password",
      "aria-autocomplete": "off"
    }
  } : {
    type: "VSelect",
    props: {}
  }) };
}
function Jn({ props: n, country: t, example: o }) {
  const a = (f, V) => typeof f == "function" ? f(V) : f, u = r(() => ({ country: t.value, example: o.value })), C = r(() => a(n.label, u.value)), $ = r(() => a(n.ariaLabel, u.value)), s = r(() => ({
    ...u.value,
    label: C.value || $.value
  })), p = r(() => a(n.countryLabel, s.value)), m = r(
    () => a(n.countryAriaLabel, s.value)
  ), T = r(() => a(n.placeholder, s.value)), v = r(() => a(n.hint, s.value)), N = r(
    () => a(n.invalidMessage, s.value)
  );
  return {
    label: C,
    ariaLabel: $,
    countryLabel: p,
    countryAriaLabel: m,
    placeholder: T,
    hint: v,
    invalidMessage: N,
    messageOptions: u
  };
}
function zn(n, t) {
  const o = S({}), a = () => {
    const u = {};
    Object.keys(n).forEach((C) => {
      const [$, ...s] = C.split(":");
      !s.length || t.indexOf($) === -1 ? u.default = {
        ...u.default || {},
        [C]: C
      } : u[$] = {
        ...u[$] || {},
        [s.join(":")]: C
      };
    }), o.value = u;
  };
  return Ln(a), Pn(a), { namespacedSlots: o };
}
const Qn = "Andorra", jn = "United Arab Emirates", Xn = "Afghanistan", _n = "Antigua and Barbuda", qn = "Anguilla", xn = "Albania", nt = "Armenia", tt = "Angola", ot = "Antarctica", et = "Argentina", at = "American Samoa", st = "Austria", ct = "Australia", rt = "Aruba", it = "Aland", ut = "Azerbaijan", lt = "Bosnia and Herzegovina", dt = "Barbados", yt = "Bangladesh", Ct = "Belgium", $t = "Burkina Faso", St = "Bulgaria", pt = "Bahrain", Mt = "Burundi", mt = "Benin", vt = "Saint Barthelemy", It = "Bermuda", gt = "Brunei", ft = "Bolivia", Gt = "Bonaire", ht = "Brazil", At = "Bahamas", Bt = "Bhutan", Tt = "Bouvet Island", Lt = "Botswana", Pt = "Belarus", bt = "Belize", Nt = "Canada", Et = "Cocos (Keeling) Islands", Rt = "Democratic Republic of the Congo", Vt = "Central African Republic", Kt = "Republic of the Congo", Ft = "Switzerland", Ot = "Ivory Coast", Ut = "Cook Islands", Dt = "Chile", Ht = "Cameroon", Zt = "China", kt = "Colombia", wt = "Costa Rica", Wt = "Cuba", Yt = "Cape Verde", Jt = "Curacao", zt = "Christmas Island", Qt = "Cyprus", jt = "Czech Republic", Xt = "Germany", _t = "Djibouti", qt = "Denmark", xt = "Dominica", no = "Dominican Republic", to = "Algeria", oo = "Ecuador", eo = "Estonia", ao = "Egypt", so = "Western Sahara", co = "Eritrea", ro = "Spain", io = "Ethiopia", uo = "Finland", lo = "Fiji", yo = "Falkland Islands", Co = "Micronesia", $o = "Faroe Islands", So = "France", po = "Gabon", Mo = "United Kingdom", mo = "Grenada", vo = "Georgia", Io = "French Guiana", go = "Guernsey", fo = "Ghana", Go = "Gibraltar", ho = "Greenland", Ao = "Gambia", Bo = "Guinea", To = "Guadeloupe", Lo = "Equatorial Guinea", Po = "Greece", bo = "South Georgia and the South Sandwich Islands", No = "Guatemala", Eo = "Guam", Ro = "Guinea-Bissau", Vo = "Guyana", Ko = "Hong Kong", Fo = "Heard Island and McDonald Islands", Oo = "Honduras", Uo = "Croatia", Do = "Haiti", Ho = "Hungary", Zo = "Indonesia", ko = "Ireland", wo = "Israel", Wo = "Isle of Man", Yo = "India", Jo = "British Indian Ocean Territory", zo = "Iraq", Qo = "Iran", jo = "Iceland", Xo = "Italy", _o = "Jersey", qo = "Jamaica", xo = "Jordan", ne = "Japan", te = "Kenya", oe = "Kyrgyzstan", ee = "Cambodia", ae = "Kiribati", se = "Comoros", ce = "Saint Kitts and Nevis", re = "North Korea", ie = "South Korea", ue = "Kuwait", le = "Cayman Islands", de = "Kazakhstan", ye = "Laos", Ce = "Lebanon", $e = "Saint Lucia", Se = "Liechtenstein", pe = "Sri Lanka", Me = "Liberia", me = "Lesotho", ve = "Lithuania", Ie = "Luxembourg", ge = "Latvia", fe = "Libya", Ge = "Morocco", he = "Monaco", Ae = "Moldova", Be = "Montenegro", Te = "Saint Martin", Le = "Madagascar", Pe = "Marshall Islands", be = "North Macedonia", Ne = "Mali", Ee = "Myanmar (Burma)", Re = "Mongolia", Ve = "Macao", Ke = "Northern Mariana Islands", Fe = "Martinique", Oe = "Mauritania", Ue = "Montserrat", De = "Malta", He = "Mauritius", Ze = "Maldives", ke = "Malawi", we = "Mexico", We = "Malaysia", Ye = "Mozambique", Je = "Namibia", ze = "New Caledonia", Qe = "Niger", je = "Norfolk Island", Xe = "Nigeria", _e = "Nicaragua", qe = "Netherlands", xe = "Norway", na = "Nepal", ta = "Nauru", oa = "Niue", ea = "New Zealand", aa = "Oman", sa = "Panama", ca = "Peru", ra = "French Polynesia", ia = "Papua New Guinea", ua = "Philippines", la = "Pakistan", da = "Poland", ya = "Saint Pierre and Miquelon", Ca = "Pitcairn Islands", $a = "Puerto Rico", Sa = "Palestine", pa = "Portugal", Ma = "Palau", ma = "Paraguay", va = "Qatar", Ia = "Reunion", ga = "Romania", fa = "Serbia", Ga = "Russia", ha = "Rwanda", Aa = "Saudi Arabia", Ba = "Solomon Islands", Ta = "Seychelles", La = "Sudan", Pa = "Sweden", ba = "Singapore", Na = "Saint Helena", Ea = "Slovenia", Ra = "Svalbard and Jan Mayen", Va = "Slovakia", Ka = "Sierra Leone", Fa = "San Marino", Oa = "Senegal", Ua = "Somalia", Da = "Suriname", Ha = "South Sudan", Za = "Sao Tome and Principe", ka = "El Salvador", wa = "Sint Maarten", Wa = "Syria", Ya = "Eswatini", Ja = "Turks and Caicos Islands", za = "Chad", Qa = "French Southern Territories", ja = "Togo", Xa = "Thailand", _a = "Tajikistan", qa = "Tokelau", xa = "East Timor", ns = "Turkmenistan", ts = "Tunisia", os = "Tonga", es = "Turkey", as = "Trinidad and Tobago", ss = "Tuvalu", cs = "Taiwan", rs = "Tanzania", is = "Ukraine", us = "Uganda", ls = "U.S. Minor Outlying Islands", ds = "United States", ys = "Uruguay", Cs = "Uzbekistan", $s = "Vatican City", Ss = "Saint Vincent and the Grenadines", ps = "Venezuela", Ms = "British Virgin Islands", ms = "U.S. Virgin Islands", vs = "Vietnam", Is = "Vanuatu", gs = "Wallis and Futuna", fs = "Samoa", Gs = "Kosovo", hs = "Yemen", As = "Mayotte", Bs = "South Africa", Ts = "Zambia", Ls = "Zimbabwe", Ps = {
  AD: Qn,
  AE: jn,
  AF: Xn,
  AG: _n,
  AI: qn,
  AL: xn,
  AM: nt,
  AO: tt,
  AQ: ot,
  AR: et,
  AS: at,
  AT: st,
  AU: ct,
  AW: rt,
  AX: it,
  AZ: ut,
  BA: lt,
  BB: dt,
  BD: yt,
  BE: Ct,
  BF: $t,
  BG: St,
  BH: pt,
  BI: Mt,
  BJ: mt,
  BL: vt,
  BM: It,
  BN: gt,
  BO: ft,
  BQ: Gt,
  BR: ht,
  BS: At,
  BT: Bt,
  BV: Tt,
  BW: Lt,
  BY: Pt,
  BZ: bt,
  CA: Nt,
  CC: Et,
  CD: Rt,
  CF: Vt,
  CG: Kt,
  CH: Ft,
  CI: Ot,
  CK: Ut,
  CL: Dt,
  CM: Ht,
  CN: Zt,
  CO: kt,
  CR: wt,
  CU: Wt,
  CV: Yt,
  CW: Jt,
  CX: zt,
  CY: Qt,
  CZ: jt,
  DE: Xt,
  DJ: _t,
  DK: qt,
  DM: xt,
  DO: no,
  DZ: to,
  EC: oo,
  EE: eo,
  EG: ao,
  EH: so,
  ER: co,
  ES: ro,
  ET: io,
  FI: uo,
  FJ: lo,
  FK: yo,
  FM: Co,
  FO: $o,
  FR: So,
  GA: po,
  GB: Mo,
  GD: mo,
  GE: vo,
  GF: Io,
  GG: go,
  GH: fo,
  GI: Go,
  GL: ho,
  GM: Ao,
  GN: Bo,
  GP: To,
  GQ: Lo,
  GR: Po,
  GS: bo,
  GT: No,
  GU: Eo,
  GW: Ro,
  GY: Vo,
  HK: Ko,
  HM: Fo,
  HN: Oo,
  HR: Uo,
  HT: Do,
  HU: Ho,
  ID: Zo,
  IE: ko,
  IL: wo,
  IM: Wo,
  IN: Yo,
  IO: Jo,
  IQ: zo,
  IR: Qo,
  IS: jo,
  IT: Xo,
  JE: _o,
  JM: qo,
  JO: xo,
  JP: ne,
  KE: te,
  KG: oe,
  KH: ee,
  KI: ae,
  KM: se,
  KN: ce,
  KP: re,
  KR: ie,
  KW: ue,
  KY: le,
  KZ: de,
  LA: ye,
  LB: Ce,
  LC: $e,
  LI: Se,
  LK: pe,
  LR: Me,
  LS: me,
  LT: ve,
  LU: Ie,
  LV: ge,
  LY: fe,
  MA: Ge,
  MC: he,
  MD: Ae,
  ME: Be,
  MF: Te,
  MG: Le,
  MH: Pe,
  MK: be,
  ML: Ne,
  MM: Ee,
  MN: Re,
  MO: Ve,
  MP: Ke,
  MQ: Fe,
  MR: Oe,
  MS: Ue,
  MT: De,
  MU: He,
  MV: Ze,
  MW: ke,
  MX: we,
  MY: We,
  MZ: Ye,
  NA: Je,
  NC: ze,
  NE: Qe,
  NF: je,
  NG: Xe,
  NI: _e,
  NL: qe,
  NO: xe,
  NP: na,
  NR: ta,
  NU: oa,
  NZ: ea,
  OM: aa,
  PA: sa,
  PE: ca,
  PF: ra,
  PG: ia,
  PH: ua,
  PK: la,
  PL: da,
  PM: ya,
  PN: Ca,
  PR: $a,
  PS: Sa,
  PT: pa,
  PW: Ma,
  PY: ma,
  QA: va,
  RE: Ia,
  RO: ga,
  RS: fa,
  RU: Ga,
  RW: ha,
  SA: Aa,
  SB: Ba,
  SC: Ta,
  SD: La,
  SE: Pa,
  SG: ba,
  SH: Na,
  SI: Ea,
  SJ: Ra,
  SK: Va,
  SL: Ka,
  SM: Fa,
  SN: Oa,
  SO: Ua,
  SR: Da,
  SS: Ha,
  ST: Za,
  SV: ka,
  SX: wa,
  SY: Wa,
  SZ: Ya,
  TC: Ja,
  TD: za,
  TF: Qa,
  TG: ja,
  TH: Xa,
  TJ: _a,
  TK: qa,
  TL: xa,
  TM: ns,
  TN: ts,
  TO: os,
  TR: es,
  TT: as,
  TV: ss,
  TW: cs,
  TZ: rs,
  UA: is,
  UG: us,
  UM: ls,
  US: ds,
  UY: ys,
  UZ: Cs,
  VA: $s,
  VC: Ss,
  VE: ps,
  VG: Ms,
  VI: ms,
  VN: vs,
  VU: Is,
  WF: gs,
  WS: fs,
  XK: Gs,
  YE: hs,
  YT: As,
  ZA: Bs,
  ZM: Ts,
  ZW: Ls
}, bs = "Andorra", Ns = "دولة الإمارات العربية المتحدة", Es = "افغانستان", Rs = "Antigua and Barbuda", Vs = "Anguilla", Ks = "Shqipëria", Fs = "Հայաստան", Os = "Angola", Us = "Antarctica", Ds = "Argentina", Hs = "American Samoa", Zs = "Österreich", ks = "Australia", ws = "Aruba", Ws = "Åland", Ys = "Azərbaycan", Js = "Bosna i Hercegovina", zs = "Barbados", Qs = "Bangladesh", js = "België", Xs = "Burkina Faso", _s = "България", qs = "‏البحرين", xs = "Burundi", nc = "Bénin", tc = "Saint-Barthélemy", oc = "Bermuda", ec = "Negara Brunei Darussalam", ac = "Bolivia", sc = "Bonaire", cc = "Brasil", rc = "Bahamas", ic = "ʼbrug-yul", uc = "Bouvetøya", lc = "Botswana", dc = "Белару́сь", yc = "Belize", Cc = "Canada", $c = "Cocos (Keeling) Islands", Sc = "République démocratique du Congo", pc = "Ködörösêse tî Bêafrîka", Mc = "République du Congo", mc = "Schweiz", vc = "Côte d'Ivoire", Ic = "Cook Islands", gc = "Chile", fc = "Cameroon", Gc = "中国", hc = "Colombia", Ac = "Costa Rica", Bc = "Cuba", Tc = "Cabo Verde", Lc = "Curaçao", Pc = "Christmas Island", bc = "Κύπρος", Nc = "Česká republika", Ec = "Deutschland", Rc = "Djibouti", Vc = "Danmark", Kc = "Dominica", Fc = "República Dominicana", Oc = "الجزائر", Uc = "Ecuador", Dc = "Eesti", Hc = "مصر‎", Zc = "الصحراء الغربية", kc = "ኤርትራ", wc = "España", Wc = "ኢትዮጵያ", Yc = "Suomi", Jc = "Fiji", zc = "Falkland Islands", Qc = "Micronesia", jc = "Føroyar", Xc = "France", _c = "Gabon", qc = "United Kingdom", xc = "Grenada", nr = "საქართველო", tr = "Guyane française", or = "Guernsey", er = "Ghana", ar = "Gibraltar", sr = "Kalaallit Nunaat", cr = "Gambia", rr = "Guinée", ir = "Guadeloupe", ur = "Guinea Ecuatorial", lr = "Ελλάδα", dr = "South Georgia", yr = "Guatemala", Cr = "Guam", $r = "Guiné-Bissau", Sr = "Guyana", pr = "香港", Mr = "Heard Island and McDonald Islands", mr = "Honduras", vr = "Hrvatska", Ir = "Haïti", gr = "Magyarország", fr = "Indonesia", Gr = "Éire", hr = "יִשְׂרָאֵל", Ar = "Isle of Man", Br = "भारत", Tr = "British Indian Ocean Territory", Lr = "العراق", Pr = "ایران", br = "Ísland", Nr = "Italia", Er = "Jersey", Rr = "Jamaica", Vr = "الأردن", Kr = "日本", Fr = "Kenya", Or = "Кыргызстан", Ur = "Kâmpŭchéa", Dr = "Kiribati", Hr = "Komori", Zr = "Saint Kitts and Nevis", kr = "북한", wr = "대한민국", Wr = "الكويت", Yr = "Cayman Islands", Jr = "Қазақстан", zr = "ສປປລາວ", Qr = "لبنان", jr = "Saint Lucia", Xr = "Liechtenstein", _r = "śrī laṃkāva", qr = "Liberia", xr = "Lesotho", ni = "Lietuva", ti = "Luxembourg", oi = "Latvija", ei = "‏ليبيا", ai = "المغرب", si = "Monaco", ci = "Moldova", ri = "Црна Гора", ii = "Saint-Martin", ui = "Madagasikara", li = "M̧ajeļ", di = "Северна Македонија", yi = "Mali", Ci = "မြန်မာ", $i = "Монгол улс", Si = "澳門", pi = "Northern Mariana Islands", Mi = "Martinique", mi = "موريتانيا", vi = "Montserrat", Ii = "Malta", gi = "Maurice", fi = "Maldives", Gi = "Malawi", hi = "México", Ai = "Malaysia", Bi = "Moçambique", Ti = "Namibia", Li = "Nouvelle-Calédonie", Pi = "Niger", bi = "Norfolk Island", Ni = "Nigeria", Ei = "Nicaragua", Ri = "Nederland", Vi = "Norge", Ki = "नेपाल", Fi = "Nauru", Oi = "Niuē", Ui = "New Zealand", Di = "عمان", Hi = "Panamá", Zi = "Perú", ki = "Polynésie française", wi = "Papua Niugini", Wi = "Pilipinas", Yi = "Pakistan", Ji = "Polska", zi = "Saint-Pierre-et-Miquelon", Qi = "Pitcairn Islands", ji = "Puerto Rico", Xi = "فلسطين", _i = "Portugal", qi = "Palau", xi = "Paraguay", nu = "قطر", tu = "La Réunion", ou = "România", eu = "Србија", au = "Россия", su = "Rwanda", cu = "العربية السعودية", ru = "Solomon Islands", iu = "Seychelles", uu = "السودان", lu = "Sverige", du = "Singapore", yu = "Saint Helena", Cu = "Slovenija", $u = "Svalbard og Jan Mayen", Su = "Slovensko", pu = "Sierra Leone", Mu = "San Marino", mu = "Sénégal", vu = "Soomaaliya", Iu = "Suriname", gu = "South Sudan", fu = "São Tomé e Príncipe", Gu = "El Salvador", hu = "Sint Maarten", Au = "سوريا", Bu = "Eswatini", Tu = "Turks and Caicos Islands", Lu = "Tchad", Pu = "Territoire des Terres australes et antarctiques fr", bu = "Togo", Nu = "ประเทศไทย", Eu = "Тоҷикистон", Ru = "Tokelau", Vu = "Timor-Leste", Ku = "Türkmenistan", Fu = "تونس", Ou = "Tonga", Uu = "Türkiye", Du = "Trinidad and Tobago", Hu = "Tuvalu", Zu = "臺灣", ku = "Tanzania", wu = "Україна", Wu = "Uganda", Yu = "United States Minor Outlying Islands", Ju = "United States", zu = "Uruguay", Qu = "O'zbekiston", ju = "Vaticano", Xu = "Saint Vincent and the Grenadines", _u = "Venezuela", qu = "British Virgin Islands", xu = "United States Virgin Islands", nl = "Việt Nam", tl = "Vanuatu", ol = "Wallis et Futuna", el = "Samoa", al = "Republika e Kosovës", sl = "اليَمَن", cl = "Mayotte", rl = "South Africa", il = "Zambia", ul = "Zimbabwe", W = {
  AD: bs,
  AE: Ns,
  AF: Es,
  AG: Rs,
  AI: Vs,
  AL: Ks,
  AM: Fs,
  AO: Os,
  AQ: Us,
  AR: Ds,
  AS: Hs,
  AT: Zs,
  AU: ks,
  AW: ws,
  AX: Ws,
  AZ: Ys,
  BA: Js,
  BB: zs,
  BD: Qs,
  BE: js,
  BF: Xs,
  BG: _s,
  BH: qs,
  BI: xs,
  BJ: nc,
  BL: tc,
  BM: oc,
  BN: ec,
  BO: ac,
  BQ: sc,
  BR: cc,
  BS: rc,
  BT: ic,
  BV: uc,
  BW: lc,
  BY: dc,
  BZ: yc,
  CA: Cc,
  CC: $c,
  CD: Sc,
  CF: pc,
  CG: Mc,
  CH: mc,
  CI: vc,
  CK: Ic,
  CL: gc,
  CM: fc,
  CN: Gc,
  CO: hc,
  CR: Ac,
  CU: Bc,
  CV: Tc,
  CW: Lc,
  CX: Pc,
  CY: bc,
  CZ: Nc,
  DE: Ec,
  DJ: Rc,
  DK: Vc,
  DM: Kc,
  DO: Fc,
  DZ: Oc,
  EC: Uc,
  EE: Dc,
  EG: Hc,
  EH: Zc,
  ER: kc,
  ES: wc,
  ET: Wc,
  FI: Yc,
  FJ: Jc,
  FK: zc,
  FM: Qc,
  FO: jc,
  FR: Xc,
  GA: _c,
  GB: qc,
  GD: xc,
  GE: nr,
  GF: tr,
  GG: or,
  GH: er,
  GI: ar,
  GL: sr,
  GM: cr,
  GN: rr,
  GP: ir,
  GQ: ur,
  GR: lr,
  GS: dr,
  GT: yr,
  GU: Cr,
  GW: $r,
  GY: Sr,
  HK: pr,
  HM: Mr,
  HN: mr,
  HR: vr,
  HT: Ir,
  HU: gr,
  ID: fr,
  IE: Gr,
  IL: hr,
  IM: Ar,
  IN: Br,
  IO: Tr,
  IQ: Lr,
  IR: Pr,
  IS: br,
  IT: Nr,
  JE: Er,
  JM: Rr,
  JO: Vr,
  JP: Kr,
  KE: Fr,
  KG: Or,
  KH: Ur,
  KI: Dr,
  KM: Hr,
  KN: Zr,
  KP: kr,
  KR: wr,
  KW: Wr,
  KY: Yr,
  KZ: Jr,
  LA: zr,
  LB: Qr,
  LC: jr,
  LI: Xr,
  LK: _r,
  LR: qr,
  LS: xr,
  LT: ni,
  LU: ti,
  LV: oi,
  LY: ei,
  MA: ai,
  MC: si,
  MD: ci,
  ME: ri,
  MF: ii,
  MG: ui,
  MH: li,
  MK: di,
  ML: yi,
  MM: Ci,
  MN: $i,
  MO: Si,
  MP: pi,
  MQ: Mi,
  MR: mi,
  MS: vi,
  MT: Ii,
  MU: gi,
  MV: fi,
  MW: Gi,
  MX: hi,
  MY: Ai,
  MZ: Bi,
  NA: Ti,
  NC: Li,
  NE: Pi,
  NF: bi,
  NG: Ni,
  NI: Ei,
  NL: Ri,
  NO: Vi,
  NP: Ki,
  NR: Fi,
  NU: Oi,
  NZ: Ui,
  OM: Di,
  PA: Hi,
  PE: Zi,
  PF: ki,
  PG: wi,
  PH: Wi,
  PK: Yi,
  PL: Ji,
  PM: zi,
  PN: Qi,
  PR: ji,
  PS: Xi,
  PT: _i,
  PW: qi,
  PY: xi,
  QA: nu,
  RE: tu,
  RO: ou,
  RS: eu,
  RU: au,
  RW: su,
  SA: cu,
  SB: ru,
  SC: iu,
  SD: uu,
  SE: lu,
  SG: du,
  SH: yu,
  SI: Cu,
  SJ: $u,
  SK: Su,
  SL: pu,
  SM: Mu,
  SN: mu,
  SO: vu,
  SR: Iu,
  SS: gu,
  ST: fu,
  SV: Gu,
  SX: hu,
  SY: Au,
  SZ: Bu,
  TC: Tu,
  TD: Lu,
  TF: Pu,
  TG: bu,
  TH: Nu,
  TJ: Eu,
  TK: Ru,
  TL: Vu,
  TM: Ku,
  TN: Fu,
  TO: Ou,
  TR: Uu,
  TT: Du,
  TV: Hu,
  TW: Zu,
  TZ: ku,
  UA: wu,
  UG: Wu,
  UM: Yu,
  US: Ju,
  UY: zu,
  UZ: Qu,
  VA: ju,
  VC: Xu,
  VE: _u,
  VG: qu,
  VI: xu,
  VN: nl,
  VU: tl,
  WF: ol,
  WS: el,
  XK: al,
  YE: sl,
  YT: cl,
  ZA: rl,
  ZM: il,
  ZW: ul
}, ll = Object.entries(Ps).sort(([n, t], [o, a]) => t.localeCompare(a)).map(([n, t]) => ({
  name: t !== W[n] ? `${W[n]} (${t})` : W[n],
  iso2: b(n),
  dialCode: `${Kn(n)}`
})), cn = class rn {
  // eslint-disable-next-line class-methods-use-this
  async guess() {
    let t, o;
    try {
      t = await fetch(rn.IP2C_URL), o = await t.text();
    } catch {
      return;
    }
    const a = o.toString().split(";");
    if (!(!a || a[0] !== "1"))
      return a[1];
  }
};
cn.IP2C_URL = "https://ip2c.org/s";
let un = cn;
class dl extends un {
  constructor() {
    super(...arguments), this.memoCountry = void 0;
  }
  async guess() {
    return this.memoCountry || (this.memoCountry = await super.guess()), this.memoCountry;
  }
  setPreference(t) {
    this.memoCountry = t;
  }
}
const yl = {
  label: "Phone",
  ariaLabel: void 0,
  countryLabel: "Country",
  countryAriaLabel: (n) => `Country for ${n.label}`,
  placeholder: void 0,
  hint: void 0,
  invalidMessage: (n) => `The "${n.label}" field is not a valid phone number (example: ${n.example}).`,
  example: void 0,
  persistentPlaceholder: void 0,
  persistentHint: void 0,
  countryIconMode: void 0,
  allCountries: ll,
  preferCountries: [],
  includeCountries: [],
  excludeCountries: [],
  defaultCountry: void 0,
  countryGuesser: new dl(),
  guessCountry: !1,
  disableGuessLoading: !1,
  enableSearchingCountry: !1,
  displayFormat: "national"
}, ln = { ...yl };
function Cl(n) {
  Object.assign(ln, n);
}
function l(n) {
  return ln[n];
}
function $l(n, t) {
  var o, a;
  return ((o = n.number) == null ? void 0 : o[t]) || ((a = n.number) == null ? void 0 : a.input) || "";
}
function Sl(n) {
  return Fn(n);
}
function en(n, t) {
  return On((n || "").trim(), { regionCode: t });
}
const Y = [
  "id",
  "class",
  "style",
], pl = [
  "variant",
  "flat",
  "tile",
  "density",
  "singleLine",
  "hideDetails",
  "hide-details",
  "direction",
  "reverse",
  "color",
  "bgColor",
  "theme",
  "disabled",
  "readonly"
], Ml = z({
  components: {
    VListItem: Un,
    VSelect: Dn,
    VTextField: on
  },
  inheritAttrs: !1,
  props: {
    label: {
      type: [String, Function],
      default: () => l("label")
    },
    ariaLabel: {
      type: [String, Function],
      default: () => l("ariaLabel")
    },
    countryLabel: {
      type: [String, Function],
      default: () => l("countryLabel")
    },
    countryAriaLabel: {
      type: [String, Function],
      default: () => l("countryAriaLabel")
    },
    placeholder: {
      type: [String, Function],
      default: () => l("placeholder")
    },
    hint: {
      type: [String, Function],
      default: () => l("hint")
    },
    invalidMessage: {
      type: [String, Function],
      default: () => l("invalidMessage")
    },
    example: {
      type: [String, Function],
      default: () => l("example")
    },
    appendIcon: {
      type: String,
      default: void 0
    },
    appendInnerIcon: {
      type: String,
      default: void 0
    },
    prependIcon: {
      type: String,
      default: void 0
    },
    prependInnerIcon: {
      type: String,
      default: void 0
    },
    rules: {
      type: Array,
      default: () => []
    },
    validateOn: {
      type: String,
      default: void 0
    },
    countryIconMode: {
      type: [String, Object],
      default: () => l("countryIconMode")
    },
    allCountries: {
      type: Array,
      default: () => l("allCountries")
    },
    preferCountries: {
      type: Array,
      default: () => l("preferCountries")
    },
    includeCountries: {
      type: Array,
      default: () => l("includeCountries")
    },
    excludeCountries: {
      type: Array,
      default: () => l("excludeCountries")
    },
    defaultCountry: {
      type: String,
      default: () => l("defaultCountry")
    },
    countryGuesser: {
      type: Object,
      default: () => l("countryGuesser")
    },
    guessCountry: {
      type: Boolean,
      default: () => l("guessCountry")
    },
    disableGuessLoading: {
      type: Boolean,
      default: () => l("disableGuessLoading")
    },
    enableSearchingCountry: {
      type: Boolean,
      default: () => l("enableSearchingCountry")
    },
    displayFormat: {
      type: String,
      default: () => l("displayFormat")
    },
    country: {
      type: String,
      default: ""
    },
    modelValue: {
      type: String,
      default: ""
    },
    wrapperProps: {
      type: Object,
      default: () => ({})
    },
    countryProps: {
      type: Object,
      default: () => ({})
    },
    phoneProps: {
      type: Object,
      default: () => ({})
    }
  },
  emits: {
    "update:modelValue": (n) => !0,
    "update:country": (n) => !0
  },
  setup(n, { attrs: t, emit: o, slots: a }) {
    const {
      countriesCount: u,
      preferredCountries: C,
      otherCountries: $,
      guessingCountry: s,
      findCountry: p,
      firstCountry: m,
      setCountryPreference: T,
      guessCountry: v
    } = wn({ props: n }), N = S(null), f = S(null), { namespacedSlots: V } = zn(a, ["country"]), { countryIconComponent: y } = Wn({ props: n }), { countrySelectComponent: G } = Yn({ props: n }), h = S(!1), I = S([]), c = S(n.country), d = S(n.modelValue || ""), M = S({ number: { input: "" } }), O = (e) => Object.keys(t).reduce((i, L) => e(L) ? { ...i, [L]: t[L] } : i, {}), dn = r(() => ({
      ...O((e) => Y.indexOf(e) !== -1),
      ...n.wrapperProps
    })), yn = r(() => ({
      ...G.value.props,
      ...O((e) => Y.indexOf(e) === -1 && pl.indexOf(e) !== -1),
      ...n.countryProps,
      menuProps: {
        maxHeight: 300,
        contentClass: "v-phone-input__country__menu",
        closeOnContentClick: !0,
        ...(n.countryProps ? n.countryProps.menuProps : void 0) || {}
      }
    })), Cn = r(() => ({
      ...O((e) => Y.indexOf(e) === -1),
      ...n.phoneProps
    })), $n = r(() => p(n.defaultCountry) || m.value), E = r(() => p(c.value) || $n.value), Sn = r(() => [...C.value.map((i) => ({ ...i, preferred: !0 })), ...$.value]), U = (e) => $l(e, n.displayFormat), pn = r(() => n.example !== void 0 ? typeof n.example == "function" ? n.example(E.value) : n.example : U(Sl(E.value.iso2))), Mn = r(() => {
      var i;
      const e = new Set(((i = n.validateOn) == null ? void 0 : i.split("")) || []);
      return e.size === 0 || e.has("input");
    }), R = Jn({ props: n, country: E, example: pn }), Q = () => {
      var e;
      Mn.value && ((e = f.value) == null || e.validate());
    }, D = () => {
      const e = n.rules.map((i) => typeof i == "function" ? () => i(d.value ?? "", M.value, R.messageOptions.value) : i);
      R.invalidMessage.value ? I.value = [
        ...e,
        () => !d.value || M.value.valid || R.invalidMessage.value
      ] : I.value = e;
    }, mn = () => {
      M.value.valid && (d.value = U(M.value));
    }, vn = () => {
      var e, i;
      n.modelValue !== (((e = M.value.number) == null ? void 0 : e.input) ?? "") && n.modelValue !== (((i = M.value.number) == null ? void 0 : (((i.international || '').replace(/ .*/, '') + ' ' + (i.national || '').replace(/ +/g,'')).trim())) ?? "") && (d.value = n.modelValue || "");
    }, In = () => {
      n.country && n.country !== c.value && (c.value = n.country);
    };
    g(() => n.rules, D, { deep: !0, immediate: !0 }), g(() => n.displayFormat, mn), g(() => n.modelValue, vn), g(() => n.country, In);
    const gn = () => {
      D(), (d.value ?? "") !== "" && Q();
    };
    g(R.invalidMessage, gn);
    const fn = () => {
      h.value = !0;
    }, Gn = () => {
      h.value = !1;
    }, j = () => {
      M.value = en(d.value, c.value), M.value.valid && (d.value = U(M.value));
    }, hn = (e, i) => {
      c.value ? (o("update:country", c.value), T(c.value)) : _(() => {
        c.value || (c.value = i);
      }), j(), (d.value ?? "") !== "" && Q();
    }, X = () => {
      if ((d.value || "").startsWith("+")) {
        const i = en(d.value).regionCode;
        i && c.value !== i && p(i) && (c.value = i);
      }
      j();
    }, An = () => {
      var i, L;
      const e = ((i = M.value.number) == null ? void 0 : (((i.international || '').replace(/ .*/, '') + ' ' + (i.national || '').replace(/ +/g,'')).trim())) || ((L = M.value.number) == null ? void 0 : L.input) || "";
      e !== n.modelValue && (o("update:modelValue", e));
    };
    g(c, hn), g(d, X), g(M, An, { deep: !0 });
    const Bn = async () => {
      if (c.value)
        return;
      if (u.value === 1) {
        c.value = m.value.iso2;
        return;
      }
      const e = await v();
      !c.value && e && (c.value = e), c.value = c.value || E.value.iso2;
    };
    return bn(() => {
      X(), _(() => {
        Bn();
      });
    }), {
      wrapperAttrs: dn,
      VTextField: on,
      countryInput: N,
      phoneInput: f,
      namespacedSlots: V,
      countrySelectComponent: G,
      countryIconComponent: y,
      countryAttrs: yn,
      phoneAttrs: Cn,
      countryFocused: h,
      guessingCountry: s,
      mergeRules: D,
      lazyCountry: c,
      lazyValue: d,
      mergedRules: I,
      activeCountry: E,
      labels: R,
      countriesItems: Sn,
      onCountryFocus: fn,
      onCountryBlur: Gn
    };
  }
}), ml = (n, t) => {
  const o = n.__vccOpts || n;
  for (const [a, u] of t)
    o[a] = u;
  return o;
}, vl = { key: 1 }, Il = { class: "v-phone-input__country__title" }, gl = { class: "v-phone-input__country__append text-body-2" };
function fl(n, t, o, a, u, C) {
  const $ = Nn("v-list-item");
  return P(), q("div", H({
    class: [{ "v-phone-input--prepend-inner-icon": n.prependInnerIcon }, "v-phone-input"]
  }, n.wrapperAttrs), [
    (P(), K(F(n.countrySelectComponent.type), H({
      ref: "countryInput",
      modelValue: n.lazyCountry,
      "onUpdate:modelValue": t[0] || (t[0] = (s) => n.lazyCountry = s),
      label: n.labels.countryLabel.value,
      "aria-label": n.labels.countryAriaLabel.value,
      items: n.countriesItems,
      "item-title": "name",
      "item-value": "iso2",
      loading: n.guessingCountry,
      "prepend-icon": n.prependIcon,
      "prepend-inner-icon": n.prependInnerIcon,
      class: [{ "v-phone-input--focused": n.countryFocused }, "v-phone-input__country__input"]
    }, n.countryAttrs, {
      onFocus: n.onCountryFocus,
      onBlur: n.onCountryBlur,
	  'onUpdate:menu': (a) => setTimeout(() => (!a && n.$refs.countryInput.isFocused) && (n.$refs.phoneInput.focus()),0)
    }), x({
      selection: A(() => [
        B(n.$slots, "country-selection", { country: n.activeCountry }, () => [
          B(n.$slots, "country-icon", {
            country: n.activeCountry,
            decorative: !1
          }, () => [
            n.countryIconComponent ? (P(), K(F(n.countryIconComponent), {
              key: 0,
              country: n.activeCountry,
              decorative: !1
            }, null, 8, ["country"])) : (P(), q("span", vl, Z(`+${n.activeCountry.dialCode}`), 1))
          ])
        ])
      ]),
      item: A((s) => [
        En($, k(w(s.props)), {
          prepend: A(() => [
            B(n.$slots, "country-icon", {
              country: s.item.raw,
              decorative: !0
            }, () => [
              n.countryIconComponent ? (P(), K(F(n.countryIconComponent), {
                key: 0,
                country: s.item.raw,
                decorative: !0
              }, null, 8, ["country"])) : Rn("", !0)
            ])
          ]),
          title: A(() => [
            B(n.$slots, "country-title", {
              country: s.item.raw
            }, () => [
              nn("span", Il, Z(s.item.raw.name), 1)
            ])
          ]),
          append: A(() => [
            B(n.$slots, "country-append", {
              country: s.item.raw
            }, () => [
              nn("span", gl, " +" + Z(s.item.raw.dialCode), 1)
            ])
          ]),
          _: 2
        }, 1040)
      ]),
      _: 2
    }, [
      tn(n.namespacedSlots.country, (s, p) => ({
        name: p,
        fn: A((m) => [
          B(n.$slots, s, k(w(m)))
        ])
      }))
    ]), 1040, ["modelValue", "label", "aria-label", "items", "loading", "prepend-icon", "prepend-inner-icon", "class", "onFocus", "onBlur"])),
    (P(), K(F(n.VTextField), H({
      ref: "phoneInput",
      modelValue: n.lazyValue,
      "onUpdate:modelValue": t[1] || (t[1] = (s) => n.lazyValue = s),
      label: n.labels.label.value,
      "aria-label": n.labels.ariaLabel.value,
      placeholder: n.labels.placeholder.value,
      hint: n.labels.hint.value,
      rules: n.mergedRules,
      "append-icon": n.appendIcon,
      "append-inner-icon": n.appendInnerIcon,
      "validate-on": n.validateOn,
      class: "v-phone-input__phone__input",
      type: "tel"
    }, n.phoneAttrs), x({ _: 2 }, [
      tn(n.namespacedSlots.default, (s, p) => ({
        name: p,
        fn: A((m) => [
          B(n.$slots, p, k(w(m)))
        ])
      }))
    ]), 1040, ["modelValue", "label", "aria-label", "placeholder", "hint", "rules", "append-icon", "append-inner-icon", "validate-on"]))
  ], 16);
}
const Gl = /* @__PURE__ */ ml(Ml, [["render", fl]]);
function Tl(n) {
  return (t, o) => {
    o && console.warn("[v-phone-input] options must be passed as first argument of createVPhoneInput()"), Cl(n || {}), t.component("VPhoneInput", Gl);
  };
}
class Ll extends un {
  constructor(t = {}) {
    super(), this.storage = t.storage || localStorage, this.key = t.key || "v_phone_input_country";
  }
  async guess() {
    const t = this.retrieveStoredCountry();
    if (t)
      return t;
    const o = await super.guess();
    return o && this.saveStoredCountry(o), o;
  }
  setPreference(t) {
    this.saveStoredCountry(t);
  }
  retrieveStoredCountry() {
    return this.storage.getItem(this.key) || void 0;
  }
  saveStoredCountry(t) {
    this.storage.setItem(this.key, t);
  }
  getStorage() {
    return this.storage;
  }
  getKey() {
    return this.key;
  }
}
export {
  yl as DEFAULT_OPTIONS,
  un as Ip2cCountryGuesser,
  dl as MemoIp2cCountryGuesser,
  Ll as StorageMemoIp2cCountryGuesser,
  Hn as VCountryIconSprite,
  Zn as VCountryIconSvg,
  Gl as VPhoneInput,
  ll as countries,
  Tl as createVPhoneInput,
  $l as formatPhone,
  l as getOption,
  Sl as makeExample,
  en as makePhone,
  Cl as mergeOptions
};
