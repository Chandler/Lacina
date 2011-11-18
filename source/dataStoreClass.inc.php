<?php
date_default_timezone_set('America/Los_Angeles');
require_once("messageClass.inc.php");

class DataStore {


    public function parse_facebook($log_location, $contact_list){
        $xml = simplexml_load_file($log_location);
        print_r($xml);        

        // for($i = 0; $i < $count; $i++){    
        //     $date_stamp = $xml->Message[$i]["Date"] . " " .  $xml->Message[$i]["Time"];
        //     $new_message = new Message();
        //     $new_message->date_stamp = strtotime($date_stamp)*1000;
        //     $new_message->message_contents = $xml->Message[$i]->Text;
        //     if($new_message->message_contents != ""){
        //         $contact_list->new_message($contact_name, $new_message);
        //     }   
        // } 
    }

    public function parse_msn($log_location, $contact_list){
        foreach(glob($log_location."*.xml") as $filename) {
            $contact_name = basename($filename,".xml");
            $xml = simplexml_load_file($filename);
            $count = count($xml->Message); //number of messages in file
            for($i = 0; $i < $count; $i++){    
                $date_stamp = $xml->Message[$i]["Date"] . " " .  $xml->Message[$i]["Time"];
                $new_message = new Message();
                $new_message->date_stamp = strtotime($date_stamp)*1000;
                $new_message->message_contents = $xml->Message[$i]->Text;
                if($new_message->message_contents != ""){
                    $contact_list->new_message($contact_name, $new_message);
                }   
            } 
        }
    }


     public function parse_skype($log_location, $contact_list){        
        ini_set('memory_limit', '-1');
        if (($handle = fopen($log_location, "r")) !== FALSE) {
            while (($data = fgetcsv($handle,",")) !== FALSE) {
                $username = $data[3];
                $log = $data[7];
                list($space, $left_name, $space, $right_name, $database) = split('[#/$;]', $log);

                if($left_name == "cbabraham"){
                    $log_name = $right_name;
                }
                else{
                    $log_name = $left_name;
                }

                if($username == $log_name || $username == "cbabraham"){
                    $new_message = new Message();
                    $new_message->date_stamp = strtotime($data[2])*1000;
                    $new_message->message_contents = $data[6];
                    
                    
                    if($data[3] == "cbabraham"){
                        $new_message->to = "contact";
                        $new_message->from = "me";
                    }
                    else{
                        $new_message->to = "me";
                        $new_message->from = "contact";
                    }
                    if($new_message->message_contents != ""){
                        $contact_list->new_message($log_name, $new_message);
                    }
                }

            }
            fclose($handle);
        }



        print("Done parsing Google Voice logs\n");
    }
    
    public function parse_google_voice($log_location, $contact_list){
        
        // remove spaces from file names
        // foreach(glob("*.html") as $filename) {
        //  $pieces = explode(" ", $filename);
        
        //  $string = "";
        //  for($i = 0; $i< count($pieces); $i++){
        //      $string = $string . $pieces[$i];
        //  }
        //  rename($filename, $string);
        // }
        
        // print("Removed white space..\n");
        
        // //remove "<br>" from all files, it breaks the xml parser.
        // foreach(glob($log_location."*.html") as $value) {
        //      if($value != ""){
        //              $sed = "sed -e 's/<br>//g' '$value' > /tmp/tempfile.tmp";
        //              shell_exec($sed);
        //              shell_exec("mv /tmp/tempfile.tmp '$value'");
        //          }
        // }
        
        // print("Removed unclosed tags..\n");
        
        //parse and store data
        foreach(glob($log_location."*.html") as $filename) {	
            // $filename = "sms_convo_example.html";
    		$xml = simplexml_load_file($filename);
            // print_r($xml);
    		$title= $xml->head->title;
    		$pieces = explode("\n", $title);
    		if(count($pieces) == 1){
    			$contact_name = trim($pieces[0]);

    		}else $contact_name = trim($pieces[1]);

    		$count = count($xml->body->div[0]->div); //number of messages in file
    		for($i = 0; $i < $count; $i++){			
    			$new_message = new Message();
    			$new_message->message_contents = $xml->body->div[0]->div[$i]->q;
    			$date_stamp = $xml->body->div[0]->div[$i]->abbr;
    			$new_message->date_stamp = strtotime($date_stamp)*1000; //convert to millisecond unix time


    			if(( $xml->body->div[0]->div[$i]->cite->a->abbr) == "Me"){
    				$new_message->to = "me";
                    $new_message->from = "contact";
    			}
    			else{
                    $new_message->to = "contact";
                    $new_message->from = "me";
                }
    			
    			if($new_message->message_contents != ""){
    				$contact_list->new_message($contact_name, $new_message);
    			}
    		}
    	}
    	print("Done parsing Google Voice logs\n");
        
    }
}

?>