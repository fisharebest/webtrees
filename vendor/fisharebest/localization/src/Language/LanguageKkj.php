<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKkj - Representation of the Kako language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKkj extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kkj';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
