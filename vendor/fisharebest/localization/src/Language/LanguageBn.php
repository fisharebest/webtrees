<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBn - Representation of the Bengali language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'bn';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptBeng;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryBd;
	}
}
