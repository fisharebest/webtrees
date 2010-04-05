The FCKeditor module allows PGV to iuse the FCKeditor for adding and editing
news items for the home page. 
This module has been tested with the FCKeditor 2.0 on August 7th, 2005.  The
code in PGV required to use this is only in PGV 3.3.5 and greater.

To install this module, download FCKeditor from the official
website (see below) and upload it to the modules/FCKeditor
directory. make sure that the directory structure is:
PhpGedView directory\
----modules\
--------FCKeditor\
------------editor\....
----------------......

As long as this is present, PGV will use this as the default editor as opposed to a plain textarea.


The FCKeditor homepage is at:
http://www.fckeditor.net
Sourceforge Project page is at:
http://sourceforge.net/projects/fckeditor/


--------------------------------------------------------------------------------------
UPDATED 16 JUL 2007:
---------------------------------------------------------------------------------------
1 - FCKeditor latest release (2.4.3) installed and tested succesfully on PGV 4.1b6.
2 - Note that the folder name /modules/FCKeditor in PGV is case sensitive.
    FCK may default to lower case 'fckeditor/' on install which will not work with PGV.
     The fix is simply to rename the folder.
3 - Note also that any folders or files that start with '_' (eg '_samples' or '_whatsnew.html')
     are not necessary, so can be safely deleted.
