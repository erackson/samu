<?php

class vertex
{
    public $key         = null;
    public $visited     = 0;
    public $distance    = 1000000;  // infinite
    public $parent      = null;
    public $path        = null;

    public function __construct($key)
    {
        $this->key  = $key;
    }
}

class PriorityQueue extends SplPriorityQueue
{
    public function compare($a, $b)
    {
        if ($a === $b) return 0;
        return $a > $b ? -1 : 1;
    }
}

function resetCreateGlobalVars(){
  global
          $v0,$v1,$v2,$v3,$v4,$v5,
          $list0, $list1, $list2, $list3, $list4, $list5,
          $adjacencyList
  ;

  $v0 = new vertex(0);
  $v1 = new vertex(1);
  $v2 = new vertex(2);
  $v3 = new vertex(3);
  $v4 = new vertex(4);
  $v5 = new vertex(5);

  $list0 = new SplDoublyLinkedList();
  $list0->push(array('vertex' => $v1, 'distance' => 3));
  $list0->push(array('vertex' => $v3, 'distance' => 1));
  $list0->rewind();

  $list1 = new SplDoublyLinkedList();
  $list1->push(array('vertex' => $v0, 'distance' => 3));
  $list1->push(array('vertex' => $v2, 'distance' => 7));
  $list1->rewind();

  $list2 = new SplDoublyLinkedList();
  $list2->push(array('vertex' => $v1, 'distance' => 7));
  $list2->push(array('vertex' => $v3, 'distance' => 8));
  $list2->push(array('vertex' => $v4, 'distance' => 12));
  $list2->rewind();

  $list3 = new SplDoublyLinkedList();
  $list3->push(array('vertex' => $v0, 'distance' => 1));
  $list3->push(array('vertex' => $v2, 'distance' => 8));
  $list3->rewind();

  $list4 = new SplDoublyLinkedList();
  $list4->push(array('vertex' => $v2, 'distance' => 12));
  $list4->push(array('vertex' => $v5, 'distance' => 3));
  $list4->rewind();

  $list5 = new SplDoublyLinkedList();
  $list5->push(array('vertex' => $v4, 'distance' => 3));
  $list5->rewind();

  $adjacencyList = array(
      $list0,
      $list1,
      $list2,
      $list3,
      $list4,
      $list5,
  );
}

resetCreateGlobalVars();

function calcShortestPaths(vertex $start, &$adjLists)
{
    // define an empty queue
    $q = new PriorityQueue();

    // push the starting vertex into the queue
    $q->insert($start, 0);
    $q->rewind();

    // mark the distance to it 0
    $start->distance = 0;

    // the path to the starting vertex
    $start->path = array($start->key);

    while ($q->valid()) {
        $t = $q->extract();
        $t->visited = 1;

        $l = $adjLists[$t->key];
        while ($l->valid()) {
            $item = $l->current();

            if (!$item['vertex']->visited) {
                if ($item['vertex']->distance > $t->distance + $item['distance']) {
                    $item['vertex']->distance = $t->distance + $item['distance'];
                    $item['vertex']->parent = $t;
                }

                $item['vertex']->path = array_merge($t->path, array($item['vertex']->key));

                $q->insert($item["vertex"], $item["vertex"]->distance);
            }
            $l->next();
        }
        $q->recoverFromCorruption();
        $q->rewind();
    }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Atividade Estrutura de Dados Não Lineares - Samu</title>
    <script type="text/javascript" src="js/vis.js"></script>

    <link rel="stylesheet" href="css/bootstrap.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="css/navbar.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="css/sticky-footer.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="css/vis.css" media="screen" title="no title" charset="utf-8">

    <style type="text/css">
      #mynetwork {
        width: 1024px;
        height: 400px;
        border: 1px solid lightgray;
      }
    </style>
  </head>
  <body>
    <!-- Begin page content -->
    <div class="container">
      <div class="jumbotron">
        <h1>Ambulância - SAMU</h1>
        <p>Dado um grafo com informações de ruas de cruzamentos de um bairro, no grafo. Pede-se
  desenvolver um sistema baseado na teoria dos grafos que determine um ponto dentro do
  bairro, qual seria o melhor local para colocar uma ambulância da SAMU, com o objetivo de
  minimizar o tempo de atendimento dessas emergências</p>
      </div>
      <div class="page-header">
        <h3>Mapeamento de valores do grafo</h3>
      </div>
      <?php
      $connections = [];
      foreach($adjacencyList as $kk => $valor){
        $connections[$kk] = [];
      ?>
      <table class="table table-hover">
        <thead>
          <tr>
            <th colspan="2">
              Arrestas do vértice <?php echo $kk; ?> (v<?php echo $kk; ?>) e seus valores
            </th>
          </tr>
          <tr>
            <th>
              Arresta com o vértice
            </th>
              <th>
                Valor
              </th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach($valor as $jj => $value){
            if(!@array_key_exists($kk, $connections[$value['vertex']->key])) $connections[$kk][$value['vertex']->key] = $value['distance'];
          ?>
          <tr>
            <td>
              v<?php echo $value['vertex']->key ?>
            </td>
            <td>
              <?php echo $value['distance'] ?>
            </td>
          </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
      <?php
      }
      ?>

      <div class="page-header">
        <h3>Somatório das distâncias de cada vértice para todos os demais vértices</h3>
      </div>
      <table class="table table-hover">
        <thead>
          <th>
            Vértice
          </th>
          <th>
            Somatório
          </th>
        </thead>
        <tbody>
        <?php
        $sum = [];
        for($x = 0; $x < 6; $x++){
          $sum[$x] = 0;
          $vertexName = "v$x";

          calcShortestPaths($$vertexName, $adjacencyList);

          for($j = 0; $j < 6; $j++){
            $vertexJName = "v$j";
            // echo '[' . implode(', ', $$vertexJName->path) . ']'.PHP_EOL;
            // echo  $$vertexJName->distance.PHP_EOL;

            $sum[$x] += $$vertexJName->distance;
          }
        ?>
          <tr>
            <td>
              <?php echo $vertexName ?>
            </td>
            <td>
              <?php echo $sum[$x] ?>
            </td>
          </tr>
        <?php

          resetCreateGlobalVars();
        }
        asort($sum);
        ?>
        </tbody>

      </table>

      <div class="page-header">
        <h3>Melhor cruzamento para posicionamente de uma ambulância da SAMU</h3>
        <h1><?php echo $bestNode = array_keys($sum)[0]; ?></h1>
      </div>

      <div class="page-header">
        <h3>Esboço gráfico do grafo</h3>
        <div id="mynetwork"></div>
      </div>

      <script type="text/javascript">
        // create an array with nodes
        var nodes = new vis.DataSet([
          <?php for($x = 0; $x < 6; $x++){ ?>
          {id: <?php echo $x; ?>, label: 'V<?php echo $x; ?>'<?php if($x == $bestNode){?>, color: '#F03967' <?php } ?>},
          <?php } ?>
        ]);

        // create an array with edges
        var edges = new vis.DataSet([
          <?php
          foreach($connections as $k => $connection){
            foreach($connection as $j => $value){
          ?>
          {from: <?php echo $k; ?>, to: <?php echo $j; ?>, label: <?php echo $value;?>, color: 'blue'},
          <?php
            }
          }
          ?>
        ]);

        // create a network
        var container = document.getElementById('mynetwork');
        var data = {
          nodes: nodes,
          edges: edges
        };
        var options = {};
        var network = new vis.Network(container, data, options);
      </script>
    </div>

    <footer class="footer">
      <div class="container">
        <p class="text-muted">Trabalho desenvolvido por Erackson Brito, Greyce Medeiros e Marcelo Policarpo.</p>
      </div>
    </footer>
  </body>
</html>
