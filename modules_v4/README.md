# THIRD-PARTY MODULES

Many webtrees functions are provided by “modules”.
Modules allows you to add additional features to webtrees and modify existing features.

## Installing and uninstalling modules

A module is a folder containing a file called `module.php`.
There may be other files in the folder, such as CSS, JS, templates,
languages, data, etc.

To install a module, copy its folder to `/modules_v4`.

To uninstall it, delete its folder from `/modules_v4`.

Note that module names (i.e. the folder names) must not contain
spaces or the characters `.`, `[` and `]`.  It must also have a
maximum length of 30 characters.

TIP: renaming a module from `<module>` to `<module.disable>`
is a quick way to hide it from webtrees.  This works because
modules containing `.` are ignored.

## Writing modules

To write a module, you need to understand the PHP programming language.

There are several example modules available at
https://github.com/webtrees

The built-in modules can be found in `app/Module/`.
These contain lots of useful examples that you can copy/paste.
