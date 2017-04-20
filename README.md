# PHP Spam Poison (phpwpoison).
2004-2005 by Mario A. Valdez-Ramirez
You can contact Mario A. Valdez-Ramirez by email at
mario@mariovaldez.org or by paper mail at 
Olmos 809, San Nicolas, NL. 66495, Mexico.

Updated 2017 by Robert Ian Hawdon
https://robertianhawdon.me.uk/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or (at
your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
USA

# About the PHP Spam Poison.

The PHP Spam Poison is a fake-page generator that simulates long
lists of fake email addresses and links to more generated pages, to
be harvested by spam-robots, effectively poisoning their databases
with useless email addresses. This spam poisoner was inspired by the
WPoison software from Monkeys.com.

The main page of the PHP Spam Poison is:
http://www.mariovaldez.net/software/phpwpoison/



# Features of the PHP Spam Poison.

* It uses PHP, so no CGI access is needed.
* Fast and lightweight.
* Can be included by others PHP pages.
* Require software available in most Linux/Unix hosting services.
* Doesn't require a SQL database. 
* Works also in Windows servers (with IIS or Apache). 
* GPL license (open-source). 
* Simple to install. 



# Requirements of the PHP Spam Poison.

Requirements.
* Required: PHP 4.1.x or higher. Your web server should be able to
interpret the PHP language. It really doesn't matter the platform
(tested with GNU/Linux and Windows 2000). 
* Required: A web server. It should work with any web server running
in your workstation or server (tested with Apache in GNU/Linux, with
Apache in Windows 2000 and IIS in Windows 2000). 



# Download the PHP Spam Poison.

The current version are available as a tar.gz package or as a zip
file at
http://www.mariovaldez.net/software/phpwpoison/

Also you can find ther the Readme (readme.txt), Changelog
(version.txt), checksums (checksums.txt) and license (license.txt)
files.
 


# Installation of the PHP Spam Poison.


Installation.
1) Get the files.
Get the files from http://www.mariovaldez.net/software/phpwpoison/
(There are zip and tar.gz files available). Be sure to download also
the wordlist.

2) Unpack.
Extract the script files in a web server directory. That will create
a "phpwpoison" directory with few filesinside. Then unpack the
wordlist and save it in the same directory.

3) Change ownership.
Change the ownership of those files and the directory "phpwpoison" to
the user used by your web server (usually "nobody" in Unix/Linux). To
change the ownership in Linux/Unix, you execute in a shell terminal
in the server the command chown:

chown -h -R nobody:nobody phpwpoison/

In Windows environments, using the Windows Explorer, check the
Security tab of the Properties dialog of the directory, and set the
permissions so that the user IUSR_servername has permissions to read
and write on the "phpwpoison" directory. 

If you cannot set the ownership, at least be sure to enable writting
permissions in the directory.

4) Rename the directory.
Rename the phpwpoison directory to a simple name. Avoid "poison",
"spam", etc. The idea is to not give a clue to those email-harvester
robots that this is a trap.

5) Rename the script.
Rename the emailusers.php file to any simple name. Avoid "poison",
"spam", etc. The idea is to not give a clue to those email-harvester
robots that this is a trap.

6) Configure.
Edit the renamed PHP file, changing at least the pwp_scriptname
variable. If you renamed the script to "listusers.php" then set the
pwp_scriptname variable to "listusers.php". Also, check the
pwp_html_postheader and pwp_html_footer variables, where you can
insert HTML so the generated pages match your website look.

7) Test.
Try to open the renamed PHP file from your the browser thru the web
server. (Please note that by default, the script will make a pause of
up to 30 seconds before finishing rendering the page; to modify or
eliminate that delay, edit the script and change the options
pwp_minsleeptime and pwp_maxsleeptime).

8) You are done.


The following step is optional:

9) Create a spammer list (option available since version 1.1.0).
Maybe you already have a list of email addresses of known spammers. A
list with real addresses (not fake addresses like those used by most
spammers). Some spammers are just uninformed people thinking that
spamming is a good business practice. Some of them will stop spamming
when learn that spamming is not good for their business. But for
those who don't...

Let the phpwpoison script create fake email addresses mixed with
spammers addresses. Let other spammers know what spamming is all
about for the receiver.

Create a text file with each line containing an email address. Avoid
using the default spammers.txt filename. Edit the phpwpoison script
and change the variables pwp_use_spammer_list, pwp_spammer_file and
pwp_spammer_ratio.



# Questions, comments, suggestions.

Don't hesitate to contact me by email (mario@mariovaldez.org).

