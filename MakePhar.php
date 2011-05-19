<?php
/* 
	This file is public domain, however it is nice when people contribute. - Glenn R. Martin 
*/

$argc = count($argv);

if ($argc < 3 || $argc > 5) {
	die ($argv[0]." <pharname> <pathtoiterate> [<indexfile>(index.php assumed) [<webindexfile>(null assumed)]]\n");
}

$pharName = $argv[1];
$pharNameCli = escapeshellarg($pharName);
$path = $argv[2];

if (!file_exists($path)) die("Bad iteration path\n");

$indexFile = 'index.php';
$webIndexFile = null;

if ($argc == 4) {
	$indexFile = $argv[3];
}

if ($argc == 5) {
	$webIndexFile = $argv[4];
}

if (!file_exists($path.DIRECTORY_SEPARATOR.$indexFile)) die("Missing PHAR Index File.\n");

try {
	$phar = new Phar($pharName, 0, $pharName);

	$phar->buildFromIterator(
		new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path)
	),
		$path
	);
	
    
	$phar->setStub("#!/usr/bin/env php\n".$phar->createDefaultStub($indexFile, $webIndexFile));
} catch (Exception $ex) {
	die("EXCEPTION ".$ex->getMessage()."\n");
}

`chmod 755 $pharNameCli`;
?>