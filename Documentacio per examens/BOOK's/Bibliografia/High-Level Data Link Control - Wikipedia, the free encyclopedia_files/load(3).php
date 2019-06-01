mw.loader.implement("ext.uls.init",function(){(function($,mw){'use strict';if(mw.hook===undefined){mw.hook=(function(){var lists={},slice=Array.prototype.slice;return function(name){var list=lists[name]||(lists[name]=$.Callbacks('memory'));return{add:list.add,remove:list.remove,fire:function(){return list.fireWith(null,slice.call(arguments));}};};}());}mw.uls=mw.uls||{};mw.uls.previousLanguagesCookie='uls-previous-languages';mw.uls.previousLanguageAutonymCookie='uls-previous-language-autonym';mw.uls.languageSettingsModules=['ext.uls.inputsettings','ext.uls.displaysettings'];mw.uls.languageSelectionMethod=undefined;mw.uls.addEventLoggingTriggers=function(){mw.uls.languageSelectionMethod=undefined;$('#map-block').on('click',function(){mw.uls.languageSelectionMethod='map';});$('#languagefilter').on('keydown',function(){if($(this).val()===''){mw.uls.languageSelectionMethod='search';}});$('#uls-lcd-quicklist a').on('click',function(){mw.uls.languageSelectionMethod='common';});};mw.uls.
changeLanguage=function(language){var uri=new mw.Uri(window.location.href),deferred=new $.Deferred();deferred.done(function(){uri.extend({setlang:language});window.location.href=uri.toString();});mw.hook('mw.uls.interface.language.change').fire(language,deferred);window.setTimeout(function(){deferred.resolve();},mw.config.get('wgULSEventLogging')*500);};mw.uls.setPreviousLanguages=function(previousLanguages){$.cookie(mw.uls.previousLanguagesCookie,$.toJSON(previousLanguages),{path:'/'});};mw.uls.getPreviousLanguages=function(){var previousLanguages=$.cookie(mw.uls.previousLanguagesCookie);if(!previousLanguages){return[];}return $.parseJSON(previousLanguages).slice(-5);};mw.uls.insertPreviousLanguage=function(prevLangCode){var previousLanguages=mw.uls.getPreviousLanguages()||[],currentLangIndex;currentLangIndex=$.inArray(prevLangCode,previousLanguages);if(currentLangIndex<0){previousLanguages.push(prevLangCode);}else{previousLanguages.splice(currentLangIndex,1);previousLanguages.push(
prevLangCode);}mw.uls.setPreviousLanguages(previousLanguages);};mw.uls.getBrowserLanguage=function(){return(window.navigator.language||window.navigator.userLanguage||'').split('-')[0];};mw.uls.getCountryCode=function(){return window.Geo&&(window.Geo.country||window.Geo.country_code);};mw.uls.getAcceptLanguageList=function(){return mw.config.get('wgULSAcceptLanguageList')||[];};mw.uls.getFrequentLanguageList=function(countryCode){var unique=[],list=[mw.config.get('wgUserLanguage'),mw.config.get('wgContentLanguage'),mw.uls.getBrowserLanguage()].concat(mw.uls.getPreviousLanguages()).concat(mw.uls.getAcceptLanguageList());countryCode=countryCode||mw.uls.getCountryCode();if(countryCode){list=list.concat($.uls.data.getLanguagesInTerritory(countryCode));}$.each(list,function(i,v){if($.inArray(v,unique)===-1){unique.push(v);}});unique=$.grep(unique,function(langCode){var target;if($.fn.uls.defaults.languages[langCode]!==undefined){return true;}target=$.uls.data.isRedirect(langCode);if(target){
return $.fn.uls.defaults.languages[target]!==undefined;}return false;});return unique;};function isBrowserSupported(){var blacklist={'msie':[['<=',7]]};if(parseInt(mw.config.get('wgVersion').split('.')[1],'10')<22){return!/MSIE [67]/i.test(navigator.userAgent);}return!$.client.test(blacklist,null,true);}mw.uls.init=function(callback){if(!isBrowserSupported()){$('#pt-uls').hide();return;}if(callback){callback.call(this);}};$(document).ready(function(){mw.uls.init();});}(jQuery,mediaWiki));;},{"css":[
".uls-menu a{cursor:pointer}.uls-menu.callout .caret-before{border-top:20px solid transparent;border-right:20px solid #C9C9C9;border-bottom:20px solid transparent;display:inline-block;left:-21px;top:30px;position:absolute}.uls-menu.callout .caret-after{border-top:20px solid transparent;border-right:20px solid #FCFCFC;border-bottom:20px solid transparent;display:inline-block;left:-20px;top:30px;position:absolute}.uls-ui-languages button{width:23%;text-overflow:ellipsis;margin-right:4%}button.uls-more-languages{width:auto}.settings-title{font-size:11pt}.settings-text{color:#555555;font-size:9pt}div.display-settings-block:hover .settings-text{color:#252525}\n/* cache key: enwiki:resourceloader:filter:minify-css:7:22d1681fa868b4ff4fbcb1ec1e58a9ea */"]},{});mw.loader.implement("ext.uls.webfonts",function(){(function($,mw){'use strict';var ulsPreferences,tofuSalt='\u0D00',tofuLanguages={};mw.webfonts=mw.webfonts||{};ulsPreferences=mw.uls.preferences();mw.webfonts.preferences={registry:{fonts
:{},webfontsEnabled:mw.config.get('wgULSWebfontsEnabled')},isEnabled:function(){return this.registry.webfontsEnabled;},enable:function(){this.registry.webfontsEnabled=true;},disable:function(){this.registry.webfontsEnabled=false;},setFont:function(language,font){this.registry.fonts[language]=font;},getFont:function(language){return this.registry.fonts[language];},save:function(callback){ulsPreferences=mw.uls.preferences();ulsPreferences.set('webfonts',this.registry);ulsPreferences.save(callback);},load:function(){mw.webfonts.preferences.registry=$.extend(this.registry,ulsPreferences.get('webfonts'));}};function detectTofu(text){var index,$fixture,width={},height={},length=Math.min(4,text.length),detected=false;if($.client.test({msie:false})){text=tofuSalt+text;}$fixture=$('<span>').css({fontSize:'72px',fontFamily:'sans-serif'}).appendTo('body');for(index=0;index<length;index++){$fixture.text(text[index]);width[index]=$fixture.width()||width[index-1];height[index]=$fixture.height();if(
index>0&&(width[index]!==width[index-1]||height[index]!==height[index-1])){detected=false;break;}}$fixture.remove();if(index===length){detected=true;}return detected;}mw.webfonts.setup=function(){var mediawikiFontRepository=$.webfonts.repository;mediawikiFontRepository.base=mw.config.get('wgULSFontRepositoryBasePath');$.extend($.fn.webfonts.defaults,{repository:mediawikiFontRepository,fontStack:$('body').css('font-family').split(/, /g),exclude:mw.config.get('wgULSNoWebfontsSelectors').join(', ')});$.fn.webfonts.defaults=$.extend($.fn.webfonts.defaults,{fontSelector:function(repository,language,classes){var font,tofu,autonym,defaultFont;if(!language){return null;}defaultFont=repository.defaultFont(language);if(classes&&$.inArray('autonym',classes)>=0){autonym=true;}font=mw.webfonts.preferences.getFont(language);if(!font||autonym){if((!defaultFont||defaultFont==='system')&&!autonym){return font;}tofu=tofuLanguages[language]||detectTofu($.uls.data.getAutonym(language));if(tofu){if(!
tofuLanguages[language]){mw.log('tofu detected for '+language);mw.hook('mw.uls.webfonts.tofudetected').fire(language);tofuLanguages[language]=true;}font=autonym?'Autonym':defaultFont;}else{font='system';}}if(font==='system'){font=null;}return font;},exclude:(function(){var excludes=$.fn.webfonts.defaults.exclude;if(mw.user.options.get('editfont')!=='default'){excludes=(excludes)?excludes+',textarea':'textarea';}return excludes;}())});setTimeout(function(){$('body').webfonts();$('body').data('webfonts').load('Autonym');},0);};$(document).ready(function(){mw.uls.init(function(){mw.webfonts.preferences.load();if(mw.webfonts.preferences.isEnabled()){mw.loader.using('ext.uls.webfonts.fonts',mw.webfonts.setup);}});});}(jQuery,mediaWiki));;},{},{});mw.loader.implement("jquery.tipsy",function(){(function($){function maybeCall(thing,ctx){return(typeof thing=='function')?(thing.call(ctx)):thing;};function fixTitle($ele){if($ele.attr('title')||typeof($ele.attr('original-title'))!='string'){$ele.
attr('original-title',$ele.attr('title')||'').removeAttr('title');}}function Tipsy(element,options){this.$element=$(element);this.options=options;this.enabled=true;fixTitle(this.$element);}Tipsy.prototype={show:function(){var title=this.getTitle();if(title&&this.enabled){var $tip=this.tip();$tip.find('.tipsy-inner')[this.options.html?'html':'text'](title);$tip[0].className='tipsy';if(this.options.className){$tip.addClass(maybeCall(this.options.className,this.$element[0]));}$tip.remove().css({top:0,left:0,visibility:'hidden',display:'block'}).appendTo(document.body);var pos=$.extend({},this.$element.offset(),{width:this.$element[0].offsetWidth,height:this.$element[0].offsetHeight});var actualWidth=$tip[0].offsetWidth,actualHeight=$tip[0].offsetHeight;var gravity=(typeof this.options.gravity=='function')?this.options.gravity.call(this.$element[0]):this.options.gravity;var tp;switch(gravity.charAt(0)){case'n':tp={top:pos.top+pos.height+this.options.offset,left:pos.left+pos.width/2-
actualWidth/2};break;case's':tp={top:pos.top-actualHeight-this.options.offset,left:pos.left+pos.width/2-actualWidth/2};break;case'e':tp={top:pos.top+pos.height/2-actualHeight/2,left:pos.left-actualWidth-this.options.offset};break;case'w':tp={top:pos.top+pos.height/2-actualHeight/2,left:pos.left+pos.width+this.options.offset};break;}if(gravity.length==2){if(gravity.charAt(1)=='w'){if(this.options.center){tp.left=pos.left+pos.width/2-15;}else{tp.left=pos.left;}}else{if(this.options.center){tp.left=pos.left+pos.width/2-actualWidth+15;}else{tp.left=pos.left+pos.width;}}}$tip.css(tp).addClass('tipsy-'+gravity);if(this.options.fade){$tip.stop().css({opacity:0,display:'block',visibility:'visible'}).animate({opacity:this.options.opacity},100);}else{$tip.css({visibility:'visible',opacity:this.options.opacity});}}},hide:function(){if(this.options.fade){this.tip().stop().fadeOut(100,function(){$(this).remove();});}else{this.tip().remove();}},getTitle:function(){var title,$e=this.$element,o=this.
options;fixTitle($e);if(typeof o.title=='string'){title=$e.attr(o.title=='title'?'original-title':o.title);}else if(typeof o.title=='function'){title=o.title.call($e[0]);}title=(''+title).replace(/(^\s*|\s*$)/,"");return title||o.fallback;},tip:function(){if(!this.$tip){this.$tip=$('<div class="tipsy"></div>').html('<div class="tipsy-arrow"></div><div class="tipsy-inner"/></div>');}return this.$tip;},validate:function(){if(!this.$element[0].parentNode){this.hide();this.$element=null;this.options=null;}},enable:function(){this.enabled=true;},disable:function(){this.enabled=false;},toggleEnabled:function(){this.enabled=!this.enabled;}};$.fn.tipsy=function(options){if(options===true){return this.data('tipsy');}else if(typeof options=='string'){return this.data('tipsy')[options]();}options=$.extend({},$.fn.tipsy.defaults,options);function get(ele){var tipsy=$.data(ele,'tipsy');if(!tipsy){tipsy=new Tipsy(ele,$.fn.tipsy.elementOptions(ele,options));$.data(ele,'tipsy',tipsy);}return tipsy;}
function enter(){var tipsy=get(this);tipsy.hoverState='in';if(options.delayIn==0){tipsy.show();}else{setTimeout(function(){if(tipsy.hoverState=='in')tipsy.show();},options.delayIn);}};function leave(){var tipsy=get(this);tipsy.hoverState='out';if(options.delayOut==0){tipsy.hide();}else{setTimeout(function(){if(tipsy.hoverState=='out')tipsy.hide();},options.delayOut);}};if(!options.live)this.each(function(){get(this);});if(options.trigger!='manual'){var binder=options.live?'live':'bind',eventIn=options.trigger=='hover'?'mouseenter':'focus',eventOut=options.trigger=='hover'?'mouseleave':'blur';this[binder](eventIn,enter)[binder](eventOut,leave);}return this;};$.fn.tipsy.defaults={className:null,delayIn:0,delayOut:0,fade:true,fallback:'',gravity:'n',center:true,html:false,live:false,offset:0,opacity:1.0,title:'title',trigger:'hover'};$.fn.tipsy.elementOptions=function(ele,options){return $.metadata?$.extend({},options,$(ele).metadata()):options;};$.fn.tipsy.autoNS=function(){return $(this
).offset().top>($(document).scrollTop()+$(window).height()/2)?'s':'n';};$.fn.tipsy.autoWE=function(){return $(this).offset().left>($(document).scrollLeft()+$(window).width()/2)?'e':'w';};})(jQuery);;},{"css":[
".tipsy{padding:5px;position:absolute;z-index:100000;cursor:default}.tipsy-inner{padding:5px 8px 4px 8px; background-color:#ffffff;border:solid 1px #a7d7f9;color:black;max-width:15em;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px; }.tipsy-arrow{position:absolute;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAALAgMAAADUwp+1AAAACVBMVEX5+fmn1/n///9s6BFKAAAAAXRSTlMAQObYZgAAACpJREFUCB1jZBD4wMiQMoeRcUU4I9uSaYxSE54xZjn8AtMgPkgcJA9UBwAeDw1Qrb3pVAAAAABJRU5ErkJggg==) no-repeat top left;background:url(//bits.wikimedia.org/static-1.23wmf20/resources/jquery.tipsy/images/tipsy.png?2014-03-27T16:43:20Z) no-repeat top left!ie;width:11px;height:6px} .tipsy-n .tipsy-arrow{top:0px;left:50%;margin-left:-5px} .tipsy-nw .tipsy-arrow{top:1px;left:10px} .tipsy-ne .tipsy-arrow{top:1px;right:10px} .tipsy-s .tipsy-arrow{bottom:0px;left:50%;margin-left:-5px;background-position:bottom left} .tipsy-sw .tipsy-arrow{bottom:0px;left:10px;background-position:bottom left} .tipsy-se .tipsy-arrow{bottom:0px;right:10px;background-position:bottom left} .tipsy-e .tipsy-arrow{top:50%;margin-top:-5px;right:1px;width:5px;height:11px;background-position:top right} .tipsy-w .tipsy-arrow{top:50%;margin-top:-5px;left:0px;width:6px;height:11px}\n/* cache key: enwiki:resourceloader:filter:minify-css:7:abbe7fb1179238c85880a681895b0551 */"
]},{});
/* cache key: enwiki:resourceloader:filter:minify-js:7:d5f3dbda55954a72f4d3aa7e60a411b4 */