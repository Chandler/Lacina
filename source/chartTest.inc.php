<?php
class Charts{

//==========================================================================
// Options for the primary graph and piechart page. Works with sms, calls, skype, msn and fb

    public function write_chart_options($graph_data, $n, $location, $filename){
        $general_options = $this->general_options();
        $main_chart_options = $this->main_chart_options($graph_data, $n);   
        $pie_chart_options = $this->pie_chart_options($graph_data, $n);

        $final_string = $general_options . "\n\n" . $main_chart_options . "\n\n" . $pie_chart_options;
        $file = $location."/".$filename.".js";
        file_put_contents($file, $final_string);
    }

    public function general_options(){

        $chart["style"] =  array("fontFamily" => "'serif'", "fontSize" => "'16px'");
        $options["chart"] = $chart;
        $options_object_literal = $this->generateJSObjectLiteral($options); 
        $final_string =  "var general_options = {" . $options_object_literal . "};\n\n";
        return $final_string;
    }

    public function pie_chart_options($graph_data, $n){
        $chart["renderTo"] = "'pie'";
        $title["text"] = "'Precent of All Messages'";
        $tooltip["formatter"] = "function() {
                            return '<b>'+ this.point.name +'</b>: '+ 
                            this.percentage +' %';
                            }";
        $plotOptions["pie"] = 
                            array("allowPointSelection" => "false",
                                  "cursor" => "'pointer'",
                                  "dataLabels" => 
                                      array("enabled"=> "true", 
                                            "color"=> "'#000000'", 
                                            "connectorColor"=> "'#000000'",
                                            "formatter"=> "function() {
                                                return '<b>'+ this.point.name +'</b>: '+
                                                Math.round(this.percentage*10)/10 +' %';
                                                }"));      
        $options["chart"] = $chart;
        $options["title"] = $title;
        $options["tooltip"] = $tooltip;
        $options["plotOptions"] = $plotOptions;

        $series["type"] = "'pie'";

        for($i = 0; $i<$n; $i++){  
            $name = $graph_data[$i][0]->contact_name;
            $tok = explode(" ",$name);
            $varname = $tok[0]. "_". $tok[1];
            $data[] = array("name " => "'$name'", "y" => $graph_data[$i][0]->get_message_count());   
         }

        $other = 0;
        $total = count($graph_data);

        for($i = $n+1; $i < $total; $i++){  
            $other = $other + $graph_data[$i][0]->get_message_count();  
        }
        $other_count = $total - $n;
        $data[] = array("name " => "'$other_count " . "others'", "y" => $other);  
        $series["data"] = $data;

        $options["series"][] = $series;

        $options_object_literal = $this->generateJSObjectLiteral($options); 
        $options_string .= "var pie_options = {".$options_object_literal."};\n\n";          
        $final_string =  $data_string ."\n\n" . $options_string; 
        return $final_string;
    }

    public function main_chart_options($graph_data, $n){
        $chart["renderTo"] = "'graph'";
        $chart["marginTop"] = "40";
        $chart["marginRight"] = "0";
        $chart["marginBottom"] = "0";
        $rangeSelector["enabled"] = "false";
        $xAxis["maxZoom"] = "2 // fourteen days";
        $legend["layout"] = "'horizontal'";
        $legend["align"] = "'top'";
        $legend["verticalAlign"] = "'top'";
        $legend["enabled"] = "true";
        $legend["y"] = "30"; 
        $plotOptions["area"] = array("stacking" => "\"normal\"");
                
        $options["chart"] = $chart;
        $options["rangeSelector"] = $rangeSelector;
        $options["title"] = $title;
        $options["xAxis"] = $xAxis;
        $options["yAxis"] = $yAxis;
        $options["legend"] = $legend;
        $options["plotOptions"] = $plotOptions;


                
        for($i = 0; $i<$n; $i++){  
            $name = $graph_data[$i][0]->contact_name;
            $tok = explode(" ",$name);
            $varname = $tok[0]. "_". $tok[1];
            $series["name"] = "'$name'";
            $series["data"] = $varname;
            // $series["type"] = "'area'";
            // $series["stack"] = "1";

            $options["series"][] = $series;
            
            $data_string .= $this->generate_javascript_array($varname, $graph_data[$i][1]);
        }
            
        $options_object_literal = $this->generateJSObjectLiteral($options); 
        $options_string .= "var main_options = {".$options_object_literal."};\n\n";          
        $final_string =  $data_string ."\n\n" . $options_string; 

        return $final_string;
    }

        public function write_scatter_options($scatter_data, $location, $filename){
        $chart["renderTo"] = "'scatter'";
        $chart["defaultSeriesType"] = "'scatter'";

        $title["text"] = "'All Text Messages'";
        $xAxis["title"] = array("text" => "'days'");

        // $xAxis["startOnTick"] = "true";
        // $xAxis["endOnTick"] = "true";
        // $xAxis["showLastLabel"] = "true";
        $yAxis["title"] = array("text" => "'hours'");

        $tooltip["formatter"] = "function() {
                                    return ''+ this.x +' cm, '+ this.y +' kg';
                                }";
        $legend["layout"] = "'vertical'";
        $legend["align"] = "'left'";
        $legend["verticalAlign"] = "'top'";
        $legend["x"] = "30"; 
        $legend["y"] = "70"; 
        $legend["floating"] = "true"; 
        $legend["backgroundColor"] = "'#FFFFFF'"; 
        $legend["borderWidth"] = "1"; 


        $plotOptions["scatter"] = 
            array("marker" => 
                array("radius"=> "5", "states"=> 
                    array("hover " =>
                        array("enabled"=> "true", "lineColor" =>"'rgb(100,100,100)'"
                        )
                    )
                ),
                "states"=> 
                    array("hover" =>
                        array("marker"=> 
                            array("enabled"=> "false"
                            )
                        )
                    )
                ); 

        $options["chart"] = $chart;
        $options["title"] = $title;
        $options["xAxis"] = $xAxis;
        $options["yAxis"] = $yAxis;
        $options["tooltip"] = $tooltip;
        $options["legend"] = $legend;
        $options["plotOptions"] = $plotOptions;

        
        foreach($scatter_data as $graph_item){
            $name = "'" . $graph_item["name"] .  "'"; 
            $series["name"] = $name;
            
            $i = 0;
            foreach($graph_item["value"] as $xyvalues){
                $data[] = array("x" => $xyvalues["x"], "y" => $xyvalues["y"]);
                if($i == 800){
                    break;
                }
                $i = $i + 1;
            }
            $series["data"] = $data;
            $options["series"][] = $series;
        }



        $options_object_literal = $this->generateJSObjectLiteral($options); 
        $final_string =  "var scatter_options = {" . $options_object_literal . "};\n\n";
        $file = $location."/".$filename.".js";
        file_put_contents($file, $final_string);    
    }

//==========================================================================
// Options for single contact chart. Works with sms

    public function write_single_contact_options($graph_data, $n, $location){
        mkdir($location);
        foreach($graph_data as $contact_data){
            $name = $contact_data["contact"]->contact_name;
            $tok = explode(" ",$name);
            $varname = $tok[0]. "_". $tok[1];

            $general_options = $this->general_options();
            $main_chart_options = $this->single_chart_options($contact_data);   
            
            $final_string = $general_options . "\n\n" . $main_chart_options . "\n\n";
            $file = $location."/".$varname.".js";
            file_put_contents($file, $final_string);

            $name_string .= $varname.".js ";
        }

        $final = "var contactString = \"".$name_string."\";\n";
        $file = $location."/"."single".".js";
        file_put_contents($file, $final);
    }

    public function single_chart_options($contact_data){

        $name = $contact_data["contact"]->contact_name;
        $tok = explode(" ",$name);
        $varname = $tok[0]. "_". $tok[1];

        $chart["renderTo"] = "'graph'";
        $chart["marginTop"] = "40";
        $chart["marginRight"] = "0";
        $chart["marginBottom"] = "0";
        $rangeSelector["enabled"] = "false";
        $title["text"] = "'Text Message Interactions Over Time'";
        $xAxis["maxZoom"] = "2 // fourteen days";
        $yAxis["title"] = array("text" => "'Weekly Message Volume'");
        $legend["layout"] = "'horizontal'";
        $legend["align"] = "'top'";
        $legend["verticalAlign"] = "'top'";
        $legend["enabled"] = "true";
        $legend["y"] = "30"; 
        $plotOptions["area"] = array("stacking" => "\"normal\"");
                
        $options["chart"] = $chart;
        $options["rangeSelector"] = $rangeSelector;
        $options["title"] = $title;
        $options["xAxis"] = $xAxis;
        $options["yAxis"] = $yAxis;
        $options["legend"] = $legend;
        $options["plotOptions"] = $plotOptions;
                
        $series["name"] = "'Sent by Chandler'";
        $series["data"] = $varname;
        $options["series"][] = $series;
        $data_string .= $this->generate_javascript_array($varname, $contact_data["outgoing"]);
        
        $series["name"] = "'Sent by " . "$name'";
        $series["data"] = "Chandler";
        $options["series"][] = $series;
        $data_string .= $this->generate_javascript_array("Chandler", $contact_data["incoming"]);
        
        $options_object_literal = $this->generateJSObjectLiteral($options); 
        $options_string .= "var main_options = {".$options_object_literal."};\n\n";          
        $final_string =  $data_string ."\n\n" . $options_string; 

        return $final_string;
    }

    public function single_pie_chart_options($contact, $name){
        $chart["renderTo"] = "'pie'";
        $title["text"] = "'Precent of All Texts'";
        $tooltip["formatter"] = "function() {
                            return '<b>'+ this.point.name +'</b>: '+ 
                            this.percentage +' %';
                            }";
        $plotOptions["pie"] = 
                            array("allowPointSelection" => "false",
                                  "cursor" => "'pointer'",
                                  "dataLabels" => 
                                      array("enabled"=> "true", 
                                            "color"=> "'#000000'", 
                                            "connectorColor"=> "'#000000'",
                                            "formatter"=> "function() {
                                                return '<b>'+ this.point.name +'</b>: '+
                                                Math.round(this.percentage*10)/10 +' %';
                                                }"));      
        $options["chart"] = $chart;
        $options["title"] = $title;
        $options["tooltip"] = $tooltip;
        $options["plotOptions"] = $plotOptions;

        $series["type"] = "'pie'";

        $data[] = array("name " => "Sent by ". "'$name'", "y" => $contact->outgoing_message_count());   
        $data[] = array("name " => "Sent by Chandler", "y" => $contact->incoming_message_count());   

        $series["data"] = $data;

        $options["series"][] = $series;

        $options_object_literal = $this->generateJSObjectLiteral($options); 
        $options_string .= "var pie_options = {".$options_object_literal."};\n\n";          
        $final_string =  $data_string ."\n\n" . $options_string; 
        return $final_string;
    }

//==========================================================================
// Helper Functions

    private function getTabs($num){
        $tabSet = "";
        for($i=0; $i<$num; $i++){
            $tabSet .= "\t";
        }
        return $tabSet;
    }

    private function isNumericArray($array){
        //loop over all keys to see if array is associative
        $keys = array_keys($array);
        $isNumeric = true;
        foreach($keys as $key => $value){
                if(!is_numeric($value)){
                        $isNumeric = false;
                        break;
                }
        }
        return $isNumeric;
    }

    public function generateJSObjectLiteral($array,$depth=null){
        if($depth == null){ $depth = 0; }
        if(empty($array)){ return; }

        $string = "";
        $count = 0;
        foreach($array as $key => $value){
            if($count > 0){
                $string .= ",\n";
            }
            if(is_array($value)){
                if(!$this->isNumericArray($value)){
                    if(!$this->isNumericArray($array)){
                       $string .= $this->getTabs($depth+1) . $key . ": {\n";
                       $string .= $this->generateJSObjectLiteral($value,$depth+1);
                       $string .= "\n" . $this->getTabs($depth+1) . "}";
                    }else{
                        if($count > 0){
                            $string .= $this->getTabs($depth+1);
                        }
                        $string .= "{\n";
                        $string .= $this->generateJSObjectLiteral($value,$depth+1);
                        $string .= "\n" . $this->getTabs($depth+1) . "}";
                    }
                }else{
                    $string .= $this->getTabs($depth+1) . $key . ": [";
                    $string .= $this->generateJSObjectLiteral($value,$depth+1);
                    $string .= "]";
                }
            }
            else{
                $string .= $this->getTabs($depth+1) . $key . ": " . $value;
            }
            $count++;
        }
        //$string .= "\n";

        return $string;
    }
    
    public function generate_javascript_array($name, $graph_data){
        $script = $script . "\nvar ".$name." = new Array(";
        $i = 0; 
        foreach ($graph_data as $value){
            if ($i < (count($graph_data)-1)){
                $script = $script . "[".$value[0].",".$value[1]."]" . ',';
            }
            else {
                $script = $script . "[".$value[0].",".$value[1]."]);\n";
            }
            $i = $i + 1;
        }
        return $script;
    }
    
}

?>