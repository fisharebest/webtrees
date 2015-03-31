<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule4;
use Fisharebest\Localization\Territory\TerritoryGb;

/**
 * Class LanguageGd - Representation of the Scottish Gaelic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGd extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'gd';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGb;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule4;
	}
}
