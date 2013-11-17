Fancy Research Links
====================

Fancy Research Links Module for webtrees 1.5x

A sidebar module that provides quick links to popular research web sites, using the individuals name as the search reference.

At the moment mainly Dutch research web sites are added as plugin. You can extend the list of possible research sites by making your own plugin.
Look in the pluginfolder of the module for examples.

A quick guide to add your own plugin:
1. Take a copy of one of the existing files from the modules plugin folder and rename it. Note: not all plugin files are exactly the same. Some have more variables depending on the research site they are linking to.
2 In the new file, change the class to match the file name and change the name of the link to something suitable;
3. At the research site you are linking to, perform a simple name only search, and note the URL the search generates. Use this to change the output of the link in your new plugin file, the section starting with return $link = ...... taking careful note where the variables need to be inserted.
4. If you made a plugin that could be interesting for other users you can do a pull request or send me a copy.

NOTE: THIS MODULE IS PROVIDED AS IS. USE IT AND ENJOY IT BUT DON'T ASK ME TO MAKE PLUGINS FOR YOU.