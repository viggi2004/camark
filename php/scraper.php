<?php
/*
*
* File: PHP Web Scraping Class 
* By: Jewel Ahmed<tojibon@gmail.com>
* Date: 28-05-2013
*  
*/
class Scraper {
        
        /*
        * @ param boolean if you need to use cookie for language support or authentication support
        */
        var $useCookie = false;
        
        /*
        * @ param string directory/path of cache contents to be stored
        */
        var $cacheLocation = "./cache/";           
        
        function __construct(){
        
        }
        
        /*
        * 
        * @ param string $url as 'http://maps.google.com'; Page url location which you want to fetch
        * @ param array  $data      
        * @ return a url page html content
        * */
        function aspFormPost($url, $data = array()){
            $regexViewstate = '/__VIEWSTATE\" value=\"(.*)\"/i';
            $regexEventVal  = '/__EVENTVALIDATION\" value=\"(.*)\"/i';
            
            $regs = array();
            $this->ckfile = tempnam ("/tmp", "CURLCOOKIE");       
            $ch = curl_init();      
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $gcontent=curl_exec($ch);
            $viewstate = $this->regexExtract($gcontent,$regexViewstate,$regs,1);
            $eventval = $this->regexExtract($gcontent, $regexEventVal,$regs,1);

            
            $data['__VIEWSTATE']=$viewstate;
            $data['__EVENTVALIDATION']=$eventval;
            echo $viewstate;
						exit();                                                          
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-length: ".strlen($data))); 

            curl_setopt($ch, CURLOPT_URL, $url);   
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->ckfile);

            $content = curl_exec($ch); 
            
            if($content === false) {
                $content = curl_error($ch);
            }   
            curl_close($ch);
            return $content;
        }
        
        /*
        * 
        * @ param string $url as 'http://maps.google.com'; Page url location which you want to fetch
        * @ param array  $data
        * @ param string $proxy [optional] as '[proxy IP]:[port]'; Proxy address and port number 
        * which you want to use
        * @ param string $userpass [optional] as '[username]:[password]'; Proxy authentication 
        * username and password
        * @ return a url page html content
        * @ access private
        * */
        function getPagePost($url, $data = array(), $proxy='', $userpass='', $header = array()) {
            
            $ch = curl_init();
            
            if(!empty($header)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
                curl_setopt($ch,CURLOPT_ENCODING , "gzip");
            }
                
            
            curl_setopt($ch, CURLOPT_URL, $url);
            if($this->useCookie)
                curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->ckfile);         
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

            if(!empty($proxy))
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
         
            if(!empty($userpass))
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userpass);
         
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            
            $result = curl_exec($ch);
            
            if($result === false) {
                $result = curl_error($ch);
            }   
            
            curl_close($ch);
            return $result;
        }
        
        /*
        * 
        * @ param string $url as 'http://maps.google.com'; Page url location which you want to fetch
        * @ param string $proxy [optional] as '[proxy IP]:[port]'; Proxy address and port number 
        * which you want to use
        * @ param string $userpass [optional] as '[username]:[password]'; Proxy authentication 
        * username and password
        * @ return a url page html content
        * 
        * */
        function curl($url, $proxy='', $userpass='') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            
            if($this->useCookie)
                curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->ckfile); 
                
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         
            if(!empty($proxy))
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
         
            if(!empty($userpass))
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userpass);
         
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
        
        /*
        * 
        * @ param string $url as 'http://maps.google.com'; Page url location which you want to fetch
        * @ param string $proxy [optional] as '[proxy IP]:[port]'; Proxy address and port number 
        * which you want to use
        * @ param string $userpass [optional] as '[username]:[password]'; Proxy authentication 
        * username and password
        * @ return a url page html content
        * 
        * */ 
        function curlWithCookie($url, $proxy='', $userpass='') {
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->ckfile); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         
            if(!empty($proxy))
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
         
            if(!empty($userpass))
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userpass);
         
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
         
        /*
        * 
        * @ param string $data Full html content which you want to parse
        * @ param string $s_tag Start tag of html content
        * @ param string $e_tag End tag of html content
        * @ return middle html content from given start tag and end tag of $data
        * */
        function getValueByTagName( $data, $s_tag, $e_tag) {
            $pos = strpos($data, $s_tag);
            if ($pos === false) {
                return '';
            } else {  
                $s = strpos( $data,$s_tag) + strlen( $s_tag);
                $e = strlen( $data);
                $data= substr($data, $s, $e);
                $s = 0;
                $e = strpos( $data,$e_tag);
                $data= substr($data, $s, $e);
                $data= substr($data, $s, $e);
                return  $data;
            }
        }    

        /*
        * 
        * @ param string $text Full html content which you want to parse
        * @ param string $regex rgx
        * @ param string $regs will be set to an array of all group values (assuming a match)
        * @ param string $nthValue nth number value example where n= 1,2,3,4
        * @ return a value of matched string
        * */
        function regexExtract($text, $regex, $regs, $nthValue) {
            if (preg_match($regex, $text, $regs)) {
                $result = $regs[$nthValue];
            } else {
                $result = "";
            }
            return $result;
        }
        
        
        /*
        * 
        * Creating an array of label and value pair from a  table which has only 2 td
        * @ param string $content full html content which you want to parse
        * @ param boolean $label_strip is the label allowed to be striped for html tags
        * @ param boolean $value_strip is the value allowed to be striped for html tags
        * $ return an array of label value pair
        * 
        */
        function table2td2array($content, $label_strip=true, $value_strip=false){
            $rowArr = explode('<tr>', $content);
            array_shift($rowArr);
            $tmp_ret_arr = array();
            
            
            if (!empty($rowArr) && is_array($rowArr)) {     
                foreach ($rowArr as $key => $value) {  
                    $tmpRowArr = explode('<td', $value);
                    array_shift($tmpRowArr);           
                    $tmpLabel = strip_tags($this->getValueByTagName($tmpRowArr[0], '>', '</td>'));                  
                    if($label_strip){
                        $tmpLabel = strip_tags($tmpLabel);     
                    }
                    $tmpValue = strip_tags($this->getValueByTagName($tmpRowArr[1], '>', '</td>'));                  
                    if($value_strip){
                        $tmpValue = strip_tags($tmpValue);                                            
                    }
                   
                    $tmpLabel = trim($tmpLabel);           
                    $tmpValue = trim($tmpValue);
                    
                    if(!empty($tmpLabel)){
                        $tmp_ret_arr[$tmpLabel] = $tmpValue;
                    }
                    
                    
                }        
            }
            return $tmp_ret_arr;
        }
        
        /*
        * 
        * Checking if cache file exist
        * @ param string $name cache file name
        * 
        */
        public function checkCacheAvailable($name){
            if($this->enableCache){
                $cachefile = $this->cacheLocation . $name;
                $cachetime = 5 * 60; //5 min
                if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)))  {
                    return true;
                } else {
                    return false;
                }    
            } else {
                return false;
            }
        }
        
        /*
        * 
        * Reading cache file
        * @ param string $name cache file name
        * 
        */
        public function readCache($name){
            $cachefile = $this->cacheLocation . $name;
            $output = file_get_contents($cachefile, FILE_USE_INCLUDE_PATH);
            return $output;
        }
        
        
        /*
        * 
        * Writing cache file with contents
        * @ param string $name cache file name
        * @ param string $content cache content to write on cache file
        * 
        */
        public function writeCache($name, $content){
            if($this->enableCache){
                $cachefile = $this->cacheLocation . $name;
                $fp = fopen($cachefile, 'w'); 
                fwrite($fp, $content); 
                fclose($fp);     
            }        
        }
}           
    
