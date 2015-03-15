<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLuo - Representation of the Luo (Kenya and Tanzania) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLuo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'luo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
