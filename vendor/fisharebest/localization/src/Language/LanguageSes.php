<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSes - Representation of the Koyraboro Senni Songhai language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSes extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ses';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMl;
	}
}
