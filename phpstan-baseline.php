<?php

declare(strict_types=1);

$ignoreErrors = [];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method find\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Auth.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Census/CensusColumnRelationToHead.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$filename of class Fisharebest\\\\Localization\\\\Translation constructor expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/CompilePoFiles.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$path of function basename expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/CompilePoFiles.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$path of function dirname expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/CompilePoFiles.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$name of method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/TreeCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$title of method Fisharebest\\\\Webtrees\\\\Services\\\\TreeService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/TreeCreate.php',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Cli\\\\Commands\\\\TreeExport\\:\\:autoCompleteTreeName\\(\\) return type has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/TreeExport.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$email of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/UserCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$email of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByEmail\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/UserCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$password of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/UserCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$real_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/UserCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$setting_value of method Fisharebest\\\\Webtrees\\\\User\\:\\:setPreference\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Cli/Commands/UserCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$user_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/UserCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$user_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByUserName\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Commands/UserCreate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$command of method Symfony\\\\Component\\\\Console\\\\Application\\:\\:add\\(\\) expects Symfony\\\\Component\\\\Console\\\\Command\\\\Command, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Cli/Console.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$id of method Fisharebest\\\\Webtrees\\\\Container\\<T of object\\>\\:\\:get\\(\\) expects class\\-string\\<T of object\\>, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Container.php',
];
$ignoreErrors[] = [
    // identifier: classConstant.unused
    'message' => '#^Constant Fisharebest\\\\Webtrees\\\\DB\\:\\:COLLATION_ASCII is unused\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/DB.php',
];
$ignoreErrors[] = [
    // identifier: classConstant.unused
    'message' => '#^Constant Fisharebest\\\\Webtrees\\\\DB\\:\\:COLLATION_UTF8 is unused\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/DB.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\DB\\:\\:driverName\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/DB.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Elements\\\\AgeAtEvent\\:\\:value\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/AgeAtEvent.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/Census.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/Census.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Elements\\\\DateValue\\:\\:escape\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/DateValue.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/PlaceHierarchy.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Elements\\\\RestrictionNotice\\:\\:canonical\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Elements/RestrictionNotice.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Encodings\\\\ANSEL\\:\\:fromUtf8\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Encodings/ANSEL.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Encodings/ANSEL.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$array of function array_map expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Encodings/AbstractEncoding.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$value of method Fisharebest\\\\Webtrees\\\\Contracts\\\\ElementInterface\\:\\:canonical\\(\\) expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Fact.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$f_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$f_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Family but returns Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\FamilyFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/FamilyFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/GedcomRecordFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/GedcomRecordFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\GedcomRecord but returns Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/GedcomRecordFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\GedcomRecordFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/GedcomRecordFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/HeaderFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/HeaderFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Header but returns Fisharebest\\\\Webtrees\\\\Header\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/HeaderFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\HeaderFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/HeaderFactory.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$num of function dechex expects int, float\\|int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IdFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$i_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IndividualFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$i_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IndividualFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Individual but returns Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IndividualFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\IndividualFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/IndividualFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/LocationFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/LocationFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Location but returns Fisharebest\\\\Webtrees\\\\Location\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/LocationFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\LocationFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/LocationFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/MediaFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/MediaFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Media but returns Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/MediaFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\MediaFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/MediaFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/NoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/NoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Note but returns Fisharebest\\\\Webtrees\\\\Note\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/NoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\NoteFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/NoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RepositoryFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RepositoryFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\RepositoryFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RepositoryFactory.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getGenerator\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RouteFactory.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getMap\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/RouteFactory.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Factories/RouteFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SharedNoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SharedNoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\SharedNote but returns Fisharebest\\\\Webtrees\\\\SharedNote\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SharedNoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SharedNoteFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SharedNoteFactory.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SlugFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$s_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SourceFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$s_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SourceFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Source but returns Fisharebest\\\\Webtrees\\\\Source\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SourceFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SourceFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SourceFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmissionFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmissionFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Submission but returns Fisharebest\\\\Webtrees\\\\Submission\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmissionFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SubmissionFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmissionFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmitterFactory.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmitterFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Submitter but returns Fisharebest\\\\Webtrees\\\\Submitter\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmitterFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Factories\\\\SubmitterFactory\\:\\:gedcom\\(\\) should return string\\|null but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/SubmitterFactory.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Factories/XrefFactory.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Family\\:\\:spouses\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> but returns Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Family.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fromUtf8\\(\\) on Fisharebest\\\\Webtrees\\\\Encodings\\\\EncodingInterface\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/GedcomFilters/GedcomEncodingFilter.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method acceptRecord\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$array of function array_shift expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateFact\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:\\$getAllNames \\(array\\<array\\<string\\>\\>\\) does not accept array\\<array\\<string\\|null\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/GedcomRecord.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method createResponse\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Helpers/functions.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Helpers/functions.php',
];
$ignoreErrors[] = [
    // identifier: identical.alwaysFalse
    'message' => '#^Strict comparison using \\=\\=\\= between \'\\-dev\' and \'\' will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Helpers/functions.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$host of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withHost\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$path of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withPath\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$port of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withPort\\(\\) expects int\\|null, int\\<0, 65535\\>\\|false\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$scheme of method Psr\\\\Http\\\\Message\\\\UriInterface\\:\\:withScheme\\(\\) expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/BaseUrl.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of function explode expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/ClientIp.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method withAttribute\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$message of static method Fisharebest\\\\Webtrees\\\\Log\\:\\:addErrorLog\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\HandleExceptions\\:\\:httpExceptionResponse\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\HandleExceptions\\:\\:thirdPartyExceptionResponse\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\HandleExceptions\\:\\:unhandledExceptionResponse\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/Middleware/HandleExceptions.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$basepath of class Aura\\\\Router\\\\RouterContainer constructor expects string\\|null, string\\|false\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/LoadRoutes.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/PublicFiles.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/ReadConfigIni.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method handle\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/RequestHandler.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Http\\\\Middleware\\\\UseTransaction\\:\\:process\\(\\) should return Psr\\\\Http\\\\Message\\\\ResponseInterface but returns Psr\\\\Http\\\\Message\\\\ResponseInterface\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Middleware/UseTransaction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AppleTouchIconPng.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AutoCompleteCitation.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AutoCompleteCitation.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/AutoCompleteCitation.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CalendarEvents.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CalendarEvents.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CalendarEvents.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom\\.$#',
    'count' => 11,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CheckTree.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$type\\.$#',
    'count' => 38,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CheckTree.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 46,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CheckTree.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/CheckTree.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 8,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ControlPanel.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixData.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Module\\\\ModuleDataFixInterface\\>\\:\\:get\\(\\) expects int, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixSelect.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixUpdateAll.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixUpdateAll.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on object\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DataFixUpdateAll.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\DeleteRecord\\:\\:removeLinks\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DeleteRecord.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DeleteRecord.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/DeleteUser.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditFactAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateFact\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditFactAction.php',
];
$ignoreErrors[] = [
    // identifier: if.alwaysTrue
    'message' => '#^If condition is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditMediaFileAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateRecord\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditNoteAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditRawFactAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/EditRawRecordAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FaviconIco.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$i_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$i_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_file\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method displayImage\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method facts\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/FixLevel0MediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$chunk_data\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/app/Http/RequestHandlers/GedcomLoad.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom_chunk_id\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Http/RequestHandlers/GedcomLoad.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/GedcomLoad.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_file\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$media_folder\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$multimedia_file_refn\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'0\' on array\\{0\\: int\\<0, max\\>, 1\\: int\\<0, max\\>, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'1\' on array\\{0\\: int\\<0, max\\>, 1\\: int\\<0, max\\>, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function strlen expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$needle of function str_starts_with expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ManageMediaData.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataAdd.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataDelete.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$latitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$longitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportCSV.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$latitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$longitude on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataExportGeoJson.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$features on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataImportAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$haystack of function str_contains expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataImportAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataImportAction.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MapDataList.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateRecord\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MergeFactsAction.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/MergeTreesAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$haystack of function str_contains expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ModuleAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$module_name of method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:findByName\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ModuleAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$token of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByToken\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PasswordResetAction.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$token of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByToken\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PasswordResetPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$change_id of method Fisharebest\\\\Webtrees\\\\Services\\\\PendingChangesService\\:\\:acceptChange\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesAcceptChange.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$change_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$change_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$new_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$old_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$status\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method setTimezone\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$change_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$new_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$old_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$status\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(object\\)\\: non\\-falsy\\-string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogDownload.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method setTimezone\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$datetime of static method DateTimeImmutable\\:\\:createFromFormat\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesLogPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$change_id of method Fisharebest\\\\Webtrees\\\\Services\\\\PendingChangesService\\:\\:rejectChange\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PendingChangesRejectChange.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/PhpInformation.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ReportGenerate.php',
];
$ignoreErrors[] = [
    // identifier: nullCoalesce.offset
    'message' => '#^Offset \'inputs\' on array\\{title\\: string, description\\: string, inputs\\: array\\<array\\{name\\: string, type\\: string, lookup\\: string, options\\: string, default\\: string, value\\: string\\}\\>\\} on left side of \\?\\? always exists and is not nullable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/ReportSetupPage.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchAdvancedPage.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Location\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Note\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Source\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchGeneralPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:updateRecord\\(\\) expects string, string\\|null given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SearchReplaceAction.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method isNotEmpty\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method push\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method withAttribute\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$code of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:init\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$driver of method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverErrors\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$driver of method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverWarnings\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$identifier of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByIdentifier\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$url of function redirect expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$user_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|float\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|int\\|string\\|null, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$wtname of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$real_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$setting_value of method Fisharebest\\\\Webtrees\\\\User\\:\\:setPreference\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$wtuser of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$email of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$wtpass of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#4 \\$password of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:create\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#4 \\$wtemail of method Fisharebest\\\\Webtrees\\\\Http\\\\RequestHandlers\\\\SetupWizard\\:\\:checkAdminUser\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$ca of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$certificate of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$database of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$driver of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$host of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$key of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$password of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$port of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$prefix of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\$username of static method Fisharebest\\\\Webtrees\\\\DB\\:\\:connect\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SetupWizard.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$ip_address\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$log_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$log_message\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$log_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$log_type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method setTimezone\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$ip_address\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$log_message\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$log_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$log_type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(object\\)\\: non\\-falsy\\-string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsDownload.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method setTimezone\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$datetime of static method DateTimeImmutable\\:\\:createFromFormat\\(\\) expects string, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SiteLogsPage.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/SynchronizeTrees.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePageBlock.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePreferencesAction.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$label\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePrivacyPage.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePrivacyPage.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tag_label\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePrivacyPage.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tag_type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePrivacyPage.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePrivacyPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/TreePrivacyPage.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$l_from on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$l_to on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<string, array\\{\\}\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<string, array\\{\\}\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function usort expects callable\\(array\\<Fisharebest\\\\Webtrees\\\\Individual\\>\\|Illuminate\\\\Support\\\\Collection\\<int\\|string, Fisharebest\\\\Webtrees\\\\Individual\\>, array\\<Fisharebest\\\\Webtrees\\\\Individual\\>\\|Illuminate\\\\Support\\\\Collection\\<int\\|string, Fisharebest\\\\Webtrees\\\\Individual\\>\\)\\: int, Closure\\(Illuminate\\\\Support\\\\Collection, Illuminate\\\\Support\\\\Collection\\)\\: int\\<\\-1, 1\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UnconnectedPage.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$active_at\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$email\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$language\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$real_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$registered_at\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_id\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$verified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$verified_by_admin\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: argument.templateType
    'message' => '#^Unable to resolve the template type TGetDefault in call to method Illuminate\\\\Support\\\\Collection\\<string,string\\>\\:\\:get\\(\\)$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserListData.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/UserPageBlock.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$user_name of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:findByUserName\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/RequestHandlers/VerifyEmail.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/I18N.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method spouseFamilies\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$haystack of function strpos expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|null given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function substr_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:\\$getAllNames \\(array\\<array\\<string\\>\\>\\) does not accept array\\<array\\<string\\|null\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Individual.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Log.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$xref of method Fisharebest\\\\Webtrees\\\\Contracts\\\\GedcomRecordFactoryInterface\\:\\:make\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Media.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$n_surn\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$n_surname\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$count on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$n_givn on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$n_surn on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{n_surn\\: mixed, n_surname\\: mixed, total\\: int\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{n_surn\\: mixed, n_surname\\: mixed, total\\: int\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: method.unresolvableReturnType
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: method.unresolvableReturnType
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),object\\{n_surn\\: mixed, n_surname\\: mixed, total\\: int\\}&stdClass\\>\\:\\:all\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractIndividualListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$interface\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$module_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$access_level on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\AbstractModule\\:\\:getPreference\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:first\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(object\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/AbstractModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/BingWebmasterToolsModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/BranchesListModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/BranchesListModule.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\|string supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method object\\:\\:censusLanguage\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method updateFact\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: foreach.emptyArray
    'message' => '#^Empty array passed to foreach\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:censusTableEmptyRow\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:censusTableHeader\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:censusTableRow\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$census of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:createNoteText\\(\\) expects Fisharebest\\\\Webtrees\\\\Census\\\\CensusInterface, object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#5 \\$ca_individuals of method Fisharebest\\\\Webtrees\\\\Module\\\\CensusAssistantModule\\:\\:createNoteText\\(\\) expects array\\<array\\<string\\>\\>, array\\<string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CensusAssistantModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ChartsMenuModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ClippingsCartModule.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/CloudsTheme.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FabTheme.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$note\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FamilyTreeFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$body\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeNewsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$subject\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeNewsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$updated\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FamilyTreeNewsModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeNewsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$n_surn on mixed\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$n_surname on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$total on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method embedTags\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FamilyTreeStatisticsModule.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset 0 on array\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset 4 on array\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method alternateName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method individualBoxMenu\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method lifespan\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method sex\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FanChartModule\\:\\:imageColor\\(\\) should return int but returns int\\<0, max\\>\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$individual of method Fisharebest\\\\Webtrees\\\\Module\\\\FanChartModule\\:\\:chartTitle\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$color of function imagecolortransparent expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#6 \\$color of function imagefilledrectangle expects int, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FanChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixCemeteryTag.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixCemeteryTag.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixDuplicateLinks.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixDuplicateLinks\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixDuplicateLinks.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixDuplicateLinks.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixDuplicateLinks.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixMissingDeaths.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixMissingDeaths.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixNameSlashesAndSpaces.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixNameSlashesAndSpaces\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixNameSlashesAndSpaces.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixNameSlashesAndSpaces.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixNameSlashesAndSpaces.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixNameTags.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixNameTags.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixPlaceNames.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixPlaceNames\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixPlaceNames.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixPlaceNames.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixPrimaryTag.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixPrimaryTag.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixSearchAndReplace.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\FixSearchAndReplace\\:\\:updateGedcom\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixSearchAndReplace.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixSearchAndReplace.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FixWtObjeSortTag.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\), Closure\\(string\\)\\: \\(object\\{xref\\: string, type\\: string\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FixWtObjeSortTag.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$block_id\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$block_order\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom_id\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$languages\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(object\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/FrequentlyAskedQuestionsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$geonames on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/GeonamesAutocomplete.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/GoogleAnalyticsModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/GoogleAnalyticsModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/GoogleWebmasterToolsModule.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/HitCountFooterModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method embedTags\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/HtmlBlockModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,string\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>, Illuminate\\\\Support\\\\Collection\\<int, mixed\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/IndividualFactsTabModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/InteractiveTree/TreeView.php',
];
$ignoreErrors[] = [
    // identifier: empty.offset
    'message' => '#^Offset 1 on array\\{Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Family\\} in empty\\(\\) always exists and is not falsy\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/InteractiveTree/TreeView.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$familyList of method Fisharebest\\\\Webtrees\\\\Module\\\\InteractiveTree\\\\TreeView\\:\\:drawChildren\\(\\) expects Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\|null\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/InteractiveTree/TreeView.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageAfrikaans.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageAlbanian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageArabic.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageBasque.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageBosnian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageBulgarian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageCatalan.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageChineseSimplified.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageChineseTraditional.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageCroatian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageCzech.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageDanish.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageDivehi.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageDutch.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageEnglishUnitedStates.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageEstonian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFaroese.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFarsi.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFinnish.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageFrench.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGalician.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGeorgian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGerman.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageGreek.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageHebrew.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageHindi.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageHungarian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageIcelandic.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageIndonesian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageItalian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageJapanese.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageJavanese.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageKazhak.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageKorean.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageKurdish.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageLatvian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageLingala.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageLithuanian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageMalay.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageMaori.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageMarathi.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageNepalese.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageNorwegianBokmal.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageNorwegianNynorsk.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageOccitan.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguagePolish.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguagePortuguese.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguagePortugueseBrazil.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageRomanian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageRussian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSerbian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSerbianLatin.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSlovakian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSlovenian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSpanish.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSundanese.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSwahili.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageSwedish.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTagalog.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTamil.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTatar.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageThai.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageTurkish.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageUkranian.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageUrdu.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageUzbek.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageVietnamese.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageWelsh.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LanguageYiddish.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$row\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LifespansChartModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\LifespansChartModule\\:\\:findIndividualsByDate\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LifespansChartModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\LifespansChartModule\\:\\:findIndividualsByPlace\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LifespansChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Menu\\|null\\>\\:\\:sort\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Menu\\|null, Fisharebest\\\\Webtrees\\\\Menu\\|null\\)\\: int\\)\\|int\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Menu, Fisharebest\\\\Webtrees\\\\Menu\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ListsMenuModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#5 \\$submenus of class Fisharebest\\\\Webtrees\\\\Menu constructor expects array\\<Fisharebest\\\\Webtrees\\\\Menu\\>, array\\<int, Fisharebest\\\\Webtrees\\\\Menu\\|null\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ListsMenuModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Location, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Location given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LocationListModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method usersLoggedInList\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/LoggedInUsersModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\MapGeoLocationGeonames\\:\\:extractLocationsFromResponse\\(\\) should return array\\<string\\> but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MapGeoLocationGeonames.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\MapGeoLocationNominatim\\:\\:extractLocationsFromResponse\\(\\) should return array\\<string\\> but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MapGeoLocationNominatim.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\MapGeoLocationOpenRouteService\\:\\:extractLocationsFromResponse\\(\\) should return array\\<string\\> but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MapGeoLocationOpenRouteService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MatomoAnalyticsModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MediaListModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(string\\)\\: string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MediaListModule.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/MinimalTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Note, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Note given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/NoteListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$features on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/OpenRouteServiceAutocomplete.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$individual of method Fisharebest\\\\Webtrees\\\\Module\\\\PedigreeChartModule\\:\\:nextLink\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/PedigreeChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$length of function array_chunk expects int\\<1, max\\>, int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/PlaceHierarchyListModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$features\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/PlacesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$change_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$new_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{record\\: Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, time\\: Fisharebest\\\\Webtrees\\\\Contracts\\\\TimestampInterface, user\\: Fisharebest\\\\Webtrees\\\\User\\|null\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{record\\: Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, time\\: Fisharebest\\\\Webtrees\\\\Contracts\\\\TimestampInterface, user\\: Fisharebest\\\\Webtrees\\\\User\\|null\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RecentChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getMap\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RedirectLegacyUrlsModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual but returns Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual but returns Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$l_from on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$l_to on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method gedcom\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method sex\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\RelationshipsChartModule\\:\\:allAncestors\\(\\) should return array\\<string\\> but returns array\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Module\\\\RelationshipsChartModule\\:\\:excludeFamilies\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$content of function response expects array\\|object\\|string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RelationshipsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReportsMenuModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Repository, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Repository given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/RepositoryListModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ResearchTaskModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ResearchTaskModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ResearchTaskModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$new_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$old_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method canShow\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ReviewChangesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: bool given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/ShareAnniversaryModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\)\\: array\\<string, string\\>, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: non\\-empty\\-array\\<string, non\\-falsy\\-string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ShareAnniversaryModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Fact\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>, Illuminate\\\\Support\\\\Collection\\<int, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ShareAnniversaryModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Note, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Note given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Repository, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Repository given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Submitter, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Submitter given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SiteMapModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$m_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$m_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:first\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(object\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SlideShowModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SourceListModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:serverParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StatcounterModule.php',
];
$ignoreErrors[] = [
    // identifier: binaryOp.invalid
    'message' => '#^Binary operation "\\+" between int and int\\|string results in an error\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: binaryOp.invalid
    'message' => '#^Binary operation "\\-" between string and 1 results in an error\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: binaryOp.invalid
    'message' => '#^Binary operation "/" between stdClass and 365\\.25 results in an error\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$d_month on mixed\\.$#',
    'count' => 11,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$f_husb on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$f_wife on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$i_sex on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$month on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$total on mixed\\.$#',
    'count' => 11,
    'path' => __DIR__ . '/app/Module/StatisticsChartModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$block_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$individual\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$languages\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$title\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$block_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$individual on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$languages on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$title on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: cast.string
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/StoriesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Submitter, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Submitter given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/SubmitterListModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method menuThemes\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/ThemeSelectModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\:\\:filter\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Individual\\|null, int\\|string\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\:\\:filter\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Individual\\|null, int\\|string\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Individual\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Fisharebest\\\\Webtrees\\\\Individual\\|null\\>\\:\\:map\\(\\) expects callable\\(Fisharebest\\\\Webtrees\\\\Individual\\|null, int\\|string\\)\\: string, Closure\\(Fisharebest\\\\Webtrees\\\\Individual\\)\\: string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TimelineChartModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method commonGivenFemaleListTotals\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method commonGivenFemaleTable\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method commonGivenMaleListTotals\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method commonGivenMaleTable\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopGivenNamesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$page_count on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopPageViewsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$page_parameter on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopPageViewsModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$n_surn on mixed\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/app/Module/TopSurnamesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$n_surname on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/TopSurnamesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$total on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/TopSurnamesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$note\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/UserFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserFavoritesModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$body\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserJournalModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$subject\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserJournalModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$updated\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/UserJournalModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserJournalModule.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$created\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/UserMessagesModule.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/UserMessagesModule.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/WebtreesTheme.php',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getUri\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:queryParams\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Module/XeneaTheme.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Note\\:\\:getNote\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Note.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$xref of method Fisharebest\\\\Webtrees\\\\Contracts\\\\GedcomRecordFactoryInterface\\:\\:make\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Note.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$p_parent_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$p_place\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Place, Closure\\(string\\)\\: Fisharebest\\\\Webtrees\\\\Place given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:sort\\(\\) expects \\(callable\\(mixed, mixed\\)\\: int\\)\\|int\\|null, Closure\\(string, string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>\\|null, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function mb_substr expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$text of static method Fisharebest\\\\Webtrees\\\\Soundex\\:\\:daitchMokotoff\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$text of static method Fisharebest\\\\Webtrees\\\\Soundex\\:\\:russell\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Place.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$latitude\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$longitude\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$latitude on object\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$longitude on object\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\PlaceLocation\\:\\:boundingRectangle\\(\\) should return array\\<array\\<float\\>\\> but returns array\\<int, array\\<int, mixed\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\PlaceLocation\\:\\:details\\(\\) should return object but returns object\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: float, Closure\\(string\\)\\: float given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>\\|null, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function mb_substr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/PlaceLocation.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function fclose expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserBase.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function feof expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserBase.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function fread expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserBase.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:\\$generation\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$new_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$old_gedcom\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: property.protected
    'message' => '#^Access to protected property Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:\\$generation\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:canShow\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:privatizeGedcom\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\:\\:tree\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: function.impossibleType
    'message' => '#^Call to function assert\\(\\) with false and LogicException will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \\(float\\|int\\) on array\\<int, string\\>\\|false\\.$#',
    'count' => 13,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \\(float\\|int\\<1, max\\>\\) on array\\<int, string\\>\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset 0 on array\\{0\\: int\\<0, max\\>, 1\\: int\\<0, max\\>, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset 1 on array\\{0\\: int\\<0, max\\>, 1\\: int\\<0, max\\>, 2\\: int, 3\\: string, mime\\: string, channels\\?\\: int, bits\\?\\: int\\}\\|false\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method addElement\\(\\) on Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method childFamilies\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method facts\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findHighlightedMediaFile\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method firstImageFile\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method privatizeGedcom\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method xref\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: cast.string
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonArray
    'message' => '#^Cannot use array destructuring on array\\<int, array\\<string\\>\\|int\\>\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonArray
    'message' => '#^Cannot use array destructuring on array\\<int, string\\>\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: if.alwaysTrue
    'message' => '#^If condition is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: instanceof.alwaysFalse
    'message' => '#^Instanceof between Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer and Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseElement will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:substituteVars\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$array of function end expects array\\|object, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\), Closure\\(object\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$expression of method Symfony\\\\Component\\\\ExpressionLanguage\\\\ExpressionLanguage\\:\\:evaluate\\(\\) expects string\\|Symfony\\\\Component\\\\ExpressionLanguage\\\\Expression, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$haystack of function str_contains expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$image of function imagesx expects GdImage, GdImage\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$image of function imagesy expects GdImage, GdImage\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function addslashes expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\Family, Fisharebest\\\\Webtrees\\\\Family\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\GedcomRecord, Fisharebest\\\\Webtrees\\\\GedcomRecord\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\GedcomRecord, Fisharebest\\\\Webtrees\\\\GedcomRecord\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function uasort expects callable\\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\)\\: int, Closure\\(Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$current_element \\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseElement\\) does not accept Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<\\(object\\{generation\\: int\\}&stdClass\\)\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\|null\\>\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$list \\(array\\<Fisharebest\\\\Webtrees\\\\GedcomRecord\\|static\\(Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\)\\>\\) does not accept array\\<string, Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$parser \\(XMLParser\\) does not accept XMLParser\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$print_data \\(bool\\) does not accept bool\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$repeat_bytes \\(int\\) does not accept array\\<string\\>\\|int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$repeats \\(array\\<string\\>\\) does not accept array\\<string\\>\\|int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$vars \\(array\\<array\\<string\\>\\>\\) does not accept array\\<array\\<string\\|null\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$wt_report \\(Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\) does not accept Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserGenerate\\:\\:\\$wt_report \\(Fisharebest\\\\Webtrees\\\\Report\\\\AbstractRenderer\\) does not accept Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseTextbox\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserGenerate.php',
];
$ignoreErrors[] = [
    // identifier: offsetAssign.dimType
    'message' => '#^Cannot assign new offset to array\\<string\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserSetup.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Report\\\\ReportParserSetup\\:\\:reportProperties\\(\\) should return array\\{title\\: string, description\\: string, inputs\\: array\\<array\\{name\\: string, type\\: string, lookup\\: string, options\\: string, default\\: string, value\\: string\\}\\>\\} but returns array\\<string, array\\<string\\>\\|string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserSetup.php',
];
$ignoreErrors[] = [
    // identifier: offsetAssign.valueType
    'message' => '#^array\\<string\\>\\|string does not accept array\\<string\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportParserSetup.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$link of method TCPDF\\:\\:setLink\\(\\) expects int, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportPdfFootnote.php',
];
$ignoreErrors[] = [
    // identifier: instanceof.alwaysFalse
    'message' => '#^Instanceof between Fisharebest\\\\Webtrees\\\\Report\\\\ReportBaseElement and Fisharebest\\\\Webtrees\\\\Report\\\\ReportPdfFootnote will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/ReportPdfTextBox.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Report\\\\RightToLeftSupport\\:\\:spanLtrRtl\\(\\) should return string but returns array\\<int, string\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Report\\\\RightToLeftSupport\\:\\:starredName\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Report/RightToLeftSupport.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$access_level on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$component on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$gedcom_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$module_name on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration42.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$max\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Schema/Migration44.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$min\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Schema/Migration44.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Family but returns Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Individual but returns Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Media but returns Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Source but returns Fisharebest\\\\Webtrees\\\\Source\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\AdminService\\:\\:duplicateXrefs\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Family\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Media\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Media\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Source\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<int, Fisharebest\\\\Webtrees\\\\Source\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(string\\)\\: non\\-falsy\\-string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$table of static method Illuminate\\\\Database\\\\Capsule\\\\Manager\\:\\:table\\(\\) expects Closure\\|Illuminate\\\\Database\\\\Query\\\\Builder\\|string, Illuminate\\\\Database\\\\Query\\\\Expression given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/AdminService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$d_day on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$d_fact on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$d_month on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$d_type on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$d_year on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/CalendarService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ChartService\\:\\:descendants\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> but returns Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Individual\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ChartService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<string,Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<string, Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<string, Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ChartService.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset string on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/ClipboardService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\)\\: Fisharebest\\\\Webtrees\\\\Fact, Closure\\(string\\)\\: Fisharebest\\\\Webtrees\\\\Fact given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ClipboardService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$gedcom of method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:createFact\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ClipboardService.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.notFound
    'message' => '#^Offset \'column\' does not exist on string\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.notFound
    'message' => '#^Offset \'dir\' does not exist on string\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\)\\: bool\\)\\|null, Closure\\(array\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:sort\\(\\) expects \\(callable\\(mixed, mixed\\)\\: int\\)\\|int\\|null, Closure\\(array, array\\)\\: \\(\\-1\\|0\\|1\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/DatatablesService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$request of static method Fisharebest\\\\Webtrees\\\\Validator\\:\\:attributes\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/EmailService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomEditService\\:\\:insertMissingRecordSubtags\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/GedcomEditService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$array of function array_shift expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomEditService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$gedcom of method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomEditService\\:\\:insertMissingLevels\\(\\) expects string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/GedcomEditService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$f_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$i_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$m_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$o_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$s_gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.notFound
    'message' => '#^Offset \'uri\' does not exist on array\\{timed_out\\: bool, blocked\\: bool, eof\\: bool, unread_bytes\\: int, stream_type\\: string, wrapper_type\\: string, wrapper_data\\: mixed, mode\\: string, \\.\\.\\.\\}\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\GedcomRecord, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\GedcomRecord given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomExportService.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method canonicalTag\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomImportService\\:\\:createMediaObject\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$haystack of function str_starts_with expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$str of function strtr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 10,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/GedcomImportService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$module_name\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/HomePageService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\HomePageService\\:\\:filterActiveBlocks\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/HomePageService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<int,string\\>\\:\\:contains\\(\\) expects \\(callable\\(string, int\\)\\: bool\\)\\|string, mixed given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/HomePageService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$active_blocks of method Fisharebest\\\\Webtrees\\\\Services\\\\HomePageService\\:\\:filterActiveBlocks\\(\\) expects Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\>, Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleBlockInterface\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/HomePageService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$path of method Fisharebest\\\\Webtrees\\\\Services\\\\HousekeepingService\\:\\:deleteFileOrFolder\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/HousekeepingService.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:sex\\(\\)\\.$#',
    'count' => 17,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: offsetAssign.valueType
    'message' => '#^Illuminate\\\\Support\\\\Collection\\<\\*NEVER\\*, \\*NEVER\\*\\> does not accept Fisharebest\\\\Webtrees\\\\Fact\\.$#',
    'count' => 31,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: instanceof.alwaysFalse
    'message' => '#^Instanceof between Fisharebest\\\\Webtrees\\\\Individual and Fisharebest\\\\Webtrees\\\\Family will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\IndividualFactsService\\:\\:familyFacts\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Fact\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\IndividualFactsService\\:\\:historicFacts\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Fact\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$gedcom of class Fisharebest\\\\Webtrees\\\\Fact constructor expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Fact\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Fact\\>, Illuminate\\\\Support\\\\Collection\\<int, mixed\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Individual\\>\\:\\:merge\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>\\|iterable\\<int, Fisharebest\\\\Webtrees\\\\Individual\\>, Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$spouse of method Fisharebest\\\\Webtrees\\\\Services\\\\IndividualFactsService\\:\\:spouseFacts\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Individual\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/IndividualFactsService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\LinkedRecordService\\:\\:allLinkedRecords\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> but returns Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\), Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\), Closure\\(string\\)\\: \\(Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Location, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Location given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Note, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Note given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Repository, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Repository given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Source, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Source given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/LinkedRecordService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$child_count\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$key\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$no_coord\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$p_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$parent_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$place\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$p_place on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\MapDataService\\:\\:activePlaces\\(\\) should return array\\<string, array\\<object\\>\\> but returns array\\<string, array\\<int, mixed\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\MapDataService\\:\\:placeIdsForLocation\\(\\) should return array\\<string\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(object\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: object, Closure\\(object\\)\\: object given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(object\\)\\: string given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/MapDataService.php',
];
$ignoreErrors[] = [
    // identifier: function.impossibleType
    'message' => '#^Call to function is_float\\(\\) with int will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: string, Closure\\(string\\)\\: non\\-falsy\\-string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<string, string\\>, Closure\\(string\\)\\: non\\-empty\\-array\\<string, string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:sort\\(\\) expects \\(callable\\(mixed, mixed\\)\\: int\\)\\|int\\|null, Closure\\(string, string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$location of method League\\\\Flysystem\\\\FilesystemReader\\:\\:listContents\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/MediaFileService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$footer_order\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$menu_order\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$module_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$sidebar_order\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$status\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tab_order\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Anonymous function should return Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method setName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:coreModules\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:customModules\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:setupLanguages\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleLanguageInterface\\> but returns Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<int\\|string, object\\>, Closure\\(object\\)\\: non\\-empty\\-array\\<int\\|string, object\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\|null\\>\\:\\:mapWithKeys\\(\\) expects callable\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\|null, int\\)\\: array\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\>, Closure\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\)\\: non\\-empty\\-array\\<string, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleCustomInterface\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\>\\:\\:sort\\(\\) expects \\(callable\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\)\\: int\\)\\|int\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Module\\\\ModuleLanguageInterface, Fisharebest\\\\Webtrees\\\\Module\\\\ModuleLanguageInterface\\)\\: int\\<\\-1, 1\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of static method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:make\\(\\) expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<int, string\\>\\|iterable\\<int, string\\>\\|null, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ModuleService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$change_id on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$change_time on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$new_gedcom on mixed\\.$#',
    'count' => 8,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$old_gedcom on mixed\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$record on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method add\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method setTimezone\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\PendingChangesService\\:\\:pendingChanges\\(\\) should return array\\<array\\<object\\>\\> but returns array\\<int\\|string, array\\<int, mixed\\>\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/PendingChangesService.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual\\:\\:childFamilies\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual\\:\\:sex\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual\\:\\:spouseFamilies\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:matchRelationships\\(\\) should return array\\<Fisharebest\\\\Webtrees\\\\Relationship\\> but returns array\\<array\\<string\\>\\|Fisharebest\\\\Webtrees\\\\Relationship\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$individual of method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:reflexivePronoun\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function array_reduce expects callable\\(array\\{string, string\\}, Fisharebest\\\\Webtrees\\\\Relationship\\)\\: array\\{string, string\\}, Closure\\(array, array\\)\\: array\\{string, string\\} given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$person1 of method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:legacyNameAlgorithm\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual\\|null, Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$person2 of method Fisharebest\\\\Webtrees\\\\Services\\\\RelationshipService\\:\\:legacyNameAlgorithm\\(\\) expects Fisharebest\\\\Webtrees\\\\Individual\\|null, Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/RelationshipService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$f_file\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$i_file\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$m_file\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$o_file\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$s_file\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchFamilyNames\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Family\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchIndividualNames\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Individual\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchLocations\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Location\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchMedia\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Media\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchNotes\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Note\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchPlaces\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Place\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchRepositories\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Repository\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSharedNotes\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\SharedNote\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSources\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Source\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSourcesByName\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Source\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSubmissions\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Submission\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\SearchService\\:\\:searchSubmitters\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, Fisharebest\\\\Webtrees\\\\Submitter\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Media, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Media given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$haystack of function mb_stripos expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SearchService.php',
];
$ignoreErrors[] = [
    // identifier: cast.string
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverErrors\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, string\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\ServerCheckService\\:\\:serverWarnings\\(\\) should return Illuminate\\\\Support\\\\Collection\\<int, string\\> but returns Illuminate\\\\Support\\\\Collection\\<int, mixed\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$value of function e expects BackedEnum\\|float\\|Illuminate\\\\Contracts\\\\Support\\\\DeferringDisplayableValue\\|Illuminate\\\\Contracts\\\\Support\\\\Htmlable\\|int\\|string\\|null, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of function explode expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/ServerCheckService.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method add\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SiteLogsService.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method setTimezone\\(\\) on DateTimeImmutable\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/SiteLogsService.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TimeoutService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tree_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:mapWithKeys\\(\\) expects callable\\(mixed, int\\|string\\)\\: array\\<int\\|string, Fisharebest\\\\Webtrees\\\\Tree\\>, Closure\\(object\\)\\: non\\-empty\\-array\\<int\\|string, Fisharebest\\\\Webtrees\\\\Tree\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function fclose expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function feof expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function fread expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function stream_filter_append expects resource, resource\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/TreeService.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Services\\\\UpgradeService\\:\\:downloadFile\\(\\) should return int but returns int\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<int,string\\>\\:\\:contains\\(\\) expects \\(callable\\(string, int\\)\\: bool\\)\\|string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$location of method League\\\\Flysystem\\\\FilesystemWriter\\:\\:delete\\(\\) expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function fclose expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function ftell expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function fwrite expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$stream of function rewind expects resource, resource\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/UpgradeService.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\User, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\User given\\.$#',
    'count' => 13,
    'path' => __DIR__ . '/app/Services/UserService.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$ip_address\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SessionDatabaseHandler.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$session_data\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SessionDatabaseHandler.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$session_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SessionDatabaseHandler.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SessionDatabaseHandler.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Site\\:\\:getPreference\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Site.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Static property Fisharebest\\\\Webtrees\\\\Site\\:\\:\\$preferences \\(array\\<string, string\\>\\) does not accept array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Site.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\:\\:statsAgeQuery\\(\\) should return array\\<array\\<stdClass\\>\\> but returns array\\<stdClass\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\.\\.\\.\\$params of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:totalGivennames\\(\\) expects string, array\\<string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\.\\.\\.\\$params of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:totalSurnames\\(\\) expects string, array\\<string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$age\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$sex\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$century of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Service\\\\CenturyService\\:\\:centuryName\\(\\) expects int, int\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    // identifier: method.unresolvableReturnType
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartAge.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartBirth.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartBirth.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartBirth.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartChildren.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartChildren.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartChildren.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDeath.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDeath.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDeath.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$gedcom on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDistribution.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$place on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDistribution.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDivorce.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDivorce.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartDivorce.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartFamilyLargest.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartFamilyLargest.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Google\\\\ChartFamilyLargest\\:\\:queryRecords\\(\\) should return array\\<object\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartFamilyLargest.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriage.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriage.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{century\\: int, total\\: float\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriage.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$age\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$sex\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{age\\: float, century\\: int, sex\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$century of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Service\\\\CenturyService\\:\\:centuryName\\(\\) expects int, int\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    // identifier: method.unresolvableReturnType
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartMarriageAge.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$century\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartNoChildrenFamilies.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$total\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Google/ChartNoChildrenFamilies.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Google\\\\ChartNoChildrenFamilies\\:\\:queryRecords\\(\\) should return array\\<object\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Google/ChartNoChildrenFamilies.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$request of method Fisharebest\\\\Webtrees\\\\Services\\\\UserService\\:\\:contactLink\\(\\) expects Psr\\\\Http\\\\Message\\\\ServerRequestInterface, mixed given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/ContactRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$fact\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$year\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyReadOnly
    'message' => '#^Property object\\{id\\: string, year\\: int, fact\\: string, type\\: string\\}\\:\\:\\$year is not writable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.unresolvableReturnType
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/EventRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$fact\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$year\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\), Closure\\(object\\)\\: \\(object\\{id\\: mixed, year\\: int, fact\\: mixed, type\\: mixed\\}&stdClass\\) given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyReadOnly
    'message' => '#^Property object\\{id\\: string, year\\: int, fact\\: string, type\\: string\\}\\:\\:\\$year is not writable\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.unresolvableReturnType
    'message' => '#^Return type of call to method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) contains unresolvable type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyDatesRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$age\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$ch1\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$ch2\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$f_numchil\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$family\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$age on mixed\\.$#',
    'count' => 9,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$famid on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$family on mixed\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$i_id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$id on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method canShow\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method formatList\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getBirthDate\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method husband\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method wife\\(\\) on Fisharebest\\\\Webtrees\\\\Family\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: cast.double
    'message' => '#^Cannot cast mixed to float\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\FamilyRepository\\:\\:ageBetweenSiblingsQuery\\(\\) should return array\\<object\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\FamilyRepository\\:\\:statsChildrenQuery\\(\\) should return array\\<stdClass\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Family, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Family given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: stdClass, Closure\\(stdClass\\)\\: stdClass given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/FamilyRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$change_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/GedcomRepository.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/HitCountRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$days\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$days on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGiven\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemale\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenFemaleTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMale\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenMaleTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknown\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownList\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownListTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownTable\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:commonGivenUnknownTotals\\(\\) should return string but returns array\\<int\\>\\|string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Statistics\\\\Repository\\\\IndividualRepository\\:\\:statsAgeQuery\\(\\) should return array\\<stdClass\\> but returns array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: Fisharebest\\\\Webtrees\\\\Individual, Closure\\(object\\)\\: Fisharebest\\\\Webtrees\\\\Individual given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 of closure expects object, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of function array_walk expects callable\\(int, string\\)\\: mixed, Closure\\(string, string\\)\\: void given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$given of method Fisharebest\\\\Webtrees\\\\Statistics\\\\Google\\\\ChartCommonGiven\\:\\:chartCommonGiven\\(\\) expects array\\<int\\>, array\\<int\\>\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/IndividualRepository.php',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/LatestUserRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/MediaRepository.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$tree on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/PlaceRepository.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Statistics/Repository/PlaceRepository.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\SurnameTradition\\\\PatrilinealSurnameTradition\\:\\:inflect\\(\\) should return string but returns string\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SurnameTradition/PatrilinealSurnameTradition.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/SurnameTradition/PatrilinealSurnameTradition.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tree_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tree_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tree_title\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$resn on mixed\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$tag_type on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$xref on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method acceptRecord\\(\\) on mixed\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: return.type
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\Tree\\:\\:getPreference\\(\\) should return string but returns mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Tree\\:\\:\\$preferences \\(array\\<string\\>\\) does not accept array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\Tree\\:\\:\\$user_preferences \\(array\\<array\\<string\\>\\>\\) does not accept array\\<array\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Tree.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method find\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/TreeUser.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$email\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/User.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$real_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/User.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/User.php',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/User.php',
];
$ignoreErrors[] = [
    // identifier: assign.propertyType
    'message' => '#^Property Fisharebest\\\\Webtrees\\\\User\\:\\:\\$preferences \\(array\\<string, string\\>\\) does not accept array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/User.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$url of function parse_url expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Validator.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$serverRequestFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\ServerRequestFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$uriFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\UriFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$uploadedFileFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\UploadedFileFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#4 \\$streamFactory of class Nyholm\\\\Psr7Server\\\\ServerRequestCreator constructor expects Psr\\\\Http\\\\Message\\\\StreamFactoryInterface, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    // identifier: ternary.alwaysTrue
    'message' => '#^Ternary operator condition is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Webtrees.php',
];
$ignoreErrors[] = [
    // identifier: booleanOr.leftAlwaysTrue
    'message' => '#^Left side of \\|\\| is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.notFound
    'message' => '#^Offset int does not exist on array\\<string, int\\>\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, mixed given\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    // identifier: booleanOr.rightAlwaysTrue
    'message' => '#^Right side of \\|\\| is always true\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/control-panel.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$child_count\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$id\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$key\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$latitude\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$longitude\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$no_coord\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$place\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type object supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset 0 on object\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset mixed on object\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$tree of method Fisharebest\\\\Webtrees\\\\Module\\\\PlaceHierarchyListModule\\:\\:listUrl\\(\\) expects Fisharebest\\\\Webtrees\\\\Tree, Fisharebest\\\\Webtrees\\\\Tree\\|null given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, object given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/locations.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$message\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/trees-check.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tag\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/resources/views/admin/trees-check.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/trees-merge.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method title\\(\\) on Fisharebest\\\\Webtrees\\\\Tree\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/admin/trees-merge.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$default_resn_id\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/admin/trees-privacy.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/trees-privacy.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$resn\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/trees-privacy.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$tag_label\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/trees-privacy.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/trees-privacy.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_id\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/resources/views/admin/users-table-options.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/admin/users-table-options.phtml',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type array\\<int, string\\>\\|false supplied for foreach, only iterables are supported\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.generics
    'message' => '#^PHPDoc tag @var for variable \\$all_facts contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.generics
    'message' => '#^PHPDoc tag @var for variable \\$menus contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:filter\\(\\) expects \\(callable\\(mixed, int\\|string\\)\\: bool\\)\\|null, Closure\\(Fisharebest\\\\Webtrees\\\\Fact\\)\\: bool given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, array\\<int, string\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/chart-box.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, int\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/badge.phtml',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/select-number.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/select-number.phtml',
];
$ignoreErrors[] = [
    // identifier: cast.string
    'message' => '#^Cannot cast mixed to string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/components/select.phtml',
];
$ignoreErrors[] = [
    // identifier: deadCode.unreachable
    'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/edit-gedcom-fields.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method formatFirstMajorFact\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/edit/reorder-children.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-children.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getBirthDate\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-children.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method formatFirstMajorFact\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/resources/views/edit/reorder-families.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/edit/reorder-families.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getMarriageDate\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/edit/reorder-families.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method displayImage\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-media.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\GedcomRecord\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/edit/reorder-media.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$record of method Fisharebest\\\\Webtrees\\\\Services\\\\GedcomEditService\\:\\:factsToAdd\\(\\) expects Fisharebest\\\\Webtrees\\\\Family\\|Fisharebest\\\\Webtrees\\\\Individual, Fisharebest\\\\Webtrees\\\\GedcomRecord given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-add-new.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-association-structure.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-association-structure.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match_all expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-gedcom-fields.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact-place.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/fact.phtml',
];
$ignoreErrors[] = [
    // identifier: foreach.nonIterable
    'message' => '#^Argument of an invalid type string supplied for foreach, only iterables are supported\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/help/date.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method displayImage\\(\\) on Fisharebest\\\\Webtrees\\\\Media\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/individual-page-images.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$status\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/administration.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$text\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/administration.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/layouts/administration.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$status\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$text\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByInterface\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method genealogyMenu\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method name\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method stylesheets\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method userMenu\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/layouts/default.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method findByComponent\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/lists/individuals-table.phtml',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/lists/locations-table.phtml',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/lists/media-table.phtml',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/resources/views/lists/notes-table.phtml',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/lists/repositories-table.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method value\\(\\) on Fisharebest\\\\Webtrees\\\\Fact\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/lists/sources-table.phtml',
];
$ignoreErrors[] = [
    // identifier: cast.int
    'message' => '#^Cannot cast mixed to int\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/resources/views/lists/sources-table.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:map\\(\\) expects callable\\(mixed, int\\|string\\)\\: int, Closure\\(string\\)\\: int given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/lists/submitters-table.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/census-assistant.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method tree\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/census-assistant.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/resources/views/modules/family_nav/sidebar-family.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$block_id\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/resources/views/modules/faq/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$block_order\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/faq/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$gedcom_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/faq/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$header\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/faq/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$faqbody\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/faq/show.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$header\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/faq/show.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$favorite_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/favorites/favorites.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$favorite_type\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/favorites/favorites.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$note\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/favorites/favorites.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/favorites/favorites.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$title\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/favorites/favorites.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$url\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/favorites/favorites.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$body\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/gedcom_news/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$news_id\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/gedcom_news/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$subject\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/gedcom_news/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$updated\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/gedcom_news/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$background\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/lifespans-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$birth_year\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/lifespans-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$death_year\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/lifespans-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$id\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/lifespans-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$individual\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/resources/views/modules/lifespans-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$row\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/lifespans-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$string of function trim expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/notes/tab.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$value of method Fisharebest\\\\Webtrees\\\\Elements\\\\SubmitterText\\:\\:value\\(\\) expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/notes/tab.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|null given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/notes/tab.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.generics
    'message' => '#^PHPDoc tag @var for variable \\$ancestors contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/pedigree-chart/chart-up.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.generics
    'message' => '#^PHPDoc tag @var for variable \\$links contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/pedigree-chart/chart-up.phtml',
];
$ignoreErrors[] = [
    // identifier: echo.nonString
    'message' => '#^Parameter \\#1 \\(mixed\\) of echo cannot be converted to string\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/pedigree-chart/chart-up.phtml',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:displayImage\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/pedigree-map/events.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, string given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/place-hierarchy/sidebar.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$time\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 12,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-table.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$time\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-table.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/recent_changes/changes-table.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method getCloseRelationshipName\\(\\) on mixed\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/relatives/family.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method date\\(\\) on Fisharebest\\\\Webtrees\\\\Fact\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/sitemap/sitemap-file-xml.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$block_id\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/stories/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$individual\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/stories/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$title\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/stories/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/stories/config.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$individual\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/stories/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$title\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/stories/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$block_id\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/stories/tab.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$story_body\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/stories/tab.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$title\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/stories/tab.phtml',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:husband\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/timeline-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method Fisharebest\\\\Webtrees\\\\GedcomRecord\\:\\:wife\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/timeline-chart/chart.phtml',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'count\' on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset \'record\' on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method fullName\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method url\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$n of static method Fisharebest\\\\Webtrees\\\\I18N\\:\\:number\\(\\) expects float, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/top10_pageviews/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$body\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/user-messages/user-messages.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$created\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/user-messages/user-messages.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$message_id\\.$#',
    'count' => 6,
    'path' => __DIR__ . '/resources/views/modules/user-messages/user-messages.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$sender\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/user-messages/user-messages.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$subject\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/resources/views/modules/user-messages/user-messages.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$body\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/user_blog/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$news_id\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/user_blog/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$subject\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/user_blog/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$updated\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/user_blog/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$fact\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$individual\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$yahrzeit_date\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/list.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$fact\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/table.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$fact_date\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/table.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$individual\\.$#',
    'count' => 7,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/table.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$yahrzeit_date\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/resources/views/modules/yahrzeit/table.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$change_id\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/pending-changes-page.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$change_time\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/pending-changes-page.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$real_name\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/pending-changes-page.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$record\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/resources/views/pending-changes-page.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$user_name\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/pending-changes-page.phtml',
];
$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property object\\:\\:\\$xref\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/resources/views/pending-changes-page.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list-age.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list-grand.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list-spouses.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-list.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-nolist-grand.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$records has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/families/top10-nolist-spouses.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/column.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/combo.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/custom.phtml',
];
$ignoreErrors[] = [
    // identifier: missingType.iterableValue
    'message' => '#^PHPDoc tag @var for variable \\$data has no value type specified in iterable type array\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/resources/views/statistics/other/charts/pie.phtml',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method createServerRequest\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method createStreamFromFile\\(\\) on mixed\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method createUploadedFile\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    // identifier: missingType.generics
    'message' => '#^Method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:withConsecutive\\(\\) return type with generic class PHPUnit\\\\Framework\\\\Constraint\\\\Callback does not specify its types\\: CallbackInput$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$object of method Fisharebest\\\\Webtrees\\\\Contracts\\\\ContainerInterface\\:\\:set\\(\\) expects object, Fisharebest\\\\Webtrees\\\\Tree\\|string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$offset of function substr expects int, int\\<0, max\\>\\|false given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset 0 on array\\<int, string\\>\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Encodings/AnselTest.php',
];
$ignoreErrors[] = [
    // identifier: offsetAccess.nonOffsetAccessible
    'message' => '#^Cannot access offset 1 on array\\<int, string\\>\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Encodings/AnselTest.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method createUri\\(\\) on mixed\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/Middleware/CheckCsrfTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$params of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, int\\|string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/FixLevel0MediaActionTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$query of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, int\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/FixLevel0MediaDataTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$query of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, array\\<string, string\\>\\|string\\> given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/ManageMediaDataTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<TKey of \\(int\\|string\\), TValue\\>\\|iterable\\<TKey of \\(int\\|string\\), TValue\\>\\|null, \'error\' given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/PingTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$items of class Illuminate\\\\Support\\\\Collection constructor expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<TKey of \\(int\\|string\\), TValue\\>\\|iterable\\<TKey of \\(int\\|string\\), TValue\\>\\|null, \'warning\' given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/PingTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$query of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, array\\<int, string\\>\\|string\\> given\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/RedirectTimeLinePhpTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$params of static method Fisharebest\\\\Webtrees\\\\TestCase\\:\\:createRequest\\(\\) expects array\\<string\\>, array\\<string, int\\|string\\> given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Http/RequestHandlers/UserEditActionTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/AhnentafelReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/AhnentafelReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthDeathMarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthDeathMarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/BirthReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/CemeteryReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/CemeteryReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/ChangeReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/ChangeReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DeathReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DeathReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DescendancyReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/DescendancyReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FactSourcesReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FactSourcesReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FamilyGroupReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/FamilyGroupReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualFamiliesReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualFamiliesReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/IndividualReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MarriageReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MissingFactsReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/MissingFactsReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/OccupationReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/OccupationReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/PedigreeReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/PedigreeReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringEndsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/RelatedIndividualsReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$string of static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertStringStartsWith\\(\\) expects string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Module/RelatedIndividualsReportModuleTest.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method tag\\(\\) on Fisharebest\\\\Webtrees\\\\Fact\\|null\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/Services/GedcomEditServiceTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$interface of method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:findByInterface\\(\\) expects class\\-string\\<Fisharebest\\\\Webtrees\\\\Module\\\\ModuleInterface\\|Psr\\\\Http\\\\Server\\\\MiddlewareInterface\\>, string given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/ModuleServiceTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.templateType
    'message' => '#^Unable to resolve the template type T in call to method Fisharebest\\\\Webtrees\\\\Services\\\\ModuleService\\:\\:findByInterface\\(\\)$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/ModuleServiceTest.php',
];
$ignoreErrors[] = [
    // identifier: method.notFound
    'message' => '#^Call to an undefined method object\\:\\:iniGet\\(\\)\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/TimeoutServiceTest.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method method\\(\\) on object\\|null\\.$#',
    'count' => 3,
    'path' => __DIR__ . '/tests/app/Services/TimeoutServiceTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$option of function ini_get expects string, mixed given\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/app/Services/TimeoutServiceTest.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method id\\(\\) on Fisharebest\\\\Webtrees\\\\User\\|null\\.$#',
    'count' => 5,
    'path' => __DIR__ . '/tests/app/Services/UserServiceTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#1 \\$timestamp of class Fisharebest\\\\Webtrees\\\\Timestamp constructor expects int, int\\|false given\\.$#',
    'count' => 19,
    'path' => __DIR__ . '/tests/app/TimestampTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|false given\\.$#',
    'count' => 2,
    'path' => __DIR__ . '/tests/app/TreeTest.php',
];
$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\|string, string\\|null given\\.$#',
    'count' => 4,
    'path' => __DIR__ . '/tests/app/TreeTest.php',
];
$ignoreErrors[] = [
    // identifier: method.nonObject
    'message' => '#^Cannot call method deleteRecord\\(\\) on Fisharebest\\\\Webtrees\\\\Individual\\|null\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/tests/feature/IndividualListTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
