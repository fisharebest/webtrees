<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees;

use Closure;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\PendingChangesService;
use InvalidArgumentException;
use League\Flysystem\FilesystemOperator;

use function array_key_exists;
use function date;
use function is_string;
use function str_starts_with;
use function strtoupper;
use function substr_replace;

/**
 * Provide an interface to the wt_gedcom table.
 */
class Tree
{
    private const RESN_PRIVACY = [
        'none'         => Auth::PRIV_PRIVATE,
        'privacy'      => Auth::PRIV_USER,
        'confidential' => Auth::PRIV_NONE,
        'hidden'       => Auth::PRIV_HIDE,
    ];

    // Default values for some tree preferences.
    protected const DEFAULT_PREFERENCES = [
        'CALENDAR_FORMAT'              => 'gregorian',
        'CHART_BOX_TAGS'               => '',
        'EXPAND_SOURCES'               => '0',
        'FAM_FACTS_QUICK'              => 'ENGA,MARR,DIV',
        'FORMAT_TEXT'                  => 'markdown',
        'GEDCOM_MEDIA_PATH'            => '',
        'GENERATE_UIDS'                => '0',
        'HIDE_GEDCOM_ERRORS'           => '1',
        'HIDE_LIVE_PEOPLE'             => '1',
        'INDI_FACTS_QUICK'             => 'BIRT,BURI,BAPM,CENS,DEAT,OCCU,RESI',
        'KEEP_ALIVE_YEARS_BIRTH'       => '',
        'KEEP_ALIVE_YEARS_DEATH'       => '',
        'LANGUAGE'                     => 'en-US',
        'MAX_ALIVE_AGE'                => '120',
        'MEDIA_DIRECTORY'              => 'media/',
        'MEDIA_UPLOAD'                 => '1', // Auth::PRIV_USER
        'META_DESCRIPTION'             => '',
        'META_TITLE'                   => Webtrees::NAME,
        'NO_UPDATE_CHAN'               => '0',
        'PEDIGREE_ROOT_ID'             => '',
        'QUICK_REQUIRED_FACTS'         => 'BIRT,DEAT',
        'QUICK_REQUIRED_FAMFACTS'      => 'MARR',
        'REQUIRE_AUTHENTICATION'       => '0',
        'SAVE_WATERMARK_IMAGE'         => '0',
        'SHOW_AGE_DIFF'                => '0',
        'SHOW_COUNTER'                 => '1',
        'SHOW_DEAD_PEOPLE'             => '2', // Auth::PRIV_PRIVATE
        'SHOW_EST_LIST_DATES'          => '0',
        'SHOW_FACT_ICONS'              => '1',
        'SHOW_GEDCOM_RECORD'           => '0',
        'SHOW_HIGHLIGHT_IMAGES'        => '1',
        'SHOW_LEVEL2_NOTES'            => '1',
        'SHOW_LIVING_NAMES'            => '1', // Auth::PRIV_USER
        'SHOW_MEDIA_DOWNLOAD'          => '0',
        'SHOW_NO_WATERMARK'            => '1', // Auth::PRIV_USER
        'SHOW_PARENTS_AGE'             => '1',
        'SHOW_PEDIGREE_PLACES'         => '9',
        'SHOW_PEDIGREE_PLACES_SUFFIX'  => '0',
        'SHOW_PRIVATE_RELATIONSHIPS'   => '1',
        'SHOW_RELATIVES_EVENTS'        => '_BIRT_CHIL,_BIRT_SIBL,_MARR_CHIL,_MARR_PARE,_DEAT_CHIL,_DEAT_PARE,_DEAT_GPAR,_DEAT_SIBL,_DEAT_SPOU',
        'SUBLIST_TRIGGER_I'            => '200',
        'SURNAME_LIST_STYLE'           => 'style2',
        'SURNAME_TRADITION'            => 'paternal',
        'USE_SILHOUETTE'               => '1',
        'WORD_WRAPPED_NOTES'           => '0',
    ];

    private int $id;

    private string $name;

    private string $title;

    /** @var array<int> Default access rules for facts in this tree */
    private array $fact_privacy;

    /** @var array<int> Default access rules for individuals in this tree */
    private array $individual_privacy;

    /** @var array<array<int>> Default access rules for individual facts in this tree */
    private array $individual_fact_privacy;

    /** @var array<string> Cached copy of the wt_gedcom_setting table. */
    private array $preferences = [];

    /** @var array<array<string>> Cached copy of the wt_user_gedcom_setting table. */
    private array $user_preferences = [];

    /**
     * Create a tree object.
     *
     * @param int    $id
     * @param string $name
     * @param string $title
     */
    public function __construct(int $id, string $name, string $title)
    {
        $this->id                      = $id;
        $this->name                    = $name;
        $this->title                   = $title;
        $this->fact_privacy            = [];
        $this->individual_privacy      = [];
        $this->individual_fact_privacy = [];

        // Load the privacy settings for this tree
        $rows = DB::table('default_resn')
            ->where('gedcom_id', '=', $this->id)
            ->get();

        foreach ($rows as $row) {
            // Convert GEDCOM privacy restriction to a webtrees access level.
            $row->resn = self::RESN_PRIVACY[$row->resn];

            if ($row->xref !== null) {
                if ($row->tag_type !== null) {
                    $this->individual_fact_privacy[$row->xref][$row->tag_type] = $row->resn;
                } else {
                    $this->individual_privacy[$row->xref] = $row->resn;
                }
            } else {
                $this->fact_privacy[$row->tag_type] = $row->resn;
            }
        }
    }

    /**
     * A closure which will create a record from a database row.
     *
     * @return Closure(object):Tree
     */
    public static function rowMapper(): Closure
    {
        return static fn (object $row): Tree => new Tree((int) $row->tree_id, $row->tree_name, $row->tree_title);
    }

    /**
     * Set the tree’s configuration settings.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return self
     */
    public function setPreference(string $setting_name, string $setting_value): Tree
    {
        if ($setting_value !== $this->getPreference($setting_name)) {
            DB::table('gedcom_setting')->updateOrInsert([
                'gedcom_id'    => $this->id,
                'setting_name' => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);

            $this->preferences[$setting_name] = $setting_value;

            Log::addConfigurationLog('Tree preference "' . $setting_name . '" set to "' . $setting_value . '"', $this);
        }

        return $this;
    }

    /**
     * Get the tree’s configuration settings.
     *
     * @param string      $setting_name
     * @param string|null $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string|null $default = null): string
    {
        if ($this->preferences === []) {
            $this->preferences = DB::table('gedcom_setting')
                ->where('gedcom_id', '=', $this->id)
                ->pluck('setting_value', 'setting_name')
                ->all();
        }

        return $this->preferences[$setting_name] ?? $default ?? self::DEFAULT_PREFERENCES[$setting_name] ?? '';
    }

    /**
     * The name of this tree
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * The title of this tree
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * The fact-level privacy for this tree.
     *
     * @return array<int>
     */
    public function getFactPrivacy(): array
    {
        return $this->fact_privacy;
    }

    /**
     * The individual-level privacy for this tree.
     *
     * @return array<int>
     */
    public function getIndividualPrivacy(): array
    {
        return $this->individual_privacy;
    }

    /**
     * The individual-fact-level privacy for this tree.
     *
     * @return array<array<int>>
     */
    public function getIndividualFactPrivacy(): array
    {
        return $this->individual_fact_privacy;
    }

    /**
     * Set the tree’s user-configuration settings.
     *
     * @param UserInterface $user
     * @param string        $setting_name
     * @param string        $setting_value
     *
     * @return self
     */
    public function setUserPreference(UserInterface $user, string $setting_name, string $setting_value): Tree
    {
        if ($this->getUserPreference($user, $setting_name) !== $setting_value) {
            // Update the database
            DB::table('user_gedcom_setting')->updateOrInsert([
                'gedcom_id'    => $this->id(),
                'user_id'      => $user->id(),
                'setting_name' => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);

            // Update the cache
            $this->user_preferences[$user->id()][$setting_name] = $setting_value;
            // Audit log of changes
            Log::addConfigurationLog('Tree preference "' . $setting_name . '" set to "' . $setting_value . '" for user "' . $user->userName() . '"', $this);
        }

        return $this;
    }

    /**
     * Get the tree’s user-configuration settings.
     *
     * @param UserInterface $user
     * @param string        $setting_name
     * @param string        $default
     *
     * @return string
     */
    public function getUserPreference(UserInterface $user, string $setting_name, string $default = ''): string
    {
        // There are lots of settings, and we need to fetch lots of them on every page
        // so it is quicker to fetch them all in one go.
        if (!array_key_exists($user->id(), $this->user_preferences)) {
            $this->user_preferences[$user->id()] = DB::table('user_gedcom_setting')
                ->where('user_id', '=', $user->id())
                ->where('gedcom_id', '=', $this->id)
                ->pluck('setting_value', 'setting_name')
                ->all();
        }

        return $this->user_preferences[$user->id()][$setting_name] ?? $default;
    }

    /**
     * The ID of this tree
     *
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Can a user accept changes for this tree?
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function canAcceptChanges(UserInterface $user): bool
    {
        return Auth::isModerator($this, $user);
    }

    /**
     * Are there any pending edits for this tree, that need reviewing by a moderator.
     *
     * @return bool
     */
    public function hasPendingEdit(): bool
    {
        return DB::table('change')
            ->where('gedcom_id', '=', $this->id)
            ->where('status', '=', 'pending')
            ->exists();
    }

    /**
     * Create a new record from GEDCOM data.
     *
     * @param string $gedcom
     *
     * @return GedcomRecord|Individual|Family|Location|Note|Source|Repository|Media|Submitter|Submission
     * @throws InvalidArgumentException
     */
    public function createRecord(string $gedcom): GedcomRecord
    {
        if (preg_match('/^0 @@ ([_A-Z]+)/', $gedcom, $match) !== 1) {
            throw new InvalidArgumentException('GedcomRecord::createRecord(' . $gedcom . ') does not begin 0 @@');
        }

        $xref   = Registry::xrefFactory()->make($match[1]);
        $gedcom = substr_replace($gedcom, $xref, 3, 0);

        // Create a change record
        $today = strtoupper(date('d M Y'));
        $now   = date('H:i:s');
        $gedcom .= "\n1 CHAN\n2 DATE " . $today . "\n3 TIME " . $now . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'status'     => 'pending',
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::gedcomRecordFactory()->new($xref, $gedcom, null, $this);

            $pending_changes_service = Registry::container()->get(PendingChangesService::class);
            $pending_changes_service->acceptRecord($record);

            return $record;
        }

        return Registry::gedcomRecordFactory()->new($xref, '', $gedcom, $this);
    }

    /**
     * Create a new family from GEDCOM data.
     *
     * @param string $gedcom
     *
     * @return Family
     * @throws InvalidArgumentException
     */
    public function createFamily(string $gedcom): GedcomRecord
    {
        if (!str_starts_with($gedcom, '0 @@ FAM')) {
            throw new InvalidArgumentException('GedcomRecord::createFamily(' . $gedcom . ') does not begin 0 @@ FAM');
        }

        $xref   = Registry::xrefFactory()->make(Family::RECORD_TYPE);
        $gedcom = substr_replace($gedcom, $xref, 3, 0);

        // Create a change record
        $today = strtoupper(date('d M Y'));
        $now   = date('H:i:s');
        $gedcom .= "\n1 CHAN\n2 DATE " . $today . "\n3 TIME " . $now . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'status'     => 'pending',
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::familyFactory()->new($xref, $gedcom, null, $this);

            $pending_changes_service = Registry::container()->get(PendingChangesService::class);
            $pending_changes_service->acceptRecord($record);

            return $record;
        }

        return Registry::familyFactory()->new($xref, '', $gedcom, $this);
    }

    /**
     * Create a new individual from GEDCOM data.
     *
     * @param string $gedcom
     *
     * @return Individual
     * @throws InvalidArgumentException
     */
    public function createIndividual(string $gedcom): GedcomRecord
    {
        if (!str_starts_with($gedcom, '0 @@ INDI')) {
            throw new InvalidArgumentException('GedcomRecord::createIndividual(' . $gedcom . ') does not begin 0 @@ INDI');
        }

        $xref   = Registry::xrefFactory()->make(Individual::RECORD_TYPE);
        $gedcom = substr_replace($gedcom, $xref, 3, 0);

        // Create a change record
        $today = strtoupper(date('d M Y'));
        $now   = date('H:i:s');
        $gedcom .= "\n1 CHAN\n2 DATE " . $today . "\n3 TIME " . $now . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'status'     => 'pending',
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::individualFactory()->new($xref, $gedcom, null, $this);

            $pending_changes_service = Registry::container()->get(PendingChangesService::class);
            $pending_changes_service->acceptRecord($record);

            return $record;
        }

        return Registry::individualFactory()->new($xref, '', $gedcom, $this);
    }

    /**
     * Create a new media object from GEDCOM data.
     *
     * @param string $gedcom
     *
     * @return Media
     * @throws InvalidArgumentException
     */
    public function createMediaObject(string $gedcom): Media
    {
        if (!str_starts_with($gedcom, '0 @@ OBJE')) {
            throw new InvalidArgumentException('GedcomRecord::createIndividual(' . $gedcom . ') does not begin 0 @@ OBJE');
        }

        $xref   = Registry::xrefFactory()->make(Media::RECORD_TYPE);
        $gedcom = substr_replace($gedcom, $xref, 3, 0);

        // Create a change record
        $today = strtoupper(date('d M Y'));
        $now   = date('H:i:s');
        $gedcom .= "\n1 CHAN\n2 DATE " . $today . "\n3 TIME " . $now . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'status'     => 'pending',
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::mediaFactory()->new($xref, $gedcom, null, $this);

            $pending_changes_service = Registry::container()->get(PendingChangesService::class);
            $pending_changes_service->acceptRecord($record);

            return $record;
        }

        return Registry::mediaFactory()->new($xref, '', $gedcom, $this);
    }

    /**
     * What is the most significant individual in this tree.
     *
     * @param UserInterface $user
     * @param string        $xref
     *
     * @return Individual
     */
    public function significantIndividual(UserInterface $user, string $xref = ''): Individual
    {
        if ($xref === '') {
            $individual = null;
        } else {
            $individual = Registry::individualFactory()->make($xref, $this);

            if ($individual === null) {
                $family = Registry::familyFactory()->make($xref, $this);

                if ($family instanceof Family) {
                    $individual = $family->spouses()->first() ?? $family->children()->first();
                }
            }
        }

        if ($individual === null && $this->getUserPreference($user, UserInterface::PREF_TREE_DEFAULT_XREF) !== '') {
            $individual = Registry::individualFactory()->make($this->getUserPreference($user, UserInterface::PREF_TREE_DEFAULT_XREF), $this);
        }

        if ($individual === null && $this->getUserPreference($user, UserInterface::PREF_TREE_ACCOUNT_XREF) !== '') {
            $individual = Registry::individualFactory()->make($this->getUserPreference($user, UserInterface::PREF_TREE_ACCOUNT_XREF), $this);
        }

        if ($individual === null && $this->getPreference('PEDIGREE_ROOT_ID') !== '') {
            $individual = Registry::individualFactory()->make($this->getPreference('PEDIGREE_ROOT_ID'), $this);
        }
        if ($individual === null) {
            $xref = DB::table('individuals')
                ->where('i_file', '=', $this->id())
                ->min('i_id');

            if (is_string($xref)) {
                $individual = Registry::individualFactory()->make($xref, $this);
            }
        }
        if ($individual === null) {
            // always return a record
            $individual = Registry::individualFactory()->new('I', '0 @I@ INDI', null, $this);
        }

        return $individual;
    }

    /**
     * Where do we store our media files.
     *
     * @return FilesystemOperator
     */
    public function mediaFilesystem(): FilesystemOperator
    {
        return Registry::filesystem()->data($this->getPreference('MEDIA_DIRECTORY'));
    }
}
