<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$zones = 0;
if(isset($data['status']) && is_array($data['status'])){
  $zones = count($data['status']);
}
?>
<script>
;(function($){
  var zone_index = <?php echo $zones; ?>;
  var fontawesome_icons = ["500px","adjust","adn","align-center","align-justify","align-left","align-right","amazon","ambulance","anchor","android","angellist","angle-double-down","angle-double-left","angle-double-right","angle-double-up","angle-down","angle-left","angle-right","angle-up","apple","archive","area-chart","arrow-circle-down","arrow-circle-left","arrow-circle-o-down","arrow-circle-o-left","arrow-circle-o-right","arrow-circle-o-up","arrow-circle-right","arrow-circle-up","arrow-down","arrow-left","arrow-right","arrow-up","arrows","arrows-alt","arrows-h","arrows-v","asterisk","at","automobile","backward","balance-scale","ban","bank","bar-chart","bar-chart-o","barcode","bars","battery-0","battery-1","battery-2","battery-3","battery-4","battery-empty","battery-full","battery-half","battery-quarter","battery-three-quarters","bed","beer","behance","behance-square","bell","bell-o","bell-slash","bell-slash-o","bicycle","binoculars","birthday-cake","bitbucket","bitbucket-square","bitcoin","black-tie","bluetooth","bluetooth-b","bold","bolt","bomb","book","bookmark","bookmark-o","briefcase","btc","bug","building","building-o","bullhorn","bullseye","bus","buysellads","cab","calculator","calendar","calendar-check-o","calendar-minus-o","calendar-o","calendar-plus-o","calendar-times-o","camera","camera-retro","car","caret-down","caret-left","caret-right","caret-square-o-down","caret-square-o-left","caret-square-o-right","caret-square-o-up","caret-up","cart-arrow-down","cart-plus","cc","cc-amex","cc-diners-club","cc-discover","cc-jcb","cc-mastercard","cc-paypal","cc-stripe","cc-visa","certificate","chain","chain-broken","check","check-circle","check-circle-o","check-square","check-square-o","chevron-circle-down","chevron-circle-left","chevron-circle-right","chevron-circle-up","chevron-down","chevron-left","chevron-right","chevron-up","child","chrome","circle","circle-o","circle-o-notch","circle-thin","clipboard","clock-o","clone","close","cloud","cloud-download","cloud-upload","cny","code","code-fork","codepen","codiepie","coffee","cog","cogs","columns","comment","comment-o","commenting","commenting-o","comments","comments-o","compass","compress","connectdevelop","contao","copy","copyright","creative-commons","credit-card","credit-card-alt","crop","crosshairs","css3","cube","cubes","cut","cutlery","dashboard","dashcube","database","dedent","delicious","desktop","deviantart","diamond","digg","dollar","dot-circle-o","download","dribbble","dropbox","drupal","edge","edit","eject","ellipsis-h","ellipsis-v","empire","envelope","envelope-o","envelope-square","eraser","eur","euro","exchange","exclamation","exclamation-circle","exclamation-triangle","expand","expeditedssl","external-link","external-link-square","eye","eye-slash","eyedropper","facebook","facebook-f","facebook-official","facebook-square","fast-backward","fast-forward","fax","feed","female","fighter-jet","file","file-archive-o","file-audio-o","file-code-o","file-excel-o","file-image-o","file-movie-o","file-o","file-pdf-o","file-photo-o","file-picture-o","file-powerpoint-o","file-sound-o","file-text","file-text-o","file-video-o","file-word-o","file-zip-o","files-o","film","filter","fire","fire-extinguisher","firefox","flag","flag-checkered","flag-o","flash","flask","flickr","floppy-o","folder","folder-o","folder-open","folder-open-o","font","fonticons","fort-awesome","forumbee","forward","foursquare","frown-o","futbol-o","gamepad","gavel","gbp","ge","gear","gears","genderless","get-pocket","gg","gg-circle","gift","git","git-square","github","github-alt","github-square","gittip","glass","globe","google","google-plus","google-plus-square","google-wallet","graduation-cap","gratipay","group","h-square","hacker-news","hand-grab-o","hand-lizard-o","hand-o-down","hand-o-left","hand-o-right","hand-o-up","hand-paper-o","hand-peace-o","hand-pointer-o","hand-rock-o","hand-scissors-o","hand-spock-o","hand-stop-o","hashtag","hdd-o","header","headphones","heart","heart-o","heartbeat","history","home","hospital-o","hotel","hourglass","hourglass-1","hourglass-2","hourglass-3","hourglass-end","hourglass-half","hourglass-o","hourglass-start","houzz","html5","i-cursor","ils","image","inbox","indent","industry","info","info-circle","inr","instagram","institution","internet-explorer","intersex","ioxhost","italic","joomla","jpy","jsfiddle","key","keyboard-o","krw","language","laptop","lastfm","lastfm-square","leaf","leanpub","legal","lemon-o","level-down","level-up","life-bouy","life-buoy","life-ring","life-saver","lightbulb-o","line-chart","link","linkedin","linkedin-square","linux","list","list-alt","list-ol","list-ul","location-arrow","lock","long-arrow-down","long-arrow-left","long-arrow-right","long-arrow-up","magic","magnet","mail-forward","mail-reply","mail-reply-all","male","map","map-marker","map-o","map-pin","map-signs","mars","mars-double","mars-stroke","mars-stroke-h","mars-stroke-v","maxcdn","meanpath","medium","medkit","meh-o","mercury","microphone","microphone-slash","minus","minus-circle","minus-square","minus-square-o","mixcloud","mobile","mobile-phone","modx","money","moon-o","mortar-board","motorcycle","mouse-pointer","music","navicon","neuter","newspaper-o","object-group","object-ungroup","odnoklassniki","odnoklassniki-square","opencart","openid","opera","optin-monster","outdent","pagelines","paint-brush","paper-plane","paper-plane-o","paperclip","paragraph","paste","pause","pause-circle","pause-circle-o","paw","paypal","pencil","pencil-square","pencil-square-o","percent","phone","phone-square","photo","picture-o","pie-chart","pied-piper","pied-piper-alt","pinterest","pinterest-p","pinterest-square","plane","play","play-circle","play-circle-o","plug","plus","plus-circle","plus-square","plus-square-o","power-off","print","product-hunt","puzzle-piece","qq","qrcode","question","question-circle","quote-left","quote-right","ra","random","rebel","recycle","reddit","reddit-alien","reddit-square","refresh","registered","remove","renren","reorder","repeat","reply","reply-all","retweet","rmb","road","rocket","rotate-left","rotate-right","rouble","rss","rss-square","rub","ruble","rupee","safari","save","scissors","scribd","search","search-minus","search-plus","sellsy","send","send-o","server","share","share-alt","share-alt-square","share-square","share-square-o","shekel","sheqel","shield","ship","shirtsinbulk","shopping-bag","shopping-basket","shopping-cart","sign-in","sign-out","signal","simplybuilt","sitemap","skyatlas","skype","slack","sliders","slideshare","smile-o","soccer-ball-o","sort","sort-alpha-asc","sort-alpha-desc","sort-amount-asc","sort-amount-desc","sort-asc","sort-desc","sort-down","sort-numeric-asc","sort-numeric-desc","sort-up","soundcloud","space-shuttle","spinner","spoon","spotify","square","square-o","stack-exchange","stack-overflow","star","star-half","star-half-empty","star-half-full","star-half-o","star-o","steam","steam-square","step-backward","step-forward","stethoscope","sticky-note","sticky-note-o","stop","stop-circle","stop-circle-o","street-view","strikethrough","stumbleupon","stumbleupon-circle","subscript","subway","suitcase","sun-o","superscript","support","table","tablet","tachometer","tag","tags","tasks","taxi","television","tencent-weibo","terminal","text-height","text-width","th","th-large","th-list","thumb-tack","thumbs-down","thumbs-o-down","thumbs-o-up","thumbs-up","ticket","times","times-circle","times-circle-o","tint","toggle-down","toggle-left","toggle-off","toggle-on","toggle-right","toggle-up","trademark","train","transgender","transgender-alt","trash","trash-o","tree","trello","tripadvisor","trophy","truck","try","tty","tumblr","tumblr-square","turkish-lira","tv","twitch","twitter","twitter-square","umbrella","underline","undo","university","unlink","unlock","unlock-alt","unsorted","upload","usb","usd","user","user-md","user-plus","user-secret","user-times","users","venus","venus-double","venus-mars","viacoin","video-camera","vimeo","vimeo-square","vine","vk","volume-down","volume-off","volume-up","warning","wechat","weibo","weixin","whatsapp","wheelchair","wifi","wikipedia-w","windows","won","wordpress","wrench","xing","xing-square","y-combinator","y-combinator-square","yahoo","yc","yc-square","yelp","yen","youtube","youtube-play","youtube-square"];
  var $offers_recommended_form = $('#offers_recommended_form');
  function applyZoneEffects($container){
    $('.active-zone .fontawesome-icons-select', $container).select2_4({
      placeholder:'Alegeti iconitele',
      multiple:true,
      theme:'bootstrap',
      width: '100%',
      data: fontawesome_icons,
      templateSelection: function(icon){
        return '<i class="fa fw fa-' + icon.id + '"></i> ';
      },
      templateResult: function(icon){
        return '<i class="fa fw fa-' + icon.id + '"></i> ' + icon.id;
      },
      initSelection: function(element, callback) {
        var id = $(element).val();
        if (id !== "") {
          var index = fontawesome_icons.indexOf(id);
          if(index>-1){
            var fontawesome_icon = fontawesome_icons[index];
            callback(fontawesome_icon);
          }
        }
      },
      escapeMarkup: function(markup) {
        return markup;
      }
    });
  }
  $('.offers-sortable').sortable({
    revert: true,
    items: ">.offers-sortable-item",
    handle: ".move-offer",
    start: function(e, ui){
      ui.placeholder.width(ui.item.width());
      ui.placeholder.height(ui.item.height());
    }
  });
  applyZoneEffects($offers_recommended_form);
  $offers_recommended_form.on('click', '#recommended_add_zone', function(e){
    e.preventDefault();
    console.log('click', this);
    var $new_tr = $('#offers_recommended_form_models > div').clone();
    $('>.card', $new_tr).addClass('active-zone');
    $('>.card>.card-header>h2>strong', $new_tr).text(zone_index+1);
    $('[id*="_0_"]', $new_tr).each(function(){
      $(this).attr('id',$(this).attr('id').replace('_0_','_' + (zone_index + 1) + '_'));
    });
    $('[for*="_0_"]', $new_tr).each(function(){
      $(this).attr('for',$(this).attr('for').replace('_0_','_' + (zone_index + 1) + '_'));
    });
    $('[name$="[-1]"]', $new_tr).each(function(){
      $(this).attr('name',$(this).attr('name').replace('[-1]','[' + zone_index + ']'));
    });
    zone_index++;
    $new_tr.insertBefore($(this).parent());
    applyZoneEffects($new_tr);
    return false;
  }).on('click', '.recommended-remove-zone', function(e){
    e.preventDefault();
    console.log('click', this);
    $(this).closest('.card').parent().remove();
    return false;
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>