# Era Farm Tool

Management tool for small-scale farms/homesteads. To-do lists, scheduling, notifications.

# Design Goals

- Runs on commonly available, low-end systems
- Easy to install
- Extensible

Developer has no interest in running cloud hosting for multiple users.  This is being designed to allow individual users to run their own private instances easily.

# Architecture

**2023 November**

Starting with PHP and file-system persistence. 

PHP because it comes with Linux and doesn't require large frameworks to run. This is expected to be the permanent choice.

File-system persistence because it is sufficient for initial testing. The project is expected to see real use from first user on a local server and be migrated to their personal server later - copy/paste of text files is easier to transfer than a database.


