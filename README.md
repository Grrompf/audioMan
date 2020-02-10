audioMan 
========

Merges multiple audio files. Suited for audio books and radio play.
Time issue of merged files are corrected. File size of is checked, too.
Wma files are converted. If album art (cover) is found, it is tagged, next to title, album and genre.
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
It will not remove or alter existing files but will move processed files to root dir. Similar named files in root
will be overwritten!      

* audioMan 

Options
-------
* __--help__ | -h
* __--version__ | -V
* __--quite__ | -q
* __--verbose__  | -v / -vv / -vvv
* __--no-normalize__ | __-N__   
* __--volumes__
* __--force__
* __--multiple__ | -m 
* __--format__ | __-f__ <#Your File Name Format#>  

Description
====

Usually audio book files are ordered in sub dirs by chapters or CDs. Multiple books of one topic will content multiple
sub dirs containing multiple sub dirs by CDs.   
AudioMan will scan all sub dirs and will start *convert and join files* on the deepest directory level. After merging
file size is checked and the resulting combined file is corrected (time issue). If *album art (cover)* is detected, it is
tagged on the mp3 file before moving to parent directory. Images are always checked of type (jpg, jpeg, png), dimension 
(rather quadratic) and size (max 100 kB). If multiple images are found, best match is the filename (cover, folder, front).
Merge and moving will stop on root level of the audio book directory. All merged files will be found there... 
Finally files are *renamed (format: # - title.mp3)*, *title, album and genre are written into tags* and the files are *normalized*.
***
__IMPORTANT!__
Change to the directory, you want to process. __AudioMan__ will always start processing on the actual directory. If you
want to process a single book or radio play series, change to the parent directory of the episodes.

__Example:__ Directory "The X-Files" contains all episodes in many sub dirs. Change to "The X-Files". 

If you have multiple audio books to process, change to the parent dir and use the option --multiple: This increases 
the sub dir level search by one. Otherwise, all your books will be merged to one.
Since the original files are always kept, it is not a big problem but the result is probably not what you are looking for. 

__Example:__ Directory "AudioBooks" contain all books in many sub dirs. Change to "AudioBooks".
             audioMan --multiple. 

Since the level depth is checked, some directories are probably skipped. This behavior happens if you have additional sub dirs
just for ordering. You can force to process these using the option --force. But be careful since the result is probably not 
what you are looking for.
It is better to move the sub dirs manually to album level. Empty dirs are ignored anyway.     
***
Normalisation is important for some older car mp3 players which cannot handle utf-8, special chars or white spaces.
You can suppress normalization if you add the option --no-normalize. 
***
Audio books may consist of several chapters or CDs. Usually, audioMan will merge all files to one big file. This file
may exceed convenient file size. Since most of the players cannot remember the file position if you stop, it is hard to 
follow the story on breaks. Therefore, you can force by using the option --volumes to stop further merging after joining
files on chapters or CDs. You will receive multiple files for the book, one for each CD or chapter __as long as the files are
provided separated in directories__ which MUST contain a number.

__Example:__
    audioMan --volumes
    
***
If you have a collection of radio plays or audio books to process, start audioMan on the directory containing the collection.
AudioMan will automatically find the files to process. You will therefore receive all resulting files on the root directory 
of each audio book. So you still will have some work to do for paste and copy.                          
If you use the option -o <DIRECTORY TO MOVE MERGED FILES>, the processed files are moved or copied to the given dir.   
With this option, the original files are not altered, touched or removed.

__Example:__
    audioMan -o <DIR> 
    
***
The resulting file name is formatted by default (eg "01 - Your fine book.mp3"). This is even the title which is written to 
the mp3 tag. If you want to force your custom title format by using the option --format or -f followed by a simplified
regex pattern. Each pattern has to be surrounded by two #, a number is mandatory and identified by n, whitespaces are \s,
a dot \. and a dash is -. The title is taken from the parent directory or above and is symbolized as TITLE.  

__Example 1:__
    #n\s-\sTITLE# (Default: "1 - My Book.mp3")  

__Example 2:__
    #n\.TITLE# ("1.My Book.mp3")  

__Example 3:__
    #TITLE\s\n# ("My Book 1.mp3")
    
Numbers are evaluated by the total amount of files, and therefore will have a leading 0 on books with more than 10 files.
    

FAQ
===

* What happens if I have sub dirs and mp3 files on album dir?
   Only sub dirs are scanned and processed. It will result on additional mp3 files on album dir.
* What happens if I have sub dirs and mp3 files in a sub dir?
   Sub dirs are scanned and processed. After moving the merged file to parent dir, it will merge all the files found on
   this level. THIS IS PROBABLY NOT THE RESULT YOU WANTED.
* What happens if a have an extra sub dir with album arts only?
   Nothing. If you want the album art to be tagged, it has to be on same level as the merged files.
   

License
=======
[MIT](https://tldrlegal.com/license/mit-license)
        