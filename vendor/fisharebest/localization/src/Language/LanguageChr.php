<?php namespace Fisharebest\Localization;

/**
 * Class LanguageChr - Representation of the Cherokee language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageChr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'chr';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUs;
	}
}
