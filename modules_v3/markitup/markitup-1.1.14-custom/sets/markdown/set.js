// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// MarkDown tags example
// http://en.wikipedia.org/wiki/Markdown
// http://daringfireball.net/projects/markdown/
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
/*global $, WT_MODULES_DIR, WT_CSRF_TOKEN */

// mIu nameSpace to avoid conflict.
var miu = {
	markdownTitle: function (markItUp, char) {
		'use strict';
		var i, n, heading = '';
		n = $.trim(markItUp.selection || markItUp.placeHolder).length;
		for (i = 0; i < n; i += 1) {
			heading += char;
		}
		return '\n' + heading;
	}
};

var mySettings = {
	onShiftEnter: {keepDefault: false, openWith: '\n\n'},
	markupSet: [
		{name: 'First Level Heading', key: '1', placeHolder: 'Your title here...',
			closeWith: function (markItUp) {
				'use strict';
				return miu.markdownTitle(markItUp, '=');
			}},
		{name: 'Second Level Heading', key: '2', placeHolder: 'Your title here...',
			closeWith: function (markItUp) {
				'use strict';
				return miu.markdownTitle(markItUp, '-');
			}},
		{name: 'Heading 3', key: '3', openWith: '### ', placeHolder: 'Your title here...' },
		{name: 'Heading 4', key: '4', openWith: '#### ', placeHolder: 'Your title here...' },
		{name: 'Heading 5', key: '5', openWith: '##### ', placeHolder: 'Your title here...' },
		{name: 'Heading 6', key: '6', openWith: '###### ', placeHolder: 'Your title here...' },
		{separator: '---------------' },
		{name: 'Bold', key: 'B', openWith: '**', closeWith: '**'},
		{name: 'Italic', key: 'I', openWith: '_', closeWith: '_'},
		{separator: '---------------' },
		{name: 'Bulleted List', openWith: '- ' },
		{name: 'Numeric List', openWith: function (markItUp) {
			'use strict';
			return markItUp.line + '. ';
		}},
		{separator: '---------------' },
		{name: 'Picture', key: 'P', replaceWith: '![[![Alternative text]!]]([![Url: !: http: //]!] "[![Title]!]")'},
		{name: 'Link', key: 'L', openWith: '[', closeWith: ']([![Url: !: http: //]!] "[![Title]!]")', placeHolder: 'Your text to link here...' },
		{separator: '---------------'},
		{name: 'Quotes', openWith: '> '},
		{name: 'Code Block / Code', openWith: '(!(\t|!|`)!)', closeWith: '(!(`)!)'},
		{separator: '---------------'},
		{name: 'Preview', call: 'preview', className: "preview"}
	]
};
