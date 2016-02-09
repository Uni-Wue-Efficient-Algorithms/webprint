<?php

/*
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
*/

$allowedExts = array("pdf");
$maxSize = 2000000;
$tempDir = "/var/tmp/webprint/";
$defaultPrinter = "PDF"; // see list below
$defaultDuplex = "2";

// Clients with IPs in this range are allowed to print without entering a
// password
$minTrustedIp = "192.168.0.1";
$maxTrustedIp = "192.168.0.10";

// Other clients are prompted for a password, which is defined here
$externalPassword = "mySuperSecretPassword";

// Here the list of available printers is defined. The key is the name of the
// printer on the CUPS server. The value is the description that is shown in
// the webinterface. You can include a room number here, e.g.
$printers = array(
	'laser' => "Laser Printer (b/w)",
	'inkjet' => "Inkjet (color)",
	'PDF' => "PDF printer (for testing only)"
);

?>
