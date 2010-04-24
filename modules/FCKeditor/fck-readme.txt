The FCKeditor module allows webtrees to use the FCKeditor for adding and editing
news items for the home page. 
This module has been tested with the FCKeditor 2.6.6 on April 24th, 2010.

To install this module, download FCKeditor from the official
website (see below) and upload it to the modules/FCKeditor
directory. make sure that the directory structure is:
PhpGedView directory\
----modules\
--------FCKeditor\
------------editor\....
----------------......

As long as this is present, webtrees will use this as the default editor as opposed to a plain textarea.


The FCKeditor homepage is at:
http://www.fckeditor.net
Sourceforge Project page is at:
http://sourceforge.net/projects/fckeditor/


- Note that the folder name /modules/FCKeditor in webtrees is case sensitive.
  FCK may default to lower case 'fckeditor/' on install which will not work with webtrees.
  The fix is simply to rename the folder.
- Note also that any folders or files that start with '_' (eg '_samples' or '_whatsnew.html')
  are not necessary, so can be safely deleted.
