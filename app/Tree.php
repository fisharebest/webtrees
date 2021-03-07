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

namespace Fisharebest\Webtrees;

use Closure;
use Fisharebest\Flysystem\Adapter\ChrootAdapter;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\TreeService;
use Illuminate\Database\Capsule\Manager as DB;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;

use function app;
use function array_key_exists;
use function date;
use function str_starts_with;
use function strlen;
use function strtoupper;
use function substr;
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

    /** @var int The tree's ID number */
    private $id;

    /** @var string The tree's name */
    private $name;

    /** @var string The tree's title */
    private $title;

    /** @var int[] Default access rules for facts in this tree */
    private $fact_privacy;

    /** @var int[] Default access rules for individuals in this tree */
    private $individual_privacy;

    /** @var integer[][] Default access rules for individual facts in this tree */
    private $individual_fact_privacy;

    /** @var string[] Cached copy of the wt_gedcom_setting table. */
    private $preferences = [];

    /** @var string[][] Cached copy of the wt_user_gedcom_setting table. */
    private $user_preferences = [];

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
     * @return Closure
     */
    public static function rowMapper(): Closure
    {
        return static function (stdClass $row): Tree {
            return new Tree((int) $row->tree_id, $row->tree_name, $row->tree_title);
        };
    }

    /**
     * Set the tree’s configuration settings.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return $this
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
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string
    {
        if ($this->preferences === []) {
            $this->preferences = DB::table('gedcom_setting')
                ->where('gedcom_id', '=', $this->id)
                ->pluck('setting_value', 'setting_name')
                ->all();
        }

        return $this->preferences[$setting_name] ?? $default;
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
     * @return int[]
     */
    public function getFactPrivacy(): array
    {
        return $this->fact_privacy;
    }

    /**
     * The individual-level privacy for this tree.
     *
     * @return int[]
     */
    public function getIndividualPrivacy(): array
    {
        return $this->individual_privacy;
    }

    /**
     * The individual-fact-level privacy for this tree.
     *
     * @return int[][]
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
     * @return $this
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
     * Are there any pending edits for this tree, than need reviewing by a moderator.
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
     * Delete everything relating to a tree
     *
     * @return void
     *
     * @deprecated - since 2.0.12 - will be removed in 2.1.0
     */
    public function delete(): void
    {
        $tree_service = new TreeService();

        $tree_service->delete($this);
    }

    /**
     * Delete all the genealogy data from a tree - in preparation for importing
     * new data. Optionally retain the media data, for when the user has been
     * editing their data offline using an application which deletes (or does not
     * support) media data.
     *
     * @param bool $keep_media
     *
     * @return void
     *
     * @deprecated - since 2.0.12 - will be removed in 2.1.0
     */
    public function deleteGenealogyData(bool $keep_media): void
    {
        $tree_service = new TreeService();

        $tree_service->deleteGenealogyData($this, $keep_media);
    }

    /**
     * Export the tree to a GEDCOM file
     *
     * @param resource $stream
     *
     * @return void
     *
     * @deprecated since 2.0.5.  Will be removed in 2.1.0
     */
    public function exportGedcom($stream): void
    {
        $gedcom_export_service = new GedcomExportService();

        $gedcom_export_service->export($this, $stream);
    }

    /**
     * Import data from a gedcom file into this tree.
     *
     * @param StreamInterface $stream   The GEDCOM file.
     * @param string          $filename The preferred filename, for export/download.
     *
     * @return void
     *
     * @deprecated since 2.0.12.  Will be removed in 2.1.0
     */
    public function importGedcomFile(StreamInterface $stream, string $filename): void
    {
        $tree_service = new TreeService();

        $tree_service->importGedcomFile($this, $stream, $filename);
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
        if (!preg_match('/^0 @@ ([_A-Z]+)/', $gedcom, $match)) {
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
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::gedcomRecordFactory()->new($xref, $gedcom, null, $this);

            app(PendingChangesService::class)->acceptRecord($record);

            return $record;
        }

        return Registry::gedcomRecordFactory()->new($xref, '', $gedcom, $this);
    }

    /**
     * Generate a new XREF, unique across all family trees
     *
     * @return string
     * @deprecated - use the factory directly.
     */
    public function getNewXref(): string
    {
        return Registry::xrefFactory()->make(GedcomRecord::RECORD_TYPE);
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
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::familyFactory()->new($xref, $gedcom, null, $this);

            app(PendingChangesService::class)->acceptRecord($record);

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
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::individualFactory()->new($xref, $gedcom, null, $this);

            app(PendingChangesService::class)->acceptRecord($record);

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
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record = Registry::mediaFactory()->new($xref, $gedcom, null, $this);

            app(PendingChangesService::class)->acceptRecord($record);

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
    public function significantIndividual(UserInterface $user, $xref = ''): Individual
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
            $xref = (string) DB::table('individuals')
                ->where('i_file', '=', $this->id())
                ->min('i_id');

            $individual = Registry::individualFactory()->make($xref, $this);
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
     * @param FilesystemInterface $data_filesystem
     *
     * @return FilesystemInterface
     */
    public function mediaFilesystem(FilesystemInterface $data_filesystem): FilesystemInterface
    {
        $media_dir = $this->getPreference('MEDIA_DIRECTORY', 'media/');
        $adapter   = new ChrootAdapter($data_filesystem, $media_dir);

        return new Filesystem($adapter);
    }
}
