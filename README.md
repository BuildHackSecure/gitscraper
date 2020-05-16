# gitscraper
A tool which scrapes public github repositories for common naming conventions in variables, folders and files

Gitscraper examines PHP files to create SecList / Dictionary files which can be used against any envinonment ( not just PHP ) for pentesting & bounty hunters.

It will collect the following:

1) Common Folder & File Names

2) Common GET & POST variables

3) Common HTTP Header Variables

4) Common Method Names

5) Laravel GET,POST & PUT Routes

Each time one of the above is found it is added into its appropriate file in the /raw directory. These raw files are then sorted and ordered by the most duplicated content at the top and then cleaned as best as possible and put in the /cleaned directory
