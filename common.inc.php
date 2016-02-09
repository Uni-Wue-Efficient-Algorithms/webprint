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

require_once('config.inc.php');

function checkIpRange() {
	global $minTrustedIp, $maxTrustedIp;

	$ip = $_SERVER['REMOTE_ADDR'];
	if ((ip2long($ip) >= ip2long($minTrustedIp))
		 && (ip2long($ip) <= ip2long($maxTrustedIp))) {
		return true;
	} else {
		return false;
	}
}

?>
