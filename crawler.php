<?php
require_once 'vendor/autoload.php';

$now = new DateTime();

$accountName = 'prefeiturademaringa';
$reportTag = 'boletim';
$reportFile = 'reports.json';
$lastExecution = 0;
$limit = 130;
$reports = array();

if(file_exists($reportFile)){
	$lastExecution = filemtime($reportFile);
	$limit = 20;
	$reports = json_decode(file_get_contents($reportFile), true);
}

$timeDiff = $now->getTimestamp() - $lastExecution;

if($timeDiff >= 3600){
	$instagram = new \InstagramScraper\Instagram();
	$medias = $instagram->getMedias($accountName, $limit);
	
	if(count($medias)){
	    foreach($medias as $m){ /** @var $m \InstagramScraper\Model\Media */
	    	if(isset($reports[$m->getId()])){
	    		break;
	    	}
	    	
	    	if(strpos($m->getCaption(), $reportTag) === false){
	    		continue;
	    	}
	    	
	    	$reports[$m->getId()] = array(
	    		'date' => $m->getCreatedTime(),
	    		'image' => $m->getImageHighResolutionUrl(),
	    		'caption' => $m->getCaption()
	    	);
	    }
	}
	
	file_put_contents($reportFile, json_encode($reports));
}