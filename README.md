# Gitscraper
IT is a tool which scrapes public github repositories for common naming conventions in variables, folders and files

This current upload contains the results from 16,018,052  php files from 102808 different repositories

Gitscraper examines PHP files to create SecList / Dictionary files which can be used against any environment ( not just PHP ) for pentesting & bounty hunters.

### How to Run

php gitscraper.php {GitHub Username} {GitHub Personal KEY}


It will collect the following:

1) Folder & File Names

2) GET & POST variables

3) HTTP Header Variables

4) Laravel GET,POST,PUT & DELETE Routes

Each time one of the above is found it is added into its appropriate file in the /raw directory. These raw files are then sorted and ordered by the most duplicated content at the top and then cleaned as best as possible and put in the /cleaned directory

Current command for cleaning files ( to be run in /raw directory )

for filename in *; do sort $filename | grep -v ' ' | grep -v -P "\t" | uniq -d -c | sort -n -r | cut -c 9- > ../cleaned/$filename  ; done

1. ( sort ) Sort the file so all occurences are next to each other
2. ( grep ) Remove lines that contain spaces
3. ( grep ) Remove lines that contain tabs
4. ( uniq ) Pick out all the uniqe lines and prepend with the number of times found
5. ( sort ) Sort by the instance of times found to the most common is at the top
6. ( cut ) Remove the instances found number
7. ( > ) Echo out to cleaned directory
