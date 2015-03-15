<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFi - Representation of the Finnish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'fi';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryFi;
	}
}
