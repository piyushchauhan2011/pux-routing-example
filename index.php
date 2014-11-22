<?php

require 'vendor/autoload.php'; // use PCRE patterns you need Pux\PatternCompiler class.
use Pux\Executor;

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
}

// Routing Variable
$mux = new Pux\Mux;

// WineController Routings
$mux->add('/', ['WineController', 'indexAction']);
$mux->add('/wines', ['WineController', 'indexAction']);
$mux->add('/wines/:id', ['WineController', 'showAction']);

// General Stuff for starting Pux application
if (!isset($_SERVER['PATH_INFO'])) {
  $route = $mux->dispatch('/');
} else {
  $route = $mux->dispatch($_SERVER['PATH_INFO']);
}
Executor::execute($route);
