petergrassbergerat
==================

petergrassbergerat is the Portfolio Website by Peter Grassberger. You can find 
the site on http://petergrassberger.at or http://petergrassberger.com for the 
english version.

Created by:
-----------
- [Peter Grassberger (PeterTheOne)](http://petergrassberger.com)

Install:
--------
1. download and copy dependencies (see below) to root directory.
2. rename sample-config.inc.php to config.inc.php .
3. add MySql-server connection constants in config.inc.php .
4. upload to webserver or use xampp on local machine.
5. set read permissions for smarty/cache and smarty/templates_c
6. create tables by running install/index.php
7. remove install/index.php and other unused files from production server.
8. have fun!

Dependencies:
-------------
- Smarty-3.*.*:
	- Site: http://www.smarty.net/
	- Path: /Smarty-3.1.13/libs/Smarty.class.php
- syntaxhighlighter_3.0.83 (allready in repository):
	- Author: [Alex Gorbatchev](http://alexgorbatchev.com)
	- Site: http://alexgorbatchev.com/SyntaxHighlighter/
	- Source: http://github.com/alexgorbatchev/SyntaxHighlighter
	- Path: /syntaxhighlighter_3.0.83/scripts/
- jQuery 1.7.1 (allready in repository):
	- Site: http://jquery.com/
	- Source: http://github.com/jquery/jquery
	- Path: /jquery-1.7.1/jquery-1.7.1.min.js
- Tabby jQuery plugin version 0.12 (allready in repository):
	- Author: [Ted Devito](http://teddevito.com)
	- Site: http://teddevito.com/demos/textarea.html
	- Path: /jquery.textarea/jquery.textarea.js
