<?php
/**
 * RTL Functions
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_FUNCTIONS_RTL_PHP', '');

$SpecialChar = array(' ','.',',','"','\'','/','\\','|',':',';','+','&','#','@','-','=','*','%','!','?','$','<','>',"\n");
$SpecialPar = array('(',')','[',']','{','}');
$SpecialNum  = array('0','1','2','3','4','5','6','7','8','9');
$RTLOrd = array(215,216,217,218,219);

$openPar  = '([{';
$closePar = ')]}';
$numbers = '0123456789';
$numberPrefix = '+-';				// Treat these like numbers when at beginning or end of numeric strings
$numberPunctuation = '- ,.:/';		// Treat these like numbers when inside numeric strings
$punctuation = ',.:;?!';

function getLRM(){
	return "&lrm;";
}

function getRLM(){
	return "&rlm;";
}

/**
 * This function strips &lrm; and &rlm; from the input string.  It should be used for all
 * text that has been passed through the PrintReady() function before that text is stored
 * in the database.  The database should NEVER contain these characters.
 *
 * @param 	string	The string from which the &lrm; and &rlm; characters should be stripped
 * @return	string	The input string, with &lrm; and &rlm; stripped
 */
function stripLRMRLM($inputText) {
	return str_replace(array(WT_UTF8_LRM, WT_UTF8_RLM, WT_UTF8_LRO, WT_UTF8_RLO, WT_UTF8_LRE, WT_UTF8_RLE, WT_UTF8_PDF, "&lrm;", "&rlm;", "&LRM;", "&RLM;"), "", $inputText);
}

/**
 * This function encapsulates all texts in the input with <span dir='xxx'> and </span>
 * according to the directionality specified.
 *
 * @param 	string	Raw input
 * @param	string	Directionality (LTR, BOTH, RTL) default BOTH
 * @param	string	Additional text to insert into output <span dir="xxx"> (such as 'class="yyy"')
 * @return	string	The string with all texts encapsulated as required
 */
function spanLTRRTL($inputText, $direction='BOTH', $class='') {
	global $TEXT_DIRECTION;
	global $openPar, $closePar, $punctuation;
	global $numbers, $numberPrefix, $numberPunctuation;
	global $previousState, $currentState, $posSpanStart, $waitingText;
	global $startLTR, $endLTR, $startRTL, $endRTL, $lenStart, $lenEnd;
	static $spanNumber = 0;

	if ($inputText == '') return '';		// Nothing to do

	$debug = false;		// false for normal operation (no calls of the DumpString function)

	$spanNumber ++;
	if ($debug) {echo '<br /><b>Input ', $spanNumber, ':</b>'; DumpString($inputText);}

	$workingText = str_replace("\n", '<br />', $inputText);
	$workingText = str_replace(array('<span class="starredname"><br />', '<span<br />class="starredname">'), '<br /><span class="starredname">',$workingText);		// Reposition some incorrectly placed line breaks
	$workingText = stripLRMRLM($workingText);		// Get rid of any existing UTF8 control codes

	$nothing 	= '&zwnj;';		// Zero Width Non-Joiner  (not sure whether this is still needed to work around a TCPDF bug)

	$startLTR	= '<LTR>';		// This will become '<span dir="ltr">' at the end
	$endLTR		= '</LTR>';		// This will become '</span>' at the end
	$startRTL	= '<RTL>';		// This will become '<span dir="rtl">' at the end
	$endRTL		= '</RTL>';		// This will become '</span>' at the end
	$lenStart	= strlen($startLTR);	// RTL version MUST have same length		
	$lenEnd		= strlen($endLTR);		// RTL version MUST have same length		

	$previousState = '';
	$currentState = strtoupper($TEXT_DIRECTION);
	$numberState = false;		// Set when we're inside a numeric string
	$result = '';
	$waitingText = '';
	$openParDirection = array();

	beginCurrentSpan($result);

	while ($workingText != '') {
		$charArray = getChar($workingText, 0);		// Get the next ASCII or UTF-8 character
		$currentLetter = $charArray['letter'];
		$currentLen = $charArray['length'];

		$openParIndex = strpos($openPar, $currentLetter);		// Which opening parenthesis is this?
		$closeParIndex = strpos($closePar, $currentLetter);		// Which closing parenthesis is this?

		switch ($currentLetter) {
		case '<':
			// Assume this '<' starts an HTML element
			$endPos = strpos($workingText, '>');	// look for the terminating '>'
			if ($endPos === false) $endPos = 0;
			$currentLen += $endPos;
			$element = substr($workingText, 0, $currentLen);
			$temp = strtolower(substr($element, 0, 3));
			if (strlen($element < 7) && $temp == '<br') {		// assume we have '<br />' or a variant thereof
				if ($numberState) {
					$numberState = false;
					$waitingText .= WT_UTF8_PDF;
				}
				breakCurrentSpan($result);
			} else if ($waitingText == '') {
				$result .= $element;
			} else {
				$waitingText .= $element;
			}
			$workingText = substr($workingText, $currentLen);
			break;
		case '&':
			// Assume this '&' starts an HTML entity
			$endPos = strpos($workingText, ';');	// look for the terminating ';'
			if ($endPos === false) $endPos = 0;
			$currentLen += $endPos;
			$entity = substr($workingText, 0, $currentLen);
			if (substr($entity, 0, 2 == '&#')) {
				// look for possible New Line codes
				if ((substr($entity, 2, 1) == 'x') || (substr($entity, 2, 1) == 'X')) {
					// the entity is a hexadecimal number
					$ordinal = hexdec(substr($entity, 3, -1));
				} else {
					// the entity is a decimal number
					$ordinal = intval(substr($entity, 2, -1));
				}
				if ($ordinal == 10) {
					// we have a New-Line code
					if ($numberState) {
						$numberState = false;
						$waitingText .= WT_UTF8_PDF;
					}
					breakCurrentSpan($result);
					$workingText = substr($workingText, $currentLen);
				}
			} else {
				if (strtolower($entity) == '&nbsp;') {
					$entity .= '&nbsp;';		// Ensure consistent case for this entity
				}
				if ($waitingText == '') {
					$result .= $entity;
				} else {
					$waitingText .= $entity;
				}
				$workingText = substr($workingText, $currentLen);
			}
			break;
		case '{':
			if (substr($workingText, 1, 1) == '{') {
				// Assume this '{{' starts a TCPDF directive
				$endPos = strpos($workingText, '}}');	// look for the terminating '}}'
				if ($endPos === false) $endPos = 0;
				$currentLen = $endPos + 2;
				$directive = substr($workingText, 0, $currentLen);
				$workingText = substr($workingText, $currentLen);
				$result = $result . $waitingText . $directive;
				$waitingText = '';
				break;
			}
		default:
			// Look for strings of numbers with optional leading or trailing + or -
			// and with optional embedded numeric punctuation
			if ($numberState) {
				// If we're inside a numeric string, look for reasons to end it
				$offset = 0;		// Be sure to look at the current character first
				$charArray = getChar($workingText."\n", $offset);
				if (strpos($numbers, $charArray['letter']) === false) {
					// This is not a digit.  Is it numeric punctuation?
					if (substr($workingText."\n", $offset, 6) == '&nbsp;') {
						$offset += 6;		// This could be numeric punctuation
					} else if (strpos($numberPunctuation, $charArray['letter']) !== false) {
						$offset += $charArray['length'];	// This could be numeric punctuation
					}
					// If the next character is a digit, the current character is numeric punctuation
					$charArray = getChar($workingText."\n", $offset);
					if (strpos($numbers, $charArray['letter']) === false) {
						// This is not a digit.  End the run of digits and punctuation.
						$numberState = false;
						if (strpos($numberPrefix, $currentLetter) === false) {
							$currentLetter = WT_UTF8_PDF . $currentLetter;
						} else {
							$currentLetter = $currentLetter . WT_UTF8_PDF;		// Include a trailing + or - in the run
						}
					}
				}
			} else {
				// If we're outside a numeric string, look for reasons to start it
				if (strpos($numberPrefix, $currentLetter) !== false) {
					// This might be a number lead-in
					$offset = $currentLen;
					$nextChar = substr($workingText."\n", $offset, 1);
					if (strpos($numbers, $nextChar) !== false) {
						$numberState = true;		// We found a digit: the lead-in is therefore numeric
						$currentLetter = WT_UTF8_LRE . $currentLetter;
					}
				} else if (strpos($numbers, $currentLetter) !== false) {
					$numberState = true;		// The current letter is a digit
					$currentLetter = WT_UTF8_LRE . $currentLetter;
				}
			}

			// Determine the directionality of the current UTF-8 character
			$newState = $currentState;
			while (true) {
				if (utf8_direction($currentLetter)=='rtl') {
					if ($currentState == '') {
						$newState = 'RTL';
						break;
					}

					if ($currentState == 'RTL') break;
					// Switch to RTL only if this isn't a solitary RTL letter
					$tempText = substr($workingText, $currentLen);
					while ($tempText != '') {
						$nextCharArray = getChar($tempText, 0);
						$nextLetter = $nextCharArray['letter'];
						$nextLen = $nextCharArray['length'];
						$tempText = substr($tempText, $nextLen);

						if (utf8_direction($nextLetter)=='rtl') {
							$newState = 'RTL';
							break 2;
						}

						if (strpos($punctuation, $nextLetter) !== false || strpos($openPar, $nextLetter) !== false) {
							$newState = 'RTL';
							break 2;
						}

						if ($nextLetter == ' ') break;
						$nextLetter .= substr($tempText."\n", 0, 5);
						if ($nextLetter == '&nbsp;') {
							$tempText = substr($tempText, 5);
							break;
						}
					}
					// This is a solitary RTL letter : wrap it in UTF8 control codes to force LTR directionality
					$currentLetter = WT_UTF8_LRO . $currentLetter . WT_UTF8_PDF;
					$newState = 'LTR';
					break;
				}
				if (($currentLen != 1) || ($currentLetter >= 'A' && $currentLetter <= 'Z') || ($currentLetter >= 'a' && $currentLetter <= 'z')) {
					// Since it's neither Hebrew nor Arabic, this UTF-8 character or ASCII letter must be LTR
					$newState = 'LTR';
					break;
				}
				if ($closeParIndex !== false) {
					// This closing parenthesis has to inherit the matching opening parenthesis' directionality
					if (!empty($openParDirection[$closeParIndex]) && $openParDirection[$closeParIndex] != '?') {
						$newState = $openParDirection[$closeParIndex];
					}
					$openParDirection[$closeParIndex] = '';
					break;
				}
				if ($openParIndex !== false) {
					// Opening parentheses always inherit the following directionality
					$waitingText .= $currentLetter;
					$workingText = substr($workingText, $currentLen);
					while (true) {
						if ($workingText == '') break;
						if (substr($workingText, 0, 1) == ' ') {
							// Spaces following this left parenthesis inherit the following directionality too
							$waitingText .= ' ';
							$workingText = substr($workingText, 1);
							continue;
						}
						if (substr($workingText, 0, 6) == '&nbsp;') {
							// Spaces following this left parenthesis inherit the following directionality too
							$waitingText .= '&nbsp;';
							$workingText = substr($workingText, 6);
							continue;
						}
						break;
					}
					$openParDirection[$openParIndex] = '?';
					break 2;	// double break because we're waiting for more information
				}

				// We have a digit or a "normal" special character.
				//
				// When this character is not at the start of the input string, it inherits the preceding directionality;
				// at the start of the input string, it assumes the following directionality.
				//
				// Exceptions to this rule will be handled later during final clean-up.
				//
				$waitingText .= $currentLetter;
				$workingText = substr($workingText, $currentLen);
				if ($currentState != '') {
					$result .= $waitingText;
					$waitingText = '';
				}
				break 2;	// double break because we're waiting for more information
			}
			if ($newState != $currentState) {
				// A direction change has occurred
				finishCurrentSpan($result, false);
				$previousState = $currentState;
				$currentState = $newState;
				beginCurrentSpan($result);
			}
			$waitingText .= $currentLetter;
			$workingText = substr($workingText, $currentLen);
			$result .= $waitingText;
			$waitingText = '';

			foreach($openParDirection as $index => $value) {
				// Since we now know the proper direction, remember it for all waiting opening parentheses
				if ($value == '?') {
					$openParDirection[$index] = $currentState;
				}
			}

			break;
		}
	}

	// We're done.  Finish last <span> if necessary
	if ($numberState) {
		$numberState = false;
		if ($waitingText == '') {
			$result .= WT_UTF8_PDF;
		} else {
			$waitingText .= WT_UTF8_PDF;
		}
	}
	finishCurrentSpan($result, true);

	// Get rid of any waiting text
	if ($waitingText != '') {
		if ($TEXT_DIRECTION == 'rtl' && $currentState == 'LTR') {
			$result .= $startRTL;
			$result .= $waitingText;
			$result .= $endRTL;
		} else {
			$result .= $startLTR;
			$result .= $waitingText;
			$result .= $endLTR;
		}
		$waitingText = '';
	}

	// Lastly, do some more cleanups
	if ($debug) {echo '<b>Interim Output:</b>'; DumpString($result);}
	
	// Move leading RTL numeric strings to following LTR text
	// (this happens when the page direction is RTL and the original text begins with a number and is followed by LTR text)
	while (substr($result, 0, $lenStart+3) == $startRTL.WT_UTF8_LRE) {
		$spanEnd = strpos($result, $endRTL.$startLTR);
		if ($spanEnd === false) {
			break;
		}
		$textSpan = stripLRMRLM(substr($result, $lenStart+3, $spanEnd-$lenStart-3));
		$langSpan = utf8_script($textSpan);
		if ($langSpan == 'hebrew' || $langSpan == 'arabic') {
			break;
		}
		$result = $startLTR . substr($result, $lenStart, $spanEnd-$lenStart) . substr($result, $spanEnd+$lenStart+$lenEnd);
		break;
	}
	
	// On RTL pages, put trailing "." in RTL numeric strings into its own RTL span
	if ($TEXT_DIRECTION == 'rtl') {
		$result = str_replace(WT_UTF8_PDF.'.'.$endRTL, WT_UTF8_PDF.$endRTL.$startRTL.'.'.$endRTL, $result);
	}
	
	// Trim trailing blanks preceding <br /> in LTR text
	while ($previousState != 'RTL') {
		if (strpos($result, ' <LTRbr />') !== false) {
			$result = str_replace(' <LTRbr />', '<LTRbr />', $result);
			continue;
		}
		if (strpos($result, '&nbsp;<LTRbr />') !== false) {
			$result = str_replace('&nbsp;<LTRbr />', '<LTRbr />', $result);
			continue;
		}
		if (strpos($result, ' <br />') !== false) {
			$result = str_replace(' <br />', '<br />', $result);
			continue;
		}
		if (strpos($result, '&nbsp;<br />') !== false) {
			$result = str_replace('&nbsp;<br />', '<br />', $result);
			continue;
		}
		break;		// Neither space nor &nbsp; : we're done
	}

	// Trim trailing blanks preceding <br /> in RTL text
	while (true) {
		if (strpos($result, ' <RTLbr />') !== false) {
			$result = str_replace(' <RTLbr />', '<RTLbr />', $result);
			continue;
		}
		if (strpos($result, '&nbsp;<RTLbr />') !== false) {
			$result = str_replace('&nbsp;<RTLbr />', '<RTLbr />', $result);
			continue;
		}
		break;		// Neither space nor &nbsp; : we're done
	}

	// Convert '<LTRbr />' and '<RTLbr /'
	$result = str_replace(array('<LTRbr />', '<RTLbr />'), array($endLTR.'<br />'.$startLTR, $endRTL.'<br />'.$startRTL), $result);

	// Include leading indeterminate directional text in whatever follows
	if (substr($result."\n", 0, $lenStart) != $startLTR && substr($result."\n", 0, $lenStart) != $startRTL && substr($result."\n", 0, 6) != '<br />') {
		$leadingText = '';
		while (true) {
			if ($result == '') {
				$result = $leadingText;
				break;
			}
			if (substr($result."\n", 0, $lenStart) != $startLTR && substr($result."\n", 0, $lenStart) != $startRTL) {
				$leadingText .= substr($result, 0, 1);
				$result = substr($result, 1);
				continue;
			}
			$result = substr($result, 0, $lenStart) . $leadingText . substr($result, $lenStart);
			break;
		}
	}

	// Include solitary "-" and "+" in surrounding RTL text
	$result = str_replace(array($endRTL.$startLTR.'-'.$endLTR.$startRTL, $endRTL.$startLTR.'-'.$endLTR.$startRTL), array('-', '+'), $result);

	// Remove empty spans
	$result = str_replace(array($startLTR.$endLTR, $startRTL.$endRTL), '', $result);

	// Finally, correct '<LTR>', '</LTR>', '<RTL>', and '</RTL>'
	switch ($direction) {
	case 'BOTH':
	case 'both':
		// LTR text: <span dir="ltr"> text </span>
		// RTL text: <span dir="rtl"> text </span>
		$sLTR	= '<span dir="ltr" '.$class.'>'.$nothing;
		$eLTR	= $nothing.'</span>';
		$sRTL	= '<span dir="rtl" '.$class.'>'.$nothing;
		$eRTL	= $nothing.'</span>';
		break;
	case 'LTR':
	case 'ltr':
		// LTR text: <span dir="ltr"> text </span>
		// RTL text: text
		$sLTR	= '<span dir="ltr" '.$class.'>'.$nothing;
		$eLTR	= $nothing.'</span>';
		$sRTL	= '';
		$eRTL	= '';
		break;
	case 'RTL':
	case 'rtl':
	default:
		// LTR text: text
		// RTL text: <span dir="rtl"> text </span>
		$sLTR	= '';
		$eLTR	= '';
		$sRTL	= '<span dir="rtl" '.$class.'>'.$nothing;
		$eRTL	= $nothing.'</span>';
		break;
	}
	$result = str_replace(array($startLTR, $endLTR, $startRTL, $endRTL), array($sLTR, $eLTR, $sRTL, $eRTL), $result);
	if ($debug) {echo '<b>Final Output:</b>'; DumpString($result);}
	return $result;
}

/*
 * Wrap words that have an asterisk suffix in <u> and </u> tags.  This should underline
 * starred names to show the preferred name
 */
function starredName($textSpan, $direction) {
	global $TEXT_DIRECTION;

	// To avoid a TCPDF bug that mixes up the word order, insert those <u> and </u> tags
	// only when page and span directions are identical.
	if ($direction == strtoupper($TEXT_DIRECTION)) {
		while (true) {
			$starPos = strpos($textSpan, '*');
			if ($starPos === false) break;
			$trailingText = substr($textSpan, $starPos+1);
			$textSpan = substr($textSpan, 0, $starPos);
			$wordStart = strrpos($textSpan, ' ');		// Find the start of the word
			if ($wordStart !== false) {
				$leadingText = substr($textSpan, 0, $wordStart+1);
				$wordText = substr($textSpan, $wordStart+1);
			} else {
				$leadingText = '';
				$wordText = $textSpan;
			}
			$textSpan = $leadingText . '<u>' . $wordText . '</u>' . $trailingText;
		}
		$textSpan = preg_replace('~<span class="starredname">(.*)</span>~', '<u>\1</u>', $textSpan);
		// The &nbsp; is a work-around for a TCPDF bug eating blanks.
		$textSpan = str_replace(array(' <u>', '</u> '), array('&nbsp;<u>', '</u>&nbsp;'), $textSpan);
	} else {
		// Text and page directions differ:  remove the <span> and </span>
		$textSpan = preg_replace('~(.*)\*~', '\1', $textSpan);
		$textSpan = preg_replace('~<span class="starredname">(.*)</span>~', '\1', $textSpan);
	}

	return $textSpan;
}

/**
 * Get the next character from an input string
 */
function getChar($text, $offset) {

	if ($text == '') return array('letter'=>'', 'length'=>0);

	$char = substr($text, $offset, 1);
	$length = 1;
	if ((ord($char) & 0xE0) == 0xC0) $length = 2;		// 2-byte sequence
	if ((ord($char) & 0xF0) == 0xE0) $length = 3;		// 3-byte sequence
	if ((ord($char) & 0xF8) == 0xF0) $length = 4;		// 4-byte sequence
	$letter = substr($text, $offset, $length);

	return array('letter'=>$letter, 'length'=>$length);
}

/**
 * Insert <br /> into current span
 */
function breakCurrentSpan(&$result) {
	global $currentState, $waitingText;

	// Interrupt the current span, insert that <br />, and then continue the current span
	$result .= $waitingText;
	$waitingText = '';

	$breakString = '<' . $currentState . 'br />';
	$result .= $breakString;

	return;
}

/**
 * Begin current span
 */
function beginCurrentSpan(&$result) {
	global $currentState, $startLTR, $startRTL, $posSpanStart;

	if ($currentState == 'LTR') $result .= $startLTR;
	if ($currentState == 'RTL') $result .= $startRTL;

	$posSpanStart = strlen($result);

	return;
}

/**
 * Finish current span
 */
function finishCurrentSpan(&$result, $theEnd=false) {
	global $previousState, $currentState, $posSpanStart, $TEXT_DIRECTION, $waitingText;
	global $startLTR, $endLTR, $startRTL, $endRTL, $lenStart, $lenEnd;
	global $numbers, $punctuation;

	$textSpan = substr($result, $posSpanStart);
	$result = substr($result, 0, $posSpanStart);

	// Get rid of empty spans, so that our check for presence of RTL will work
	$result = str_replace(array($startLTR.$endLTR, $startRTL.$endRTL), '', $result);

	// Look for numeric strings that are times (hh:mm:ss).  These have to be separated from surrounding numbers.
	$tempResult = '';
	while ($textSpan != '') {
		$posColon = strpos($textSpan, ':');
		if ($posColon === false) break;		// No more possible time strings
		$posLRE = strpos($textSpan, WT_UTF8_LRE);
		if ($posLRE === false) break;		// No more numeric strings
		$posPDF = strpos($textSpan, WT_UTF8_PDF, $posLRE);
		if ($posPDF === false) break;		// No more numeric strings

		$tempResult .= substr($textSpan, 0, $posLRE+3);					// Copy everything preceding the numeric string
		$numericString = substr($textSpan, $posLRE+3, $posPDF-$posLRE);	// Separate the entire numeric string
		$textSpan = substr($textSpan, $posPDF+3);
		$posColon = strpos($numericString, ':');
		if ($posColon === false) {
			// Nothing that looks like a time here
			$tempResult .= $numericString;
			continue;
		}
		$posBlank = strpos($numericString.' ', ' ');
		$posNbsp = strpos($numericString.'&nbsp;', '&nbsp;');
		if ($posBlank < $posNbsp) {
			$posSeparator = $posBlank;
			$lengthSeparator = 1;
		} else {
			$posSeparator = $posNbsp;
			$lengthSeparator = 6;
		}
		if ($posColon > $posSeparator) {
			// We have a time string preceded by a blank: Exclude that blank from the numeric string
			$tempResult .= substr($numericString, 0, $posSeparator);
			$tempResult .= WT_UTF8_PDF;
			$tempResult .= substr($numericString, $posSeparator, $lengthSeparator);
			$tempResult .= WT_UTF8_LRE;
			$numericString = substr($numericString, $posSeparator+$lengthSeparator);
		}

		$posBlank = strpos($numericString, ' ');
		$posNbsp = strpos($numericString, '&nbsp;');
		if ($posBlank === false && $posNbsp === false) {
			// The time string isn't followed by a blank
			$textSpan = $numericString . $textSpan;
			continue;
		}

		// We have a time string followed by a blank: Exclude that blank from the numeric string
		if ($posBlank === false) {
			$posSeparator = $posNbsp;
			$lengthSeparator = 6;
		} else if ($posNbsp === false) {
			$posSeparator = $posBlank;
			$lengthSeparator = 1;
		} else if ($posBlank < $posNbsp) {
			$posSeparator = $posBlank;
			$lengthSeparator = 1;
		} else {
			$posSeparator = $posNbsp;
			$lengthSeparator = 6;
		}
		$tempResult .= substr($numericString, 0, $posSeparator);
		$tempResult .= WT_UTF8_PDF;
		$tempResult .= substr($numericString, $posSeparator, $lengthSeparator);
		$posSeparator += $lengthSeparator;
		$numericString = substr($numericString, $posSeparator);
		$textSpan = WT_UTF8_LRE . $numericString . $textSpan;
	}
	$textSpan = $tempResult . $textSpan;
	$trailingBlanks = '';
	$trailingBreaks = '';

/* ****************************** LTR text handling ******************************** */

	if ($currentState == 'LTR') {
		// Move trailing numeric strings to the following RTL text.  Include any blanks preceding or following the numeric text too.
		if ($TEXT_DIRECTION == 'rtl' && $previousState == 'RTL' && !$theEnd) {
			$trailingString = '';
			$savedSpan = $textSpan;
			while ($textSpan != '') {
				// Look for trailing spaces and tentatively move them
				if (substr($textSpan, -1) == ' ') {
					$trailingString = ' ' . $trailingString;
					$textSpan = substr($textSpan, 0, -1);
					continue;
				}
				if (substr($textSpan, -6) == '&nbsp;') {
					$trailingString = '&nbsp;' . $trailingString;
					$textSpan = substr($textSpan, 0, -1);
					continue;
				}
				if (substr($textSpan, -3) != WT_UTF8_PDF) {
					// There is no trailing numeric string
					$textSpan = $savedSpan;
					break;
				}

				// We have a numeric string
				$posStartNumber = strrpos($textSpan, WT_UTF8_LRE);
				if ($posStartNumber === false) $posStartNumber = 0;
				$trailingString = substr($textSpan, $posStartNumber, strlen($textSpan)-$posStartNumber) . $trailingString;
				$textSpan = substr($textSpan, 0, $posStartNumber);

				// Look for more spaces and move them too
				while ($textSpan != '') {
					if (substr($textSpan, -1) == ' ') {
						$trailingString = ' ' . $trailingString;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -6) == '&nbsp;') {
						$trailingString = '&nbsp;' . $trailingString;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					break;
				}

				$waitingText = $trailingString . $waitingText;
				break;
			}
		}

		$savedSpan = $textSpan;
		// Move any trailing <br />, optionally preceded or followed by blanks, outside this LTR span
		while ($textSpan != '') {
			if (substr($textSpan, -1) == ' ') {
				$trailingBlanks = ' ' . $trailingBlanks;
				$textSpan = substr($textSpan, 0, -1);
				continue;
			}
			if (substr('......'.$textSpan, -6) == '&nbsp;') {
				$trailingBlanks = '&nbsp;' . $trailingBlanks;
				$textSpan = substr($textSpan, 0, -6);
				continue;
			}
			break;
		}
		while (substr($textSpan, -9) == '<LTRbr />') {
			$trailingBreaks = '<br />' . $trailingBreaks;		// Plain <br /> because it's outside a span
			$textSpan = substr($textSpan, 0, -9);
		}
		if ($trailingBreaks != '') {
			while ($textSpan != '') {
				if (substr($textSpan, -1) == ' ') {
					$trailingBreaks = ' ' . $trailingBreaks;
					$textSpan = substr($textSpan, 0, -1);
					continue;
				}
				if (substr('......'.$textSpan, -6) == '&nbsp;') {
					$trailingBreaks = '&nbsp;' . $trailingBreaks;
					$textSpan = substr($textSpan, 0, -6);
					continue;
				}
				break;
			}
			$waitingText = $trailingBlanks . $waitingText;		// Put those trailing blanks inside the following span
		} else {
			$textSpan = $savedSpan;
		}

		$savedSpan = $textSpan;
		$trailingBlanks = '';
		$trailingPunctuation = '';
		$trailingID = '';
		$trailingSeparator = '';
		$leadingSeparator = '';
		while ($TEXT_DIRECTION == 'rtl') {
			if (strpos($result, $startRTL) !== false) {
				// Remove trailing blanks for inclusion in a separate LTR span
				while ($textSpan != '') {
					if (substr($textSpan, -1) == ' ') {
						$trailingBlanks = ' ' . $trailingBlanks;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -6) == '&nbsp;') {
						$trailingBlanks = '&nbsp;' . $trailingBlanks;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					break;
				}

				// Remove trailing punctuation for inclusion in a separate LTR span
				if ($textSpan == '') $trailingChar = "\n";
				else $trailingChar = substr($textSpan, -1);
				if (strpos($punctuation, $trailingChar) !== false) {
					$trailingPunctuation = $trailingChar;
					$textSpan = substr($textSpan, 0, -1);
				}
			}

			// Remove trailing ID numbers that look like "(xnnn)" for inclusion in a separate LTR span
			while (true) {
				if (substr($textSpan, -1) != ')') break;		// There is no trailing ')'
				$posLeftParen = strrpos($textSpan, '(');
				if ($posLeftParen === false) break;				// There is no leading '('
				$temp = stripLRMRLM(substr($textSpan, $posLeftParen));		// Get rid of UTF8 control codes

				// If the parenthesized text doesn't look like an ID number,
				// we don't want to touch it.
				// This check won't work if somebody uses ID numbers with an unusual format.
				$offset = 1;
				$charArray = getchar($temp, $offset);	// Get 1st character of parenthesized text
				if (strpos($numbers, $charArray['letter']) !== false) break;
				$offset += $charArray['length'];		// Point at 2nd character of parenthesized text
				if (strpos($numbers, substr($temp, $offset, 1)) === false) break;
				// 1st character of parenthesized text is alpha, 2nd character is a digit; last has to be a digit too
				if (strpos($numbers, substr($temp, -2, 1)) === false) break;

				$trailingID = substr($textSpan, $posLeftParen);
				$textSpan = substr($textSpan, 0, $posLeftParen);
				break;
			}

			// Look for " - " or blank preceding the ID number and remove it for inclusion in a separate LTR span
			$savedSpan = $textSpan;
			if ($trailingID != '') {
				while ($textSpan != '') {
					if (substr($textSpan, -1) == ' ') {
						$trailingSeparator = ' ' . $trailingSeparator;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -6) == '&nbsp;') {
						$trailingSeparator = '&nbsp;' . $trailingSeparator;
						$textSpan = substr($textSpan, 0, -6);
						continue;
					}
					if (substr($textSpan, -1) == '-') {
						$trailingSeparator = '-' . $trailingSeparator;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					break;
				}
			}

			// Look for " - " preceding the text and remove it for inclusion in a separate LTR span
			$foundSeparator = false;
			$savedSpan = $textSpan;
			while ($textSpan != '') {
				if (substr($textSpan, 0, 1) == ' ') {
					$leadingSeparator = ' ' . $leadingSeparator;
					$textSpan = substr($textSpan, 1);
					continue;
				}
				if (substr($textSpan, 0, 6) == '&nbsp;') {
					$leadingSeparator = '&nbsp;' . $leadingSeparator;
					$textSpan = substr($textSpan, 6);
					continue;
				}
				if (substr($textSpan, 0, 1) == '-') {
					$leadingSeparator = '-' . $leadingSeparator;
					$textSpan = substr($textSpan, 1);
					$foundSeparator = true;
					continue;
				}
				break;
			}
			if (!$foundSeparator) {
				$textSpan = $savedSpan;
				$leadingSeparator = '';
			}
			break;
		}

		// We're done: finish the span
		$textSpan = starredName($textSpan, 'LTR');		// Wrap starred name in <u> and </u> tags
		while (true) {
			// Remove blanks that precede <LTRbr />
			if (strpos($textSpan, ' <LTRbr />') !== false) {
				$textSpan = str_replace(' <LTRbr />', '<LTRbr />', $textSpan);
				continue;
			}
			if (strpos($textSpan, '&nbsp;<LTRbr />') !== false) {
				$textSpan = str_replace('&nbsp;<LTRbr />', '<LTRbr />', $textSpan);
				continue;
			}
			break;
		}
		if ($leadingSeparator != '') $result = $result . $startLTR . $leadingSeparator . $endLTR;
		$result = $result . $textSpan . $endLTR;
		if ($trailingSeparator != '') $result = $result . $startLTR . $trailingSeparator . $endLTR;
		if ($trailingID != '') $result = $result . $startLTR . $trailingID . $endLTR;
		if ($trailingPunctuation != '') $result = $result . $startLTR . $trailingPunctuation . $endLTR;
		if ($trailingBlanks != '') $result = $result . $startLTR . $trailingBlanks . $endLTR;
	}

/* ****************************** RTL text handling ******************************** */

	if ($currentState == 'RTL') {
		$savedSpan = $textSpan;

		// Move any trailing <br />, optionally followed by blanks, outside this RTL span
		while ($textSpan != '') {
			if (substr($textSpan, -1) == ' ') {
				$trailingBlanks = ' ' . $trailingBlanks;
				$textSpan = substr($textSpan, 0, -1);
				continue;
			}
			if (substr('......'.$textSpan, -6) == '&nbsp;') {
				$trailingBlanks = '&nbsp;' . $trailingBlanks;
				$textSpan = substr($textSpan, 0, -6);
				continue;
			}
			break;
		}
		while (substr($textSpan, -9) == '<RTLbr />') {
			$trailingBreaks = '<br />' . $trailingBreaks;		// Plain <br /> because it's outside a span
			$textSpan = substr($textSpan, 0, -9);
		}
		if ($trailingBreaks != '') {
			$waitingText = $trailingBlanks . $waitingText;		// Put those trailing blanks inside the following span
		} else {
			$textSpan = $savedSpan;
		}

		// Move trailing numeric strings to the following LTR text.  Include any blanks preceding or following the numeric text too.
		if (!$theEnd && $TEXT_DIRECTION != 'rtl') {
			$trailingString = '';
			$savedSpan = $textSpan;
			while ($textSpan != '') {
				// Look for trailing spaces and tentatively move them
				if (substr($textSpan, -1) == ' ') {
					$trailingString = ' ' . $trailingString;
					$textSpan = substr($textSpan, 0, -1);
					continue;
				}
				if (substr($textSpan, -6) == '&nbsp;') {
					$trailingString = '&nbsp;' . $trailingString;
					$textSpan = substr($textSpan, 0, -1);
					continue;
				}
				if (substr($textSpan, -3) != WT_UTF8_PDF) {
					// There is no trailing numeric string
					$textSpan = $savedSpan;
					break;
				}

				// We have a numeric string
				$posStartNumber = strrpos($textSpan, WT_UTF8_LRE);
				if ($posStartNumber === false) $posStartNumber = 0;
				$trailingString = substr($textSpan, $posStartNumber, strlen($textSpan)-$posStartNumber) . $trailingString;
				$textSpan = substr($textSpan, 0, $posStartNumber);

				// Look for more spaces and move them too
				while ($textSpan != '') {
					if (substr($textSpan, -1) == ' ') {
						$trailingString = ' ' . $trailingString;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -6) == '&nbsp;') {
						$trailingString = '&nbsp;' . $trailingString;
						$textSpan = substr($textSpan, 0, -1);
						continue;
					}
					break;
				}

				$waitingText = $trailingString . $waitingText;
				break;
			}
		}

		// Trailing " - " needs to be prefixed to the following span
		if (!$theEnd && substr('...'.$textSpan, -3) == ' - ') {
			$textSpan = substr($textSpan, 0, -3);
			$waitingText = ' - ' . $waitingText;
		}

		while ($TEXT_DIRECTION == 'rtl') {
			// Look for " - " preceding <RTLbr /> and relocate it to the front of the string
			$posDashString = strpos($textSpan, ' - <RTLbr />');
			if ($posDashString === false) break;
			$posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr />');
			if ($posStringStart === false) $posStringStart = 0;
			else $posStringStart += 9;		// Point to the first char following the last <RTLbr />

			$textSpan = substr($textSpan, 0, $posStringStart) . ' - ' . substr($textSpan, $posStringStart, $posDashString-$posStringStart) . substr($textSpan, $posDashString+3);
		}

		// Strip leading spaces from the RTL text
		$countLeadingSpaces = 0;
		while ($textSpan != '') {
			if (substr($textSpan, 0, 1) == ' ') {
				$countLeadingSpaces ++;
				$textSpan = substr($textSpan, 1);
				continue;
			}
			if (substr($textSpan, 0, 6) == '&nbsp;') {
				$countLeadingSpaces ++;
				$textSpan = substr($textSpan, 6);
				continue;
			}
			break;
		}

		// Strip trailing spaces from the RTL text
		$countTrailingSpaces = 0;
		while ($textSpan != '') {
			if (substr($textSpan, -1) == ' ') {
				$countTrailingSpaces ++;
				$textSpan = substr($textSpan, 0, -1);
				continue;
			}
			if (substr($textSpan, -6) == '&nbsp;') {
				$countTrailingSpaces ++;
				$textSpan = substr($textSpan, 0, -6);
				continue;
			}
			break;
		}

		// Look for trailing " -", reverse it, and relocate it to the front of the string
		if (substr($textSpan, -2) == ' -') {
			$posDashString = strlen($textSpan) - 2;
			$posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr />');
			if ($posStringStart === false) $posStringStart = 0;
			else $posStringStart += 9;		// Point to the first char following the last <RTLbr />

			$textSpan = substr($textSpan, 0, $posStringStart) . '- ' . substr($textSpan, $posStringStart, $posDashString-$posStringStart) . substr($textSpan, $posDashString+2);
		}

		if ($countLeadingSpaces != 0) {
			$newLength = strlen($textSpan) + $countLeadingSpaces;
			$textSpan = str_pad($textSpan, $newLength, ' ', ($TEXT_DIRECTION=='rtl' ? STR_PAD_LEFT:STR_PAD_RIGHT));
		}
		if ($countTrailingSpaces != 0) {
			if ($TEXT_DIRECTION == 'ltr') {
				if ($trailingBreaks == '') {
					// Move trailing RTL spaces to front of following LTR span
					$newLength = strlen($waitingText) + $countTrailingSpaces;
					$waitingText = str_pad($waitingText, $newLength, ' ', STR_PAD_LEFT);
				}
			} else {
				$newLength = strlen($textSpan) + $countTrailingSpaces;
				$textSpan = str_pad($textSpan, $newLength, ' ', STR_PAD_RIGHT);
			}
		}

		// We're done: finish the span
		$textSpan = starredName($textSpan, 'RTL');		// Wrap starred name in <u> and </u> tags
		$result = $result . $textSpan . $endRTL;
	}

	if ($currentState != 'LTR' && $currentState != 'RTL') {
		$result = $result . $textSpan;
	}

	$result .= $trailingBreaks;		// Get rid of any waiting <br />

	return;
}

/**
 * convert HTML entities to to their original characters
 *
 * original found at http://www.php.net/manual/en/function.get-html-translation-table.php
 * @see http://www.php.net/manual/en/function.get-html-translation-table.php
 * @param string $string	the string to remove the entities from
 * @return string	the string with entities converted
 */
function unhtmlentities($string)  {
	$trans_tbl=array_flip(get_html_translation_table (HTML_ENTITIES));
	$trans_tbl['&lrm;']=WT_UTF8_LRM;
	$trans_tbl['&rlm;']=WT_UTF8_RLM;
	return preg_replace('/&#(\d+);/e', "chr(\\1)", strtr($string, $trans_tbl));
}

/**
 * process a string according to bidirectional rules
 *
 * this function will take a text string and reverse it for RTL languages
 * according to bidi rules.
 * @param string $text	String to change
 * @return string	the new bidi string
 * @todo add other RTL langauges
 */
function bidi_text($text) {
	global $RTLOrd;

	// דו"ח אישי
	//קראטוןםפ שדגכעיחלךף זסבה� מצתץ עברי איתה מאיה (אתקה) שם משפחה ‎
	//מספר מזהה (SSN)

	$found = false;
	foreach($RTLOrd as $indexval => $ord) {
    	if (strpos($text, chr($ord))!==false) $found=true;
	}
	if (!$found) return $text;

	$special_chars = array(' ','"','\'','(',')','[',']',':',"\n");
	$newtext = "";
	$parts = array();
	$temp = "";
	$state = 0;
	$p = 0;
	for($i=0; $i<strlen($text); $i++) {
		$letter = $text{$i};
		//print $letter.ord($letter).",";
		//-- handle Hebrew chars
		if (in_array(ord($letter),$RTLOrd)) {
			if (!empty($temp)) {
				//-- just in case the $temp is a Hebrew char push it onto the stack
				if (in_array(ord($temp{0}),$RTLOrd));
				//-- if the $temp starts with a char in the special_chars array then remove the space and push it onto the stack seperately
				else if (in_array($temp{strlen($temp)-1}, $special_chars)) {
					$char = substr($temp, strlen($temp)-1);
					$temp = substr($temp, 0, strlen($temp)-1);
					if ($char=="[") $char = "]";
					else if ($char=="(") $char = ")";
					array_push($parts, $temp);
					array_push($parts, $char);
				}
				//-- otherwise push it onto the begining of the stack
				else array_unshift($parts, $temp);
			}
			$temp = $letter . $text{$i+1};
			$i++;
			if ($i < strlen($text)-1) {
				$l = $text{$i+1};
				if (in_array($l, $special_chars)) {
					if ($l=="]") $l = "[";
					else if ($l==")") $l = "(";
					$temp = $l . $temp;
					$i++;
				}
			}
			array_push($parts, $temp);
			$temp = "";
		}
		else if (ord($letter)==226) {
			if ($i < strlen($text)-2) {
				$l = $letter.$text{$i+1}.$text{$i+2};
				$i += 2;
				if (($l==WT_UTF8_LRM)||($l==WT_UTF8_RLM)) {
					if (!empty($temp)) {
						$last = array_pop($parts);
						if ($temp{0}==")") $last = '(' . $last;
						else if ($temp{0}=="(") $last = ')' . $last;
						else if ($temp{0}=="]") $last = '[' . $last;
						else if ($temp{0}=="[") $last = ']' . $last;
						array_push($parts, $last);
						$temp = "";
					}
				}
			}
		}
		else $temp .= $letter;
	}
	if (!empty($temp)) {
		if (in_array(ord($temp{0}),$RTLOrd)) array_push($parts, $temp);
		else array_push($parts, $temp);
	}

	//-- loop through and check if parenthesis are correct... if parenthesis were broken by
	//-- rtl text then they need to be reversed
	for($i=0; $i<count($parts); $i++) {
		$bef = "";
		$aft = "";
		$wt = preg_match("/^(\s*).*(\s*)$/", $parts[$i], $match);
		if ($wt>0) {
			$bef = $match[1];
			$aft = $match[2];
		}
		$temp = trim($parts[$i]);
		if (!empty($temp)) {
			if ($temp{0}=="(" && $temp{strlen($temp)-1}!=")") $parts[$i] = $bef.substr($temp, 1).")".$aft;
			if ($temp{0}=="[" && $temp{strlen($temp)-1}!="]") $parts[$i] = $bef.substr($temp, 1)."]".$aft;
			if ($temp{0}!="(" && $temp{strlen($temp)-1}==")") $parts[$i] = $bef."(".substr($temp, 0, strlen($temp)-1).$aft;
			if ($temp{0}!="[" && $temp{strlen($temp)-1}=="]") $parts[$i] = $bef."[".substr($temp, 0, strlen($temp)-1).$aft;
		}
	}
	//print_r($parts);
	$parts = array_reverse($parts);
	$newtext = implode("", $parts);
	return $newtext;
}

/**
 * Verify if text is a RtL character
 *
 * This will verify if text is a RtL character
 * @param string $text to verify
 */
function oneRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI?
	return (strlen($text)==2 && in_array(ord($text),$RTLOrd));
}

/**
 * Verify if text starts by a RtL character
 *
 * This will verify if text starts by a RtL character
 * @param string $text to verify
 */
function begRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI?
	return (in_array(ord(substr(trim($text),0,2)),$RTLOrd) || in_array(ord(substr(trim($text),1,2)),$RTLOrd));
}

/**
 * Verify if text ends by a RtL character
 *
 * This will verify if text ends by a RtL character
 * @param string $text to verify
 */
function endRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI? -- I believe that not used
	return (in_array(ord(substr(trim($text),strlen(trim($text))-2,2)),$RTLOrd) || in_array(ord(substr(trim($text),strlen(trim($text))-3,2)),$RTLOrd));
}

/**
 * Verify if text is RtL
 *
 * This will verify if text has RtL characters
 * @param string $text to verify
 */
function hasRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI?
	// if (!(strpos($text, chr(215))=== false)) return true;  // OK?
	for ($i=0; $i<strlen($text); $i++) {
	  if (in_array(ord(substr(trim($text),$i,2)),$RTLOrd)) return true;
	}
	return false;

}

/**
 * Verify if text is LtR
 *
 * This will verify if text has LtR characters that are not special characters
 * @param string $text to verify
 */
function hasLTRText($text) {
	global $SpecialChar, $SpecialPar, $SpecialNum, $RTLOrd;
	//--- What if gedcom in ANSI?
	//--- Should have one fullspecial characters array in PGV -

	for ($i=0; $i<strlen($text); $i++) {
		if (in_array(ord(substr(trim($text),$i,2)),$RTLOrd) || in_array(ord(substr(trim($text),$i-1,2)),$RTLOrd)) $i++;
	  	else {
		  	if (substr($text,$i,26)=='<span class="starredname">') $i+=25;
		  	else if (substr($text,$i,7)=="</span>") $i+=6;
		  	else {
				$byte = substr(trim($text),$i,1);
		    	if (!in_array($byte,$SpecialChar) && !in_array($byte,$SpecialPar) && !in_array($byte,$SpecialNum)) return true;
	    	}
	    }
	}
	return false;
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
	$UTF8_numbers=WT_UTF8_DIGITS;
	$UTF8_brackets=WT_UTF8_PARENTHESES;

	$text = strip_tags(html_entity_decode($text,ENT_COMPAT,'UTF-8'));
	$text = str_replace(array('&lrm;', '&rlm;', WT_UTF8_LRM, WT_UTF8_RLM), '', $text);
	$textLanguage = utf8_script($text);
	if ($textLanguage!='hebrew' && $textLanguage!='arabic') return $text;

	$reversedText = '';
	$numbers = '';
	while ($text!='') {
		$charLen = 1;
		$letter = substr($text, 0, 1);
		if ((ord($letter) & 0xE0) == 0xC0) $charLen = 2;		// 2-byte sequence
		if ((ord($letter) & 0xF0) == 0xE0) $charLen = 3;		// 3-byte sequence
		if ((ord($letter) & 0xF8) == 0xF0) $charLen = 4;		// 4-byte sequence

		$letter = substr($text, 0, $charLen);
		$text = substr($text, $charLen);
		if (in_array($letter, $UTF8_numbers)) $numbers .= $letter;		// accumulate numbers in LTR mode
		else {
			$reversedText = $numbers.$reversedText;		// emit any waiting LTR numbers now
			$numbers = '';
			if (isset($UTF8_brackets[$letter])) $reversedText = $UTF8_brackets[$letter].$reversedText;
			else $reversedText = $letter.$reversedText;
		}
	}

	$reversedText = $numbers.$reversedText;		// emit any waiting LTR numbers now
	return $reversedText;
}

?>
