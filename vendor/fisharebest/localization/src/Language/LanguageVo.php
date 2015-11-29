<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageVo - Representation of the Volapük language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageVo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'vo';
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
