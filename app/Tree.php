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

use Exception;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use InvalidArgumentException;
use PDOException;
use function substr_compare;

/**
 * Provide an interface to the wt_gedcom table.
 */
class Tree
{
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

    /** @var Tree[] All trees that we have permission to see. */
    public static $trees = [];

    /** @var string[] Cached copy of the wt_gedcom_setting table. */
    private $preferences = [];

    /** @var string[][] Cached copy of the wt_user_gedcom_setting table. */
    private $user_preferences = [];

    private const RESN_PRIVACY = [
        'none'         => Auth::PRIV_PRIVATE,
        'privacy'      => Auth::PRIV_USER,
        'confidential' => Auth::PRIV_NONE,
        'hidden'       => Auth::PRIV_HIDE,
    ];

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
     * The ID of this tree
     *
     * @return int
     */
    public function id(): int
    {
        return $this->id;
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
                'gedcom_id' =>$this->id,
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
     * Get the tree’s user-configuration settings.
     *
     * @param User   $user
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getUserPreference(User $user, string $setting_name, string $default = ''): string
    {
        // There are lots of settings, and we need to fetch lots of them on every page
        // so it is quicker to fetch them all in one go.
        if (!array_key_exists($user->getUserId(), $this->user_preferences)) {
            $this->user_preferences[$user->getUserId()] = DB::table('user_gedcom_setting')
                ->where('user_id', '=', $user->getUserId())
                ->where('gedcom_id', '=', $this->id)
                ->pluck('setting_value', 'setting_name')
                ->all();
        }

        return $this->user_preferences[$user->getUserId()][$setting_name] ?? $default;
    }

    /**
     * Set the tree’s user-configuration settings.
     *
     * @param User   $user
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return $this
     */
    public function setUserPreference(User $user, string $setting_name, string $setting_value): Tree
    {
        if ($this->getUserPreference($user, $setting_name) !== $setting_value) {
            // Update the database
            DB::table('user_gedcom_setting')->updateOrInsert([
                'gedcom_id' => $this->id(),
                'user_id' =>$user->getUserId(),
                'setting_name' => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);

            // Update the cache
            $this->user_preferences[$user->getUserId()][$setting_name] = $setting_value;
            // Audit log of changes
            Log::addConfigurationLog('Tree preference "' . $setting_name . '" set to "' . $setting_value . '" for user "' . $user->getUserName() . '"', $this);
        }

        return $this;
    }

    /**
     * Can a user accept changes for this tree?
     *
     * @param User $user
     *
     * @return bool
     */
    public function canAcceptChanges(User $user): bool
    {
        return Auth::isModerator($this, $user);
    }

    /**
     * Fetch all the trees that we have permission to access.
     *
     * @return Tree[]
     */
    public static function getAll(): array
    {
        if (empty(self::$trees)) {
            // Admins see all trees
            $query = DB::table('gedcom')
                ->leftJoin('gedcom_setting', function (JoinClause $join): void {
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
                    ->join('gedcom_setting AS gs2', function (JoinClause $join): void {
                        $join->on('gs2.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('gs2.setting_name', '=', 'imported');
                    })
                    ->join('gedcom_setting AS gs3', function (JoinClause $join): void {
                        $join->on('gs3.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('gs3.setting_name', '=', 'REQUIRE_AUTHENTICATION');
                    })
                    ->leftJoin('user_gedcom_setting', function (JoinClause $join): void {
                        $join->on('user_gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                            ->where('user_gedcom_setting.user_id', '=', Auth::id())
                            ->where('user_gedcom_setting.setting_name', '=', 'canedit');
                    })
                    ->where(function (Builder $query): void {
                        $query
                            // Managers
                            ->where('user_gedcom_setting.setting_value', '=', 'admin')
                            // Members
                            ->orWhere(function (Builder $query): void {
                                $query
                                    ->where('gs2.setting_value', '=', '1')
                                    ->where('gs3.setting_value', '=', '1')
                                    ->where('user_gedcom_setting.setting_value', '<>', 'none');
                            })
                            // PUblic trees
                            ->orWhere(function (Builder $query): void {
                                $query
                                    ->where('gs2.setting_value', '=', '1')
                                    ->where('gs3.setting_value', '<>', '1');
                            });
                    });
            }

            $rows = $query->get();

            foreach ($rows as $row) {
                self::$trees[$row->tree_name] = new self((int) $row->tree_id, $row->tree_name, $row->tree_title);
            }
        }

        return self::$trees;
    }

    /**
     * Find the tree with a specific ID.
     *
     * @param int $tree_id
     *
     * @throws \DomainException
     * @return Tree
     */
    public static function findById($tree_id): Tree
    {
        foreach (self::getAll() as $tree) {
            if ($tree->id == $tree_id) {
                return $tree;
            }
        }
        throw new \DomainException();
    }

    /**
     * Find the tree with a specific name.
     *
     * @param string $tree_name
     *
     * @return Tree|null
     */
    public static function findByName($tree_name)
    {
        foreach (self::getAll() as $tree) {
            if ($tree->name === $tree_name) {
                return $tree;
            }
        }

        return null;
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

        // Update the list of trees - to include this new one
        self::$trees[$tree_name] = $tree;

        $tree->setPreference('imported', '0');
        $tree->setPreference('title', $tree_title);

        // Module privacy
        Module::setDefaultAccess($tree_id);

        // Set preferences from default tree
        (new Builder(DB::connection()))->from('gedcom_setting')->insertUsing(
            ['gedcom_id', 'setting_name', 'setting_value'],
            function (Builder $query) use ($tree_id): void {
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

        (new Builder(DB::connection()))->from('block')->insertUsing(
            ['gedcom_id', 'location', 'block_order', 'module_name'],
            function (Builder $query) use ($tree_id): void {
                $query
                    ->select([DB::raw($tree_id), 'location', 'block_order', 'module_name'])
                    ->from('block')
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
     * Are there any pending edits for this tree, than need reviewing by a moderator.
     *
     * @return bool
     */
    public function hasPendingEdit(): bool
    {
        return (bool) Database::prepare(
            "SELECT 1 FROM `##change` WHERE status = 'pending' AND gedcom_id = :tree_id"
        )->execute([
            'tree_id' => $this->id,
        ])->fetchOne();
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
    public function deleteGenealogyData(bool $keep_media)
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
                ->where('l_type', '<>',  'OBJE')
                ->delete();
        } else {
            DB::table('link')->where('l_file', '=', $this->id)->delete();
            DB::table('media_file')->where('m_file', '=', $this->id)->delete();
            DB::table('media')->where('m_file', '=', $this->id)->delete();
        }
    }

    /**
     * Delete everything relating to a tree
     *
     * @return void
     */
    public function delete()
    {
        // If this is the default tree, then unset it
        if (Site::getPreference('DEFAULT_GEDCOM') === $this->name) {
            Site::setPreference('DEFAULT_GEDCOM', '');
        }

        $this->deleteGenealogyData(false);

        Database::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE gedcom_id=?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##block`               WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##user_gedcom_setting` WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##gedcom_setting`      WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##module_privacy`      WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##hit_counter`         WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##default_resn`        WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##gedcom_chunk`        WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##log`                 WHERE gedcom_id = ?")->execute([$this->id]);
        Database::prepare("DELETE FROM `##gedcom`              WHERE gedcom_id = ?")->execute([$this->id]);

        // After updating the database, we need to fetch a new (sorted) copy
        self::$trees = [];
    }

    /**
     * Export the tree to a GEDCOM file
     *
     * @param resource $stream
     *
     * @return void
     */
    public function exportGedcom($stream)
    {
        $stmt = Database::prepare(
            "SELECT i_gedcom AS gedcom, i_id AS xref, 1 AS n FROM `##individuals` WHERE i_file = :tree_id_1" .
            " UNION ALL " .
            "SELECT f_gedcom AS gedcom, f_id AS xref, 2 AS n FROM `##families`    WHERE f_file = :tree_id_2" .
            " UNION ALL " .
            "SELECT s_gedcom AS gedcom, s_id AS xref, 3 AS n FROM `##sources`     WHERE s_file = :tree_id_3" .
            " UNION ALL " .
            "SELECT o_gedcom AS gedcom, o_id AS xref, 4 AS n FROM `##other`       WHERE o_file = :tree_id_4 AND o_type NOT IN ('HEAD', 'TRLR')" .
            " UNION ALL " .
            "SELECT m_gedcom AS gedcom, m_id AS xref, 5 AS n FROM `##media`       WHERE m_file = :tree_id_5" .
            " ORDER BY n, LENGTH(xref), xref"
        )->execute([
            'tree_id_1' => $this->id,
            'tree_id_2' => $this->id,
            'tree_id_3' => $this->id,
            'tree_id_4' => $this->id,
            'tree_id_5' => $this->id,
        ]);

        $buffer = FunctionsExport::reformatRecord(FunctionsExport::gedcomHeader($this, 'UTF-8'));
        while (($row = $stmt->fetch()) !== false) {
            $buffer .= FunctionsExport::reformatRecord($row->gedcom);
            if (strlen($buffer) > 65535) {
                fwrite($stream, $buffer);
                $buffer = '';
            }
        }
        fwrite($stream, $buffer . '0 TRLR' . Gedcom::EOL);
        $stmt->closeCursor();
    }

    /**
     * Import data from a gedcom file into this tree.
     *
     * @param string $path     The full path to the (possibly temporary) file.
     * @param string $filename The preferred filename, for export/download.
     *
     * @return void
     * @throws Exception
     */
    public function importGedcomFile(string $path, string $filename)
    {
        // Read the file in blocks of roughly 64K. Ensure that each block
        // contains complete gedcom records. This will ensure we don’t split
        // multi-byte characters, as well as simplifying the code to import
        // each block.

        $file_data = '';
        $fp        = fopen($path, 'rb');

        if ($fp === false) {
            throw new Exception('Cannot write file: ' . $path);
        }

        $this->deleteGenealogyData((bool) $this->getPreference('keep_media'));
        $this->setPreference('gedcom_filename', $filename);
        $this->setPreference('imported', '0');

        while (!feof($fp)) {
            $file_data .= fread($fp, 65536);
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

        fclose($fp);
    }

    /**
     * Generate a new XREF, unique across all family trees
     *
     * @return string
     */
    public function getNewXref(): string
    {
        $prefix = 'X';

        $increment = 1.0;
        do {
            // Use LAST_INSERT_ID(expr) to provide a transaction-safe sequence. See
            // http://dev.mysql.com/doc/refman/5.6/en/information-functions.html#function_last-insert-id
            $statement = Database::prepare(
                "UPDATE `##site_setting` SET setting_value = LAST_INSERT_ID(setting_value + :increment) WHERE setting_name = 'next_xref'"
            );
            $statement->execute([
                'increment' => (int) $increment,
            ]);

            if ($statement->rowCount() === 0) {
                $num = '1';
                Site::setPreference('next_xref', $num);
            } else {
                $num = (string) Database::prepare("SELECT LAST_INSERT_ID()")->fetchOne();
            }

            $xref = $prefix . $num;

            // Records may already exist with this sequence number.
            $already_used = Database::prepare(
                "SELECT" .
                " EXISTS (SELECT 1 FROM `##individuals` WHERE i_id = :i_id) OR" .
                " EXISTS (SELECT 1 FROM `##families` WHERE f_id = :f_id) OR" .
                " EXISTS (SELECT 1 FROM `##sources` WHERE s_id = :s_id) OR" .
                " EXISTS (SELECT 1 FROM `##media` WHERE m_id = :m_id) OR" .
                " EXISTS (SELECT 1 FROM `##other` WHERE o_id = :o_id) OR" .
                " EXISTS (SELECT 1 FROM `##change` WHERE xref = :xref)"
            )->execute([
                'i_id' => $xref,
                'f_id' => $xref,
                's_id' => $xref,
                'm_id' => $xref,
                'o_id' => $xref,
                'xref' => $xref,
            ])->fetchOne();

            // This exponential increment allows us to scan over large blocks of
            // existing data in a reasonable time.
            $increment *= 1.01;
        } while ($already_used !== '0');

        return $xref;
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
        if (substr_compare($gedcom, '0 @@', 0, 4) !== 0) {
            throw new InvalidArgumentException('GedcomRecord::createRecord(' . $gedcom . ') does not begin 0 @@');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@' . substr($gedcom, 4);

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();

        // Create a pending change
        Database::prepare(
            "INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, '', ?, ?)"
        )->execute([
            $this->id,
            $xref,
            $gedcom,
            Auth::id(),
        ]);

        // Accept this pending change
        if (Auth::user()->getPreference('auto_accept')) {
            FunctionsImport::acceptAllChanges($xref, $this);

            return new GedcomRecord($xref, $gedcom, null, $this);
        }

        return GedcomRecord::getInstance($xref, $this, $gedcom);
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
        if (substr_compare($gedcom, '0 @@ FAM', 0, 8) !== 0) {
            throw new InvalidArgumentException('GedcomRecord::createFamily(' . $gedcom . ') does not begin 0 @@ FAM');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@' . substr($gedcom, 4);

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();

        // Create a pending change
        Database::prepare(
            "INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, '', ?, ?)"
        )->execute([
            $this->id,
            $xref,
            $gedcom,
            Auth::id(),
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
        if (substr_compare($gedcom, '0 @@ INDI', 0, 9) !== 0) {
            throw new InvalidArgumentException('GedcomRecord::createIndividual(' . $gedcom . ') does not begin 0 @@ INDI');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@' . substr($gedcom, 4);

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();

        // Create a pending change
        Database::prepare(
            "INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, '', ?, ?)"
        )->execute([
            $this->id,
            $xref,
            $gedcom,
            Auth::id(),
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
        if (substr_compare($gedcom, '0 @@ OBJE', 0, 9) !== 0) {
            throw new InvalidArgumentException('GedcomRecord::createIndividual(' . $gedcom . ') does not begin 0 @@ OBJE');
        }

        $xref   = $this->getNewXref();
        $gedcom = '0 @' . $xref . '@' . substr($gedcom, 4);

        // Create a change record
        $gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();

        // Create a pending change
        Database::prepare(
            "INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, '', ?, ?)"
        )->execute([
            $this->id,
            $xref,
            $gedcom,
            Auth::id(),
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
     * @param User $user
     *
     * @return Individual
     */
    public function significantIndividual(User $user): Individual
    {
        static $individual; // Only query the DB once.

        if (!$individual && $this->getUserPreference($user, 'rootid') !== '') {
            $individual = Individual::getInstance($this->getUserPreference($user, 'rootid'), $this);
        }
        if (!$individual && $this->getUserPreference($user, 'gedcomid') !== '') {
            $individual = Individual::getInstance($this->getUserPreference($user, 'gedcomid'), $this);
        }
        if (!$individual) {
            $individual = Individual::getInstance($this->getPreference('PEDIGREE_ROOT_ID'), $this);
        }
        if (!$individual) {
            $xref = (string) Database::prepare(
                "SELECT MIN(i_id) FROM `##individuals` WHERE i_file = :tree_id"
            )->execute([
                'tree_id' => $this->id(),
            ])->fetchOne();

            $individual = Individual::getInstance($xref, $this);
        }
        if (!$individual) {
            // always return a record
            $individual = new Individual('I', '0 @I@ INDI', null, $this);
        }

        return $individual;
    }

    /**
     * Get significant information from this page, to allow other pages such as
     * charts and reports to initialise with the same records
     *
     * @return Individual
     */
    public function getSignificantIndividual(): Individual
    {
        static $individual; // Only query the DB once.

        if (!$individual && $this->getUserPreference(Auth::user(), 'rootid') !== '') {
            $individual = Individual::getInstance($this->getUserPreference(Auth::user(), 'rootid'), $this);
        }
        if (!$individual && $this->getUserPreference(Auth::user(), 'gedcomid') !== '') {
            $individual = Individual::getInstance($this->getUserPreference(Auth::user(), 'gedcomid'), $this);
        }
        if (!$individual) {
            $individual = Individual::getInstance($this->getPreference('PEDIGREE_ROOT_ID'), $this);
        }
        if (!$individual) {
            $xref = (string) Database::prepare(
                "SELECT MIN(i_id) FROM `##individuals` WHERE i_file = :tree_id"
            )->execute([
                'tree_id' => $this->id(),
            ])->fetchOne();

            $individual = Individual::getInstance($xref, $this);
        }
        if (!$individual) {
            // always return a record
            $individual = new Individual('I', '0 @I@ INDI', null, $this);
        }

        return $individual;
    }
}
