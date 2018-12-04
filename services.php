<?php
$conn = null;
$action = $_POST['action'];
header('Content-type:application/json');
$conn = getDB();
switch ($action) {
  case 'getplaces':
    $stmt = $conn->prepare("SELECT p.id, p.name, p.longitud, p.latitud, p.image, u.username FROM place p JOIN usuario  u ON p.idCreador = u.id");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    echo(json_encode($stmt->fetchAll()));
    break;
  case 'signup':
    $ctSt = $conn->prepare("SELECT u.username, u.email FROM usuario u WHERE u.username = :username OR u.email = :email");
    $ctSt->bindParam(':username', $_POST['user']);
    $ctSt->bindParam(':email', $_POST['mail']);
    $ctSt->execute();
    $resultCheck = $ctSt->setFetchMode(PDO::FETCH_ASSOC);
    $resultNewuser = (object) Array(
      'code' => 200,
    );
    $resultExisting = (object) Array(
      'code' => 503,
      'message' => 'Existing user or mail'
    );
    if(count($ctSt->fetchAll()) == 0){
      $newUser = $conn->prepare("INSERT INTO usuario (username, email, password) VALUES (:usename, :email, :password)");
      $newUser->execute();
      echo(json_encode($resultNewuser));
      file_put_contents('./result.json', json_encode($resultNewuser));
    }else {
      echo(json_encode($resultExisting));
      file_put_contents('./result.json', json_encode($resultExisting));
    }
    break;
  default:
    echo(json_encode("ok"));
    break;
}

function getDB(){
  $servername = "localhost";
  $username = "root";
  $password = "";

  try{
    $conn = new PDO("mysql:host=$servername;dbname=campfire", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
  }catch(PDOException $e){
    echo "Connection failed: ".$e->getMessage();
  }
}
?>
