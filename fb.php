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

$facebook_log = "dataSources/chandl3r/test_messages.html";
$facebook_location = "jsdata/facebook";

$data_store->parse_facebook($facebook_log, $contact_list);

$data_map = array(); //map aggregated data to a contact object.

$contact_list->sort_contacts();
foreach($contact_list->list as $key => $contact){
    echo $contact->contact_name . " -- " . $contact->get_message_count(). "\n";

    //prepare data for primary chart
    $timestamps = $contact->get_message_timestamps();
    $weekly_sms_data[]  = array($contact, $data_aggregation->aggregate_timestamps($timestamps, "week"));
}

$charts->write_facebook_options($weekly_sms_data, 20, $facebook_location, "facebook");
?>