<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use PDOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Controller for importing from PhpGedView.
 *
 * This process needs to be as tolerant as possible of invalid or missing data,
 * to allow a PGV user to recover their genealogy data.
 */
class AdminPhpGedViewController extends AbstractBaseController
{
    // Icons for success and failure
    const SUCCESS = '<i class="fas fa-check" style="color:green"></i>';
    const FAILURE = '<i class="fas fa-times" style="color:red"></i>';

    // PhpGedView language codes and the equivalent in webtrees.
    const PGV_LANGUAGES = [
        'arabic'     => 'ar',
        'catalan'    => 'ca',
        'chinese'    => 'zh-Hans',
        'croatian'   => 'hr',
        'danish'     => 'da',
        'dutch'      => 'nl',
        'english'    => 'en-US',
        'english-uk' => 'en-GB',
        'estonian'   => 'et',
        'french'     => 'fr',
        'finnish'    => 'fi',
        'german'     => 'de',
        'greek'      => 'el',
        'hebrew'     => 'he',
        'hungarian'  => 'hu',
        'indonesian' => 'id',
        'italian'    => 'it',
        'lithuanian' => 'lt',
        'norwegian'  => 'nn',
        'polish'     => 'pl',
        'portuguese' => 'pt',
        'romanian'   => 'ro',
        'russian'    => 'ru',
        'serbian-la' => 'sr@Latn',
        'slovak'     => 'sk',
        'slovenian'  => 'sl',
        'spanish'    => 'es',
        'spanish-ar' => 'es',
        'swedish'    => 'sv',
        'turkish'    => 'tr',
        'vietnamese' => 'vi',
    ];

    // PhpGedView themes and the equivalent in webtrees.
    const PGV_THEMES = [
        ''                    => '',
        'themes/cloudy/'      => 'clouds',
        'themes/minimal/'     => 'minimal',
        'themes/simplyblue/'  => 'colors',
        'themes/simplygreen/' => 'colors',
        'themes/simplyred/'   => 'colors',
        'themes/xenea/'       => 'xenea',
    ];

    /** @var string */
    protected $layout = 'layouts/administration';

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function wizard(Request $request): Response
    {
        $pgv_path = $request->get('pgv_path', '');

        $title = I18N::translate('PhpGedView to webtrees transfer wizard');

        if (file_exists($pgv_path . 'config.php')) {
            return $this->viewResponse('admin/phpgedview/steps', [
                'steps' => $this->wizardSteps($pgv_path),
                'title' => $title,
            ]);
        }

        return $this->viewResponse('admin/phpgedview/wizard', [
            'pgv_paths' => $this->defaultPhpGedViewPaths(),
            'title'     => $title,
        ]);
    }

    /**
     * Perform one step of the wizard
     *
     * @param Request $request
     *
     * @return Response
     */
    public function step(Request $request): Response
    {
        $step     = $request->get('step');
        $pgv_path = $request->get('pgv_path');

        $config = $this->readPhpGedViewConfig($pgv_path);

        switch ($step) {
            case 'Check':
                return $this->wizardStepCheck($config);
            case 'Delete':
                return $this->wizardStepDelete();
            case 'GeoData':
                return $this->wizardStepGeographicData($config);
            case 'Site':
                return $this->wizardStepSitePreferences($config);
            case 'Trees':
                return $this->wizardStepTrees($config);
            case 'Users':
                return $this->wizardStepUsers($config);
            default:
                throw new NotFoundHttpException();
        }
    }

    /**
     * @param string $pgv_path
     *
     * @return string[]
     */
    private function wizardSteps(string $pgv_path): array
    {
        return [
            route('phpgedview-wizard', [
                'pgv_path' => $pgv_path,
                'step'     => 'Check',
            ]) => 'config.php',
            route('phpgedview-wizard', [
                'pgv_path' => $pgv_path,
                'step'     => 'Delete',
            ]) => I18N::translate('Delete'),
            route('phpgedview-wizard', [
                'pgv_path' => $pgv_path,
                'step'     => 'GeoData',
            ]) => I18N::translate('Geographic data'),
            route('phpgedview-wizard', [
                'pgv_path' => $pgv_path,
                'step'     => 'Site',
            ]) => I18N::translate('Site preferences'),
            route('phpgedview-wizard', [
                'pgv_path' => $pgv_path,
                'step'     => 'Trees',
            ]) => I18N::translate('Family trees'),
            route('phpgedview-wizard', [
                'pgv_path' => $pgv_path,
                'step'     => 'Users',
            ]) => I18N::translate('Users'),
        ];
    }

    /**
     * @param mixed[] $config
     *
     * @return Response
     */
    private function wizardStepCheck(array $config): Response
    {
        $pgv_path = $config['pgv_path'];

        if (!is_dir($pgv_path) || !is_readable($pgv_path . '/config.php')) {
            return new Response(I18N::translate('The specified folder does not contain an installation of PhpGedView.'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $INDEX_DIRECTORY = $config['INDEX_DIRECTORY'];

        if (!is_dir($INDEX_DIRECTORY)) {
            return new Response(I18N::translate('%1$s does not exist', e($INDEX_DIRECTORY)), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $DBHOST    = $config['DBHOST'] ?? '';
        $DBNAME    = $config['DBNAME'] ?? '';
        $TBLPREFIX = $config['TBLPREFIX'] ?? '';

        // Read the webtrees DB config
        $wt_config = parse_ini_file(WT_ROOT . 'data/config.ini.php');

        if ($DBHOST != $wt_config['dbhost']) {
            return new Response(I18N::translate('PhpGedView must use the same database as webtrees.'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $PGV_SCHEMA_VERSION = Database::prepare(
                "SELECT site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting` WHERE site_setting_name='PGV_SCHEMA_VERSION'"
            )->fetchOne();
            if ($PGV_SCHEMA_VERSION < 10) {
                return new Response(I18N::translate('The version of %s is too old.', 'PhpGedView'), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($PGV_SCHEMA_VERSION > 14) {
                return new Response(I18N::translate('The version of %s is too new.', 'PhpGedView'), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $ex) {
            DebugBar::addThrowable($ex);

            $content = I18N::translate('webtrees cannot connect to the PhpGedView database: %s.', $DBNAME . '@' . $DBHOST) .
                '<br>' .
                I18N::translate('MySQL gave the error: %s', $ex->getMessage());

            return new Response($content, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(self::SUCCESS);
    }

    /**
     * @return Response
     */
    private function wizardStepDelete(): Response
    {
        Database::exec("DELETE FROM `##block_setting`");
        Database::exec("DELETE FROM `##block` WHERE user_id > 0 OR gedcom_id > 0");
        Database::exec("DELETE FROM `##change`");
        Database::exec("DELETE FROM `##message`");
        Database::exec("DELETE FROM `##user_gedcom_setting` WHERE user_id > 0");

        return new Response(self::SUCCESS);
    }

    /**
     * Copy the geographic data from the maps module (if it is installed).
     *
     * @param mixed[] $config
     *
     * @return Response
     */
    private function wizardStepGeographicData(array $config): Response
    {
        $DBNAME    = $config['DBNAME'];
        $TBLPREFIX = $config['TBLPREFIX'];

        try {
            Database::prepare(
                "REPLACE INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon)" .
                " SELECT pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM `{$DBNAME}`.`{$TBLPREFIX}placelocation`"
            )->execute();
        } catch (PDOException $ex) {
            DebugBar::addThrowable($ex);

            // This table will only exist if the gm module is installed in PhpGedView/WT
        }

        return new Response(self::SUCCESS);
    }

    /**
     * @param mixed[] $config
     *
     * @return Response
     */
    private function wizardStepSitePreferences(array $config): Response
    {
        Site::setPreference('USE_REGISTRATION_MODULE', $config['USE_REGISTRATION_MODULE'] ?? '');
        Site::setPreference('ALLOW_USER_THEMES', $config['ALLOW_USER_THEMES'] ?? '');
        Site::setPreference('ALLOW_CHANGE_GEDCOM', $config['ALLOW_CHANGE_GEDCOM'] ?? '');
        Site::setPreference('SMTP_ACTIVE', ($config['PGV_SMTP_ACTIVE'] ?? '') ? 'external' : 'internal');
        Site::setPreference('SMTP_HOST', $config['PGV_SMTP_HOST'] ?? '');
        Site::setPreference('SMTP_HELO', $config['PGV_SMTP_HELO'] ?? '');
        Site::setPreference('SMTP_PORT', $config['PGV_SMTP_PORT'] ?? '');
        Site::setPreference('SMTP_AUTH', $config['PGV_SMTP_AUTH'] ?? '');
        Site::setPreference('SMTP_AUTH_USER', $config['PGV_SMTP_AUTH_USER'] ?? '');
        Site::setPreference('SMTP_AUTH_PASS', $config['PGV_SMTP_AUTH_PASS'] ?? '');
        Site::setPreference('SMTP_SSL', $config['PGV_SMTP_SSL'] ?? '');
        Site::setPreference('SMTP_FROM_NAME', $config['PGV_SMTP_FROM_NAME'] ?? '');

        $DBNAME    = $config['DBNAME'];
        $TBLPREFIX = $config['TBLPREFIX'];

        Database::prepare(
            "REPLACE INTO `##site_setting` (setting_name, setting_value)" .
            " SELECT site_setting_name, site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting`" .
            " WHERE site_setting_name IN ('DEFAULT_GEDCOM', 'LAST_CHANGE_EMAIL')"
        )->execute();

        return new Response(self::SUCCESS);
    }

    /**
     *
     * @TODO Most of the migration happens in the "Users" step.  This needs to
     *       be broken down into smaller functions.
     *
     * @param mixed[] $config
     *
     * @return Response
     */
    private function wizardStepTrees(array $config): Response
    {
        return new Response(self::SUCCESS);
    }

    /**
     * @param mixed[] $config
     *
     * @return Response
     */
    private function wizardStepUsers(array $config): Response
    {
        $pgv_path = $config['pgv_path'];

        $INDEX_DIRECTORY    = $config['INDEX_DIRECTORY'];
        $DBNAME             = $config['DBNAME'];
        $TBLPREFIX          = $config['TBLPREFIX'];
        $PGV_SCHEMA_VERSION = (int) ($config['PGV_SCHEMA_VERSION'] ?? '0');

        // Delete the existing user accounts, and any information associated with it
        Database::exec("UPDATE `##log` SET user_id=NULL");
        Database::exec("DELETE FROM `##user_setting` WHERE user_id > 0");
        Database::exec("DELETE FROM `##user` WHERE user_id > 0");


        ////////////////////////////////////////////////////////////////////////////////

        if ($PGV_SCHEMA_VERSION >= 12) {
            // pgv_gedcom => wt_gedcom…

            Database::prepare(
                "INSERT INTO `##gedcom` (gedcom_id, gedcom_name)" .
                " SELECT gedcom_id, gedcom_name FROM `{$DBNAME}`.`{$TBLPREFIX}gedcom`"
            )->execute();

            // pgv_gedcom_setting => wt_gedcom_setting…

            Database::prepare(
                "INSERT INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value)" .
                " SELECT gedcom_id, setting_name," .
                "  CASE setting_name" .
                "  WHEN 'THEME_DIR' THEN" .
                "   CASE setting_value" .
                "   WHEN ''                    THEN ''" .
                "   WHEN 'themes/cloudy/'      THEN 'clouds'" .
                "   WHEN 'themes/minimal/'     THEN 'minimal'" .
                "   WHEN 'themes/simplyblue/'  THEN 'colors'" .
                "   WHEN 'themes/simplygreen/' THEN 'colors'" .
                "   WHEN 'themes/simplyred/'   THEN 'colors'" .
                "   WHEN 'themes/xenea/'       THEN 'xenea'" .
                "   ELSE 'themes/webtrees/'" . // ocean, simplyred/blue/green, standard, wood
                "  END" .
                "  WHEN 'LANGUAGE' THEN" .
                "   CASE setting_value" .
                "   WHEN 'arabic'     THEN 'ar'" .
                "   WHEN 'catalan'    THEN 'ca'" .
                "   WHEN 'chinese'    THEN 'zh_CN'" .
                "   WHEN 'croatian'   THEN 'hr'" .
                "   WHEN 'danish'     THEN 'da'" .
                "   WHEN 'dutch'      THEN 'nl'" .
                "   WHEN 'english'    THEN 'en_US'" .
                "   WHEN 'english-uk' THEN 'en_GB'" . // PhpGedView once had the config for this, but no language files
                "   WHEN 'estonian'   THEN 'et'" .
                "   WHEN 'finnish'    THEN 'fi'" .
                "   WHEN 'french'     THEN 'fr'" .
                "   WHEN 'german'     THEN 'de'" .
                "   WHEN 'greek'      THEN 'el'" .
                "   WHEN 'hebrew'     THEN 'he'" .
                "   WHEN 'hungarian'  THEN 'hu'" .
                "   WHEN 'indonesian' THEN 'id'" .
                "   WHEN 'italian'    THEN 'it'" .
                "   WHEN 'lithuanian' THEN 'lt'" .
                "   WHEN 'norwegian'  THEN 'nn'" .
                "   WHEN 'polish'     THEN 'pl'" .
                "   WHEN 'portuguese' THEN 'pt'" .
                "   WHEN 'romanian'  THEN 'ro'" .
                "   WHEN 'russian'    THEN 'ru'" .
                "   WHEN 'serbian-la' THEN 'sr@Latn'" .
                "   WHEN 'slovak'     THEN 'sk'" .
                "   WHEN 'slovenian'  THEN 'sl'" .
                "   WHEN 'spanish'    THEN 'es'" .
                "   WHEN 'spanish-ar' THEN 'es'" . // webtrees does not yet have this variant
                "   WHEN 'swedish'    THEN 'sv'" .
                "   WHEN 'turkish'    THEN 'tr'" .
                "   WHEN 'vietnamese' THEN 'vi'" .
                "   ELSE 'en_US'" .
                "  END" .
                "  ELSE setting_value" .
                "  END" .
                " FROM `{$DBNAME}`.`{$TBLPREFIX}gedcom_setting`" .
                " WHERE setting_name NOT IN ('HOME_SITE_TEXT', 'HOME_SITE_URL')"
            )->execute();

            // pgv_user => wt_user…

            try {
                // "INSERT IGNORE" is needed to allow for PhpGedView users with duplicate emails. Only the first will be imported.
                Database::prepare(
                    "INSERT IGNORE INTO `##user` (user_id, user_name, real_name, email, password)" .
                    " SELECT user_id, user_name, CONCAT_WS(' ', us1.setting_value, us2.setting_value), us3.setting_value, password FROM `{$DBNAME}`.`{$TBLPREFIX}user`" .
                    " LEFT JOIN `{$DBNAME}`.`{$TBLPREFIX}user_setting` us1 USING (user_id)" .
                    " LEFT JOIN `{$DBNAME}`.`{$TBLPREFIX}user_setting` us2 USING (user_id)" .
                    " JOIN `{$DBNAME}`.`{$TBLPREFIX}user_setting` us3 USING (user_id)" .
                    " WHERE us1.setting_name='firstname'" .
                    " AND us2.setting_name='lastname'" .
                    " AND us3.setting_name='email'"
                )->execute();
            } catch (PDOException $ex) {
                DebugBar::addThrowable($ex);

                // Ignore duplicates
            }

            // pgv_user_setting => wt_user_setting…

            Database::prepare(
                "INSERT INTO `##user_setting` (user_id, setting_name, setting_value)" .
                " SELECT user_id, setting_name," .
                " CASE setting_name" .
                " WHEN 'language' THEN " .
                "  CASE setting_value" .
                "  WHEN 'arabic'     THEN 'ar'" .
                "  WHEN 'catalan'    THEN 'ca'" .
                "  WHEN 'chinese'    THEN 'zh_CN'" .
                "  WHEN 'croatian'   THEN 'hr'" .
                "  WHEN 'danish'     THEN 'da'" .
                "  WHEN 'dutch'      THEN 'nl'" .
                "  WHEN 'english'    THEN 'en_US'" .
                "  WHEN 'english-uk' THEN 'en_GB'" . // PhpGedView once had the config for this, but no language files
                "  WHEN 'estonian'   THEN 'et'" .
                "  WHEN 'finnish'    THEN 'fi'" .
                "  WHEN 'french'     THEN 'fr'" .
                "  WHEN 'german'     THEN 'de'" .
                "  WHEN 'greek'      THEN 'el'" .
                "  WHEN 'hebrew'     THEN 'he'" .
                "  WHEN 'hungarian'  THEN 'hu'" .
                "  WHEN 'indonesian' THEN 'id'" .
                "  WHEN 'italian'    THEN 'it'" .
                "  WHEN 'lithuanian' THEN 'lt'" .
                "  WHEN 'norwegian'  THEN 'nn'" .
                "  WHEN 'polish'     THEN 'pl'" .
                "  WHEN 'portuguese' THEN 'pt'" .
                "  WHEN 'romanian'  THEN 'ro'" .
                "  WHEN 'russian'    THEN 'ru'" .
                "  WHEN 'serbian-la' THEN 'sr@Latn'" .
                "  WHEN 'slovak'     THEN 'sk'" .
                "  WHEN 'slovenian'  THEN 'sl'" .
                "  WHEN 'spanish'    THEN 'es'" .
                "  WHEN 'spanish-ar' THEN 'es'" . // webtrees does not yet have this variant
                "  WHEN 'swedish'    THEN 'sv'" .
                "  WHEN 'turkish'    THEN 'tr'" .
                "  WHEN 'vietnamese' THEN 'vi'" .
                "  ELSE 'en_US'" .
                "  END" .
                " WHEN 'theme' THEN" .
                "  CASE setting_value" .
                "  WHEN ''                    THEN ''" .
                "  WHEN 'themes/cloudy/'      THEN 'clouds'" .
                "  WHEN 'themes/minimal/'     THEN 'minimal'" .
                "  WHEN 'themes/simplyblue/'  THEN 'colors'" .
                "  WHEN 'themes/simplygreen/' THEN 'colors'" .
                "  WHEN 'themes/simplyred/'   THEN 'colors'" .
                "  WHEN 'themes/xenea/'       THEN 'xenea'" .
                "  ELSE 'themes/webtrees/'" . // ocean, simplyred/blue/green, standard, wood
                "  END" .
                " ELSE" .
                "  CASE" .
                "  WHEN setting_value IN ('Y', 'yes') THEN 1 WHEN setting_value IN ('N', 'no') THEN 0 ELSE setting_value END" .
                " END" .
                " FROM `{$DBNAME}`.`{$TBLPREFIX}user_setting`" .
                " JOIN `##user` USING (user_id)" .
                " WHERE setting_name NOT IN ('email', 'firstname', 'lastname', 'loggedin')"
            )->execute();

            // pgv_user_gedcom_setting => wt_user_gedcom_setting…

            Database::prepare(
                "INSERT INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value)" .
                " SELECT user_id, gedcom_id, setting_name, setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}user_gedcom_setting`" .
                " JOIN `##user` USING (user_id)"
            )->execute();
        } else {
            // Copied from PhpGedView's db_schema_11_12
            if (file_exists($INDEX_DIRECTORY . 'gedcoms.php')) {
                // This array is set by gedcoms.php
                $GEDCOMS = [];

                require_once $INDEX_DIRECTORY . 'gedcoms.php';

                foreach ($GEDCOMS as $array) {
                    try {
                        Database::prepare("INSERT INTO `##gedcom` (gedcom_id, gedcom_name) VALUES (?,?)")
                            ->execute([
                                $array['id'],
                                $array['gedcom'],
                            ]);
                    } catch (PDOException $ex) {
                        DebugBar::addThrowable($ex);

                        // Ignore duplicates
                    }
                    // insert gedcom
                    foreach ($array as $key => $value) {
                        if ($key != 'id' && $key != 'gedcom' && $key != 'commonsurnames') {
                            try {
                                Database::prepare("INSERT INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value) VALUES (?,?, ?)")
                                    ->execute([
                                        $array['id'],
                                        $key,
                                        $value,
                                    ]);
                            } catch (PDOException $ex) {
                                DebugBar::addThrowable($ex);

                                // Ignore duplicates
                            }
                        }
                    }
                }
            }

            // Migrate the data from pgv_users into pgv_user/pgv_user_setting/pgv_user_gedcom_setting
            // pgv_users => wt_user…

            try {
                // "INSERT IGNORE" is needed to allow for PhpGedView users with duplicate emails. Only the first will be imported.
                Database::prepare(
                    "INSERT IGNORE INTO `##user` (user_name, real_name, email, password)" .
                    " SELECT u_username, CONCAT_WS(' ', u_firstname, u_lastname), u_email, u_password FROM `{$DBNAME}`.`{$TBLPREFIX}users`"
                )->execute();
            } catch (PDOException $ex) {
                DebugBar::addThrowable($ex);

                // This could only fail if;
                // a) we've already done it (upgrade)
                // b) it doesn't exist (new install)
            }

            // pgv_users => wt_user_setting…

            try {
                Database::prepare(
                    "INSERT INTO `##user_setting` (user_id, setting_name, setting_value)" .
                    " SELECT user_id, 'canadmin', CASE WHEN u_canadmin IN ('Y', 'yes') THEN 1 ELSE 0 END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'verified', CASE WHEN u_verified IN ('Y', 'yes') THEN 1 ELSE 0 END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'verified_by_admin', CASE WHEN u_verified_by_admin IN ('Y', 'yes') THEN 1 ELSE 0 END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'language', CASE u_language" .
                    "  WHEN 'catalan'    THEN 'ca'" .
                    "  WHEN 'danish'     THEN 'da'" .
                    "  WHEN 'dutch'      THEN 'nl'" .
                    "  WHEN 'english'    THEN 'en-US'" .
                    "  WHEN 'english-uk' THEN 'en-GB'" .
                    "  WHEN 'estonian'   THEN 'et'" .
                    "  WHEN 'finnish'    THEN 'fi'" .
                    "  WHEN 'french'     THEN 'fr'" .
                    "  WHEN 'german'     THEN 'de'" .
                    "  WHEN 'hebrew'     THEN 'he'" .
                    "  WHEN 'hungarian'  THEN 'hu'" .
                    "  WHEN 'italian'    THEN 'it'" .
                    "  WHEN 'norwegian'  THEN 'nn'" .
                    "  WHEN 'polish'     THEN 'pl'" .
                    "  WHEN 'portuguese' THEN 'pt'" .
                    "  WHEN 'russian'    THEN 'ru'" .
                    "  WHEN 'slovak'     THEN 'sk'" .
                    "  WHEN 'slovenian'  THEN 'sl'" .
                    "  WHEN 'spanish'    THEN 'es'" .
                    "  WHEN 'swedish'    THEN 'sv'" .
                    "  WHEN 'turkish'    THEN 'tr'" .
                    "  ELSE 'en-US'" . // PhpGedView supports other languages that webtrees does not (yet)
                    " END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'reg_timestamp', IFNULL(u_reg_timestamp, '')" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'reg_hashcode', IFNULL(u_reg_hashcode, '')" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'theme', CASE u_theme" .
                    "  WHEN 'themes/cloudy/'      THEN 'clouds'" .
                    "  WHEN 'themes/minimal/'     THEN 'minimal'" .
                    "  WHEN 'themes/simplyblue/'  THEN 'colors'" .
                    "  WHEN 'themes/simplygreen/' THEN 'colors'" .
                    "  WHEN 'themes/simplyred/'   THEN 'colors'" .
                    "  WHEN 'themes/xenea/'       THEN 'xenea'" .
                    "  ELSE ''" .
                    " END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'sessiontime', IFNULL(u_sessiontime, '')" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'contactmethod', IFNULL(u_contactmethod, '')" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'visibleonline', CASE WHEN u_visibleonline IN ('Y', 'yes') THEN 1 ELSE 0 END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'comment', IFNULL(u_comment, '')" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'relationship_privacy', CASE WHEN u_relationship_privacy IN ('Y', 'yes') THEN 1 ELSE 0 END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'max_relation_path', IFNULL(u_max_relation_path, '')" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)" .
                    " UNION ALL" .
                    " SELECT user_id, 'auto_accept', CASE WHEN u_auto_accept IN ('Y', 'yes') THEN 1 ELSE 0 END" .
                    " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                    " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)"
                )->execute();
            } catch (PDOException $ex) {
                DebugBar::addThrowable($ex);

                // This could only fail if;
                // a) we've already done it (upgrade)
                // b) it doesn't exist (new install)
            }
            // Some PhpGedView installations store the u_reg_timestamp in the format "2010-03-07 21:41:07"
            Database::prepare(
                "UPDATE `##user_setting` SET setting_value=UNIX_TIMESTAMP(setting_value) WHERE setting_name='reg_timestamp' AND setting_value LIKE '____-__-__ __:__:__'"
            )->execute();
            // Some PhpGedView installations have empty/invalid values for reg_timestamp
            Database::prepare(
                "UPDATE `##user_setting` SET setting_value=CAST(setting_value AS UNSIGNED) WHERE setting_name='reg_timestamp'"
            )->execute();

            // pgv_users => wt_user_gedcom_setting…

            $user_gedcom_settings = Database::prepare(
                "SELECT user_id, u_gedcomid, u_rootid, u_canedit" .
                " FROM `{$DBNAME}`.`{$TBLPREFIX}users`" .
                " JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)"
            )->fetchAll();

            foreach ($user_gedcom_settings as $setting) {
                try {
                    $array = unserialize($setting->u_gedcomid);
                    foreach ($array as $gedcom => $value) {
                        $tree = Tree::findByName($gedcom);
                        if ($tree !== null) {
                            Database::prepare(
                                "INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, ?)"
                            )->execute([
                                $setting->user_id,
                                $tree->getTreeId(),
                                'gedcomid',
                                $value,
                            ]);
                        }
                    }
                } catch (Throwable $ex) {
                    DebugBar::addThrowable($ex);

                    // Invalid serialized data?
                }

                try {
                    $array = unserialize($setting->u_rootid);
                    foreach ($array as $gedcom => $value) {
                        $tree = Tree::findByName($gedcom);
                        if ($tree !== null) {
                            Database::prepare(
                                "INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, ?)"
                            )->execute([
                                $setting->user_id,
                                $tree->getTreeId(),
                                'rootid',
                                $value,
                            ]);
                        }
                    }
                } catch (Throwable $ex) {
                    DebugBar::addThrowable($ex);

                    // Invalid serialized data?
                }

                try {
                    $array = unserialize($setting->u_canedit);
                    foreach ($array as $gedcom => $value) {
                        $tree = Tree::findByName($gedcom);
                        if ($tree !== null) {
                            Database::prepare(
                                "INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, ?)"
                            )->execute([
                                $setting->user_id,
                                $tree->getTreeId(),
                                'canedit',
                                $value,
                            ]);
                        }
                    }
                } catch (Throwable $ex) {
                    DebugBar::addThrowable($ex);

                    // Invalid serialized data?
                }
            }
        }

        define('PGV_PHPGEDVIEW', true);
        define('PGV_PRIV_PUBLIC', Auth::PRIV_PRIVATE);
        define('PGV_PRIV_USER', Auth::PRIV_USER);
        define('PGV_PRIV_NONE', Auth::PRIV_NONE);
        define('PGV_PRIV_HIDE', Auth::PRIV_HIDE);

        global $PRIV_HIDE, $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE;
        $PRIV_PUBLIC = Auth::PRIV_PRIVATE;
        $PRIV_USER   = Auth::PRIV_USER;
        $PRIV_NONE   = Auth::PRIV_NONE;
        $PRIV_HIDE   = Auth::PRIV_HIDE;

        // Old versions of PhpGedView used a $GEDCOMS[] array.
        // New versions used a database.
        $GEDCOMS = Database::prepare(
            "SELECT" .
            " gedcom_id         AS id," .
            " gedcom_name       AS gedcom," .
            " gs1.setting_value AS config," .
            " gs2.setting_value AS privacy" .
            " FROM  `##gedcom`" .
            " JOIN  `##gedcom_setting` AS gs1 USING (gedcom_id)" .
            " JOIN  `##gedcom_setting` AS gs2 USING (gedcom_id)" .
            " WHERE gedcom_id>0" .
            " AND   gs1.setting_name='config'" .
            " AND   gs2.setting_name='privacy'"
        )->fetchAll();

        foreach ($GEDCOMS as $GEDCOM => $GED_DATA) {
            // We read these variables from PhpGedView's index/*_conf.php, and set them here in case any are missing.
            $ADVANCED_NAME_FACTS          = '';
            $ADVANCED_PLAC_FACTS          = '';
            $ALLOW_THEME_DROPDOWN         = '';
            $CALENDAR_FORMAT              = '';
            $CHART_BOX_TAGS               = '';
            $CONTACT_EMAIL                = '';
            $DEFAULT_PEDIGREE_GENERATIONS = '';
            $EXPAND_NOTES                 = '';
            $EXPAND_SOURCES               = '';
            $FAM_FACTS_ADD                = '';
            $FAM_FACTS_QUICK              = '';
            $FAM_FACTS_UNIQUE             = '';
            $FULL_SOURCES                 = '';
            $GENERATE_UIDS                = '';
            $HIDE_GEDCOM_ERRORS           = '';
            $HIDE_LIVE_PEOPLE             = '';
            $INDI_FACTS_ADD               = '';
            $INDI_FACTS_QUICK             = '';
            $INDI_FACTS_UNIQUE            = '';
            $LANGUAGE                     = '';
            $MAX_ALIVE_AGE                = '';
            $MAX_DESCENDANCY_GENERATIONS  = '';
            $MAX_PEDIGREE_GENERATIONS     = '';
            $MAX_RELATION_PATH_LENGTH     = '';
            $META_DESCRIPTION             = '';
            $META_TITLE                   = '';
            $MULTI_MEDIA                  = '';
            $NOTE_FACTS_ADD               = '';
            $NOTE_FACTS_QUICK             = '';
            $NOTE_FACTS_UNIQUE            = '';
            $NO_UPDATE_CHAN               = '';
            $PEDIGREE_LAYOUT              = '';
            $PEDIGREE_ROOT_ID             = '';
            $PEDIGREE_SHOW_GENDER         = '';
            $PREFER_LEVEL2_SOURCES        = '';
            $QUICK_REQUIRED_FACTS         = '';
            $QUICK_REQUIRED_FAMFACTS      = '';
            $REPO_FACTS_ADD               = '';
            $REPO_FACTS_QUICK             = '';
            $REPO_FACTS_UNIQUE            = '';
            $REQUIRE_AUTHENTICATION       = '';
            $SHOW_COUNTER                 = '';
            $SHOW_DEAD_PEOPLE             = '';
            $SHOW_EST_LIST_DATES          = '';
            $SHOW_FACT_ICONS              = '';
            $SHOW_GEDCOM_RECORD           = '';
            $SHOW_HIGHLIGHT_IMAGES        = '';
            $SHOW_LDS_AT_GLANCE           = '';
            $SHOW_LIST_PLACES             = '';
            $SHOW_LIVING_NAMES            = '';
            $SHOW_MEDIA_DOWNLOAD          = '';
            $SHOW_PARENTS_AGE             = '';
            $SHOW_PEDIGREE_PLACES         = '';
            $SHOW_PRIVATE_RELATIONSHIPS   = '';
            $SHOW_RELATIVES_EVENTS        = '';
            $SOUR_FACTS_ADD               = '';
            $SOUR_FACTS_QUICK             = '';
            $SOUR_FACTS_UNIQUE            = '';
            $SUBLIST_TRIGGER_I            = '';
            $SURNAME_LIST_STYLE           = '';
            $SURNAME_TRADITION            = '';
            $THEME_DIR                    = '';
            $USE_RELATIONSHIP_PRIVACY     = '';
            $WEBMASTER_EMAIL              = '';
            $WORD_WRAPPED_NOTES           = '';

            $config = str_replace([
                '$INDEX_DIRECTORY',
                '${INDEX_DIRECTORY}',
            ], $INDEX_DIRECTORY, $GED_DATA->config);
            if (substr($config, 0, 1) === '.') {
                $config = $pgv_path . '/' . $config;
            }
            if (is_readable($config)) {
                require $config;
            }

            $stmt_default_resn   = Database::prepare("INSERT INTO `##default_resn` (gedcom_id, xref, tag_type, resn) VALUES (?, ?, ?, CASE ? WHEN -1 THEN 'hidden' WHEN 0 THEN 'confidential' WHEN 1 THEN 'privacy' ELSE 'none' END)");
            $stmt_gedcom_setting = Database::prepare("INSERT INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value) VALUES (?,?,?)");

            $privacy = str_replace([
                '$INDEX_DIRECTORY',
                '${INDEX_DIRECTORY}',
            ], $INDEX_DIRECTORY, $GED_DATA->privacy);
            if (substr($config, 0, 1) == '.') {
                $privacy = $pgv_path . '/' . $privacy;
            }

            if (is_readable($privacy)) {
                // These arrays are defined in the privacy file
                $global_facts   = [];
                $person_privacy = [];
                $person_facts   = [];

                require $privacy;

                foreach ($global_facts as $key => $value) {
                    if (isset($value['details'])) {
                        $stmt_default_resn->execute([
                            $GED_DATA->id,
                            null,
                            $key,
                            $value['details'],
                        ]);
                    }
                }

                foreach ($person_privacy as $key => $value) {
                    $stmt_default_resn->execute([
                        $GED_DATA->id,
                        $key,
                        null,
                        $value['details'],
                    ]);
                }

                foreach ($person_facts as $key1 => $array) {
                    foreach ($array as $key2 => $value) {
                        if (isset($value['details'])) {
                            $stmt_default_resn->execute([
                                $GED_DATA->id,
                                $key1,
                                $key2,
                                $value['details'],
                            ]);
                        }
                    }
                }
            }

            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'ADVANCED_NAME_FACTS',
                $ADVANCED_NAME_FACTS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'ADVANCED_PLAC_FACTS',
                $ADVANCED_PLAC_FACTS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'ALLOW_THEME_DROPDOWN',
                $ALLOW_THEME_DROPDOWN,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'CALENDAR_FORMAT',
                $CALENDAR_FORMAT,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'CHART_BOX_TAGS',
                $CHART_BOX_TAGS,
            ]);
            $user = User::findByIdentifier($CONTACT_EMAIL);
            if ($user) {
                $stmt_gedcom_setting->execute([
                    $GED_DATA->id,
                    'CONTACT_USER_ID',
                    $user->getUserId(),
                ]);
            }
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'DEFAULT_PEDIGREE_GENERATIONS',
                $DEFAULT_PEDIGREE_GENERATIONS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'EXPAND_NOTES',
                $EXPAND_NOTES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'EXPAND_SOURCES',
                $EXPAND_SOURCES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'FAM_FACTS_ADD',
                $FAM_FACTS_ADD,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'FAM_FACTS_QUICK',
                $FAM_FACTS_QUICK,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'FAM_FACTS_UNIQUE',
                $FAM_FACTS_UNIQUE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'FULL_SOURCES',
                $FULL_SOURCES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'GENERATE_UIDS',
                $GENERATE_UIDS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'HIDE_GEDCOM_ERRORS',
                $HIDE_GEDCOM_ERRORS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'HIDE_LIVE_PEOPLE',
                $HIDE_LIVE_PEOPLE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'INDI_FACTS_ADD',
                $INDI_FACTS_ADD,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'INDI_FACTS_QUICK',
                $INDI_FACTS_QUICK,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'INDI_FACTS_UNIQUE',
                $INDI_FACTS_UNIQUE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'LANGUAGE',
                self::PGV_LANGUAGES[$LANGUAGE] ?? 'en-US',
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'MAX_ALIVE_AGE',
                $MAX_ALIVE_AGE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'MAX_DESCENDANCY_GENERATIONS',
                $MAX_DESCENDANCY_GENERATIONS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'MAX_PEDIGREE_GENERATIONS',
                $MAX_PEDIGREE_GENERATIONS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'MAX_RELATION_PATH_LENGTH',
                $MAX_RELATION_PATH_LENGTH,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'MEDIA_DIRECTORY',
                'media/',
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'META_DESCRIPTION',
                $META_DESCRIPTION,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'META_TITLE',
                $META_TITLE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'MEDIA_UPLOAD',
                $MULTI_MEDIA,
            ]); // see schema v12-13
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'NOTE_FACTS_ADD',
                $NOTE_FACTS_ADD,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'NOTE_FACTS_QUICK',
                $NOTE_FACTS_QUICK,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'NOTE_FACTS_UNIQUE',
                $NOTE_FACTS_UNIQUE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'NO_UPDATE_CHAN',
                $NO_UPDATE_CHAN,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'PEDIGREE_LAYOUT',
                $PEDIGREE_LAYOUT,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'PEDIGREE_ROOT_ID',
                $PEDIGREE_ROOT_ID,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'PEDIGREE_SHOW_GENDER',
                $PEDIGREE_SHOW_GENDER,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'PREFER_LEVEL2_SOURCES',
                $PREFER_LEVEL2_SOURCES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'QUICK_REQUIRED_FACTS',
                $QUICK_REQUIRED_FACTS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'QUICK_REQUIRED_FAMFACTS',
                $QUICK_REQUIRED_FAMFACTS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'REPO_FACTS_ADD',
                $REPO_FACTS_ADD,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'REPO_FACTS_QUICK',
                $REPO_FACTS_QUICK,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'REPO_FACTS_UNIQUE',
                $REPO_FACTS_UNIQUE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'REQUIRE_AUTHENTICATION',
                $REQUIRE_AUTHENTICATION,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_COUNTER',
                $SHOW_COUNTER,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_DEAD_PEOPLE',
                $SHOW_DEAD_PEOPLE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_EST_LIST_DATES',
                $SHOW_EST_LIST_DATES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_FACT_ICONS',
                $SHOW_FACT_ICONS,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_GEDCOM_RECORD',
                $SHOW_GEDCOM_RECORD,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_HIGHLIGHT_IMAGES',
                $SHOW_HIGHLIGHT_IMAGES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_LDS_AT_GLANCE',
                $SHOW_LDS_AT_GLANCE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_LIST_PLACES',
                $SHOW_LIST_PLACES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_LIVING_NAMES',
                $SHOW_LIVING_NAMES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_MEDIA_DOWNLOAD',
                $SHOW_MEDIA_DOWNLOAD,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_PARENTS_AGE',
                $SHOW_PARENTS_AGE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_PEDIGREE_PLACES',
                $SHOW_PEDIGREE_PLACES,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_PRIVATE_RELATIONSHIPS',
                $SHOW_PRIVATE_RELATIONSHIPS,
            ]);

            // Update these - see db_schema_5_6.php
            $SHOW_RELATIVES_EVENTS = preg_replace('/_(BIRT|MARR|DEAT)_(COUS|MSIB|FSIB|GGCH|NEPH|GGPA)/', '', $SHOW_RELATIVES_EVENTS);
            $SHOW_RELATIVES_EVENTS = preg_replace('/_FAMC_(RESI_EMIG)/', '', $SHOW_RELATIVES_EVENTS);
            $SHOW_RELATIVES_EVENTS = preg_replace('/_MARR_(MOTH|FATH|FAMC)/', '_MARR_PARE', $SHOW_RELATIVES_EVENTS);
            $SHOW_RELATIVES_EVENTS = preg_replace('/_DEAT_(MOTH|FATH)/', '_DEAT_PARE', $SHOW_RELATIVES_EVENTS);
            preg_match_all('/[_A-Z]+/', $SHOW_RELATIVES_EVENTS, $match);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SHOW_RELATIVES_EVENTS',
                implode(',', array_unique($match[0])),
            ]);

            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SOUR_FACTS_ADD',
                $SOUR_FACTS_ADD,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SOUR_FACTS_QUICK',
                $SOUR_FACTS_QUICK,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SOUR_FACTS_UNIQUE',
                $SOUR_FACTS_UNIQUE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SUBLIST_TRIGGER_I',
                $SUBLIST_TRIGGER_I,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SURNAME_LIST_STYLE',
                $SURNAME_LIST_STYLE,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'SURNAME_TRADITION',
                $SURNAME_TRADITION,
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'THEME_DIR',
                self::PGV_THEMES[$THEME_DIR] ?? 'webtrees',
            ]);
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'USE_RELATIONSHIP_PRIVACY',
                $USE_RELATIONSHIP_PRIVACY,
            ]);

            $user = User::findByIdentifier($WEBMASTER_EMAIL);
            if ($user) {
                $stmt_gedcom_setting->execute([
                    $GED_DATA->id,
                    'WEBMASTER_USER_ID',
                    $user->getUserId(),
                ]);
            }
            $stmt_gedcom_setting->execute([
                $GED_DATA->id,
                'WORD_WRAPPED_NOTES',
                $WORD_WRAPPED_NOTES,
            ]);
        }
        Database::prepare("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('config', 'privacy', 'path', 'pgv_ver', 'imported')")->execute();

        // webtrees 1.0.5 combines user and gedcom settings for relationship privacy
        // into a combined user-gedcom setting, for more granular control
        Database::exec(
            "INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value)" .
            " SELECT u.user_id, g.gedcom_id, 'RELATIONSHIP_PATH_LENGTH', LEAST(us1.setting_value, gs1.setting_value)" .
            " FROM   `##user` u" .
            " CROSS  JOIN `##gedcom` g" .
            " LEFT   JOIN `##user_setting`   us1 ON (u.user_id  =us1.user_id   AND us1.setting_name='max_relation_path')" .
            " LEFT   JOIN `##user_setting`   us2 ON (u.user_id  =us2.user_id   AND us2.setting_name='relationship_privacy')" .
            " LEFT   JOIN `##gedcom_setting` gs1 ON (g.gedcom_id=gs1.gedcom_id AND gs1.setting_name='MAX_RELATION_PATH_LENGTH')" .
            " LEFT   JOIN `##gedcom_setting` gs2 ON (g.gedcom_id=gs2.gedcom_id AND gs2.setting_name='USE_RELATIONSHIP_PRIVACY')" .
            " WHERE  us2.setting_value AND gs2.setting_value"
        );

        Database::exec(
            "DELETE FROM `##gedcom_setting` WHERE setting_name IN ('MAX_RELATION_PATH_LENGTH', 'USE_RELATIONSHIP_PRIVACY')"
        );

        Database::exec(
            "DELETE FROM `##user_setting` WHERE setting_name IN ('relationship_privacy', 'max_relation_path_length')"
        );

        ////////////////////////////////////////////////////////////////////////////////
        // The PhpGedView blocks don't migrate easily.
        // Just give everybody and every tree default blocks
        ////////////////////////////////////////////////////////////////////////////////

        Database::prepare(
            "INSERT INTO `##block` (user_id, location, block_order, module_name)" .
            " SELECT `##user`.user_id, location, block_order, module_name" .
            " FROM `##block`" .
            " JOIN `##user`" .
            " WHERE `##block`.user_id = -1" .
            " AND   `##user`.user_id  >  0"
        )->execute();

        Database::prepare(
            "INSERT INTO `##block` (gedcom_id, location, block_order, module_name)" .
            " SELECT `##gedcom`.gedcom_id, location, block_order, module_name" .
            " FROM `##block`" .
            " JOIN `##gedcom`" .
            " WHERE `##block`.gedcom_id = -1" .
            " AND   `##gedcom`.gedcom_id  >  0"
        )->execute();

        ////////////////////////////////////////////////////////////////////////////////
        // Hit counter
        ////////////////////////////////////////////////////////////////////////////////
        //
        if ($PGV_SCHEMA_VERSION >= 13) {
            // pgv_hit_counter => wt_hit_counter…

            Database::prepare(
                "REPLACE INTO `##hit_counter` (gedcom_id, page_name, page_parameter, page_count)" .
                " SELECT gedcom_id, page_name, page_parameter, page_count FROM `{$DBNAME}`.`{$TBLPREFIX}hit_counter`"
            )->execute();
        } else {
            // Copied from PhpGedView's db_schema_12_13
            $statement = Database::prepare("INSERT IGNORE INTO `##hit_counter` (gedcom_id, page_name, page_parameter, page_count) VALUES (?, ?, ?, ?)");

            foreach ($GEDCOMS as $GEDCOM => $GED_DATA) {
                $file = $INDEX_DIRECTORY . '/' . $GED_DATA->gedcom . 'pgv_counters.txt';
                // $file, ' => wt_hit_counter…

                if (file_exists($file)) {
                    foreach (file($file) as $line) {
                        if (preg_match('/(@([A-Za-z0-9:_-]+)@ )?(\d+)/', $line, $match)) {
                            if ($match[2]) {
                                $page_name      = 'individual.php';
                                $page_parameter = $match[2];
                            } else {
                                $page_name      = 'index.php';
                                $page_parameter = 'gedcom:' . $GED_DATA->id;
                            }
                            try {
                                $statement->execute([
                                    $GED_DATA->id,
                                    $page_name,
                                    $page_parameter,
                                    $match[3],
                                ]);
                            } catch (PDOException $ex) {
                                DebugBar::addThrowable($ex);

                                // Primary key violation? Ignore?
                            }
                        }
                    }
                }
            }
        }

        ////////////////////////////////////////////////////////////////////////////////

        foreach ($GEDCOMS as $GED_DATA) {
            Module::setDefaultAccess($GED_DATA->id);
        }

        // pgv_site_setting => wt_module_setting…

        Database::prepare(
            "REPLACE INTO `##module_setting` (module_name, setting_name, setting_value)" .
            " SELECT 'googlemap', site_setting_name, site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting`" .
            " WHERE site_setting_name LIKE 'GM_%'"
        )->execute();
        Database::prepare(
            "REPLACE INTO `##module_setting` (module_name, setting_name, setting_value)" .
            " SELECT 'lightbox', site_setting_name, site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting`" .
            " WHERE site_setting_name LIKE 'LB_%'"
        )->execute();

        ////////////////////////////////////////////////////////////////////////////////

        // pgv_favorites => wt_favorite…

        try {
            Database::prepare(
                "REPLACE INTO `##favorite` (favorite_id, user_id, gedcom_id, xref, favorite_type, url, title, note)" .
                " SELECT fv_id, u.user_id, g.gedcom_id, fv_gid, fv_type, fv_url, fv_title, fv_note" .
                " FROM `{$DBNAME}`.`{$TBLPREFIX}favorites` f" .
                " LEFT JOIN `##gedcom` g ON (f.fv_username=g.gedcom_name)" .
                " LEFT JOIN `##user`   u ON (f.fv_username=u.user_name)"
            )->execute();
        } catch (PDOException $ex) {
            DebugBar::addThrowable($ex);

            // This table will only exist if the favorites module is installed in WT
        }

        ////////////////////////////////////////////////////////////////////////////////

        // pgv_news => wt_news…

        try {
            Database::prepare(
                "REPLACE INTO `##news` (news_id, user_id, gedcom_id, subject, body, updated)" .
                " SELECT n_id, u.user_id, g.gedcom_id, n_title, n_text, FROM_UNIXTIME(n_date)" .
                " FROM `{$DBNAME}`.`{$TBLPREFIX}news` n" .
                " LEFT JOIN `##gedcom` g ON (n.n_username=g.gedcom_name)" .
                " LEFT JOIN `##user` u ON (n.n_username=u.user_name)"
            )->execute();
        } catch (PDOException $ex) {
            DebugBar::addThrowable($ex);

            // This table will only exist if the news/blog module is installed in WT
        }

        ////////////////////////////////////////////////////////////////////////////////

        // pgv_messages => wt_message…

        Database::prepare(
            "REPLACE INTO `##message` (message_id, sender, ip_address, user_id, subject, body, created)" .
            " SELECT m_id, m_from, '127.0.0.1', user_id, m_subject, m_body, STR_TO_DATE(LEFT(m_created,25),'%a, %d %M %Y %H:%i:%s')" .
            " FROM `{$DBNAME}`.`{$TBLPREFIX}messages`" .
            " JOIN `##user` ON (CONVERT(m_to USING utf8) COLLATE utf8_unicode_ci=user_name)"
        )->execute();


        ////////////////////////////////////////////////////////////////////////////////

        // Genealogy records…

        Database::prepare(
            "INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
            " SELECT o_file, o_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}other`" .
            " JOIN `##gedcom` ON (o_file = gedcom_id)" .
            " ORDER BY o_type!='HEAD'" // Must load HEAD record first
        )->execute();

        Database::prepare(
            "INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
            " SELECT i_file, i_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}individuals`" .
            " JOIN `##gedcom` ON (i_file = gedcom_id)"
        )->execute();

        Database::prepare(
            "INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
            " SELECT f_file, f_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}families`" .
            " JOIN `##gedcom` ON (f_file = gedcom_id)"
        )->execute();

        Database::prepare(
            "INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
            " SELECT s_file, s_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}sources`" .
            " JOIN `##gedcom` ON (s_file = gedcom_id)"
        )->execute();

        Database::prepare(
            "INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
            " SELECT m_gedfile, m_gedrec, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}media`" .
            " JOIN `##gedcom` ON (m_gedfile = gedcom_id)"
        )->execute();

        Database::prepare(
            "UPDATE `##gedcom_setting` SET setting_value='0' WHERE setting_name='imported'"
        )->execute();

        $content =
            I18N::translate('You need to sign in again, using your PhpGedView username and password.') .
            '<br>' .
            '<a href="' . e(route('admin-trees')) . '" class="btn btn-primary">' . I18N::translate('continue') . '</a>';

        Auth::logout();

        return new Response($content);
    }

    /**
     * Look for nearby installations of PhpGedView.
     *
     * @return string[]
     */
    private function defaultPhpGedViewPaths(): array
    {
        $php_paths = array_merge(
            glob('../config.php') ?: [],
            glob('../*/config.php') ?: []
        );

        return array_map(function (string $path): string {
            return dirname($path) . DIRECTORY_SEPARATOR;
        }, $php_paths);
    }

    /**
     * Read the entries from config.php into an array.
     *
     * @param string $pgv_path
     *
     * @return mixed[]
     */
    private function readPhpGedViewConfig(string $pgv_path): array
    {
        // Make a copy of the file, which ignores lines containing require/include
        $config_php = file_get_contents($pgv_path . '/config.php');
        $config_php = preg_replace('/^\s*(include|require).*/m', '', $config_php);
        $tmp_file   = WT_DATA_DIR . 'pgv-config.php';
        file_put_contents($tmp_file, $config_php);

        // This is defined in the config file.
        $INDEX_DIRECTORY = '';

        try {
            ob_start();
            include $tmp_file;
        } catch (Exception $ex) {
            // Invalid config file?  Nothing we can do.
        } finally {
            unlink($tmp_file);
            ob_end_clean();
        }

        // The index directory can be either absolute or relative to the PhpGedView root.
        if (preg_match('/^(\/|\\|[A-Z]:)/', $INDEX_DIRECTORY)) {
            $INDEX_DIRECTORY = realpath($INDEX_DIRECTORY) . DIRECTORY_SEPARATOR;
        } else {
            $INDEX_DIRECTORY = realpath($pgv_path . DIRECTORY_SEPARATOR . $INDEX_DIRECTORY) . DIRECTORY_SEPARATOR;
        }

        return get_defined_vars();
    }
}
