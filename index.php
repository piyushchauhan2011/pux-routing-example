<?php

require 'vendor/autoload.php'; // use PCRE patterns you need Pux\PatternCompiler class.
use Pux\Executor;

//require 'rb.php';
//R::setup('sqlite:tmp/pux.db');
//R::setup('mysql:host=localhost;dbname=pux_test',
//        'root','diehard4');

$mysqli = mysqli_connect("localhost", "root", "diehard4", "pux_test");

class ProductController {
  public function indexAction() {
    //echo 'Index Action';
    //$post = R::dispense('post');
    //$post->text = 'Hello World';

    //$id = R::store($post);       //Create or Update
    //$post = R::load('post',$id); //Retrieve
    //R::trash($post);             //Delete
    
    $arr = array(
      'name' => 'Piyush',
      'age'  => 24
    );

    //echo json_encode($arr);
    //echo json_encode($post->export());
    global $mysqli;
    $res = mysqli_query($mysqli, "SELECT * FROM posts");
    $row = mysqli_fetch_assoc($res);

    echo json_encode($row);

  }
  public function listAction() {
    echo 'product list';
  }
  public function itemAction($id) { 
    echo "product $id";
  }
}
$mux = new Pux\Mux;
$mux->add('/', ['ProductController','indexAction']);
$mux->add('/product', ['ProductController','listAction']);
$mux->add('/product/:id', ['ProductController','itemAction'] , [
  'require' => [ 'id' => '\d+', ],
  'default' => [ 'id' => '1', ]
]);
if (!isset($_SERVER['PATH_INFO'])) {
  $route = $mux->dispatch('/');
} else {
  $route = $mux->dispatch($_SERVER['PATH_INFO']);
}
Executor::execute($route);

//R::close();
