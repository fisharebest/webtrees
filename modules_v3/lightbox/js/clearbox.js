/* clearbox.js - Author Brian Holland .... email webman@windmillway.f2s.com    -  (modified from Clearbox.js - Author Pyro ... email pyrex@chello.hu)
 * @package webtrees
 * @subpackage Module
 * @version $Id$
 * @author Brian Holland
*/

/*
	ClearBox JS by pyro
	
	script home:		http://www.kreatura.hu/pyro/clearbox
	author e-mail:		pyrexkukacchelloponthu
	author msn:		prokukacradiomaxponthu
	support forum 1:	http://www.sg.hu/listazas.php3?id=1172325655
	support forum 2:	http://www.tutorial.hu/forum/index.php?showtopic=1391&st=0

	LICENSZ FELT?TELEK:

	A ClearBox szabadon felhaszn?lhat? b?rmilyen nem kereskedelmi jelleg? honlapon, teh?t olyanokon
	amik nem hivatalos c?gek oldalai, amik nem tartalmaznak kereskedelmi jelleg? szolg?ltat?st vagy
	term?k(ek) elad?s(?)t, illetve rekl?moz?s?t. A kereskedelmi jelleg? honlapokon val?
	felhaszn?l?s?r?l ?rdekl?dj a k?sz?t?n?l! A ClearBox forr?sa (clsource.js) kiz?r?lag a k?sz?t?
	el?zetes hozz?j?rul?s?val m?dos?that?. A ClearBox a k?sz?t? beleegyez?se n?lk?l p?nz?rt harmadik
	f?lnek tov?bb nem adhat?!
*/
		var CB_version='200beta';

/*
	clearbox.js:

		ClearBox konfigur?ci?s f?jl. Ebben a f?jlban tudod kedved szerint testre szabni a scriptet.
		Az 1.70 verzi?t?l kezdve a ClearBox script k?t k?l?n .js f?jlra tagol?dott: clearbox.js ?s
		cbsource.js. A cbsource.js f?jl k?dolva ?s t?m?r?tve tartalmazza a ClearBox scriptet, azt
		ne v?ltoztasd! A dokument?ci?t k?l?n f?jlok helyett a clearbox.js (ez a f?jl) tartalmazza.
		Minden param?tern?l le?r?st tal?lsz, hogy az hogyan befoly?solja a ClearBox m?k?d?s?t.
		
	CB_HideColor:
	
		?rt?ke egy sz?nk?d. Megadja, hogy a script megh?v?sakor a dokumentumot 'eltakar?'
		fel?let milyen sz?n?rnyalat? legyen. Mindenk?ppen haszn?ld a ' ' jeleket ?s a #-et. 
*/
		var CB_HideColor='#000';

/*
	CB_HideOpacity:
	
		?rt?ke egy nem negat?v eg?sz sz?m (0-100). Megadja, hogy a h?tt?r h?ny sz?zal?kig
		vegye fel a CB_HideColor sz?nk?dot. Ha 0, akkor egy?ltal?n nem fog l?tsz?dni, ha 100,
		akkor teljesen elfedi a dokumentumot. A k?ztes esetekben pedig ?ttetsz? lesz. 
*/
		var CB_HideOpacity=75;

/*
	CB_OpacityStep:

		?rt?ke egy pozit?v eg?sz sz?m (1-CB_HideOpacity). Megadja, hogy a h?tt?r el?t?n?se,
		halv?ny?t?sa mekkora l?p?sekben t?rt?njen, am?g el nem ?ri a CB_HideOpacity ?rt?ket.
		(Ne legyen nagyobb, mint CB_HideOpacity ?s nem ?rt, ha CB_HideOpacity marad?ktalanul
		oszthat? CB_OpacityStep-pel!)

*/
		var CB_OpacityStep=25;
		
/*
	CB_WinBaseW:

		?rt?ke egy pozit?v eg?sz sz?m. (25- ) A ClearBox ablak kezdeti sz?less?ge
		(ehhez m?g hozz?ad?dik a CB_RoundPix ?rt?knek a k?tszerese).
		Ne adj meg t?l nagy ?rt?ket! Ha nem l?tfontoss?g?, ne v?ltoztass az alap?rt?ken!
*/		
		var CB_WinBaseW=120;
		
/*
	CB_WinBaseH:
	
		?rt?ke egy pozit?v eg?sz sz?m. (50- ) A ClearBox ablak kezdeti magass?ga
		(ehhez m?g hozz?ad?dik a CB_RoundPix ?rt?knek a k?tszerese, viszont beletartozik a
		CB_TextH ?rt?ke.). Ne adj meg t?l nagy ?rt?ket! Ha nem l?tfontoss?g?, ne v?ltoztass az
		alap?rt?ken!
*/
		var CB_WinBaseH=110;

/*
	CB_WinPadd:

		?rt?ke egy nem negat?v eg?sz sz?m. (0- ) Megadja, hogy norm?lis esetben minimum h?ny
		pixel legyen a b?ng?sz? sz?le ?s a ClearBox ablak k?z?tt. Ne adj meg t?l nagy ?rt?ket! 
		Megjegyz?s: alap?llapotban a ClearBox ablakot k?r?lvev? ?rny?k miatt ez az ?rt?k nagyobbnak
		n?z ki, mint ami meg	van adva!)
*/		
		var CB_WinPadd=1;

/*
	CB_RoundPix:
	
		?rt?ke nem negat?v eg?sz sz?m. Megadja a lekerek?t?sek k?peinek nagys?g?t.
		Fontos: V?ltoztat?s?hoz a lekerek?t?sek k?peit is cser?lni kell, ez?rt ennek a megv?ltoztat?sa
		csak a jobban hozz?rt?knek aj?nlott!
		Tipp: Amennyiben azt szeretn?d, hogy a ClearBox ablak sarkai sz?gletesek legyenek, ?ll?tsd az
		?rt?ket 0-ra. (Ilyenkor nem ?rt megn?velni a CB_Padd ?rt?k?t minimum 5-re, a szebb vizu?lis
		megjelen?s ?rdek?ben.)
*/
		var CB_RoundPix=12;

/*
	CB_Animation:
	
		A ClearBox ablak anim?ci?ja ?ll?that? be vele. T?bbf?le lehet?s?g k?z?l v?laszthatsz:

			'none': 		ilyenkor az ablak egy l?p?sben felveszi a m?retet,
			'normal':		ez a m?r megszokott anim?ci?,
			'double':		ilyenkor az ablak egyszerre m?retez?dik X ?s Y ir?nyban
			'warp':		mint a double, de a k?p m?g az anim?ci? megkezd?se el?tt megjelenik
						(nagy a cpu ig?nye, ez?rt kisebb m?ret? k?pekhez aj?nlott)
		
		Mindenk?ppen haszn?ld a ' ' jeleket.
*/
//		var CB_Animation='none';

/*
	CB_Jump_X:
		
		?rt?ke egy pozit?v eg?sz sz?m (1-99). Megadja a v?zszintes ?tm?retez? anim?ci? gyorsas?g?t, 
		r?szletess?g?t.
*/
		var CB_Jump_X=	40;

/*
	CB_Jump_Y:
		
		?rt?ke egy pozit?v eg?sz sz?m (1-99). Megadja a f?gg?leges ?tm?retez? anim?ci? gyorsas?g?t, 
		r?szletess?g?t.
*/
		var CB_Jump_Y=	40;

/*
	CB_AnimTimeout:
	
		?rt?ke egy pozit?v eg?sz sz?m (5- ). Megadja (milisecundumban), hogy ?tm?retez? anim?ci? l?p?sei
		k?zben	mennyit 'v?rjon' a script.
*/
		var CB_AnimTimeout=5;

/*
	CB_ImgBorder:
	
		?rt?ke egy nem negat?v eg?sz sz?m (0- ). Megadja a k?p k?r?li keret vastags?g?t.
*/
		var CB_ImgBorder=1;
		
/*
	CB_ImgBorderColor:
	
		?rt?ke egy sz?nk?d. Megadja a k?p k?r?li keret sz?n?t. Mindenk?ppen haszn?ld a ' ' jeleket ?s a #-et.
*/
		var CB_ImgBorderColor='#ccc';

/*
	CB_Padd:
	
		?rt?ke egy nem negat?v eg?sz sz?m (0- ). A k?p keret?n (?s a lekerek?t?seken) k?v?l ad m?g egy plusz
		? gyakorlatilag l?thatatlan - keretet a k?pnek (magyarul a k?p k?r?li feh?r keret vastags?g?t n?veli).
*/
		var CB_Padd=0;

/*
	CB_ShowImgURL:
	
		?rt?ke lehet 'be' vagy 'ki'. Megadja, hogy a ClearBox ablakban megjelenjen-e a k?p el?r?si ?tja, amennyiben
		nincs c?m megadva a k?pnek. Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_ShowImgURL='be';

/*
	CB_ImgNum:
	
		?rt?ke lehet 'be' vagy 'ki'. Megadja, hogy ha a ClearBox gal?ri?kn?l legyen-e kijelezve az ?sszes k?p, illetve,
		hogy ?ppen h?nyadik	k?p van megjelen?tve. Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_ImgNum='ki';

/*
	CB_ImgNumBracket:

		Ha a CB_ImgNum be van kapcsolva, akkor ez adja meg, hogy milyen z?r?jelben legyen a k?pek sz?moz?s?nak a
		kijelz?se. Mindenk?ppen haszn?ld a ' ' jeleket ?s mindenk?ppen k?t, azaz 2 karaktert adj meg, sz?net n?lk?l.
*/
		var CB_ImgNumBracket='[]';

/*
	CB_SlShowTime:
	
		?rt?ke egy pozit?v eg?sz sz?m (1- ). Megadja, hogy a SlideShow effekt (1.6 verzi?t?l) mennyit v?rjon
		(m?sodpercben), miel?tt megjelen?ti a k?vetkez? k?pet. Megjegyz?s: sajnos ez csak k?zel?t?leges ?rt?ket ad,
		val?j?ban a b?ng?sz?t?l is f?gg.
*/
//		var CB_SlShowTime=6;

/*
	CB_PadT:
	
		?rt?ke egy nem negat?v eg?sz sz?m (0- ). A sz?vegmez? k?p alj?t?l val? t?vols?g?t adja meg.
		Fontos: haszn?ld okosan ezt a param?tert a CB_TextH ?rt?kkel. A CB_PaddT a CB_TextH-b?l mindenk?ppen
		levon?dik, ?gy k?nnyen hib?t gener?lhatunk a nem megfelel? ?rt?kad?ssal!
*/
//		var CB_PadT=10;
		var CB_PadT=8;		

/*
	CB_TextH:
	
		?rt?ke egy pozit?v eg?sz sz?m (25- ). A k?p alatti sz?vegmez? magass?g?t adja meg.
		Fontos: figyelj a CB_PadT megfelel? ?rt?k?re is!
*/
//		var CB_TextH=40;
		var CB_TextH=30;		
		
/*
	CB_Font:
		
		?rt?ke egy (vagy t?bb) bet?t?pus. A k?p alatti sz?veg bet?t?pus?t adja meg.
		Haszn?lhatunk egyszerre a CSS-ben is alkalmazhat? t?bb ?rt?ket, pl: 'Arial, Verdana, Tahoma'.
		Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_Font='arial';

/*
	Cb_FontSize:
	
		?rt?ke egy pozit?v eg?sz sz?m (6- ). A k?p alatti sz?veg m?ret?t adja meg.
*/
		var CB_FontSize=12;
		
/*
	CB_FontColor:
	
		?rt?ke egy sz?nk?d. A k?p alatti sz?veg sz?n?t adja meg. Mindenk?ppen haszn?ld a ' ' jeleket ?s a #-et. 
*/
//		var CB_FontColor='#656565';
		var CB_FontColor='#0000FF';		

/*
	CB_FontWeight:
	
		?rt?ke lehet 'normal' vagy 'bold'. A k?p alatti sz?veg bet?vastags?g?t adja meg.
		Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_FontWeigth='bold';

/*
	CB_CheckDuplicates:
	
		K?s?bbi funkci?, jelenleg nem m?k?dik!
		?rt?ke lehet 'be' vagy 'ki'. Megadja, hogy legyen-e a gel?ri?kban ism?tl?d?s-ellen?rz?s. Amennyiben be van
		kapcsolva, ?gy agy adott gal?ri?ban ugyanazzal az el?r?si ?ttal egyszerre csak egy k?p lehet, teh?t v?letlen?l
		sem t?rt?nik ism?tl?d?s. Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_CheckDuplicates='ki';

/*
	CB_LoadingText:
	
		Megadja, hogy alul milyen sz?veg jelenjen meg a k?pek bet?lt?se k?zben. Mindenk?ppen haszn?ld a ' ' jeleket.
*/
//		var CB_LoadingText='- k?p bet?lt?se -';
		var CB_LoadingText='- >  < -';
		
/*
	CB_PicDir:
	
		Megadja a ClearBox-hoz tartoz? k?pek el?r?si ?tj?t. Amennyiben megv?ltoztatod, a clearbox.css-ben se felejtsd el
		v?grehajtani a sz?ks?ges v?ltoztat?sokat! Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_PicDir=WT_STATIC_URL+WT_MODULES_DIR+'lightbox/pic';
//		var CB_MusicDir='../music';

/*
	CB_BodyMargin param?terek:
	
		Amennyiben az oldaladon sz?nd?kozol konkr?t ?rt?ket adni a BODY margin-j?nak, teh?t ha:
		margin-left, margin-top, margin-bottom vagy margin-right B?RMELYIKE NEM 0 ?s NEM auto, akkor az al?bbi
		param?terekkel	meg KELL adnod azt a ClearBox-nak is. Ez a HideContent Layer pontos m?retez?se miatt sz?ks?ges!
		Megjegyz?s: figyelj arra, hogy ha nem adt?l meg a css f?jlodban a body-nak egy?ltal?n margin ?rt?ket, akkor is van egy
		alap?rtelmezett margin ?rt?k a b?ng?sz?k szerint!
		Fontos: NE haszn?lj %-os ?rt?keket, mert  akkor a ClearBox HideContent Layer-e nem fog megfelel?en megjelenni!
*/
		var CB_BodyMarginLeft=0;
		var CB_BodyMarginRight=0;
		var CB_BodyMarginTop=0;
		var CB_BodyMarginBottom=0;

/*
	CB_Preload:
	
		?rt?ke lehet 'be' vagy 'ki'. Megadja, hogy gal?ri?kn?l legyen-e az el?z? ?s k?vetkez? k?pek el?re t?lt?se.
		Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_Preload='be';

/*
	CB_TextNav:
	
		?rt?ke lehet 'be' vagy 'ki'. Megadja, hogy gal?ri?kn?l legyen-e el?z? ?s k?vetkez? felirat megjelen?t?se a CB_TextH
		s?vban (ahol a k?p neve is tal?lhat?). Mindenk?ppen haszn?ld a ' ' jeleket.
*/
		var CB_TextNav='ki';

/*
	CB_NavText param?terek:
	
		Megadhatod a CB_TextH megjelen? gombok (linkek) nev?t. Mindenk?ppen haszn?ld a ' ' jeleket.
		A linkek kin?zet?t a clearbox.css f?jlban tudod m?dos?tani, a linkek class oszt?lya: CB_TextNav.
*/
//BH		var CB_NavTextPrv='el?z?';
//BH		var CB_NavTextNxt='k?vetkez?';
//BH		var CB_NavTextCls='bez?r';
		var CB_NavTextPrv='Prev';
		var CB_NavTextNxt='Next';
//BH		var CB_NavTextCls='';		

/*
	CB_Picture param?terek:
	
		Ha meg akarod v?ltoztatni a ClearBox ?ltal haszn?lt Start, Pause ?s Close gombokat, valamint a Loading k?pet,
		akkor azt itt teheted meg. Fontos: kiz?r?lag a f?jlnevet add meg, / jel ?s mappa n?lk?l!
		Mindenk?ppen haszn?ld a ' ' jeleket.
		Megjegyz?s: az El?z? ?s K?vetkez? gombokat a clearbox.css-ben tudod megv?ltoztatni.

*/
		var CB_PictureStart='start.png';
		var CB_PicturePause='pause.png';
		var CB_PictureClose='close_red.png';
		var CB_PictureLoading='loading.gif';
		var CB_PictureNotes='notes.png';
		var CB_PictureDetails='detail.png';
		var CB_MusicStart='music_off.png';		
		var CB_MusicStop='music_on.png';
		var CB_MusicNull='music_null.png';
		var CB_Speak='music_off.png';
		var CB_ZoomStart='zoom_on.png';
		var CB_ZoomStop='zoom_off.png';
		

// Slideshow music configurable options ---------------------------------------------------------
	var foreverLoop 				= 0;	// Set 0 if want to stop on the last image or Set it to 1 for Infinite loop feature
	var loopMusic					= true; //loops music if it is shorter then slideshow
	var SoundBridgeSWF 				=  WT_STATIC_URL+WT_MODULES_DIR+"lightbox/js/SoundBridge.swf";	
	
// Music variables ---------------------------------------------------------
	var slideshowMusic = null;
	var firstTime = 1;
	var objSpeakerImage;
	var saveLoopMusic;
