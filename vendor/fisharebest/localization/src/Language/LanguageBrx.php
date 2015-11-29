<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageBrx - Representation of the Bodo (India) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBrx extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'brx';
	}

	public function defaultTerritory() {
		return new TerritoryIn;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
