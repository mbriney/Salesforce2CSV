<?php

function outputCSV($data,$filename) {
    $outputBuffer = fopen($filename, 'w');
    foreach($data as $val) {
        fputcsv($outputBuffer, $val);
    }
    fclose($outputBuffer);
}

 
function objectToArray($d) {
  if (is_object($d)) {
    $d = get_object_vars($d);
  }

  if (is_array($d)) {
    return array_map(__FUNCTION__, $d);
  }
  else {
    return $d;
  }
}
 
// Configure your header rows here (CSV Header Label => Salesforce field value) 
$header = array(
  'Id' => 'Id',
  'Name' => 'Name',
  'Email Address' => 'Email',
  );

// Convert header array to a query
$query = implode(',', $header);

define("SOAP_CLIENT_BASEDIR", "./soapclient");
require_once (SOAP_CLIENT_BASEDIR.'/SforceEnterpriseClient.php');
require_once (SOAP_CLIENT_BASEDIR.'/userAuth.php');
try {
  $mySforceConnection = new SforceEnterpriseClient();
  $mySoapClient = $mySforceConnection->createConnection(SOAP_CLIENT_BASEDIR.'/enterprise.wsdl.xml');
  $mylogin = $mySforceConnection->login($USERNAME, $PASSWORD);
  
  // Query Salesforce
  $query = 'SELECT '.$query.' from Contact limit 5';
  $response = $mySforceConnection->query(($query));

  // Convert returned data to array
  $data = objectToArray($response->records);
  
  // Create the CSV header line
  $headerline[0] = array_flip($header);

  // Merge everything together
  $data = array_merge($headerline, $data);

  // Output as CSV
  outputCSV($data,'test.csv');

} catch (Exception $e) {
  echo $e->faultstring;
}

?>
