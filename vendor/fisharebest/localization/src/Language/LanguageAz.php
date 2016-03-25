<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryIr;

/**
 * Class LanguageAz - Representation of the Azerbaijani language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAz extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'az';
	}

	public function defaultTerritory() {
		return new TerritoryIr;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
