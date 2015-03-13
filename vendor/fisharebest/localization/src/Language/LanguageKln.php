<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKln - Representation of the Kalenjin language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKln extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kln';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
