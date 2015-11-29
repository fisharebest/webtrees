<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageIa - Representation of the Interlingua (International Auxiliary AbstractLanguage language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ia';
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
