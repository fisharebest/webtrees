<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Populate the gedcom_setting table
 */
class SeedGedcomSettingTable implements SeedInterface
{
    private const DEFAULT_SETTINGS = [
        'ADVANCED_NAME_FACTS'          => 'NICK',
        'ADVANCED_PLAC_FACTS'          => '',
        'CALENDAR_FORMAT'              => 'gregorian',
        'CHART_BOX_TAGS'               => '',
        'EXPAND_SOURCES'               => '0',
        'FORMAT_TEXT'                  => 'markdown',
        'FULL_SOURCES'                 => '0',
        'GEDCOM_MEDIA_PATH'            => '',
        'GENERATE_UIDS'                => '0',
        'HIDE_GEDCOM_ERRORS'           => '1',
        'HIDE_LIVE_PEOPLE'             => '1',
        'INDI_FACTS_ADD'               => 'AFN,BIRT,DEAT,BURI,CREM,ADOP,BAPM,BARM,BASM,BLES,CHRA,CONF,FCOM,ORDN,NATU,EMIG,IMMI,CENS,PROB,WILL,GRAD,RETI,DSCR,EDUC,IDNO,NATI,NCHI,NMR,OCCU,PROP,RELI,RESI,SSN,TITL,BAPL,CONL,ENDL,SLGC,ASSO,RESN',
        'INDI_FACTS_QUICK'             => 'BIRT,BURI,BAPM,CENS,DEAT,OCCU,RESI',
        'INDI_FACTS_UNIQUE'            => '',
        'KEEP_ALIVE_YEARS_BIRTH'       => '',
        'KEEP_ALIVE_YEARS_DEATH'       => '',
        'LANGUAGE'                     => 'en-US',
        'MAX_ALIVE_AGE'                => '120',
        'MEDIA_DIRECTORY'              => 'media/',
        'MEDIA_UPLOAD'                 => Auth::PRIV_USER,
        'META_DESCRIPTION'             => '',
        'META_TITLE'                   => Webtrees::NAME,
        'NO_UPDATE_CHAN'               => '0',
        'PEDIGREE_ROOT_ID'             => '',
        'PREFER_LEVEL2_SOURCES'        => '1',
        'QUICK_REQUIRED_FACTS'         => 'BIRT,DEAT',
        'QUICK_REQUIRED_FAMFACTS'      => 'MARR',
        'REQUIRE_AUTHENTICATION'       => '0',
        'SAVE_WATERMARK_IMAGE'         => '0',
        'SHOW_AGE_DIFF'                => '0',
        'SHOW_COUNTER'                 => '1',
        'SHOW_DEAD_PEOPLE'             => Auth::PRIV_PRIVATE,
        'SHOW_EST_LIST_DATES'          => '0',
        'SHOW_FACT_ICONS'              => '1',
        'SHOW_GEDCOM_RECORD'           => '0',
        'SHOW_HIGHLIGHT_IMAGES'        => '1',
        'SHOW_LEVEL2_NOTES'            => '1',
        'SHOW_LIVING_NAMES'            => Auth::PRIV_USER,
        'SHOW_MEDIA_DOWNLOAD'          => '0',
        'SHOW_NO_WATERMARK'            => Auth::PRIV_USER,
        'SHOW_PARENTS_AGE'             => '1',
        'SHOW_PEDIGREE_PLACES'         => '9',
        'SHOW_PEDIGREE_PLACES_SUFFIX'  => '0',
        'SHOW_PRIVATE_RELATIONSHIPS'   => '1',
        'SHOW_RELATIVES_EVENTS'        => '_BIRT_CHIL,_BIRT_SIBL,_MARR_CHIL,_MARR_PARE,_DEAT_CHIL,_DEAT_PARE,_DEAT_GPAR,_DEAT_SIBL,_DEAT_SPOU',
        'SUBLIST_TRIGGER_I'            => '200',
        'SURNAME_LIST_STYLE'           => 'style2',
        'SURNAME_TRADITION'            => 'paternal',
        'THUMBNAIL_WIDTH'              => '100',
        'USE_SILHOUETTE'               => '1',
        'WORD_WRAPPED_NOTES'           => '0',
    ];

    /**
     *  Run the seeder.
     *
     * @return void
     */
    public function run(): void
    {
        // Set default settings for new trees
        foreach (self::DEFAULT_SETTINGS as $setting_name => $setting_value) {
            DB::table('gedcom_setting')->updateOrInsert([
                'gedcom_id'     => -1,
                'setting_name'  => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);
        }
    }
}
