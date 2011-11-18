<?php
date_default_timezone_set('America/Los_Angeles');

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

$data_store->parse_google_voice($google_voice_logs, $contact_list);
$data_map = array(); //map aggregated data to a contact object.

$contact_list->sort_contacts();
foreach($contact_list->list as $key => $contact){
    echo $contact->contact_name . " -- " . $contact->get_message_count(). "\n";

    //prepare data for primary chart
    $timestamps = $contact->get_message_timestamps();
    $weekly_sms_data[]  = array($contact, $data_aggregation->aggregate_timestamps($timestamps, "week"));

    //prepare data for contact specific charts
    $timestamps = $contact->get_split_message_timestamps();
    $incoming_aggr = $data_aggregation->aggregate_timestamps($timestamps["incoming"], "week");
    $outgoing_aggr = $data_aggregation->aggregate_timestamps($timestamps["outgoing"], "week");
    $split_sms_data[] = array("contact" => $contact,
    						  "outgoing" => $outgoing_aggr,
    						  "incoming" => $incoming_aggr);
}

$charts->write_chart_options($weekly_sms_data, 15, $gvmessage_location, "current");
$charts->write_single_contact_options($split_sms_data, 5, $single_contact_location);

?>