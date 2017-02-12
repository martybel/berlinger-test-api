## Berlinger Api Test

This is my implementation of the Berlinger API test assignment. The task
described, requires an upload endpoint where CSV files in a specific format
can be uploaded, and 2 endpoints for retrieving information.

## Solution

My Solution is built using the Laravel Framework and has a very simplistic 
front-end to make debugging a bit easier.

My solution has a CSV upload which will do a simple preliminary check to see
if all required fields are present in the CSV. If not, then it will not accept
the upload.

If the upload is accepted, it will pass it on to a background job which will
do the actual import. I seperated this so to avoid HTTP timeouts or long waits
for the API. This should make the API a bit more robust.

The background job will parse all records and validate them so only ones
with a valid URL get processed. Since the others won't yield a valid image
i reject them from storing.

When making a local copy i check if the copy was succesful. If so, it will
make then available online by default. Otherwise the record will be stored,
but with a remark stating that the original file could not be found.

All pictures stored will have two unique identifiers: their "title" (as per
spec), and an auto generated "uuid". Information of a specific picture
can be obtained by using either one.

## API Endpoints
The API endpoints have CSRF protection in place. This is something that is 
enabled by default when using Laravel. To avoid misuse, this is not a 
"bad thing". Laravel also has throttling enabled by default. 

<b>POST /1.0/csv/upload</b>

CSV/upload handles the upload of CSV files to the site.The file is expected in 
a parameter called 'csv'. furthermore you need to pass along the CSRF token.
 
When uploaded succesfully, it will return the "batch" number used for the
 import background job.

<b>GET /1.0/media/</b>

Will return a list of all objects in the database including all their metadata

<b>GET /1.0/media/{slug}></b>

Will display the image of {slug}

<b>GET /1.0/media/info/{slug}</b>

Will display the meta information of a single item identified by {slug}

## Extra

The front-end is built using Vue. This may also help you loading/testing the data.