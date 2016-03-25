<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule3;
use Fisharebest\Localization\Territory\Territory001;

/**
 * Class LanguagePrg - Representation of the Old Prussian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguagePrg extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'prg';
	}

	public function defaultTerritory() {
		return new Territory001;
	}

	public function pluralRule() {
		return new PluralRule3;
	}
}
