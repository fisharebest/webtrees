function getStateName(stateAbbr)
{
        if (stateAbbr == "AL") { staat = "Alabama" }
        if (stateAbbr == "AK") { staat = "Alaska" }
        if (stateAbbr == "AS") { staat = "American Samoa" }
        if (stateAbbr == "AR") { staat = "Arkansas" }
        if (stateAbbr == "AZ") { staat = "Arizona" }
        if (stateAbbr == "CA") { staat = "California" }
        if (stateAbbr == "CO") { staat = "Colorado" }
        if (stateAbbr == "CT") { staat = "Connecticut" }
        if (stateAbbr == "DE") { staat = "Delaware" }
        if (stateAbbr == "DC") { staat = "District of Columbia" }
        if (stateAbbr == "FM") { staat = "Federated States of Micronesia" }
        if (stateAbbr == "FL") { staat = "Florida" }
        if (stateAbbr == "GA") { staat = "Georgia" }
        if (stateAbbr == "GU") { staat = "Guam" }
        if (stateAbbr == "HI") { staat = "Hawaii" }
        if (stateAbbr == "ID") { staat = "Idaho" }
        if (stateAbbr == "IL") { staat = "Illinois" }
        if (stateAbbr == "IN") { staat = "Indiana" }
        if (stateAbbr == "IA") { staat = "Iowa" }
        if (stateAbbr == "KS") { staat = "Kansas" }
        if (stateAbbr == "KY") { staat = "Kentucky" }
        if (stateAbbr == "LA") { staat = "Louisiana" }
        if (stateAbbr == "ME") { staat = "Maine" }
        if (stateAbbr == "MH") { staat = "Marshall Islands" }
        if (stateAbbr == "MD") { staat = "Maryland" }
        if (stateAbbr == "MA") { staat = "Massachusetts" }
        if (stateAbbr == "MI") { staat = "Michigan" }
        if (stateAbbr == "MN") { staat = "Minnesota" }
        if (stateAbbr == "MS") { staat = "Mississippi" }
        if (stateAbbr == "MO") { staat = "Missouri" }
        if (stateAbbr == "MT") { staat = "Montana" }
        if (stateAbbr == "NE") { staat = "Nebraska" }
        if (stateAbbr == "NV") { staat = "Nevada" }
        if (stateAbbr == "NJ") { staat = "New Jersey" }
        if (stateAbbr == "NH") { staat = "New Hampshire" }
        if (stateAbbr == "NM") { staat = "New Mexico" }
        if (stateAbbr == "NY") { staat = "New York" }
        if (stateAbbr == "NC") { staat = "North Carolina" }
        if (stateAbbr == "ND") { staat = "North Dakota" }
        if (stateAbbr == "MP") { staat = "Northern Mariana Islands" }
        if (stateAbbr == "OH") { staat = "Ohio" }
        if (stateAbbr == "OK") { staat = "Oklahoma" }
        if (stateAbbr == "OR") { staat = "Oregon" }
        if (stateAbbr == "PA") { staat = "Pennsylvania" }
        if (stateAbbr == "PR") { staat = "Puerto Rico" }
        if (stateAbbr == "PW") { staat = "Republic of Palau" }
        if (stateAbbr == "RI") { staat = "Rhode Island" }
        if (stateAbbr == "SC") { staat = "South Carolina" }
        if (stateAbbr == "SD") { staat = "South Dakota" }
        if (stateAbbr == "TN") { staat = "Tennessee" }
        if (stateAbbr == "TX") { staat = "Texas" }
        if (stateAbbr == "UM") { staat = "U.S. Minor Outlying Islands" }
        if (stateAbbr == "UT") { staat = "Utah" }
        if (stateAbbr == "VT") { staat = "Vermont" }
        if (stateAbbr == "VI") { staat = "Virgin Islands" }
        if (stateAbbr == "VA") { staat = "Virginia" }
        if (stateAbbr == "WA") { staat = "Washington" }
        if (stateAbbr == "WV") { staat = "West Virginia" }
        if (stateAbbr == "WI") { staat = "Wisconsin" }
        if (stateAbbr == "WY") { staat = "Wyoming" }
        return staat
}

/County\|Civil/ { 
        lati_dec = substr($9, 1, 3)
        lati_min = substr($9, 4, 2)
        lati_sec = substr($9, 6, 2)
        lati_dir = substr($9, 8, 1)
        lon_dec = substr($8, 1, 2)
        lon_min = substr($8, 3, 2)
        lon_sec = substr($8, 5, 2)
        lon_dir = substr($8, 7, 1)
        lati = (lati_sec/60)
        lati = (lati + lati_min)/60 + lati_dec
        lon = (lon_sec/60)
        lon = (lon + lon_min)/60 + lon_dec
        lati_str = lati_dir lati
        lon_str = lon_dir lon
        print "2;USA",getStateName($2),$5,"",lati_str,lon_str,"9;"
        }
/Populated Place/ {
        lati_dec = substr($9, 1, 3)
        lati_min = substr($9, 4, 2)
        lati_sec = substr($9, 6, 2)
        lati_dir = substr($9, 8, 1)
        lon_dec = substr($8, 1, 2)
        lon_min = substr($8, 3, 2)
        lon_sec = substr($8, 5, 2)
        lon_dir = substr($8, 7, 1)
        lati = (lati_sec/60)
        lati = (lati + lati_min)/60 + lati_dec
        lon = (lon_sec/60)
        lon = (lon + lon_min)/60 + lon_dec
        lati_str = lati_dir lati
        lon_str = lon_dir lon
        print "3;USA",getStateName($2),$5,$3,lati_str,lon_str,"12;"
        }
