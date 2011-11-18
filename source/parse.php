<?php 
date_default_timezone_set('America/Los_Angeles');
parse_logs();



function msgcmp($a, $b)
{
    if ($a->date_stamp == $b->date_stamp) {
        return 0;
    }
    return ($a->date_stamp < $b->date_stamp) ? -1 : 1;
}
function contactcmp($a, $b)
{
    if ($a->message_count == $b->message_count) {
        return 0;
    }
    return ($a->message_count > $b->message_count) ? -1 : 1;
}

class Contact
{
    public $contact_name;
	public $tel;
	public $message_count;
	public $all_messages; 
	public $all_calls;
	public $data_aggregations;
	
}

class Message
{
	public $sent_by_contact;
 	public $date_stamp;
	public $message;
}


function write_js_data($contact_list, $content_type, $frequency, $items_to_write){
	$i = 0;
	foreach($contact_list as $key => $contact){
		echo $contact->contact_name . ":  ";
		echo $contact->message_count . "\n";
	
		$tok = explode(" ",$contact->contact_name);
		$name = $tok[0]. "_". $tok[1];
		$data_array = $contact->data_aggregations[$content_type][$frequency];

		$script = $script . "\nvar ". $name . " = new Array(";
		if (count($data_array>1)) {
		    foreach ($data_array as $key => $value){
		        if ($key < (count($data_array)-1)){
		            $script = $script ."[". $value[0] . "," . $value[1] . '],';
		        }
		        else {
		            $script = $script ."[". $value[0] . "," . $value[1] . "]);\n";
		        }
		    }
		}
		else {  
		    $script = $script . "1);\nlcValues[0]=" . $lc_values[0] . ";\n";
		}		
	//	echo $script . " \n";	
		$file = "jsdata/".$content_type."/".$name.".js";
		file_put_contents($file, $script);
		
		$i = $i + 1;
		if($i > $items_to_write) break;	
	}
}

function parse_logs(){
	$contact_list = array();
	$log_location = "cbabraham/voice/conversations/";
//	$log_location = "caitlin/";
//	$log_location = "testconvo/";
	
	foreach(glob($log_location."*.html") as $filename) {	
		$xml = simplexml_load_file($filename);	
		$title= $xml->head->title;
		$pieces = explode("\n", $title);
		if(count($pieces) == 1){
			$contact_name = trim($pieces[0]);

		}else $contact_name = trim($pieces[1]);

			if($contact_name == "+12085149798" || $contact_name == "+12084122592"){
		echo "pppppp";
		$contact_name = "Lorena Davis";
		}
		if (!array_key_exists($contact_name, $contact_list)) {
			$new_contact = new Contact();
			$new_contact->contact_name = $contact_name;		
			$contact_list[$new_contact->contact_name] = $new_contact;

		}

		$count = count($xml->body->div[0]->div); //number of messages in file
		for($i = 0; $i < $count; $i++){			
			$new_message = new Message();
			$new_message->message = $xml->body->div[0]->div[$i]->q;
			$date_stamp = $xml->body->div[0]->div[$i]->abbr;
			$new_message->date_stamp = strtotime($date_stamp)*1000; //convert to millisecond unix time

			if(($xml->body->div[0]->div[$i]->abbr) == "me"){
				$new_message->sent_by_contact = false;	
			}
			else $new_message->sent_by_contact = true;
			// echo "-----" . $new_message->message . "------>" . $contact_name."\n";	
			if($new_message->message != ""){
				$contact_list[$contact_name]->all_messages[] = $new_message;
			}
		}
	}
	$frequency = "week";
	aggregate_logs($contact_list, $frequency);
}

function aggregate_logs($contact_list, $frequency){

	foreach($contact_list as $key => $contact){
		echo $contact->contact_name . "\n";
		usort($contact->all_messages, "msgcmp");
		$contact->message_count=count($contact->all_messages);		
		$first_date_stamp = $contact->all_messages[0]->time;	
		$milliseconds = array("day"=> 86400,"week"=> 604800000, 
							  "month"=> 2628000000,"year"=> 31536000000);	
		$aggregate_frequency = $milliseconds["week"]; 
		$start = round(($start/$aggregate_frequency))*$aggregate_frequency; //round date_stamp to multiple
																			// of aggregate_frequency
		$next = $first_date_stamp  + $aggregate_frequency;
		$item_count = 0;	
		
		foreach($contact->all_messages as $messageKey => $message){
		//	echo $time . " vs " . $i  ."\n";
			if($message->date_stamp > $next){
			//	echo "new week\n";
				$contact->data_aggregations["messages"][$frequency] = array($next, $text_count);			
				$text_count = 0;
				$next = $next + $aggregate_frequency;	
			}
			$item_count = $item_count + 1;
		}
	//	echo "hg\n";
	//	print_r($value->week_aggr);
		
	}

	uasort($contact_list, "contactcmp");
	write_js_data($contact_list, "messages", $frequency, 25);
}


?>
