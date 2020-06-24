<?php
$key = '6827e780173d6cd9381da441dee77443';
$token = 'd5ef25d79a637a6fa828c795a6555d6ab0cf05cc3ed927f165f1480cb6e1069f';



function doRequest($action){
$cURLConnection = curl_init();
curl_setopt($cURLConnection, CURLOPT_URL, 'https://api.trello.com/1/'.$action.'?key=6827e780173d6cd9381da441dee77443&token=d5ef25d79a637a6fa828c795a6555d6ab0cf05cc3ed927f165f1480cb6e1069f');
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
$phoneList = curl_exec($cURLConnection);
curl_close($cURLConnection);
$data = json_decode($phoneList);

return $data;
}

$memberNaam = doRequest('members/5195e4438a158dda4200074e/fullName');
$data = doRequest('members/me/boards');
$bol4_AO = doRequest('boards/58d5463bd578d3c42e47af8c/memberships');

// foreach ($data as $row) {
//     echo '<p>';
//     echo $row->name;
//     echo '<p>';
// }
?>  <!--  in deze ul worden de trello borden getoond -->
    <h1>Trello borden van Arnold</h1>
    <ul> 
        <?php foreach ($data as $row) { ?>
        <li><a href="<?= $row->url ?>"><?= $row->name ?></a></li>
        <?php } ?>
    </ul>
    <h1>Members van Bol4 AO</h1>
    <ul> 
        <?php foreach ($bol4_AO as $row) {
        //echo $row->idMember;
        $memberName = doRequest('members/'.$row->idMember.'/fullName');
        ?>
        <li><?php echo $memberName->_value;?></li>
        <?php } ?>
    </ul>


<?php
echo '<pre>';
var_dump($memberNaam);
echo '</pre>';
// echo '<pre>';
// echo print_r($data[5]);
// echo '</pre>';
echo '<pre>';
echo print_r($bol4_AO);
echo '</pre>';

?>