<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\ExtCalendar\ArabicCalendar;
use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\ExtCalendar\PersianCalendar;
use Fisharebest\Localization\Locale;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Localization\Translation;
use Fisharebest\Localization\Translator;

/**
 * Internationalization (i18n) and localization (l10n).
 */
class I18N {
	/** @var LocaleInterface The current locale (e.g. LocaleEnGb) */
	private static $locale;

	/** @var Translator An object that performs translation*/
	private static $translator;

	// Digits are always rendered LTR, even in RTL text.
	const DIGITS = '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹';

	// Reversable character conversions from the UNICODE 5.1 database.
	// It excludes ambiguous (turkish dotless i) and mixed-case (Dz) characters.
	// The characters should be arranged in default unicode-collation order.
	const ALPHABET_LOWER = 'aàáâãäåāăąǎǟǡǻȁȃȧḁạảấầẩẫậắằẳẵặⓐａæǣǽbḃḅḇⓑｂƀɓƃcçćĉċčḉⅽⓒｃƈdďḋḍḏḑḓⅾⓓｄǆǳđɖɗƌðeèéêëēĕėęěȅȇȩḕḗḙḛḝẹẻẽếềểễệⓔｅǝəɛfḟⓕｆƒgĝğġģǧǵḡⓖｇǥɠɣƣhĥȟḣḥḧḩḫⓗｈƕħiìíîïĩīĭįǐȉȋḭḯỉịⅰⓘｉⅱⅲĳⅳⅸɨɩjĵⓙｊkķǩḱḳḵⓚｋƙlĺļľḷḹḻḽⅼⓛｌŀǉłƚmḿṁṃⅿⓜｍnñńņňǹṅṇṉṋⓝｎǌɲƞŋoòóôõöōŏőơǒǫǭȍȏȫȭȯȱṍṏṑṓọỏốồổỗộớờởỡợⓞｏœøǿɔɵȣpṕṗⓟｐƥqⓠｑrŕŗřȑȓṙṛṝṟⓡｒʀsśŝşšșṡṣṥṧṩⓢｓʃtţťțṫṭṯṱⓣｔŧƭʈuùúûüũūŭůűųưǔǖǘǚǜȕȗṳṵṷṹṻụủứừửữựⓤｕʉɯʊvṽṿⅴⓥｖⅵⅶⅷʋʌwŵẁẃẅẇẉⓦｗxẋẍⅹⓧｘⅺⅻyýÿŷȳẏỳỵỷỹⓨｙƴzźżžẑẓẕⓩｚƶȥǯʒƹȝþƿƨƽƅάαἀἁἂἃἄἅἆἇὰάᾀᾁᾂᾃᾄᾅᾆᾇᾰᾱᾳβγδέεἐἑἒἓἔἕὲέϝϛζήηἠἡἢἣἤἥἦἧὴήᾐᾑᾒᾓᾔᾕᾖᾗῃθϊἰἱἲἳἴἵἶἷὶίῐῑκϗλμνξοόὀὁὂὃὄὅὸόπϟϙρῥσϲτυϋύὑὓὕὗὺύῠῡφχψωώὠὡὢὣὤὥὦὧὼώᾠᾡᾢᾣᾤᾥᾦᾧῳϡϸϻϣϥϧϩϫϭϯаӑӓәӛӕбвгґғҕдԁђԃѓҙеѐёӗєжӂӝҗзԅӟѕӡԇиѝӣҋӥіїйјкқӄҡҟҝлӆљԉмӎнӊңӈҥњԋоӧөӫпҧҁрҏсԍҫтԏҭћќуӯўӱӳүұѹфхҳһѡѿѽѻцҵчӵҷӌҹҽҿџшщъыӹьҍѣэӭюяѥѧѫѩѭѯѱѳѵѷҩաբգդեզէըթժիլխծկհձղճմյնշոչպջռսվտրցւփքօֆȼɂɇɉɋɍɏͱͳͷͻͼͽӏӷӻӽӿԑԓԕԗԙԛԝԟԡԣԥᵹᵽỻỽỿⅎↄⰰⰱⰲⰳⰴⰵⰶⰷⰸⰹⰺⰻⰼⰽⰾⰿⱀⱁⱂⱃⱄⱅⱆⱇⱈⱉⱊⱋⱌⱍⱎⱏⱐⱑⱒⱓⱔⱕⱖⱗⱘⱙⱚⱛⱜⱝⱞⱡⱨⱪⱬⱳⱶⲁⲃⲅⲇⲉⲋⲍⲏⲑⲓⲕⲗⲙⲛⲝⲟⲡⲣⲥⲧⲩⲫⲭⲯⲱⲳⲵⲷⲹⲻⲽⲿⳁⳃⳅⳇⳉⳋⳍⳏⳑⳓⳕⳗⳙⳛⳝⳟⳡⳣⳬⳮⴀⴁⴂⴃⴄⴅⴆⴇⴈⴉⴊⴋⴌⴍⴎⴏⴐⴑⴒⴓⴔⴕⴖⴗⴘⴙⴚⴛⴜⴝⴞⴟⴠⴡⴢⴣⴤⴥꙁꙃꙅꙇꙉꙋꙍꙏꙑꙓꙕꙗꙙꙛꙝꙟꙣꙥꙧꙩꙫꙭꚁꚃꚅꚇꚉꚋꚍꚏꚑꚓꚕꚗꜣꜥꜧꜩꜫꜭꜯꜳꜵꜷꜹꜻꜽꜿꝁꝃꝅꝇꝉꝋꝍꝏꝑꝓꝕꝗꝙꝛꝝꝟꝡꝣꝥꝧꝩꝫꝭꝯꝺꝼꝿꞁꞃꞅꞇꞌ';
	const ALPHABET_UPPER = 'AÀÁÂÃÄÅĀĂĄǍǞǠǺȀȂȦḀẠẢẤẦẨẪẬẮẰẲẴẶⒶＡÆǢǼBḂḄḆⒷＢɃƁƂCÇĆĈĊČḈⅭⒸＣƇDĎḊḌḎḐḒⅮⒹＤǄǱĐƉƊƋÐEÈÉÊËĒĔĖĘĚȄȆȨḔḖḘḚḜẸẺẼẾỀỂỄỆⒺＥƎƏƐFḞⒻＦƑGĜĞĠĢǦǴḠⒼＧǤƓƔƢHĤȞḢḤḦḨḪⒽＨǶĦIÌÍÎÏĨĪĬĮǏȈȊḬḮỈỊⅠⒾＩⅡⅢĲⅣⅨƗƖJĴⒿＪKĶǨḰḲḴⓀＫƘLĹĻĽḶḸḺḼⅬⓁＬĿǇŁȽMḾṀṂⅯⓂＭNÑŃŅŇǸṄṆṈṊⓃＮǊƝȠŊOÒÓÔÕÖŌŎŐƠǑǪǬȌȎȪȬȮȰṌṎṐṒỌỎỐỒỔỖỘỚỜỞỠỢⓄＯŒØǾƆƟȢPṔṖⓅＰƤQⓆＱRŔŖŘȐȒṘṚṜṞⓇＲƦSŚŜŞŠȘṠṢṤṦṨⓈＳƩTŢŤȚṪṬṮṰⓉＴŦƬƮUÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛȔȖṲṴṶṸṺỤỦỨỪỬỮỰⓊＵɄƜƱVṼṾⅤⓋＶⅥⅦⅧƲɅWŴẀẂẄẆẈⓌＷXẊẌⅩⓍＸⅪⅫYÝŸŶȲẎỲỴỶỸⓎＹƳZŹŻŽẐẒẔⓏＺƵȤǮƷƸȜÞǷƧƼƄΆΑἈἉἊἋἌἍἎἏᾺΆᾈᾉᾊᾋᾌᾍᾎᾏᾸᾹᾼΒΓΔΈΕἘἙἚἛἜἝῈΈϜϚΖΉΗἨἩἪἫἬἭἮἯῊΉᾘᾙᾚᾛᾜᾝᾞᾟῌΘΪἸἹἺἻἼἽἾἿῚΊῘῙΚϏΛΜΝΞΟΌὈὉὊὋὌὍῸΌΠϞϘΡῬΣϹΤΥΫΎὙὛὝὟῪΎῨῩΦΧΨΩΏὨὩὪὫὬὭὮὯῺΏᾨᾩᾪᾫᾬᾭᾮᾯῼϠϷϺϢϤϦϨϪϬϮАӐӒӘӚӔБВГҐҒҔДԀЂԂЃҘЕЀЁӖЄЖӁӜҖЗԄӞЅӠԆИЍӢҊӤІЇЙЈКҚӃҠҞҜЛӅЉԈМӍНӉҢӇҤЊԊОӦӨӪПҦҀРҎСԌҪТԎҬЋЌУӮЎӰӲҮҰѸФХҲҺѠѾѼѺЦҴЧӴҶӋҸҼҾЏШЩЪЫӸЬҌѢЭӬЮЯѤѦѪѨѬѮѰѲѴѶҨԱԲԳԴԵԶԷԸԹԺԻԼԽԾԿՀՁՂՃՄՅՆՇՈՉՊՋՌՍՎՏՐՑՒՓՔՕՖȻɁɆɈɊɌɎͰͲͶϽϾϿӀӶӺӼӾԐԒԔԖԘԚԜԞԠԢԤꝽⱣỺỼỾℲↃⰀⰁⰂⰃⰄⰅⰆⰇⰈⰉⰊⰋⰌⰍⰎⰏⰐⰑⰒⰓⰔⰕⰖⰗⰘⰙⰚⰛⰜⰝⰞⰟⰠⰡⰢⰣⰤⰥⰦⰧⰨⰩⰪⰫⰬⰭⰮⱠⱧⱩⱫⱲⱵⲀⲂⲄⲆⲈⲊⲌⲎⲐⲒⲔⲖⲘⲚⲜⲞⲠⲢⲤⲦⲨⲪⲬⲮⲰⲲⲴⲶⲸⲺⲼⲾⳀⳂⳄⳆⳈⳊⳌⳎⳐⳒⳔⳖⳘⳚⳜⳞⳠⳢⳫⳭႠႡႢႣႤႥႦႧႨႩႪႫႬႭႮႯႰႱႲႳႴႵႶႷႸႹႺႻႼႽႾႿჀჁჂჃჄჅꙀꙂꙄꙆꙈꙊꙌꙎꙐꙒꙔꙖꙘꙚꙜꙞꙢꙤꙦꙨꙪꙬꚀꚂꚄꚆꚈꚊꚌꚎꚐꚒꚔꚖꜢꜤꜦꜨꜪꜬꜮꜲꜴꜶꜸꜺꜼꜾꝀꝂꝄꝆꝈꝊꝌꝎꝐꝒꝔꝖꝘꝚꝜꝞꝠꝢꝤꝦꝨꝪꝬꝮꝹꝻꝾꞀꞂꞄꞆꞋ';

	/** @var string Alphabet, in lower case, for the current locale. */
	private static $alphabet_lower = 'abcdefghijklmnopqrstuvwxyz';

	/** @var string Alphabet, in upper case, for the current locale. */
	private static $alphabet_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/** @var int[][] Character ranges used by each script. */
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

	/** @var string[] Characters that are displayed in mirror form in RTL text. */
	private static $mirror_characters = array(
		'('   => ')',
		')'   => '(',
		'['   => ']',
		']'   => '[',
		'{'   => '}',
		'}'   => '{',
		'<'   => '>',
		'>'   => '<',
		'‹'   => '›',
		'›'   => '‹',
		'«'   => '»',
		'»'   => '«',
		'﴾'   => '﴿',
		'﴿'   => '﴾',
		'“'   => '”',
		'”'   => '“',
		'‘'   => '’',
		'’'   => '‘',
	);

	/** @var string Punctuation used to separate list items, typically a comma */
	public static $list_separator;

	/**
	 * The prefered locales for this site, or a default list if no preference.
	 *
	 * @return LocaleInterface[]
	 */
	public static function activeLocales() {
		$code_list = Site::getPreference('LANGUAGES');

		if ($code_list) {
			$codes = explode(',', $code_list);
		} else {
			$codes = array(
				'ar', 'bg', 'bs', 'ca', 'cs', 'da', 'de', 'el', 'en-GB', 'en-US', 'es',
				'et', 'fi', 'fr', 'he', 'hr', 'hu', 'is', 'it', 'ka', 'lt', 'mr', 'nb',
				'nl', 'nn', 'pl', 'pt', 'ru', 'sk', 'sv', 'tr', 'uk', 'vi', 'zh-Hans',
			);
		}

		$locales = array();
		foreach ($codes as $code) {
			if (file_exists(WT_ROOT . 'language/' . $code . '.mo')) {
				try {
					$locales[] = Locale::create($code);
				} catch (\Exception $ex) {
					// No such locale exists?
				}
			}
		}
		usort($locales, '\Fisharebest\Localization\Locale::compare');

		return $locales;
	}

	/**
	 * Which MySQL collation should be used for this locale?
	 *
	 * @return string
	 */
	public static function collation() {
		$collation = self::$locale->collation();
		switch ($collation) {
		case 'croatian_ci':
		case 'german2_ci':
		case 'vietnamese_ci':
			// Only available in MySQL 5.6
			return 'utf8_unicode_ci';
		default:
			return 'utf8_' . $collation;
		}
	}

	/**
	 * What format is used to display dates in the current locale?
	 *
	 * @return string
	 */
	public static function dateFormat() {
		return /* I18N: This is the format string for full dates.  See http://php.net/date for codes */ self::$translator->translate('%j %F %Y');
	}

	/**
	 * Generate consistent I18N for datatables.js
	 *
	 * @param array|null $lengths An optional array of page lengths
	 *
	 * @return string
	 */
	public static function datatablesI18N(array $lengths = null) {
		if ($lengths === null) {
			$lengths = array(10, 20, 30, 50, 100, -1);
		}

		$length_menu = '';
		foreach ($lengths as $length) {
			$length_menu .=
				'<option value="' . $length . '">' .
				($length === -1 ? /* I18N: listbox option, e.g. “10,25,50,100,all” */ self::translate('All') : self::number($length)) .
				'</option>';
		}
		$length_menu = '<select>' . $length_menu . '</select>';
		$length_menu = /* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ self::translate('Display %s', $length_menu);

		$digits = self::$locale->digits('0123456789');
		if ($digits === '0123456789') {
			$callback = '';
		} else {
			$callback = ',
				"infoCallback": function(oSettings, iStart, iEnd, iMax, iTotal, sPre) {
					return sPre
						.replace(/0/g, "' . mb_substr($digits, 0, 1) . '")
						.replace(/1/g, "' . mb_substr($digits, 1, 1) . '")
						.replace(/2/g, "' . mb_substr($digits, 2, 1) . '")
						.replace(/3/g, "' . mb_substr($digits, 3, 1) . '")
						.replace(/4/g, "' . mb_substr($digits, 4, 1) . '")
						.replace(/5/g, "' . mb_substr($digits, 5, 1) . '")
						.replace(/6/g, "' . mb_substr($digits, 6, 1) . '")
						.replace(/7/g, "' . mb_substr($digits, 7, 1) . '")
						.replace(/8/g, "' . mb_substr($digits, 8, 1) . '")
						.replace(/9/g, "' . mb_substr($digits, 9, 1) . '");
				},
				"formatNumber": function(iIn) {
					return String(iIn)
						.replace(/0/g, "' . mb_substr($digits, 0, 1) . '")
						.replace(/1/g, "' . mb_substr($digits, 1, 1) . '")
						.replace(/2/g, "' . mb_substr($digits, 2, 1) . '")
						.replace(/3/g, "' . mb_substr($digits, 3, 1) . '")
						.replace(/4/g, "' . mb_substr($digits, 4, 1) . '")
						.replace(/5/g, "' . mb_substr($digits, 5, 1) . '")
						.replace(/6/g, "' . mb_substr($digits, 6, 1) . '")
						.replace(/7/g, "' . mb_substr($digits, 7, 1) . '")
						.replace(/8/g, "' . mb_substr($digits, 8, 1) . '")
						.replace(/9/g, "' . mb_substr($digits, 9, 1) . '");
				}
			';
		}

		return
			'"language": {' .
			' "paginate": {' .
			'  "first":    "' . /* I18N: button label, first page    */ self::translate('first') . '",' .
			'  "last":     "' . /* I18N: button label, last page     */ self::translate('last') . '",' .
			'  "next":     "' . /* I18N: button label, next page     */ self::translate('next') . '",' .
			'  "previous": "' . /* I18N: button label, previous page */ self::translate('previous') . '"' .
			' },' .
			' "emptyTable":     "' . self::translate('No records to display') . '",' .
			' "info":           "' . /* I18N: %s are placeholders for numbers */ self::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_') . '",' .
			' "infoEmpty":      "' . self::translate('Showing %1$s to %2$s of %3$s', 0, 0, 0) . '",' .
			' "infoFiltered":   "' . /* I18N: %s is a placeholder for a number */ self::translate('(filtered from %s total entries)', '_MAX_') . '",' .
			' "infoPostfix":    "",' .
			' "lengthMenu":     "' . Filter::escapeJs($length_menu) . '",' .
			' "loadingRecords": "' . self::translate('Loading…') . '",' .
			' "processing":     "' . self::translate('Loading…') . '",' .
			' "search":         "' . self::translate('Filter') . '",' .
			' "url":            "",' .
			' "zeroRecords":    "' . self::translate('No records to display') . '"' .
			'}' .
			$callback;
	}

	/**
	 * Convert the digits 0-9 into the local script
	 *
	 * Used for years, etc., where we do not want thousands-separators, decimals, etc.
	 *
	 * @param int $n
	 *
	 * @return string
	 */
	public static function digits($n) {
		return self::$locale->digits($n);
	}

	/**
	 * What is the direction of the current locale
	 *
	 * @return string "ltr" or "rtl"
	 */
	public static function direction() {
		return self::$locale->direction();
	}

	/**
	 * What is the first day of the week.
	 *
	 * @return int Sunday=0, Monday=1, etc.
	 */
	public static function firstDay() {
		return self::$locale->territory()->firstDay();
	}

	/**
	 * Convert a GEDCOM age string into translated_text
	 *
	 * NB: The import function will have normalised this, so we don't need
	 * to worry about badly formatted strings
	 * NOTE: this function is not yet complete - eventually it will replace FunctionsDate::get_age_at_event()
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function gedcomAge($string) {
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
		$age = array();
		if (preg_match('/(\d+)y/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$years = $match[1];
			$age[] = self::plural('%s year', '%s years', $years, self::number($years));
		} else {
			$years = -1;
		}
		if (preg_match('/(\d+)m/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$age[] = self::plural('%s month', '%s months', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)w/', $string, $match)) {
			// I18N: Part of an age string. e.g. 7 weeks and 3 days
			$age[] = self::plural('%s week', '%s weeks', $match[1], self::number($match[1]));
		}
		if (preg_match('/(\d+)d/', $string, $match)) {
			// I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
			$age[] = self::plural('%s day', '%s days', $match[1], self::number($match[1]));
		}
		// If an age is just a number of years, only show the number
		if (count($age) === 1 && $years >= 0) {
			$age = $years;
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
	 * Generate i18n markup for the <html> tag, e.g. lang="ar" dir="rtl"
	 *
	 * @return string
	 */
	public static function htmlAttributes() {
		return self::$locale->htmlAttributes();
	}

	/**
	 * Initialise the translation adapter with a locale setting.
	 *
	 * @param string|null $code Use this locale/language code, or choose one automatically
	 *
	 * @return string $string
	 */
	public static function init($code = null) {
		global $WT_TREE;

		mb_internal_encoding('UTF-8');

		if ($code !== null) {
			// Create the specified locale
			self::$locale = Locale::create($code);
		} else {
			// Negotiate a locale, but if we can't then use a failsafe
			self::$locale = new LocaleEnUs;
			if (Session::has('locale')) {
				// Previously used
				self::$locale = Locale::create(Session::get('locale'));
			} else {
				// Browser negotiation
				$default_locale = new LocaleEnUs;
				try {
					if ($WT_TREE) {
						$default_locale = Locale::create($WT_TREE->getPreference('LANGUAGE'));
					}
				} catch (\Exception $ex) {
				}
				self::$locale = Locale::httpAcceptLanguage($_SERVER, self::installedLocales(), $default_locale);
			}
		}

		$cache_dir_exists = File::mkdir(WT_DATA_DIR . 'cache');
		$cache_file = WT_DATA_DIR . 'cache/language-' . self::$locale->languageTag() . '-cache.php';
		if (file_exists($cache_file)) {
			$filemtime = filemtime($cache_file);
		} else {
			$filemtime = 0;
		}

		// Load the translation file(s)
		// Note that glob() returns false instead of an empty array when open_basedir_restriction
		// is in force and no files are found.  See PHP bug #47358.
		if (defined('GLOB_BRACE')) {
			$translation_files = array_merge(
				array(WT_ROOT . 'language/' . self::$locale->languageTag() . '.mo'),
				glob(WT_MODULES_DIR . '*/language/' . self::$locale->languageTag() . '.{csv,php,mo}', GLOB_BRACE) ?: array(),
				glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.{csv,php,mo}', GLOB_BRACE) ?: array()
			);
		} else {
			// Some servers do not have GLOB_BRACE - see http://php.net/manual/en/function.glob.php
			$translation_files = array_merge(
				array(WT_ROOT . 'language/' . self::$locale->languageTag() . '.mo'),
				glob(WT_MODULES_DIR . '*/language/' . self::$locale->languageTag() . '.csv') ?: array(),
				glob(WT_MODULES_DIR . '*/language/' . self::$locale->languageTag() . '.php') ?: array(),
				glob(WT_MODULES_DIR . '*/language/' . self::$locale->languageTag() . '.mo') ?: array(),
				glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.csv') ?: array(),
				glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.php') ?: array(),
				glob(WT_DATA_DIR . 'language/' . self::$locale->languageTag() . '.mo') ?: array()
			);
		}
		// Rebuild files after one hour
		$rebuild_cache = time() > $filemtime + 3600;
		// Rebuild files if any translation file has been updated
		foreach ($translation_files as $translation_file) {
			if (filemtime($translation_file) > $filemtime) {
				$rebuild_cache = true;
				break;
			}
		}

		if ($rebuild_cache) {
			$translations = array();
			foreach ($translation_files as $translation_file) {
				$translation  = new Translation($translation_file);
				$translations = array_merge($translations, $translation->asArray());
			}
			if ($cache_dir_exists) { // During setup, we may not have been able to create it.
				file_put_contents($cache_file, '<' . '?php return ' . var_export($translations, true) . ';');
			}
		} else {
			$translations = include $cache_file;
		}

		// Create a translator
		self::$translator = new Translator($translations, self::$locale->pluralRule());

		// Alphabetic sorting sequence (upper-case letters), used by webtrees to sort strings
		list(, self::$alphabet_upper) = explode('=', self::$translator->translate('ALPHABET_upper=ABCDEFGHIJKLMNOPQRSTUVWXYZ'));
		// Alphabetic sorting sequence (lower-case letters), used by webtrees to sort strings
		list(, self::$alphabet_lower) = explode('=', self::$translator->translate('ALPHABET_lower=abcdefghijklmnopqrstuvwxyz'));

		self::$list_separator = /* I18N: This punctuation is used to separate lists of items */ self::translate(', ');

		return self::$locale->languageTag();
	}

	/**
	 * All locales for which a translation file exists.
	 *
	 * @return LocaleInterface[]
	 */
	public static function installedLocales() {
		$locales = array();
		foreach (glob(WT_ROOT . 'language/*.mo') as $file) {
			try {
				$locales[] = Locale::create(basename($file, '.mo'));
			} catch (\Exception $ex) {
				// Not a recognised locale
			}
		}
		usort($locales, '\Fisharebest\Localization\Locale::compare');

		return $locales;
	}

	/**
	 * Return the endonym for a given language - as per http://cldr.unicode.org/
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function languageName($locale) {
		return Locale::create($locale)->endonym();
	}

	/**
	 * Return the script used by a given language
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	public static function languageScript($locale) {
		return Locale::create($locale)->script()->code();
	}

	/**
	 * Translate a number into the local representation.
	 *
	 * e.g. 12345.67 becomes
	 * en: 12,345.67
	 * fr: 12 345,67
	 * de: 12.345,67
	 *
	 * @param float $n
	 * @param int   $precision
	 *
	 * @return string
	 */
	public static function number($n, $precision = 0) {
		return self::$locale->number(round($n, $precision));
	}

	/**
	 * Translate a fraction into a percentage.
	 *
	 * e.g. 0.123 becomes
	 * en: 12.3%
	 * fr: 12,3 %
	 * de: 12,3%
	 *
	 * @param float $n
	 * @param int   $precision
	 *
	 * @return string
	 */
	public static function percentage($n, $precision = 0) {
		return self::$locale->percent(round($n, $precision + 2));
	}

	/**
	 * Translate a plural string
	 *
	 * echo self::plural('There is an error', 'There are errors', $num_errors);
	 * echo self::plural('There is one error', 'There are %s errors', $num_errors);
	 * echo self::plural('There is %1$s %2$s cat', 'There are %1$s %2$s cats', $num, $num, $colour);
	 *
	 * @return string
	 */
	public static function plural(/* var_args */) {
		$args    = func_get_args();
		$args[0] = self::$translator->translatePlural($args[0], $args[1], (int) $args[2]);
		unset($args[1], $args[2]);

		return self::substitutePlaceholders($args);
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
		$text = Filter::unescapeHtml($text);

		// LTR text doesn't need reversing
		if (self::scriptDirection(self::textScript($text)) === 'ltr') {
			return $text;
		}

		// Mirrored characters
		$text = strtr($text, self::$mirror_characters);

		$reversed = '';
		$digits   = '';
		while ($text != '') {
			$letter = mb_substr($text, 0, 1);
			$text   = mb_substr($text, 1);
			if (strpos(self::DIGITS, $letter) !== false) {
				$digits .= $letter;
			} else {
				$reversed = $letter . $digits . $reversed;
				$digits   = '';
			}
		}

		return $digits . $reversed;
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
	 * UTF8 version of PHP::strcasecmp()
	 *
	 * Perform a case-insensitive comparison of two strings, using rules from the current locale
	 *
	 * @param string $string1
	 * @param string $string2
	 *
	 * @return int
	 */
	public static function strcasecmp($string1, $string2) {
		$strpos1 = 0;
		$strpos2 = 0;
		$strlen1 = strlen($string1);
		$strlen2 = strlen($string2);
		while ($strpos1 < $strlen1 && $strpos2 < $strlen2) {
			$byte1 = ord($string1[$strpos1]);
			$byte2 = ord($string2[$strpos2]);
			if (($byte1 & 0xE0) === 0xC0) {
				$chr1 = $string1[$strpos1++] . $string1[$strpos1++];
			} elseif (($byte1 & 0xF0) === 0xE0) {
				$chr1 = $string1[$strpos1++] . $string1[$strpos1++] . $string1[$strpos1++];
			} else {
				$chr1 = $string1[$strpos1++];
			}
			if (($byte2 & 0xE0) === 0xC0) {
				$chr2 = $string2[$strpos2++] . $string2[$strpos2++];
			} elseif (($byte2 & 0xF0) === 0xE0) {
				$chr2 = $string2[$strpos2++] . $string2[$strpos2++] . $string2[$strpos2++];
			} else {
				$chr2 = $string2[$strpos2++];
			}
			if ($chr1 === $chr2) {
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
				if ($offset1 === $offset2) {
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
				if ($offset1 === $offset2) {
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
	 * UTF8 version of PHP::strtolower()
	 *
	 * Convert a string to lower case, using the rules from the current locale
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function strtolower($string) {
		if (self::$locale->language()->code() === 'tr' || self::$locale->language()->code() === 'az') {
			$string = strtr($string, array('I' => 'ı', 'İ' => 'i'));
		}

		return mb_strtolower($string);
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
		if (self::$locale->language()->code() === 'tr' || self::$locale->language()->code() === 'az') {
			$string = strtr($string, array('ı' => 'I', 'i' => 'İ'));
		}

		return mb_strtoupper($string);
	}

	/**
	 * Substitute any "%s" placeholders in a translated string.
	 * This also allows us to have translated strings that contain
	 * "%" characters, which can't be passed to sprintf.
	 *
	 * @param string[] $args translated string plus optional parameters
	 *
	 * @return string
	 */
	private static function substitutePlaceholders(array $args) {
		if (count($args) > 1) {
			return call_user_func_array('sprintf', $args);
		} else {
			return $args[0];
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
		$string = strip_tags($string); // otherwise HTML tags show up as latin
		$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8'); // otherwise HTML entities show up as latin
		$string = str_replace(array('@N.N.', '@P.N.'), '', $string); // otherwise unknown names show up as latin
		$pos    = 0;
		$strlen = strlen($string);
		while ($pos < $strlen) {
			// get the Unicode Code Point for the character at position $pos
			$byte1 = ord($string[$pos]);
			if ($byte1 < 0x80) {
				$code_point = $byte1;
				$chrlen     = 1;
			} elseif ($byte1 < 0xC0) {
				// Invalid continuation character
				return 'Latn';
			} elseif ($byte1 < 0xE0) {
				$code_point = (($byte1 & 0x1F) << 6) + (ord($string[$pos + 1]) & 0x3F);
				$chrlen     = 2;
			} elseif ($byte1 < 0xF0) {
				$code_point = (($byte1 & 0x0F) << 12) + ((ord($string[$pos + 1]) & 0x3F) << 6) + (ord($string[$pos + 2]) & 0x3F);
				$chrlen     = 3;
			} elseif ($byte1 < 0xF8) {
				$code_point = (($byte1 & 0x07) << 24) + ((ord($string[$pos + 1]) & 0x3F) << 12) + ((ord($string[$pos + 2]) & 0x3F) << 6) + (ord($string[$pos + 3]) & 0x3F);
				$chrlen     = 3;
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
	 * Convert a number of seconds into a relative time.  For example, 630 => "10 hours, 30 minutes ago"
	 *
	 * @param int $seconds
	 *
	 * @return string
	 */
	public static function timeAgo($seconds) {
		$minute = 60;
		$hour   = 60 * $minute;
		$day    = 24 * $hour;
		$month  = 30 * $day;
		$year   = 365 * $day;

		if ($seconds > $year) {
			$years = (int) ($seconds / $year);

			return self::plural('%s year ago', '%s years ago', $years, self::number($years));
		} elseif ($seconds > $month) {
			$months = (int) ($seconds / $month);

			return self::plural('%s month ago', '%s months ago', $months, self::number($months));
		} elseif ($seconds > $day) {
			$days = (int) ($seconds / $day);

			return self::plural('%s day ago', '%s days ago', $days, self::number($days));
		} elseif ($seconds > $hour) {
			$hours = (int) ($seconds / $hour);

			return self::plural('%s hour ago', '%s hours ago', $hours, self::number($hours));
		} elseif ($seconds > $minute) {
			$minutes = (int) ($seconds / $minute);

			return self::plural('%s minute ago', '%s minutes ago', $minutes, self::number($minutes));
		} else {
			return self::plural('%s second ago', '%s seconds ago', $seconds, self::number($seconds));
		}
	}

	/**
	 * What format is used to display dates in the current locale?
	 *
	 * @return string
	 */
	public static function timeFormat() {
		return /* I18N: This is the format string for the time-of-day.  See http://php.net/date for codes */ self::$translator->translate('%H:%i:%s');
	}

	/**
	 * Translate a string, and then substitute placeholders
	 *
	 * echo I18N::translate('Hello World!');
	 * echo I18N::translate('The %s sat on the mat', 'cat');
	 *
	 * @return string
	 */
	public static function translate(/* var_args */) {
		$args    = func_get_args();
		$args[0] = self::$translator->translate($args[0]);

		return self::substitutePlaceholders($args);
	}

	/**
	 * Context sensitive version of translate.
	 *
	 * echo I18N::translateContext('NOMINATIVE', 'January');
	 * echo I18N::translateContext('GENITIVE',   'January');
	 *
	 * @return string
	 */
	public static function translateContext(/* var_args */) {
		$args    = func_get_args();
		$args[0] = self::$translator->translateContext($args[0], $args[1]);
		unset($args[1]);

		return self::substitutePlaceholders($args);
	}

	/**
	 * What is the last day of the weekend.
	 *
	 * @return int Sunday=0, Monday=1, etc.
	 */
	public static function weekendEnd() {
		return self::$locale->territory()->weekendEnd();
	}

	/**
	 * What is the first day of the weekend.
	 *
	 * @return int Sunday=0, Monday=1, etc.
	 */
	public static function weekendStart() {
		return self::$locale->territory()->weekendStart();
	}

	/**
	 * Which calendar prefered in this locale?
	 *
	 * @return CalendarInterface
	 */
	public static function defaultCalendar() {
		switch (self::$locale->languageTag()) {
			case 'ar':
				return new ArabicCalendar;
			case 'fa':
				return new PersianCalendar;
			case 'he':
			case 'yi':
				return new JewishCalendar;
			default:
				return new GregorianCalendar;
		}
	}
}
