<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageKcg - Representation of the Katab language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKaj extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'kaj';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNg;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
