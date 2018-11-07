<?php
/**
 * Script to generate random testdata.
 *
 * row count <-> file size
 * -rw-r--r--  1 jenkins jenkins 1396665687 Nov  7 17:46 s10000.sql
 * -rw-r--r--  1 jenkins jenkins  139666872 Nov  7 17:45 s1000.sql
 * -rw-r--r--  1 jenkins jenkins   13966641 Nov  7 17:45 s100.sql
 * -rw-r--r--  1 jenkins jenkins    1396915 Nov  7 17:44 s10.sql
 * -rw-r--r--  1 jenkins jenkins     139872 Nov  7 17:44 s1.sql
 */

$opt = getopt('s:o:');

if (!array_key_exists('s', $opt) || empty($opt['s'])) {
    die('missing parameter s for size in 1 = 1000 rows');
}
if (!array_key_exists('o', $opt) || empty($opt['o'])) {
    die('missing parameter o for output file');
}

$pattern = "INSERT INTO `test01`.`test01table` (`id`, `a`, `b`, `c`, `d`) VALUES (NULL, %d, %d, %d, '%s');\n";

$fp = fopen($opt['o'], 'w+');

fwrite($fp, '-- generated on '.date('Y-m-d H:i:s')."\n");
fwrite($fp, '-- size: '.$opt['s']."\n");
fwrite($fp, 'USE test01;'."\n");

for ($i = 0; $i <= ((int)$opt['s']*1000); $i++) {
    fwrite($fp, sprintf($pattern, mt_rand(1,9999999), mt_rand(1,9999999), mt_rand(1,9999999), md5(mt_rand(1,9999999))));
}

fclose($fp);