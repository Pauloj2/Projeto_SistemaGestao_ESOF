<?php 
session_start();
include('../controllers/verificar_login.php');
include('../config/conexao.php');

if($_SESSION['cargo_usuario'] != 'Funcionario' && $_SESSION['cargo_usuario'] != 'Gerente' && $_SESSION['cargo_usuario'] != 'Administrador'){
    header('Location: ../index.php');
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
  <title>Painel Funcionário - EletroService</title>

  <style>
    :root {
      --primary-color: #1ED760;
      --dark-bg: #2c3e50;
      --sidebar-bg: #043d2f;
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

    .custom-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      border: none;
    }

    .custom-card:hover {
      transform: translateY(-5px);
    }

    .card-icon-wrapper {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .icon-warning {
      background-color: #fff3cd;
      color: #856404;
    }

    .icon-success {
      background-color: #d1edff;
      color: #0c63e4;
    }

    .icon-danger {
      background-color: #f8d7da;
      color: #721c24;
    }

    .icon-primary {
      background-color: #cce5ff;
      color: #004085;
    }

    .card-category {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 5px;
      text-transform: uppercase;
      font-weight: 500;
    }

    .card-value {
      font-size: 1.8rem;
      font-weight: bold;
      color: #333;
      margin: 0;
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
            <a class="nav-link" href="../controllers/clientes.php">
              <i class="bi bi-people"></i>
              Clientes
            </a>
            <a class="nav-link" href="../controllers/abrir_orcamentos.php">
              <i class="bi bi-cash-stack"></i>
              Abrir Orçamento
            </a>
            <a class="nav-link" href="../controllers/fechar_orcamentos.php">
              <i class="bi bi-box"></i>
              Fechar Orçamento
            </a>
            <a class="nav-link" href="../controllers/rel_orcamentos.php">
              <i class="bi bi-file-earmark-text"></i>
              Relatório Orçamento
            </a>
            <a class="nav-link" href="../controllers/os_abertas.php">
              <i class="bi bi-bar-chart"></i>
              Ordens de Serviço Abertas
            </a>
            <a class="nav-link" href="../controllers/consultar_os.php">
              <i class="bi bi-question-circle"></i>
              Consultar Ordens de Serviço
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
                <h2 class="mb-0 me-2">Funcionário(a)</h2>
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
                    <li><a class="dropdown-item" href="../views/painel_tesouraria.php">
                        <i class="bi bi-cash me-2"></i>Painel Financeiro
                      </a></li>
                    <li><a class="dropdown-item" href="../views/painel_admin.php">
                        <i class="bi bi-shield-check me-2"></i>Painel Administrador
                      </a></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                  <?php endif; ?>
                  <li><a class="dropdown-item text-danger" href="../index.php">
                      <i class="bi bi-box-arrow-right me-2"></i>Sair
                    </a></li>
                </ul>
              </div>
            </div>
          </header>

          <!-- Page Content -->
          <main class="p-4">
            <!-- Dashboard Cards -->
            <div class="row g-4 mb-4">
              <!-- Orçamentos Abertos -->
              <div class="col-lg-3 col-md-6">
                <div class="card custom-card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-4">
                        <div class="card-icon-wrapper icon-warning">
                          <i class="bi bi-file-earmark-plus"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <p class="card-category mb-1">Orçamentos Abertos</p>
                        <?php
                        // Consulta para orçamentos abertos (assumindo que existe uma tabela 'orcamentos' com status)
                        $query_orc_abertos = "SELECT COUNT(*) as total FROM orcamentos WHERE status = 'Aberto' OR status = 'Pendente'";
                        $result_orc_abertos = mysqli_query($conexao, $query_orc_abertos);
                        $orc_abertos = mysqli_fetch_array($result_orc_abertos);
                        ?>
                        <h4 class="card-value"><?php echo $orc_abertos['total']; ?></h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <small><i class="bi bi-clock me-1"></i> Aguardando aprovação</small>
                  </div>
                </div>
              </div>

              <!-- Orçamentos Fechados -->
              <div class="col-lg-3 col-md-6">
                <div class="card custom-card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-4">
                        <div class="card-icon-wrapper icon-success">
                          <i class="bi bi-file-earmark-check"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <p class="card-category mb-1">Orç Fechados</p>
                        <?php
                        // Consulta para orçamentos fechados
                        $query_orc_fechados = "SELECT COUNT(*) as total FROM orcamentos WHERE status = 'Fechado' OR status = 'Aprovado'";
                        $result_orc_fechados = mysqli_query($conexao, $query_orc_fechados);
                        $orc_fechados = mysqli_fetch_array($result_orc_fechados);
                        ?>
                        <h4 class="card-value"><?php echo $orc_fechados['total']; ?></h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <small><i class="bi bi-check-circle me-1"></i> Concluídos</small>
                  </div>
                </div>
              </div>

              <!-- OS Abertas -->
              <div class="col-lg-3 col-md-6">
                <div class="card custom-card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-4">
                        <div class="card-icon-wrapper icon-primary">
                          <i class="bi bi-wrench"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <p class="card-category mb-1">OS Abertas</p>
                        <?php
                        // Consulta para ordens de serviço abertas (assumindo que existe uma tabela 'ordem_servico' ou 'os')
                        $query_os_abertas = "SELECT COUNT(*) as total FROM os WHERE status = 'Aberta' OR status = 'Em Andamento'";
                        $result_os_abertas = mysqli_query($conexao, $query_os_abertas);
                        $os_abertas = mysqli_fetch_array($result_os_abertas);
                        ?>
                        <h4 class="card-value"><?php echo $os_abertas['total']; ?></h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <small><i class="bi bi-gear me-1"></i> Em andamento</small>
                  </div>
                </div>
              </div>

              <!-- Total de Clientes -->
              <div class="col-lg-3 col-md-6">
                <div class="card custom-card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-4">
                        <div class="card-icon-wrapper icon-danger">
                          <i class="bi bi-people"></i>
                        </div>
                      </div>
                      <div class="col-8">
                        <p class="card-category mb-1">Total Clientes</p>
                        <?php
                        // Consulta para total de clientes
                        $query_clientes = "SELECT COUNT(*) as total FROM clientes";
                        $result_clientes = mysqli_query($conexao, $query_clientes);
                        $total_clientes = mysqli_fetch_array($result_clientes);
                        ?>
                        <h4 class="card-value"><?php echo $total_clientes['total']; ?></h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent border-top">
                    <small><i class="bi bi-person-plus me-1"></i> Cadastrados</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Content Area -->
            <div class="row">
              <div class="col-12">
                <div class="card custom-card">
                  <div class="card-header bg-transparent">
                    <h5 class="mb-0">Dashboard do Funcionário</h5>
                  </div>
                  <div class="card-body">
                    <p class="text-muted">Bem-vindo ao painel do funcionário. Utilize o menu lateral para navegar entre as diferentes funcionalidades do sistema.</p>
                    
                    <div class="row g-3 mt-3">
                      <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                          <i class="bi bi-people fs-1 text-primary mb-2"></i>
                          <h6>Gerenciar Clientes</h6>
                          <p class="small text-muted">Cadastre e gerencie informações dos clientes</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                          <i class="bi bi-cash-stack fs-1 text-success mb-2"></i>
                          <h6>Orçamentos</h6>
                          <p class="small text-muted">Crie e gerencie orçamentos para os clientes</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                          <i class="bi bi-bar-chart fs-1 text-warning mb-2"></i>
                          <h6>Ordens de Serviço</h6>
                          <p class="small text-muted">Acompanhe o status das OS em andamento</p>
                        </div>
                      </div>
                    </div>
                  </div>
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