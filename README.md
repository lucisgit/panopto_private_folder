Panopto-Private-Folder-Creation
==================

Panopto Private Folder Creation System

## What is it?

When recording panopto sessions it is often desired that they are first place in a folder that is not visible to students.  Many academics would like to edit their sessions before finally making them visible to students.  To acomodate this, Lancaster University has begun to create academics their own folders, folder to which only they have access.  These folders are the ideal place to record sessions to ready to make public to their Moodle courses.  As the popularity in the system grows more and more people are requesting these folders, so we created a solution.

This set of php files is designed to authenticate users of Lancaster University through co-sign and then perform LDAP lookups on their username.  These details are then used to provision a new folder on Panopto named "Private Folder: [username]".

This system uses a modified version of the API classes developed for use with the Moodle plugin(block and mod) developed by Spenser Jones and Jay Briers.

## Credits

The original Panopto plugin(block) was written by Panopto for Moodle 1.9 and earlier. It has since been rewritten for Moodle 2 by [Spenser Jones](http://spenserjones.com), and subsequently made open-source for collaboration between the open-source community and Panopto.

This Panopto mod for Moodle was adapted from the rewritten version by Jay Briers(jay@jrbriers.co.uk) in collaboration with Information System Services at Lancaster University. 

## Copyright

Copyright Panopto 2009 - 2013 / With contributions from Spenser Jones (sjones@ambrose.edu) and Jay Briers(jay@jrbriers.co.uk)

## License

The Panopto plugin for Moodle is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The Panopto plugin for Moodle is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with the Panopto plugin for Moodle.  If not, see <http://www.gnu.org/licenses/>.
