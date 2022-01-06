<?php
//mysql server information
$DB_HOST = "mysql-app";
$DB_NAME = "crud_php";
$DB_USER = "root";
$DB_PASSWORD = "JGnf43!@";
$DB_TABLE = "people";
$people = [];

//connect to database
function connection($dbHost = "", $dbName = "", $dbUser = "", $dbPassword = "")
{
  try {
    $handle = new PDO(
      "mysql:host=$dbHost;dbname=$dbName;charset=utf8",
      "$dbUser",
      "$dbPassword",
      array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
    $handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $handle;
  } catch (PDOException $e) {
    return $e;
  }
}

//criate database
function createDatabase($dbHost = "", $dbName = "", $dbUser = "", $dbPassword = "")
{
  $conn = connection($dbHost, "", $dbUser, $dbPassword);
  //cria database caso não exista
  try {
    $stmt = $conn->prepare("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8 COLLATE utf8_general_ci");
    $stmt->execute();
    $stmt = null;
    $conn = null;
    return true;
  } catch (PDOException $e) {
    //http_response_code(503);
    echo $e->getMessage();
    return false;
  }
}

//create table
function createTable($conn = null, $tableName = "")
{
  //cria table caso não exista
  try {
    $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (";
    $sql .=  "id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,";
    $sql .= "name varchar(50) NOT NULL,";
    $sql .= "surname varchar(50) NOT NULL,";
    $sql .= "birth_date datetime NOT NULL";
    $sql .=  ");";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt = null;
    return true;
  } catch (PDOException $e) {
    //http_response_code(503);
    echo $e->getCode();
    return false;
  }
}

$conn = connection($DB_HOST, $DB_NAME, $DB_USER, $DB_PASSWORD);

if (method_exists($conn, "getCode") && $conn->getCode() == 1049) {
  createDatabase($DB_HOST, $DB_NAME, $DB_USER, $DB_PASSWORD);
  $conn = connection($DB_HOST, $DB_NAME, $DB_USER, $DB_PASSWORD);
  $table = createTable($conn, $DB_TABLE);
} else if (method_exists($conn, "getCode") && $conn->getCode() == 1045) {
  http_response_code(503);
  exit($conn->getCode());
} else if (method_exists($conn, "getCode") && $conn->getCode() == 2002) {
  http_response_code(503);
  exit($conn->getCode());
}

function insertPeople($conn = null, $tableName = "")
{
  if (isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['birthDate'])) {
    try {
      $stmt = $conn->prepare("INSERT INTO $tableName (name, surname, birth_date) VALUES (:name, :surname, :birth_date)");
      $stmt->bindParam(":name", $_POST['name']);
      $stmt->bindParam(":surname", $_POST['surname']);
      $stmt->bindParam(":birth_date", $_POST['birthDate']);
      $stmt->execute();
      header("Location: ./");
    } catch (PDOException $e) {
      http_response_code(503);
      exit($e->getCode());
    }
  }
}

function getPeople($conn, $dbTable){
  try {
    $stmt = $conn->prepare("SELECT id, name, surname, birth_date FROM $dbTable");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      $people = $stmt->fetchAll(PDO::FETCH_OBJ);
      return $people;
    }
    return [];
  } catch (PDOException $e) {
    if (method_exists($e, "getCode") && $e->getCode() == "42S02"){
      $table = createTable($conn, $dbTable);
      return [];
    }else{
      http_response_code(503);
      exit($e->getCode());
    }
  }
}

insertPeople($conn, $DB_TABLE);
$people = getPeople($conn, $DB_TABLE);
?>
<!doctype html>
<html lang="pt-BR">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

  <title>CRUD_PHP</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded-3">
    <div class="container-fluid">
      <a class="navbar-brand" href="./">
        <img src="https://getbootstrap.com/docs/5.1/assets/brand/bootstrap-logo.svg" alt="" width="30" height="24" class="d-inline-block align-text-top">
        CRUD_PHP
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="./">Home</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="row">
      <div class="col-md-12">
        <form action="./" method="POST">

          <div class="row">


            <div class="col-6 mb-3">
              <label for="inputName" class="form-label">Nome</label>
              <input value="joca" type="text" name="name" class="form-control" id="inputName" aria-describedby="nameHelp" required>
              <div id="nameHelp" class="form-text">Insira seu primeiro nome</div>
            </div>


            <div class="col-6 mb-3">
              <label for="inputSurname" class="form-label">Sobrenome</label>
              <input value="fonseca" type="text" name="surname" class="form-control" id="inputSurname" aria-describedby="sobrenomeHelp" required>
              <div id="sobrenomeHelp" class="form-text">Insira seu primeiro nome</div>
            </div>

            <div class="col-6 mb-3">
              <label for="inputBirthDate" class="form-label">Data de nascimento</label>
              <input value="1990-09-13" type="date" name="birthDate" class="form-control" id="inputBirthDate" aria-describedby="birthDateHelp" required>
              <div id="birthDateHelp" class="form-text">Insira sua data de nascimento</div>
            </div>

          </div>


          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
  <?php if (count($people) > 0) : ?>
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
            <table class="table table-striped table-hover table-dark table-bordered border-primary align-middle">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nome</th>
                  <th scope="col">Sobrenome</th>
                  <th scope="col">Data de nascimento</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($people as $key => $p) : ?>
                  <tr>
                    <th scope="row"><?php echo $p->id; ?></th>
                    <td><?php echo $p->name; ?></td>
                    <td><?php echo $p->surname; ?></td>
                    <td><?php echo $p->birth_date; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>


  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>
