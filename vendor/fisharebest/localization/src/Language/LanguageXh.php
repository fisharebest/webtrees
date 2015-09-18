<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LanguageXh - Representation of the Xhosa language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageXh extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'xh';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZa;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
