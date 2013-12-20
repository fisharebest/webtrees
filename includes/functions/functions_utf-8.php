<?php
// UTF-8 versions of PHP string functions
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// This is a list of parentheses, which need special RTL logic.
define('WT_UTF8_PARENTHESES1', ')(][}{><»«﴾﴿‹›“”‘’');
define('WT_UTF8_PARENTHESES2', '()[]{}<>«»﴿﴾›‹”“’‘');

// This is a list of digits.  Note that arabic digits are displayed LTR, even in RTL text
define('WT_UTF8_DIGITS', '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹');

function utf8_strtoupper($string) {
	global $ALPHABET_lower, $ALPHABET_upper; // Language-specific conversions, e.g. Turkish dotless i

	$upper=$string;
	$pos=0;
	$strlen=strlen($string);
	while ($pos<$strlen) {
		$byte=ord($string[$pos]);
		if (($byte & 0xE0)==0xC0) {
			$chrlen=2; $chr=$string[$pos].$string[$pos+1];
		} elseif (($byte & 0xF0)==0xE0) {
			$chrlen=3; $chr=$string[$pos].$string[$pos+1].$string[$pos+2];
		} else {
			$chrlen=1; $chr=$string[$pos];
		}
		// Try language-specific conversion before generic conversion
		if (($chrpos=strpos($ALPHABET_lower, $chr))!==false) {
			$upper=substr_replace($upper, substr($ALPHABET_upper, $chrpos, $chrlen), $pos, $chrlen);
		} elseif (($chrpos=strpos(WT_UTF8_ALPHABET_LOWER, $chr))!==false) {
			$upper=substr_replace($upper, substr(WT_UTF8_ALPHABET_UPPER, $chrpos, $chrlen), $pos, $chrlen);
		}
		$pos+=$chrlen;
	}
	return $upper;
}

function utf8_strtolower($string) {
	global $ALPHABET_lower, $ALPHABET_upper; // Language-specific conversions, e.g. Turkish dotless i

	$lower=$string;
	$pos=0;
	$strlen=strlen($string);
	while ($pos<$strlen) {
		$byte=ord($string[$pos]);
		if (($byte & 0xE0)==0xC0) {
			$chrlen=2; $chr=$string[$pos].$string[$pos+1];
		} elseif (($byte & 0xF0)==0xE0) {
			$chrlen=3; $chr=$string[$pos].$string[$pos+1].$string[$pos+2];
		} else {
			$chrlen=1; $chr=$string[$pos];
		}
		// Try language-specific conversion before generic conversion
		if (($chrpos=strpos($ALPHABET_upper, $chr))!==false) {
			$lower=substr_replace($lower, substr($ALPHABET_lower, $chrpos, $chrlen), $pos, $chrlen);
		} elseif (($chrpos=strpos(WT_UTF8_ALPHABET_UPPER, $chr))!==false) {
			$lower=substr_replace($lower, substr(WT_UTF8_ALPHABET_LOWER, $chrpos, $chrlen), $pos, $chrlen);
		}
		$pos+=$chrlen;
	}
	return $lower;
}

function utf8_substr($string, $pos, $len=PHP_INT_MAX) {
	if ($len<0) {
		return '';
	}
	$strlen=strlen($string);
	if ($pos==0) {
		$start=0;
	} elseif ($pos>0) {
		$start=0;
		while ($pos>0 && $start<$strlen) {
			++$start;
			while ($start<$strlen && (ord($string[$start]) & 0xC0) == 0x80) {
				++$start;
			}
			--$pos;
		}
	} else {
		$start=$strlen-1;
		do {
			--$start;
			while ($start && (ord($string[$start]) & 0xC0) == 0x80) {
				--$start;
			}
			++$pos;
		} while ($start && $pos<0);
	}
	if ($len==PHP_INT_MAX || $len<0) {
		return substr($string, $start);
	}
	$end=$start;
	while ($len>0) {
		++$end;
		while ($end<$strlen && (ord($string[$end]) & 0xC0) == 0x80) {
			++$end;
		}
		--$len;
	}
	return substr($string, $start, $end-$start);
}

function utf8_strlen($string) {
	$pos=0;
	$len=strlen($string);
	$utf8_len=0;
	while ($pos<$len) {
		if ((ord($string[$pos]) & 0xC0) != 0x80) {
			++$utf8_len;
		}
		++$pos;
	}
	return $utf8_len;
}

function utf8_strcasecmp($string1, $string2) {
	// Language-specific alphabet sequence
	global $ALPHABET_lower, $ALPHABET_upper;

	$strpos1=0;
	$strpos2=0;
	$strlen1=strlen($string1);
	$strlen2=strlen($string2);
	while ($strpos1<$strlen1 && $strpos2<$strlen2) {
		$byte1=ord($string1[$strpos1]);
		$byte2=ord($string2[$strpos2]);
		if (($byte1 & 0xE0)==0xC0) {
			$chr1=$string1[$strpos1++].$string1[$strpos1++];
		} elseif (($byte1 & 0xF0)==0xE0) {
			$chr1=$string1[$strpos1++].$string1[$strpos1++].$string1[$strpos1++];
		} else {
			$chr1=$string1[$strpos1++];
		}
		if (($byte2 & 0xE0)==0xC0) {
			$chr2=$string2[$strpos2++].$string2[$strpos2++];
		} elseif (($byte2 & 0xF0)==0xE0) {
			$chr2=$string2[$strpos2++].$string2[$strpos2++].$string2[$strpos2++];
		} else {
			$chr2=$string2[$strpos2++];
		}
		if ($chr1==$chr2) {
			continue;
		}
		// Try the local alphabet first
		$offset1=strpos($ALPHABET_lower, $chr1);
		if ($offset1===false) {
			$offset1=strpos($ALPHABET_upper, $chr1);
		}
		$offset2=strpos($ALPHABET_lower, $chr2);
		if ($offset2===false) {
			$offset2=strpos($ALPHABET_upper, $chr2);
		}
		if ($offset1!==false && $offset2!==false) {
			if ($offset1==$offset2) {
				continue;
			} else {
				return $offset1-$offset2;
			}
		}
		// Try the global alphabet next
		$offset1=strpos(WT_UTF8_ALPHABET_LOWER, $chr1);
		if ($offset1===false) {
			$offset1=strpos(WT_UTF8_ALPHABET_UPPER, $chr1);
		}
		$offset2=strpos(WT_UTF8_ALPHABET_LOWER, $chr2);
		if ($offset2===false) {
			$offset2=strpos(WT_UTF8_ALPHABET_UPPER, $chr2);
		}
		if ($offset1!==false && $offset2!==false) {
			if ($offset1==$offset2) {
				continue;
			} else {
				return $offset1-$offset2;
			}
		}
		// Just compare by unicode order
		return strcmp($chr1, $chr2);
	}
	// Shortest string comes first.
	return ($strlen1-$strpos1)-($strlen2-$strpos2);
}

/*
 * Function to reverse RTL text for proper appearance on charts.
 *
 * GoogleChart and the GD library don't handle RTL text properly.  They assume that all text is LTR.
 * This function reverses the input text so that it will appear properly when rendered by GoogleChart
 * and by the GD library (the Circle Diagram).
 *
 * Note 1: Numbers must always be rendered LTR, even when the rest of the text is RTL.
 * Note 2: The visual direction of paired characters such as parentheses, brackets, directional
 *         quotation marks, etc. must be reversed so that the appearance of the RTL text is preserved.
 */
function reverseText($text) {
	$text = strip_tags(html_entity_decode($text,ENT_COMPAT,'UTF-8'));
	$text = str_replace(array('&lrm;', '&rlm;', WT_UTF8_LRM, WT_UTF8_RLM), '', $text);
	$textLanguage = WT_I18N::textScript($text);
	if ($textLanguage!='Hebr' && $textLanguage!='Arab') return $text;

	$reversedText = '';
	$numbers = '';
	while ($text!='') {
		$charLen = 1;
		$letter = substr($text, 0, 1);
		if ((ord($letter) & 0xE0) == 0xC0) $charLen = 2; // 2-byte sequence
		if ((ord($letter) & 0xF0) == 0xE0) $charLen = 3; // 3-byte sequence
		if ((ord($letter) & 0xF8) == 0xF0) $charLen = 4; // 4-byte sequence

		$letter = substr($text, 0, $charLen);
		$text = substr($text, $charLen);
		if (strpos(WT_UTF8_DIGITS, $letter)!==false) {
			$numbers .= $letter; // accumulate numbers in LTR mode
		} else {
			$reversedText = $numbers.$reversedText; // emit any waiting LTR numbers now
			$numbers = '';
			if (strpos(WT_UTF8_PARENTHESES1, $letter)!==false) {
				$reversedText = substr(WT_UTF8_PARENTHESES2, strpos(WT_UTF8_PARENTHESES1, $letter), strlen($letter)).$reversedText;
			} else {
				$reversedText = $letter.$reversedText;
			}
		}
	}

	$reversedText = $numbers.$reversedText; // emit any waiting LTR numbers now
	return $reversedText;
}

// This is a list of all reversable character conversions from the UNICODE 5.1 database.
// It excludes ambiguous (dotless i) and mixed-case (Dz) characters.
// The characters should be arranged in default unicode-collation order.
define('WT_UTF8_ALPHABET_LOWER', 'aàáâãäåāăąǎǟǡǻȁȃȧḁạảấầẩẫậắằẳẵặⓐａæǣǽbḃḅḇⓑｂƀɓƃcçćĉċčḉⅽⓒｃƈdďḋḍḏḑḓⅾⓓｄǆǳđɖɗƌðeèéêëēĕėęěȅȇȩḕḗḙḛḝẹẻẽếềểễệⓔｅǝəɛfḟⓕｆƒgĝğġģǧǵḡⓖｇǥɠɣƣhĥȟḣḥḧḩḫⓗｈƕħiìíîïĩīĭįǐȉȋḭḯỉịⅰⓘｉⅱⅲĳⅳⅸɨɩjĵⓙｊkķǩḱḳḵⓚｋƙlĺļľḷḹḻḽⅼⓛｌŀǉłƚmḿṁṃⅿⓜｍnñńņňǹṅṇṉṋⓝｎǌɲƞŋoòóôõöōŏőơǒǫǭȍȏȫȭȯȱṍṏṑṓọỏốồổỗộớờởỡợⓞｏœøǿɔɵȣpṕṗⓟｐƥqⓠｑrŕŗřȑȓṙṛṝṟⓡｒʀsśŝşšșṡṣṥṧṩⓢｓʃtţťțṫṭṯṱⓣｔŧƭʈuùúûüũūŭůűųưǔǖǘǚǜȕȗṳṵṷṹṻụủứừửữựⓤｕʉɯʊvṽṿⅴⓥｖⅵⅶⅷʋʌwŵẁẃẅẇẉⓦｗxẋẍⅹⓧｘⅺⅻyýÿŷȳẏỳỵỷỹⓨｙƴzźżžẑẓẕⓩｚƶȥǯʒƹȝþƿƨƽƅάαἀἁἂἃἄἅἆἇὰάᾀᾁᾂᾃᾄᾅᾆᾇᾰᾱᾳβγδέεἐἑἒἓἔἕὲέϝϛζήηἠἡἢἣἤἥἦἧὴήᾐᾑᾒᾓᾔᾕᾖᾗῃθϊἰἱἲἳἴἵἶἷὶίῐῑκϗλμνξοόὀὁὂὃὄὅὸόπϟϙρῥσϲτυϋύὑὓὕὗὺύῠῡφχψωώὠὡὢὣὤὥὦὧὼώᾠᾡᾢᾣᾤᾥᾦᾧῳϡϸϻϣϥϧϩϫϭϯаӑӓәӛӕбвгґғҕдԁђԃѓҙеѐёӗєжӂӝҗзԅӟѕӡԇиѝӣҋӥіїйјкқӄҡҟҝлӆљԉмӎнӊңӈҥњԋоӧөӫпҧҁрҏсԍҫтԏҭћќуӯўӱӳүұѹфхҳһѡѿѽѻцҵчӵҷӌҹҽҿџшщъыӹьҍѣэӭюяѥѧѫѩѭѯѱѳѵѷҩաբգդեզէըթժիլխծկհձղճմյնշոչպջռսվտրցւփքօֆȼɂɇɉɋɍɏͱͳͷͻͼͽӏӷӻӽӿԑԓԕԗԙԛԝԟԡԣԥᵹᵽỻỽỿⅎↄⰰⰱⰲⰳⰴⰵⰶⰷⰸⰹⰺⰻⰼⰽⰾⰿⱀⱁⱂⱃⱄⱅⱆⱇⱈⱉⱊⱋⱌⱍⱎⱏⱐⱑⱒⱓⱔⱕⱖⱗⱘⱙⱚⱛⱜⱝⱞⱡⱨⱪⱬⱳⱶⲁⲃⲅⲇⲉⲋⲍⲏⲑⲓⲕⲗⲙⲛⲝⲟⲡⲣⲥⲧⲩⲫⲭⲯⲱⲳⲵⲷⲹⲻⲽⲿⳁⳃⳅⳇⳉⳋⳍⳏⳑⳓⳕⳗⳙⳛⳝⳟⳡⳣⳬⳮⴀⴁⴂⴃⴄⴅⴆⴇⴈⴉⴊⴋⴌⴍⴎⴏⴐⴑⴒⴓⴔⴕⴖⴗⴘⴙⴚⴛⴜⴝⴞⴟⴠⴡⴢⴣⴤⴥꙁꙃꙅꙇꙉꙋꙍꙏꙑꙓꙕꙗꙙꙛꙝꙟꙣꙥꙧꙩꙫꙭꚁꚃꚅꚇꚉꚋꚍꚏꚑꚓꚕꚗꜣꜥꜧꜩꜫꜭꜯꜳꜵꜷꜹꜻꜽꜿꝁꝃꝅꝇꝉꝋꝍꝏꝑꝓꝕꝗꝙꝛꝝꝟꝡꝣꝥꝧꝩꝫꝭꝯꝺꝼꝿꞁꞃꞅꞇꞌ');
define('WT_UTF8_ALPHABET_UPPER', 'AÀÁÂÃÄÅĀĂĄǍǞǠǺȀȂȦḀẠẢẤẦẨẪẬẮẰẲẴẶⒶＡÆǢǼBḂḄḆⒷＢɃƁƂCÇĆĈĊČḈⅭⒸＣƇDĎḊḌḎḐḒⅮⒹＤǄǱĐƉƊƋÐEÈÉÊËĒĔĖĘĚȄȆȨḔḖḘḚḜẸẺẼẾỀỂỄỆⒺＥƎƏƐFḞⒻＦƑGĜĞĠĢǦǴḠⒼＧǤƓƔƢHĤȞḢḤḦḨḪⒽＨǶĦIÌÍÎÏĨĪĬĮǏȈȊḬḮỈỊⅠⒾＩⅡⅢĲⅣⅨƗƖJĴⒿＪKĶǨḰḲḴⓀＫƘLĹĻĽḶḸḺḼⅬⓁＬĿǇŁȽMḾṀṂⅯⓂＭNÑŃŅŇǸṄṆṈṊⓃＮǊƝȠŊOÒÓÔÕÖŌŎŐƠǑǪǬȌȎȪȬȮȰṌṎṐṒỌỎỐỒỔỖỘỚỜỞỠỢⓄＯŒØǾƆƟȢPṔṖⓅＰƤQⓆＱRŔŖŘȐȒṘṚṜṞⓇＲƦSŚŜŞŠȘṠṢṤṦṨⓈＳƩTŢŤȚṪṬṮṰⓉＴŦƬƮUÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛȔȖṲṴṶṸṺỤỦỨỪỬỮỰⓊＵɄƜƱVṼṾⅤⓋＶⅥⅦⅧƲɅWŴẀẂẄẆẈⓌＷXẊẌⅩⓍＸⅪⅫYÝŸŶȲẎỲỴỶỸⓎＹƳZŹŻŽẐẒẔⓏＺƵȤǮƷƸȜÞǷƧƼƄΆΑἈἉἊἋἌἍἎἏᾺΆᾈᾉᾊᾋᾌᾍᾎᾏᾸᾹᾼΒΓΔΈΕἘἙἚἛἜἝῈΈϜϚΖΉΗἨἩἪἫἬἭἮἯῊΉᾘᾙᾚᾛᾜᾝᾞᾟῌΘΪἸἹἺἻἼἽἾἿῚΊῘῙΚϏΛΜΝΞΟΌὈὉὊὋὌὍῸΌΠϞϘΡῬΣϹΤΥΫΎὙὛὝὟῪΎῨῩΦΧΨΩΏὨὩὪὫὬὭὮὯῺΏᾨᾩᾪᾫᾬᾭᾮᾯῼϠϷϺϢϤϦϨϪϬϮАӐӒӘӚӔБВГҐҒҔДԀЂԂЃҘЕЀЁӖЄЖӁӜҖЗԄӞЅӠԆИЍӢҊӤІЇЙЈКҚӃҠҞҜЛӅЉԈМӍНӉҢӇҤЊԊОӦӨӪПҦҀРҎСԌҪТԎҬЋЌУӮЎӰӲҮҰѸФХҲҺѠѾѼѺЦҴЧӴҶӋҸҼҾЏШЩЪЫӸЬҌѢЭӬЮЯѤѦѪѨѬѮѰѲѴѶҨԱԲԳԴԵԶԷԸԹԺԻԼԽԾԿՀՁՂՃՄՅՆՇՈՉՊՋՌՍՎՏՐՑՒՓՔՕՖȻɁɆɈɊɌɎͰͲͶϽϾϿӀӶӺӼӾԐԒԔԖԘԚԜԞԠԢԤꝽⱣỺỼỾℲↃⰀⰁⰂⰃⰄⰅⰆⰇⰈⰉⰊⰋⰌⰍⰎⰏⰐⰑⰒⰓⰔⰕⰖⰗⰘⰙⰚⰛⰜⰝⰞⰟⰠⰡⰢⰣⰤⰥⰦⰧⰨⰩⰪⰫⰬⰭⰮⱠⱧⱩⱫⱲⱵⲀⲂⲄⲆⲈⲊⲌⲎⲐⲒⲔⲖⲘⲚⲜⲞⲠⲢⲤⲦⲨⲪⲬⲮⲰⲲⲴⲶⲸⲺⲼⲾⳀⳂⳄⳆⳈⳊⳌⳎⳐⳒⳔⳖⳘⳚⳜⳞⳠⳢⳫⳭႠႡႢႣႤႥႦႧႨႩႪႫႬႭႮႯႰႱႲႳႴႵႶႷႸႹႺႻႼႽႾႿჀჁჂჃჄჅꙀꙂꙄꙆꙈꙊꙌꙎꙐꙒꙔꙖꙘꙚꙜꙞꙢꙤꙦꙨꙪꙬꚀꚂꚄꚆꚈꚊꚌꚎꚐꚒꚔꚖꜢꜤꜦꜨꜪꜬꜮꜲꜴꜶꜸꜺꜼꜾꝀꝂꝄꝆꝈꝊꝌꝎꝐꝒꝔꝖꝘꝚꝜꝞꝠꝢꝤꝦꝨꝪꝬꝮꝹꝻꝾꞀꞂꞄꞆꞋ');

