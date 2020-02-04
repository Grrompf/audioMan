audioMan 
========

Merges multiple audio files. Suited for audio books and radio play.
Time issue of merged files are corrected. File size of is checked, too.
Wma files are converted. If album art (cover) is found, it is tagged, next to title, album and genre.
Files are renamed in format <# - title.mp3> and finally normalized.


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

* audioMan -V  (shows the version)
* audioMan -q  (Working without output messages)
* audioMan     (Working with normal output on error and success)
* audioMan -v  (Working verbose)
* audioMan -vn  (Working very verbose)    


HINT
====

Usually audio book files are ordered in sub dirs by chapter or CD. Multiple books of one topic will content multiple
sub dirs containing multiple sub dirs by CDs.   
AudioMan will scan all sub dirs and will start convert and joining files on the deepest directory level. After merging
file size is checked and the resulting combined file is corrected (time issue). If album art (cover) is detected, it is
tagged on the mp3 file before moving to parent directory. Images are always checked of type (jpg, jpeg, png), dimension 
(rather quadratic) and size (max 100 kB). If multiple images are found, best match is the filename (cover, folder, front).
Merge and moving will stop on root level of the audio book directory. All merged files will be found there... 
Finally files are renamed (format: # - title.mp3), title, album and genre are written into tags and the files are normalized.


FAQ
===

* What happens if I have sub dirs and mp3 files on root dir?
   Only sub dirs are scanned and processed. It will result on additional mp3 files on root dir.
* What happens if I have sub dirs and mp3 files in a sub dir?
   Sub dirs are scanned and processed. After moving the merged file to parent dir, it will merge all the files found on
   this level. THIS IS PROBABLY NOT THE RESULT YOU WANTED.
* What happens if a have an extra sub dir with album arts only?
   Nothing. If you want the album art to be tagged, it has to be on same level as the merged files.
        