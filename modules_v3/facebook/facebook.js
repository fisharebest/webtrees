/*
  Facebook Module for webtrees

  Copyright (C) 2012 Matthew N.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// from http://stackoverflow.com/a/5158301
function facebook_getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

$(document).ready(function init_facebook() {
    var fbForm = $(
    '<div id="facebook-login-box" style="display: inline-block">' +
    '<form id="login-form" action="module.php?mod=facebook&mod_action=connect" method="post">' +
    '<input type="hidden" name="url" value="" id="facebook_return_url"/>' +
    '<input type="hidden" name="csrf" value="" id="facebook_connect_csrf"/>' +
    '<input type="hidden" name="timediff" value=""/>' +
    '<button id="facebook-login-button">' + FACEBOOK_LOGIN_TEXT + '</button>' +
    '</form>' +
    '</div>');
    fbForm.find("#facebook_connect_csrf").attr('value', WT_CSRF_TOKEN);
    fbForm.find("#facebook_return_url").attr('value', facebook_getParameterByName('url'));
    fbForm.find("#login-form")[0].addEventListener("submit", function(evt) {
        this.timediff.value = (new Date()).getTimezoneOffset() * 60;
    });
    $("#login-form, #register-form").before(fbForm);
});
