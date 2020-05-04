# Tests running
For local development and testing different PHP versions.

Required:
* Docker
* Docker-compose


### Testing single PHP version
* cd to repository root
* Run `docker-compose -f docker-compose.tests.yml up --build`
* See output like that:
```
Building php-7.3
Step 1/11 : ARG IMAGE
Step 2/11 : FROM ${IMAGE}
 
 ... Docker building ...
 
Successfully built 223c37da768b
Successfully tagged php-whois_php-7.3:latest
Recreating php-whois_php-7.3_1 ... done
Attaching to php-whois_php-7.3_1
php-7.3_1  | Do not run Composer as root/super user! See https://getcomposer.org/root for details
php-7.3_1  | Loading composer repositories with package information
php-7.3_1  | Installing dependencies (including require-dev) from lock file
php-7.3_1  | Nothing to install or update
php-7.3_1  | Generating autoload files
php-7.3_1  | PHPUnit 8.3.5 by Sebastian Bergmann and contributors.
php-7.3_1  | 
php-7.3_1  | Text Helper (Iodev\Whois\Helpers\TextHelper)
php-7.3_1  |  ✔ ToUtf8 FIN
php-7.3_1  |  ✔ ToUtf8 UKR
   
   ... Tests ...
   
php-7.3_1  | Whois (Iodev\Whois\Whois)
php-7.3_1  |  ✔ Construct
php-7.3_1  |  ✔ Get loader
php-7.3_1  | 
php-7.3_1  | Time: 17.74 seconds, Memory: 8.00 MB
php-7.3_1  | 
php-7.3_1  | OK (350 tests, 4155 assertions)
php-whois_php-7.3_1 exited with code 0
```

### Testing all PHP versions
* cd to repository root
* Run `docker-compose -f docker-compose.tests.full.yml up --build`
* See output like that:
```
Building php-7.2
Step 1/11 : ARG IMAGE
 
 ... Docker building ...
 
Successfully built 6e1da79145b9
Successfully tagged php-whois_php-7.4:latest
Starting php-whois_php-7.4_1   ... done
Starting php-whois_php-7.2_1   ... done
Recreating php-whois_php-7.3_1 ... done
Attaching to php-whois_php-7.4_1, php-whois_php-7.3_1, php-whois_php-7.2_1
php-7.4_1  | Do not run Composer as root/super user! See https://getcomposer.org/root for details
php-7.4_1  | Loading composer repositories with package information
php-7.4_1  | Installing dependencies (including require-dev) from lock file
php-7.4_1  | Nothing to install or update
php-7.4_1  | Generating autoload files
php-7.4_1  | PHPUnit 8.3.5 by Sebastian Bergmann and contributors.
php-7.4_1  | 
php-7.3_1  | Do not run Composer as root/super user! See https://getcomposer.org/root for details
php-7.3_1  | Loading composer repositories with package information
php-7.3_1  | Installing dependencies (including require-dev) from lock file
php-7.3_1  | Nothing to install or update
php-7.3_1  | Generating autoload files
php-7.3_1  | PHPUnit 8.3.5 by Sebastian Bergmann and contributors.
php-7.3_1  | 
php-7.4_1  | ...............................................................  63 / 350 ( 18%)
php-7.2_1  | Do not run Composer as root/super user! See https://getcomposer.org/root for details
php-7.2_1  | Loading composer repositories with package information
php-7.2_1  | Installing dependencies (including require-dev) from lock file
php-7.2_1  | Nothing to install or update
php-7.2_1  | Generating autoload files
php-7.2_1  | PHPUnit 8.3.5 by Sebastian Bergmann and contributors.
php-7.2_1  | 
php-7.3_1  | ...............................................................  63 / 350 ( 18%)
php-7.4_1  | ............................................................... 126 / 350 ( 36%)
php-7.3_1  | ............................................................... 126 / 350 ( 36%)
php-7.2_1  | ...............................................................  63 / 350 ( 18%)
php-7.4_1  | ............................................................... 189 / 350 ( 54%)
php-7.3_1  | ............................................................... 189 / 350 ( 54%)
php-7.4_1  | ............................................................... 252 / 350 ( 72%)
php-7.3_1  | ............................................................... 252 / 350 ( 72%)
php-7.4_1  | ............................................................... 315 / 350 ( 90%)
php-7.4_1  | ...................................                             350 / 350 (100%)
php-7.4_1  | 
php-7.4_1  | Time: 17.42 seconds, Memory: 8.00 MB
php-7.4_1  | 
php-7.4_1  | OK (350 tests, 4155 assertions)
php-whois_php-7.4_1 exited with code 0
php-7.3_1  | ............................................................... 315 / 350 ( 90%)
php-7.3_1  | ...................................                             350 / 350 (100%)
php-7.3_1  | 
php-7.3_1  | Time: 17.62 seconds, Memory: 8.00 MB
php-7.3_1  | 
php-7.3_1  | OK (350 tests, 4155 assertions)
php-whois_php-7.3_1 exited with code 0
php-7.2_1  | ............................................................... 126 / 350 ( 36%)
php-7.2_1  | ............................................................... 189 / 350 ( 54%)
php-7.2_1  | ............................................................... 252 / 350 ( 72%)
php-7.2_1  | ............................................................... 315 / 350 ( 90%)
php-7.2_1  | ...................................                             350 / 350 (100%)
php-7.2_1  | 
php-7.2_1  | Time: 1.27 minutes, Memory: 8.00 MB
php-7.2_1  | 
php-7.2_1  | OK (350 tests, 4155 assertions)
php-whois_php-7.2_1 exited with code 0
```
