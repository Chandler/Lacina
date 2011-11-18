<?php
date_default_timezone_set('America/Los_Angeles');

class DataAggregation {

	public function word_count($contact){
		$dictionary = array();
		foreach($contact->all_messages as $current_message){
			$pieces = split("[ .]", $current_message->message_contents);
			foreach($pieces as $word){
				if (array_key_exists($word, $dictionary)) {
					$dictionary[$word]++;
				}
				else{
					$dictionary[$word] = 1;
				}
			}
		}
		asort($dictionary);
		return $dictionary;
	}

	public function scatter_plot($timestamps){
		foreach($timestamps as $stamp){
			$data_aggregation[] = array("x" => $stamp, "y" => date("G",$stamp));
		}		
		return $data_aggregation;
	}
	public function response_time($contact){
		$previous_message = NULL;
		$sum = 0;
		$average = 0;
		$i = 0;

		foreach($contact->all_messages as $current_message){
			if(($previous_message != NULL) && ($previous_message->from == "me") && ($current_message->from != "me")){

				$previous_message = $current_message;
				$time = $current_message->data_stamp - $previous_message->date_stamp;
				if($time < 10800000){
					$sum = $sum + $time;
					echo $sum;
					$i = $i + 1;
				}else{
					$previous_message = NULL;
				}
			}
		}
		if($i){
			$average = $sum/$i;				
		}else{
			$average = 0;
		}
		return $average;
	}
	public function aggregate_timestamps($timestamps, $frequency){
		sort($timestamps);
		$data_aggregation = array();
			
		$first_timestamp = $timestamps[0];	
		$milliseconds = array("day"=> 86400,"week"=> 604800000, 
								"month"=> 2628000000,"year"=> 31536000000);	
		$aggregate_frequency = $milliseconds[$frequency]; 
		
		//round date_stamp to multiple of aggregate_frequency
		$start = round(($first_timestamp/$aggregate_frequency))*$aggregate_frequency; 
		$next = $start  + $aggregate_frequency;
		$item_count = 0;	

		foreach($timestamps as $stamp){    		
			if($stamp > $next){
				$data_aggregation[] = array($next, $item_count);       		    			
				$item_count = 0;
				$next = $next + $aggregate_frequency;	
			}
			$item_count = $item_count + 1;
		}
		$data_aggregation[] = array($next, $item_count);       		    			
		return $data_aggregation;
	 }
}
	
?>