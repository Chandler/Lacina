<?php

class Contact{
     
    public $contact_name;
    public $username;
	public $tel;
	
	public $all_messages;
	private $message_count;
	private $new_message_flag;
	
	private $all_calls;
	private $call_count;
	private $call_flag;	
	
	public function add_message($message){
	    $this->all_messages[] = $message;
	    $this->new_message_flag = true;
	}
	public function add_call($call){ //$message object
	    $this->all_calls[] = $call;
	    $this->new_call_flag = true;
	}
	
	public function get_message_count(){
	    if($this->new_message_flag == true){
	        $this->message_count = count($this->all_messages);
    		usort($this->all_messages, array($this, "msgcmp"));
    		$this->new_message_flag = false;
	    }
	    return $this->message_count;
	}	
	public function get_call_count(){
	    if($this->new_call_flag == true){
	        $this->call_count = count($this->all_messages);
    		usort($this->all_calls, array($this, "msgcmp"));
    		$this->new_message_flag = false;
	    }
	    return $this->message_count;
	}

    	public function get_message_timestamps(){
	    return $this->return_timestamps($this->all_messages);
	}

	public function get_call_timestamps(){
	    return $this->return_timestamps($this->all_calls);
	}

	private function return_timestamps($message_list){
	    $timestamps = array();	    
   		foreach($message_list as $messageKey => $message){
   		    $timestamps[] = $message->date_stamp;
   		} 		
   		return $timestamps;	
    }

	public function get_split_message_timestamps(){
	    $incoming_timestamps = array();	
	    $outgoing_timestamps = array();    
   		foreach($this->all_messages as $messageKey => $message){
   		    if($message->from == "me"){
   		    	$outgoing_timestamps[] = $message->date_stamp;	
   		    }
   		    if($message->to == "me"){
   		    	$incoming_timestamps[] = $message->date_stamp;	
   		    }
   		} 	
   		return array("incoming" => $incoming_timestamps, "outgoing" => $outgoing_timestamps);
    }
    
    private function msgcmp($a, $b)
    {
        if ($a->date_stamp == $b->date_stamp) {
            return 0;
        }
        return ($a->date_stamp < $b->date_stamp) ? -1 : 1;
    }
    
}
	
?>