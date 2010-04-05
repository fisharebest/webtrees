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

	LICENSZ FELTÉTELEK:

	A ClearBox szabadon felhasználható bármilyen nem kereskedelmi jellegû honlapon, tehát olyanokon
	amik nem hivatalos cégek oldalai, amik nem tartalmaznak kereskedelmi jellegû szolgáltatást vagy
	termék(ek) eladás(á)t, illetve reklámozását. A kereskedelmi jellegû honlapokon való
	felhasználásáról érdeklõdj a készítõnél! A ClearBox forrása (clsource.js) kizárólag a készítõ
	elõzetes hozzájárulásával módosítható. A ClearBox a készítõ beleegyezése nélkül pénzért harmadik
	félnek tovább nem adható!
*/
		var CB_version='200beta';

/*
	clearbox.js:

		ClearBox konfigurációs fájl. Ebben a fájlban tudod kedved szerint testre szabni a scriptet.
		Az 1.70 verziótól kezdve a ClearBox script két külön .js fájlra tagolódott: clearbox.js és
		cbsource.js. A cbsource.js fájl kódolva és tömörítve tartalmazza a ClearBox scriptet, azt
		ne változtasd! A dokumentációt külön fájlok helyett a clearbox.js (ez a fájl) tartalmazza.
		Minden paraméternél leírást találsz, hogy az hogyan befolyásolja a ClearBox mûködését.
		
	CB_HideColor:
	
		Értéke egy színkód. Megadja, hogy a script meghívásakor a dokumentumot 'eltakaró'
		felület milyen színárnyalatú legyen. Mindenképpen használd a ' ' jeleket és a #-et. 
*/
		var CB_HideColor='#000';

/*
	CB_HideOpacity:
	
		Értéke egy nem negatív egész szám (0-100). Megadja, hogy a háttér hány százalékig
		vegye fel a CB_HideColor színkódot. Ha 0, akkor egyáltalán nem fog látszódni, ha 100,
		akkor teljesen elfedi a dokumentumot. A köztes esetekben pedig áttetszõ lesz. 
*/
		var CB_HideOpacity=75;

/*
	CB_OpacityStep:

		Értéke egy pozitív egész szám (1-CB_HideOpacity). Megadja, hogy a háttér elõtûnése,
		halványítása mekkora lépésekben történjen, amíg el nem éri a CB_HideOpacity értéket.
		(Ne legyen nagyobb, mint CB_HideOpacity és nem árt, ha CB_HideOpacity maradéktalanul
		osztható CB_OpacityStep-pel!)

*/
		var CB_OpacityStep=25;
		
/*
	CB_WinBaseW:

		Értéke egy pozitív egész szám. (25- ) A ClearBox ablak kezdeti szélessége
		(ehhez még hozzáadódik a CB_RoundPix értéknek a kétszerese).
		Ne adj meg túl nagy értéket! Ha nem létfontosságú, ne változtass az alapértéken!
*/		
		var CB_WinBaseW=120;
		
/*
	CB_WinBaseH:
	
		Értéke egy pozitív egész szám. (50- ) A ClearBox ablak kezdeti magassága
		(ehhez még hozzáadódik a CB_RoundPix értéknek a kétszerese, viszont beletartozik a
		CB_TextH értéke.). Ne adj meg túl nagy értéket! Ha nem létfontosságú, ne változtass az
		alapértéken!
*/
		var CB_WinBaseH=110;

/*
	CB_WinPadd:

		Értéke egy nem negatív egész szám. (0- ) Megadja, hogy normális esetben minimum hány
		pixel legyen a böngészõ széle és a ClearBox ablak között. Ne adj meg túl nagy értéket! 
		Megjegyzés: alapállapotban a ClearBox ablakot körülvevõ árnyék miatt ez az érték nagyobbnak
		néz ki, mint ami meg	van adva!)
*/		
		var CB_WinPadd=1;

/*
	CB_RoundPix:
	
		Értéke nem negatív egész szám. Megadja a lekerekítések képeinek nagyságát.
		Fontos: Változtatásához a lekerekítések képeit is cserélni kell, ezért ennek a megváltoztatása
		csak a jobban hozzértõknek ajánlott!
		Tipp: Amennyiben azt szeretnéd, hogy a ClearBox ablak sarkai szögletesek legyenek, állítsd az
		értéket 0-ra. (Ilyenkor nem árt megnövelni a CB_Padd értékét minimum 5-re, a szebb vizuális
		megjelenés érdekében.)
*/
		var CB_RoundPix=12;

/*
	CB_Animation:
	
		A ClearBox ablak animációja állítható be vele. Többféle lehetõség közül választhatsz:

			'none': 		ilyenkor az ablak egy lépésben felveszi a méretet,
			'normal':		ez a már megszokott animáció,
			'double':		ilyenkor az ablak egyszerre méretezõdik X és Y irányban
			'warp':		mint a double, de a kép még az animáció megkezdése elõtt megjelenik
						(nagy a cpu igénye, ezért kisebb méretû képekhez ajánlott)
		
		Mindenképpen használd a ' ' jeleket.
*/
//		var CB_Animation='none';

/*
	CB_Jump_X:
		
		Értéke egy pozitív egész szám (1-99). Megadja a vízszintes átméretezõ animáció gyorsaságát, 
		részletességét.
*/
		var CB_Jump_X=	40;

/*
	CB_Jump_Y:
		
		Értéke egy pozitív egész szám (1-99). Megadja a függõleges átméretezõ animáció gyorsaságát, 
		részletességét.
*/
		var CB_Jump_Y=	40;

/*
	CB_AnimTimeout:
	
		Értéke egy pozitív egész szám (5- ). Megadja (milisecundumban), hogy átméretezõ animáció lépései
		közben	mennyit 'várjon' a script.
*/
		var CB_AnimTimeout=5;

/*
	CB_ImgBorder:
	
		Értéke egy nem negatív egész szám (0- ). Megadja a kép körüli keret vastagságát.
*/
		var CB_ImgBorder=1;
		
/*
	CB_ImgBorderColor:
	
		Értéke egy színkód. Megadja a kép körüli keret színét. Mindenképpen használd a ' ' jeleket és a #-et.
*/
		var CB_ImgBorderColor='#ccc';

/*
	CB_Padd:
	
		Értéke egy nem negatív egész szám (0- ). A kép keretén (és a lekerekítéseken) kívül ad még egy plusz
		– gyakorlatilag láthatatlan - keretet a képnek (magyarul a kép körüli fehér keret vastagságát növeli).
*/
		var CB_Padd=0;

/*
	CB_ShowImgURL:
	
		Értéke lehet 'be' vagy 'ki'. Megadja, hogy a ClearBox ablakban megjelenjen-e a kép elérési útja, amennyiben
		nincs cím megadva a képnek. Mindenképpen használd a ' ' jeleket.
*/
		var CB_ShowImgURL='be';

/*
	CB_ImgNum:
	
		Értéke lehet 'be' vagy 'ki'. Megadja, hogy ha a ClearBox galériáknál legyen-e kijelezve az összes kép, illetve,
		hogy éppen hányadik	kép van megjelenítve. Mindenképpen használd a ' ' jeleket.
*/
		var CB_ImgNum='ki';

/*
	CB_ImgNumBracket:

		Ha a CB_ImgNum be van kapcsolva, akkor ez adja meg, hogy milyen zárójelben legyen a képek számozásának a
		kijelzése. Mindenképpen használd a ' ' jeleket és mindenképpen két, azaz 2 karaktert adj meg, szünet nélkül.
*/
		var CB_ImgNumBracket='[]';

/*
	CB_SlShowTime:
	
		Értéke egy pozitív egész szám (1- ). Megadja, hogy a SlideShow effekt (1.6 verziótól) mennyit várjon
		(másodpercben), mielõtt megjeleníti a következõ képet. Megjegyzés: sajnos ez csak közelítõleges értéket ad,
		valójában a böngészõtõl is függ.
*/
//		var CB_SlShowTime=6;

/*
	CB_PadT:
	
		Értéke egy nem negatív egész szám (0- ). A szövegmezõ kép aljától való távolságát adja meg.
		Fontos: használd okosan ezt a paramétert a CB_TextH értékkel. A CB_PaddT a CB_TextH-ból mindenképpen
		levonódik, így könnyen hibát generálhatunk a nem megfelelõ értékadással!
*/
//		var CB_PadT=10;
		var CB_PadT=8;		

/*
	CB_TextH:
	
		Értéke egy pozitív egész szám (25- ). A kép alatti szövegmezõ magasságát adja meg.
		Fontos: figyelj a CB_PadT megfelelõ értékére is!
*/
//		var CB_TextH=40;
		var CB_TextH=30;		
		
/*
	CB_Font:
		
		Értéke egy (vagy több) betûtípus. A kép alatti szöveg betûtípusát adja meg.
		Használhatunk egyszerre a CSS-ben is alkalmazható több értéket, pl: 'Arial, Verdana, Tahoma'.
		Mindenképpen használd a ' ' jeleket.
*/
		var CB_Font='arial';

/*
	Cb_FontSize:
	
		Értéke egy pozitív egész szám (6- ). A kép alatti szöveg méretét adja meg.
*/
		var CB_FontSize=12;
		
/*
	CB_FontColor:
	
		Értéke egy színkód. A kép alatti szöveg színét adja meg. Mindenképpen használd a ' ' jeleket és a #-et. 
*/
//		var CB_FontColor='#656565';
		var CB_FontColor='#0000FF';		

/*
	CB_FontWeight:
	
		Értéke lehet 'normal' vagy 'bold'. A kép alatti szöveg betûvastagságát adja meg.
		Mindenképpen használd a ' ' jeleket.
*/
		var CB_FontWeigth='bold';

/*
	CB_CheckDuplicates:
	
		Késõbbi funkció, jelenleg nem mûködik!
		Értéke lehet 'be' vagy 'ki'. Megadja, hogy legyen-e a gelériákban ismétlõdés-ellenõrzés. Amennyiben be van
		kapcsolva, úgy agy adott galériában ugyanazzal az elérési úttal egyszerre csak egy kép lehet, tehát véletlenül
		sem történik ismétlõdés. Mindenképpen használd a ' ' jeleket.
*/
		var CB_CheckDuplicates='ki';

/*
	CB_LoadingText:
	
		Megadja, hogy alul milyen szöveg jelenjen meg a képek betöltése közben. Mindenképpen használd a ' ' jeleket.
*/
//		var CB_LoadingText='- kép betöltése -';
		var CB_LoadingText='- >  < -';
		
/*
	CB_PicDir:
	
		Megadja a ClearBox-hoz tartozó képek elérési útját. Amennyiben megváltoztatod, a clearbox.css-ben se felejtsd el
		végrehajtani a szükséges változtatásokat! Mindenképpen használd a ' ' jeleket.
*/
		var CB_PicDir='modules/lightbox/pic';
//		var CB_MusicDir='../music';

/*
	CB_BodyMargin paraméterek:
	
		Amennyiben az oldaladon szándékozol konkrét értéket adni a BODY margin-jának, tehát ha:
		margin-left, margin-top, margin-bottom vagy margin-right BÁRMELYIKE NEM 0 és NEM auto, akkor az alábbi
		paraméterekkel	meg KELL adnod azt a ClearBox-nak is. Ez a HideContent Layer pontos méretezése miatt szükséges!
		Megjegyzés: figyelj arra, hogy ha nem adtál meg a css fájlodban a body-nak egyáltalán margin értéket, akkor is van egy
		alapértelmezett margin érték a böngészõk szerint!
		Fontos: NE használj %-os értékeket, mert  akkor a ClearBox HideContent Layer-e nem fog megfelelõen megjelenni!
*/
		var CB_BodyMarginLeft=0;
		var CB_BodyMarginRight=0;
		var CB_BodyMarginTop=0;
		var CB_BodyMarginBottom=0;

/*
	CB_Preload:
	
		Értéke lehet 'be' vagy 'ki'. Megadja, hogy galériáknál legyen-e az elõzõ és következõ képek elõre töltése.
		Mindenképpen használd a ' ' jeleket.
*/
		var CB_Preload='be';

/*
	CB_TextNav:
	
		Értéke lehet 'be' vagy 'ki'. Megadja, hogy galériáknál legyen-e elõzõ és következõ felirat megjelenítése a CB_TextH
		sávban (ahol a kép neve is található). Mindenképpen használd a ' ' jeleket.
*/
		var CB_TextNav='ki';

/*
	CB_NavText paraméterek:
	
		Megadhatod a CB_TextH megjelenõ gombok (linkek) nevét. Mindenképpen használd a ' ' jeleket.
		A linkek kinézetét a clearbox.css fájlban tudod módosítani, a linkek class osztálya: CB_TextNav.
*/
//BH		var CB_NavTextPrv='elõzõ';
//BH		var CB_NavTextNxt='következõ';
//BH		var CB_NavTextCls='bezár';
		var CB_NavTextPrv='Prev';
		var CB_NavTextNxt='Next';
//BH		var CB_NavTextCls='';		

/*
	CB_Picture paraméterek:
	
		Ha meg akarod változtatni a ClearBox által használt Start, Pause és Close gombokat, valamint a Loading képet,
		akkor azt itt teheted meg. Fontos: kizárólag a fájlnevet add meg, / jel és mappa nélkül!
		Mindenképpen használd a ' ' jeleket.
		Megjegyzés: az Elõzõ és Következõ gombokat a clearbox.css-ben tudod megváltoztatni.

*/
		var CB_PictureStart='start.png';
		var CB_PicturePause='pause.png';
		var CB_PictureClose='close_red.png';
		var CB_PictureLoading='loading.gif';
		var CB_PictureNotes='notes.gif';
		var CB_PictureDetails='detail.gif';
		var CB_MusicStart='music_off.png';		
		var CB_MusicStop='music_on.png';
		var CB_MusicNull='music_null.png';
		var CB_Speak='music_off.png';
		var CB_ZoomStart='zoom_on.png';
		var CB_ZoomStop='zoom_off.png';
		

// Slideshow music configurable options ---------------------------------------------------------
	var foreverLoop 				= 0;	// Set 0 if want to stop on the last image or Set it to 1 for Infinite loop feature
	var loopMusic					= true; //loops music if it is shorter then slideshow
	var SoundBridgeSWF 				=  "modules/lightbox/js/SoundBridge.swf";	
	
// Music variables ---------------------------------------------------------
	var slideshowMusic = null;
	var firstTime = 1;
	var objSpeakerImage;
	var saveLoopMusic;

		
/*
	Az alábbi kódon ne változtass, ellenkezõ esetben a ClearBox nem (megfelelõen) fog mûködni!.
	clearbox.js elérési útjának megkeresése, majd ez alapján cbsource.js beillesztése a html dokumentumba:
*/

		var CB_Scripts = document.getElementsByTagName('script');
		for(i=0;i<CB_Scripts.length;i++){
			if(CB_Scripts[i].src.match('clearbox.js')!=null){
				var CB_jsdir=CB_Scripts[i].src.substring(0,CB_Scripts[i].src.length-11);
			}
		}
		document.write('<' + 'script');
		document.write(' language="javascript"');
		document.write(' type="text/javascript"');
		document.write(' src="'+CB_jsdir+'clsource_music.js">');
		document.write('</' + 'script' + '>');
