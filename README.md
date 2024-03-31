# Era Farm Tool

Management tool for small-scale farms/homesteads. To-do lists, scheduling, notifications.

## Design Goals

- Runs on commonly available, low-end systems
- Easy to install
- Extensible

Developer has no interest in running cloud hosting for multiple users.  This is being designed to allow individual users to run their own private instances easily.

System is expected to be used by very few users (<10) concurrently at any time. System is expected to manage only a small amount of data over an instance's life time (as compared to Big Data).

## Architecture

**2023 November**

Starting with PHP and file-system persistence. 

PHP because it comes with Linux and doesn't require large frameworks to run. This is expected to be the permanent choice.

File-system persistence because it is sufficient for initial testing. The project is expected to see real use from first user on a local server and be migrated to their personal server later - copy/paste of text files is easier to transfer than a database.  This is also just something I am interested in trying, to learn the pros and cons of it directly.

Data files will be locked during editing. With the low number of users this app is built for, there shouldn't be timeout issues.

Currently developing against `PHP 7.4.3`.

# For Administrators

## Install EFT on Server

- Install PHP 7 or later.
- Copy all the `php` files to somewhere under a `public_html` folder.
  - Include all subfolders (folder `tests` is optional).
  - Update GROUP of files under `data` folder to allow apache/php to edit them: `sudo chgrp www-data data/*.txt`. 
- Create the first admin user from the command line. Everything else can be setup through the GUI/webpage.
  
## Command Line Tool

From the project's `[home]` folder:

Add an admin user:    
`php command_line.php -a --admin -u username -p password [-e email] [-ph phonenumber]`  
`php command_line.php --add --admin -u username -p password [-e email] [-ph phonenumber]`  

Add a regular user:  
`php command_line.php -a -u username -p password [-e email] [-ph phonenumber]`  
`php command_line.php --add -u username -p password [-e email] [-ph phonenumber]`  
  
## Data files

EFT stores all data to the file system rather than to a database. All files are in the `[home]/data` folder.

Format of files in `data` folder:
- there may be comment lines before the header row (starting with `#` character)
  - specifies version number for file format
- after the comments, the next line is the headers
- files are pipe (|) delimited
  - using pipes instead of commas due to how often commas are used in regular inputs
  - pipes are stripped from all input fields
  - there is no way to "escape" the pipes
- date format is `YYYYMMDD`
- booleans are `0` or `1`
- text fields are stored with end-line characters encoded as `%0A` (the html encoding)

Data files with Ids will increment to the next integer that is not currently in use.

Versions of each data file:
- **users.txt** version 1.0
	- Id|CreatedDate|IsAdmin|Username|PasswordHashed|Email|PhoneNumber|LastLoginDate|SessionKey|IsDeactivated
- **tasks.txt** version 1.0
	- Id|CreatedByUserId|CreatedDate|DueDate|Text|IsClosed|ClosedByUserId|ClosedDate|ClosingText
- **plants.txt** version 1.0
	- Id|Name|Categories|Notes
	- "Categories" is a comma delimited list of terms

## Run unit tests

PHPUnit enables the php unit tests.
- To install: `sudo apt install phpunit`
- Or [this guide](https://linux.how2shout.com/3-ways-to-install-phpunit-in-ubuntu-22-04-or-20-04-lts/)

From `[home]/tests` run `phpunit [filename].php`. It relies on "include" relative paths, so the tests must be run from within the `tests` folder.

(not currently using Mockery, but if it is used later)
- [Github](https://github.com/mockery/mockery)
- To install: `sudo apt-get install -y php-mockery`

