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
require_once('common.inc.php');

if (!checkIpRange()) {
	if (!isset($_POST['password'])) {
		http_response_code(400);
		die('Your IP address does not allow printing without password');
	}
	if ($_POST['password'] != $externalPassword) {
		http_response_code(400);
		die('Wrong password');
	}
}
if (isset($_GET['p'])) {
	$printer = trim($_GET['p']);
	if (!array_key_exists($printer, $printers)) {
		echo "printer: " . var_dump($printer);
		echo "<br><br>";
		echo "printers: " . var_dump($printers);
		http_response_code(400);
		die('Invalid printer');
	}
} else {
	$printer = $defaultPrinter;
}
if (isset($_GET['d'])) {
	$duplex = trim($_GET['d']);
	if ($duplex != 1 && $duplex != 2) {
		http_response_code(400);
		die('Invalid duplex mode');
	}
} else {
	$duplex = $defaultDuplex;
}
$upload_file = $_FILES["file"];
$extension = end(explode(".", $upload_file["name"]));

// detect MIME-type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimetype = finfo_file($finfo, $upload_file["tmp_name"]);
finfo_close($finfo);

echo "Upload: " . $upload_file["name"] . "<br>";
echo "Type (submitted): " . $upload_file["type"] . "<br>";
echo "Type (detected): " . $mimetype . "<br>";
echo "Size: " . ($upload_file["size"] / 1024) . " kB<br>";
echo "Temp file: " . $upload_file["tmp_name"] . "<br>";
if ((($mimetype == "application/pdf"))
		&& ($upload_file["size"] < $maxSize)
		&& in_array($extension, $allowedExts))
{
	if ($upload_file["error"] > 0)
	{
			http_response_code(400);
			echo "Return Code: " . $upload_file["error"] . "<br>";
	}
	else
	{
		if (file_exists($tempDir . $upload_file["name"]))
		{
			http_response_code(400);
			echo $upload_file["name"] . " already exists. ";
		}
		else
		{
			move_uploaded_file($upload_file["tmp_name"],
					$tempDir . $upload_file["name"]);
			echo "Stored in: " . $tempDir . $upload_file["name"] . "<br>";
			if ($duplex == 1) {
				$duplexOption = "sides=one-sided";
			} else if ($duplex == 2) {
				$duplexOption = "sides=two-sided-long-edge";
			}			

			echo "Printer: " . $printer . "<br>";
			$command = "lpr -P " . $printer 
				. " -U " . escapeshellarg($_SERVER['REMOTE_ADDR']) 
				. " -o " . escapeshellarg($duplexOption) 
				. " " . escapeshellarg($tempDir . $upload_file["name"]);
			exec($command, $execOutput, $execReturn);
			echo "Executed command: " . $command . "<br>";
			if ($execReturn == 0) {
				echo "<strong>Printing file successful</strong>"; 
			} else {
				http_response_code(400);
				echo "<strong>Printing file <em>not</em> successful</strong>"; 
				echo "Return code of lpr: " . $execReturn . "<br>";
				echo implode("<br>", $execOutput);
			}
			unlink($tempDir . $upload_file["name"]);
		}
	}
}
else
{
	http_response_code(400);
	echo "Invalid file";
}
?>
