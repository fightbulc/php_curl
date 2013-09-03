<?php
//    Copyright (C) 2011 BauerUK
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.

require 'vendor/autoload.php';

// Example 1. Grab some JSON
$curl = \Wrapper\Curl\Curl::init('http://www.reddit.com/r/php/.json')->setReturnTransfer(true);

var_dump(json_decode($curl->execute()));

// Example 2. Use our existing CURL object, and retrieve some headers
$headers =
    $curl->setHeader(true)
        ->setNobody(true)
        ->setReturnTransfer(true)
        ->execute();

print $headers . PHP_EOL;

// Example 3. Using the `->get...` functions
print $curl->getHTTPCode() . PHP_EOL;
