<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\StreamInterface;
use stdClass;

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
    /** @var Tree[] All trees that we have permission to see, indexed by ID. */
    public static $trees = [];
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
     * Create a tree object. This is a private constructor - it can only
     * be called from Tree::getAll() to ensure proper initialisation.
     *
     * @param int    $id
     * @param string $name
     * @param string $title
     */
    private function __construct($id, $name, $title)
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
                    $this->individual_fact_privacy[$row->xref][$row->tag_type] = (int) $row->resn;
                } else {
                    $this->individual_privacy[$row->xref] = (int) $row->resn;
                }
            } else {
                $this->fact_privacy[$row->tag_type] = (int) $row->resn;
            }
        }
    }

    /**
     * Find the tree with a specific ID.
     *
     * @param int $tree_id
     *
     * @return Tree
     */
    public static function findById(int $tree_id): Tree
    {
        return self::getAll()[$tree_id];
    }

    /**
     * Fetch all the trees that we have permission to access.
     *
     * @return Tree[]
     */
    public static function getAll(): array
    {
        if (empty(self::$trees)) {
            self::$trees = self::all()->all();
        }

        return self::$trees;
    }

    /**
     * All the trees that we have permission to access.
     *
     * @return Collection
     * @return Tree[]
     */
    public static function all(): Collection
    {
        return app('cache.array')->rememberForever(__CLASS__, static function (): Collection {
            // Admins see all trees
            $query = DB::table('gedcom')
                ->leftJoin('gedcom_setting', static function (JoinClause $join): void {
                    $join->on('gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                        ->where('gedcom_setting.setting_name', '=', 'title');
                })
                ->where('gedcom.gedcom_id', '>', 0)
                ->select([
                    'gedcom.gedcom_id AS tree_id',
                    'gedcom.gedcom_name AS tree_name',
                    'gedcom_setting.setting_value AS tree_title',
                ])
                ->orderBy('gedcom.sort_order')
                ->orderBy('gedcom_setting.setting_value');

            // Non-admins may not see all trees
            if (!Auth::isAdmin()) {
                $query
                    ->join('gedcom_setting AS gs2', static function (JoinClause $join): void {
                        $join->on('gs2.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('gs2.setting_name', '=', 'imported');
                    })
                    ->join('gedcom_setting AS gs3', static function (JoinClause $join): void {
                        $join->on('gs3.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('gs3.setting_name', '=', 'REQUIRE_AUTHENTICATION');
                    })
                    ->leftJoin('user_gedcom_setting', static function (JoinClause $join): void {
                        $join->on('user_gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('user_gedcom_setting.user_id', '=', Auth::id())
                            ->where('user_gedcom_setting.setting_name', '=', 'canedit');
                    })
                    ->where(static function (Builder $query): void {
                        $query
                            // Managers
                            ->where('user_gedcom_setting.setting_value', '=', 'admin')
                            // Members
                            ->orWhere(static function (Builder $query): void {
                                $query
                                    ->where('gs2.setting_value', '=', '1')
                                    ->where('gs3.setting_value', '=', '1')
                                    ->where('user_gedcom_setting.setting_value', '<>', 'none');
                            })
                            // Public trees
                            ->orWhere(static function (Builder $query): void {
                                $query
                                    ->where('gs2.setting_value', '=', '1')
                                    ->where('gs3.setting_value', '<>', '1');
                            });
                    });
            }

            return $query
                ->get()
                ->mapWithKeys(static function (stdClass $row): array {
                    return [$row->tree_id => new self((int) $row->tree_id, $row->tree_name, $row->tree_title)];
                });
        });
    }

    /**
     * Create arguments to select_edit_control()
     * Note - these will be escaped later
     *
     * @return string[]
     */
    public static function getIdList(): array
    {
        $list = [];
        foreach (self::getAll() as $tree) {
            $list[$tree->id] = $tree->title;
        }

        return $list;
    }

    /**
     * Create arguments to select_edit_control()
     * Note - these will be escaped later
     *
     * @return string[]
     */
    public static function getNameList(): array
    {
        $list = [];
        foreach (self::getAll() as $tree) {
            $list[$tree->name] = $tree->title;
        }

        return $list;
    }

    /**
     * Create a new tree
     *
     * @param string $tree_name
     * @param string $tree_title
     *
     * @return Tree
     */
    public static function create(string $tree_name, string $tree_title): Tree
    {
        try {
            // Create a new tree
            DB::table('gedcom')->insert([
                'gedcom_name' => $tree_name,
            ]);

            $tree_id = (int) DB::connection()->getPdo()->lastInsertId();

            $tree = new self($tree_id, $tree_name, $tree_title);
        } catch (PDOException $ex) {
            // A tree with that name already exists?
            return self::findByName($tree_name);
        }

        $tree->setPreference('imported', '0');
        $tree->setPreference('title', $tree_title);

        // Set preferences from default tree
        (new Builder(DB::connection()))->from('gedcom_setting')->insertUsing(
            ['gedcom_id', 'setting_name', 'setting_value'],
            static function (Builder $query) use ($tree_id): void {
                $query
                    ->select([DB::raw($tree_id), 'setting_name', 'setting_value'])
                    ->from('gedcom_setting')
                    ->where('gedcom_id', '=', -1);
            }
        );

        (new Builder(DB::connection()))->from('default_resn')->insertUsing(
            ['gedcom_id', 'tag_type', 'resn'],
            function (Builder $query) use ($tree_id): void {
                $query
                    ->select([DB::raw($tree_id), 'tag_type', 'resn'])
                    ->from('default_resn')
                    ->where('gedcom_id', '=', -1);
            }
        );

        // Gedcom and privacy settings
        $tree->setPreference('CONTACT_USER_ID', (string) Auth::id());
        $tree->setPreference('WEBMASTER_USER_ID', (string) Auth::id());
        $tree->setPreference('LANGUAGE', WT_LOCALE); // Default to the current admin’s language

        switch (WT_LOCALE) {
            case 'es':
                $tree->setPreference('SURNAME_TRADITION', 'spanish');
                break;
            case 'is':
                $tree->setPreference('SURNAME_TRADITION', 'icelandic');
                break;
            case 'lt':
                $tree->setPreference('SURNAME_TRADITION', 'lithuanian');
                break;
            case 'pl':
                $tree->setPreference('SURNAME_TRADITION', 'polish');
                break;
            case 'pt':
            case 'pt-BR':
                $tree->setPreference('SURNAME_TRADITION', 'portuguese');
                break;
            default:
                $tree->setPreference('SURNAME_TRADITION', 'paternal');
                break;
        }

        // Genealogy data
        // It is simpler to create a temporary/unimported GEDCOM than to populate all the tables...
        /* I18N: This should be a common/default/placeholder name of an individual. Put slashes around the surname. */
        $john_doe = I18N::translate('John /DOE/');
        $note     = I18N::translate('Edit this individual and replace their details with your own.');
        $gedcom   = "0 HEAD\n1 CHAR UTF-8\n0 @X1@ INDI\n1 NAME {$john_doe}\n1 SEX M\n1 BIRT\n2 DATE 01 JAN 1850\n2 NOTE {$note}\n0 TRLR\n";

        DB::table('gedcom_chunk')->insert([
            'gedcom_id'  => $tree_id,
            'chunk_data' => $gedcom,
        ]);

        // Update our cache
        self::$trees[$tree->id] = $tree;

        return $tree;
    }

    /**
     * Find the tree with a specific name.
     *
     * @param string $tree_name
     *
     * @return Tree|null
     */
    public static function findByName($tree_name): ?Tree
    {
        foreach (self::getAll() as $tree) {
            if ($tree->name === $tree_name) {
                return $tree;
            }
        }

        return null;
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
        if (empty($this->preferences)) {
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
     */
    public function delete(): void
    {
        // If this is the default tree, then unset it
        if (Site::getPreference('DEFAULT_GEDCOM') === $this->name) {
            Site::setPreference('DEFAULT_GEDCOM', '');
        }

        $this->deleteGenealogyData(false);

        DB::table('block_setting')
            ->join('block', 'block.block_id', '=', 'block_setting.block_id')
            ->where('gedcom_id', '=', $this->id)
            ->delete();
        DB::table('block')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('user_gedcom_setting')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('gedcom_setting')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('module_privacy')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('hit_counter')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('default_resn')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('gedcom_chunk')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('log')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('gedcom')->where('gedcom_id', '=', $this->id)->delete();

        // After updating the database, we need to fetch a new (sorted) copy
        self::$trees = [];
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
     */
    public function deleteGenealogyData(bool $keep_media): void
    {
        DB::table('gedcom_chunk')->where('gedcom_id', '=', $this->id)->delete();
        DB::table('individuals')->where('i_file', '=', $this->id)->delete();
        DB::table('families')->where('f_file', '=', $this->id)->delete();
        DB::table('sources')->where('s_file', '=', $this->id)->delete();
        DB::table('other')->where('o_file', '=', $this->id)->delete();
        DB::table('places')->where('p_file', '=', $this->id)->delete();
        DB::table('placelinks')->where('pl_file', '=', $this->id)->delete();
        DB::table('name')->where('n_file', '=', $this->id)->delete();
        DB::table('dates')->where('d_file', '=', $this->id)->delete();
        DB::table('change')->where('gedcom_id', '=', $this->id)->delete();

        if ($keep_media) {
            DB::table('link')->where('l_file', '=', $this->id)
                ->where('l_type', '<>', 'OBJE')
                ->delete();
        } else {
            DB::table('link')->where('l_file', '=', $this->id)->delete();
            DB::table('media_file')->where('m_file', '=', $this->id)->delete();
            DB::table('media')->where('m_file', '=', $this->id)->delete();
        }
    }

    /**
     * Export the tree to a GEDCOM file
     *
     * @param resource $stream
     *
     * @return void
     */
    public function exportGedcom($stream): void
    {
        $buffer = FunctionsExport::reformatRecord(FunctionsExport::gedcomHeader($this, 'UTF-8'));

        $union_families = DB::table('families')
            ->where('f_file', '=', $this->id)
            ->select(['f_gedcom AS gedcom', 'f_id AS xref', DB::raw('LENGTH(f_id) AS len'), DB::raw('2 AS n')]);

        $union_sources = DB::table('sources')
            ->where('s_file', '=', $this->id)
            ->select(['s_gedcom AS gedcom', 's_id AS xref', DB::raw('LENGTH(s_id) AS len'), DB::raw('3 AS n')]);

        $union_other = DB::table('other')
            ->where('o_file', '=', $this->id)
            ->whereNotIn('o_type', ['HEAD', 'TRLR'])
            ->select(['o_gedcom AS gedcom', 'o_id AS xref', DB::raw('LENGTH(o_id) AS len'), DB::raw('4 AS n')]);

        $union_media = DB::table('media')
            ->where('m_file', '=', $this->id)
            ->select(['m_gedcom AS gedcom', 'm_id AS xref', DB::raw('LENGTH(m_id) AS len'), DB::raw('5 AS n')]);

        DB::table('individuals')
            ->where('i_file', '=', $this->id)
            ->select(['i_gedcom AS gedcom', 'i_id AS xref', DB::raw('LENGTH(i_id) AS len'), DB::raw('1 AS n')])
            ->union($union_families)
            ->union($union_sources)
            ->union($union_other)
            ->union($union_media)
            ->orderBy('n')
            ->orderBy('len')
            ->orderBy('xref')
            ->chunk(100, static function (Collection $rows) use ($stream, &$buffer): void {
                foreach ($rows as $row) {
                    $buffer .= FunctionsExport::reformatRecord($row->gedcom);
                    if (strlen($buffer) > 65535) {
                        fwrite($stream, $buffer);
                        $buffer = '';
                    }
                }
            });

        fwrite($stream, $buffer . '0 TRLR' . Gedcom::EOL);
    }

    /**
     * Import data from a gedcom file into this tree.
     *
     * @param StreamInterface $stream   The GEDCOM file.
     * @param string          $filename The preferred filename, for export/download.
     *
     * @return void
     */
    public function importGedcomFile(StreamInterface $stream, string $filename): void
    {
        // Read the file in blocks of roughly 64K. Ensure that each block
        // contains complete gedcom records. This will ensure we don’t split
        // multi-byte characters, as well as simplifying the code to import
        // each block.

        $file_data = '';

        $this->deleteGenealogyData((bool) $this->getPreference('keep_media'));
        $this->setPreference('gedcom_filename', $filename);
        $this->setPreference('imported', '0');

        while (!$stream->eof()) {
            $file_data .= $stream->read(65536);
            // There is no strrpos() function that searches for substrings :-(
            for ($pos = strlen($file_data) - 1; $pos > 0; --$pos) {
                if ($file_data[$pos] === '0' && ($file_data[$pos - 1] === "\n" || $file_data[$pos - 1] === "\r")) {
                    // We’ve found the last record boundary in this chunk of data
                    break;
                }
            }
            if ($pos) {
                DB::table('gedcom_chunk')->insert([
                    'gedcom_id'  => $this->id,
                    'chunk_data' => substr($file_data, 0, $pos),
                ]);

                $file_data = substr($file_data, $pos);
            }
        }
        DB::table('gedcom_chunk')->insert([
            'gedcom_id'  => $this->id,
            'chunk_data' => $file_data,
        ]);

        $stream->close();
    }

    /**
     * Create a new record from GEDCOM data.
     *
     * @param string $gedcom
     *
     * @return GedcomRecord|Individual|Family|Note|Source|Repository|Media
     * @throws InvalidArgumentException
     */
    public function createRecord(string $gedcom): GedcomRecord
    {
        if (!Str::startsWith($gedcom, '0 @@ ')) {
            throw new InvalidArgumentException('GedcomRecord::createRecord(' . $gedcom . ') does not begin 0 @@');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@ ' . Str::after($gedcom, '0 @@ ');

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference('auto_accept')) {
            FunctionsImport::acceptAllChanges($xref, $this);

            return new GedcomRecord($xref, $gedcom, null, $this);
        }

        return GedcomRecord::getInstance($xref, $this, $gedcom);
    }

    /**
     * Generate a new XREF, unique across all family trees
     *
     * @return string
     */
    public function getNewXref(): string
    {
        // Lock the row, so that only one new XREF may be generated at a time.
        DB::table('site_setting')
            ->where('setting_name', '=', 'next_xref')
            ->lockForUpdate()
            ->get();

        $prefix = 'X';

        $increment = 1.0;
        do {
            $num = (int) Site::getPreference('next_xref') + (int) $increment;

            // This exponential increment allows us to scan over large blocks of
            // existing data in a reasonable time.
            $increment *= 1.01;

            $xref = $prefix . $num;

            // Records may already exist with this sequence number.
            $already_used =
                DB::table('individuals')->where('i_id', '=', $xref)->exists() ||
                DB::table('families')->where('f_id', '=', $xref)->exists() ||
                DB::table('sources')->where('s_id', '=', $xref)->exists() ||
                DB::table('media')->where('m_id', '=', $xref)->exists() ||
                DB::table('other')->where('o_id', '=', $xref)->exists() ||
                DB::table('change')->where('xref', '=', $xref)->exists();
        } while ($already_used);

        Site::setPreference('next_xref', (string) $num);

        return $xref;
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
        if (!Str::startsWith($gedcom, '0 @@ FAM')) {
            throw new InvalidArgumentException('GedcomRecord::createFamily(' . $gedcom . ') does not begin 0 @@ FAM');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@ FAM' . Str::after($gedcom, '0 @@ FAM');

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference('auto_accept')) {
            FunctionsImport::acceptAllChanges($xref, $this);

            return new Family($xref, $gedcom, null, $this);
        }

        return new Family($xref, '', $gedcom, $this);
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
        if (!Str::startsWith($gedcom, '0 @@ INDI')) {
            throw new InvalidArgumentException('GedcomRecord::createIndividual(' . $gedcom . ') does not begin 0 @@ INDI');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@ INDI' . Str::after($gedcom, '0 @@ INDI');

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference('auto_accept')) {
            FunctionsImport::acceptAllChanges($xref, $this);

            return new Individual($xref, $gedcom, null, $this);
        }

        return new Individual($xref, '', $gedcom, $this);
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
        if (!Str::startsWith($gedcom, '0 @@ OBJE')) {
            throw new InvalidArgumentException('GedcomRecord::createIndividual(' . $gedcom . ') does not begin 0 @@ OBJE');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@ OBJE' . Str::after($gedcom, '0 @@ OBJE');

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->userName();

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->id,
            'xref'       => $xref,
            'old_gedcom' => '',
            'new_gedcom' => $gedcom,
            'user_id'    => Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference('auto_accept')) {
            FunctionsImport::acceptAllChanges($xref, $this);

            return new Media($xref, $gedcom, null, $this);
        }

        return new Media($xref, '', $gedcom, $this);
    }

    /**
     * What is the most significant individual in this tree.
     *
     * @param UserInterface $user
     *
     * @return Individual
     */
    public function significantIndividual(UserInterface $user): Individual
    {
        $individual = null;

        if ($this->getUserPreference($user, 'rootid') !== '') {
            $individual = Individual::getInstance($this->getUserPreference($user, 'rootid'), $this);
        }

        if ($individual === null && $this->getUserPreference($user, 'gedcomid') !== '') {
            $individual = Individual::getInstance($this->getUserPreference($user, 'gedcomid'), $this);
        }

        if ($individual === null && $this->getPreference('PEDIGREE_ROOT_ID') !== '') {
            $individual = Individual::getInstance($this->getPreference('PEDIGREE_ROOT_ID'), $this);
        }
        if ($individual === null) {
            $xref = (string) DB::table('individuals')
                ->where('i_file', '=', $this->id())
                ->min('i_id');

            $individual = Individual::getInstance($xref, $this);
        }
        if ($individual === null) {
            // always return a record
            $individual = new Individual('I', '0 @I@ INDI', null, $this);
        }

        return $individual;
    }
}
