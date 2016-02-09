# Webprint

## Prerequisites
* Webserver (Tested with Apache HTTPD 2)
* PHP (no safe mode; need to use shell command)
* Unix
* CUPS (needs to call lpr command)
* Desired printers need to be installed on the server

## Usage
Copy all files to a directory on your web server.
Copy `config.sample.inc.php` to `config.inc.php` and adapt it to your needs.
Open `index.php` in your browser to print a file.

## Authors
* [Benedikt Budig](http://www1.informatik.uni-wuerzburg.de/mitarbeiterinnen/budig_benedikt/)
* [Fabian Lipp](http://www1.informatik.uni-wuerzburg.de/mitarbeiterinnen/lipp_fabian/)

## License
    WebPrint - Printing via Web Browser
    Copyright (C) 2015-2016 Benedikt Budig, Fabian Lipp
    2015 Lehrstuhl für Informatik I, Uni Würzburg

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

