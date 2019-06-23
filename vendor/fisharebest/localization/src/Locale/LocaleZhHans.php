<?php

namespace Fisharebest\Localization\Locale;

/**
 * Class LocaleZhHans - Simplified Chinese
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleZhHans extends LocaleZh
{
    public function endonym()
    {
        if (get_class($this) === __NAMESPACE__ . '\LocaleZhHans') {
            // If the Hans script has been specified (but no other tags), then it is customary to include it.
            return '简体中文';
        }

        return parent::endonym();
    }

    public function languageTag()
    {
        if (get_class($this) === __NAMESPACE__ . '\LocaleZhHans') {
            // If the Hans script has been specified (but no other tags), then it is customary to include it.
            return 'zh-Hans';
        }

        return parent::languageTag();
    }
}
