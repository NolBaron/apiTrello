<?php 

//5e6e268afafb454145398774
  include 'includes/header.php';
  require 'includes/functions.php';
  if(isset($_GET['list'])){
    $keyName = $_GET['list'];
  } else {
    $keyName = "Corona";
  }
  if(isset($_GET['id'])){
    $memberId = $_GET['id'];
  } else {
    $memberId = '5195e4438a158dda4200074e';
  }

  $cardsByBoard = doRequest('boards/'.BOARDIDBOL4AO.'/cards/open');
  $getCard = doRequest('cards/5ed9f22209ef721fabc1d2ff');
  $GetActionsByCard = doRequest('cards/5ed9f22209ef721fabc1d2ff/actions');
  $bol4_AO = doRequest('boards/'.BOARDIDBOL4AO.'/memberships');
  $memberById = doRequest('members/'.$memberId.'/fullName');
  $boardName = doRequest('boards/'.BOARDIDBOL4AO.'/lists');
  $memberEmoji = doRequest('members/'.$memberId.'/customStickers');
  $member = doRequest('members/'.$memberId);
  // echo '<pre>';
  // echo var_dump($GetActionsByCard);
  // echo '</pre>';
  print_r($GetActionsByCard);
  echo $GetActionsByCard[0]->data->text;
?>
<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container">
    <h1>Bol4 AO Dashboard voor iedereen <?= ucfirst($memberById->_value); ?></h1>
    <div class="row">
      <div class="col-3 this-height mt-4"><h4>Lijst</h4>
          <?php 
           $listNames = [];
            foreach($cardsByBoard as $card){
              if(in_array($memberId, $card->idMembers)){
                foreach($boardName as $list){
                  if($card->idList == $list->id){
                    $listNames[$list->name] = 1;
                  }
                }
              }
            }
            ksort($listNames);
            foreach ($listNames as $key => $value) { ?>
              <p class="p-0 m-0">
                <a href="index.php?id=<?= $memberId ?>&list=<?= $key ?>">
                  <?= $key ?></a>
              </p>
          <?php
            }
          ?>
      </div>
      <div class="col-8 bg-secondary pb-5 this-height">
        <div class="row pl-5 pt-3 pb-2 text-white">
          <?php
            if($member->avatarUrl == ""){
              $avatar = "https://werken-aan-projecten.nl/wp-content/uploads/2014/09/jong-aapje-voor-wordpress-bewerkt.png";
            } else {
              $avatar = $member->avatarUrl . "/original.png";
            }
          ?>
          <img class="mr-5 rounded-circle" height="100px" src="<?= $avatar ?>" alt="" title="">
          <h3>Open cards</h3>
        </div>
        <div class="row">
          <?php 
          foreach ($boardName as $key) { 
            if($key->name == $keyName){
            ?>
            <?php 
              foreach ($cardsByBoard as $row) {
                $leden = $row->idMembers;
                if(in_array($memberId, $leden)){
                  if($row->idList == $key->id ){ ?>
                    <div class="card bg-light mt-2 ml-5 p-0 col-3">
                      <div class="card-body">
                        <h5 class="card-title"><?= $key->name ?></h5>
                        <p class="card-text"><?= $row->name ?></p>
                      </div>
                    </div>
          <?php  
                  }
                }
              }
            }
          } ?>
        </div>
      </div>
    </div>
    
  <!-- We will be putting our dashboard right here -->
  </div> 
</body>
</html>