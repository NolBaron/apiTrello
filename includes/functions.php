<?php
require 'config.php';



function dbConnect(){
	$servername = SERVERNAME;
    $username = USERNAME;
    $password = PASSWORD;
    $databasename = DATABASENAME;
    try
    {
        $conn = new PDO("mysql:host=$servername;dbname=$databasename", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }
    return $conn;
}

function getMemberFromDb($id){
	$conn = dbConnect();    
	$stmt = $conn->prepare("SELECT * FROM users WHERE trelloId = :id");
	$stmt->execute([':id' => $id]);
	$result = $stmt->fetch();
	if($result){
	  return $result['fullName'];
	} else {
	  $memberName = doRequest('members/'.$id.'/fullName');
	  $stmt = $conn->prepare("INSERT INTO users (trelloId, fullName) VALUES (:id, :name)");
	  $stmt->execute([':id' => $id, ':name' => $memberName->_value]);
	  return $memberName->_value;
	}
}

function doRequest($action){

	$cURLConnection = curl_init();
	curl_setopt($cURLConnection, 
	CURLOPT_URL, 'https://api.trello.com/1/'.$action.'?key='.APIKEY.'&token='.APITOKEN);
	curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
	$phoneList = curl_exec($cURLConnection);
	curl_close($cURLConnection);
	$data = json_decode($phoneList);

	return $data;
}

?>