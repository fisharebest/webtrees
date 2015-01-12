/*global jQuery, confirm, WT_CSRF_TOKEN, window, modalDialog */
/*exported blog */
// functions for blog module
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

String.prototype.truncate =
	function (n) {
		'use strict';
		var p = new RegExp ("^.{0," + n + "}[\\S]*", 'g'),
			re = this.match (p),
			l = re[0].length;

		re = re[0].replace (/\s$/, '');
		if (l < this.length) {
			return re + ' \u2026';
		}
	};

var blog = (function () {
	'use strict';

	var instance,
		parms;

	function create() {
		jQuery ('body')
			.on ('click', '.blog_block a', function (e) {
			e.preventDefault ();
			var self = jQuery (this),
				ctype = self.parents ('.block').attr ('id').split ('_').shift (),
				article = self.closest ('.blog_article'),
				news_id = article.length ? article.attr ('id').split ('_').pop () : null,
				archived;

			switch (jQuery (this).attr ('href')) {
				case '#add':
					modalDialog (parms.cmd + 'edit&ctype=' + ctype, parms.title.replace ('%s', parms.add), parms.width);
					break;
				case '#edit':
					modalDialog (parms.cmd + 'edit&ctype=' + ctype + '&news_id=' + news_id, parms.title.replace ('%s', parms.edit), parms.width);
					break;
				case '#delete':
					var txt = jQuery ('#blog_' + news_id).text ().truncate (60);
					if (confirm (parms.del + '\n\n' + txt)) {
						jQuery.post (parms.cmd + 'delete', {
							csrf: WT_CSRF_TOKEN,
							news_id: news_id
						})
						.done (function () {
							window.location.reload ();
						})
						.fail(function(){
							window.alert('Deletion failed');
						});
					}
					break;
				case '#archive':
					archived = jQuery ('.blog_archive');
					if (archived.filter (':hidden').length) {
						//show
						archived.slideDown (function(){
							self.html (self.data('hide'))
								.attr ('title', self.data('hide'));
						});
					} else {
						//hide
						archived.slideUp (function(){
							self.html (self.data('view'))
								.attr ('title', self.data('view'));
						});
					}
					break;
				default:
				//do nothing
			}
		});

		return {
			init: function (p) {
				parms = parms || p;
			}
		};
	}


	return {
		getInstance: function () {
			if (!instance) {
				instance = create ();
			}
			return instance;
		},
		version: '1.6.3'
	};

}) ();
