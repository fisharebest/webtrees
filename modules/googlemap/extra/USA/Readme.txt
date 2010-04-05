In the ZIP-file in this directory you find files containing the geographic
location of all places in the US. These files are generated using the files
from the U.S. Geological Survey. The files can be downloaded from:
http://geonames.usgs.gov/domestic/download_data.htm.

The ZIP-file in this directory contains CSV files for all states in the
US. These files can be imported into the placelocation module of PGV.

As these files are very large the time to import them might exceed your
maximum script time.

It is also possible to recreate the files The following steps are needed
to do this:

1. Download the files from geonames.usgs.gov
2. Execute the following commands (on Unix; replace <XX> with the filename):
   gawk -F\| -v OFS=\; -f generate.awk <XX> |sed '/(subdivision)/d;/(city)/d;/(historical)/d' | sort > <XX>.csv
3. Import the generated CSV file

The generate.awk script converts the data from USGS into the CSV format.
The SED command filters some unwanted lines (subdivisions, cities, and
historical sites). If you want historical sites as well, you can remove
the part containing ";/(historical)/d"
   

Some closing remarks:
- I did some testing with these files, but I cannot guarantee that they all
  work. Also I cannot give any guarantee on the completeness of the data.
- Using the data and using the script is at your own risk. I did run the script
  to generate the files, but this can be dependant on your environment.
- If you generate files, always inspect the files to see if they are correct.

