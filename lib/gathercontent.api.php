<?php

class PR_GatherContent { 

    private $api_url; 
    private $api_key; 
    private $password; 

    /** 
     * Class Constructor 
     *
     * @access public 
     * @return null 
     */ 

    public function __construct()
    {
        $this->api_url = API_URL; 
        $this->api_key = API_KEY; 
        $this->password = 'x'; // leave it as 'x' 
    } 

    /** 
     * Function test_api 
     * 
     * Few test calls to GatherContent API 
     * 
     * @access public 
     * @return null 
     */ 

    public function request($command = '', $postfields = array())
    { 
        $query = $this->_curl($command, $postfields); 
        
        $results = json_decode($query['response'], true); 
        
        return $results;

        
    } 

    /** 
     * Function _curl 
     * 
     * Using cURL to access GatherContent API 
     * 
     * @access private 
     * @param string 
     * @param array 
     * @return array 
     */ 

    private function _curl($command = '', $postfields = array())
    { 
        $postfields = http_build_query($postfields); 
        $session = curl_init(); 

        curl_setopt($session, CURLOPT_URL, $this->api_url.$command); 
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); 
        curl_setopt($session, CURLOPT_HEADER, false); 
        curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded')); 
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($session, CURLOPT_USERPWD, $this->api_key . ":" . $this->password); 
        curl_setopt($session, CURLOPT_POST, true); 
        curl_setopt($session, CURLOPT_POSTFIELDS, $postfields); 

        if (substr($this->api_url, 0, 8) == 'https://') {
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, true); 
        }

        $response = curl_exec($session); 
        $httpcode = curl_getinfo($session, CURLINFO_HTTP_CODE); 
        curl_close($session); 
                

        return array( 'code' => $httpcode, 'response' => $response );
    } 
}