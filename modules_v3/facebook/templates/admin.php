<?php
// Facebook Module for webtrees
//
// Copyright (C) 2013 Matthew N.
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

$usernameValidationAttrs = 'pattern="[.a-zA-Z0-9]{5,}" title="' . WT_I18N::translate("Facebook usernames can only contain alphanumeric characters (A-Z, 0-9) or a period") . '"';

?>

<link rel="stylesheet" href="<?php echo WT_MODULES_DIR . $this->getName(); ?>/facebook.css?v=<?php echo  WT_FACEBOOK_VERSION; ?>" />
<h3><?php echo $this->getTitle(); ?></h3>
<div>
  <strong><?php echo WT_I18N::translate('Version: ')?></strong><?php echo WT_FACEBOOK_VERSION; ?>
  <span id="updateBanner" class="ui-state-highlight"></span>
</div>

<hr/>
<h4><?php echo WT_I18N::translate('Facebook API'); ?></h4>
<form method="post" action="">
  <?php echo WT_Filter::getCsrf(); ?>
  <p><?php echo WT_I18N::translate('The App ID and secret can be setup at %s.', '<a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a>'); ?></p>
  <label>
    <?php echo WT_I18N::translate('App ID:'); ?>
    <input type="text" name="app_id" value="<?php echo $this->getSetting('app_id', ''); ?>" />
  </label>
  <label>
    <?php echo WT_I18N::translate('App Secret:'); ?>
    <input type="text" name="app_secret" value="<?php echo $this->getSetting('app_secret', ''); ?>" size="40" />
  </label>
  <?php if (!WT_Site::getPreference('USE_REGISTRATION_MODULE')) { ?>
  <p><strong><?php echo WT_I18N::translate('NOTE: New user registration is disabled in Site configuration so only existing users will be able to login.');?></strong></p>
  <?php } ?>
  <p>
    <label>
      <input type="checkbox" name="require_verified" value="1"<?php echo ($this->getSetting('require_verified', 1) ? 'checked="checked" ' : ''); ?> />
      <?php echo WT_I18N::translate('Require verified Facebook accounts'); ?>
      <em>(<?php echo WT_I18N::translate('Only disable for testing'); ?>)</em>
    </label>
  </p>
  <p>
    <label>
      <input type="checkbox" name="hide_standard_forms" value="1"<?php echo ($this->getSetting('hide_standard_forms', 0) ? 'checked="checked" ' : ''); ?> />
      <?php echo WT_I18N::translate('Hide regular log-in and registration forms'); ?>
    </label>
  </p>
  <p><input type="submit" name="saveAPI" value="<?php echo WT_I18N::translate('Save'); ?>"></p>
</form>

<hr/>

<h4><?php echo WT_I18N::translate('Linked users');?></h4>
<form method="post" action="">
  <?php echo WT_Filter::getCsrf(); ?>
  <p><?php echo WT_I18N::translate("Associate a webtrees user with a Facebook account."); ?></p>
<table>
  <thead>
    <tr>
      <th><?php echo WT_I18N::translate('webtrees Username'); ?></th>
      <th><?php echo WT_I18N::translate('Real name'); ?></th>
      <th><?php echo WT_I18N::translate('Facebook Account'); ?></th>
      <th><?php echo WT_I18N::translate('Unlink'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
      if (!empty($linkedUsers)) {
        $index = 0;
        foreach ($linkedUsers as $user_id => $user) {
          $class = ($index++ % 2 ? 'odd' : 'even');
          echo '
    <tr class="'.$class.'">
      <td><a href="admin_users.php?filter='.$user->user_name.'">'.$user->user_name.'</a></td>
      <td><a href="admin_users.php?filter='.$user->user_name.'">'.$user->real_name.'</a></td>
      <td>'.$this->facebookProfileLink($user->facebook_username).'</td>
      <td style="text-align: center;"><button name="deleteLink" value="'.$user_id.'" class="icon-delete" formnovalidate="formnovalidate" style="border:none;"></button></td>
    </tr>';
        }
      }
    ?>
    <tr>
      <td colspan="2"><select name="user_id"><?php echo $unlinkedOptions; ?></select></td>
      <td><input type="text" name="facebook_username" required="required" <?php echo $usernameValidationAttrs; ?> /></td>
      <td><input type="submit" name="addLink" value="<?php echo WT_I18N::translate('Add'); ?>"></td>
    </tr>
  </tbody>
</table>
</form>

<hr/>

<h4><?php echo WT_I18N::translate('Pre-approve users'); ?></h4>
<form method="post" action="">
  <?php echo WT_Filter::getCsrf(); ?>
  <p><?php echo WT_I18N::translate("If you know a user's Facebook username but they don't have an account in webtrees, you can pre-approve one so they can login immediately the first time they visit."); ?></p>
  <ul>
    <li><a href="?mod=facebook&mod_action=admin_friend_picker">
      <?php echo WT_I18N::translate("Import from your Facebook friends"); ?>
    </a></li>
  </ul>
<p><input type="submit" name="savePreapproved" value="<?php echo WT_I18N::translate('Save'); ?>"></p>
<table id="preapproved">
  <thead>
    <tr>
      <th rowspan="2"><?php echo WT_I18N::translate('Facebook Account'); ?></th>
      <?php
        $index = 0;
        foreach (WT_Tree::getAll() as $tree) {
          echo '<th colspan="3" class="'.($index++ % 2 ? 'odd' : 'even').'">', $tree->tree_name_html, '</th>';
        }
      ?>
    </tr>
    <tr>
      <?php
      $index = 0;
      foreach (WT_Tree::getAll() as $tree) {
        $class = ($index++ % 2 ? 'odd' : 'even');
?>
      <th class="<?php echo $class; ?>"><?php echo WT_I18N::translate('Default individual'), help_link('default_individual'); ?></th>
      <th class="<?php echo $class; ?>"><?php echo WT_I18N::translate('Individual record'), help_link('useradmin_gedcomid'); ?></th>
      <th class="<?php echo $class; ?>"><?php echo WT_I18N::translate('Role'), help_link('role'); ?></th>

      <?php } ?>
    </tr>
  </thead>
  <tbody>
    <tr class="preapproved_row_add">
      <td><input type="text" name="preApproved_new_facebook_username" <?php echo $usernameValidationAttrs; ?> size="18"/></td>
      <?php
        $index = 0;
        foreach (WT_Tree::getAll() as $tree) {
          $class = ($index++ % 2 ? 'odd' : 'even');
          echo '<td class="'.$class.'">',
          $this->indiField('preApproved[new]['.$tree->tree_id.'][rootid]',
                           '', $tree->tree_name_url), '</td>',
          '<td class="'.$class.'">',
          $this->indiField('preApproved[new]['.$tree->tree_id.'][gedcomid]',
                           '', $tree->tree_name_url), '</td>',
          '<td class="'.$class.'">',
          select_edit_control('preApproved[new]['.$tree->tree_id.'][canedit]',
                              $this->get_edit_options(), NULL, NULL), '</td>';
        }
      ?>
    </tr>
    <?php
      if (!empty($preApproved)) {
        ksort($preApproved);
        foreach ($preApproved as $fbUsername => $details) {
          echo '
<tr>
      <td nowrap="nowrap">' . $this->facebookProfileLink($fbUsername) . '</td>';
          $index = 0;
          foreach (WT_Tree::getAll() as $tree) {
            $class = ($index++ % 2 ? 'odd' : 'even');
            echo '<td class="'.$class.'">',
            $this->indiField('preApproved['.$fbUsername.']['.$tree->tree_id.'][rootid]',
                             @$details[$tree->tree_id]['rootid'], $tree->tree_name_url), '</td>',
            '<td class="'.$class.'">',
            $this->indiField('preApproved['.$fbUsername.']['.$tree->tree_id.'][gedcomid]',
                             @$details[$tree->tree_id]['gedcomid'], $tree->tree_name_url), '</td>',
            '<td class="'.$class.'">',
            select_edit_control('preApproved['.$fbUsername.']['.$tree->tree_id.'][canedit]',
                                $this->get_edit_options(), NULL, @$details[$tree->tree_id]['canedit']),
	    '</td>';
          }
          echo '
      <td><button name="deletePreapproved" value="'.$fbUsername.'" class="icon-delete"></button></td>
    </tr>';
        }
      }
    ?>
  </tbody>
</table>
</form>
<script>
function paste_id(value) {
  pastefield.value=value;
}

function update_check() {
    $.ajax("<?php echo WT_FACEBOOK_UPDATE_CHECK_URL; ?>", {
        headers: { "Accept": "application/vnd.github.v3.raw+json" },
        dataType: "text"
    }).done(function(data) {
        var versions = JSON.parse(data);
        if (versions.latest_version > "<?php echo WT_FACEBOOK_VERSION; ?>") {
            var updateText = "<?php echo WT_Filter::escapeJs(WT_I18N::translate('An update to this module is available: ')); ?>";
           $("#updateBanner").html(updateText + "<a href='" + versions.website + "'>" + versions.latest_version + " (" + versions.latest_release_date + ")</a>");
        }
    });
}

window.addEventListener("load", update_check);
</script>
