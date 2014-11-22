<?php

require 'vendor/autoload.php'; // use PCRE patterns you need Pux\PatternCompiler class.
use Pux\Executor;

// Get the database connection and return the reference for use
function getConnection() {
  $dbhost = "localhost";
  $dbuser = "root";
  $dbpass = "diehard4";
  $dbname = "pux_test";
  $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbh;
}

// Wine Controller
class WineController {
  public function __construct() {
    $this->dbh = getConnection();
  }
  public function indexAction() {
    $sql = "select * FROM wines ORDER BY name";
    try {
      $db = $this->dbh;
      $stmt = $db->query($sql);
      $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
      echo '{"wines": ' . json_encode($wines) . '}';
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
  }
  public function showAction($id) {
    $sql = "SELECT * FROM wines WHERE id=:id";
    try {
      $db = $this->dbh;
      $stmt = $db->prepare($sql);
      $stmt->bindParam("id", $id);
      $stmt->execute();
      $wine = $stmt->fetchObject();
      $db = null;
      echo json_encode($wine);
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
  }
  public function createAction() {
    $sql = "INSERT INTO wines (name, grapes, country, region, year, description) VALUES (:name, :grapes, :country, :region, :year, :description)";
    try {
      $db = $this->dbh;
      $stmt = $db->prepare($sql);
      $stmt->bindParam("name", $_POST['name']);
      $stmt->bindParam("grapes", $_POST['grapes']);
      $stmt->bindParam("country", $_POST['country']);
      $stmt->bindParam("region", $_POST['region']);
      $stmt->bindParam("year", $_POST['year']);
      $stmt->bindParam("description", $_POST['description']);
      $stmt->execute();
      $wine->id = $db->lastInsertId();
      $db = null;
      echo json_encode($wine);
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
  }
  public function updateAction($id) {
    // Remember to send request using x-www-form-urlencoded
    parse_str(file_get_contents('php://input'),$_PUT);
    $wine = $_PUT;
    $sql = "UPDATE wines SET name=:name, grapes=:grapes, country=:country, region=:region, year=:year, description=:description, picture=:picture WHERE id=:id";
    try {
      $db = $this->dbh;
      $stmt = $db->prepare($sql);
      $stmt->bindParam("name", $wine['name']);
      $stmt->bindParam("grapes", $wine['grapes']);
      $stmt->bindParam("country", $wine['country']);
      $stmt->bindParam("region", $wine['region']);
      $stmt->bindParam("year", $wine['year']);
      $stmt->bindParam("description", $wine['description']);
      $stmt->bindParam("picture", $wine['picture']);
      $stmt->bindParam("id", $id);
      $stmt->execute();
      $db = null;
      echo json_encode($wine);
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
  }
  public function destroyAction($id) {
    $sql = "DELETE FROM wines WHERE id=:id";
    try {
      $db = $this->dbh;
      $stmt = $db->prepare($sql);
      $stmt->bindParam("id", $id);
      $stmt->execute();
      $db = null;
      echo '{"success":{"text":"Deleted successfully."}}';
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
  }
  public function searchAction($query) {
    $sql = "SELECT * FROM wines WHERE UPPER(name) LIKE :query ORDER BY name";
    try {
      $db = $this->dbh;
      $stmt = $db->prepare($sql);
      $query = "%".$query."%";
      $stmt->bindParam("query", $query);
      $stmt->execute();
      $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
      echo '{"wines": ' . json_encode($wines) . '}';
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
  }
}

// Routing Variable
$mux = new Pux\Mux;

// WineController Routings
$mux->get('/', ['WineController', 'indexAction']);
$mux->get('/wines', ['WineController', 'indexAction']);
$mux->get('/wines/:id', ['WineController', 'showAction']);
$mux->post('/wines', ['WineController', 'createAction']);
$mux->put('/wines/:id', ['WineController', 'updateAction']);
$mux->delete('/wines/:id', ['WineController', 'destroyAction']);
$mux->get('/wines/search/:query', ['WineController', 'searchAction']);

// General Stuff for starting Pux application
if (!isset($_SERVER['PATH_INFO'])) {
  $route = $mux->dispatch('/');
} else {
  $route = $mux->dispatch($_SERVER['PATH_INFO']);
}
Executor::execute($route);
