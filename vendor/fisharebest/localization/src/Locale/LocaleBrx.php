<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBrx - Bodo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBrx extends Locale {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'बड़ो';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBrx;
	}
}
