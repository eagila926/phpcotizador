<?php
$menufarmacia = '
  <nav class="navbar navbar-default" role="navigation" style="background-color: #9D578A;">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <!--<a class="navbar-brand" href="#">Escollanos</a>-->
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class=""><a href="reporte_general.php" style="color: white;">Cotizador</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li class=""><a href="index.php" style="color: white;">Inicio</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="color: white;">
             <i class="fa fa-product-hunt fa-fw"></i> Pedido<b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <li><a href="formulas_ingresadas.php" style="color: black;">Formulas Establecidas</a></li>
              <li><a href="formula.php" style="color: black;">Formulas No Establecidas</a></li>
            </ul>
          </li>
          <li class=""><a href="inventario.php" style="color: white;"><i class="fa fa-list "></i>Inventario</a></li>
          
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>
';
?>
