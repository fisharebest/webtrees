<?php namespace Fisharebest\Localization;

/**
 * Class LanguageGuz - Representation of the Gusii language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGuz extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'guz';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
