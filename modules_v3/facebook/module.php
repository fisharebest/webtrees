<?php
// Facebook Module for webtrees
//
// Copyright (C) 2012 Matthew N.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.

if (!defined('WT_WEBTREES')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('WT_FACEBOOK_VERSION', "v1.0-beta.7");
define('WT_FACEBOOK_UPDATE_CHECK_URL', "https://api.github.com/repos/mnoorenberghe/webtrees-facebook/contents/versions.json?ref=gh-pages");

use WT\Auth;
use WT\Log;
use WT\User;

class facebook_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Menu {
    const scope = 'user_birthday,user_hometown,user_location,user_relationships,user_relationship_details,email';
    const user_setting_facebook_username = 'facebook_username';
    const profile_photo_large_width = 1024;
    const api_dir = "v1.0/"; // TODO: make an admin preference so new installs can use this module.

    private $hideStandardForms = false;

    public function __construct() {
        parent::__construct();

        // Load any local user translations
        if (is_dir(WT_MODULES_DIR . $this->getName() . '/language')) {
            if (file_exists(WT_MODULES_DIR . $this->getName() . '/language/' . WT_LOCALE . '.mo')) {
                $tr = new Zend_Translate('gettext', WT_MODULES_DIR . $this->getName() . '/language/'
                                         . WT_LOCALE . '.mo', WT_LOCALE);
                Zend_Registry::get('Zend_Translate')->addTranslation($tr);
            }
        }
    }

    // Implement WT_Module_Config
    public function getConfigLink() {
        return 'module.php?mod='.$this->getName().'&amp;mod_action=admin';
    }

    // Extend WT_Module
    public function getTitle() {
        return /* I18N: Name of a module */ WT_I18N::translate('Facebook');
    }

    // Extend WT_Module
    public function getDescription() {
        return /* I18N: Description of the "Facebook" module */ WT_I18N::translate('Allow users to login with Facebook.');
    }

    // Extend WT_Module
    public function modAction($mod_action) {
        switch($mod_action) {
            case 'admin':
                $this->admin();
                break;
	    case 'connect':
                $this->connect();
                break;
            case 'admin_friend_picker':
                $this->fetchFriendList();
                break;
	    default:
                header('HTTP/1.0 404 Not Found');
        }
    }

    private function get_edit_options() {
        return array( // from admin_users.php
                     'none'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Visitor'),
                     'access'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Member'),
                     'edit'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Editor'),
                     'accept'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Moderator'),
                     'admin' => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Manager')
                      );
    }

    private function admin() {
        $mod_name = $this->getName();
        $preApproved = unserialize($this->getSetting('preapproved'));

        if (WT_Filter::post('saveAPI') && WT_Filter::checkCsrf()) {
            $this->setSetting('app_id', WT_Filter::post('app_id', WT_REGEX_ALPHANUM));
            $this->setSetting('app_secret', WT_Filter::post('app_secret', WT_REGEX_ALPHANUM));
            $this->setSetting('require_verified', WT_Filter::post('require_verified', WT_REGEX_INTEGER, false));
            $this->setSetting('hide_standard_forms', WT_Filter::post('hide_standard_forms', WT_REGEX_INTEGER, false));
            Log::addConfigurationLog("Facebook: API settings changed");
            WT_FlashMessages::addMessage(WT_I18N::translate('Settings saved'));
        } else if (WT_Filter::post('addLink') && WT_Filter::checkCsrf()) {
            $user_id = WT_Filter::post('user_id', WT_REGEX_INTEGER);
            $facebook_username = $this->cleanseFacebookUsername(WT_Filter::post('facebook_username', WT_REGEX_USERNAME));
            if ($user_id && $facebook_username && !$this->get_user_id_from_facebook_username($facebook_username)) {
                $user = User::find($user_id);
                $user->setPreference(self::user_setting_facebook_username, $facebook_username);
                if (isset($preApproved[$facebook_username])) {
                    // Delete a pre-approval for the Facebook username.
                    unset($preApproved[$facebook_username]);
                    $this->setSetting('preapproved', serialize($preApproved));
                }
                Log::addConfigurationLog("Facebook: User $user_id linked to Facebook user $facebook_username");
                WT_FlashMessages::addMessage(WT_I18N::translate('User %1$s linked to Facebook user %2$s', $user_id, $facebook_username));
            } else {
                WT_FlashMessages::addMessage(WT_I18N::translate('The user could not be linked'));
            }
        } else if (WT_Filter::post('deleteLink') && WT_Filter::checkCsrf()) {
            $user_id = WT_Filter::post('deleteLink', WT_REGEX_INTEGER);
            if ($user_id) {
                $user = User::find($user_id);
                $user->deletePreference(self::user_setting_facebook_username);
                Log::addConfigurationLog("Facebook: User $user_id unlinked from a Facebook user");
                WT_FlashMessages::addMessage(WT_I18N::translate('User unlinked'));
            } else {
                WT_FlashMessages::addMessage(WT_I18N::translate('The link could not be deleted'));
            }
        } else if (WT_Filter::post('savePreapproved') && WT_Filter::checkCsrf()) {
            $table = WT_Filter::post('preApproved');
            if ($facebook_username = $this->cleanseFacebookUsername(WT_Filter::post('preApproved_new_facebook_username', WT_REGEX_USERNAME))) {
                // Process additions
                $row = $table['new'];
                $this->appendPreapproved($preApproved, $facebook_username, $row);
                $this->setSetting('preapproved', serialize($preApproved));
                Log::addConfigurationLog("Facebook: Pre-approved Facebook user: $facebook_username");
                WT_FlashMessages::addMessage(WT_I18N::translate('Pre-approved user "%s" added', $facebook_username));
            }
            unset($table['new']);
            // Process changes
            foreach($table as $facebook_username => $row) {
                $this->appendPreapproved($preApproved, $facebook_username, $row);
            }
            $this->setSetting('preapproved', serialize($preApproved));
            Log::addConfigurationLog("Facebook: Pre-approved Facebook users changed");
            WT_FlashMessages::addMessage(WT_I18N::translate('Changes to pre-approved users saved'));
        } else if (WT_Filter::post('deletePreapproved') && WT_Filter::checkCsrf()) {
            $facebook_username = trim(WT_Filter::post('deletePreapproved', WT_REGEX_USERNAME));
            if ($facebook_username && isset($preApproved[$facebook_username])) {
                unset($preApproved[$facebook_username]);
                $this->setSetting('preapproved', serialize($preApproved));
                Log::addConfigurationLog("Facebook: Pre-approved Facebook user deleted: $facebook_username");
                WT_FlashMessages::addMessage(WT_I18N::translate('Pre-approved user "%s" deleted', $facebook_username));
            } else {
                WT_FlashMessages::addMessage(WT_I18N::translate('The pre-approved user "%s" could not be deleted', $facebook_username));
            }
        }

        $controller = new WT_Controller_Page();
        $controller
            ->restrictAccess(\WT\Auth::isAdmin())
            ->setPageTitle($this->getTitle())
            ->pageHeader();

        $linkedUsers = array();
        $unlinkedUsers = array();
        $users = $this->get_users_with_module_settings();
        foreach ($users as $userid => $user) {
            if (empty($user[0]->facebook_username)) {
                $unlinkedUsers[$userid] = $user[0];
            } else {
                $linkedUsers[$userid] = $user[0];
            }
        }

        $unlinkedOptions = $this->user_options($unlinkedUsers);

        require_once WT_ROOT.'includes/functions/functions_edit.php';
        require 'templates/admin.php';
    }

    private function appendPreapproved(&$preApproved, $facebook_username, $row) {
        $facebook_username = $this->cleanseFacebookUsername($facebook_username);
        if (!$facebook_username) {
            WT_FlashMessages::addMessage(WT_I18N::translate('Missing Facebook username'));
            return;
        }
        if ($this->get_user_id_from_facebook_username($facebook_username)) {
            WT_FlashMessages::addMessage(WT_I18N::translate('User is already registered'));
            return;
        }

        $preApproved[$facebook_username] = array();
        foreach ($row as $gedcom => $settings) {
            $preApproved[$facebook_username][$gedcom] = array(
                                                              'rootid' => filter_var(@$settings['rootid'], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(' .  WT_REGEX_XREF . ')$/u'))),
                                                              'gedcomid' => filter_var(@$settings['gedcomid'], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(' . WT_REGEX_XREF . ')$/u'))),
                                                              'canedit' => filter_var($settings['canedit'], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(' . WT_REGEX_ALPHA . ')$/u')))
            );
        }
    }

    private function isSetup() {
        $mod_name = $this->getName();
        $app_id = $this->getSetting('app_id');
        $app_secret = $this->getSetting('app_secret');
        $this->hideStandardForms = $this->getSetting('hide_standard_forms', false);

        return !empty($app_id) && !empty($app_secret);
    }

    private function connect() {
        global $WT_SESSION;

        $url = WT_Filter::post('url', NULL, WT_Filter::get('url', NULL, ''));
        // If we’ve clicked login from the login page, we don’t want to go back there.
        if (strpos($url, 'login.php') === 0
            || (strpos($url, 'mod=facebook') !== false
                && strpos($url, 'mod_action=connect') !== false)) {
            $url = '';
        }

        // Redirect to the homepage/$url if the user is already logged-in.
        if ($WT_SESSION->wt_user) {
            header('Location: ' . WT_SCRIPT_PATH . $url);
            exit;
        }

        $app_id = $this->getSetting('app_id');
        $app_secret = $this->getSetting('app_secret');
        $connect_url = $this->getConnectURL($url);

        if (!$app_id || !$app_secret) {
            $this->error_page(WT_I18N::translate('Facebook logins have not been setup by the administrator.'));
            return;
        }

        $code = @$_REQUEST["code"];

        if (!empty($_REQUEST['error'])) {
            Log::addErrorLog('Facebook Error: ' . WT_Filter::get('error') . '. Reason: ' . WT_Filter::get('error_reason'));
            if ($_REQUEST['error_reason'] == 'user_denied') {
                $this->error_page(WT_I18N::translate('You must allow access to your Facebook account in order to login with Facebook.'));
            } else {
                $this->error_page(WT_I18N::translate('An error occurred trying to log you in with Facebook.'));
            }
        } else if (empty($code) && empty($WT_SESSION->facebook_access_token)) {
            if (!WT_Filter::checkCsrf()) {
                echo WT_I18N::translate('This form has expired.  Try again.');
                return;
            }

            $WT_SESSION->timediff = WT_Filter::postInteger('timediff', -43200, 50400, 0); // Same range as date('Z')
            // FB Login flow has not begun so redirect to login dialog.
            $WT_SESSION->facebook_state = md5(uniqid(rand(), TRUE)); // CSRF protection
            $dialog_url = "https://www.facebook.com/dialog/oauth?client_id="
                . $app_id . "&redirect_uri=" . urlencode($connect_url) . "&state="
                . $WT_SESSION->facebook_state . "&scope=" . self::scope;
            Zend_Session::writeClose();
            echo("<script> window.location.href='" . $dialog_url . "'</script>");
        } else if (!empty($WT_SESSION->facebook_access_token)) {
            // User has already authorized the app and we have a token so get their info.
            $graph_url = "https://graph.facebook.com/" . self::api_dir . "me?access_token="
                . $WT_SESSION->facebook_access_token;
            $response = $this->fetch_url($graph_url);
            if ($response === FALSE) {
                Log::addErrorLog("Facebook: Access token is no longer valid");
                // Clear the state and try again with a new token.
                try {
                    unset($WT_SESSION->facebook_access_token);
                    unset($WT_SESSION->facebook_state);
                    Zend_Session::writeClose();
                } catch (Exception $e) { }

                header("Location: " . $this->getConnectURL($url));
                exit;
            }

            $user = json_decode($response);
            $this->login_or_register($user, $url);
        } else if (!empty($WT_SESSION->facebook_state) && ($WT_SESSION->facebook_state === $_REQUEST['state'])) {
            // User has already been redirected to login dialog.
            // Exchange the code for an access token.
            $token_url = "https://graph.facebook.com/" . self::api_dir . "oauth/access_token?"
                . "client_id=" . $app_id . "&redirect_uri=" . urlencode($connect_url)
                . "&client_secret=" . $app_secret . "&code=" . $code;

            $response = $this->fetch_url($token_url);
            if ($response === FALSE) {
                Log::addErrorLog("Facebook: Couldn't exchange the code for an access token");
                $this->error_page(WT_I18N::translate("Your Facebook code is invalid. This can happen if you hit back in your browser after login or if Facebook logins have been setup incorrectly by the administrator."));
            }
            $params = null;
            parse_str($response, $params);
            if (empty($params['access_token'])) {
                Log::addErrorLog("Facebook: The access token was empty");
                $this->error_page(WT_I18N::translate("Your Facebook code is invalid. This can happen if you hit back in your browser after login or if Facebook logins have been setup incorrectly by the administrator."));
            }

            $WT_SESSION->facebook_access_token = $params['access_token'];

            $graph_url = "https://graph.facebook.com/" . self::api_dir . "me?access_token="
                . $WT_SESSION->facebook_access_token;
            $meResponse = $this->fetch_url($graph_url);
            if ($meResponse === FALSE) {
                $this->error_page(WT_I18N::translate("Could not fetch your information from Facebook. Please try again."));
            }
            $user = json_decode($meResponse);
            $this->login_or_register($user, $url);
        } else {
            $this->error_page(WT_I18N::translate("The state does not match. You may been tricked to load this page."));
        }
    }

    private function fetch_url($url) {
        try {
            $client = new Zend_Http_Client($url,
                                           array(
                                                 'keepalive' => true,
                                                 'storeresponse' => false
                                                 )
                                           );
            $response = $client->request();
        } catch (Exception $e) {
            Log::addErrorLog($e);
            return false;
        }
        return $response->isError() ? false : $response->getBody();
    }

    private function hidden_input($name, $value) {
        return '<input type="hidden" name="'.$name.'" value="'.$value.'"/>';
    }

    private function user_options($users) {
        $output = '';
        foreach ($users as $user_id => $user) {
            $output .= '<option value="'.$user_id.'">' . $user->user_name . ' (' . $user->real_name . ')</option>';
        }
        return $output;
    }

    private function get_users_with_module_settings() {
	$sql=
		"SELECT u.user_id, user_name, real_name, email, facebook_username.setting_value as facebook_username".
		" FROM `##user` u".
		" LEFT JOIN `##user_setting` facebook_username ON (u.user_id = facebook_username.user_id AND facebook_username.setting_name='facebook_username')".
                " WHERE u.user_id > 0".
                " ORDER BY user_name ASC";
	return WT_DB::prepare($sql)->execute()->fetchAll(PDO::FETCH_OBJ | PDO::FETCH_GROUP);
    }

    private function get_user_id_from_facebook_username($facebookUsername) {
        $statement = WT_DB::prepare(
                                    "SELECT SQL_CACHE user_id FROM `##user_setting` WHERE setting_name=? AND setting_value=?"
                                   );
	return $statement->execute(array(self::user_setting_facebook_username, $this->cleanseFacebookUsername($facebookUsername)))->fetchOne();
    }

    private function facebookProfileLink($username) {
        return '<a href="https://www.facebook.com/'.$username.'"><img src="https://graph.facebook.com/' . self::api_dir .$username.'/picture?type=square" height="25" width="25"/>&nbsp;'.$username.'</a>';
    }

    // Guidelines from https://www.facebook.com/help/105399436216001
    private function cleanseFacebookUsername($username) {
        // Case and periods don't matter
        return strtolower(trim(str_replace('.', '', $username)));
    }

    private function getConnectURL($returnTo='') {
        return WT_SERVER_NAME . WT_SCRIPT_PATH . "module.php?mod=" . $this->getName()
            . "&mod_action=connect" . ($returnTo ? "&url=" . urlencode($returnTo) : ""); // Workaround FB bug where "&url=" (empty value) prevents OAuth
    }

    private function fetchFriendList() {
        global $WT_SESSION, $controller;

        $controller = new WT_Controller_Page();

        $controller->addInlineJavaScript("
            $('head').append('<link rel=\"stylesheet\" href=\"".WT_MODULES_DIR . $this->getName() . "/facebook.css?v=" . WT_FACEBOOK_VERSION."\" />');",
                                         WT_Controller_Page::JS_PRIORITY_LOW);

        $preApproved = unserialize($this->getSetting('preapproved'));

        if (WT_Filter::postArray('preApproved') && WT_Filter::checkCsrf()) {
            $roleRows = WT_Filter::postArray('preApproved');
            $fbUsernames = WT_Filter::postArray('facebook_username', WT_REGEX_USERNAME);
            foreach($fbUsernames as $facebook_username) {
                $facebook_username = $this->cleanseFacebookUsername($facebook_username);
                $this->appendPreapproved($preApproved, $facebook_username, $roleRows);
            }
            $this->setSetting('preapproved', serialize($preApproved));
            WT_FlashMessages::addMessage(WT_I18N::translate('Users successfully imported from Facebook'));
            header("Location: module.php?mod=" . $this->getName() . "&mod_action=admin");
            exit;
        }

        if (empty($WT_SESSION->facebook_access_token)) {
            $this->error_page(WT_I18N::translate("You must <a href='%s'>login to the site via Facebook</a> in order to import friends from Facebook", "index.php?logout=1"));
        }
        $graph_url = "https://graph.facebook.com/" . self::api_dir . "me/friends?fields=first_name,last_name,name,username&access_token="
            . $WT_SESSION->facebook_access_token;
        $friendsResponse = $this->fetch_url($graph_url);
        if ($friendsResponse === FALSE) {
            $this->error_page(WT_I18N::translate("Could not fetch your friends from Facebook. Note that this feature won't work for Facebook Apps created after 2014-04-30 due to a Facebook policy change."));
        }

        $controller
            ->restrictAccess(\WT\Auth::isAdmin())
            ->setPageTitle($this->getTitle())
            ->pageHeader();

        $friends = json_decode($friendsResponse);
        if (empty($friends->data)) {
            $this->error_page(WT_I18N::translate("No friend data"));
            return;
        }

        function nameSort($a, $b) {
            return strcmp($a->last_name . " " . $a->first_name, $b->last_name . " " . $b->first_name);
        }

        usort($friends->data, "nameSort");
        echo "<form id='facebook_friend_list' method='post' action=''>";
        require_once WT_ROOT.'includes/functions/functions_edit.php'; // for select_edit_control
        $index = 0;
        foreach (WT_Tree::getAll() as $tree) {
            $class = ($index++ % 2 ? 'odd' : 'even');
            echo "<label>" . $tree->tree_name_html . " - " .
                WT_I18N::translate('Role') . help_link('role') . ": " .
                select_edit_control('preApproved['.$tree->tree_id.'][canedit]',
                                    $this->get_edit_options(), NULL, NULL) .
                "</label>";
      }

        foreach ($friends->data as $friend) {
            $facebook_username = $this->cleanseFacebookUsername(isset($friend->username) ? $friend->username : $friend->id);

            // Exclude friends who are already pre-approved or are current users
            if (isset($preApproved[$facebook_username])
                || $this->get_user_id_from_facebook_username($facebook_username)) {
                continue;
            }
            echo "<label><input name='facebook_username[]' type='checkbox' value='" .
                $facebook_username . "'/>" .
                $friend->name . "</label>";
        }
        echo WT_Filter::getCsrf();
        echo "<button>Select Friends</button></form>";
    }

    private function login($user_id) {
        global $WT_SESSION;
        $user = User::find($user_id);
        $user_name = $user->getUserName();

        // Below copied from authenticateUser in authentication.php
        $is_admin=$user->getPreference('canadmin');
        $verified=$user->getPreference('verified');
        $approved=$user->getPreference('verified_by_admin');
        if ($verified && $approved || $is_admin) {
            Auth::login($user);
            Log::addAuthenticationLog('Login: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());

            $WT_SESSION->locale    = Auth::user()->getPreference('language');
            $WT_SESSION->theme_dir = Auth::user()->getPreference('theme');
            $WT_SESSION->activity_time = WT_TIMESTAMP;
            $user->setPreference('sessiontime', WT_TIMESTAMP);

            Zend_Session::writeClose();

            return $user_id;
        } elseif (!$is_admin && !$verified) {
            Log::addAuthenticationLog('Login failed ->'.$user_name.'<- not verified');
            return -1;
        } elseif (!$is_admin && !$approved) {
            Log::addAuthenticationLog('Login failed ->'.$user_name.'<- not approved');
            return -2;
        }
        throw new Exception('Login failure: Unexpected condition');
    }

    /**
     * If the Facebook username or email is associated with an account, login to it. Otherwise, register a new account.
     *
     * @param object $facebookUser Facebook user
     * @param string $url          (optional) URL to redirect to afterwards.
     */
    private function login_or_register(&$facebookUser, $url='') {
        $REQUIRE_ADMIN_AUTH_REGISTRATION = WT_Site::getPreference('REQUIRE_ADMIN_AUTH_REGISTRATION');

        if ($this->getSetting('require_verified', 1) && empty($facebookUser->verified)) {
            $this->error_page(WT_I18N::translate('Only verified Facebook accounts are authorized. Please verify your account on Facebook and then try again'));
        }

        if (empty($facebookUser->username)) {
            $facebookUser->username = $facebookUser->id;
        }
        $user_id = $this->get_user_id_from_facebook_username($facebookUser->username);
        if (!$user_id) {
            if (!isset($facebookUser->email)) {
                $this->error_page(WT_I18N::translate('You must grant access to your email address via Facebook in order to use this website. Please uninstall the application on Facebook and try again.'));
            }
            $user = User::findByIdentifier($facebookUser->email);
            if ($user) {
                $user_id = $user->getUserId();
            }
        }

        if ($user_id) { // This is an existing user so log them in if they are approved

            $login_result = $this->login($user_id);
            $message = '';
            switch ($login_result) {
		case -1: // not validated
		    $message=WT_I18N::translate('This account has not been verified.  Please check your email for a verification message.');
                    break;
		case -2: // not approved
                    $message=WT_I18N::translate('This account has not been approved.  Please wait for an administrator to approve it.');
                    break;
                default:
                    $user = User::find($user_id);
                    $user->setPreference(self::user_setting_facebook_username, $this->cleanseFacebookUsername($facebookUser->username));
                    // redirect to the homepage/$url
                    header('Location: ' . WT_SCRIPT_PATH . $url);
                    return;
            }
            $this->error_page($message);
        } else { // This is a new Facebook user who may or may not already have a manual account

            if (!WT_Site::getPreference('USE_REGISTRATION_MODULE')) {
                $this->error_page('<p>' . WT_I18N::translate('The administrator has disabled registrations.') . '</p>');
            }

            // check if the username is already in use
            $username = $this->cleanseFacebookUsername($facebookUser->username);
            $wt_username = substr($username, 0, 32); // Truncate the username to 32 characters to match the DB.

            if (User::findByIdentifier($wt_username)) {
                // fallback to email as username since we checked above that a user with the email didn't exist.
                $wt_username = $facebookUser->email;
                $wt_username = substr($wt_username, 0, 32); // Truncate the username to 32 characters to match the DB.
            }

            // Generate a random password since the user shouldn't need it and can always reset it.
            $password = md5(uniqid(rand(), TRUE));
            $hashcode = md5(uniqid(rand(), true));
            $preApproved = unserialize($this->getSetting('preapproved'));

            // From login.php:
            Log::addAuthenticationLog('User registration requested for: ' . $wt_username);
            if ($user = User::create($wt_username, $facebookUser->name, $facebookUser->email, $password)) {
                $verifiedByAdmin = !$REQUIRE_ADMIN_AUTH_REGISTRATION || isset($preApproved[$username]);

                $user
                    ->setPreference(self::user_setting_facebook_username, $this->cleanseFacebookUsername($facebookUser->username))
                    ->setPreference('language',          WT_LOCALE)
                    ->setPreference('verified',          '1')
                    ->setPreference('verified_by_admin', $verifiedByAdmin ? '1' : '0')
                    ->setPreference('reg_timestamp',     date('U'))
                    ->setPreference('reg_hashcode',      $hashcode)
                    ->setPreference('contactmethod',     'messaging2')
                    ->setPreference('visibleonline',     '1')
                    ->setPreference('editaccount',       '1')
                    ->setPreference('auto_accept',       '0')
                    ->setPreference('canadmin',          '0')
                    ->setPreference('sessiontime',       $verifiedByAdmin ? WT_TIMESTAMP : '0')
                    ->setPreference('comment',
                                    @$facebookUser->birthday . "\n " .
                                    "https://www.facebook.com/" . $this->cleanseFacebookUsername($facebookUser->username));

                // Apply pre-approval settings
                if (isset($preApproved[$username])) {
                    $userSettings = $preApproved[$username];

                    foreach ($userSettings as $gedcom => $userGedcomSettings) {
                        foreach (array('gedcomid', 'rootid', 'canedit') as $userPref) {
                            if (empty($userGedcomSettings[$userPref])) {
                                continue;
                            }

                            // Use a direct DB query instead of $tree->setUserPreference since we
                            // can't get a reference to the WT_Tree since it checks permissions but
                            // we are trying to give the permissions.
                            WT_DB::prepare(
                                           "REPLACE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, LEFT(?, 255))"
                                           )->execute(array($user->getUserId(),
                                                            $gedcom,
                                                            $userPref,
                                                            $userGedcomSettings[$userPref]));
                        }
                    }
                    // Remove the pre-approval record
                    unset($preApproved[$username]);
                    $this->setSetting('preapproved', serialize($preApproved));
                }

                // We need jQuery below
                global $controller;
                $controller = new WT_Controller_Page();
                $controller
                    ->setPageTitle($this->getTitle())
                    ->pageHeader();

                echo '<form id="verify-form" name="verify-form" method="post" action="', WT_LOGIN_URL, '" class="ui-autocomplete-loading" style="width:16px;height:16px;padding:0">';
                echo $this->hidden_input("action", "verify_hash");
                echo $this->hidden_input("user_name", $wt_username);
                echo $this->hidden_input("user_password", $password);
                echo $this->hidden_input("user_hashcode", $hashcode);
                echo WT_Filter::getCsrf();
                echo '</form>';

                if ($verifiedByAdmin) {
                    $controller->addInlineJavaScript('
function verify_hash_success() {
  // now the account is approved but not logged in. Now actually login for the user.
  window.location = "' . $this->getConnectURL($url) . '";
}

function verify_hash_failure() {
  alert("' . WT_I18N::translate("There was an error verifying your account. Contact the site administrator if you are unable to access the site.")  . '");
  window.location = "' . WT_SCRIPT_PATH . '";
}
$(document).ready(function() {
  $.post("' . WT_LOGIN_URL . '", $("#verify-form").serialize(), verify_hash_success).fail(verify_hash_failure);
});
');
                } else {
                    echo '<script>document.getElementById("verify-form").submit()</script>';
                }

            } else {
                Log::addErrorLog("Facebook: Couldn't create the user account");
                $this->error_page('<p>' . WT_I18N::translate('Unable to create your account.  Please try again.') . '</p>' .
                                  '<div class="back"><a href="javascript:history.back()">' . WT_I18N::translate('Back') . '</a></div>');
            }
        }
    }

    private function error_page($message) {
        global $controller, $WT_SESSION;

        try {
            unset($WT_SESSION->facebook_access_token);
            unset($WT_SESSION->facebook_state);
            Zend_Session::writeClose();
        } catch (Exception $e) { }

        $controller = new WT_Controller_Page();
        $controller
            ->setPageTitle($this->getTitle())
            ->pageHeader();

        try {
            WT_FlashMessages::addMessage($message);
        } catch (Zend_Session_Exception $zse) {
            echo '<div class="warning">'.$message.'</div>';
        }
        exit;
    }

    private function print_findindi_link($element_id, $indiname='', $gedcomTitle=WT_GEDURL) {
	return '<a href="#" tabindex="-1"
                   onclick="findIndi(document.getElementById(\''.$element_id.'\'), document.getElementById(\''.$indiname.'\'), \''.$gedcomTitle.'\'); return false;"
                   class="icon-button_indi" title="'.WT_I18N::translate('Find an individual').'"></a>';
    }

    private function indiField($field, $value='', $gedcomTitle=WT_GEDURL) {
        return '<input type="text" size="5" name="'.$field.'" id="'.$field.'" value="'.htmlspecialchars($value).'"> '
            . $this->print_findindi_link($field, '', $gedcomTitle);
    }

    /* Inject JS into some pages (via a menu) to show the Facebook login button */

    // Implement WT_Module_Menu
    public function defaultMenuOrder() {
        return 999;
    }

    // Implement WT_Module_Menu
    public function getMenu() {
        // We don't actually have a menu - this is just a convenient "hook" to execute
        // code at the right time during page execution
        global $controller, $THUMBNAIL_WIDTH;

        if (!$this->isSetup()) {
            return null;
        }

        $controller->addExternalJavascript(WT_MODULES_DIR . $this->getName() . '/facebook.js?v=' . WT_FACEBOOK_VERSION);
        /* Stylesheets added by addExternalStylesheet are never output
        if (method_exists($controller, 'addExternalStylesheet')) {
          $controller->addExternalStylesheet(WT_MODULES_DIR . $this->getName() . '/facebook.css?v=' . WT_FACEBOOK_VERSION); // Only in 1.3.3+
        } else {
         */
          $controller->addInlineJavaScript("
            var FACEBOOK_LOGIN_TEXT = '" . addslashes(WT_I18N::translate('Login with Facebook')) . "';
            $('head').append('<link rel=\"stylesheet\" href=\"".WT_MODULES_DIR . $this->getName() . "/facebook.css?v=" . WT_FACEBOOK_VERSION."\" />');" .
              ($this->hideStandardForms ? '$(document).ready(function() {$("#login-form[name=\'login-form\'], #register-form").hide();});' : ""),
            WT_Controller_Page::JS_PRIORITY_NORMAL);
        //}

          // Use the Facebook profile photo if there isn't an existing photo
          if (!empty($controller->record) && method_exists($controller->record, 'findHighlightedMedia')
              && !$controller->record->findHighlightedMedia()) {
              $fbUsername = $this->getFacebookUsernameForINDI($controller->record);
              if ($fbUsername) {
                  $fbPicture = 'https://graph.facebook.com/' . self::api_dir . $fbUsername . '/picture';
                  $controller->addInlineJavaScript('$(document).ready(function() {' .
                      '$("#indi_mainimage").html("<a class=\"gallery\" href=\"'.$fbPicture.'?width=' .
                      self::profile_photo_large_width.'\" data-obje-url=\"'.$fbPicture.'?type=large\">' .
                      '<img width=\"'.$THUMBNAIL_WIDTH.'\" src=\"'.$fbPicture.'?width='.$THUMBNAIL_WIDTH.'\"/>' .
                      '</a>");' .
                 '});');
              }
          }


        return null;
    }

    public function getFacebookUsernameForINDI($indi) {
        global $WT_TREE;

        // If they have an account, look for the link on their user record.
        if ($user = User::findByGenealogyRecord($WT_TREE, $indi)) {
            return $user->getPreference(self::user_setting_facebook_username);
        }

        // Otherwise, look in the list of pre-approved users.
        $preApproved = unserialize($this->getSetting('preapproved'));
        if (empty($preApproved)) {
            return NULL;
        }

        foreach ($preApproved as $fbUsername => $details) {
            if ($indi->getXref() == @$details[$WT_TREE->tree_id]['gedcomid']) {
                return $fbUsername;
            }
        }
        return NULL;
    }

}
