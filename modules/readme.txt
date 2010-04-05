=======================================================
    PhpGedView

    Version 4.0
    Copyright 2005 John Finlay and others

    This and other information can be found online at
    http://www.PhpGedView.net

    # $Id$
=======================================================

CONTENTS
     1.  LICENSE
     2.  INTRODUCTION
     3.  CUSTOM MENUS

-------------------------------------------------------
LICENSE

webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
Copyright (C) 2002 to 2005  John Finlay and Others

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

See the file GPL.txt included with this software for more
detailed licensing information.

-------------------------------------------------------
INTRODUCTION

Modules are additional software that can be added to the core PhpGedView.
They provide additional functionality not part of the core PhpGedView 
distribution. Some modules integrate very tightly with the PhpGedView
software while others are separate software that loosely links to PhpGedView,
such as the PunBB bulletin board system

Like PhpGedView, modules available for download are Open Source software that
has been produced by people from many countries freely donating their time
and talents to the project, though there is nothing to prevent someone from
developing a source module that is not open source. 


-------------------------------------------------------
CUSTOM MENUS

With PGV version 4.0, there is a new system in place for adding menus to the
main PGV Menu bar.  To do this, create a file called menu.php in your module
folder.  This menu.php should implement an interface which implements the 
getMenu() method. The name of the class must be prefixed by the module name 
and an underscore.  

As an example, if you were building the "acme" module, you would could the file
"acme/menu.php" which would have a class definition similar to the following:
 
class acme_ModuleMenu { 
	function &getMenu() { 
		$menu = new Menu(); 
		return $menu; 
	} 
} 