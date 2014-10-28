<?php
// Class to support internationalisation (i18n) functionality.
//
// We use gettext to provide translation.  You should configure xgettext to
// search for:
// translate()
// plural()
//
// We wrap the Zend_Translate gettext library, to allow us to add extra
// functionality, such as mixed RTL and LTR text.
//
// Copyright (C) 2014 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use Patchwork\TurkishUtf8;
use WT\Auth;

/**
 * Class WT_I18N - library of useful functions for locales and translation
 */
class WT_I18N {
	// Digits are always rendered LTR, even in RTL text.
	const DIGITS = '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹';

	// Reversable character conversions from the UNICODE 5.1 database.
	// It excludes ambiguous (turkish dotless i) and mixed-case (Dz) characters.
	// The characters should be arranged in default unicode-collation order.
	const ALPHABET_LOWER = 'aàáâãäåāăąǎǟǡǻȁȃȧḁạảấầẩẫậắằẳẵặⓐａæǣǽbḃḅḇⓑｂƀɓƃcçćĉċčḉⅽⓒｃƈdďḋḍḏḑḓⅾⓓｄǆǳđɖɗƌðeèéêëēĕėęěȅȇȩḕḗḙḛḝẹẻẽếềểễệⓔｅǝəɛfḟⓕｆƒgĝğġģǧǵḡⓖｇǥɠɣƣhĥȟḣḥḧḩḫⓗｈƕħiìíîïĩīĭįǐȉȋḭḯỉịⅰⓘｉⅱⅲĳⅳⅸɨɩjĵⓙｊkķǩḱḳḵⓚｋƙlĺļľḷḹḻḽⅼⓛｌŀǉłƚmḿṁṃⅿⓜｍnñńņňǹṅṇṉṋⓝｎǌɲƞŋoòóôõöōŏőơǒǫǭȍȏȫȭȯȱṍṏṑṓọỏốồổỗộớờởỡợⓞｏœøǿɔɵȣpṕṗⓟｐƥqⓠｑrŕŗřȑȓṙṛṝṟⓡｒʀsśŝşšșṡṣṥṧṩⓢｓʃtţťțṫṭṯṱⓣｔŧƭʈuùúûüũūŭůűųưǔǖǘǚǜȕȗṳṵṷṹṻụủứừửữựⓤｕʉɯʊvṽṿⅴⓥｖⅵⅶⅷʋʌwŵẁẃẅẇẉⓦｗxẋẍⅹⓧｘⅺⅻyýÿŷȳẏỳỵỷỹⓨｙƴzźżžẑẓẕⓩｚƶȥǯʒƹȝþƿƨƽƅάαἀἁἂἃἄἅἆἇὰάᾀᾁᾂᾃᾄᾅᾆᾇᾰᾱᾳβγδέεἐἑἒἓἔἕὲέϝϛζήηἠἡἢἣἤἥἦἧὴήᾐᾑᾒᾓᾔᾕᾖᾗῃθϊἰἱἲἳἴἵἶἷὶίῐῑκϗλμνξοόὀὁὂὃὄὅὸόπϟϙρῥσϲτυϋύὑὓὕὗὺύῠῡφχψωώὠὡὢὣὤὥὦὧὼώᾠᾡᾢᾣᾤᾥᾦᾧῳϡϸϻϣϥϧϩϫϭϯаӑӓәӛӕбвгґғҕдԁђԃѓҙеѐёӗєжӂӝҗзԅӟѕӡԇиѝӣҋӥіїйјкқӄҡҟҝлӆљԉмӎнӊңӈҥњԋоӧөӫпҧҁрҏсԍҫтԏҭћќуӯўӱӳүұѹфхҳһѡѿѽѻцҵчӵҷӌҹҽҿџшщъыӹьҍѣэӭюяѥѧѫѩѭѯѱѳѵѷҩաբգդեզէըթժիլխծկհձղճմյնշոչպջռսվտրցւփքօֆȼɂɇɉɋɍɏͱͳͷͻͼͽӏӷӻӽӿԑԓԕԗԙԛԝԟԡԣԥᵹᵽỻỽỿⅎↄⰰⰱⰲⰳⰴⰵⰶⰷⰸⰹⰺⰻⰼⰽⰾⰿⱀⱁⱂⱃⱄⱅⱆⱇⱈⱉⱊⱋⱌⱍⱎⱏⱐⱑⱒⱓⱔⱕⱖⱗⱘⱙⱚⱛⱜⱝⱞⱡⱨⱪⱬⱳⱶⲁⲃⲅⲇⲉⲋⲍⲏⲑⲓⲕⲗⲙⲛⲝⲟⲡⲣⲥⲧⲩⲫⲭⲯⲱⲳⲵⲷⲹⲻⲽⲿⳁⳃⳅⳇⳉⳋⳍⳏⳑⳓⳕⳗⳙⳛⳝⳟⳡⳣⳬⳮⴀⴁⴂⴃⴄⴅⴆⴇⴈⴉⴊⴋⴌⴍⴎⴏⴐⴑⴒⴓⴔⴕⴖⴗⴘⴙⴚⴛⴜⴝⴞⴟⴠⴡⴢⴣⴤⴥꙁꙃꙅꙇꙉꙋꙍꙏꙑꙓꙕꙗꙙꙛꙝꙟꙣꙥꙧꙩꙫꙭꚁꚃꚅꚇꚉꚋꚍꚏꚑꚓꚕꚗꜣꜥꜧꜩꜫꜭꜯꜳꜵꜷꜹꜻꜽꜿꝁꝃꝅꝇꝉꝋꝍꝏꝑꝓꝕꝗꝙꝛꝝꝟꝡꝣꝥꝧꝩꝫꝭꝯꝺꝼꝿꞁꞃꞅꞇꞌ';
	const ALPHABET_UPPER = 'AÀÁÂÃÄÅĀĂĄǍǞǠǺȀȂȦḀẠẢẤẦẨẪẬẮẰẲẴẶⒶＡÆǢǼBḂḄḆⒷＢɃƁƂCÇĆĈĊČḈⅭⒸＣƇDĎḊḌḎḐḒⅮⒹＤǄǱĐƉƊƋÐEÈÉÊËĒĔĖĘĚȄȆȨḔḖḘḚḜẸẺẼẾỀỂỄỆⒺＥƎƏƐFḞⒻＦƑGĜĞĠĢǦǴḠⒼＧǤƓƔƢHĤȞḢḤḦḨḪⒽＨǶĦIÌÍÎÏĨĪĬĮǏȈȊḬḮỈỊⅠⒾＩⅡⅢĲⅣⅨƗƖJĴⒿＪKĶǨḰḲḴⓀＫƘLĹĻĽḶḸḺḼⅬⓁＬĿǇŁȽMḾṀṂⅯⓂＭNÑŃŅŇǸṄṆṈṊⓃＮǊƝȠŊOÒÓÔÕÖŌŎŐƠǑǪǬȌȎȪȬȮȰṌṎṐṒỌỎỐỒỔỖỘỚỜỞỠỢⓄＯŒØǾƆƟȢPṔṖⓅＰƤQⓆＱRŔŖŘȐȒṘṚṜṞⓇＲƦSŚŜŞŠȘṠṢṤṦṨⓈＳƩTŢŤȚṪṬṮṰⓉＴŦƬƮUÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛȔȖṲṴṶṸṺỤỦỨỪỬỮỰⓊＵɄƜƱVṼṾⅤⓋＶⅥⅦⅧƲɅWŴẀẂẄẆẈⓌＷXẊẌⅩⓍＸⅪⅫYÝŸŶȲẎỲỴỶỸⓎＹƳZŹŻŽẐẒẔⓏＺƵȤǮƷƸȜÞǷƧƼƄΆΑἈἉἊἋἌἍἎἏᾺΆᾈᾉᾊᾋᾌᾍᾎᾏᾸᾹᾼΒΓΔΈΕἘἙἚἛἜἝῈΈϜϚΖΉΗἨἩἪἫἬἭἮἯῊΉᾘᾙᾚᾛᾜᾝᾞᾟῌΘΪἸἹἺἻἼἽἾἿῚΊῘῙΚϏΛΜΝΞΟΌὈὉὊὋὌὍῸΌΠϞϘΡῬΣϹΤΥΫΎὙὛὝὟῪΎῨῩΦΧΨΩΏὨὩὪὫὬὭὮὯῺΏᾨᾩᾪᾫᾬᾭᾮᾯῼϠϷϺϢϤϦϨϪϬϮАӐӒӘӚӔБВГҐҒҔДԀЂԂЃҘЕЀЁӖЄЖӁӜҖЗԄӞЅӠԆИЍӢҊӤІЇЙЈКҚӃҠҞҜЛӅЉԈМӍНӉҢӇҤЊԊОӦӨӪПҦҀРҎСԌҪТԎҬЋЌУӮЎӰӲҮҰѸФХҲҺѠѾѼѺЦҴЧӴҶӋҸҼҾЏШЩЪЫӸЬҌѢЭӬЮЯѤѦѪѨѬѮѰѲѴѶҨԱԲԳԴԵԶԷԸԹԺԻԼԽԾԿՀՁՂՃՄՅՆՇՈՉՊՋՌՍՎՏՐՑՒՓՔՕՖȻɁɆɈɊɌɎͰͲͶϽϾϿӀӶӺӼӾԐԒԔԖԘԚԜԞԠԢԤꝽⱣỺỼỾℲↃⰀⰁⰂⰃⰄⰅⰆⰇⰈⰉⰊⰋⰌⰍⰎⰏⰐⰑⰒⰓⰔⰕⰖⰗⰘⰙⰚⰛⰜⰝⰞⰟⰠⰡⰢⰣⰤⰥⰦⰧⰨⰩⰪⰫⰬⰭⰮⱠⱧⱩⱫⱲⱵⲀⲂⲄⲆⲈⲊⲌⲎⲐⲒⲔⲖⲘⲚⲜⲞⲠⲢⲤⲦⲨⲪⲬⲮⲰⲲⲴⲶⲸⲺⲼⲾⳀⳂⳄⳆⳈⳊⳌⳎⳐⳒⳔⳖⳘⳚⳜⳞⳠⳢⳫⳭႠႡႢႣႤႥႦႧႨႩႪႫႬႭႮႯႰႱႲႳႴႵႶႷႸႹႺႻႼႽႾႿჀჁჂჃჄჅꙀꙂꙄꙆꙈꙊꙌꙎꙐꙒꙔꙖꙘꙚꙜꙞꙢꙤꙦꙨꙪꙬꚀꚂꚄꚆꚈꚊꚌꚎꚐꚒꚔꚖꜢꜤꜦꜨꜪꜬꜮꜲꜴꜶꜸꜺꜼꜾꝀꝂꝄꝆꝈꝊꝌꝎꝐꝒꝔꝖꝘꝚꝜꝞꝠꝢꝤꝦꝨꝪꝬꝮꝹꝻꝾꞀꞂꞄꞆꞋ';

	// Alphabet for the currently selected locale
	private static $alphabet_lower = 'abcdefghijklmnopqrstuvwxyz';
	private static $alphabet_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	// Lookup table to convert unicode code-points into scripts.
	// See https://en.wikipedia.org/wiki/Unicode_block
	// Note: we only need details for scripts of languages into which webtrees is translated.
	private static $scripts = array(
		array('Latn', 0x0041, 0x005A), // a-z
		array('Latn', 0x0061, 0x007A), // A-Z
		array('Latn', 0x0100, 0x02AF),
		array('Grek', 0x0370, 0x03FF),
		array('Cyrl', 0x0400, 0x052F),
		array('Hebr', 0x0590, 0x05FF),
		array('Arab', 0x0600, 0x06FF),
		array('Arab', 0x0750, 0x077F),
		array('Arab', 0x08A0, 0x08FF),
		array('Deva', 0x0900, 0x097F),
		array('Taml', 0x0B80, 0x0BFF),
		array('Sinh', 0x0D80, 0x0DFF),
		array('Thai', 0x0E00, 0x0E7F),
		array('Geor', 0x10A0, 0x10FF),
		array('Grek', 0x1F00, 0x1FFF),
		array('Deva', 0xA8E0, 0xA8FF),
		array('Hans', 0x3000, 0x303F), // Mixed CJK, not just Hans
		array('Hans', 0x3400, 0xFAFF), // Mixed CJK, not just Hans
		array('Hans', 0x20000, 0x2FA1F), // Mixed CJK, not just Hans
	);

	// Characters that are displayed in mirror form in RTL text.
	private static $mirror_characters = array(
		'(' => ')',
		')' => '(',
		'[' => ']',
		']' => '[',
		'{' => '}',
		'}' => '{',
		'<' => '>',
		'>' => '<',
		'‹' => '›',
		'›' => '‹',
		'«' => '»',
		'»' => '«',
		'﴾' => '﴿',
		'﴿' => '﴾',
		'“' => '”',
		'”' => '“',
		'‘' => '’',
		'’' => '‘',
	);

	/** @var string[] The names of all currently supported languages */
	private static $language_data = array(
		'af' => array('Latn', 'Afrikaans'),
		'ar' => array('Arab', 'العربية'),
		'bg' => array('Cyrl', 'български'),
		'bs' => array('Latn', 'bosanski'),
		'ca' => array('Latn', 'català'),
		'cs' => array('Latn', 'čeština'),
		'da' => array('Latn', 'dansk'),
		'de' => array('Latn', 'Deutsch'),
		'dv' => array('Thaa', 'ދިވެހިބަސް'),
		'el' => array('Grek', 'Ελληνικά'),
		'en' => array('Latn', 'English'),
		'en-AU' => array('Latn', 'Australian English'),
		'en-GB' => array('Latn', 'British English'),
		'en-US' => array('Latn', 'U.S. English'),
		'es' => array('Latn', 'español'),
		'et' => array('Latn', 'eesti'),
		'fa' => array('Arab', 'فارسی'),
		'fi' => array('Latn', 'suomi'),
		'fo' => array('Latn', 'føroyskt'),
		'fr' => array('Latn', 'français'),
		'fr-CA' => array('Latn', 'français canadien'),
		'gl' => array('Latn', 'galego'),
		'haw' => array('Latn', 'ʻŌlelo Hawaiʻi'),
		'he' => array('Hebr', 'עברית'),
		'hr' => array('Latn', 'hrvatski'),
		'hu' => array('Latn', 'magyar'),
		'id' => array('Latn', 'Bahasa Indonesia'),
		'is' => array('Latn', 'íslenska'),
		'it' => array('Latn', 'italiano'),
		'ja' => array('Kana', '日本語'),
		'ka' => array('Geor', 'ქართული'),
		'ko' => array('Kore', '한국어'),
		'lt' => array('Latn', 'lietuvių'),
		'lv' => array('Latn', 'latviešu'),
		'mi' => array('Latn', 'Māori'),
		'mr' => array('Mymr', 'मराठी'),
		'ms' => array('Latn', 'Bahasa Melayu'),
		'nb' => array('Latn', 'norsk bokmål'),
		'ne' => array('Deva', 'नेपाली'),
		'nl' => array('Latn', 'Nederlands'),
		'nn' => array('Latn', 'nynorsk'),
		'oc' => array('Latn', 'occitan'),
		'pl' => array('Latn', 'polski'),
		'pt' => array('Latn', 'português'),
		'pt-BR' => array('Latn', 'português do Brasil'),
		'ro' => array('Latn', 'română'),
		'ru' => array('Cyrl', 'русский'),
		'sk' => array('Latn', 'slovenčina'),
		'sl' => array('Latn', 'slovenščina'),
		'sr' => array('Cyrl', 'Српски'),
		'sr-Latn' => array('Latn', 'srpski'),
		'sv' => array('Latn', 'svenska'),
		'ta' => array('Taml', 'தமிழ்'),
		'tr' => array('Latn', 'Türkçe'),
		'tt' => array('Cyrl', 'Татар'),
		'uk' => array('Cyrl', 'українська'),
		'vi' => array('Latn', 'Tiếng Việt'),
		'yi' => array('Hebr', 'ייִדיש'),
		'zh' => array('Hans', '中文'),
		'zh-CN' => array('Hans', '简体中文'),
		'zh-TW' => array('Hant', '繁體中文'),
	);

	/** @var string the name of the current locale, such as fr or en_GB */
	public  static $locale;

	/** @var string The MySQL collation sequence used by this language, typically utf8_unicode_ci */
	public  static $collation;

	/** @var string Puncutation used to separate list items, typically a comma */
	public  static $list_separator;

	/** @var string Text direction; ltr or rtl */
	private static $dir;

	/** @var Zend_Cache_Core */
	private static $cache;

	/** @var string The numbering system used by this language; typically latin digits */
	private static $numbering_system;

	/** @var Zend_Translate */
	private static $translation_adapter;

	/**
	 * Initialise the translation adapter with a locale setting.
	 *
	 * @param string|null $locale If no locale specified, choose one automatically
	 *
	 * @return string $string
	 */
	public static function init($locale=null) {
		global $WT_SESSION, $WT_TREE;

		// The translation libraries only work with a cache.
		$cache_options = array(
			'automatic_serialization' => true,
			'cache_id_prefix'         => md5(WT_SERVER_NAME . WT_SCRIPT_PATH),
		);

		if (ini_get('apc.enabled')) {
			self::$cache = Zend_Cache::factory('Core', 'Apc', $cache_options, array());
		} elseif (WT_File::mkdir(WT_DATA_DIR . 'cache')) {
			self::$cache = Zend_Cache::factory('Core', 'File', $cache_options, array('cache_dir'=>WT_DATA_DIR . 'cache'));
		} else {
			self::$cache = Zend_Cache::factory('Core', 'Zend_Cache_Backend_BlackHole', $cache_options, array(), false, true);
		}

		Zend_Locale::setCache(self::$cache);
		Zend_Translate::setCache(self::$cache);

		$installed_languages=self::installed_languages();
		if (is_null($locale) || !array_key_exists($locale, $installed_languages)) {
			// Automatic locale selection.
			$locale = WT_Filter::get('lang');
			if ($locale && array_key_exists($locale, $installed_languages)) {
				// Requested in the URL?
				Auth::user()->setPreference('language', $locale);
			} elseif (array_key_exists($WT_SESSION->locale, $installed_languages)) {
				// Rembered from a previous visit?
				$locale = $WT_SESSION->locale;
			} else {
				// Browser preference takes priority over gedcom default
				if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					$prefs = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
				} else {
					$prefs = array();
				}
				if (WT_GED_ID) {
					// Add the tree’s default language as a low-priority
					$locale = $WT_TREE->getPreference('LANGUAGE');
					$prefs[] = $locale.';q=0.2';
				}
				$prefs2=array();
				foreach ($prefs as $pref) {
					list($l, $q)=explode(';q=', $pref.';q=1.0');
					$l=preg_replace_callback(
						'/_[a-z][a-z]$/',
						function($x) { return strtoupper($x[0]); },
						str_replace('-', '_', $l)
					); // en-gb => en_GB
					if (array_key_exists($l, $prefs2)) {
						$prefs2[$l]=max((float)$q, $prefs2[$l]);
					} else {
						$prefs2[$l]=(float)$q;
					}
				}
				// Ensure there is a fallback.
				if (!array_key_exists('en_US', $prefs2)) {
					$prefs2['en_US']=0.01;
				}
				arsort($prefs2);
				foreach (array_keys($prefs2) as $pref) {
					if (array_key_exists($pref, $installed_languages)) {
						$locale=$pref;
						break;
					}
				}
			}
		}

		// Load the translation file
		self::$translation_adapter = new Zend_Translate('gettext', WT_ROOT.'language/'.$locale.'.mo', $locale);

		// Deprecated - some custom modules use this to add translations
		Zend_Registry::set('Zend_Translate', self::$translation_adapter);

		// Load any local user translations
		if (is_dir(WT_DATA_DIR . 'language')) {
			if (file_exists(WT_DATA_DIR . 'language/' . $locale . '.mo')) {
				self::addTranslation(
					new Zend_Translate('gettext', WT_DATA_DIR.'language/' . $locale . '.mo', $locale)
				);
			}
			if (file_exists(WT_DATA_DIR.'language/' . $locale . '.php')) {
				self::addTranslation(
					new Zend_Translate('array', WT_DATA_DIR . 'language/' . $locale . '.php', $locale)
				);
			}
			if (file_exists(WT_DATA_DIR . 'language/' . $locale . '.csv')) {
				self::addTranslation(
					new Zend_Translate('csv', WT_DATA_DIR . 'language/' . $locale . '.csv', $locale)
				);
			}
		}

		// Extract language settings from the translation file
		global $DATE_FORMAT; // I18N: This is the format string for full dates.  See http://php.net/date for codes
		$DATE_FORMAT=self::noop('%j %F %Y');

		global $TIME_FORMAT; // I18N: This is the format string for the time-of-day.  See http://php.net/date for codes
		$TIME_FORMAT=self::noop('%H:%i:%s');

		// Alphabetic sorting sequence (upper-case letters), used by webtrees to sort strings
		list(, self::$alphabet_upper) = explode('=', self::noop('ALPHABET_upper=ABCDEFGHIJKLMNOPQRSTUVWXYZ'));
		// Alphabetic sorting sequence (lower-case letters), used by webtrees to sort strings
		list(, self::$alphabet_lower) = explode('=', self::noop('ALPHABET_lower=abcdefghijklmnopqrstuvwxyz'));

		global $WEEK_START; // I18N: This is the first day of the week on calendars. 0=Sunday, 1=Monday...
		list(, $WEEK_START) = explode('=', self::noop('WEEK_START=0'));

		global $TEXT_DIRECTION;
		$TEXT_DIRECTION = self::scriptDirection(self::languageScript($locale));

		self::$locale=$locale;
		self::$dir=$TEXT_DIRECTION;

		// I18N: This punctuation is used to separate lists of items.
		self::$list_separator=self::translate(', ');

		// I18N: This is the name of the MySQL collation that applies to your language.  A list is available at http://dev.mysql.com/doc/refman/5.0/en/charset-unicode-sets.html
		self::$collation=self::translate('utf8_unicode_ci');

		// Non-latin numbers may require non-latin digits
		try {
			self::$numbering_system = Zend_Locale_Data::getContent($locale, 'defaultnumberingsystem');
		} catch (Zend_Locale_Exception $ex) {
			// The latest CLDR database omits some languges such as Tatar (tt)
			self::$numbering_system = 'latin';
		}

		return $locale;
	}

	/**
	 * Add a translation file
	 *
	 * @param Zend_Translate $translation
	 */
	public static function addTranslation(Zend_Translate $translation) {
		self::$translation_adapter->getAdapter()->addTranslation(array('content' => $translation));
	}

	/**
	 * Check which languages are installed
	 *
	 * @return array
	 */
	public static function installed_languages() {
		$mo_files = glob(WT_ROOT.'language'.DIRECTORY_SEPARATOR.'*.mo');
		$cache_key = md5(serialize($mo_files));

		if (!($installed_languages = self::$cache->load($cache_key))) {
			$installed_languages = array();
			foreach ($mo_files as $mo_file) {
				if (preg_match('/^(([a-z][a-z][a-z]?)([-_][A-Z][A-Z])?([-_][A-Za-z]+)*)\.mo$/', basename($mo_file), $match)) {
					// Sort by the transation of the base language, then the variant.
					// e.g. English|British English, Portuguese|Brazilian Portuguese
					$tmp1 = self::languageName($match[1]);
					if ($match[1] == $match[2]) {
						$tmp2 = $tmp1;
					} else {
						$tmp2 = self::languageName($match[2]);
					}
					$installed_languages[$match[1]] = $tmp2 . '|' . $tmp1;
				}
			}
			if (empty($installed_languages)) {
				// We cannot translate this
				die('There are no languages installed.  You must include at least one xx.mo file in /language/');
			}
			// Sort by the combined language/language name...
			uasort($installed_languages, array('WT_I18N', 'strcasecmp'));
			foreach ($installed_languages as &$value) {
				// The locale database doesn't have translations for certain
				// "default" languages, such as zn_CH.
				if (substr($value, -1) == '|') {
					list($value,) = explode('|', $value);
				} else {
					list(,$value) = explode('|', $value);
				}
			}
			self::$cache->save($installed_languages, $cache_key);
		}
		return $installed_languages;
	}

	/**
	 * Generate i18n markup for the <html> tag, e.g. lang="ar" dir="rtl"
	 *
	 * @return string
	 */
	public static function html_markup() {
		$localeData=Zend_Locale_Data::getList(self::$locale, 'layout');
		$dir=$localeData['characterOrder']=='right-to-left' ? 'rtl' : 'ltr';
		list($lang) = preg_split('/[-_@]/', self::$locale);
		return 'lang="'.$lang.'" dir="'.$dir.'"';
	}

	/**
	 * Translate a number into the local representation.
	 *
	 * e.g. 12345.67 becomes
	 * en: 12,345.67
	 * fr: 12 345,67
	 * de: 12.345,67
	 *
	 * @param float   $n
	 * @param integer $precision
	 *
	 * @return string
	 */
	public static function number($n, $precision=0) {
		// Add "punctuation" and convert digits
		$n=Zend_Locale_Format::toNumber($n, array('locale'=>WT_LOCALE, 'precision'=>$precision));
		$n=self::digits($n);
		return $n;
	}

	/**
	 * Convert the digits 0-9 into the local script
	 *
	 * Used for years, etc., where we do not want thousands-separators, decimals, etc.
	 *
	 * @param integer $n
	 *
	 * @return string
	 */
	public static function digits($n) {
		if (self::$numbering_system != 'latn') {
			return Zend_Locale_Format::convertNumerals($n, 'latn', self::$numbering_system);
		} else {
			return $n;
		}
	}

	/**
	 * Translate a fraction into a percentage.
	 *
	 * e.g. 0.123 becomes
	 * en: 12.3%
	 * fr: 12,3 %
	 * de: 12,3%
	 *
	 * @param float   $n
	 * @param integer $precision
	 *
	 * @return string
	 */
	public static function percentage($n, $precision=0) {
		return
			/* I18N: This is a percentage, such as “32.5%”. “%s” is the number, “%%” is the percent symbol.  Some languages require a (non-breaking) space between the two, or a different symbol. */
			self::translate('%s%%', self::number($n*100.0, $precision));
	}

	/**
	 * Translate a string, and then substitute placeholders
	 *
	 * echo WT_I18N::translate('Hello World!');
	 * echo WT_I18N::translate('The %s sat on the mat', 'cat');
	 *
	 * @return string
	 */
	public static function translate(/* var_args */) {
		$args = func_get_args();
		$args[0] = self::$translation_adapter->getAdapter()->_($args[0]);

		return call_user_func_array('sprintf', $args);
	}

	/**
	 * Context sensitive version of translate.
	 *
	 * echo WT_I18N::translate_c('NOMINATIVE', 'January');
	 * echo WT_I18N::translate_c('GENITIVE',   'January');
	 *
	 * @return string
	 */
	public static function translate_c(/* var_args */) {
		$args = func_get_args();
		$msgid = $args[0] . "\x04" . $args[1];
		$msgtxt = self::$translation_adapter->getAdapter()->_($msgid);
		if ($msgtxt == $msgid) {
			$msgtxt = $args[1];
		}
		$args[0] = $msgtxt;
		unset($args[1]);

		return call_user_func_array('sprintf', $args);
	}

	/**
	 * Similar to translate, but do perform "no operation" on it.
	 *
	 * This is necessary to fetch a format string (containing % characters) without
	 * performing sustitution of arguments.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function noop($string) {
		return self::$translation_adapter->getAdapter()->_($string);
	}

	/**
	 * Translate a plural string
	 *
	 * echo self::plural('There is an error', 'There are errors', $num_errors);
	 * echo self::plural('There is one error', 'There are %s errors', $num_errors);
	 * echo self::plural('There is %1$d %2$s cat', 'There are %1$d %2$s cats', $num, $num, $colour);
	 *
	 * @return string
	 */
	public static function plural(/* var_args */) {
		$args = func_get_args();
		$string = self::$translation_adapter->getAdapter()->plural($args[0], $args[1], $args[2]);
		array_splice($args, 0, 3, array($string));

		return call_user_func_array('sprintf', $args);
	}

	/**
	 * Convert a GEDCOM age string into translated_text
	 *
	 * NB: The import function will have normalised this, so we don't need
	 * to worry about badly formatted strings
	 * NOTE: this function is not yet complete - eventually it will replace get_age_at_event()
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function gedcom_age($string) {
		switch ($string) {
		case 'STILLBORN':
			// I18N: Description of an individual’s age at an event.  For example, Died 14 Jan 1900 (stillborn)
			return self::translate('(stillborn)');
		case 'INFANT':
			// I18N: Description of an individual’s age at an event.  For example, Died 14 Jan 1900 (in infancy)
			return self::translate('(in infancy)');
		case 'CHILD':
			// I18N: Description of an individual’s age at an event.  For example, Died 14 Jan 1900 (in childhood)
			return self::translate('(in childhood)');
		}
		$age=array();
		if (preg_match('/(\d+)y/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$years=$match[1];
			$age[]=self::plural('%s year', '%s years', $years, self::number($years));
		} else {
			$years=-1;
		}
		if (preg_match('/(\d+)m/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$age[]=self::plural('%s month', '%s months', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)w/', $string, $match)) {
			// I18N: Part of an age string. e.g. 7 weeks and 3 days
			$age[]=self::plural('%s week', '%s weeks', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)d/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$age[]=self::plural('%s day', '%s days', $match[1], self::number($match[1]));
		}
		// If an age is just a number of years, only show the number
		if (count($age)==1 && $years>=0) {
			$age=$years;
		}
		if ($age) {
			if (!substr_compare($string, '<', 0, 1)) {
				// I18N: Description of an individual’s age at an event.  For example, Died 14 Jan 1900 (aged less than 21 years)
				return self::translate('(aged less than %s)', $age);
			} elseif (!substr_compare($string, '>', 0, 1)) {
				// I18N: Description of an individual’s age at an event.  For example, Died 14 Jan 1900 (aged more than 21 years)
				return self::translate('(aged more than %s)', $age);
			} else {
				// I18N: Description of an individual’s age at an event.  For example, Died 14 Jan 1900 (aged 43 years)
				return self::translate('(aged %s)', $age);
			}
		} else {
			// Not a valid string?
			return self::translate('(aged %s)', $string);
		}
	}

	/**
	 * Convert a number of seconds into a relative time.  For example, 630 => "10 hours, 30 minutes ago"
	 *
	 * @param integer $seconds
	 *
	 * @return string
	 *
	 * @todo Does Nesbot\Carbon do this for us?
	 */
	public static function timeAgo($seconds) {
		$minute = 60;
		$hour   = 60 * $minute;
		$day    = 24 * $hour;
		$month  = 30 * $day;
		$year   = 365 * $day;

		if ($seconds>$year) {
			$years=(int)($seconds/$year);
			return self::plural('%s year ago', '%s years ago', $years, self::number($years));
		} elseif ($seconds>$month) {
			$months=(int)($seconds/$month);
			return self::plural('%s month ago', '%s months ago', $months, self::number($months));
		} elseif ($seconds>$day) {
			$days=(int)($seconds/$day);
			return self::plural('%s day ago', '%s days ago', $days, self::number($days));
		} elseif ($seconds>$hour) {
			$hours=(int)($seconds/$hour);
			return self::plural('%s hour ago', '%s hours ago', $hours, self::number($hours));
		} elseif ($seconds>$minute) {
			$minutes=(int)($seconds/$minute);
			return self::plural('%s minute ago', '%s minutes ago', $minutes, self::number($minutes));
		} else {
			return self::plural('%s second ago', '%s seconds ago', $seconds, self::number($seconds));
		}
	}

	/**
	 * Return the endonym for a given language - as per http://cldr.unicode.org/
	 * 
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function languageName($locale) {
		$language_tag = str_replace(array('_', '@'), '-', $locale);

		if (array_key_exists($language_tag, self::$language_data)) {
			return self::$language_data[$language_tag][1];
		} elseif (class_exists('\Locale')) {
			return Locale::getDisplayName($locale, $locale);
		} else {
			return $locale;
		}
	}

	/**
	 * Return the script used by a given language
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function languageScript($locale) {
		$language_tag = str_replace(array('_', '@'), '-', $locale);

		if (array_key_exists($language_tag, self::$language_data)) {
			return self::$language_data[$language_tag][0];
		} else {
			return 'Latn';
		}
	}

	/**
	 * Identify the script used for a piece of text
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function textScript($string) {
		$string = strip_tags($string);                               // otherwise HTML tags show up as latin
		$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');  // otherwise HTML entities show up as latin
		$string = str_replace(array('@N.N.', '@P.N.'), '', $string); // otherwise unknown names show up as latin
		$pos = 0;
		$strlen = strlen($string);
		while ($pos < $strlen) {
			// get the Unicode Code Point for the character at position $pos
			$byte1 = ord($string[$pos]);
			if ($byte1 < 0x80) {
				$code_point = $byte1;
				$chrlen = 1;
			} elseif ($byte1 < 0xC0) {
				// Invalid continuation character
				return 'Latn';
			} elseif ($byte1 < 0xE0) {
				$code_point = (($byte1 & 0x1F) << 6) + (ord($string[$pos + 1]) & 0x3F);
				$chrlen = 2;
			} elseif ($byte1 < 0xF0) {
				$code_point = (($byte1 & 0x0F) << 12) + ((ord($string[$pos + 1]) & 0x3F) << 6) + (ord($string[$pos + 2]) & 0x3F);
				$chrlen = 3;
			} elseif ($byte1 < 0xF8) {
				$code_point = (($byte1 & 0x07) << 24) + ((ord($string[$pos + 1]) & 0x3F) << 12) + ((ord($string[$pos + 2]) & 0x3F) << 6) + (ord($string[$pos + 3]) & 0x3F);
				$chrlen = 3;
 			} else {
				// Invalid UTF
				return 'Latn';
			}

			foreach (self::$scripts as $range) {
				if ($code_point >= $range[1] && $code_point <= $range[2]) {
					return $range[0];
				}
			}
			// Not a recognised script.  Maybe punctuation, spacing, etc.  Keep looking.
			$pos += $chrlen;
		}

		return 'Latn';
	}

	/**
	 * Return the direction (ltr or rtl) for a given script
	 *
	 * The PHP/intl library does not provde this information, so we need
	 * our own lookup table.
	 *
	 * @param string $script
	 *
	 * @return string
	 */
	public static function scriptDirection($script) {
		switch ($script) {
		case 'Arab':
		case 'Hebr':
		case 'Mong':
		case 'Thaa':
			return 'rtl';
		default:
			return 'ltr';
		}
	}

	/**
	 * UTF8 version of PHP::strtoupper()
	 *
	 * Convert a string to upper case, using the rules from the current locale
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function strtoupper($string) {
		if (self::$locale == 'tr' || self::$locale == 'az') {
			return TurkishUtf8::strtoupper($string);
		} else {
			return mb_strtoupper($string);
		}
	}

	/**
	 * UTF8 version of PHP::strtolower()
	 *
	 * Convert a string to lower case, using the rules from the current locale
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function strtolower($string) {
		if (self::$locale == 'tr' || self::$locale == 'az') {
			return TurkishUtf8::strtolower($string);
		} else {
			return mb_strtolower($string);
		}
	}

	/**
	 * UTF8 version of PHP::strcasecmp()
	 *
	 * Perform a case-insensitive comparison of two strings, using rules from the current locale
	 *
	 * @param string $string1
	 * @param string $string2
	 *
	 * @return integer
	 */
	public static function strcasecmp($string1, $string2) {
		$strpos1 = 0;
		$strpos2 = 0;
		$strlen1 = strlen($string1);
		$strlen2 = strlen($string2);
		while ($strpos1 < $strlen1 && $strpos2 < $strlen2) {
			$byte1 = ord($string1[$strpos1]);
			$byte2 = ord($string2[$strpos2]);
			if (($byte1 & 0xE0) == 0xC0) {
				$chr1 = $string1[$strpos1++].$string1[$strpos1++];
			} elseif (($byte1 & 0xF0) == 0xE0) {
				$chr1 = $string1[$strpos1++].$string1[$strpos1++].$string1[$strpos1++];
			} else {
				$chr1 = $string1[$strpos1++];
			}
			if (($byte2 & 0xE0)==0xC0) {
				$chr2 = $string2[$strpos2++].$string2[$strpos2++];
			} elseif (($byte2 & 0xF0)==0xE0) {
				$chr2 = $string2[$strpos2++].$string2[$strpos2++].$string2[$strpos2++];
			} else {
				$chr2 = $string2[$strpos2++];
			}
			if ($chr1 == $chr2) {
				continue;
			}
			// Try the local alphabet first
			$offset1 = strpos(self::$alphabet_lower, $chr1);
			if ($offset1 === false) {
				$offset1 = strpos(self::$alphabet_upper, $chr1);
			}
			$offset2 = strpos(self::$alphabet_lower, $chr2);
			if ($offset2 === false) {
				$offset2 = strpos(self::$alphabet_upper, $chr2);
			}
			if ($offset1 !== false && $offset2 !== false) {
				if ($offset1 == $offset2) {
					continue;
				} else {
					return $offset1 - $offset2;
				}
			}
			// Try the global alphabet next
			$offset1 = strpos(self::ALPHABET_LOWER, $chr1);
			if ($offset1 === false) {
				$offset1 = strpos(self::ALPHABET_UPPER, $chr1);
			}
			$offset2 = strpos(self::ALPHABET_LOWER, $chr2);
			if ($offset2 === false) {
				$offset2 = strpos(self::ALPHABET_UPPER, $chr2);
			}
			if ($offset1 !== false && $offset2 !== false) {
				if ($offset1 == $offset2) {
					continue;
				} else {
					return $offset1 - $offset2;
				}
			}
			// Just compare by unicode order
			return strcmp($chr1, $chr2);
		}
		// Shortest string comes first.
		return ($strlen1 - $strpos1) - ($strlen2 - $strpos2);
	}

	/**
	 * UTF8 version of PHP::strrev()
	 *
	 * Reverse RTL text for third-party libraries such as GD2 and googlechart.
	 *
	 * These do not support UTF8 text direction, so we must mimic it for them.
	 *
	 * Numbers are always rendered LTR, even in RTL text.
	 * The visual direction of characters such as parentheses should be reversed.
	 *
	 * @param string $text Text to be reversed
	 *
	 * @return string
	 */
	public static function reverseText($text) {
		// Remove HTML markup - we can't display it and it is LTR.
		$text = WT_Filter::unescapeHtml($text);

		// LTR text doesn't need reversing
		if (self::scriptDirection(self::textScript($text)) == 'ltr') {
			return $text;
		}

		// Mirrored characters
		$text = strtr($text, self::$mirror_characters);

		$reversed = '';
		$digits = '';
		while ($text != '') {
			$letter = mb_substr($text, 0, 1);
			$text = mb_substr($text, 1);
			if (strpos(self::DIGITS, $letter) !== false) {
				$digits .= $letter;
			} else {
				$reversed = $letter . $digits . $reversed;
				$digits = '';
			}
		}

		return $digits . $reversed;
	}

	/**
	 * Generate consistent I18N for datatables.js
	 *
	 * @param array|null $lengths An optional array of page lengths
	 *
	 * @return string
	 */
	public static function datatablesI18N(array $lengths=null) {
		if ($lengths===null) {
			$lengths=array(10, 20, 30, 50, 100, -1);
		}

		$length_menu='';
		foreach ($lengths as $length) {
			$length_menu.=
				'<option value="'.$length.'">'.
				($length==-1 ? /* I18N: listbox option, e.g. “10,25,50,100,all” */ self::translate('All') : self::number($length)).
				'</option>';
		}
		$length_menu='<select>'.$length_menu.'</select>';
		$length_menu=/* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ self::translate('Display %s', $length_menu);

		// Which symbol is used for separating numbers into groups
		$symbols = Zend_Locale_Data::getList(self::$locale, 'symbols');
		// Which digits are used for numbers
		$digits = Zend_Locale_Data::getContent(self::$locale, 'numberingsystem', self::$numbering_system);

		if ($digits=='0123456789') {
			$callback='';
		} else {
			$callback=',
				"infoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
					return sPre
						.replace(/0/g, "'.mb_substr($digits, 0, 1).'")
						.replace(/1/g, "'.mb_substr($digits, 1, 1).'")
						.replace(/2/g, "'.mb_substr($digits, 2, 1).'")
						.replace(/3/g, "'.mb_substr($digits, 3, 1).'")
						.replace(/4/g, "'.mb_substr($digits, 4, 1).'")
						.replace(/5/g, "'.mb_substr($digits, 5, 1).'")
						.replace(/6/g, "'.mb_substr($digits, 6, 1).'")
						.replace(/7/g, "'.mb_substr($digits, 7, 1).'")
						.replace(/8/g, "'.mb_substr($digits, 8, 1).'")
						.replace(/9/g, "'.mb_substr($digits, 9, 1).'");
				},
				"formatNumber": function(iIn) {
					return String(iIn)
						.replace(/0/g, "'.mb_substr($digits, 0, 1).'")
						.replace(/1/g, "'.mb_substr($digits, 1, 1).'")
						.replace(/2/g, "'.mb_substr($digits, 2, 1).'")
						.replace(/3/g, "'.mb_substr($digits, 3, 1).'")
						.replace(/4/g, "'.mb_substr($digits, 4, 1).'")
						.replace(/5/g, "'.mb_substr($digits, 5, 1).'")
						.replace(/6/g, "'.mb_substr($digits, 6, 1).'")
						.replace(/7/g, "'.mb_substr($digits, 7, 1).'")
						.replace(/8/g, "'.mb_substr($digits, 8, 1).'")
						.replace(/9/g, "'.mb_substr($digits, 9, 1).'");
				}
			';
		}

		return
			'"language": {'.
			' "paginate": {'.
			'  "first":    "'./* I18N: button label, first page    */ self::translate('first').'",'.
			'  "last":     "'./* I18N: button label, last page     */ self::translate('last').'",'.
			'  "next":     "'./* I18N: button label, next page     */ self::translate('next').'",'.
			'  "previous": "'./* I18N: button label, previous page */ self::translate('previous').'"'.
			' },'.
			' "emptyTable":     "'.self::translate('No records to display').'",'.
			' "info":           "'./* I18N: %s are placeholders for numbers */ self::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",'.
			' "infoEmpty":      "'.self::translate('Showing %1$s to %2$s of %3$s', 0, 0, 0).'",'.
			' "infoFiltered":   "'./* I18N: %s is a placeholder for a number */ self::translate('(filtered from %s total entries)', '_MAX_').'",'.
			' "infoPostfix":    "",'.
			' "infoThousands":  "'.$symbols['group'].'",'.
			' "lengthMenu":     "'.WT_Filter::escapeJs($length_menu).'",'.
			' "loadingRecords": "'.self::translate('Loading…').'",'.
			' "processing":     "'.self::translate('Loading…').'",'.
			' "search":         "'.self::translate('Filter').'",'.
			' "url":            "",'.
			' "zeroRecords":    "'.self::translate('No records to display').'"'.
			'}'.
			$callback;
	}
}
