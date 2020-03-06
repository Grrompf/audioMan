audioMan 
========

Merges multiple audio files. Suited for audio books and radio play.
Time issue of merged files are corrected. File size is checked, too.
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

**Requirements**

* sudo apt-get update && apt-get upgrade
* sudo apt-get install php
* sudo apt-get install ffmpeg
* sudo apt-get install python-mutagen


**Install audioMan**
* Download audioMan from github  "https://github.com/Grrompf/audioMan.git" OR 

  use phive "phive install grrompf/audioMan" (details on phive https://phar.io/)


USAGE
===
By default audioMan always scans recursive sub directories of the current directory (root).
It will not remove or alter existing files. Processed files are copied to an output directory (default: ~/audioMan).
Temporary files are removed instantly.

**HINT:** Make audioMan accessible everywhere by moving the phar to your user bin: **PATH/audioMan.phar /usr/local/bin/audioMan**
          OR create a symbolic link in /usr/local/bin/audioMan.

audioMan
=== 

Options
-------
* __--help__ | -h
* __--version__ | -V
* __--quiet__ | -q
* __--verbose__  | -v / -vv / -vvv
* __--force-merge__  
* __--level__ | __-l__ 
* __--no-interaction__| __-y__
* __--no-normalize__ | __-N__ 
* __--format__ | __-f__ <#Your File Name Format#> 

Manual
====

Usually audio book files are ordered in sub dirs by chapters or CDs. Multiple books of one topic will content multiple
sub dirs containing multiple sub dirs by CDs.   
AudioMan will scan all sub dirs and will assign cover and audio files to albums and its episodes.
Therefore, you have to change to the directory containing the audio files. If you have many different audio books to process,
move them into a parent dir.

>    
>       -SF books
>          --book 1
>               ---files     
>          --book 2
>               ---files
>

SF books is the parent directory containing many books (albums). You can change also directly to the album directory but
in most cases you have to force merging in this case (use option --force-merge). If the filename is always the same apart from
an increasing number, files will be merged automatically.
    
---

audioMan will analyse the nesting level to identify the album level automatically. You may bypass analysis by providing
a level by yourself (use option -l, eg. audioMan -l2).
  
---

Albums (books) and its episodes (chapters) are detected automatically. If an episode contains multiple sub dirs with similar
names, audioMan identifies these as volumes (eg. CD1, CD1 or Chapter 1, Chapter 2). Volumes are preserved. The parent
dir becomes title and filename with an appending number.
   
**CAUTION:** For volume detection suitable dir names are mandatory. Chapter A cannot identified as an volume. In this case
consider renaming!
        
---
 
Other audio formats (wma, ogg, ac3, acc, wav, m4a, mp4) are detected and converted to mp3. Due to the merge process and 
tagging this is necessary.

---

By default, you will be asked for each album to proceed. So you can control the processed files before going on. We hardly
recommend to take your time to control and read the output. Anyway, if your files are all well formatted and named,
you can ignore interaction by using the option -y (yes to all).

---
    
There are several verbose level. In most cases option -v is good enough. In need for more verbosity, use -vv or -vvv (debug).
General verbose output (success, error, warnings) is provided even without using the verbose option. If you want no
output at all use the option -q (quiet)
   
---

Files are processed album wise. Steps are conversion, merge, time fix, tagging and normalizing. The time fix is 
mandatory for concatenated audio files. Otherwise, the time length shown in most players will not match real time length.
The resulting file size is proven and shown on verbose level -v. 
Empty files are checked and will result to skip the episode. Skipped episodes are another good verbose info for you :-)
 
---

Album art are automatically detected and assigned to episodes or albums. Empty images are excluded, so are images without
correct mime-type. For multiple images, audioMan will select the best matching album art concerning dimension, file size and
filename. If files already contain album art, this is preserved even in a conversion or merge process. Existing album art is
not exchanged!
    
---

The file name is normalized. Therefore, all empty spaces are replaced by underlines. Any kind of umlauts are replaced, too.
So your files become compatible to older players, especially in cars.  You can stop normalizing using the option --no-normalize.
The well-formatted filename is preserved in the tag.

---

Tagging is performed for album art, title, album and type (always Other). Title is taken from the episode, volume or book.
If titles contain a leading number as i.e. episodes, you can customize the separator (using option -f).   
By default, the number is separated by a dash surrounded by spaces.
  
---

If sorting dirs are found below album level, they are preserved. This works for first-level sorting dirs only. 
HINT: Please double-check files always on sorting dirs.

---

All processed files can be found in the audioMan directory of your home dir (~/audioMan). This default directory
is created automatically.
 
---

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
        