# gitscraper
A tool which scrapes public github repositories for common naming conventions in variables, folders and files

This current upload contains the results from 5,256,950 php files from 39,069 different repositories

Gitscraper examines PHP files to create SecList / Dictionary files which can be used against any envinonment ( not just PHP ) for pentesting & bounty hunters.

### How to Run

php gitscraper.php {GitHub Username} {GitHub Personal KEY}


It will collect the following:

1) Folder & File Names

2) GET & POST variables

3) HTTP Header Variables

4) Laravel GET,POST,PUT & DELETE Routes

Each time one of the above is found it is added into its appropriate file in the /raw directory. These raw files are then sorted and ordered by the most duplicated content at the top and then cleaned as best as possible and put in the /cleaned directory

Current command for cleaning file

sort raw/{filename}.txt | uniq -c -d | sort -n -r | sed '/^[[:alnum:]/-._ ]*$/!d' | cut -c 9- | sed '/^$/d' > cleaned/{filename}.txt

1. ( sort ) Sort the file so all occurences are next to each other
2. ( uniq ) Pick out all the uniqe lines and prepend with the number of times found
3. ( sort ) Sort by the instance of times found to the most common is at the top
4. ( sed ) Remove any lines that don't contain alphanumeric text, hyphens underscores, full stops and spaces.
5. ( cut ) Remove the instances found number
6. ( sed ) Remove any blank lines
7. ( > ) Echo out to cleaned directory
