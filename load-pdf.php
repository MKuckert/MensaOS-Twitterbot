<?php
// Ugg, this script is really dirty...

ignore_user_abort(true);
require_once 'pdf2text.php';

define('URI_BASE', 'http://www.studentenwerk-osnabrueck.de/fileadmin/user_upload/Speiseplaene/{DATE}.pdf');
// Determine date of monday of the current week
$stamp=time();
$dayOfWeek=date('w', $stamp);
$mondayOfWeek=strtotime('-'.($dayOfWeek-1).' days', $stamp);
$date=date('y_m_d', $mondayOfWeek);
$pdfFile=str_replace('{DATE}', $date, URI_BASE);

file_put_contents($date.'.pdf', file_get_contents($pdfFile));

$text=pdf2text($date.'.pdf');

$text=mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
file_put_contents('text-utf8.txt', $text);
$text=explode("\n", $text);
$text=array_map('rtrim', $text);

$a="Au\xC3\x9Ferdem:";
$weekdays=array('Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', $a);
$weekday='NON';
$currentDataCollection=array();
$currentData=array();
$data=array();

function isSeparator($string) {
	static $separator=array('Teller', 'Terrine', 'Tages');
	if(in_array($string, $separator)) {
		return true;
	}
	else if(preg_match('/^\\d+,\\d+$/', $string)) {
		return true;
	}
	return false;
}

function sanitizeWhitespace($string) {
	return preg_replace('/\\s+/', ' ', $string);
}

// Search begin of the week
foreach($text as $key=>$item) {
	if(in_array($item, $weekdays)) {
		unset($weekdays[array_search($item, $weekdays)]);
		$data[$weekday]=$currentDataCollection;
		$currentDataCollection=$currentData=array();
		$weekday=$item;
	}
	else if(isSeparator($item)) {
		if(count($currentData)>0) {
			$currentDataCollection[]=$currentData;
			$currentData=array();
		}
	}
	else {
		$currentData[]=$item;
	}
}

$data[$weekday]=$currentData;
unset($data['NON'], $data[$a], $data['Sa']);
unset($text);

// Concatenate underlying text rows to a string
foreach($data as $weekday=>$dataCollection) {
	foreach($dataCollection as $key=>$dataRow) {
		// First row is always just one value
		if($key===0) {
			$data[$weekday][$key]=implode(' ', array_map('trim', $dataRow));
			continue;
		}
		
		$collection=array();
		foreach($dataRow as $item) {
			$collectionCount=count($collection);
			
			if($collectionCount===0 or $item[0]===' ' and $item[1]!==' ') {
				$collection[]=trim($item);
				$collectionCount++;
			}
			else {
				$item=trim($item);
				$char=mb_substr($item, 0, 1);
				// Is uppercase char
				if(mb_strtoupper($char)===$char) {
					$item=' '.$item;
				}
				$collection[$collectionCount-1].=$item;
			}
		}
		
		// Remove the "vegetarian" annotation
		foreach($collection as $colKey=>$colValue) {
			$colValue=preg_replace('/\\(vegetarisch\\)\\s*/i', '', $colValue);
			if($colValue==='') {
				unset($collection[$colKey]);
			}
			else {
				$collection[$colKey]=$colValue;
			}
		}
		
		$data[$weekday][$key]=array_map('sanitizeWhitespace', $collection);
	}
}

// Select just the main meal data
$mainMeals=array();
foreach($data as $weekday=>$dataCollection) {
	$mainMeal=$dataCollection[1];
	$mainMeal=array_map('trim', $mainMeal);
	$mainMeal=implode(', ', $mainMeal);
	$mainMeals[$weekday]=$mainMeal;
}

// Write the meal data to the export file
file_put_contents('mensa-export.php', '<?php return '.var_export($mainMeals, true).';');
