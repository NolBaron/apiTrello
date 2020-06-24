
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand" href="#">Bol4 AO</span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item disabled">
        <a class="nav-link" href="#">Home 
          <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Gebruikers
        </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <?php foreach ($bol4_AO as $row) {
              $memberName = getMemberFromDb($row->idMember);
              /* hieronder de 

              */
              //$memberName = doRequest('members/'.$row->idMember.'/fullName');
              ?>
              <a class="dropdown-item" href="index.php?list=<?= $keyName ?>&id=<?= $row->idMember ?>"><?php echo ucfirst($memberName);?></a>
            <?php } ?>
          </div>
      </li>
    </ul>
  </div>
</nav>

