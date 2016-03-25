<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageEo - Representation of the Esperanto language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'eo';
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
