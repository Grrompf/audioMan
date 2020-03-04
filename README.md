audioMan 
========

Merges multiple audio files. Suited for audio books and radio play.
Time issue of merged files are corrected. File size of is checked, too.
Other audio formats are converted to mp3.
If album art (cover) is found, it is tagged, next to title, album and genre.
Files are renamed in format <# - title.mp3> and finally normalized.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg?style=plastic)](https://php.net/)
![GitHub](https://img.shields.io/github/license/grrompf/audioMan?style=plastic)
![GitHub last commit](https://img.shields.io/github/last-commit/grrompf/audioMan?style=plastic)

REQUIREMENTS
============
* php 7.2 or more
* ffmpeg
* mid3v2 (mutagen)

The tool runs on Linux (Ubuntu 18.04). It may run on Windows or Mac OS X but is not tested on theses platforms.


INSTALLATION
============

Ubuntu 18.04
------------

* sudo apt-get update && apt-get upgrade
* sudo apt-get install php
* sudo apt-get install ffmpeg
* sudo apt-get install python-mutagen


USAGE
=====
By default audioMan always scans recursive sub directories of the current directory (root).
It will not remove or alter existing files. Processed files are copied to an output directory (default: ~/audioMan).
Temporary files are removed instantly.

* audioMan 

Options
-------
* __--help__ | -h
* __--version__ | -V
* __--quiet__ | -q
* __--verbose__  | -v / -vv / -vvv
* __--level__ | __-l__ 
* __--no-interaction__| __-y__
* __--no-normalize__ | __-N__ 
* __--format__ | __-f__ <#Your File Name Format#>  
* __--out__ | __-o__ <#PATH TO YOUR OUTPUT DIR#>  

Description
====

Usually audio book files are ordered in sub dirs by chapters or CDs. Multiple books of one topic will content multiple
sub dirs containing multiple sub dirs by CDs.   
AudioMan will scan all sub dirs and will assign cover and audio files to albums and its episodes. 
By default, you will be asked for each album to proceed. Other audio formats (wma, ogg, ac3, acc, wav, m4a) are 
converted to mp3, merged, fixed and finally tagged.  
The resulting file size is checked as all other sub procedures.
Empty files are checked and will result to skip the episode. Images are always checked of its mime type (jpeg, png), its
dimension and its size. If there are more than one cover image to choose, the best guess is taken.
Volume files (i.e. CD1, CD2) are renamed and get an appending number. 
THe file name is normalized. But you can stop normalizing using the option --no-normalize. 
Use the option --force-merge to merge all episodes of an album. HINT: Use this option on a single album
***

__IMPORTANT!__
Change to the directory, you want to process. __AudioMan__ will always start processing on the actual directory. If you
want to process a single book or radio play series, change to the parent directory of the episodes.

__Example:__ Directory "The X-Files" contains all episodes in many sub dirs. Change to "The X-Files". 

***
Normalisation is important for some older car mp3 players which cannot handle utf-8, special chars or white spaces.
You can suppress normalization if you add the option --no-normalize. 
***
If you have a collection of radio plays or audio books to process, start audioMan on the directory containing the collection.
AudioMan will automatically find the files to process. You will therefore receive all resulting files on the default out
put directory (~/audioMan).                          
If you use the option -o <DIRECTORY TO MOVE MERGED FILES>, the processed files are moved or copied to the given dir.   

__Example:__
    audioMan -o <DIR> 
    
***
The resulting file name is formatted by default (eg "01 - Your fine book.mp3"). You can customize the separator 
by using the option --format or -f  

Numbers are evaluated by the total amount of files, and therefore will have a leading 0 on books with more than 10 files.

License
=======
[MIT](https://tldrlegal.com/license/mit-license)
        