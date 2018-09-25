<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSu;

/**
 * Class LocaleSu
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class LocaleSu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ᮘᮞ ᮞᮥᮔ᮪ᮓ';
    }

    public function language()
    {
        return new LanguageSu();
    }
}
