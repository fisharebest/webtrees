README file for the Google Maps™ module for webtrees

The files in this archive should be extracted into your modules folder.

These are the instruction on getting your Google Maps™ interface to work:

1. Go to the Modules. You can find this page under the Administration page.
2. Tick Enable Google Maps™.

The map will only be shown if at least one fact has a place with coordinates
attached to it. Attaching a coordinate can be done through the generic
place-location interface (located at the Google Maps™ preferences page) or
by specifying a MAP record with an event.

Adding places one by one to your GEDCOM file (not recommended):

This method is included so that you may have an idea of how a GEDCOM stores
place data. You do not need the Google Maps™ module to use this procedure.
The co-ordinates for an event can only be added directly to a GEDCOM file. 
The correct way to do this is for a PLAC record:
2 PLAC <Placename>
3 MAP
4 LONG <Longitude>
4 LATI <Latitude>
(Make sure you use the "3 MAP" record after a PLAC record.)
The MAP, LONG and LATI lines should be added directly after the PLAC line.
In the ‘edit’ function pop-up window, there is a ‘+’ (plus sign) under ‘Place’
where you can select a country, state, county, and city that exists in your
webtrees ‘place’ file. This can help avoid duplications by various spellings
or versions of the same place. The webtrees configuration allows for ‘expanded’
editing if that option is selected.

It is also possible to define a MAP record within a ADDR record, even though
this is not according to the standard (these records are created by Legacy).

Add a place using webtrees Google Maps™ module (recommended method):

Forenote: the Google Map module is designed to work with locations in tree-like
fashion. If we were to consider a suburb of London, the tree would take the
order 'England, London, Hackney'. This is not how we enter place names in our
family data, but it is how we manage the Google Map module. This will give
access to useful place lookup features as we build up map information. It will
help you find groups of people from the same locale. And it conforms to the
GEDCOM specification, a feature of webtrees. 

And, BEFORE you start with any mapping endeavours, review your data. Make sure
spelling is consistent, there are no 'almost' duplicates, places are in their
right country, and the places you describe are in true tree fashion. Only then
will the module make it easy for you to connect family data with Google Maps™. 

This feature uses new database tables to store place text and location
information. Existing places can be imported and location information can be
added using graphic tools (zoom/click on map) or specific location data. 

The location information is held outside the GEDCOM (and can be shared between
family trees in webtrees) and location data is entered only once for each place.
Backup of location data is available by export of each new place database table
to a text file (separated with ";"). Bulk additions can be performed by text
file import (with reservations for specific place structure and spelling). 

Using the tree structure mentioned above, we start to build location data
from the top down – which, in 99% of cases, will mean starting by entering
a country. When that is done, we move to the next layer (state, county,
whatever is appropriate for your locale.) 

To make the flags work make sure that ./places/flags folder exists.

More information:
http://wiki.webtrees.net/en/Google_Map_module
