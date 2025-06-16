<?php
session_start();
include('../controllers/verificar_login.php');
include('../config/conexao.php');

if ($_SESSION['cargo_usuario'] != 'Administrador' && $_SESSION['cargo_usuario'] != 'Gerente' && $_SESSION['cargo_usuario'] != 'Tesoureiro') {
  header('Location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <title>Painel Financeiro - EletroService</title>

  <style>
    :root {
      --primary-color: #1ED760;
      --dark-bg: #2c3e50;
      --sidebar-bg : #043d2f;
    }

    .sidebar {
      background: var(--sidebar-bg);
      min-height: 100vh;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar .logo {
      color: white;
      font-weight: bold;
      font-size: 1.5rem;
      padding: 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .sidebar .nav-link {
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0;
      transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      transform: translateX(5px);
    }

    .sidebar .nav-link i {
      margin-right: 0.5rem;
      width: 20px;
    }

    .main-content {
      background: #f8f9fa;
      min-height: 100vh;
    }

    .header-bar {
      background: white;
      border-bottom: 1px solid #dee2e6;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .user-name {
      color: var(--primary-color);
      font-weight: bold;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        z-index: 1050;
        transition: left 0.3s ease;
      }

      .sidebar.show {
        left: 0;
      }

      .sidebar-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <div class="row g-0">
      <!-- Sidebar -->
      <div class="col-lg-3 col-xl-2">
        <div class="sidebar" id="sidebar">
          <div class="logo">
            <i class="bi bi-lightning-charge"></i>
            EletroService
          </div>

          <nav class="nav flex-column">
            <a class="nav-link" href="../controllers/movimentacoes.php">
              <i class="bi bi-arrow-left-right"></i>
              Movimentações
            </a>
            <a class="nav-link" href="../controllers/gastos.php">
              <i class="bi bi-cash-stack"></i>
              Gastos
            </a>
            <a class="nav-link" href="../controllers/vendas.php">
              <i class="bi bi-cart"></i>
              Vendas
            </a>
            <a class="nav-link" href="../controllers/pagamentos.php">
              <i class="bi bi-credit-card"></i>
              Pagamentos
            </a>
            <a class="nav-link" href="../controllers/compras.php">
              <i class="bi bi-bag-check"></i>
              Compras
            </a>
          </nav>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-lg-9 col-xl-10">
        <div class="main-content">
          <!-- Header -->
          <header class="header-bar p-3">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Mobile menu button -->
              <button class="btn btn-outline-secondary d-lg-none" type="button" id="sidebarToggle">
                <i class="bi bi-list"></i>
              </button>

              <!-- Title -->
              <div class="d-flex align-items-center">
                <h2 class="mb-0 me-2">Tesoureiro(a)</h2>
                <?php if ($_SESSION['cargo_usuario'] != 'Administrador' && $_SESSION['cargo_usuario'] != 'Gerente'): ?>
                  <span class="user-name"><?php echo $_SESSION['nome_usuario']; ?></span>
                <?php endif; ?>
              </div>

              <!-- Options Dropdown -->
              <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" id="optionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-gear me-1"></i>
                  Opções
                </button>

                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="optionsDropdown">
                  <?php if ($_SESSION['cargo_usuario'] == 'Administrador' || $_SESSION['cargo_usuario'] == 'Gerente'): ?>
                    <li><a class="dropdown-item" href="../views/painel_funcionario.php">
                        <i class="bi bi-people me-2"></i>Painel Funcionário
                      </a></li>
                    <li><a class="dropdown-item" href="../views/painel_admin.php">
                        <i class="bi bi-shield-check me-2"></i>Painel Administrador
                      </a></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                  <?php endif; ?>
                  <li><a class="dropdown-item text-danger" href="index.php">
                      <i class="bi bi-box-arrow-right me-2"></i>Sair
                    </a></li>
                </ul>
              </div>
            </div>
          </header>

          <!-- Page Content -->
          <main class="p-4">
            <!-- Financial Summary Cards -->
            <div class="row g-4 mb-4">
              <!-- Serviços -->
              <div class="col-lg-3 col-md-6">
                <div class="card text-dark bg-success-subtle">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-4">
                        <div class="text-center">
                          <i class="bi bi-tools fs-1 opacity-75"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <div class="numbers">
                          <p class="card-category mb-1">Serviços</p>
                          <?php
                          $query_servicos = "select sum(valor) as total from movimentacoes where data = curDate() and movimento = 'Serviço' order by id asc";
                          $result_servicos = mysqli_query($conexao, $query_servicos);

                          while ($res_serv = mysqli_fetch_array($result_servicos)) {
                          ?>
                            <h4 class="card-title mb-0">R$ <?php echo number_format($res_serv['total'], 2, ',', '.'); ?></h4>
                          <?php
                          }
                          ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <div class="stats">
                      <?php
                      $query_servicos = "select * from movimentacoes where data = curDate() and movimento = 'Serviço' order by id asc";
                      $result_servicos = mysqli_query($conexao, $query_servicos);
                      $numero_servicos = mysqli_num_rows($result_servicos);
                      ?>
                      <small><i class="bi bi-arrow-clockwise me-1"></i> Total de Serviços: <?php echo $numero_servicos; ?></small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Vendas -->
              <div class="col-lg-3 col-md-6">
                <div class="card text-dark bg-success-subtle">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-4">
                        <div class="text-center">
                          <i class="bi bi-cart-check fs-1 opacity-75"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <div class="numbers">
                          <p class="card-category mb-1">Vendas</p>
                          <?php
                          $query_vendas = "select sum(valor) as total from vendas where data = curDate() and status = 'Efetuada' order by id asc";
                          $result_vendas = mysqli_query($conexao, $query_vendas);

                          while ($res_vendas = mysqli_fetch_array($result_vendas)) {
                          ?>
                            <h4 class="card-title mb-0">R$ <?php echo number_format($res_vendas['total'], 2, ',', '.'); ?></h4>
                          <?php
                          }
                          ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <div class="stats">
                      <?php
                      $query_vendas = "select * from vendas where data = curDate() and status = 'Efetuada' order by id asc";
                      $result_vendas = mysqli_query($conexao, $query_vendas);
                      $numero_vendas = mysqli_num_rows($result_vendas);
                      ?>
                      <small><i class="bi bi-arrow-clockwise me-1"></i> Total de Vendas: <?php echo $numero_vendas; ?></small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Gastos -->
              <div class="col-lg-3 col-md-6">
                <div class="card text-dark bg-success-subtle">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-4">
                        <div class="text-center">
                          <i class="bi bi-arrow-down-circle fs-1 opacity-75"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <div class="numbers">
                          <p class="card-category mb-1">Gastos</p>
                          <?php
                          $query_gastos = "select sum(valor) as total from gastos where data = curDate() order by id asc";
                          $result_gastos = mysqli_query($conexao, $query_gastos);

                          while ($res_gastos = mysqli_fetch_array($result_gastos)) {
                          ?>
                            <h4 class="card-title mb-0">R$ <?php echo number_format($res_gastos['total'], 2, ',', '.'); ?></h4>
                          <?php
                          }
                          ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <div class="stats">
                      <?php
                      $query_gastos = "select * from gastos where data = curDate() order by id asc";
                      $result_gastos = mysqli_query($conexao, $query_gastos);
                      $numero_gastos = mysqli_num_rows($result_gastos);
                      ?>
                      <small><i class="bi bi-arrow-clockwise me-1"></i> Total de Gastos: <?php echo $numero_gastos; ?></small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Saldo Diário -->
              <div class="col-lg-3 col-md-6">
                <div class="card text-dark bg-success-subtle">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-4">
                        <div class="text-center">
                          <i class="bi bi-wallet2 fs-1 opacity-75"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <div class="numbers">
                          <p class="card-category mb-1">Saldo Diário</p>
                          <?php
                          $query_entradas = "select sum(valor) as total_entradas from movimentacoes where data = curDate() and tipo = 'Entrada' order by id asc";
                          $result_entradas = mysqli_query($conexao, $query_entradas);

                          while ($res_entradas = mysqli_fetch_array($result_entradas)) {
                            $query_saidas = "select sum(valor) as total_saidas from movimentacoes where data = curDate() and tipo = 'Saída' order by id asc";
                            $result_saidas = mysqli_query($conexao, $query_saidas);

                            while ($res_saidas = mysqli_fetch_array($result_saidas)) {
                              $total = $res_entradas['total_entradas'] - $res_saidas['total_saidas'];
                          ?>
                              <h4 class="card-title mb-0">
                                <?php
                                if ($total >= 0) {
                                  echo '<span class="text-white">R$ ' . number_format($total, 2, ',', '.') . '</span>';
                                } else {
                                  echo '<span class="text-warning">R$ ' . number_format($total, 2, ',', '.') . '</span>';
                                }
                                ?>
                              </h4>
                          <?php
                            }
                          }
                          ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <div class="stats">
                      <?php
                      $query_mov = "select * from movimentacoes where data = curDate() order by id asc";
                      $result_mov = mysqli_query($conexao, $query_mov);
                      $numero_mov = mysqli_num_rows($result_mov);
                      ?>
                      <small><i class="bi bi-arrow-clockwise me-1"></i> Total Movimentações: <?php echo $numero_mov; ?></small>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Últimas Movimentações -->
            <div class="row">
              <div class="col-12">
                <h5 class="mb-3">ÚLTIMAS MOVIMENTAÇÕES</h5>
                <hr>

                <div class="row g-3">
                  <?php
                  $query = "select * from movimentacoes where data >= curDate() order by id desc limit 4";
                  $result = mysqli_query($conexao, $query);
                  $row = mysqli_num_rows($result);

                  if ($row == 0) {
                    echo '<div class="col-12"><div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Não existem movimentações hoje!</div></div>';
                  } else {
                    while ($res_1 = mysqli_fetch_array($result)) {
                      $tipo = $res_1["tipo"];
                      $movimento = $res_1["movimento"];
                      $valor = $res_1["valor"];
                      $funcionario = $res_1["funcionario"];

                      if ($tipo == 'Entrada') {
                  ?>
                        <div class="col-lg-3 col-md-6">
                          <div class="card text-dark bg-success-subtle">
                            <div class="card-header">
                              <h6 class="mb-0"><?php echo $movimento; ?></h6>
                            </div>
                            <div class="card-body">
                              <h5 class="card-title">R$ <?php echo number_format($valor, 2, ',', '.'); ?></h5>
                              <p class="card-text small"><?php echo $funcionario; ?></p>
                            </div>
                          </div>
                        </div>
                      <?php
                      } else {
                      ?>
                        <div class="col-lg-3 col-md-6">
                          <div class="card text-dark bg-success-subtle">
                            <div class="card-header">
                              <h6 class="mb-0"><?php echo $movimento; ?></h6>
                            </div>
                            <div class="card-body">
                              <h5 class="card-title">R$ <?php echo number_format($valor, 2, ',', '.'); ?></h5>
                              <p class="card-text small"><?php echo $funcionario; ?></p>
                            </div>
                          </div>
                        </div>
                  <?php
                      }
                    }
                  }
                  ?>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>
  </div>

  <!-- Mobile sidebar backdrop -->
  <div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop" style="display: none;"></div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Mobile sidebar toggle
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      const sidebar = document.getElementById('sidebar');
      const backdrop = document.getElementById('sidebarBackdrop');

      sidebar.classList.add('show');
      backdrop.style.display = 'block';
    });

    // Close sidebar when clicking backdrop
    document.getElementById('sidebarBackdrop').addEventListener('click', function() {
      const sidebar = document.getElementById('sidebar');
      const backdrop = document.getElementById('sidebarBackdrop');

      sidebar.classList.remove('show');
      backdrop.style.display = 'none';
    });

    // Close sidebar on window resize if large screen
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 992) {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        sidebar.classList.remove('show');
        backdrop.style.display = 'none';
      }
    });
  </script>
</body>

</html>