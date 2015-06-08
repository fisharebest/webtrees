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
namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\I18N;

/**
 * RTL Functions for use in the PDF/HTML reports
 */
class FunctionsRtl {
	const OPEN_PARENTHESES = '([{';

	const CLOSE_PARENTHESES = ')]}';

	const NUMBERS = '0123456789';

	const NUMBER_PREFIX = '+-'; // Treat these like numbers when at beginning or end of numeric strings

	const NUMBER_PUNCTUATION = '- ,.:/'; // Treat these like numbers when inside numeric strings

	const PUNCTUATION = ',.:;?!';

	/** @var string Were we previously processing LTR or RTL. */
	private static $previousState;

	/** @var string Are we currently processing LTR or RTL. */
	private static $currentState;

	/** @var string Text waiting to be processed. */
	private static $waitingText;

	/** @var string LTR text. */
	private static $startLTR;

	/** @var string LTR text. */
	private static $endLTR;

	/** @var string RTL text. */
	private static $startRTL;

	/** @var string RTL text. */
	private static $endRTL;

	/** @var int Offset into the text. */
	private static $lenStart;

	/** @var int Offset into the text. */
	private static $lenEnd;

	/** @var int Offset into the text. */
	private static $posSpanStart;

	/**
	 * This function strips &lrm; and &rlm; from the input string.  It should be used for all
	 * text that has been passed through the PrintReady() function before that text is stored
	 * in the database.  The database should NEVER contain these characters.
	 *
	 * @param  string $inputText The string from which the &lrm; and &rlm; characters should be stripped
	 *
	 * @return string The input string, with &lrm; and &rlm; stripped
	 */
	public static function stripLrmRlm($inputText) {
		return str_replace(array(WT_UTF8_LRM, WT_UTF8_RLM, WT_UTF8_LRO, WT_UTF8_RLO, WT_UTF8_LRE, WT_UTF8_RLE, WT_UTF8_PDF, "&lrm;", "&rlm;", "&LRM;", "&RLM;"), "", $inputText);
	}

	/**
	 * This function encapsulates all texts in the input with <span dir='xxx'> and </span>
	 * according to the directionality specified.
	 *
	 * @param string $inputText Raw input
	 * @param string $direction Directionality (LTR, BOTH, RTL) default BOTH
	 * @param string $class Additional text to insert into output <span dir="xxx"> (such as 'class="yyy"')
	 *
	 * @return string The string with all texts encapsulated as required
	 */
	public static function spanLtrRtl($inputText, $direction = 'BOTH', $class = '') {
		if ($inputText == '') {
			// Nothing to do
			return '';
		}

		$workingText = str_replace("\n", '<br>', $inputText);
		$workingText = str_replace(array('<span class="starredname"><br>', '<span<br>class="starredname">'), '<br><span class="starredname">', $workingText); // Reposition some incorrectly placed line breaks
		$workingText = self::stripLrmRlm($workingText); // Get rid of any existing UTF8 control codes

		//	$nothing  = '&zwnj;'; // Zero Width Non-Joiner  (not sure whether this is still needed to work around a TCPDF bug)
		$nothing = '';

		self::$startLTR = '<LTR>'; // This will become '<span dir="ltr">' at the end
		self::$endLTR   = '</LTR>'; // This will become '</span>' at the end
		self::$startRTL = '<RTL>'; // This will become '<span dir="rtl">' at the end
		self::$endRTL   = '</RTL>'; // This will become '</span>' at the end
		self::$lenStart = strlen(self::$startLTR); // RTL version MUST have same length
		self::$lenEnd   = strlen(self::$endLTR); // RTL version MUST have same length

		self::$previousState = '';
		self::$currentState  = strtoupper(I18N::direction());
		$numberState         = false; // Set when we're inside a numeric string
		$result              = '';
		self::$waitingText   = '';
		$openParDirection    = array();

		self::beginCurrentSpan($result);

		while ($workingText != '') {
			$charArray     = self::getChar($workingText, 0); // Get the next ASCII or UTF-8 character
			$currentLetter = $charArray['letter'];
			$currentLen    = $charArray['length'];

			$openParIndex  = strpos(self::OPEN_PARENTHESES, $currentLetter); // Which opening parenthesis is this?
			$closeParIndex = strpos(self::CLOSE_PARENTHESES, $currentLetter); // Which closing parenthesis is this?

			switch ($currentLetter) {
				case '<':
					// Assume this '<' starts an HTML element
					$endPos = strpos($workingText, '>'); // look for the terminating '>'
					if ($endPos === false) {
						$endPos = 0;
					}
					$currentLen += $endPos;
					$element = substr($workingText, 0, $currentLen);
					$temp    = strtolower(substr($element, 0, 3));
					if (strlen($element) < 7 && $temp == '<br') {
						if ($numberState) {
							$numberState = false;
							if (self::$currentState == 'RTL') {
								self::$waitingText .= WT_UTF8_PDF;
							}
						}
						self::breakCurrentSpan($result);
					} elseif (self::$waitingText == '') {
						$result .= $element;
					} else {
						self::$waitingText .= $element;
					}
					$workingText = substr($workingText, $currentLen);
					break;
				case '&':
					// Assume this '&' starts an HTML entity
					$endPos = strpos($workingText, ';'); // look for the terminating ';'
					if ($endPos === false) {
						$endPos = 0;
					}
					$currentLen += $endPos;
					$entity = substr($workingText, 0, $currentLen);
					if (strtolower($entity) == '&nbsp;') {
						$entity .= '&nbsp;'; // Ensure consistent case for this entity
					}
					if (self::$waitingText == '') {
						$result .= $entity;
					} else {
						self::$waitingText .= $entity;
					}
					$workingText = substr($workingText, $currentLen);
					break;
				case '{':
					if (substr($workingText, 1, 1) == '{') {
						// Assume this '{{' starts a TCPDF directive
						$endPos = strpos($workingText, '}}'); // look for the terminating '}}'
						if ($endPos === false) {
							$endPos = 0;
						}
						$currentLen        = $endPos + 2;
						$directive         = substr($workingText, 0, $currentLen);
						$workingText       = substr($workingText, $currentLen);
						$result            = $result . self::$waitingText . $directive;
						self::$waitingText = '';
						break;
					}
				default:
					// Look for strings of numbers with optional leading or trailing + or -
					// and with optional embedded numeric punctuation
					if ($numberState) {
						// If we're inside a numeric string, look for reasons to end it
						$offset    = 0; // Be sure to look at the current character first
						$charArray = self::getChar($workingText . "\n", $offset);
						if (strpos(self::NUMBERS, $charArray['letter']) === false) {
							// This is not a digit.  Is it numeric punctuation?
							if (substr($workingText . "\n", $offset, 6) == '&nbsp;') {
								$offset += 6; // This could be numeric punctuation
							} elseif (strpos(self::NUMBER_PUNCTUATION, $charArray['letter']) !== false) {
								$offset += $charArray['length']; // This could be numeric punctuation
							}
							// If the next character is a digit, the current character is numeric punctuation
							$charArray = self::getChar($workingText . "\n", $offset);
							if (strpos(self::NUMBERS, $charArray['letter']) === false) {
								// This is not a digit.  End the run of digits and punctuation.
								$numberState = false;
								if (self::$currentState == 'RTL') {
									if (strpos(self::NUMBER_PREFIX, $currentLetter) === false) {
										$currentLetter = WT_UTF8_PDF . $currentLetter;
									} else {
										$currentLetter = $currentLetter . WT_UTF8_PDF; // Include a trailing + or - in the run
									}
								}
							}
						}
					} else {
						// If we're outside a numeric string, look for reasons to start it
						if (strpos(self::NUMBER_PREFIX, $currentLetter) !== false) {
							// This might be a number lead-in
							$offset   = $currentLen;
							$nextChar = substr($workingText . "\n", $offset, 1);
							if (strpos(self::NUMBERS, $nextChar) !== false) {
								$numberState = true; // We found a digit: the lead-in is therefore numeric
								if (self::$currentState == 'RTL') {
									$currentLetter = WT_UTF8_LRE . $currentLetter;
								}
							}
						} elseif (strpos(self::NUMBERS, $currentLetter) !== false) {
							$numberState = true; // The current letter is a digit
							if (self::$currentState == 'RTL') {
								$currentLetter = WT_UTF8_LRE . $currentLetter;
							}
						}
					}

					// Determine the directionality of the current UTF-8 character
					$newState = self::$currentState;
					while (true) {
						if (I18N::scriptDirection(I18N::textScript($currentLetter)) === 'rtl') {
							if (self::$currentState == '') {
								$newState = 'RTL';
								break;
							}

							if (self::$currentState == 'RTL') {
								break;
							}
							// Switch to RTL only if this isn't a solitary RTL letter
							$tempText = substr($workingText, $currentLen);
							while ($tempText != '') {
								$nextCharArray = self::getChar($tempText, 0);
								$nextLetter    = $nextCharArray['letter'];
								$nextLen       = $nextCharArray['length'];
								$tempText      = substr($tempText, $nextLen);

								if (I18N::scriptDirection(I18N::textScript($nextLetter)) === 'rtl') {
									$newState = 'RTL';
									break 2;
								}

								if (strpos(self::PUNCTUATION, $nextLetter) !== false || strpos(self::OPEN_PARENTHESES, $nextLetter) !== false) {
									$newState = 'RTL';
									break 2;
								}

								if ($nextLetter === ' ') {
									break;
								}
								$nextLetter .= substr($tempText . "\n", 0, 5);
								if ($nextLetter === '&nbsp;') {
									break;
								}
							}
							// This is a solitary RTL letter : wrap it in UTF8 control codes to force LTR directionality
							$currentLetter = WT_UTF8_LRO . $currentLetter . WT_UTF8_PDF;
							$newState      = 'LTR';
							break;
						}
						if (($currentLen != 1) || ($currentLetter >= 'A' && $currentLetter <= 'Z') || ($currentLetter >= 'a' && $currentLetter <= 'z')) {
							// Since it’s neither Hebrew nor Arabic, this UTF-8 character or ASCII letter must be LTR
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
							self::$waitingText .= $currentLetter;
							$workingText = substr($workingText, $currentLen);
							while (true) {
								if ($workingText === '') {
									break;
								}
								if (substr($workingText, 0, 1) === ' ') {
									// Spaces following this left parenthesis inherit the following directionality too
									self::$waitingText .= ' ';
									$workingText = substr($workingText, 1);
									continue;
								}
								if (substr($workingText, 0, 6) === '&nbsp;') {
									// Spaces following this left parenthesis inherit the following directionality too
									self::$waitingText .= '&nbsp;';
									$workingText = substr($workingText, 6);
									continue;
								}
								break;
							}
							$openParDirection[$openParIndex] = '?';
							break 2; // double break because we're waiting for more information
						}

						// We have a digit or a "normal" special character.
						//
						// When this character is not at the start of the input string, it inherits the preceding directionality;
						// at the start of the input string, it assumes the following directionality.
						//
						// Exceptions to this rule will be handled later during final clean-up.
						//
						self::$waitingText .= $currentLetter;
						$workingText = substr($workingText, $currentLen);
						if (self::$currentState != '') {
							$result .= self::$waitingText;
							self::$waitingText = '';
						}
						break 2; // double break because we're waiting for more information
					}
					if ($newState != self::$currentState) {
						// A direction change has occurred
						self::finishCurrentSpan($result, false);
						self::$previousState = self::$currentState;
						self::$currentState  = $newState;
						self::beginCurrentSpan($result);
					}
					self::$waitingText .= $currentLetter;
					$workingText = substr($workingText, $currentLen);
					$result .= self::$waitingText;
					self::$waitingText = '';

					foreach ($openParDirection as $index => $value) {
						// Since we now know the proper direction, remember it for all waiting opening parentheses
						if ($value === '?') {
							$openParDirection[$index] = self::$currentState;
						}
					}

					break;
			}
		}

		// We're done.  Finish last <span> if necessary
		if ($numberState) {
			if (self::$waitingText === '') {
				if (self::$currentState === 'RTL') {
					$result .= WT_UTF8_PDF;
				}
			} else {
				if (self::$currentState === 'RTL') {
					self::$waitingText .= WT_UTF8_PDF;
				}
			}
		}
		self::finishCurrentSpan($result, true);

		// Get rid of any waiting text
		if (self::$waitingText != '') {
			if (I18N::direction() === 'rtl' && self::$currentState === 'LTR') {
				$result .= self::$startRTL;
				$result .= self::$waitingText;
				$result .= self::$endRTL;
			} else {
				$result .= self::$startLTR;
				$result .= self::$waitingText;
				$result .= self::$endLTR;
			}
			self::$waitingText = '';
		}

		// Lastly, do some more cleanups

		// Move leading RTL numeric strings to following LTR text
		// (this happens when the page direction is RTL and the original text begins with a number and is followed by LTR text)
		while (substr($result, 0, self::$lenStart + 3) === self::$startRTL . WT_UTF8_LRE) {
			$spanEnd = strpos($result, self::$endRTL . self::$startLTR);
			if ($spanEnd === false) {
				break;
			}
			$textSpan = self::stripLrmRlm(substr($result, self::$lenStart + 3, $spanEnd - self::$lenStart - 3));
			if (I18N::scriptDirection(I18N::textScript($textSpan)) === 'rtl') {
				break;
			}
			$result = self::$startLTR . substr($result, self::$lenStart, $spanEnd - self::$lenStart) . substr($result, $spanEnd + self::$lenStart + self::$lenEnd);
			break;
		}

		// On RTL pages, put trailing "." in RTL numeric strings into its own RTL span
		if (I18N::direction() === 'rtl') {
			$result = str_replace(WT_UTF8_PDF . '.' . self::$endRTL, WT_UTF8_PDF . self::$endRTL . self::$startRTL . '.' . self::$endRTL, $result);
		}

		// Trim trailing blanks preceding <br> in LTR text
		while (self::$previousState != 'RTL') {
			if (strpos($result, ' <LTRbr>') !== false) {
				$result = str_replace(' <LTRbr>', '<LTRbr>', $result);
				continue;
			}
			if (strpos($result, '&nbsp;<LTRbr>') !== false) {
				$result = str_replace('&nbsp;<LTRbr>', '<LTRbr>', $result);
				continue;
			}
			if (strpos($result, ' <br>') !== false) {
				$result = str_replace(' <br>', '<br>', $result);
				continue;
			}
			if (strpos($result, '&nbsp;<br>') !== false) {
				$result = str_replace('&nbsp;<br>', '<br>', $result);
				continue;
			}
			break; // Neither space nor &nbsp; : we're done
		}

		// Trim trailing blanks preceding <br> in RTL text
		while (true) {
			if (strpos($result, ' <RTLbr>') !== false) {
				$result = str_replace(' <RTLbr>', '<RTLbr>', $result);
				continue;
			}
			if (strpos($result, '&nbsp;<RTLbr>') !== false) {
				$result = str_replace('&nbsp;<RTLbr>', '<RTLbr>', $result);
				continue;
			}
			break; // Neither space nor &nbsp; : we're done
		}

		// Convert '<LTRbr>' and '<RTLbr /'
		$result = str_replace(array('<LTRbr>', '<RTLbr>'), array(self::$endLTR . '<br>' . self::$startLTR, self::$endRTL . '<br>' . self::$startRTL), $result);

		// Include leading indeterminate directional text in whatever follows
		if (substr($result . "\n", 0, self::$lenStart) != self::$startLTR && substr($result . "\n", 0, self::$lenStart) != self::$startRTL && substr($result . "\n", 0, 6) != '<br>') {
			$leadingText = '';
			while (true) {
				if ($result == '') {
					$result = $leadingText;
					break;
				}
				if (substr($result . "\n", 0, self::$lenStart) != self::$startLTR && substr($result . "\n", 0, self::$lenStart) != self::$startRTL) {
					$leadingText .= substr($result, 0, 1);
					$result = substr($result, 1);
					continue;
				}
				$result = substr($result, 0, self::$lenStart) . $leadingText . substr($result, self::$lenStart);
				break;
			}
		}

		// Include solitary "-" and "+" in surrounding RTL text
		$result = str_replace(array(self::$endRTL . self::$startLTR . '-' . self::$endLTR . self::$startRTL, self::$endRTL . self::$startLTR . '-' . self::$endLTR . self::$startRTL), array('-', '+'), $result);

		// Remove empty spans
		$result = str_replace(array(self::$startLTR . self::$endLTR, self::$startRTL . self::$endRTL), '', $result);

		// Finally, correct '<LTR>', '</LTR>', '<RTL>', and '</RTL>'
		switch ($direction) {
			case 'BOTH':
			case 'both':
				// LTR text: <span dir="ltr"> text </span>
				// RTL text: <span dir="rtl"> text </span>
				$sLTR = '<span dir="ltr" ' . $class . '>' . $nothing;
				$eLTR = $nothing . '</span>';
				$sRTL = '<span dir="rtl" ' . $class . '>' . $nothing;
				$eRTL = $nothing . '</span>';
				break;
			case 'LTR':
			case 'ltr':
				// LTR text: <span dir="ltr"> text </span>
				// RTL text: text
				$sLTR = '<span dir="ltr" ' . $class . '>' . $nothing;
				$eLTR = $nothing . '</span>';
				$sRTL = '';
				$eRTL = '';
				break;
			case 'RTL':
			case 'rtl':
			default:
				// LTR text: text
				// RTL text: <span dir="rtl"> text </span>
				$sLTR = '';
				$eLTR = '';
				$sRTL = '<span dir="rtl" ' . $class . '>' . $nothing;
				$eRTL = $nothing . '</span>';
				break;
		}
		$result = str_replace(array(self::$startLTR, self::$endLTR, self::$startRTL, self::$endRTL), array($sLTR, $eLTR, $sRTL, $eRTL), $result);

		return $result;
	}

	/**
	 * Wrap words that have an asterisk suffix in <u> and </u> tags.
	 * This should underline starred names to show the preferred name.
	 *
	 * @param string $textSpan
	 * @param string $direction
	 *
	 * @return string
	 */
	public static function starredName($textSpan, $direction) {
		// To avoid a TCPDF bug that mixes up the word order, insert those <u> and </u> tags
		// only when page and span directions are identical.
		if ($direction === strtoupper(I18N::direction())) {
			while (true) {
				$starPos = strpos($textSpan, '*');
				if ($starPos === false) {
					break;
				}
				$trailingText = substr($textSpan, $starPos + 1);
				$textSpan     = substr($textSpan, 0, $starPos);
				$wordStart    = strrpos($textSpan, ' '); // Find the start of the word
				if ($wordStart !== false) {
					$leadingText = substr($textSpan, 0, $wordStart + 1);
					$wordText    = substr($textSpan, $wordStart + 1);
				} else {
					$leadingText = '';
					$wordText    = $textSpan;
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
	 *
	 * @param string $text
	 * @param string $offset
	 *
	 * @return array
	 */
	public static function getChar($text, $offset) {

		if ($text == '') {
			return array('letter' => '', 'length' => 0);
		}

		$char   = substr($text, $offset, 1);
		$length = 1;
		if ((ord($char) & 0xE0) == 0xC0) {
			$length = 2;
		}
		if ((ord($char) & 0xF0) == 0xE0) {
			$length = 3;
		}
		if ((ord($char) & 0xF8) == 0xF0) {
			$length = 4;
		}
		$letter = substr($text, $offset, $length);

		return array('letter' => $letter, 'length' => $length);
	}

	/**
	 * Insert <br> into current span
	 *
	 * @param string $result
	 */
	public static function breakCurrentSpan(&$result) {
		// Interrupt the current span, insert that <br>, and then continue the current span
		$result .= self::$waitingText;
		self::$waitingText = '';

		$breakString = '<' . self::$currentState . 'br>';
		$result .= $breakString;

		return;
	}

	/**
	 * Begin current span
	 *
	 * @param string $result
	 */
	public static function beginCurrentSpan(&$result) {
		if (self::$currentState == 'LTR') {
			$result .= self::$startLTR;
		}
		if (self::$currentState == 'RTL') {
			$result .= self::$startRTL;
		}

		self::$posSpanStart = strlen($result);
	}

	/**
	 * Finish current span
	 *
	 * @param string $result
	 * @param bool $theEnd
	 */
	public static function finishCurrentSpan(&$result, $theEnd = false) {
		$textSpan = substr($result, self::$posSpanStart);
		$result   = substr($result, 0, self::$posSpanStart);

		// Get rid of empty spans, so that our check for presence of RTL will work
		$result = str_replace(array(self::$startLTR . self::$endLTR, self::$startRTL . self::$endRTL), '', $result);

		// Look for numeric strings that are times (hh:mm:ss).  These have to be separated from surrounding numbers.
		$tempResult = '';
		while ($textSpan != '') {
			$posColon = strpos($textSpan, ':');
			if ($posColon === false) {
				break;
			} // No more possible time strings
			$posLRE = strpos($textSpan, WT_UTF8_LRE);
			if ($posLRE === false) {
				break;
			} // No more numeric strings
			$posPDF = strpos($textSpan, WT_UTF8_PDF, $posLRE);
			if ($posPDF === false) {
				break;
			} // No more numeric strings

			$tempResult .= substr($textSpan, 0, $posLRE + 3); // Copy everything preceding the numeric string
			$numericString = substr($textSpan, $posLRE + 3, $posPDF - $posLRE); // Separate the entire numeric string
			$textSpan      = substr($textSpan, $posPDF + 3);
			$posColon      = strpos($numericString, ':');
			if ($posColon === false) {
				// Nothing that looks like a time here
				$tempResult .= $numericString;
				continue;
			}
			$posBlank = strpos($numericString . ' ', ' ');
			$posNbsp  = strpos($numericString . '&nbsp;', '&nbsp;');
			if ($posBlank < $posNbsp) {
				$posSeparator    = $posBlank;
				$lengthSeparator = 1;
			} else {
				$posSeparator    = $posNbsp;
				$lengthSeparator = 6;
			}
			if ($posColon > $posSeparator) {
				// We have a time string preceded by a blank: Exclude that blank from the numeric string
				$tempResult .= substr($numericString, 0, $posSeparator);
				$tempResult .= WT_UTF8_PDF;
				$tempResult .= substr($numericString, $posSeparator, $lengthSeparator);
				$tempResult .= WT_UTF8_LRE;
				$numericString = substr($numericString, $posSeparator + $lengthSeparator);
			}

			$posBlank = strpos($numericString, ' ');
			$posNbsp  = strpos($numericString, '&nbsp;');
			if ($posBlank === false && $posNbsp === false) {
				// The time string isn't followed by a blank
				$textSpan = $numericString . $textSpan;
				continue;
			}

			// We have a time string followed by a blank: Exclude that blank from the numeric string
			if ($posBlank === false) {
				$posSeparator    = $posNbsp;
				$lengthSeparator = 6;
			} elseif ($posNbsp === false) {
				$posSeparator    = $posBlank;
				$lengthSeparator = 1;
			} elseif ($posBlank < $posNbsp) {
				$posSeparator    = $posBlank;
				$lengthSeparator = 1;
			} else {
				$posSeparator    = $posNbsp;
				$lengthSeparator = 6;
			}
			$tempResult .= substr($numericString, 0, $posSeparator);
			$tempResult .= WT_UTF8_PDF;
			$tempResult .= substr($numericString, $posSeparator, $lengthSeparator);
			$posSeparator += $lengthSeparator;
			$numericString = substr($numericString, $posSeparator);
			$textSpan      = WT_UTF8_LRE . $numericString . $textSpan;
		}
		$textSpan       = $tempResult . $textSpan;
		$trailingBlanks = '';
		$trailingBreaks = '';

		/* ****************************** LTR text handling ******************************** */

		if (self::$currentState === 'LTR') {
			// Move trailing numeric strings to the following RTL text.  Include any blanks preceding or following the numeric text too.
			if (I18N::direction() === 'rtl' && self::$previousState === 'RTL' && !$theEnd) {
				$trailingString = '';
				$savedSpan      = $textSpan;
				while ($textSpan !== '') {
					// Look for trailing spaces and tentatively move them
					if (substr($textSpan, -1) === ' ') {
						$trailingString = ' ' . $trailingString;
						$textSpan       = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -6) === '&nbsp;') {
						$trailingString = '&nbsp;' . $trailingString;
						$textSpan       = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -3) !== WT_UTF8_PDF) {
						// There is no trailing numeric string
						$textSpan = $savedSpan;
						break;
					}

					// We have a numeric string
					$posStartNumber = strrpos($textSpan, WT_UTF8_LRE);
					if ($posStartNumber === false) {
						$posStartNumber = 0;
					}
					$trailingString = substr($textSpan, $posStartNumber, strlen($textSpan) - $posStartNumber) . $trailingString;
					$textSpan       = substr($textSpan, 0, $posStartNumber);

					// Look for more spaces and move them too
					while ($textSpan != '') {
						if (substr($textSpan, -1) == ' ') {
							$trailingString = ' ' . $trailingString;
							$textSpan       = substr($textSpan, 0, -1);
							continue;
						}
						if (substr($textSpan, -6) == '&nbsp;') {
							$trailingString = '&nbsp;' . $trailingString;
							$textSpan       = substr($textSpan, 0, -1);
							continue;
						}
						break;
					}

					self::$waitingText = $trailingString . self::$waitingText;
					break;
				}
			}

			$savedSpan = $textSpan;
			// Move any trailing <br>, optionally preceded or followed by blanks, outside this LTR span
			while ($textSpan != '') {
				if (substr($textSpan, -1) == ' ') {
					$trailingBlanks = ' ' . $trailingBlanks;
					$textSpan       = substr($textSpan, 0, -1);
					continue;
				}
				if (substr('......' . $textSpan, -6) == '&nbsp;') {
					$trailingBlanks = '&nbsp;' . $trailingBlanks;
					$textSpan       = substr($textSpan, 0, -6);
					continue;
				}
				break;
			}
			while (substr($textSpan, -9) == '<LTRbr>') {
				$trailingBreaks = '<br>' . $trailingBreaks; // Plain <br> because it’s outside a span
				$textSpan       = substr($textSpan, 0, -9);
			}
			if ($trailingBreaks != '') {
				while ($textSpan != '') {
					if (substr($textSpan, -1) == ' ') {
						$trailingBreaks = ' ' . $trailingBreaks;
						$textSpan       = substr($textSpan, 0, -1);
						continue;
					}
					if (substr('......' . $textSpan, -6) == '&nbsp;') {
						$trailingBreaks = '&nbsp;' . $trailingBreaks;
						$textSpan       = substr($textSpan, 0, -6);
						continue;
					}
					break;
				}
				self::$waitingText = $trailingBlanks . self::$waitingText; // Put those trailing blanks inside the following span
			} else {
				$textSpan = $savedSpan;
			}

			$trailingBlanks      = '';
			$trailingPunctuation = '';
			$trailingID          = '';
			$trailingSeparator   = '';
			$leadingSeparator    = '';
			while (I18N::direction() === 'rtl') {
				if (strpos($result, self::$startRTL) !== false) {
					// Remove trailing blanks for inclusion in a separate LTR span
					while ($textSpan != '') {
						if (substr($textSpan, -1) === ' ') {
							$trailingBlanks = ' ' . $trailingBlanks;
							$textSpan       = substr($textSpan, 0, -1);
							continue;
						}
						if (substr($textSpan, -6) === '&nbsp;') {
							$trailingBlanks = '&nbsp;' . $trailingBlanks;
							$textSpan       = substr($textSpan, 0, -1);
							continue;
						}
						break;
					}

					// Remove trailing punctuation for inclusion in a separate LTR span
					if ($textSpan == '') {
						$trailingChar = "\n";
					} else {
						$trailingChar = substr($textSpan, -1);
					}
					if (strpos(self::PUNCTUATION, $trailingChar) !== false) {
						$trailingPunctuation = $trailingChar;
						$textSpan            = substr($textSpan, 0, -1);
					}
				}

				// Remove trailing ID numbers that look like "(xnnn)" for inclusion in a separate LTR span
				while (true) {
					if (substr($textSpan, -1) != ')') {
						break;
					} // There is no trailing ')'
					$posLeftParen = strrpos($textSpan, '(');
					if ($posLeftParen === false) {
						break;
					} // There is no leading '('
					$temp = self::stripLrmRlm(substr($textSpan, $posLeftParen)); // Get rid of UTF8 control codes

					// If the parenthesized text doesn't look like an ID number,
					// we don't want to touch it.
					// This check won’t work if somebody uses ID numbers with an unusual format.
					$offset    = 1;
					$charArray = self::getchar($temp, $offset); // Get 1st character of parenthesized text
					if (strpos(self::NUMBERS, $charArray['letter']) !== false) {
						break;
					}
					$offset += $charArray['length']; // Point at 2nd character of parenthesized text
					if (strpos(self::NUMBERS, substr($temp, $offset, 1)) === false) {
						break;
					}
					// 1st character of parenthesized text is alpha, 2nd character is a digit; last has to be a digit too
					if (strpos(self::NUMBERS, substr($temp, -2, 1)) === false) {
						break;
					}

					$trailingID = substr($textSpan, $posLeftParen);
					$textSpan   = substr($textSpan, 0, $posLeftParen);
					break;
				}

				// Look for " - " or blank preceding the ID number and remove it for inclusion in a separate LTR span
				if ($trailingID != '') {
					while ($textSpan != '') {
						if (substr($textSpan, -1) == ' ') {
							$trailingSeparator = ' ' . $trailingSeparator;
							$textSpan          = substr($textSpan, 0, -1);
							continue;
						}
						if (substr($textSpan, -6) == '&nbsp;') {
							$trailingSeparator = '&nbsp;' . $trailingSeparator;
							$textSpan          = substr($textSpan, 0, -6);
							continue;
						}
						if (substr($textSpan, -1) == '-') {
							$trailingSeparator = '-' . $trailingSeparator;
							$textSpan          = substr($textSpan, 0, -1);
							continue;
						}
						break;
					}
				}

				// Look for " - " preceding the text and remove it for inclusion in a separate LTR span
				$foundSeparator = false;
				$savedSpan      = $textSpan;
				while ($textSpan != '') {
					if (substr($textSpan, 0, 1) == ' ') {
						$leadingSeparator = ' ' . $leadingSeparator;
						$textSpan         = substr($textSpan, 1);
						continue;
					}
					if (substr($textSpan, 0, 6) == '&nbsp;') {
						$leadingSeparator = '&nbsp;' . $leadingSeparator;
						$textSpan         = substr($textSpan, 6);
						continue;
					}
					if (substr($textSpan, 0, 1) == '-') {
						$leadingSeparator = '-' . $leadingSeparator;
						$textSpan         = substr($textSpan, 1);
						$foundSeparator   = true;
						continue;
					}
					break;
				}
				if (!$foundSeparator) {
					$textSpan         = $savedSpan;
					$leadingSeparator = '';
				}
				break;
			}

			// We're done: finish the span
			$textSpan = self::starredName($textSpan, 'LTR'); // Wrap starred name in <u> and </u> tags
			while (true) {
				// Remove blanks that precede <LTRbr>
				if (strpos($textSpan, ' <LTRbr>') !== false) {
					$textSpan = str_replace(' <LTRbr>', '<LTRbr>', $textSpan);
					continue;
				}
				if (strpos($textSpan, '&nbsp;<LTRbr>') !== false) {
					$textSpan = str_replace('&nbsp;<LTRbr>', '<LTRbr>', $textSpan);
					continue;
				}
				break;
			}
			if ($leadingSeparator != '') {
				$result = $result . self::$startLTR . $leadingSeparator . self::$endLTR;
			}
			$result = $result . $textSpan . self::$endLTR;
			if ($trailingSeparator != '') {
				$result = $result . self::$startLTR . $trailingSeparator . self::$endLTR;
			}
			if ($trailingID != '') {
				$result = $result . self::$startLTR . $trailingID . self::$endLTR;
			}
			if ($trailingPunctuation != '') {
				$result = $result . self::$startLTR . $trailingPunctuation . self::$endLTR;
			}
			if ($trailingBlanks != '') {
				$result = $result . self::$startLTR . $trailingBlanks . self::$endLTR;
			}
		}

		/* ****************************** RTL text handling ******************************** */

		if (self::$currentState == 'RTL') {
			$savedSpan = $textSpan;

			// Move any trailing <br>, optionally followed by blanks, outside this RTL span
			while ($textSpan != '') {
				if (substr($textSpan, -1) == ' ') {
					$trailingBlanks = ' ' . $trailingBlanks;
					$textSpan       = substr($textSpan, 0, -1);
					continue;
				}
				if (substr('......' . $textSpan, -6) == '&nbsp;') {
					$trailingBlanks = '&nbsp;' . $trailingBlanks;
					$textSpan       = substr($textSpan, 0, -6);
					continue;
				}
				break;
			}
			while (substr($textSpan, -9) == '<RTLbr>') {
				$trailingBreaks = '<br>' . $trailingBreaks; // Plain <br> because it’s outside a span
				$textSpan       = substr($textSpan, 0, -9);
			}
			if ($trailingBreaks != '') {
				self::$waitingText = $trailingBlanks . self::$waitingText; // Put those trailing blanks inside the following span
			} else {
				$textSpan = $savedSpan;
			}

			// Move trailing numeric strings to the following LTR text.  Include any blanks preceding or following the numeric text too.
			if (!$theEnd && I18N::direction() !== 'rtl') {
				$trailingString = '';
				$savedSpan      = $textSpan;
				while ($textSpan != '') {
					// Look for trailing spaces and tentatively move them
					if (substr($textSpan, -1) === ' ') {
						$trailingString = ' ' . $trailingString;
						$textSpan       = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -6) === '&nbsp;') {
						$trailingString = '&nbsp;' . $trailingString;
						$textSpan       = substr($textSpan, 0, -1);
						continue;
					}
					if (substr($textSpan, -3) !== WT_UTF8_PDF) {
						// There is no trailing numeric string
						$textSpan = $savedSpan;
						break;
					}

					// We have a numeric string
					$posStartNumber = strrpos($textSpan, WT_UTF8_LRE);
					if ($posStartNumber === false) {
						$posStartNumber = 0;
					}
					$trailingString = substr($textSpan, $posStartNumber, strlen($textSpan) - $posStartNumber) . $trailingString;
					$textSpan       = substr($textSpan, 0, $posStartNumber);

					// Look for more spaces and move them too
					while ($textSpan != '') {
						if (substr($textSpan, -1) == ' ') {
							$trailingString = ' ' . $trailingString;
							$textSpan       = substr($textSpan, 0, -1);
							continue;
						}
						if (substr($textSpan, -6) == '&nbsp;') {
							$trailingString = '&nbsp;' . $trailingString;
							$textSpan       = substr($textSpan, 0, -1);
							continue;
						}
						break;
					}

					self::$waitingText = $trailingString . self::$waitingText;
					break;
				}
			}

			// Trailing " - " needs to be prefixed to the following span
			if (!$theEnd && substr('...' . $textSpan, -3) == ' - ') {
				$textSpan          = substr($textSpan, 0, -3);
				self::$waitingText = ' - ' . self::$waitingText;
			}

			while (I18N::direction() === 'rtl') {
				// Look for " - " preceding <RTLbr> and relocate it to the front of the string
				$posDashString = strpos($textSpan, ' - <RTLbr>');
				if ($posDashString === false) {
					break;
				}
				$posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr>');
				if ($posStringStart === false) {
					$posStringStart = 0;
				} else {
					$posStringStart += 9;
				} // Point to the first char following the last <RTLbr>

				$textSpan = substr($textSpan, 0, $posStringStart) . ' - ' . substr($textSpan, $posStringStart, $posDashString - $posStringStart) . substr($textSpan, $posDashString + 3);
			}

			// Strip leading spaces from the RTL text
			$countLeadingSpaces = 0;
			while ($textSpan != '') {
				if (substr($textSpan, 0, 1) == ' ') {
					$countLeadingSpaces++;
					$textSpan = substr($textSpan, 1);
					continue;
				}
				if (substr($textSpan, 0, 6) == '&nbsp;') {
					$countLeadingSpaces++;
					$textSpan = substr($textSpan, 6);
					continue;
				}
				break;
			}

			// Strip trailing spaces from the RTL text
			$countTrailingSpaces = 0;
			while ($textSpan != '') {
				if (substr($textSpan, -1) == ' ') {
					$countTrailingSpaces++;
					$textSpan = substr($textSpan, 0, -1);
					continue;
				}
				if (substr($textSpan, -6) == '&nbsp;') {
					$countTrailingSpaces++;
					$textSpan = substr($textSpan, 0, -6);
					continue;
				}
				break;
			}

			// Look for trailing " -", reverse it, and relocate it to the front of the string
			if (substr($textSpan, -2) === ' -') {
				$posDashString  = strlen($textSpan) - 2;
				$posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr>');
				if ($posStringStart === false) {
					$posStringStart = 0;
				} else {
					$posStringStart += 9;
				} // Point to the first char following the last <RTLbr>

				$textSpan = substr($textSpan, 0, $posStringStart) . '- ' . substr($textSpan, $posStringStart, $posDashString - $posStringStart) . substr($textSpan, $posDashString + 2);
			}

			if ($countLeadingSpaces != 0) {
				$newLength = strlen($textSpan) + $countLeadingSpaces;
				$textSpan  = str_pad($textSpan, $newLength, ' ', (I18N::direction() === 'rtl' ? STR_PAD_LEFT : STR_PAD_RIGHT));
			}
			if ($countTrailingSpaces != 0) {
				if (I18N::direction() === 'ltr') {
					if ($trailingBreaks === '') {
						// Move trailing RTL spaces to front of following LTR span
						$newLength         = strlen(self::$waitingText) + $countTrailingSpaces;
						self::$waitingText = str_pad(self::$waitingText, $newLength, ' ', STR_PAD_LEFT);
					}
				} else {
					$newLength = strlen($textSpan) + $countTrailingSpaces;
					$textSpan  = str_pad($textSpan, $newLength, ' ', STR_PAD_RIGHT);
				}
			}

			// We're done: finish the span
			$textSpan = self::starredName($textSpan, 'RTL'); // Wrap starred name in <u> and </u> tags
			$result   = $result . $textSpan . self::$endRTL;
		}

		if (self::$currentState != 'LTR' && self::$currentState != 'RTL') {
			$result = $result . $textSpan;
		}

		$result .= $trailingBreaks; // Get rid of any waiting <br>

		return;
	}

	/**
	 * Wrap text, similar to the PHP wordwrap() function.
	 *
	 * @param string $string
	 * @param int $width
	 * @param string $sep
	 * @param bool $cut
	 *
	 * @return string
	 */
	public static function utf8WordWrap($string, $width = 75, $sep = "\n", $cut = false) {
		$out = '';
		while ($string) {
			if (mb_strlen($string) <= $width) {
				// Do not wrap any text that is less than the output area.
				$out .= $string;
				$string = '';
			} else {
				$sub1 = mb_substr($string, 0, $width + 1);
				if (mb_substr($string, mb_strlen($sub1) - 1, 1) == ' ') {
					// include words that end by a space immediately after the area.
					$sub = $sub1;
				} else {
					$sub = mb_substr($string, 0, $width);
				}
				$spacepos = strrpos($sub, ' ');
				if ($spacepos === false) {
					// No space on line?
					if ($cut) {
						$out .= $sub . $sep;
						$string = mb_substr($string, mb_strlen($sub));
					} else {
						$spacepos = strpos($string, ' ');
						if ($spacepos === false) {
							$out .= $string;
							$string = '';
						} else {
							$out .= substr($string, 0, $spacepos) . $sep;
							$string = substr($string, $spacepos + 1);
						}
					}
				} else {
					// Split at space;
					$out .= substr($string, 0, $spacepos) . $sep;
					$string = substr($string, $spacepos + 1);
				}
			}
		}

		return $out;
	}
}
