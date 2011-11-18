<?php
date_default_timezone_set('America/Los_Angeles');
ini_set('memory_limit', '-1');

require_once("source/dataAggregationClass.inc.php");
require_once("source/dataStoreClass.inc.php");
require_once("source/contactListClass.inc.php");
require_once("source/chartsClass.inc.php");

$data_store = new DataStore();
$data_aggregation = new DataAggregation();
$contact_list = new ContactList();
$charts = new Charts();

$gvmessage_location = "jsdata/gvmessages";
$gvcalls_location = "jsdata/gvcalls";
$single_contact_location= "jsdata/single";
$google_voice_logs = "dataSources/cbabraham/voice/conversations/";

$names_list = array("lorena", "tracy", "emily", "mattea", "marji", 
					"myra", "caitlin", "savanna","lorie", "kassi",
					"kelcie","katelin","allie", "nick", "laurie", "ann");
$data_map = array(); //map aggregated data to a contact object.
$word_count = array();

$data_store->parse_google_voice($google_voice_logs, $contact_list);
$contact_list->sort_contacts();

$i = 0;
foreach($contact_list->list as $key => $contact){
	echo $contact->contact_name . " -- " . $contact->get_message_count(). "\n";
    $word_count[$contact->contact_name] = $data_aggregation->word_count($contact);
    if($i > 15) break;
    $i++;
}

//word count
$gossip = array();
foreach($word_count as $contact_name => $word_list){
	foreach($names_list as $name){
			$mentions[$name] = $word_list[$name];
	}
	asort($mentions);
	$gossip[$contact_name] = $mentions;
}

print_r($gossip);





?>