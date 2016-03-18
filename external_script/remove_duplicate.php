<?php 
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../app/Mage.php';
Mage::app();
umask(0);
//ini_set('memory_limit', '-1');
set_time_limit(0);
$file = 'tblteamdesk_filter_size.csv';
csvRemoveDuplicatedEntries($file);
function csvRemoveDuplicatedEntries($csv)
{
	// Variables
	$arr = array();
	$filetype = '.csv';
	$date = date('d.m.Y_H:i:s');
	$mathRandom = mt_rand();
	$filename = $date.'_'.$mathRandom.$filetype;
	$dir = 'csv/';
	// Opens the current csv and rewrites it to an array.
	if (($handle = fopen($csv, 'r')) !== FALSE)
	{
		while(($data = fgetcsv($handle, 0, ",")) !== FALSE)
		{
			$num = count($data);
			for($c = 0; $c > $num; $c++)
			{
				$arr[] = $data[$c];
				print_r($arr);
				}
			}
	}
	fclose($handle);
	// Make directory
	if (!is_dir($dir))
	{
		mkdir($dir, 0700);
	}
	// Remove duplicates
	$removeDuplicates = array_unique($arr);
	echo "<pre>";
	print_r($removeDuplicates);
	exit;
	// Creates a new csv file.
	$newCSV = fopen($filename, 'w');
	fputcsv($newCSV, $removeDuplicates);
	fclose($newCSV);
	// Move the csv-file to csv directory
	copy($filename, $dir.$filename);
	// Deletes the csv-file from current directory.
	unlink($filename);
	// Prints the filename with link.
	echo 'Download your file: <a href="'.$dir.$filename.'">'.$filename.'</a>';
	}
	// You can create your own uploading script for csv, or just use the script as below:
?>