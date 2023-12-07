# How To (Admins)

## "Install" EFT on Server

- Install PHP 7 or later.
- Copy all the `php` files to somewhere under a `public_html` folder.
  - Include all subfolders; subfolder `tests` is optional.
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
- there may be comment lines before the header row (starting with "#" character)
  - specifies version number for file format
- after the comments, the next line is the headers
- files are pipe (|) delimited
- date format is YYYYMMDD
- booleans are `0` or `1`

Data files with Ids will increment to the integer that is not currently in use.

Versions of each data file:
- **users.txt** version 1.0
	- Id|CreatedDate|IsAdmin|Username|PasswordHashed|Email|PhoneNumber|LastLoginDate|IsDeactivated

## Run unit tests

PHPUnit enables the php unit tests.
- To install: `sudo apt install phpunit`
- Or [this guide](https://linux.how2shout.com/3-ways-to-install-phpunit-in-ubuntu-22-04-or-20-04-lts/)

From `[home]/tests` run `phpunit [filename].php`. It relies on "include" relative paths, so the tests must be run from within the `tests` folder.

(not currently using Mockery, but if it is used later)
- [Github](https://github.com/mockery/mockery)
- To install: `sudo apt-get install -y php-mockery`

