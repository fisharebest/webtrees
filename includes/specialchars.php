<?PHP
// Special Character tables, for use by Javascript to input characters
// that aren't on your keyboard
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2007 PGV Development Team
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$specialchar_languages = array(
	'af'  => WT_I18N::languageName('af'),
	'ar'  => WT_I18N::languageName('ar'),
	'cs'  => WT_I18N::languageName('cs'),
	'da'  => WT_I18N::languageName('da'),
	'de'  => WT_I18N::languageName('de'),
	'el'  => WT_I18N::languageName('el'),
	'en'  => WT_I18N::languageName('en'),
	'es'  => WT_I18N::languageName('es'),
	'eu'  => WT_I18N::languageName('eu'),
	'fi'  => WT_I18N::languageName('fi'),
	'fr'  => WT_I18N::languageName('fr'),
	'gd'  => WT_I18N::languageName('gd'),
	'haw' => WT_I18N::languageName('haw'),
	'he'  => WT_I18N::languageName('he'),
	'hu'  => WT_I18N::languageName('hu'),
	'is'  => WT_I18N::languageName('is'),
	'it'  => WT_I18N::languageName('it'),
	'lt'  => WT_I18N::languageName('lt'),
	'nl'  => WT_I18N::languageName('nl'),
	'nn'  => WT_I18N::languageName('nn'),
	'pl'  => WT_I18N::languageName('pl'),
	'pt'  => WT_I18N::languageName('pt'),
	'ru'  => WT_I18N::languageName('ru'),
	'sk'  => WT_I18N::languageName('sk'),
	'sl'  => WT_I18N::languageName('sl'),
	'sv'  => WT_I18N::languageName('sv'),
	'tr'  => WT_I18N::languageName('tr'),
	'vi'  => WT_I18N::languageName('vi'),
);

switch ($language_filter) {
case 'af':
	$ucspecialchars = array(
		'È', 'É', 'Ê', 'Ë', 'Î', 'Ï', 'Ô', 'Û',
	);
	$lcspecialchars = array(
		'è', 'é', 'ê', 'ë', 'î', 'ï', 'ô', 'û', 'ŉ',
	);
	break;
case 'cs':
	$ucspecialchars = array(
		'Á', 'Ą', 'Ä', 'É', 'Ę', 'Ě', 'Í', 'Ó', 'Ô', 'Ú', 'Ů', 'Ý', 'Č', 'Ĺ', 'Ň', 'Ŕ', 'Ř', 'Š', 'Ž',
	);
	$lcspecialchars = array(
		'á', 'ą', 'ä', 'é', 'ę', 'ě', 'í', 'ó', 'ô', 'ú', 'ů', 'ý', 'č', 'ď', 'ť', 'ĺ', 'ň', 'ŕ', 'ř', 'š', 'ž',
	);
	break;
case 'sk':
	$ucspecialchars = array(
		'Á', 'Ä', 'Č', 'Ď', 'É', 'Ě', 'Í', 'Ĺ', 'Ľ', 'Ň', 'Ó', 'Ô', 'Ŕ', 'Ř', 'Š', 'Ť', 'Ú', 'Ů', 'Ý', 'Ž',
	);
	$lcspecialchars = array(
		'á', 'ä', 'č', 'ď', 'é', 'ě', 'í', 'ĺ', 'ľ', 'ň', 'ó', 'ô', 'ŕ', 'ř', 'š', 'ť', 'ú', 'ů', 'ý', 'ž',
	);
	break;
case 'da':
	$ucspecialchars = array(
		'Å', 'Æ', 'É', 'Ø', 'Á', 'Í', 'Ó', 'Ú', 'Ý',
	);
	$lcspecialchars = array(
		'å', 'æ', 'é', 'ø', 'á', 'í', 'ó', 'ú', 'ý',
	);
	break;
case 'de':
	$ucspecialchars = array(
		'Ä', 'Ö', 'Ü', 'À', 'É',
	);
	$lcspecialchars = array(
		'ä', 'ö', 'ü', 'à', 'é', 'ß',
	);
	break;
case 'en':
	$ucspecialchars = array(
		'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'Ð', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï',
		'Ĳ', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Œ', 'Ø', 'Þ', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Ÿ',
	);
	$lcspecialchars = array(
		'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'ð', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï',
		'ĳ', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'œ', 'ø', 'þ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ß',
	);
	break;
case 'es':
	$ucspecialchars = array(
		'Á', 'É', 'Í', 'Ñ', 'Ó', 'Ú', 'Ü', 'Ç',
	);
	$lcspecialchars = array(
		'á', 'é', 'í', 'ñ', 'ó', 'ú', 'ü', 'ç',
	);
	break;
case 'eu':
	$ucspecialchars = array(
		'Ç',
	);
	$lcspecialchars = array(
		'ç',
	);
	break;
case 'fr':
	$ucspecialchars = array(
		'À', 'Â', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Î', 'Ï', 'Ô', 'Œ', 'Ù', 'Û', 'Ü', 'Ÿ',
	);
	$lcspecialchars = array(
		'à', 'â', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'î', 'ï', 'ô', 'œ', 'ù', 'û', 'ü', 'ÿ',
	);
	break;
case 'gd':
	$ucspecialchars = array(
		'Á', 'É', 'Í', 'Ó', 'Ú',
	);
	$lcspecialchars = array(
		'á', 'é', 'í', 'ó', 'ú',
	);
	break;
case 'is':
	$ucspecialchars = array(
		'Á', 'Æ', 'Ð', 'É', 'Í', 'Ó', 'Ö', 'Þ', 'Ú', 'Ý',
	);
	$lcspecialchars = array(
		'á', 'æ', 'ð', 'é', 'í', 'ó', 'ö', 'þ', 'ú', 'ý',
	);
	break;
case 'it':
	$ucspecialchars = array(
		'À', 'È', 'É', 'Ì', 'Í', 'Ò', 'Ó', 'Ù', 'Ú', 'Ï',
	);
	$lcspecialchars = array(
		'à', 'è', 'é', 'ì', 'í', 'ò', 'ó', 'ù', 'ú', 'ï',
	);
	break;
case 'hu':
	$ucspecialchars = array(
		'Á', 'É', 'Í', 'Ó', 'Ö', 'Ő', 'Ú', 'Ü', 'Ű',);
	$lcspecialchars = array(
		'á', 'é', 'í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű',
	);
	break;
case 'lt':
	$ucspecialchars = array(
		'Ą', 'Č', 'Ę', 'Ė', 'Į', 'Š', 'Ų', 'Ū', 'Ž',
	);
	$lcspecialchars = array(
		'ą', 'č', 'ę', 'ė', 'į', 'š', 'ų', 'ū', 'ž',
	);
	break;
case 'nl':
	$ucspecialchars = array(
		'Á', 'Â', 'È', 'É', 'Ê', 'Ë', 'Í', 'Ï', 'Ĳ', 'Ó', 'Ô', 'Ö', 'Ú', 'Ù', 'Ä', 'Û', 'Ü',
	);
	$lcspecialchars = array(
		'á', 'â', 'è', 'é', 'ê', 'ë', 'í', 'ï', 'ĳ', 'ó', 'ô', 'ö', 'ú', 'ù', 'ä', 'û', 'ü',
	);
	break;
case 'no':
	$ucspecialchars = array(
		'Æ', 'Ø', 'Å', 'À', 'É', 'Ê', 'Ó', 'Ò', 'Ô',
	);
	$lcspecialchars = array(
		'æ', 'ø', 'å', 'à', 'é', 'ê', 'ó', 'ò', 'ô',
	);
	break;
case 'hawaiian':
	$ucspecialchars = array(
		'Ā', 'Ē', 'Ī', 'Ō', 'Ū', '‘',
	);
	$lcspecialchars = array(
		'ā', 'ē', 'ī', 'ō', 'ū', '‘',
	);
	break;
case 'pl':
	$ucspecialchars = array(
		'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż',
	);
	$lcspecialchars = array(
		'ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż',
	);
	break;
case 'pt':
	$ucspecialchars = array(
		'À', 'Á', 'Â', 'Ã', 'Ç', 'É', 'Ê', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'Ü', 'È', 'Ò',
	);
	$lcspecialchars = array(
		'à', 'á', 'â', 'ã', 'ç', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'è', 'ò',
	);
	break;
case 'sl':
	$ucspecialchars = array(
		'Č', 'Š', 'Ž', 'Ć', 'Ð', 'Ä', 'Ö', 'Ü',
	);
	$lcspecialchars = array(
		'č', 'š', 'ž', 'ć', 'đ', 'ä', 'ö', 'ü',
	);
	break;
case 'fi':
	$ucspecialchars = array(
		'Ä', 'Ö', 'Å', 'Š', 'Ž',
	);
	$lcspecialchars = array(
		'ä', 'ö', 'å', 'š', 'ž',
	);
	break;
case 'sv':
	$ucspecialchars = array(
		'Ä', 'Å', 'É', 'Ö', 'Á', 'Ë', 'Ü',
	);
	$lcspecialchars = array(
		'ä', 'å', 'é', 'ö', 'á', 'ë', 'ü',
	);
	break;
case 'tr':
	$ucspecialchars = array(
		'Â', 'Ç', 'Ğ', 'Î', 'İ', 'Ö', 'Ş', 'Û', 'Ü',
	);
	$lcspecialchars = array(
		'â', 'ç', 'ğ', 'î', 'ı', 'ö', 'ş', 'û', 'ü',
	);
	break;
case 'el':
	$ucspecialchars = array(
		'Ά', 'Α', 'Β', 'Γ', 'Δ', 'Έ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ί', 'Ϊ', 'Ι', 'Κ', 'Λ', 'Μ',
		'Ν', 'Ξ', 'Ό', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Ύ', 'Ϋ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ώ', 'Ω',
	);
	$lcspecialchars = array(
		'ά', 'α', 'β', 'γ', 'δ', 'έ', 'ε', 'ζ', 'η', 'θ', 'ί', 'ϊ', 'ΐ', 'ι', 'κ', 'λ', 'μ', 'ν',
		'ξ', 'ό', 'ο', 'π', 'ρ', 'σ', 'ς', 'τ', 'ύ', 'ϋ', 'ΰ', 'υ', 'φ', 'χ', 'ψ', 'ώ', 'ω',
	);
	break;
case 'he':
	$ucspecialchars = array(
		'א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'כ', 'ך', 'ל', 'מ',
		'ם', 'נ', 'ן', 'ס', 'ע', 'פ', 'ף', 'צ', 'ץ', 'ק', 'ר', 'ש', 'ת',
	);
	$lcspecialchars = array();
	break;
case 'ar':
	$ucspecialchars = array(
		'ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط',
		'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي', 'آ', 'ة', 'ى', 'ی',
	);
	$lcspecialchars = array();
	break;
case 'ru':
	$ucspecialchars = array(
		'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
		'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
	);
	$lcspecialchars = array(
		'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
		'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
	);
	break;
case 'vi':
	$ucspecialchars = array(
		'À', 'Á', 'Â', 'Ã', 'Ạ', 'Ả', 'Ă', 'Ấ', 'Ầ', 'Ẫ', 'Ậ', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ',
		'Đ', 'È', 'É', 'Ê', 'Ẹ', 'Ẻ', 'Ẽ', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ì', 'Í', 'Ĩ', 'Ỉ', 'Ị',
		'Ò', 'Ó', 'Ô', 'Õ', 'Ơ', 'Ọ', 'Ỏ', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',
		'Ù', 'Ú', 'Ũ', 'Ư', 'Ụ', 'Ủ', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Ý', 'Ỳ', 'Ỵ', 'Ỷ', 'Ỹ',
	);
	$lcspecialchars = array(
		'à', 'á', 'â', 'ã', 'ạ', 'ả', 'ă', 'ấ', 'ầ', 'ẫ', 'ậ', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ',
		'đ', 'è', 'é', 'ê', 'ẹ', 'ẻ', 'ẽ', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ì', 'í', 'ĩ', 'ỉ', 'ị',
		'ò', 'ó', 'ô', 'õ', 'ơ', 'ọ', 'ỏ', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
		'ù', 'ú', 'ũ', 'ư', 'ụ', 'ủ', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'ý', 'ỳ', 'ỵ', 'ỷ', 'ỹ',
	);
	break;

default:
	$ucspecialchars = array(
		'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ą', 'Ā', 'Æ', 'Ç', 'Č', 'Ć', 'Ð', 'Ð', 'Ď',
		'È', 'É', 'Ê', 'Ë', 'Ę', 'Ě', 'Ē', 'Ğ', 'Ì', 'Í', 'Î', 'Ï', 'İ', 'Ī', 'Ĳ',
		'Ĺ', 'Ľ', 'Ł', 'Ñ', 'Ň', 'Ń', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ő', 'Ō', 'Œ', 'Ø',
		'Ŕ', 'Ř', 'Š', 'Ś', 'Ş', 'Ť', 'Ù', 'Ú', 'Û', 'Ü', 'Ů', 'Ű', 'Ū', 'Ý', 'Þ',
		'Ÿ', 'Ž', 'Ź', 'Ż', '‘',
	);
	$lcspecialchars = array(
		'à', 'á', 'â', 'ã', 'ä', 'å', 'ą', 'ā', 'æ', 'ç', 'č', 'ć', 'ď', 'đ', 'ð',
		'è', 'é', 'ê', 'ë', 'ę', 'ě', 'ē', 'ğ', 'ì', 'í', 'î', 'ï', 'ı', 'ī', 'ĳ',
		'ĺ', 'ľ', 'ł', 'ñ', 'ŉ', 'ň', 'ń', 'ò', 'ó', 'ô', 'õ', 'ö', 'ő', 'ō', 'œ',
		'ø', 'ŕ', 'ř', 'š', 'ś', 'ş', 'ß', 'ť', 'ù', 'ú', 'û', 'ü', 'ů', 'ű', 'ū',
		'ý', 'þ', 'ÿ', 'ž', 'ź', 'ż', '‘',
	);
}
$otherspecialchars = array(
	'¡', '¿', '„', '“', '”', '‚', '‛', '‘', '’', '«', '»', '‹', '›',
	'–', 'ª', 'º', '€', '¢', '£', '¥', '©', '°', '†', '‡', '§', '¶',
);
