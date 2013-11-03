/**
 * @license Copyright (c) CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.plugins.add('wordcount', {
    lang: ['ca', 'de', 'en', 'es', 'fr', 'no', 'pl'],
    init: function (editor) {
        if (editor.elementMode === CKEDITOR.ELEMENT_MODE_INLINE) {
            return;
        }

        var defaultFormat = '<span class="cke_path_item">',
            intervalId,
            lastWordCount,
            lastCharCount = 0,
            limitReachedNotified = false,
            limitRestoredNotified = false;

        // Default Config
        var defaultConfig = {
            showWordCount: true,
            showCharCount: false,
            charLimit: 'unlimited',
            wordLimit: 'unlimited',
            countHTML: false
        };

        // Get Config & Lang
        var config = CKEDITOR.tools.extend(defaultConfig, editor.config.wordcount || {}, true);

        if (config.showCharCount) {
            var charLabel = editor.lang.wordcount[config.countHTML ? 'CharCountWithHTML' : 'CharCount'];

            defaultFormat += charLabel + '&nbsp;%charCount%';

            if (config.charLimit != 'unlimited') {
                defaultFormat += '&nbsp;(' + editor.lang.wordcount.limit + '&nbsp;' + config.charLimit + ')';
            }
        }

        if (config.showCharCount && config.showWordCount) {
            defaultFormat += ',&nbsp;';
        }

        if (config.showWordCount) {
            defaultFormat += editor.lang.wordcount.WordCount + ' %wordCount%';

            if (config.wordLimit != 'unlimited') {
                defaultFormat += '&nbsp;(' + editor.lang.wordcount.limit + '&nbsp;' + config.wordLimit + ')';
            }
        }

        defaultFormat += '</span>';

        var format = defaultFormat;

        CKEDITOR.document.appendStyleSheet(this.path + 'css/wordcount.css');

        function counterId(editorInstance) {
            return 'cke_wordcount_' + editorInstance.name;
        }

        function counterElement(editorInstance) {
            return document.getElementById(counterId(editorInstance));
        }

        function strip(html) {
            var tmp = document.createElement("div");
            tmp.innerHTML = html;

            if (tmp.textContent == '' && typeof tmp.innerText == 'undefined') {
                return '0';
            }
            return tmp.textContent || tmp.innerText;
        }

        function updateCounter(editorInstance) {
            var wordCount = 0,
                charCount = 0,
                normalizedText,
                text;

            if (text = editorInstance.getData()) {
                if ((!config.countHTML && config.showCharCount) || config.showWordCount) {
                    normalizedText = text.
                        replace(/(\r\n|\n|\r)/gm, " ").
                        replace(/^\s+|\s+$/g, '').
                        replace("&nbsp;", " ");

                    normalizedText = strip(normalizedText);
                }

                if (config.showCharCount) {
                    charCount = config.countHTML ? text.length : normalizedText.length;
                }

                if (config.showWordCount) {
                    wordCount = normalizedText.split(/\s+/).length;
                }
            }
            var html = format.replace('%wordCount%', wordCount).replace('%charCount%', charCount);

            counterElement(editorInstance).innerHTML = html;

            if (charCount == lastCharCount) {
                return true;
            }
            
            lastWordCount = wordCount;
            lastCharCount = charCount;

            // Check for word limit
            if (config.showWordCount && wordCount > config.wordLimit) {
                limitReached(editor, limitReachedNotified);
            } else if (!limitRestoredNotified && wordCount < config.wordLimit) {
                limitRestored(editor);
            }

            // Check for char limit
            if (config.showCharCount && charCount > config.charLimit) {
                limitReached(editor, limitReachedNotified);
            } else if (!limitRestoredNotified && charCount < config.charLimit) {
                limitRestored(editor);

            }

            return true;
        }

        function limitReached(editorInstance, notify) {
            limitReachedNotified = true;
            limitRestoredNotified = false;

            editorInstance.execCommand('undo');
            if (!notify) {
                counterElement(editorInstance).className += " cke_wordcountLimitReached";

                editorInstance.fire('limitReached', {}, editor);
            }
            // lock editor
            editorInstance.config.Locked = 1;
            editorInstance.fire("change");
        }

        function limitRestored(editorInstance) {
            limitRestoredNotified = true;
            limitReachedNotified = false;
            editorInstance.config.Locked = 0;

            counterElement(editorInstance).className = "cke_wordcount";
        }

        editor.on('uiSpace', function (event) {
            if (event.data.space == 'bottom') {
                event.data.html += '<div id="' + counterId(event.editor) + '" class="cke_wordcount" style=""' + ' title="' + editor.lang.wordcount.title + '"' + '>&nbsp;</div>';
            }
        }, editor, null, 100);
        editor.on('dataReady', function (event) {
            var count = event.editor.getData().length;
            if (count > config.wordLimit) {
                limitReached(editor);
            }
            updateCounter(event.editor);
        }, editor, null, 100);
        editor.on('key', function (event) {
            updateCounter(event.editor);
        }, editor, null, 100);
        editor.on('afterPaste', function (event) {
            updateCounter(event.editor);
        }, editor, null, 100);
        /* editor.on('change', function (event) {
             updateCounter(event.editor);
         }, editor, null, 100);*/
        /*editor.on('focus', function (event) {
            editorHasFocus = true;
            intervalId = window.setInterval(function () {
                updateCounter(editor);
            }, 300, event.editor);
        }, editor, null, 300);*/
        editor.on('blur', function () {
            if (intervalId) {
                window.clearInterval(intervalId);
            }
        }, editor, null, 300);
        
        if (!String.prototype.trim) {
            String.prototype.trim = function () {
                return this.replace(/^\s+|\s+$/g, '');
            };
        }
    }
});
