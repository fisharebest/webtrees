<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageEn - Representation of the English language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'en';
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
