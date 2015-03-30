<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule10;
use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageHsb - Representation of the Upper Sorbian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHsb extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'hsb';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDe;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule10;
	}
}
