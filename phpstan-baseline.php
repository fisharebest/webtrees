<?php

declare(strict_types=1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Cannot call method find\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Auth.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Census/CensusColumnRelationToHead.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$id of method Fisharebest\\\\Webtrees\\\\Container\\<T of object\\>\\:\\:get\\(\\) expects class\\-string\\<T of object\\>, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Container.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Elements\\\\AgeAtEvent\\:\\:value\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/AgeAtEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/Census.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/Census.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Elements\\\\DateValue\\:\\:escape\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/DateValue.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/PlaceHierarchy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Elements\\\\RestrictionNotice\\:\\:canonical\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/RestrictionNotice.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Encodings\\\\ANSEL\\:\\:fromUtf8\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Encodings/ANSEL.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Encodings/ANSEL.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$array of function array_map expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Encodings/AbstractEncoding.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Fact\\:\\:value\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Fact.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of method Fisharebest\\\\Webtrees\\\\Contracts\\\\ElementInterface\\:\\:canonical\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Fact.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Family but returns Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\FamilyFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\GedcomRecord but returns Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/GedcomRecordFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\GedcomRecordFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/GedcomRecordFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Header but returns Fisharebest\\\\Webtrees\\\\Header\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/HeaderFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\HeaderFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/HeaderFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$num of function dechex expects int, float\\|int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IdFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Individual but returns Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IndividualFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IndividualFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Location but returns Fisharebest\\\\Webtrees\\\\Location\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/LocationFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\LocationFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/LocationFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Media but returns Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/MediaFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\MediaFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/MediaFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Note but returns Fisharebest\\\\Webtrees\\\\Note\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/NoteFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\NoteFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/NoteFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\RepositoryFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RepositoryFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getGenerator\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RouteFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getMap\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RouteFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Factories/RouteFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\SharedNote but returns Fisharebest\\\\Webtrees\\\\SharedNote\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SharedNoteFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SharedNoteFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SharedNoteFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SlugFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Source but returns Fisharebest\\\\Webtrees\\\\Source\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SourceFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SourceFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SourceFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Submission but returns Fisharebest\\\\Webtrees\\\\Submission\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmissionFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SubmissionFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmissionFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Submitter but returns Fisharebest\\\\Webtrees\\\\Submitter\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmitterFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SubmitterFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmitterFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/XrefFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Family\\:\\:spouses\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> but returns Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Family.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fromUtf8\\(\\) on Fisharebest\\\\Webtrees\\\\Encodings\\\\EncodingInterface\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/GedcomFilters/GedcomEncodingFilter.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method acceptRecord\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$array of function array_shift expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateFact\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:\\$getAllNames \\(array\\<array\\<string\\>\\>\\) does not accept array\\<array\\<string\\|null\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method createResponse\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Helpers/functions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Helpers/functions.php',
];
$ignoreErrors[] = [
    'message' => '#^Strict comparison using \\=\\=\\= between \'\\-dev\' and \'\' will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Helpers/functions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$host of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withHost\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$path of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withPath\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$port of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withPort\\(\\) expects int\\|null, int\\<0, 65535\\>\\|false\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$scheme of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withScheme\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of function explode expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/ClientIp.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method withAttribute\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$message of static method Fisharebest\\\\Webtrees\\\\Log\\:\\:addErrorLog\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\HandleExceptions\\:\\:httpExceptionResponse\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\HandleExceptions\\:\\:thirdPartyExceptionResponse\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\HandleExceptions\\:\\:unhandledExceptionResponse\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$basepath of class Aura\\\\Router\\\\RouterContainer constructor expects string\\|null, string\\|false\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/LoadRoutes.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/PublicFiles.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/ReadConfigIni.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method handle\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/RequestHandler.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\UseTransaction\\:\\:process\\(\\) should return Psr\\\\Http\\\\Message\\\\ResponseInterface but returns Psr\\\\Http\\\\Message\\\\ResponseInterface\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/UseTransaction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AppleTouchIconPng.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AutoCompleteCitation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AutoCompleteCitation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AutoCompleteCitation.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CalendarEvents.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CalendarEvents.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CalendarEvents.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CheckTree.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\ControlPanel\\:\\:totalChanges\\(\\) should return array\\<string\\> but returns array\\<int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ControlPanel.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 8,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ControlPanel.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Module\\\\ModuleDataFixInterface\\>\\:\\:get\\(\\) expects int, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixSelect.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on object\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixUpdateAll.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\DeleteRecord\\:\\:removeLinks\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DeleteRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DeleteRecord.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DeleteUser.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditFactAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateFact\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditFactAction.php',
];
$ignoreErrors[] = [
    'message' => '#^If condition is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditMediaFileAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateRecord\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditNoteAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditRawFactAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditRawRecordAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FaviconIco.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method displayImage\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method facts\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/GedcomLoad.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$progress on left side of \\?\\? always exists and is not nullable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/GedcomLoad.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method createFact\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method facts\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method firstImageFile\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method tree\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method updateRecord\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of method Fisharebest\\\\Webtrees\\\\Services\\\\PendingChangesService\\:\\:acceptRecord\\(\\) expects Fisharebest\\\\Webtrees\\\\GedcomRecord, Fisharebest\\\\Webtrees\\\\Media\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$tree of method Fisharebest\\\\Webtrees\\\\Contracts\\\\MediaFactoryInterface\\:\\:make\\(\\) expects Fisharebest\\\\Webtrees\\\\Tree, Fisharebest\\\\Webtrees\\\\Tree\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 0 on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 0 on non\\-empty\\-array\\<int, string\\>\\|false\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 1 on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 2 on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsData.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TMakeKey in call to method static method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:make\\(\\)$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ImportThumbnailsData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'0\' on array\\{0\\: int, 1\\: int, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'1\' on array\\{0\\: int, 1\\: int, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function strlen expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$needle of function str_starts_with expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataAdd.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataDelete.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$latitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$longitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$latitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$longitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$features on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataImportAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$haystack of function str_contains expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataImportAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataImportAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataList.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateRecord\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MergeFactsAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MergeTreesAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$haystack of function str_contains expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ModuleAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$module_name of method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:findByName\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ModuleAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$token of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByToken\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PasswordResetAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$token of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByToken\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PasswordResetPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$change_id of method Fisharebest\\\\Webtrees\\\\Services\\\\PendingChangesService\\:\\:acceptChange\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesAcceptChange.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(object\\)\\: non\\-falsy\\-string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of method Fisharebest\\\\Webtrees\\\\Contracts\\\\TimestampFactoryInterface\\:\\:fromString\\(\\) expects string\\|null, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$change_id of method Fisharebest\\\\Webtrees\\\\Services\\\\PendingChangesService\\:\\:rejectChange\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesRejectChange.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PhpInformation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ReportGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<string\\>\\|string supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ReportSetupPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Binary operation "\\+\\=" between string and array\\{type\\: \'text\', default\\: \'\', lookup\\: \'\', extra\\: \'\'\\} results in an error\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ReportSetupPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Location\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Note\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Source\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateRecord\\(\\) expects string, string\\|null given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchReplaceAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method isNotEmpty\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method push\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method withAttribute\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$code of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:init\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$driver of method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverErrors\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$driver of method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverWarnings\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$identifier of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByIdentifier\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$url of function redirect expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$user_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$wtname of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$real_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$setting_value of method Fisharebest\\\\Webtrees\\\\User\\:\\:setPreference\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$wtuser of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$email of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$wtpass of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#4 \\$password of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#4 \\$wtemail of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(object\\)\\: non\\-falsy\\-string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of method Fisharebest\\\\Webtrees\\\\Contracts\\\\TimestampFactoryInterface\\:\\:fromString\\(\\) expects string\\|null, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SynchronizeTrees.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePageBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePreferencesAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePrivacyPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$l_from on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$l_to on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<string, array\\{\\}\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<string, array\\{\\}\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function usort expects callable\\(array\\<Fisharebest\\\\Webtrees\\\\Individual\\>\\|Illuminate\\\\Support\\\\Collection\\<int\\|string, Fisharebest\\\\Webtrees\\\\Individual\\>, array\\<Fisharebest\\\\Webtrees\\\\Individual\\>\\|Illuminate\\\\Support\\\\Collection\\<int\\|string, Fisharebest\\\\Webtrees\\\\Individual\\>\\)\\: int, Closure\\(Illuminate\\\\Support\\\\Collection, Illuminate\\\\Support\\\\Collection\\)\\: int\\<\\-1, 1\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TGetDefault in call to method Illuminate\\\\Support\\\\Collection\\<string,string\\>\\:\\:get\\(\\)$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserPageBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$user_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByUserName\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/VerifyEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/I18N.php',
];
$ignoreErrors[] = [
    'message' => '#^Binary operation "\\*" between string and 365 results in an error\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method spouseFamilies\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$haystack of function strpos expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Log.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$xref of method Fisharebest\\\\Webtrees\\\\Contracts\\\\GedcomRecordFactoryInterface\\:\\:make\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Media.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$access_level on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\AbstractModule\\:\\:getBlockSetting\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\AbstractModule\\:\\:getPreference\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:first\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(object\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/BingWebmasterToolsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/BranchesListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/BranchesListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\|string supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method object\\:\\:censusLanguage\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method updateFact\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Empty array passed to foreach\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:censusTableEmptyRow\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:censusTableHeader\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:censusTableRow\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:createNoteText\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#5 \\$ca_individuals of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:createNoteText\\(\\) expects array\\<array\\<string\\>\\>, array\\<string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ChartsMenuModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ClippingsCartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeFavoritesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeNewsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$n_surn on mixed\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$n_surname on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$total on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method embedTags\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 0 on array\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 4 on array\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method alternateName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method individualBoxMenu\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method lifespan\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method sex\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FanChartModule\\:\\:imageColor\\(\\) should return int but returns int\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagecolorallocate expects GdImage, GdImage\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagecolortransparent expects GdImage, GdImage\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagefilledarc expects GdImage, GdImage\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagefilledrectangle expects GdImage, GdImage\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagepng expects GdImage, GdImage\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagettftext expects GdImage, GdImage\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of method Fisharebest\\\\Webtrees\\\\Module\\\\FanChartModule\\:\\:imageColor\\(\\) expects GdImage, GdImage\\|false given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$individual of method Fisharebest\\\\Webtrees\\\\Module\\\\FanChartModule\\:\\:chartTitle\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$color of function imagecolortransparent expects int\\|null, int\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#6 \\$color of function imagefilledrectangle expects int, int\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Unsafe access to private constant Fisharebest\\\\Webtrees\\\\Module\\\\FanChartModule\\:\\:FONT through static\\:\\:\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixDuplicateLinks\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixDuplicateLinks.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixDuplicateLinks.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixNameSlashesAndSpaces\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixNameSlashesAndSpaces.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixNameSlashesAndSpaces.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixPlaceNames\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixPlaceNames.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixSearchAndReplace\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixSearchAndReplace.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(object\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$geonames on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/GeonamesAutocomplete.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/GoogleAnalyticsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/GoogleAnalyticsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/GoogleWebmasterToolsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/HitCountFooterModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method embedTags\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/HtmlBlockModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,string\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>, Illuminate\\\\Support\\\\Collection\\<int, mixed\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/IndividualFactsTabModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$count on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$n_givn on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$n_surn on mixed\\.$#',
    'count' => 8,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$n_surname on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$total on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/IndividualListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/InteractiveTree/TreeView.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset 1 on array\\{Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Family\\} in empty\\(\\) always exists and is not falsy\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/InteractiveTree/TreeView.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$familyList of method Fisharebest\\\\Webtrees\\\\Module\\\\InteractiveTree\\\\TreeView\\:\\:drawChildren\\(\\) expects Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\|null\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/InteractiveTree/TreeView.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageAfrikaans.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageAlbanian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageArabic.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageBasque.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageBosnian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageBulgarian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageCatalan.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageChineseSimplified.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageChineseTraditional.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageCroatian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageCzech.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageDanish.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageDivehi.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageDutch.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageEnglishUnitedStates.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageEstonian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFaroese.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFarsi.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFinnish.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFrench.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGalician.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGeorgian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGerman.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGreek.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageHebrew.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageHindi.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageHungarian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageIcelandic.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageIndonesian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageItalian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageJapanese.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageJavanese.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageKazhak.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageKorean.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageKurdish.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageLatvian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageLingala.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageLithuanian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageMalay.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageMaori.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageMarathi.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageNepalese.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageNorwegianBokmal.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageNorwegianNynorsk.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageOccitan.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguagePolish.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguagePortuguese.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguagePortugueseBrazil.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageRomanian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageRussian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSerbian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSerbianLatin.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSlovakian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSlovenian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSpanish.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSundanese.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSwahili.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSwedish.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTagalog.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTamil.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTatar.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageThai.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTurkish.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageUkranian.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageUrdu.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageUzbek.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageVietnamese.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageWelsh.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageYiddish.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\LifespansChartModule\\:\\:findIndividualsByDate\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LifespansChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\LifespansChartModule\\:\\:findIndividualsByPlace\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LifespansChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Menu\\|null\\>\\:\\:sort\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Menu\\|null, Fisharebest\\\\Webtrees\\\\Menu\\|null\\)\\: int\\)\\|int\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Menu, Fisharebest\\\\Webtrees\\\\Menu\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ListsMenuModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#5 \\$submenus of class Fisharebest\\\\Webtrees\\\\Menu constructor expects array\\<Fisharebest\\\\Webtrees\\\\Menu\\>, array\\<int, Fisharebest\\\\Webtrees\\\\Menu\\|null\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ListsMenuModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Location, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Location given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LocationListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method usersLoggedInList\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LoggedInUsersModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\MapGeoLocationGeonames\\:\\:extractLocationsFromResponse\\(\\) should return array\\<string\\> but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MapGeoLocationGeonames.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\MapGeoLocationNominatim\\:\\:extractLocationsFromResponse\\(\\) should return array\\<string\\> but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MapGeoLocationNominatim.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\MapGeoLocationOpenRouteService\\:\\:extractLocationsFromResponse\\(\\) should return array\\<string\\> but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MapGeoLocationOpenRouteService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MatomoAnalyticsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MediaListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(string\\)\\: string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MediaListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Note, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Note given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/NoteListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$features on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/OpenRouteServiceAutocomplete.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$individual of method Fisharebest\\\\Webtrees\\\\Module\\\\PedigreeChartModule\\:\\:nextLink\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/PedigreeChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$length of function array_chunk expects int\\<1, max\\>, int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/PlaceHierarchyListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{record\\: Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, time\\: Fisharebest\\\\Webtrees\\\\Contracts\\\\TimestampInterface, user\\: Fisharebest\\\\Webtrees\\\\User\\|null\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{record\\: Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, time\\: Fisharebest\\\\Webtrees\\\\Contracts\\\\TimestampInterface, user\\: Fisharebest\\\\Webtrees\\\\User\\|null\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getMap\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RedirectLegacyUrlsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\GedcomRecord but returns Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\GedcomRecord but returns Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$l_from on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$l_to on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method gedcom\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method sex\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\RelationshipsChartModule\\:\\:allAncestors\\(\\) should return array\\<string\\> but returns array\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\RelationshipsChartModule\\:\\:excludeFamilies\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$nodes of method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:nameFromPath\\(\\) expects array\\<Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual\\>, array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReportsMenuModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Repository, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Repository given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RepositoryListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ResearchTaskModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ResearchTaskModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ResearchTaskModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$new_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$old_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method canShow\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Sabre\\\\VObject\\\\Node\\:\\:add\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/ShareAnniversaryModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: bool given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/ShareAnniversaryModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\)\\: array\\<string, string\\>, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: non\\-empty\\-array\\<string, non\\-falsy\\-string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ShareAnniversaryModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Fact\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>, Illuminate\\\\Support\\\\Collection\\<int, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ShareAnniversaryModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Note, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Note given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Repository, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Repository given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Submitter, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Submitter given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$m_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$m_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:first\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(object\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SourceListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StatcounterModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Binary operation "\\+" between int and int\\|string results in an error\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Binary operation "\\-" between string and 1 results in an error\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Binary operation "/" between stdClass and 365\\.25 results in an error\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$d_month on mixed\\.$#',
    'count' => 11,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$f_husb on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$f_wife on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$i_sex on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$month on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$total on mixed\\.$#',
    'count' => 11,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$block_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$individual on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$languages on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$title on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Submitter, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Submitter given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SubmitterListModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method menuThemes\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ThemeSelectModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\:\\:filter\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Individual\\|null, int\\|string\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\:\\:filter\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Individual\\|null, int\\|string\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Individual\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\:\\:map\\(\\) expects callable\\(Fisharebest\\\\Webtrees\\\\Individual\\|null, int\\|string\\)\\: string, Closure\\(Fisharebest\\\\Webtrees\\\\Individual\\)\\: string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method commonGivenFemaleListTotals\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method commonGivenFemaleTable\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method commonGivenMaleListTotals\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method commonGivenMaleTable\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$page_count on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopPageViewsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$page_parameter on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopPageViewsModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$n_surn on mixed\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/app/Module/TopSurnamesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$n_surname on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/TopSurnamesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$total on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopSurnamesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserFavoritesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserJournalModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserMessagesModule.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Note\\:\\:getNote\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Note.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$xref of method Fisharebest\\\\Webtrees\\\\Contracts\\\\GedcomRecordFactoryInterface\\:\\:make\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Note.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Place, Closure\\(string\\)\\: Fisharebest\\\\Webtrees\\\\Place given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:sort\\(\\) expects \\(callable\\(mixed, mixed\\)\\: int\\)\\|int\\|null, Closure\\(string, string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>\\|null, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function mb_substr expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$text of static method Fisharebest\\\\Webtrees\\\\Soundex\\:\\:daitchMokotoff\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$text of static method Fisharebest\\\\Webtrees\\\\Soundex\\:\\:russell\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$latitude on object\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$longitude on object\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\PlaceLocation\\:\\:boundingRectangle\\(\\) should return array\\<array\\<float\\>\\> but returns array\\<int, array\\<int, mixed\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\PlaceLocation\\:\\:details\\(\\) should return object but returns object\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: float, Closure\\(string\\)\\: float given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>\\|null, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function mb_substr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function fclose expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserBase.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function feof expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserBase.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function fread expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserBase.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:\\$generation\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to protected property Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:\\$generation\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Binary operation "\\+" between non\\-empty\\-string and 1 results in an error\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:canShow\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:privatizeGedcom\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:tree\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function assert\\(\\) with false and LogicException will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \\(float\\|int\\) on array\\<int, string\\>\\|false\\.$#',
    'count' => 13,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \\(float\\|int\\<1, max\\>\\) on array\\<int, string\\>\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 0 on array\\{0\\: int, 1\\: int, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 1 on array\\{0\\: int, 1\\: int, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method addElement\\(\\) on Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method childFamilies\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method facts\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findHighlightedMediaFile\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method firstImageFile\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method privatizeGedcom\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot use array destructuring on array\\<int, array\\<string\\>\\|int\\>\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot use array destructuring on array\\<int, string\\>\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^If condition is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer and Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseElement will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:substituteVars\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$array of function end expects array\\|object, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\), Closure\\(object\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$expression of method Symfony\\\\Component\\\\ExpressionLanguage\\\\ExpressionLanguage\\:\\:evaluate\\(\\) expects string\\|Symfony\\\\Component\\\\ExpressionLanguage\\\\Expression, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$haystack of function str_contains expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagesx expects GdImage, GdImage\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$image of function imagesy expects GdImage, GdImage\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function addslashes expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\Family, Fisharebest\\\\Webtrees\\\\Family\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\GedcomRecord, Fisharebest\\\\Webtrees\\\\GedcomRecord\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\GedcomRecord, Fisharebest\\\\Webtrees\\\\GedcomRecord\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$current_element \\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseElement\\) does not accept Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<\\(object\\{generation\\: int\\}&stdClass\\)\\|\\(object\\{generation\\: int\\}&stdClass\\)\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\|null\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<string, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$parser \\(XMLParser\\) does not accept XMLParser\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$print_data \\(bool\\) does not accept bool\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$repeat_bytes \\(int\\) does not accept array\\<string\\>\\|int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$repeats \\(array\\<string\\>\\) does not accept array\\<string\\>\\|int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$vars \\(array\\<array\\<string\\>\\>\\) does not accept array\\<array\\<string\\|null\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$wt_report \\(Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\) does not accept Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$wt_report \\(Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\) does not accept Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseTextbox\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot assign new offset to array\\<string\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserSetup.php',
];
$ignoreErrors[] = [
    'message' => '#^array\\<string\\>\\|string does not accept array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserSetup.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$link of method TCPDF\\:\\:setLink\\(\\) expects int, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportPdfFootnote.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseElement and Fisharebest\\\\Webtrees\\\\Report\\\\ReportPdfFootnote will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportPdfTextBox.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Report\\\\RightToLeftSupport\\:\\:spanLtrRtl\\(\\) should return string but returns array\\<int, string\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Report\\\\RightToLeftSupport\\:\\:starredName\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$access_level on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$component on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$gedcom_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$module_name on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Family but returns Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Individual but returns Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Media but returns Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Source but returns Fisharebest\\\\Webtrees\\\\Source\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined static method Fisharebest\\\\Webtrees\\\\DB\\:\\:query\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Family\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Media\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Media\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Source\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Source\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$table of static method Illuminate\\\\Database\\\\Capsule\\\\Manager\\:\\:table\\(\\) expects Closure\\|Illuminate\\\\Database\\\\Query\\\\Builder\\|string, Illuminate\\\\Database\\\\Query\\\\Expression given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$d_day on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$d_fact on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$d_month on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$d_type on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$d_year on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ChartService\\:\\:descendants\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> but returns Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Individual\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ChartService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<string,Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<string, Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<string, Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ChartService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset string on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/ClipboardService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: Fisharebest\\\\Webtrees\\\\Fact, Closure\\(string\\)\\: Fisharebest\\\\Webtrees\\\\Fact given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ClipboardService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:createFact\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ClipboardService.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'column\' does not exist on string\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'dir\' does not exist on string\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\)\\: bool\\)\\|null, Closure\\(array\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:sort\\(\\) expects \\(callable\\(mixed, mixed\\)\\: int\\)\\|int\\|null, Closure\\(array, array\\)\\: \\(\\-1\\|0\\|1\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/EmailService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomEditService\\:\\:insertMissingRecordSubtags\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/GedcomEditService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$array of function array_shift expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomEditService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$gedcom of method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomEditService\\:\\:insertMissingLevels\\(\\) expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/GedcomEditService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$f_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$i_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$m_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$o_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$s_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\GedcomRecord, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\GedcomRecord given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function stream_get_meta_data expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method canonicalTag\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomImportService\\:\\:createMediaObject\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$haystack of function str_starts_with expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 10,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\HomePageService\\:\\:filterActiveBlocks\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/HomePageService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<int,string\\>\\:\\:contains\\(\\) expects \\(callable\\(string, int\\)\\: bool\\)\\|string, mixed given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/HomePageService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$active_blocks of method Fisharebest\\\\Webtrees\\\\Services\\\\HomePageService\\:\\:filterActiveBlocks\\(\\) expects Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\>, Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/HomePageService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$path of method Fisharebest\\\\Webtrees\\\\Services\\\\HousekeepingService\\:\\:deleteFileOrFolder\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/HousekeepingService.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:sex\\(\\)\\.$#',
    'count' => 17,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Illuminate\\\\Support\\\\Collection\\<\\*NEVER\\*, \\*NEVER\\*\\> does not accept Fisharebest\\\\Webtrees\\\\Fact\\.$#',
    'count' => 31,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between Fisharebest\\\\Webtrees\\\\Individual and Fisharebest\\\\Webtrees\\\\Family will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\IndividualFactsService\\:\\:familyFacts\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Fact\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\IndividualFactsService\\:\\:historicFacts\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Fact\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$gedcom of class Fisharebest\\\\Webtrees\\\\Fact constructor expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Fact\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>, Illuminate\\\\Support\\\\Collection\\<int, mixed\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$spouse of method Fisharebest\\\\Webtrees\\\\Services\\\\IndividualFactsService\\:\\:spouseFacts\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\LinkedRecordService\\:\\:allLinkedRecords\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> but returns Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\), Closure\\(string\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Location, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Location given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Note, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Note given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Repository, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Repository given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$p_place on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\MapDataService\\:\\:activePlaces\\(\\) should return array\\<string, array\\<object\\>\\> but returns array\\<string, array\\<int, mixed\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\MapDataService\\:\\:placeIdsForLocation\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(object\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(object\\)\\: string given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_float\\(\\) with int will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(string\\)\\: non\\-falsy\\-string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<string, string\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<string, string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:sort\\(\\) expects \\(callable\\(mixed, mixed\\)\\: int\\)\\|int\\|null, Closure\\(string, string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$location of method League\\\\Flysystem\\\\FilesystemReader\\:\\:listContents\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method setName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:coreModules\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:customModules\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:setupLanguages\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleLanguageInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<int\\|string, object\\>, Closure\\(object\\)\\: non\\-empty\\-array\\<int\\|string, object\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\|null\\>\\:\\:mapWithKeys\\(\\) expects callable\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\|null, int\\)\\: array\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\>, Closure\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\)\\: non\\-empty\\-array\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\>\\:\\:sort\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\)\\: int\\)\\|int\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleLanguageInterface, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleLanguageInterface\\)\\: int\\<\\-1, 1\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of static method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:make\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>\\|null, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$change_id on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$change_time on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$new_gedcom on mixed\\.$#',
    'count' => 8,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$old_gedcom on mixed\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$record on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\PendingChangesService\\:\\:pendingChanges\\(\\) should return array\\<array\\<object\\>\\> but returns array\\<int\\|string, array\\<int, mixed\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual\\:\\:childFamilies\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual\\:\\:sex\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual\\:\\:spouseFamilies\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:matchRelationships\\(\\) should return array\\<Fisharebest\\\\Webtrees\\\\Relationship\\> but returns array\\<array\\<string\\>\\|Fisharebest\\\\Webtrees\\\\Relationship\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$individual of method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:reflexivePronoun\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function array_reduce expects callable\\(array\\{string, string\\}, Fisharebest\\\\Webtrees\\\\Relationship\\)\\: array\\{string, string\\}, Closure\\(array, array\\)\\: array\\{string, string\\} given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$person1 of method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:legacyNameAlgorithm\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual\\|null, Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$person2 of method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:legacyNameAlgorithm\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual\\|null, Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchFamilyNames\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchIndividualNames\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchLocations\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Location\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchMedia\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Media\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchNotes\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Note\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchPlaces\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Place\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchRepositories\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Repository\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSharedNotes\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\SharedNote\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSources\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Source\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSourcesByName\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Source\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSubmissions\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Submission\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSubmitters\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Submitter\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$haystack of function mb_stripos expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverErrors\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, string\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverWarnings\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, string\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of function explode expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TimeoutService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<int\\|string, Fisharebest\\\\Webtrees\\\\Tree\\>, Closure\\(object\\)\\: non\\-empty\\-array\\<int\\|string, Fisharebest\\\\Webtrees\\\\Tree\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function fclose expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function feof expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function fread expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function stream_filter_append expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\UpgradeService\\:\\:downloadFile\\(\\) should return int but returns int\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<int,string\\>\\:\\:contains\\(\\) expects \\(callable\\(string, int\\)\\: bool\\)\\|string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$location of method League\\\\Flysystem\\\\FilesystemWriter\\:\\:delete\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function fclose expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function ftell expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function fwrite expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$stream of function rewind expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\User, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\User given\\.$#',
    'count' => 13,
    'path' => __DIR__ . '/app/Services/UserService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Site\\:\\:getPreference\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Site.php',
];
$ignoreErrors[] = [
    'message' => '#^Static property Fisharebest\\\\Webtrees\\\\Site\\:\\:\\$preferences \\(array\\<string, string\\>\\) does not accept array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Site.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\:\\:statsAgeQuery\\(\\) should return array\\<array\\<stdClass\\>\\> but returns array\\<stdClass\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\.\\.\\.\\$params of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:totalGivennames\\(\\) expects string, array\\<string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\.\\.\\.\\$params of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:totalSurnames\\(\\) expects string, array\\<string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$century of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Service\\\\CenturyService\\:\\:centuryName\\(\\) expects int, int\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartBirth.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartChildren.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDeath.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDistribution.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDistribution.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDivorce.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Google\\\\ChartFamilyLargest\\:\\:queryRecords\\(\\) should return array\\<object\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartFamilyLargest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriage.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$century of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Service\\\\CenturyService\\:\\:centuryName\\(\\) expects int, int\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Google\\\\ChartNoChildrenFamilies\\:\\:queryRecords\\(\\) should return array\\<object\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartNoChildrenFamilies.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$request of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:contactLink\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/ContactRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Property object\\{id\\: string, year\\: int, fact\\: string, type\\: string\\}\\:\\:\\$year is not writable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Property object\\{id\\: string, year\\: int, fact\\: string, type\\: string\\}\\:\\:\\$year is not writable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$age on mixed\\.$#',
    'count' => 9,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$famid on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$family on mixed\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$i_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method canShow\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method formatList\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getBirthDate\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method husband\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method wife\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to float\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\FamilyRepository\\:\\:ageBetweenSiblingsQuery\\(\\) should return array\\<object\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\FamilyRepository\\:\\:statsChildrenQuery\\(\\) should return array\\<stdClass\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: stdClass, Closure\\(stdClass\\)\\: stdClass given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/HitCountRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$days on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGiven\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemale\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMale\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknown\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:statsAgeQuery\\(\\) should return array\\<stdClass\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$ of closure expects object, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of function array_walk expects callable\\(int, string\\)\\: mixed, Closure\\(string, string\\)\\: void given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$given of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Google\\\\ChartCommonGiven\\:\\:chartCommonGiven\\(\\) expects array\\<int\\>, array\\<int\\>\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/LatestUserRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/MediaRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$tree on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/PlaceRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/PlaceRepository.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\SurnameTradition\\\\PatrilinealSurnameTradition\\:\\:inflect\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SurnameTradition/PatrilinealSurnameTradition.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SurnameTradition/PatrilinealSurnameTradition.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$resn on mixed\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$tag_type on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method acceptRecord\\(\\) on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Tree\\:\\:getPreference\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Tree\\:\\:\\$preferences \\(array\\<string\\>\\) does not accept array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Tree\\:\\:\\$user_preferences \\(array\\<array\\<string\\>\\>\\) does not accept array\\<array\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method find\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/TreeUser.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\User\\:\\:\\$preferences \\(array\\<string, string\\>\\) does not accept array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$url of function parse_url expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Validator.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$serverRequestFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\ServerRequestFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$uriFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\UriFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$uploadedFileFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\UploadedFileFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#4 \\$streamFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\StreamFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    'message' => '#^Left side of \\|\\| is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Offset int does not exist on array\\<string, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, mixed given\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Right side of \\|\\| is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$list in empty\\(\\) always exists and is not falsy\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/fix-level-0-media-action.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/import-complete.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/import-fail.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/import-progress.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type object supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 0 on object\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset mixed on object\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$tree of method Fisharebest\\\\Webtrees\\\\Module\\\\PlaceHierarchyListModule\\:\\:listUrl\\(\\) expects Fisharebest\\\\Webtrees\\\\Tree, Fisharebest\\\\Webtrees\\\\Tree\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, object given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int\\<1, max\\> given\\.$#',
    'count' => 12,
    'path' => __DIR__ . '/resources/views/admin/media-upload.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/trees-merge.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method title\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/trees-merge.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/resources/views/calendar-page.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$all_facts contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$menus contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, int\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/badge.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$value on left side of \\?\\? always exists and is not nullable\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/components/checkbox-inline.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$value on left side of \\?\\? always exists and is not nullable\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/components/checkbox.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/select-number.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/select-number.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/select-place.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/select.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int\\|string given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/edit-blocks-block.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/change-family-members.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int\\<1, max\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/change-family-members.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/edit-gedcom-fields.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method formatFirstMajorFact\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/edit/reorder-children.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-children.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getBirthDate\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-children.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method formatFirstMajorFact\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/resources/views/edit/reorder-families.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/edit/reorder-families.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getMarriageDate\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/edit/reorder-families.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method displayImage\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-media.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-media.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomEditService\\:\\:factsToAdd\\(\\) expects Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\GedcomRecord given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-add-new.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-association-structure.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-association-structure.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-gedcom-fields.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-place.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/help/date.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method displayImage\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/individual-page-images.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/layouts/administration.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method genealogyMenu\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method stylesheets\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method userMenu\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/lists/anniversaries-list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/lists/anniversaries-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/lists/individuals-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/lists/locations-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/lists/media-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/resources/views/lists/notes-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/lists/repositories-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method value\\(\\) on Fisharebest\\\\Webtrees\\\\Fact\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/lists/sources-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/resources/views/lists/sources-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/lists/submitters-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/ancestors-chart/page.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/block-template.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/census-assistant.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method tree\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/census-assistant.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/charts/chart.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/descendancy_chart/page.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/resources/views/modules/family_nav/sidebar-family.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/faq/show.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 18,
    'path' => __DIR__ . '/resources/views/modules/favorites/favorites.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/gedcom_news/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$GOOGLE_ANALYTICS_ID on left side of \\?\\? always exists and is not nullable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/google-analytics/snippet-v4.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$GOOGLE_ANALYTICS_ID on left side of \\?\\? always exists and is not nullable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/google-analytics/snippet.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/notes/tab.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of method Fisharebest\\\\Webtrees\\\\Elements\\\\SubmitterText\\:\\:value\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/notes/tab.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/notes/tab.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$ancestors contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/pedigree-chart/chart-up.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$links contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/pedigree-chart/chart-up.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\(mixed\\) of echo cannot be converted to string\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/pedigree-chart/chart-up.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:displayImage\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/pedigree-map/events.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, string given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/place-hierarchy/sidebar.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\ModuleAnalyticsInterface\\:\\:externalUrl\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/privacy-policy/page.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/config.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/relatives/family.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/share-anniversary/share.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method date\\(\\) on Fisharebest\\\\Webtrees\\\\Fact\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/sitemap/sitemap-file-xml.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, float given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/sitemap/sitemap-file-xml.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:husband\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/timeline-chart/chart.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:wife\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/timeline-chart/chart.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/timeline-chart/chart.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/todo/research-tasks.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'count\' on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset \'record\' on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method fullName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method url\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/user_blog/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|string\\|null, int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/table.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list-age.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list-grand.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list-spouses.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-nolist-grand.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-nolist-spouses.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/column.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/combo.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/custom.phtml',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/pie.phtml',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method createServerRequest\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method createStreamFromFile\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method createUploadedFile\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:withConsecutive\\(\\) return type with generic class PHPUnit\\\\Framework\\\\Constraint\\\\Callback does not specify its types\\: CallbackInput$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$object of method Fisharebest\\\\Webtrees\\\\Contracts\\\\ContainerInterface\\:\\:set\\(\\) expects object, Fisharebest\\\\Webtrees\\\\Tree\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 0 on array\\<int, string\\>\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Encodings/AnselTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access offset 1 on array\\<int, string\\>\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Encodings/AnselTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method createUri\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/Middleware/CheckCsrfTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$params of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, int\\|string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/FixLevel0MediaActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$query of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, int\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/FixLevel0MediaDataTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$query of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, array\\<string, string\\>\\|string\\> given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/ManageMediaDataTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<TKey of \\(int\\|string\\), TValue\\>\\|iterable\\<TKey of \\(int\\|string\\), TValue\\>\\|null, \'error\' given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/PingTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<TKey of \\(int\\|string\\), TValue\\>\\|iterable\\<TKey of \\(int\\|string\\), TValue\\>\\|null, \'warning\' given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/PingTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectAncestryPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\AncestorsChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectAncestryPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectAncestryPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectAncestryPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectBranchesPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectBranchesPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectCalendarPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectCompactPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\CompactTreeChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectCompactPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectCompactPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectCompactPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectDescendancyPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\DescendancyChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectDescendancyPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectDescendancyPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectDescendancyPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\FamilyListModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamilyBookPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\FamilyBookChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamilyBookPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamilyBookPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamilyBookPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\FamilyFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamilyPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFamilyPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFanChartPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\FanChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFanChartPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFanChartPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectFanChartPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\GedcomRecordFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectGedRecordPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectGedRecordPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectHourGlassPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\HourglassChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectHourGlassPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectHourGlassPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectHourGlassPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\IndividualListModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectIndiListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectIndiListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectIndiListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectIndividualPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectIndividualPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\LifespansChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectLifeSpanPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectLifeSpanPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectLifeSpanPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\MediaListModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectMediaListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectMediaListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectMediaListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\MediaFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectMediaViewerPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectMediaViewerPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectModulePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\InteractiveTreeModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectModulePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\PedigreeMapModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectModulePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectModulePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectModulePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\NoteListModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectNoteListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectNoteListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectNoteListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\NoteFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectNotePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectNotePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectPedigreePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\PedigreeChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectPedigreePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectPedigreePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectPedigreePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\PlaceHierarchyListModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectPlaceListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectPlaceListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectPlaceListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRelationshipPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\RelationshipsChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRelationshipPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRelationshipPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRelationshipPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\RepositoryListModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRepoListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRepoListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRepoListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectReportEnginePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\RepositoryFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRepositoryPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectRepositoryPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\SourceListModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectSourceListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectSourceListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectSourceListPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\SourceFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectSourcePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectSourcePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\StatisticsChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectStatisticsPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectStatisticsPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectStatisticsPhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectTimeLinePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Module\\\\TimelineChartModule&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectTimeLinePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectTimeLinePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService&PHPUnit\\\\Framework\\\\MockObject\\\\Stub\\:\\:expects\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectTimeLinePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$query of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, array\\<int, string\\>\\|string\\> given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectTimeLinePhpTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$params of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, int\\|string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/UserEditActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/AhnentafelReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/AhnentafelReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthDeathMarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthDeathMarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/CemeteryReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/CemeteryReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/ChangeReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/ChangeReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DeathReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DeathReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DescendancyReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DescendancyReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FactSourcesReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FactSourcesReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FamilyGroupReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FamilyGroupReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualFamiliesReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualFamiliesReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MissingFactsReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MissingFactsReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/OccupationReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/OccupationReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/PedigreeReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/PedigreeReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/RelatedIndividualsReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/RelatedIndividualsReportModuleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method tag\\(\\) on Fisharebest\\\\Webtrees\\\\Fact\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Services/GedcomEditServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$interface of method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:findByInterface\\(\\) expects class\\-string\\<Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\|Psr\\\\Http\\\\Server\\\\MiddlewareInterface\\>, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/ModuleServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type T in call to method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:findByInterface\\(\\)$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/ModuleServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method object\\:\\:iniGet\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/TimeoutServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method method\\(\\) on object\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Services/TimeoutServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$option of function ini_get expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/TimeoutServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method id\\(\\) on Fisharebest\\\\Webtrees\\\\User\\|null\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/tests/app/Services/UserServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/TreeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/tests/app/TreeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method PHPUnit\\\\Framework\\\\MockObject\\\\Builder\\\\InvocationStubber\\:\\:with\\(\\)\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/ValidatorTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$valueMap of method PHPUnit\\\\Framework\\\\MockObject\\\\Builder\\\\InvocationStubber\\:\\:willReturnMap\\(\\) expects array\\<int, array\\<int, mixed\\>\\>, array\\<string, Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/feature/RelationshipNamesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$valueMap of method PHPUnit\\\\Framework\\\\MockObject\\\\Builder\\\\InvocationStubber\\:\\:willReturnMap\\(\\) expects array\\<int, array\\<int, mixed\\>\\>, array\\<string, Fisharebest\\\\Webtrees\\\\Individual\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/feature/RelationshipNamesTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
