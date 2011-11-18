<?php
require_once("contactClass.inc.php");

class ContactList{
    
    public $list;
    
    public function new_message($contact_name, $message){
        if($contact_name == "+12085149798" || $contact_name == "+12084122592" ||$contact_name == "+12085966152"){
    		$contact_name = "Lorena Davis";
    	}
        if($contact_name == "hnh_lorena"){
            $contact_name = "lorenadavis";
        }
        
        if (!array_key_exists($contact_name, $this->list)) {
			$new_contact = new Contact();
			$new_contact->contact_name = $contact_name;		
			$this->list[$new_contact->contact_name] = $new_contact;
		}
    	$this->list[$contact_name]->add_message($message);
		
    }
    public function new_call($contact_name, $call){
        if($contact_name == "+12085149798" || $contact_name == "+12084122592" || $contact_name == "+12085966152"){
    		$contact_name = "Lorena Davis";
    	}
    	
        if (!array_key_exists($contact_name, $this->list)) {
			$new_contact = new Contact();
			$new_contact->contact_name = $contact_name;		
			$this->list[$new_contact->contact_name] = $new_contact;
		}
    	$this->list[$contact_name]->add_call($call);
		
    }
    public function get_contact($name){
        
        foreach($this->list as $contact){
            if($contact->contact_name == $name){
                $result = $contact;
                break;
            }
        }
        return $result;
    }
      
    public function sort_contacts(){
        uasort($this->list, array($this, 'contactcmp'));
    }
      
    public function __construct(){
        $this->list = array();
    }
    
    public function contactcmp($a, $b)
    {
        if ($a->get_message_count() == $b->get_message_count() ) {
            return 0;
        }
        return ($a->get_message_count() > $b->get_message_count() ) ? -1 : 1;
    }
    
}
?>