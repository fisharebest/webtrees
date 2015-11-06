<?php

/******************************************************************************
*
* Filename:     JPEG.php
*
* Description:  Provides functions for reading and writing information to/from
*               JPEG format files
*
* Author:       Evan Hunter
*
* Date:         23/7/2004
*
* Project:      PHP JPEG Metadata Toolkit
*
* Revision:     1.10
*
* Changes:      1.00 -> 1.10 : changed put_jpeg_header_data to check if the data
*                              being written exists
*
* URL:          http://electronics.ozhiker.com
*
* Copyright:    Copyright Evan Hunter 2004
*
* License:      This file is part of the PHP JPEG Metadata Toolkit.
*
*               The PHP JPEG Metadata Toolkit is free software; you can
*               redistribute it and/or modify it under the terms of the
*               GNU General Public License as published by the Free Software
*               Foundation; either version 2 of the License, or (at your
*               option) any later version.
*
*               The PHP JPEG Metadata Toolkit is distributed in the hope
*               that it will be useful, but WITHOUT ANY WARRANTY; without
*               even the implied warranty of MERCHANTABILITY or FITNESS
*               FOR A PARTICULAR PURPOSE.  See the GNU General Public License
*               for more details.
*
*               You should have received a copy of the GNU General Public
*               License along with the PHP JPEG Metadata Toolkit; if not,
*               write to the Free Software Foundation, Inc., 59 Temple
*               Place, Suite 330, Boston, MA  02111-1307  USA
*
*               If you require a different license for commercial or other
*               purposes, please contact the author: evan@ozhiker.com
*
******************************************************************************/




/******************************************************************************
*
* Function:     get_jpeg_header_data
*
* Description:  Reads all the JPEG header segments from an JPEG image file into an
*               array
*
* Parameters:   filename - the filename of the file to JPEG file to read
*
* Returns:      headerdata - Array of JPEG header segments
*               FALSE - if headers could not be read
*
******************************************************************************/

function get_jpeg_header_data( $filename )
{

        // prevent refresh from aborting file operations and hosing file
        ignore_user_abort(true);


        // Attempt to open the jpeg file - the at symbol supresses the error message about
        // not being able to open files. The file_exists would have been used, but it
        // does not work with files fetched over http or ftp.
        $filehnd = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if ( ! $filehnd  )
        {
                // Could't open the file - exit
                echo "<p>Could not open file $filename</p>\n";
                return FALSE;
        }


        // Read the first two characters
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the first two characters are 0xFF 0xDA  (SOI - Start of image)
        if ( $data != "\xFF\xD8" )
        {
                // No SOI (FF D8) at start of file - This probably isn't a JPEG file - close file and return;
                echo "<p>This probably is not a JPEG file</p>\n";
                fclose($filehnd);
                return FALSE;
        }


        // Read the third character
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the third character is 0xFF (Start of first segment header)
        if ( $data{0} != "\xFF" )
        {
                // NO FF found - close file and return - JPEG is probably corrupted
                fclose($filehnd);
                return FALSE;
        }

        // Flag that we havent yet hit the compressed image data
        $hit_compressed_image_data = FALSE;


        // Cycle through the file until, one of: 1) an EOI (End of image) marker is hit,
        //                                       2) we have hit the compressed image data (no more headers are allowed after data)
        //                                       3) or end of file is hit

        while ( ( $data{1} != "\xD9" ) && (! $hit_compressed_image_data) && ( ! feof( $filehnd ) ))
        {
                // Found a segment to look at.
                // Check that the segment marker is not a Restart marker - restart markers don't have size or data after them
                if (  ( ord($data{1}) < 0xD0 ) || ( ord($data{1}) > 0xD7 ) )
                {
                        // Segment isn't a Restart marker
                        // Read the next two bytes (size)
                        $sizestr = network_safe_fread( $filehnd, 2 );

                        // convert the size bytes to an integer
                        $decodedsize = unpack ("nsize", $sizestr);

                        // Save the start position of the data
                        $segdatastart = ftell( $filehnd );

                        // Read the segment data with length indicated by the previously read size
                        $segdata = network_safe_fread( $filehnd, $decodedsize['size'] - 2 );


                        // Store the segment information in the output array
                        $headerdata[] = array(  "SegType" => ord($data{1}),
                                                "SegName" => $GLOBALS[ "JPEG_Segment_Names" ][ ord($data{1}) ],
                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ ord($data{1}) ],
                                                "SegDataStart" => $segdatastart,
                                                "SegData" => $segdata );
                }

                // If this is a SOS (Start Of Scan) segment, then there is no more header data - the compressed image data follows
                if ( $data{1} == "\xDA" )
                {
                        // Flag that we have hit the compressed image data - exit loop as no more headers available.
                        $hit_compressed_image_data = TRUE;
                }
                else
                {
                        // Not an SOS - Read the next two bytes - should be the segment marker for the next segment
                        $data = network_safe_fread( $filehnd, 2 );

                        // Check that the first byte of the two is 0xFF as it should be for a marker
                        if ( $data{0} != "\xFF" )
                        {
                                // NO FF found - close file and return - JPEG is probably corrupted
                                fclose($filehnd);
                                return FALSE;
                        }
                }
        }

        // Close File
        fclose($filehnd);
        // Alow the user to abort from now on
        ignore_user_abort(false);

        // Return the header data retrieved
        return $headerdata;
}


/******************************************************************************
* End of Function:     get_jpeg_header_data
******************************************************************************/




/******************************************************************************
*
* Function:     put_jpeg_header_data
*
* Description:  Writes JPEG header data into a JPEG file. Takes an array in the
*               same format as from get_jpeg_header_data, and combines it with
*               the image data of an existing JPEG file, to create a new JPEG file
*               WARNING: As this function will replace all JPEG headers,
*                        including SOF etc, it is best to read the jpeg headers
*                        from a file, alter them, then put them back on the same
*                        file. If a SOF segment wer to be transfered from one
*                        file to another, the image could become unreadable unless
*                        the images were idenical size and configuration
*
*
* Parameters:   old_filename - the JPEG file from which the image data will be retrieved
*               new_filename - the name of the new JPEG to create (can be same as old_filename)
*               jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data
*
* Returns:      TRUE - on Success
*               FALSE - on Failure
*
******************************************************************************/

function put_jpeg_header_data( $old_filename, $new_filename, $jpeg_header_data )
{

        // Change: added check to ensure data exists, as of revision 1.10
        // Check if the data to be written exists
        if ( $jpeg_header_data == FALSE )
        {
                // Data to be written not valid - abort
                return FALSE;
        }

        // extract the compressed image data from the old file
        $compressed_image_data = get_jpeg_image_data( $old_filename );

        // Check if the extraction worked
        if ( ( $compressed_image_data === FALSE ) || ( $compressed_image_data === NULL ) )
        {
                // Couldn't get image data from old file
                return FALSE;
        }


        // Cycle through new headers
        foreach ($jpeg_header_data as $segno => $segment)
        {
                // Check that this header is smaller than the maximum size
                if ( strlen($segment['SegData']) > 0xfffd )
                {
                        // Could't open the file - exit
                        echo "<p>A Header is too large to fit in JPEG segment</p>\n";
                        return FALSE;
                }
        }

        ignore_user_abort(true);    ## prevent refresh from aborting file operations and hosing file


        // Attempt to open the new jpeg file
        $newfilehnd = @fopen($new_filename, 'wb');
        // Check if the file opened successfully
        if ( ! $newfilehnd  )
        {
                // Could't open the file - exit
                echo "<p>Could not open file $new_filename</p>\n";
                return FALSE;
        }

        // Write SOI
        fwrite( $newfilehnd, "\xFF\xD8" );

        // Cycle through new headers, writing them to the new file
        foreach ($jpeg_header_data as $segno => $segment)
        {

                // Write segment marker
                fwrite( $newfilehnd, sprintf( "\xFF%c", $segment['SegType'] ) );

                // Write segment size
                fwrite( $newfilehnd, pack( "n", strlen($segment['SegData']) + 2 ) );

                // Write segment data
                fwrite( $newfilehnd, $segment['SegData'] );
        }

        // Write the compressed image data
        fwrite( $newfilehnd, $compressed_image_data );

        // Write EOI
        fwrite( $newfilehnd, "\xFF\xD9" );

        // Close File
        fclose($newfilehnd);

        // Alow the user to abort from now on
        ignore_user_abort(false);


        return TRUE;

}

/******************************************************************************
* End of Function:     put_jpeg_header_data
******************************************************************************/



/******************************************************************************
*
* Function:     get_jpeg_Comment
*
* Description:  Retreives the contents of the JPEG Comment (COM = 0xFFFE) segment if one
*               exists
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      string - Contents of the Comment segement
*               FALSE - if the comment segment couldnt be found
*
******************************************************************************/

function get_jpeg_Comment( $jpeg_header_data )
{
        //Cycle through the header segments until COM is found or we run out of segments
        $i = 0;
        while ( ( $i < count( $jpeg_header_data) )  && ( $jpeg_header_data[$i]['SegName'] != "COM" ) )
        {
                $i++;
        }

        // Check if a COM segment has been found
        if (  $i < count( $jpeg_header_data) )
        {
                // A COM segment was found, return it's contents
                return $jpeg_header_data[$i]['SegData'];
        }
        else
        {
                // No COM segment found
                return FALSE;
        }
}

/******************************************************************************
* End of Function:     get_jpeg_Comment
******************************************************************************/


/******************************************************************************
*
* Function:     put_jpeg_Comment
*
* Description:  Creates a new JPEG Comment segment from a string, and inserts
*               this segment into the supplied JPEG header array
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data, into which the
*                                  new Comment segment will be put
*               $new_Comment - a string containing the new Comment
*
* Returns:      jpeg_header_data - the JPEG header data array with the new
*                                  JPEG Comment segment added
*
******************************************************************************/

function put_jpeg_Comment( $jpeg_header_data, $new_Comment )
{
        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an COM header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "COM" ) == 0 )
                {
                        // Found a preexisting Comment block - Replace it with the new one and return.
                        $jpeg_header_data[$i]['SegData'] = $new_Comment;
                        return $jpeg_header_data;
                }
        }



        // No preexisting Comment block found, find where to put it by searching for the highest app segment
        $i = 0;
        while ( ( $i < count( $jpeg_header_data ) ) && ( $jpeg_header_data[$i]["SegType"] >= 0xE0 ) )
        {
                $i++;
        }


        // insert a Comment segment new at the position found of the header data.
        array_splice($jpeg_header_data, $i , 0, array( array(   "SegType" => 0xFE,
                                                                "SegName" => $GLOBALS[ "JPEG_Segment_Names" ][ 0xFE ],
                                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xFE ],
                                                                "SegData" => $new_Comment ) ) );
        return $jpeg_header_data;
}

/******************************************************************************
* End of Function:     put_jpeg_Comment
******************************************************************************/




/******************************************************************************
*
* Function:     Interpret_Comment_to_HTML
*
* Description:  Generates html showing the contents of any JPEG Comment segment
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      output - the HTML
*
******************************************************************************/

function Interpret_Comment_to_HTML( $jpeg_header_data )
{
        // Create a string to receive the output
        $output = "";

        // read the comment segment
        $comment = get_jpeg_Comment( $jpeg_header_data );

        // Check if the comment segment was valid
        if ( $comment !== FALSE )
        {
                // Comment exists - add it to the output
                $output .= "<h2 class=\"JPEG_Comment_Main_Heading\">JPEG Comment</h2>\n";
                $output .= "<p class=\"JPEG_Comment_Text\">$comment</p>\n";
        }

        // Return the result
        return $output;
}

/******************************************************************************
* End of Function:     Interpret_Comment_to_HTML
******************************************************************************/




/******************************************************************************
*
* Function:     get_jpeg_intrinsic_values
*
* Description:  Retreives information about the intrinsic characteristics of the
*               jpeg image, such as Bits per Component, Height and Width.
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      array - An array containing the intrinsic JPEG values
*               FALSE - if the comment segment couldnt be found
*
******************************************************************************/

function get_jpeg_intrinsic_values( $jpeg_header_data )
{
        // Create a blank array for the output
        $Outputarray = array( );

        //Cycle through the header segments until Start Of Frame (SOF) is found or we run out of segments
        $i = 0;
        while ( ( $i < count( $jpeg_header_data) )  && ( substr( $jpeg_header_data[$i]['SegName'], 0, 3 ) != "SOF" ) )
        {
                $i++;
        }

        // Check if a SOF segment has been found
        if ( substr( $jpeg_header_data[$i]['SegName'], 0, 3 ) == "SOF" )
        {
                // SOF segment was found, extract the information

                $data = $jpeg_header_data[$i]['SegData'];

                // First byte is Bits per component
                $Outputarray['Bits per Component'] = ord( $data{0} );

                // Second and third bytes are Image Height
                $Outputarray['Image Height'] = ord( $data{ 1 } ) * 256 + ord( $data{ 2 } );

                // Forth and fifth bytes are Image Width
                $Outputarray['Image Width'] = ord( $data{ 3 } ) * 256 + ord( $data{ 4 } );

                // Sixth byte is number of components
                $numcomponents = ord( $data{ 5 } );

                // Following this is a table containing information about the components
                for( $i = 0; $i < $numcomponents; $i++ )
                {
                        $Outputarray['Components'][] = array (  'Component Identifier' => ord( $data{ 6 + $i * 3 } ),
                                                                'Horizontal Sampling Factor' => ( ord( $data{ 7 + $i * 3 } ) & 0xF0 ) / 16,
                                                                'Vertical Sampling Factor' => ( ord( $data{ 7 + $i * 3 } ) & 0x0F ),
                                                                'Quantization table destination selector' => ord( $data{ 8 + $i * 3 } ) );
                }
        }
        else
        {
                // Couldn't find Start Of Frame segment, hence can't retrieve info
                return FALSE;
        }

        return $Outputarray;
}


/******************************************************************************
* End of Function:     get_jpeg_intrinsic_values
******************************************************************************/





/******************************************************************************
*
* Function:     Interpret_intrinsic_values_to_HTML
*
* Description:  Generates html showing some of the intrinsic JPEG values which
*               were retrieved with the get_jpeg_intrinsic_values function
*
* Parameters:   values - the JPEG intrinsic values, as read from get_jpeg_intrinsic_values
*
* Returns:      OutputStr - A string containing the HTML
*
******************************************************************************/

function Interpret_intrinsic_values_to_HTML( $values )
{
        // Check values are valid
        if ( $values != FALSE )
        {
                // Write Heading
                $OutputStr = "<h2 class=\"JPEG_Intrinsic_Main_Heading\">Intrinsic JPEG Information</h2>\n";

                // Create Table
                $OutputStr .= "<table class=\"JPEG_Intrinsic_Table\" border=1>\n";

                // Put image height and width into table
                $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Image Height</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . $values['Image Height'] . " pixels</td></tr>\n";
                $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Image Width</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . $values['Image Width'] . " pixels</td></tr>\n";

                // Put colour depth into table
                if ( count( $values['Components'] ) == 1 )
                {
                        $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Colour Depth</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . $values['Bits per Component'] . " bit Monochrome</td></tr>\n";
                }
                else
                {
                        $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Colour Depth</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . ($values['Bits per Component'] * count( $values['Components'] ) ) . " bit</td></tr>\n";
                }

                // Close Table
                $OutputStr .= "</table>\n";

                // Return html
                return $OutputStr;
        }
}

/******************************************************************************
* End of Function:     Interpret_intrinsic_values_to_HTML
******************************************************************************/







/******************************************************************************
*
* Function:     get_jpeg_image_data
*
* Description:  Retrieves the compressed image data part of the JPEG file
*
* Parameters:   filename - the filename of the JPEG file to read
*
* Returns:      compressed_data - A string containing the compressed data
*               FALSE - if retrieval failed
*
******************************************************************************/

function get_jpeg_image_data( $filename )
{

        // prevent refresh from aborting file operations and hosing file
        ignore_user_abort(true);

        // Attempt to open the jpeg file
        $filehnd = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if ( ! $filehnd  )
        {
                // Could't open the file - exit
                return FALSE;
        }


        // Read the first two characters
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the first two characters are 0xFF 0xDA  (SOI - Start of image)
        if ( $data != "\xFF\xD8" )
        {
                // No SOI (FF D8) at start of file - close file and return;
                fclose($filehnd);
                return FALSE;
        }



        // Read the third character
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the third character is 0xFF (Start of first segment header)
        if ( $data{0} != "\xFF" )
        {
                // NO FF found - close file and return
                fclose($filehnd);
                return;
        }

        // Flag that we havent yet hit the compressed image data
        $hit_compressed_image_data = FALSE;


        // Cycle through the file until, one of: 1) an EOI (End of image) marker is hit,
        //                                       2) we have hit the compressed image data (no more headers are allowed after data)
        //                                       3) or end of file is hit

        while ( ( $data{1} != "\xD9" ) && (! $hit_compressed_image_data) && ( ! feof( $filehnd ) ))
        {
                // Found a segment to look at.
                // Check that the segment marker is not a Restart marker - restart markers don't have size or data after them
                if (  ( ord($data{1}) < 0xD0 ) || ( ord($data{1}) > 0xD7 ) )
                {
                        // Segment isn't a Restart marker
                        // Read the next two bytes (size)
                        $sizestr = network_safe_fread( $filehnd, 2 );

                        // convert the size bytes to an integer
                        $decodedsize = unpack ("nsize", $sizestr);

                         // Read the segment data with length indicated by the previously read size
                        $segdata = network_safe_fread( $filehnd, $decodedsize['size'] - 2 );
                }

                // If this is a SOS (Start Of Scan) segment, then there is no more header data - the compressed image data follows
                if ( $data{1} == "\xDA" )
                {
                        // Flag that we have hit the compressed image data - exit loop after reading the data
                        $hit_compressed_image_data = TRUE;

                        // read the rest of the file in
                        // Can't use the filesize function to work out
                        // how much to read, as it won't work for files being read by http or ftp
                        // So instead read 1Mb at a time till EOF

                        $compressed_data = "";
                        do
                        {
                                $compressed_data .= network_safe_fread( $filehnd, 1048576 );
                        } while( ! feof( $filehnd ) );

                        // Strip off EOI and anything after
                        $EOI_pos = strpos( $compressed_data, "\xFF\xD9" );
                        $compressed_data = substr( $compressed_data, 0, $EOI_pos );
                }
                else
                {
                        // Not an SOS - Read the next two bytes - should be the segment marker for the next segment
                        $data = network_safe_fread( $filehnd, 2 );

                        // Check that the first byte of the two is 0xFF as it should be for a marker
                        if ( $data{0} != "\xFF" )
                        {
                                // Problem - NO FF foundclose file and return";
                                fclose($filehnd);
                                return;
                        }
                }
        }

        // Close File
        fclose($filehnd);

        // Alow the user to abort from now on
        ignore_user_abort(false);


        // Return the compressed data if it was found
        if ( $hit_compressed_image_data )
        {
                return $compressed_data;
        }
        else
        {
                return FALSE;
        }
}


/******************************************************************************
* End of Function:     get_jpeg_image_data
******************************************************************************/







/******************************************************************************
*
* Function:     Generate_JPEG_APP_Segment_HTML
*
* Description:  Generates html showing information about the Application (APP)
*               segments which are present in the JPEG file
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      output - A string containing the HTML
*
******************************************************************************/

function Generate_JPEG_APP_Segment_HTML( $jpeg_header_data )
{
        if ( $jpeg_header_data == FALSE )
        {
                return "";
        }


        // Write Heading
        $output = "<h2 class=\"JPEG_APP_Segments_Main_Heading\">Application Metadata Segments</h2>\n";

        // Create table
        $output .= "<table class=\"JPEG_APP_Segments_Table\" border=1>\n";


        // Cycle through each segment in the array

        foreach( $jpeg_header_data as $jpeg_header )
        {

                // Check if the segment is a APP segment

                if ( ( $jpeg_header['SegType'] >= 0xE0 ) && ( $jpeg_header['SegType'] <= 0xEF ) )
                {
                        // This is an APP segment

                        // Read APP Segment Name - a Null terminated string at the start of the segment
                        $seg_name = strtok($jpeg_header['SegData'], "\x00");

                        // Some Segment names are either too long or not meaningfull, so
                        // we should clean them up

                        if ( $seg_name == "http://ns.adobe.com/xap/1.0/" )
                        {
                                $seg_name = "XAP/RDF (\"http://ns.adobe.com/xap/1.0/\")";
                        }
                        elseif ( $seg_name == "Photoshop 3.0" )
                        {
                                $seg_name = "Photoshop IRB (\"Photoshop 3.0\")";
                        }
                        elseif ( ( strncmp ( $seg_name, "[picture info]", 14) == 0 ) ||
                                 ( strncmp ( $seg_name, "\x0a\x09\x09\x09\x09[picture info]", 19) == 0 ) )
                        {
                                $seg_name = "[picture info]";
                        }
                        elseif (  strncmp ( $seg_name, "Type=", 5) == 0 )
                        {
                                $seg_name = "Epson Info";
                        }
                        elseif ( ( strncmp ( $seg_name, "HHHHHHHHHHHHHHH", 15) == 0 ) ||
                                 ( strncmp ( $seg_name, "@s33", 5) == 0 ) )
                        {
                                $seg_name = "HP segment full of \"HHHHH\"";
                        }


                        // Clean the segment name so it doesn't cause problems with HTML
                        $seg_name = htmlentities( $seg_name );

                        // Output a Table row containing this APP segment
                        $output .= "<tr class=\"JPEG_APP_Segments_Table_Row\"><td class=\"JPEG_APP_Segments_Caption_Cell\">$seg_name</td><td class=\"JPEG_APP_Segments_Type_Cell\">" . $jpeg_header['SegName'] . "</td><td  class=\"JPEG_APP_Segments_Size_Cell\" align=\"right\">" . strlen( $jpeg_header['SegData']). " bytes</td></tr>\n";
                }
        }

        // Close the table
        $output .= "</table>\n";

        // Return the HTML
        return $output;
}


/******************************************************************************
* End of Function:     Generate_JPEG_APP_Segment_HTML
******************************************************************************/




/******************************************************************************
*
* Function:     network_safe_fread
*
* Description:  Retrieves data from a file. This function is required since
*               the fread function will not always return the requested number
*               of characters when reading from a network stream or pipe
*
* Parameters:   file_handle - the handle of a file to read from
*               length - the number of bytes requested
*
* Returns:      data - the data read from the file. may be less than the number
*                      requested if EOF was hit
*
******************************************************************************/

function network_safe_fread( $file_handle, $length )
{
        // Create blank string to receive data
        $data = "";

        // Keep reading data from the file until either EOF occurs or we have
        // retrieved the requested number of bytes

        while ( ( !feof( $file_handle ) ) && ( strlen($data) < $length ) )
        {
                $data .= fread( $file_handle, $length-strlen($data) );
        }

        // return the data read
        return $data;
}

/******************************************************************************
* End of Function:     network_safe_fread
******************************************************************************/




/******************************************************************************
* Global Variable:      JPEG_Segment_Names
*
* Contents:     The names of the JPEG segment markers, indexed by their marker number
*
******************************************************************************/

$GLOBALS[ "JPEG_Segment_Names" ] = array(

0xC0 =>  "SOF0",  0xC1 =>  "SOF1",  0xC2 =>  "SOF2",  0xC3 =>  "SOF4",
0xC5 =>  "SOF5",  0xC6 =>  "SOF6",  0xC7 =>  "SOF7",  0xC8 =>  "JPG",
0xC9 =>  "SOF9",  0xCA =>  "SOF10", 0xCB =>  "SOF11", 0xCD =>  "SOF13",
0xCE =>  "SOF14", 0xCF =>  "SOF15",
0xC4 =>  "DHT",   0xCC =>  "DAC",

0xD0 =>  "RST0",  0xD1 =>  "RST1",  0xD2 =>  "RST2",  0xD3 =>  "RST3",
0xD4 =>  "RST4",  0xD5 =>  "RST5",  0xD6 =>  "RST6",  0xD7 =>  "RST7",

0xD8 =>  "SOI",   0xD9 =>  "EOI",   0xDA =>  "SOS",   0xDB =>  "DQT",
0xDC =>  "DNL",   0xDD =>  "DRI",   0xDE =>  "DHP",   0xDF =>  "EXP",

0xE0 =>  "APP0",  0xE1 =>  "APP1",  0xE2 =>  "APP2",  0xE3 =>  "APP3",
0xE4 =>  "APP4",  0xE5 =>  "APP5",  0xE6 =>  "APP6",  0xE7 =>  "APP7",
0xE8 =>  "APP8",  0xE9 =>  "APP9",  0xEA =>  "APP10", 0xEB =>  "APP11",
0xEC =>  "APP12", 0xED =>  "APP13", 0xEE =>  "APP14", 0xEF =>  "APP15",


0xF0 =>  "JPG0",  0xF1 =>  "JPG1",  0xF2 =>  "JPG2",  0xF3 =>  "JPG3",
0xF4 =>  "JPG4",  0xF5 =>  "JPG5",  0xF6 =>  "JPG6",  0xF7 =>  "JPG7",
0xF8 =>  "JPG8",  0xF9 =>  "JPG9",  0xFA =>  "JPG10", 0xFB =>  "JPG11",
0xFC =>  "JPG12", 0xFD =>  "JPG13",

0xFE =>  "COM",   0x01 =>  "TEM",   0x02 =>  "RES",

);

/******************************************************************************
* End of Global Variable:     JPEG_Segment_Names
******************************************************************************/


/******************************************************************************
* Global Variable:      JPEG_Segment_Descriptions
*
* Contents:     The descriptions of the JPEG segment markers, indexed by their marker number
*
******************************************************************************/

$GLOBALS[ "JPEG_Segment_Descriptions" ] = array(

/* JIF Marker byte pairs in JPEG Interchange Format sequence */
0xC0 => "Start Of Frame (SOF) Huffman  - Baseline DCT",
0xC1 =>  "Start Of Frame (SOF) Huffman  - Extended sequential DCT",
0xC2 =>  "Start Of Frame Huffman  - Progressive DCT (SOF2)",
0xC3 =>  "Start Of Frame Huffman  - Spatial (sequential) lossless (SOF3)",
0xC5 =>  "Start Of Frame Huffman  - Differential sequential DCT (SOF5)",
0xC6 =>  "Start Of Frame Huffman  - Differential progressive DCT (SOF6)",
0xC7 =>  "Start Of Frame Huffman  - Differential spatial (SOF7)",
0xC8 =>  "Start Of Frame Arithmetic - Reserved for JPEG extensions (JPG)",
0xC9 =>  "Start Of Frame Arithmetic - Extended sequential DCT (SOF9)",
0xCA =>  "Start Of Frame Arithmetic - Progressive DCT (SOF10)",
0xCB =>  "Start Of Frame Arithmetic - Spatial (sequential) lossless (SOF11)",
0xCD =>  "Start Of Frame Arithmetic - Differential sequential DCT (SOF13)",
0xCE =>  "Start Of Frame Arithmetic - Differential progressive DCT (SOF14)",
0xCF =>  "Start Of Frame Arithmetic - Differential spatial (SOF15)",
0xC4 =>  "Define Huffman Table(s) (DHT)",
0xCC =>  "Define Arithmetic coding conditioning(s) (DAC)",

0xD0 =>  "Restart with modulo 8 count 0 (RST0)",
0xD1 =>  "Restart with modulo 8 count 1 (RST1)",
0xD2 =>  "Restart with modulo 8 count 2 (RST2)",
0xD3 =>  "Restart with modulo 8 count 3 (RST3)",
0xD4 =>  "Restart with modulo 8 count 4 (RST4)",
0xD5 =>  "Restart with modulo 8 count 5 (RST5)",
0xD6 =>  "Restart with modulo 8 count 6 (RST6)",
0xD7 =>  "Restart with modulo 8 count 7 (RST7)",

0xD8 =>  "Start of Image (SOI)",
0xD9 =>  "End of Image (EOI)",
0xDA =>  "Start of Scan (SOS)",
0xDB =>  "Define quantization Table(s) (DQT)",
0xDC =>  "Define Number of Lines (DNL)",
0xDD =>  "Define Restart Interval (DRI)",
0xDE =>  "Define Hierarchical progression (DHP)",
0xDF =>  "Expand Reference Component(s) (EXP)",

0xE0 =>  "Application Field 0 (APP0) - usually JFIF or JFXX",
0xE1 =>  "Application Field 1 (APP1) - usually EXIF or XMP/RDF",
0xE2 =>  "Application Field 2 (APP2) - usually Flashpix",
0xE3 =>  "Application Field 3 (APP3)",
0xE4 =>  "Application Field 4 (APP4)",
0xE5 =>  "Application Field 5 (APP5)",
0xE6 =>  "Application Field 6 (APP6)",
0xE7 =>  "Application Field 7 (APP7)",

0xE8 =>  "Application Field 8 (APP8)",
0xE9 =>  "Application Field 9 (APP9)",
0xEA =>  "Application Field 10 (APP10)",
0xEB =>  "Application Field 11 (APP11)",
0xEC =>  "Application Field 12 (APP12) - usually [picture info]",
0xED =>  "Application Field 13 (APP13) - usually photoshop IRB / IPTC",
0xEE =>  "Application Field 14 (APP14)",
0xEF =>  "Application Field 15 (APP15)",


0xF0 =>  "Reserved for JPEG extensions (JPG0)",
0xF1 =>  "Reserved for JPEG extensions (JPG1)",
0xF2 =>  "Reserved for JPEG extensions (JPG2)",
0xF3 =>  "Reserved for JPEG extensions (JPG3)",
0xF4 =>  "Reserved for JPEG extensions (JPG4)",
0xF5 =>  "Reserved for JPEG extensions (JPG5)",
0xF6 =>  "Reserved for JPEG extensions (JPG6)",
0xF7 =>  "Reserved for JPEG extensions (JPG7)",
0xF8 =>  "Reserved for JPEG extensions (JPG8)",
0xF9 =>  "Reserved for JPEG extensions (JPG9)",
0xFA =>  "Reserved for JPEG extensions (JPG10)",
0xFB =>  "Reserved for JPEG extensions (JPG11)",
0xFC =>  "Reserved for JPEG extensions (JPG12)",
0xFD =>  "Reserved for JPEG extensions (JPG13)",

0xFE =>  "Comment (COM)",
0x01 =>  "For temp private use arith code (TEM)",
0x02 =>  "Reserved (RES)",

);

/******************************************************************************
* End of Global Variable:     JPEG_Segment_Descriptions
******************************************************************************/



?>