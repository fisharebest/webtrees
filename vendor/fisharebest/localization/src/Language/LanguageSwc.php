<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSwc - Representation of the Congo Swahili language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSwc extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'swc';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCd;
	}
}
