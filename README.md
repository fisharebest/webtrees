[![Latest Stable Version](https://poser.pugx.org/fisharebest/webtrees/v/stable.svg)](https://packagist.org/packages/fisharebest/webtrees)
[![Build Status](https://travis-ci.org/fisharebest/webtrees.svg?branch=master)](https://travis-ci.org/fisharebest/webtrees)
[![Coverage Status](https://coveralls.io/repos/github/fisharebest/webtrees/badge.svg?branch=master)](https://coveralls.io/github/fisharebest/webtrees?branch=master)
[![Translation status](https://translate.webtrees.net/widgets/webtrees/-/svg-badge.svg)](https://translate.webtrees.net/engage/webtrees/?utm_source=widget)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/78a5ba19-7ddf-4a58-8262-1c8a149f38de/mini.png)](https://insight.sensiolabs.com/projects/78a5ba19-7ddf-4a58-8262-1c8a149f38de)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/webtrees/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/webtrees/?branch=master)
[![Code Climate](https://codeclimate.com/github/fisharebest/webtrees/badges/gpa.svg)](https://codeclimate.com/github/fisharebest/webtrees)

# webtrees

The project’s website is [www.webtrees.net](https://www.webtrees.net).
Further documentation is available at [wiki.webtrees.net](https://wiki.webtrees.net).


## Contents

* [License](#license)
* [Introduction](#introduction)
* [System requirements](#system-requirements)
* [Installation](#installation)
* [Upgrading](#upgrading)
* [Gedcom (family tree) files](#gedcom-family-tree-files)
* [Security](#security)
* [Backup](#backup)
* [Converting from phpgedview](#converting-from-phpgedview)


### License

* **webtrees: online genealogy**
* Copyright (C) 2016 webtrees development team

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.


### Introduction

**webtrees** is the web's leading online collaborative genealogy application.

* It works from standard GEDCOM files, and is therefore compatible with every
major desktop application.
* It aims to to be efficient and effective by using the right combination of
third-party tools, design techniques and open standards.

**webtrees** allows you to view and edit your genealogy on your website. It has
full editing capabilities, full privacy functions, and supports imedia such as
photos and document images. As an online program, it fosters extended family
participation and good ancestral recording habits, as it simplifies the process
of collaborating with others working on your family lines. Your latest information
is always on your web site and available for others to see, defined by viewing
rules you set. For more information and to see working demos, visit
[webtrees.net](https://webtrees.net/).

**webtrees** is Open Source software that has been produced by people from many
countries freely donating their time and talents to the project. All service,
support, and future development is dependent on the time developers are willing
to donate to the project, often at the expense of work, recreation, and family.
Beyond the few donations received from users, developers receive no compensation
for the time they spend working on the project. There is also no outside source
of revenue to support the project. Please consider these circumstances when
making support requests and consider volunteering your own time and skills to make
the project even stronger and better.


### System requirements

To install **webtrees**, you need:

* A webserver. Apache and IIS are the most common types. There are no requirements
  to use a specific type or version.
* Approximately 65MB of disk space for the application files, plus whatever is
  needed for your media files, GEDCOM files and database.
* PHP 5.3.2 or later. Note that many web hosts offer *both* PHP4 and PHP5,
  typically with PHP4 as the default. If this is the case, you will be able to
  switch between the two using a control panel or a configuration file. Refer
  to your web host's support documentation for details.
   * PHP should be configured with the PHP/PDO library for MySQL. This is a
     server configuration option. It is enabled by default on most hosts.
     See [http://php.net/pdo](http://php.net/pdo)
   * PHP should be configured to allow sufficient server resources (memory and
     execution time) for the size of your system. Typical requirements are:
      * Small systems (500 individuals): 16–32 MB, 10–20 seconds
      * Medium systems (5,000 individuals): 32–64 MB, 20–40 seconds
      * Large systems (50,000 individuals): 64–128 MB, 40–80 seconds
* MySQL 5.0.13 or later. Note that **webtrees** can share a single database
  with other applications, by choosing a unique table prefix during configuration.
  If the number of databases is not restricted, you can set up a database purely
  for use by **webtrees** and create a separate user and password for only
  your genealogy.
* Internet browser compatibility. **webtrees** supports the use of most
  current versions of open-source browsers such as Firefox, Chrome, and Safari.
  We will do our best to support others such as Opera and Internet Explorer,
  though not their earlier versions. Currently many things do not work well in
  IE7, and some not in IE8 either. We strongly recommend anyone using these
  obsolete browsers upgrade as soon as possible. We are also aware that IE
  provides poor RTL language support generally, so cannot recommend it for
  sites requiring RTL languages.
* To view sites that contain both left-to-right and right-to-left text (e.g.
  English data on Hebrew pages), you will need to use a browser that provides
  support for the HTML5 **dir="auto"** attribute. At present, Internet Explorer
  (11 and lower) do not support this.
* HTML Frames. Note that **webtrees** uses cookies to track login sessions. Sites
  that make **webtrees** pages available inside an HTML Frames will encounter
  problems with login for versions 7, 8, and 9 of Internet Explorer. IE users
  should review the ``Privacy settings Tools`` / ``Internet Options`` for more details.


### Installation

Installing **webtrees** is really easy. All you need is a webserver with PHP and
MySQL. Almost every web hosting service provides these, but be sure to confirm
that those supplied meet or exceed the minimum system requirements.

1. Download latest stable version from [webtrees.net](https://webtrees.net/)
2. Unzip the files and upload them to an empty directory on your web server.
3. Open your web browser and type the URL for your **webtrees** site (for example,
   [http://www.yourserver.com/webtrees](http://www.yourserver.com/webtrees)) into
   the address bar.
4. The **webtrees** setup wizard will start automatically. Simply follow the steps,
   answering each question as you proceed. (See '''Step Six''' procedure below.)

That's it!

However, before you can use **webtrees**, you need one (or possibly more) GEDCOM
(family tree) files. If you have been doing your research using a desktop program
such as Family Tree Maker, you can use it's “save as GEDCOM” function to create
a GEDCOM file. If you are starting from scratch, then **webtrees** can create a
GEDCOM file for you. Alternatively, you can import data directly from PhpGedView.

So, after installation, you'll be directed to the GEDCOM (family tree)
administration page, where you'll need to select one of the following options:

* On successful completion of all steps you will be taken to the GEDCOM (family tree)
  administration page where you can:
   * UPLOAD a GEDCOM file from your local machine
   * ADD a GEDCOM file from your server, (if your GEDCOM file is too large to upload,
     you can copy it to the webtrees/data folder, and load it from there)
   * CREATE a new, empty GEDCOM file
   * TRANSFER your existing PhpGedView data straight into **webtrees**, using the
     PhpGedView-to-**webtrees** wizard described in section 9 below:
     [Converting from phpgedview](#converting-from-phpgedview)

There are *lots* of configuration options. You'll probably want to review the
privacy settings first. Don't worry too much about all the other options - the
defaults are good for most people. If you get stuck, there's lots of built-in
help and you can get friendly advice from the [help](https://webtrees.net/forums)
forum.


### Upgrading

Upgrading **webtrees** is quick and easy. It is strongly recommended that you
upgrade your installation whenever a new version is made available. Even minor
**webtrees** version updates usually contain a significant number of bug fixes
as well as interface improvements and program enhancements. The Administration
page of your **webtrees** installation will display a notification whenever a
new version is available.

1. Now would be a good time to make a [backup](#backup).
2. Download the latest version of **webtrees** available from
   [webtrees.net](https://webtrees.net/)
3. While you are in the middle of uploading the new files,
   a visitor to your site would encounter a mixture of new and old files.  This
   could cause unpredictable behaviour or errors.  To prevent this, create the
   file **data/offline.txt**.  While this file exists, visitors will see a
   “site unavailable - come back later” message.
4. Unzip the .ZIP file, and upload the files to your web server, overwriting the existing files.
5. Delete the file **data/offline.txt**


#### Note for Macintosh users

Step 4 assumes you are using a copy tool that **merges** directories rather than
replaces them. (**Merge** is standard behaviour on Windows and Linux.) If you use
the Macintosh Finder or other similar tool to perform step 3, it will **replace**
your configuration, media and other directories with the empty/default ones from
the installation. This would be very bad (but you did take a backup in step 1,
didn't you!). Further details and recommendations for suitable tools can be found
by searching [google.com](http://google.com).


#### Note for anyone using custom code (modules, themes, etc.).

It is **very likely** that your custom code will not work when you upgrade
**webtrees**.

**We recommend that you disable all custom code before you apply the upgrade.**

Disable custom modules, switch over to a standard
theme, and remove any code “hacks”. Once the upgrade is complete and you are satisfied
your site is fully operational contact the source of those modules or themes for
a new version.


#### General note

Depending on the changes in the new files, your browser configuration
and possibly other factors, it is always wise to clear both the **webtrees** cache
and your browser cache immediately after the upgrade is completed. The **webtrees**
cache can be cleared simply by going to ``Administration`` ->
``Cleanup data directory`` and deleting the cache.

If you have any problems or questions, help is available on the
[webtrees forum](https://webtrees.net/forums).


### Gedcom (family tree) files

When you ADD, IMPORT or UPLOAD a family tree (GEDCOM) file in **webtrees** the
data from the file is all transferred to the database tables. The file itself is
no longer used or required by **webtrees**

* If you use ADD or IMPORT, your file remains in the webtrees/data folder you
  first copied it to, and will not be changed by any subsequent editing of the
  **webtrees** data.
* If you use UPLOAD, the file is left in its original location, and again remains
  untouched.

When or if you change your genealogy data outside of **webtrees**, it is not
necessary to delete your GEDCOM file or database from **webtrees** and start
over. Follow these steps to update a GEDCOM that has already been imported:

* Decide if you want to IMPORT or UPLOAD your new GEDCOM file.
   * Use UPLOAD if your family tree file is smaller than your server's PHP file
     upload limit (often 2MB).The new file can have any name you choose.
   * Use IMPORT for larger files. In this case you need to use FTP to first copy
     your file to the webtrees/data folder. Either copy over the existing file,
     or use a different name.
* From the Administration page, go to your **webtrees** Family trees (GEDCOM)
  configuration page. On the line  relating to this particular family tree (GEDCOM)
  file (or a new one) click either IMPORT or UPLOAD.
* Take careful note of the media items option (_“If you have created media objects
  in **webtrees**, and have edited your data off-line using software that
  deletes media objects, then tick this box to merge the current media objects
  with the new GEDCOM.”_) In most cases you should leave this box **UNCHECKED**.
* Click “SAVE”. **webtrees** will validate the GEDCOM again before importing.
  During this process, **webtrees** copies your entire family tree (GEDCOM file)
  to a 'chunk' table within your database. Depending on the coding of your file,
  its file size and the capabilities of your server and the supporting software,
  this may take some time. **No progress bar will show while the data is being
  copied** and should you navigate away from this page, the process is suspended.
  It will start again when you return to the Family Tree management page.


#### FORMAT

Every Family History program has its own method of creating GEDCOM files, and
differing output format options to select from. **webtrees'** import routines
can read many different formats, but not necessarily all. If your software has
a “UTF8” option you should always use that. However, **webtrees** has been
tested with these alternative formats:

* ANSI
   * imports OK, but is slow due to the translation into UTF8 as part
     of the import process.
* MAC
   * imports OK, but is slow due to the translation into UTF8 as part
     of the import process.
* DOS
   * imports OK, but is slow due to the translation into UTF8 as part
     of the import process.
* ANSEL
   * currently will not import. Gives warning *Error: cannot convert
     GEDCOM file from ANSEL encoding to UTF-8 encoding*. Later releases
     of **webtrees** may include translation from ANSEL to UTF8, but this
     is not a simple process.


### Security

**Security** in _webtrees_ means ensuring your site is safe from unwanted
intrusions, hacking, or access to data and configuration files. The developers
of _webtrees_ regard security as an extremely important part of its development
and have made every attempt to ensure your data is safe.

The area most at risk of intrusion would be the /data folder that contains your
config.ini.php file, and various temporary files. If you are concerned there
may be a risk there is a very simple test you can do: try to fetch **config.ini.php**
by typing **http://_url to your site_/data/config.ini.php** in your web
browser.

The most likely result is an “access denied” message like this:

    Forbidden

    You don't have permission to access /data/xxxx.ged on this server.

This indicates that the protection built into **webtrees** is working, and no
further action is required.

In the unlikely event you do fetch the file (you will just see a semicolon),
then that protection is not working on your site and you should take some further
action.

If your server runs PHP in CGI mode, then change the permission of the /data
directory to 700 instead of 777. This will block access to the httpd process,
while still allowing access to PHP scripts.

This will work for perhaps 99% of all users. Only the remaining 1% should consider
the most complex solution, moving the /data/ directory out of accessible web
space. (**_Note:_** In many shared hosting environments this is not an option anyway.)

If you do find it necessary, following is an example of the process required:

If your home directory is something like **/home/username**,
and the root directory for your web site is **/home/username/public_html**,
and you have installed **webtrees** in the **public_html/webtrees** directory,
then you would create a new **data** folder in your home directory at the same
level as your public_html directory, such as **/home/username/private/data**,
and place your GEDCOM (family tree) file there.

Then change the **Data file directory** setting on the ``Admin`` ->
``Site Administration`` page from the default **data/** to the new
location **/home/username/private/data**

You will have **two** data directories:

* [path to webtrees]/data - just needs to contain config.ini.php
* /home/username/private/data - contains everything else


#### Hypertext Transfer Protocol Secure (HTTPS)

**webtrees** supports https access. If your website is configured with mandatory
or optional https support **webtrees** will operate correctly in either mode.

If your website is configured with optional https support, **webtrees** can be
configured to switch to https at login. To enable https at login, set the Login
URL setting on the ``Admin`` -> ``Site Administration`` ->
``Configuration page`` to your https login URL, which is often in the form
[https://example.com/admin.php](https://example.com/admin.php)
(substitute your domain for example.com).

**Warning:** Misconfiguration can disable your login links. If this occurs,
access the login by typing the correct URL directly into your browser's address input.


### Backup

Backups are good. Whatever problem you have, it can always be fixed from a good
backup.

To make a backup of webtrees, you need to make a copy of the following

1. The files in the *webtrees/data* directory.
2. The files in the *webtrees/media* directory.
3. The tables in the database. Freely available tools such as
   [phpMyAdmin](http://www.phpmyadmin.net) allow you to do this in one click.

Remember that most web hosting services do NOT backup your data, and this is
your responsibility.


### Converting from phpgedview

If you are moving to **webtrees** from an existing PhpGedView setup, and
your PhpGedView install meets certain requirements, **webtrees** has provided a “wizard”
to help make the transfer of the majority of your data a relatively quick and
painless operation. See exceptions noted below. Please note that we have designed
this wizard so as to not disturb your existing PhpGedView installation, leaving all those
settings, data and your website intact and fully functional.

The requirements are:

* The PhpGedView database and index directory must be on the same server as **webtrees**.
* Your **webtrees** MySQL database username and password must either be the same
  as your PhpGedView username and password, or if you created a new user for **webtrees**,
  that new user must also have full privileges to access your PhpGedView database.
* PhpGedView must be at least versions 4.2.3 or 4.2.4 (this corresponds to an internal
  “PGV_SCHEMA_VERSION” of between 10 and 14).  Newer versions, including the current
  version 4.3 SVN work (as of JAN 2013) also currently, and later versions, should
  they be released, will probably work, provided the data structures do not change;
* All changes in PhpGedView must be accepted (as pending edits will not be transfered).
* All existing PhpGedView users must have an email address, and it must be unique to that
  user (PhpGedView allows users to delete their email address, or have the same email
  address as other users;  **webtrees** requires that all users have their own
  unique email address).
* The wizard transfer process overwrites the username and password you may have
  entered in setting up the initial admin account. The main administration user
  name and password in **webtrees** will be identical to the admin user name and
  password from PhpGedView after running the wizard. Once done, you can change it back
  if desired.


#### Warning

Please read the [https://wiki.webtrees.net/en/Converting_from_PhpGedView](https://wiki.webtrees.net/en/Converting_from_PhpGedView)
before doing a transfer as important pre-processing steps and known issues may
be outlined there.


#### Important Note

This transfer wizard is not able to assist with moving media items.  You will need
to set up and move or copy your media configuration and objects separately after
the transfer wizard is finished. If you use the media firewall in PhpGedView with a
directory outside the PhpGedView root, then duplicating the media configuration in
**webtrees** to use the same firewall directory should make your media available
in **webtrees**.

After the transfer is complete, you should check your family tree configuration
and privacy settings. Due to differences in internal data formats, the following
settings are not yet transfered: custom privacy restrictions, block configuration,
FAQs, and HTML blocks.  We hope to add these to the wizard in a future release.


#### Custom privacy restrictions, block configuration, FAQs and HTML blocks

We hope to add these to the wizard in a future release. Otherwise, read the
[https://wiki.webtrees.net/en/Converting_from_PhpGedView](https://wiki.webtrees.net/en/Converting_from_PhpGedView)
before reporting a problem in the forum.

The transfer wizard is accessed in **webtrees** from the bottom of the
“Manage family trees” page to which you will be automatically directed once you
have completed the initial **webtrees** installation steps (section 4 above:
[installation](#installation)). This option is only available on a new,
empty **webtrees** installation; once you have created a GEDCOM (family tree)
or added user accounts, it will no longer be available.
