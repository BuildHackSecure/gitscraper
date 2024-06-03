# gitscraper

These are the results of scraping 176,734 GitHub repos and 35,958,908 individual files for commonly occurring strings which can be used for fuzzing.

It was created by grepping php files for particular patterns but can be used against any web stack.

The results have been processed and ordered from the most frequently found at the top to least at the bottom.

### files.txt
This is the filename of the individual php file

### folders.txt
This is the folder name that the php file was found in.

### headers.txt
These are headers that the web application requests.

### methods.txt
These are function names within the php application.

### routes.txt
These are routes which are defined in the 3 most popular php frameworks.

### params.txt
These are parameters that the web application looks for in the request body or query string.