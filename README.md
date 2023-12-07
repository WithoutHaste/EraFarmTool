# Era Farm Tool

Management tool for small-scale farms/homesteads. To-do lists, scheduling, notifications.

[How To (Admins)](HOWTO_FOR_ADMINS.md)  

# Design Goals

- Runs on commonly available, low-end systems
- Easy to install
- Extensible

Developer has no interest in running cloud hosting for multiple users.  This is being designed to allow individual users to run their own private instances easily.

System is expected to be used by very few users (<10) concurrently at any time. System is expected to manage only a small amount of data over an instance's life time (as compared to Big Data).

# Architecture

**2023 November**

Starting with PHP and file-system persistence. 

PHP because it comes with Linux and doesn't require large frameworks to run. This is expected to be the permanent choice.

File-system persistence because it is sufficient for initial testing. The project is expected to see real use from first user on a local server and be migrated to their personal server later - copy/paste of text files is easier to transfer than a database.

Data files will be locked during editing. With the low number of users this app is built for, there shouldn't be timeout issues.

Currently developing against `PHP 7.4.3`.
