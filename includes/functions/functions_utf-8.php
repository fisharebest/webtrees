<?php
/**
 * UTF-8 versions of PHP string functions
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_UTF_8_PHP', '');

// Identify the script used in a given string.  Just look at the first significant character.
// If there are mixed scripts, then the enclosed scripts should already have been wrapped
// in lrm/rlm markup.
function utf8_script($string) {
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
		if (strpos(WT_UTF8_LATIN_CHARACTERS, $chr)!==false) {
			return 'latin';
		} elseif (strpos(WT_UTF8_CYRILLIC_CHARACTERS, $chr)!==false) {
			return 'cyrillic';
		} elseif (strpos(WT_UTF8_HEBREW_CHARACTERS, $chr)!==false) {
			return 'hebrew';
		} elseif (strpos(WT_UTF8_GREEK_CHARACTERS, $chr)!==false) {
			return 'greek';
		} elseif (strpos(WT_UTF8_ARABIC_CHARACTERS, $chr)!==false) {
			return 'arabic';
		}
		$pos+=$chrlen;
	}
	// No characters with strong directionality.
	return 'common';
}

// Determine whether a string contains LTR or RTL characters
function utf8_direction($string) {
	switch (utf8_script($string)) {
	case 'latin':
	case 'cyrillic':
	case 'greek':
		return 'ltr';
	case 'arabic':
	case 'hebrew':
		return 'rtl';
	default:
		return 'unknown';
	}
}

function utf8_strtoupper($string) {
	global $ALPHABET_lower, $ALPHABET_upper; // Language-specific conversions, e.g. Turkish dotless i
$ALPHABET_lower='';$ALPHABET_upper='';
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
			while ((ord($string[$start]) & 0xC0) == 0x80) {
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

function utf8_strcmp($string1, $string2) {
	// Language-specific conversions, e.g. Turkish dotless i
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
		if (($offset1=strpos($ALPHABET_lower, $chr1))!==false && ($offset2=strpos($ALPHABET_lower, $chr2))!==false) {
			return $offset1-$offset2;
		}
		if (($offset1=strpos($ALPHABET_upper, $chr1))!==false && ($offset2=strpos($ALPHABET_upper, $chr2))!==false) {
			return $offset1-$offset2;
		}
		// Try the global alphabet next
		if (($offset1=strpos(WT_UTF8_ALPHABET_LOWER, $chr1))!==false && ($offset2=strpos(WT_UTF8_ALPHABET_LOWER, $chr2))!==false) {
			return $offset1-$offset2;
		}
		if (($offset1=strpos(WT_UTF8_ALPHABET_UPPER, $chr1))!==false && ($offset2=strpos(WT_UTF8_ALPHABET_UPPER, $chr2))!==false) {
			return $offset1-$offset2;
		}
		// Just compare by unicode order
		return strcmp($chr1, $chr2);
	}
	// Shortest string comes first.
	return ($strlen1-$strpos1)-($strlen2-$strpos2);
}

function utf8_strcasecmp($string1, $string2) {
	// Language-specific alphabet sequence
	global $ALPHABET_upper;

	$string1=utf8_strtoupper($string1);
	$string2=utf8_strtoupper($string2);
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
		if (($offset1=strpos($ALPHABET_upper, $chr1))!==false && ($offset2=strpos($ALPHABET_upper, $chr2))!==false) {
			return $offset1-$offset2;
		}
		// Try the global alphabet next
		if (($offset1=strpos(WT_UTF8_ALPHABET_UPPER, $chr1))!==false && ($offset2=strpos(WT_UTF8_ALPHABET_UPPER, $chr2))!==false) {
			return $offset1-$offset2;
		}
		// Just compare by unicode order
		return strcmp($chr1, $chr2);
	}
	// Shortest string comes first.
	return ($strlen1-$strpos1)-($strlen2-$strpos2);
}

function utf8_wordwrap($string, $width=75, $sep="\n", $cut=false) {
	$out='';
	while ($string) {
		$sub=utf8_substr($string, 0, $width);
		$spacepos=strrpos($sub, ' ');
		if ($spacepos==false) {
			// No space on line?
			if ($cut) {
				$out.=$sub.$sep;
				$string=substr($string, strlen($sub));
			} else {
				$spacepos=strpos($string, ' ');
				if ($spacepos==false) {
					$out.=$string;
					$string='';
				} else {
					$out.=substr($string, 0, $spacepos).$sep;
					$string=substr($string, $spacepos+1);
				}
			}
		} else {
			// Split at space;
			$out.=substr($string, 0, $spacepos).$sep;
			$string=substr($string, $spacepos+1);
		}

	}
	return $out;
}

// This is a list of digits.  Note that arabic digits are displayed LTR, even in RTL text
define('WT_UTF8_DIGITS', '0123456789٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹');
// This is a list of parentheses, which need special RTL logic.
define('WT_UTF8_PARENTHESES', ')(][}{><»«﴾﴿‹›““”‘‘’');

// This is a list of all reversable character conversions from the UNICODE 5.1 database.
// It excludes ambiguous (dotless i) and mixed-case (Dz) characters.
// The characters should be arranged in default unicode-collation order.
define('WT_UTF8_ALPHABET_LOWER', 'aàáâãäåāăąǎǟǡǻȁȃȧḁạảấầẩẫậắằẳẵặⓐａæǣǽbḃḅḇⓑｂƀɓƃcçćĉċčḉⅽⓒｃƈdďḋḍḏḑḓⅾⓓｄǆǳđɖɗƌðeèéêëēĕėęěȅȇȩḕḗḙḛḝẹẻẽếềểễệⓔｅǝəɛfḟⓕｆƒgĝğġģǧǵḡⓖｇǥɠɣƣhĥȟḣḥḧḩḫⓗｈƕħiìíîïĩīĭįǐȉȋḭḯỉịⅰⓘｉⅱⅲĳⅳⅸɨɩjĵⓙｊkķǩḱḳḵⓚｋƙlĺļľḷḹḻḽⅼⓛｌŀǉłƚmḿṁṃⅿⓜｍnñńņňǹṅṇṉṋⓝｎǌɲƞŋoòóôõöōŏőơǒǫǭȍȏȫȭȯȱṍṏṑṓọỏốồổỗộớờởỡợⓞｏœøǿɔɵȣpṕṗⓟｐƥqⓠｑrŕŗřȑȓṙṛṝṟⓡｒʀsśŝşšșṡṣṥṧṩⓢｓʃtţťțṫṭṯṱⓣｔŧƭʈuùúûüũūŭůűųưǔǖǘǚǜȕȗṳṵṷṹṻụủứừửữựⓤｕʉɯʊvṽṿⅴⓥｖⅵⅶⅷʋʌwŵẁẃẅẇẉⓦｗxẋẍⅹⓧｘⅺⅻyýÿŷȳẏỳỵỷỹⓨｙƴzźżžẑẓẕⓩｚƶȥǯʒƹȝþƿƨƽƅάαἀἁἂἃἄἅἆἇὰάᾀᾁᾂᾃᾄᾅᾆᾇᾰᾱᾳβγδέεἐἑἒἓἔἕὲέϝϛζήηἠἡἢἣἤἥἦἧὴήᾐᾑᾒᾓᾔᾕᾖᾗῃθϊἰἱἲἳἴἵἶἷὶίῐῑκϗλμνξοόὀὁὂὃὄὅὸόπϟϙρῥσϲτυϋύὑὓὕὗὺύῠῡφχψωώὠὡὢὣὤὥὦὧὼώᾠᾡᾢᾣᾤᾥᾦᾧῳϡϸϻϣϥϧϩϫϭϯаӑӓәӛӕбвгґғҕдԁђԃѓҙеѐёӗєжӂӝҗзԅӟѕӡԇиѝӣҋӥіїйјкқӄҡҟҝлӆљԉмӎнӊңӈҥњԋоӧөӫпҧҁрҏсԍҫтԏҭћќуӯўӱӳүұѹфхҳһѡѿѽѻцҵчӵҷӌҹҽҿџшщъыӹьҍѣэӭюяѥѧѫѩѭѯѱѳѵѷҩաբգդեզէըթժիլխծկհձղճմյնշոչպջռսվտրցւփքօֆȼɂɇɉɋɍɏͱͳͷͻͼͽӏӷӻӽӿԑԓԕԗԙԛԝԟԡԣԥᵹᵽỻỽỿⅎↄⰰⰱⰲⰳⰴⰵⰶⰷⰸⰹⰺⰻⰼⰽⰾⰿⱀⱁⱂⱃⱄⱅⱆⱇⱈⱉⱊⱋⱌⱍⱎⱏⱐⱑⱒⱓⱔⱕⱖⱗⱘⱙⱚⱛⱜⱝⱞⱡⱨⱪⱬⱳⱶⲁⲃⲅⲇⲉⲋⲍⲏⲑⲓⲕⲗⲙⲛⲝⲟⲡⲣⲥⲧⲩⲫⲭⲯⲱⲳⲵⲷⲹⲻⲽⲿⳁⳃⳅⳇⳉⳋⳍⳏⳑⳓⳕⳗⳙⳛⳝⳟⳡⳣⳬⳮⴀⴁⴂⴃⴄⴅⴆⴇⴈⴉⴊⴋⴌⴍⴎⴏⴐⴑⴒⴓⴔⴕⴖⴗⴘⴙⴚⴛⴜⴝⴞⴟⴠⴡⴢⴣⴤⴥꙁꙃꙅꙇꙉꙋꙍꙏꙑꙓꙕꙗꙙꙛꙝꙟꙣꙥꙧꙩꙫꙭꚁꚃꚅꚇꚉꚋꚍꚏꚑꚓꚕꚗꜣꜥꜧꜩꜫꜭꜯꜳꜵꜷꜹꜻꜽꜿꝁꝃꝅꝇꝉꝋꝍꝏꝑꝓꝕꝗꝙꝛꝝꝟꝡꝣꝥꝧꝩꝫꝭꝯꝺꝼꝿꞁꞃꞅꞇꞌ');
define('WT_UTF8_ALPHABET_UPPER', 'AÀÁÂÃÄÅĀĂĄǍǞǠǺȀȂȦḀẠẢẤẦẨẪẬẮẰẲẴẶⒶＡÆǢǼBḂḄḆⒷＢɃƁƂCÇĆĈĊČḈⅭⒸＣƇDĎḊḌḎḐḒⅮⒹＤǄǱĐƉƊƋÐEÈÉÊËĒĔĖĘĚȄȆȨḔḖḘḚḜẸẺẼẾỀỂỄỆⒺＥƎƏƐFḞⒻＦƑGĜĞĠĢǦǴḠⒼＧǤƓƔƢHĤȞḢḤḦḨḪⒽＨǶĦIÌÍÎÏĨĪĬĮǏȈȊḬḮỈỊⅠⒾＩⅡⅢĲⅣⅨƗƖJĴⒿＪKĶǨḰḲḴⓀＫƘLĹĻĽḶḸḺḼⅬⓁＬĿǇŁȽMḾṀṂⅯⓂＭNÑŃŅŇǸṄṆṈṊⓃＮǊƝȠŊOÒÓÔÕÖŌŎŐƠǑǪǬȌȎȪȬȮȰṌṎṐṒỌỎỐỒỔỖỘỚỜỞỠỢⓄＯŒØǾƆƟȢPṔṖⓅＰƤQⓆＱRŔŖŘȐȒṘṚṜṞⓇＲƦSŚŜŞŠȘṠṢṤṦṨⓈＳƩTŢŤȚṪṬṮṰⓉＴŦƬƮUÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛȔȖṲṴṶṸṺỤỦỨỪỬỮỰⓊＵɄƜƱVṼṾⅤⓋＶⅥⅦⅧƲɅWŴẀẂẄẆẈⓌＷXẊẌⅩⓍＸⅪⅫYÝŸŶȲẎỲỴỶỸⓎＹƳZŹŻŽẐẒẔⓏＺƵȤǮƷƸȜÞǷƧƼƄΆΑἈἉἊἋἌἍἎἏᾺΆᾈᾉᾊᾋᾌᾍᾎᾏᾸᾹᾼΒΓΔΈΕἘἙἚἛἜἝῈΈϜϚΖΉΗἨἩἪἫἬἭἮἯῊΉᾘᾙᾚᾛᾜᾝᾞᾟῌΘΪἸἹἺἻἼἽἾἿῚΊῘῙΚϏΛΜΝΞΟΌὈὉὊὋὌὍῸΌΠϞϘΡῬΣϹΤΥΫΎὙὛὝὟῪΎῨῩΦΧΨΩΏὨὩὪὫὬὭὮὯῺΏᾨᾩᾪᾫᾬᾭᾮᾯῼϠϷϺϢϤϦϨϪϬϮАӐӒӘӚӔБВГҐҒҔДԀЂԂЃҘЕЀЁӖЄЖӁӜҖЗԄӞЅӠԆИЍӢҊӤІЇЙЈКҚӃҠҞҜЛӅЉԈМӍНӉҢӇҤЊԊОӦӨӪПҦҀРҎСԌҪТԎҬЋЌУӮЎӰӲҮҰѸФХҲҺѠѾѼѺЦҴЧӴҶӋҸҼҾЏШЩЪЫӸЬҌѢЭӬЮЯѤѦѪѨѬѮѰѲѴѶҨԱԲԳԴԵԶԷԸԹԺԻԼԽԾԿՀՁՂՃՄՅՆՇՈՉՊՋՌՍՎՏՐՑՒՓՔՕՖȻɁɆɈɊɌɎͰͲͶϽϾϿӀӶӺӼӾԐԒԔԖԘԚԜԞԠԢԤꝽⱣỺỼỾℲↃⰀⰁⰂⰃⰄⰅⰆⰇⰈⰉⰊⰋⰌⰍⰎⰏⰐⰑⰒⰓⰔⰕⰖⰗⰘⰙⰚⰛⰜⰝⰞⰟⰠⰡⰢⰣⰤⰥⰦⰧⰨⰩⰪⰫⰬⰭⰮⱠⱧⱩⱫⱲⱵⲀⲂⲄⲆⲈⲊⲌⲎⲐⲒⲔⲖⲘⲚⲜⲞⲠⲢⲤⲦⲨⲪⲬⲮⲰⲲⲴⲶⲸⲺⲼⲾⳀⳂⳄⳆⳈⳊⳌⳎⳐⳒⳔⳖⳘⳚⳜⳞⳠⳢⳫⳭႠႡႢႣႤႥႦႧႨႩႪႫႬႭႮႯႰႱႲႳႴႵႶႷႸႹႺႻႼႽႾႿჀჁჂჃჄჅꙀꙂꙄꙆꙈꙊꙌꙎꙐꙒꙔꙖꙘꙚꙜꙞꙢꙤꙦꙨꙪꙬꚀꚂꚄꚆꚈꚊꚌꚎꚐꚒꚔꚖꜢꜤꜦꜨꜪꜬꜮꜲꜴꜶꜸꜺꜼꜾꝀꝂꝄꝆꝈꝊꝌꝎꝐꝒꝔꝖꝘꝚꝜꝞꝠꝢꝤꝦꝨꝪꝬꝮꝹꝻꝾꞀꞂꞄꞆꞋ');

// These are lists of all characters for a given script.
// We should include scripts for all languages that we support, as
// we use this to identify LTR/RTL when embedding strings within strings.
define('WT_UTF8_ARABIC_CHARACTERS', '؆؇؈؉؊؋؍؎؏ؘؙؚؐؑؒؓؔؕؖؗ؞ءآأؤإئابةتثجحخدذرزسشصضطظعغػؼؽؾؿفقكلمنهوىيٖٜٗ٘ٙٚٛٝٞ٪٫٬٭ٮٯٱٲٳٴٵٶٷٸٹٺٻټٽپٿڀځڂڃڄڅچڇڈډڊڋڌڍڎڏڐڑڒړڔڕږڗژڙښڛڜڝڞڟڠڡڢڣڤڥڦڧڨکڪګڬڭڮگڰڱڲڳڴڵڶڷڸڹںڻڼڽھڿۀہۂۃۄۅۆۇۈۉۊۋیۍێۏېۑےۓ۔ەۖۗۘۙۚۛۜ۞ۣ۟۠ۡۢۤۥۦۧۨ۩۪ۭ۫۬ۮۯ۰۱۲۳۴۵۶۷۸۹ۺۻۼ۽۾ۿݐݑݒݓݔݕݖݗݘݙݚݛݜݝݞݟݠݡݢݣݤݥݦݧݨݩݪݫݬݭݮݯݰݱݲݳݴݵݶݷݸݹݺݻݼݽݾݿﭐﭑﭒﭓﭔﭕﭖﭗﭘﭙﭚﭛﭜﭝﭞﭟﭠﭡﭢﭣﭤﭥﭦﭧﭨﭩﭪﭫﭬﭭﭮﭯﭰﭱﭲﭳﭴﭵﭶﭷﭸﭹﭺﭻﭼﭽﭾﭿﮀﮁﮂﮃﮄﮅﮆﮇﮈﮉﮊﮋﮌﮍﮎﮏﮐﮑﮒﮓﮔﮕﮖﮗﮘﮙﮚﮛﮜﮝﮞﮟﮠﮡﮢﮣﮤﮥﮦﮧﮨﮩﮪﮫﮬﮭﮮﮯﮰﮱﯓﯔﯕﯖﯗﯘﯙﯚﯛﯜﯝﯞﯟﯠﯡﯢﯣﯤﯥﯦﯧﯨﯩﯪﯫﯬﯭﯮﯯﯰﯱﯲﯳﯴﯵﯶﯷﯸﯹﯺﯻﯼﯽﯾﯿﰀﰁﰂﰃﰄﰅﰆﰇﰈﰉﰊﰋﰌﰍﰎﰏﰐﰑﰒﰓﰔﰕﰖﰗﰘﰙﰚﰛﰜﰝﰞﰟﰠﰡﰢﰣﰤﰥﰦﰧﰨﰩﰪﰫﰬﰭﰮﰯﰰﰱﰲﰳﰴﰵﰶﰷﰸﰹﰺﰻﰼﰽﰾﰿﱀﱁﱂﱃﱄﱅﱆﱇﱈﱉﱊﱋﱌﱍﱎﱏﱐﱑﱒﱓﱔﱕﱖﱗﱘﱙﱚﱛﱜﱝﱞﱟﱠﱡﱢﱣﱤﱥﱦﱧﱨﱩﱪﱫﱬﱭﱮﱯﱰﱱﱲﱳﱴﱵﱶﱷﱸﱹﱺﱻﱼﱽﱾﱿﲀﲁﲂﲃﲄﲅﲆﲇﲈﲉﲊﲋﲌﲍﲎﲏﲐﲑﲒﲓﲔﲕﲖﲗﲘﲙﲚﲛﲜﲝﲞﲟﲠﲡﲢﲣﲤﲥﲦﲧﲨﲩﲪﲫﲬﲭﲮﲯﲰﲱﲲﲳﲴﲵﲶﲷﲸﲹﲺﲻﲼﲽﲾﲿﳀﳁﳂﳃﳄﳅﳆﳇﳈﳉﳊﳋﳌﳍﳎﳏﳐﳑﳒﳓﳔﳕﳖﳗﳘﳙﳚﳛﳜﳝﳞﳟﳠﳡﳢﳣﳤﳥﳦﳧﳨﳩﳪﳫﳬﳭﳮﳯﳰﳱﳲﳳﳴﳵﳶﳷﳸﳹﳺﳻﳼﳽﳾﳿﴀﴁﴂﴃﴄﴅﴆﴇﴈﴉﴊﴋﴌﴍﴎﴏﴐﴑﴒﴓﴔﴕﴖﴗﴘﴙﴚﴛﴜﴝﴞﴟﴠﴡﴢﴣﴤﴥﴦﴧﴨﴩﴪﴫﴬﴭﴮﴯﴰﴱﴲﴳﴴﴵﴶﴷﴸﴹﴺﴻﴼﴽﵐﵑﵒﵓﵔﵕﵖﵗﵘﵙﵚﵛﵜﵝﵞﵟﵠﵡﵢﵣﵤﵥﵦﵧﵨﵩﵪﵫﵬﵭﵮﵯﵰﵱﵲﵳﵴﵵﵶﵷﵸﵹﵺﵻﵼﵽﵾﵿﶀﶁﶂﶃﶄﶅﶆﶇﶈﶉﶊﶋﶌﶍﶎﶏﶒﶓﶔﶕﶖﶗﶘﶙﶚﶛﶜﶝﶞﶟﶠﶡﶢﶣﶤﶥﶦﶧﶨﶩﶪﶫﶬﶭﶮﶯﶰﶱﶲﶳﶴﶵﶶﶷﶸﶹﶺﶻﶼﶽﶾﶿﷀﷁﷂﷃﷄﷅﷆﷇﷰﷱﷲﷳﷴﷵﷶﷷﷸﷹﷺﷻ﷼ﹰﹱﹲﹳﹴﹶﹷﹸﹹﹺﹻﹼﹽﹾﹿﺀﺁﺂﺃﺄﺅﺆﺇﺈﺉﺊﺋﺌﺍﺎﺏﺐﺑﺒﺓﺔﺕﺖﺗﺘﺙﺚﺛﺜﺝﺞﺟﺠﺡﺢﺣﺤﺥﺦﺧﺨﺩﺪﺫﺬﺭﺮﺯﺰﺱﺲﺳﺴﺵﺶﺷﺸﺹﺺﺻﺼﺽﺾﺿﻀﻁﻂﻃﻄﻅﻆﻇﻈﻉﻊﻋﻌﻍﻎﻏﻐﻑﻒﻓﻔﻕﻖﻗﻘﻙﻚﻛﻜﻝﻞﻟﻠﻡﻢﻣﻤﻥﻦﻧﻨﻩﻪﻫﻬﻭﻮﻯﻰﻱﻲﻳﻴﻵﻶﻷﻸﻹﻺﻻﻼ');
define('WT_UTF8_CYRILLIC_CHARACTERS', 'ЀЁЂЃЄЅІЇЈЉЊЋЌЍЎЏАБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдежзийклмнопрстуфхцчшщъыьэюяѐёђѓєѕіїјљњћќѝўџѠѡѢѣѤѥѦѧѨѩѪѫѬѭѮѯѰѱѲѳѴѵѶѷѸѹѺѻѼѽѾѿҀҁ҂҃҄҇҈҉ҊҋҌҍҎҏҐґҒғҔҕҖҗҘҙҚқҜҝҞҟҠҡҢңҤҥҦҧҨҩҪҫҬҭҮүҰұҲҳҴҵҶҷҸҹҺһҼҽҾҿӀӁӂӃӄӅӆӇӈӉӊӋӌӍӎӏӐӑӒӓӔӕӖӗӘәӚӛӜӝӞӟӠӡӢӣӤӥӦӧӨөӪӫӬӭӮӯӰӱӲӳӴӵӶӷӸӹӺӻӼӽӾӿԀԁԂԃԄԅԆԇԈԉԊԋԌԍԎԏԐԑԒԓԔԕԖԗԘԙԚԛԜԝԞԟԠԡԢԣԤԥᴫᵸⷠⷡⷢⷣⷤⷥⷦⷧⷨⷩⷪⷫⷬⷭⷮⷯⷰⷱⷲⷳⷴⷵⷶⷷⷸⷹⷺⷻⷼⷽⷾⷿꙀꙁꙂꙃꙄꙅꙆꙇꙈꙉꙊꙋꙌꙍꙎꙏꙐꙑꙒꙓꙔꙕꙖꙗꙘꙙꙚꙛꙜꙝꙞꙟꙢꙣꙤꙥꙦꙧꙨꙩꙪꙫꙬꙭꙮ꙯꙰꙱꙲꙳꙼꙽꙾ꙿꚀꚁꚂꚃꚄꚅꚆꚇꚈꚉꚊꚋꚌꚍꚎꚏꚐꚑꚒꚓꚔꚕꚖꚗ');
define('WT_UTF8_GREEK_CHARACTERS', 'ͰͱͲͳ͵Ͷͷͺͻͼͽ΄ΆΈΉΊΌΎΏΐΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩΪΫάέήίΰαβγδεζηθικλμνξοπρςστυφχψωϊϋόύώϏϐϑϒϓϔϕϖϗϘϙϚϛϜϝϞϟϠϡϰϱϲϳϴϵ϶ϷϸϹϺϻϼϽϾϿᴦᴧᴨᴩᴪᵝᵞᵟᵠᵡᵦᵧᵨᵩᵪᶿἀἁἂἃἄἅἆἇἈἉἊἋἌἍἎἏἐἑἒἓἔἕἘἙἚἛἜἝἠἡἢἣἤἥἦἧἨἩἪἫἬἭἮἯἰἱἲἳἴἵἶἷἸἹἺἻἼἽἾἿὀὁὂὃὄὅὈὉὊὋὌὍὐὑὒὓὔὕὖὗὙὛὝὟὠὡὢὣὤὥὦὧὨὩὪὫὬὭὮὯὰάὲέὴήὶίὸόὺύὼώᾀᾁᾂᾃᾄᾅᾆᾇᾈᾉᾊᾋᾌᾍᾎᾏᾐᾑᾒᾓᾔᾕᾖᾗᾘᾙᾚᾛᾜᾝᾞᾟᾠᾡᾢᾣᾤᾥᾦᾧᾨᾩᾪᾫᾬᾭᾮᾯᾰᾱᾲᾳᾴᾶᾷᾸᾹᾺΆᾼ᾽ι᾿῀῁ῂῃῄῆῇῈΈῊΉῌ῍῎῏ῐῑῒΐῖῗῘῙῚΊ῝῞῟ῠῡῢΰῤῥῦῧῨῩῪΎῬ῭΅`ῲῳῴῶῷῸΌῺΏῼ´῾Ω');
define('WT_UTF8_HEBREW_CHARACTERS', 'ְֱֲֳִֵֶַָֹֺֻּֽ֑֖֛֢֣֤֥֦֧֪֚֭֮֒֓֔֕֗֘֙֜֝֞֟֠֡֨֩֫֬֯־ֿ׀ׁׂ׃ׅׄ׆ׇאבגדהוזחטיךכלםמןנסעףפץצקרשתװױײ׳״יִﬞײַﬠﬡﬢﬣﬤﬥﬦﬧﬨ﬩שׁשׂשּׁשּׂאַאָאּבּגּדּהּוּזּטּיּךּכּלּמּנּסּףּפּצּקּרּשּתּוֹבֿכֿפֿﭏ');
define('WT_UTF8_LATIN_CHARACTERS', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzªºÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿŀŁłŃńŅņŇňŉŊŋŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſƀƁƂƃƄƅƆƇƈƉƊƋƌƍƎƏƐƑƒƓƔƕƖƗƘƙƚƛƜƝƞƟƠơƢƣƤƥƦƧƨƩƪƫƬƭƮƯưƱƲƳƴƵƶƷƸƹƺƻƼƽƾƿǀǁǂǃǄǅǆǇǈǉǊǋǌǍǎǏǐǑǒǓǔǕǖǗǘǙǚǛǜǝǞǟǠǡǢǣǤǥǦǧǨǩǪǫǬǭǮǯǰǱǲǳǴǵǶǷǸǹǺǻǼǽǾǿȀȁȂȃȄȅȆȇȈȉȊȋȌȍȎȏȐȑȒȓȔȕȖȗȘșȚțȜȝȞȟȠȡȢȣȤȥȦȧȨȩȪȫȬȭȮȯȰȱȲȳȴȵȶȷȸȹȺȻȼȽȾȿɀɁɂɃɄɅɆɇɈɉɊɋɌɍɎɏɐɑɒɓɔɕɖɗɘəɚɛɜɝɞɟɠɡɢɣɤɥɦɧɨɩɪɫɬɭɮɯɰɱɲɳɴɵɶɷɸɹɺɻɼɽɾɿʀʁʂʃʄʅʆʇʈʉʊʋʌʍʎʏʐʑʒʓʔʕʖʗʘʙʚʛʜʝʞʟʠʡʢʣʤʥʦʧʨʩʪʫʬʭʮʯʰʱʲʳʴʵʶʷʸˠˡˢˣˤᴀᴁᴂᴃᴄᴅᴆᴇᴈᴉᴊᴋᴌᴍᴎᴏᴐᴑᴒᴓᴔᴕᴖᴗᴘᴙᴚᴛᴜᴝᴞᴟᴠᴡᴢᴣᴤᴥᴬᴭᴮᴯᴰᴱᴲᴳᴴᴵᴶᴷᴸᴹᴺᴻᴼᴽᴾᴿᵀᵁᵂᵃᵄᵅᵆᵇᵈᵉᵊᵋᵌᵍᵎᵏᵐᵑᵒᵓᵔᵕᵖᵗᵘᵙᵚᵛᵜᵢᵣᵤᵥᵫᵬᵭᵮᵯᵰᵱᵲᵳᵴᵵᵶᵷᵹᵺᵻᵼᵽᵾᵿᶀᶁᶂᶃᶄᶅᶆᶇᶈᶉᶊᶋᶌᶍᶎᶏᶐᶑᶒᶓᶔᶕᶖᶗᶘᶙᶚᶛᶜᶝᶞᶟᶠᶡᶢᶣᶤᶥᶦᶧᶨᶩᶪᶫᶬᶭᶮᶯᶰᶱᶲᶳᶴᶵᶶᶷᶸᶹᶺᶻᶼᶽᶾḀḁḂḃḄḅḆḇḈḉḊḋḌḍḎḏḐḑḒḓḔḕḖḗḘḙḚḛḜḝḞḟḠḡḢḣḤḥḦḧḨḩḪḫḬḭḮḯḰḱḲḳḴḵḶḷḸḹḺḻḼḽḾḿṀṁṂṃṄṅṆṇṈṉṊṋṌṍṎṏṐṑṒṓṔṕṖṗṘṙṚṛṜṝṞṟṠṡṢṣṤṥṦṧṨṩṪṫṬṭṮṯṰṱṲṳṴṵṶṷṸṹṺṻṼṽṾṿẀẁẂẃẄẅẆẇẈẉẊẋẌẍẎẏẐẑẒẓẔẕẖẗẘẙẚẛẜẝẞẟẠạẢảẤấẦầẨẩẪẫẬậẮắẰằẲẳẴẵẶặẸẹẺẻẼẽẾếỀềỂểỄễỆệỈỉỊịỌọỎỏỐốỒồỔổỖỗỘộỚớỜờỞởỠỡỢợỤụỦủỨứỪừỬửỮữỰựỲỳỴỵỶỷỸỹỺỻỼỽỾỿⁱⁿₐₑₒₓₔKÅℲⅎⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩⅪⅫⅬⅭⅮⅯⅰⅱⅲⅳⅴⅵⅶⅷⅸⅹⅺⅻⅼⅽⅾⅿↀↁↂↃↄↅↆↇↈⱠⱡⱢⱣⱤⱥⱦⱧⱨⱩⱪⱫⱬⱭⱮⱯⱰⱱⱲⱳⱴⱵⱶⱷⱸⱹⱺⱻⱼⱽⱾⱿꜢꜣꜤꜥꜦꜧꜨꜩꜪꜫꜬꜭꜮꜯꜰꜱꜲꜳꜴꜵꜶꜷꜸꜹꜺꜻꜼꜽꜾꜿꝀꝁꝂꝃꝄꝅꝆꝇꝈꝉꝊꝋꝌꝍꝎꝏꝐꝑꝒꝓꝔꝕꝖꝗꝘꝙꝚꝛꝜꝝꝞꝟꝠꝡꝢꝣꝤꝥꝦꝧꝨꝩꝪꝫꝬꝭꝮꝯꝰꝱꝲꝳꝴꝵꝶꝷꝸꝹꝺꝻꝼꝽꝾꝿꞀꞁꞂꞃꞄꞅꞆꞇꞋꞌꟻꟼꟽꟾꟿﬀﬁﬂﬃﬄﬅﬆＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚ');
