<?php
	require_once __DIR__ . '/site_paths.php';
	session_start();
	//define('DEBUG', true);

	//error_reporting(0);
	//ini_set('display_errors', DEBUG ? '1' : '0');
	require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'load_membersite.php';
	$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('index.php');
	$Season = 0;
	$Category = 'null';
	$Language = 'null';
	//echo $Config->getAlias() . '<br>';
	//echo $_SESSION[$Config->getAlias() . "season"] . ', ' . $_SESSION[$Config->getAlias() . "category"] . ', ' . $_SESSION[$Config->getAlias() . 'language'] . '<br>';
	if(!isset($_COOKIE[$Config->getAlias() . "season"]) || $_COOKIE[$Config->getAlias() . "season"] === ''){
		$sql = "select max(Torneo_ID) Torneo_ID
				from $schema.Torneos
				where Actual = 'S'";
		$result = $Config->query($sql);
		if ($result->num_rows > 0) {
				// output data of each row
				while($row2 = $result->fetch_assoc()) {
						setcookie($Config->getAlias() . "season",$row2["Torneo_ID"],0,'/');
						$Season = $row2["Torneo_ID"];
				}
		}
	}else{
			$Season = $_COOKIE[$Config->getAlias() . "season"];
	}
	
	
	if(!isset($_COOKIE[$Config->getAlias() . "category"]) || $_COOKIE[$Config->getAlias() . "category"] === ''){
			$sql = "select Categoria_ID
					from $schema.Categorias
					where Categoria_ID in ( select Fuerza
											from $schema.Equipos
											where Torneo_ID = $Season)
					order by Categoria_Orden asc
					limit 1;";
			//echo $sql;
			$result = $Config->query($sql);
			if ($result->num_rows > 0) {
					// output data of each row
					while($row2 = $result->fetch_assoc()) {
							setcookie($Config->getAlias() . "category",$row2["Categoria_ID"],0,'/');
							$Category = $row2["Categoria_ID"];
					}

			}
	}else{
			$Category = $_COOKIE[$Config->getAlias() . "category"];
	}

	if(!isset($_COOKIE[$Config->getAlias() . "language"]) || $_COOKIE[$Config->getAlias() . "language"] === ''){
			$Config->LoadLanguage();
			setcookie($Config->getAlias() . "language",$Config->lan,0,'/');
			$Language = $Config->lan;
	}else{
			$Language = $_COOKIE[$Config->getAlias() . "language"];
	}

	$date = new DateTime();
	include('./languages/lang.' . $Language . '.php');

	$Config->LoadLogo();
	$Config->LoadFlags();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta name="google" content="notranslate">
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="apple-touch-icon" sizes="76x76" href="imagenes/<?php echo $Config->logo;?>.png">
		<link rel="icon" type="image/png" href="imagenes/<?php echo $Config->logo;?>.png">
		<title><?php echo $Config->liga;?></title>
		<!--     Fonts and icons     -->
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900">
		<!--<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />-->
		<!-- Nucleo Icons -->
		<link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
		<link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
		
		<!-- Font Awesome Icons -->
		<script src="https://kit.fontawesome.com/0ae04dd81a.js" crossorigin="anonymous"></script>
		
		<!-- Material Icons -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		
		<!-- CSS Files -->
		<link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
		
		<script src="assets/js/core/jquery.min.js" type="text/javascript"></script>
		
		<link rel="stylesheet" type="text/css" href="css/login/fg_membersite.css">
		<link rel="stylesheet" type="text/css" href="css/main.css">
		<link rel="stylesheet" type="text/css" href="css/login/pwdwidget.css">
		<link rel="stylesheet" type="text/css" href="css/farbtastic.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		
		<script src="javascript/rgraph/RGraph.common.core.js"></script>
		<script src="javascript/rgraph/RGraph.common.dynamic.js"></script>   <!-- Just needed for dynamic features -->
		<script src="javascript/rgraph/RGraph.common.annotate.js"></script>  <!-- Just needed for annotating -->
		<script src="javascript/rgraph/RGraph.common.context.js"></script>   <!-- Just needed for context menus -->
		<script src="javascript/rgraph/RGraph.common.effects.js"></script>   <!-- Just needed for visual effects -->
		<script src="javascript/rgraph/RGraph.common.key.js"></script>       <!-- Just needed for keys -->
		<script src="javascript/rgraph/RGraph.common.resizing.js"></script>  <!-- Just needed for resizing -->
		<script src="javascript/rgraph/RGraph.common.tooltips.js"></script>  <!-- Just needed for tooltips -->
		<script src="javascript/rgraph/RGraph.common.zoom.js"></script>      <!-- Just needed for zoom -->
		<script src="javascript/rgraph/RGraph.svg.common.core.js"></script>
		<script src="javascript/rgraph/RGraph.svg.common.ajax.js"></script>
		<script src="javascript/rgraph/RGraph.svg.bar.js"></script>   
		<script src="javascript/rgraph/RGraph.bar.js"></script>              <!-- Just needed for Bar charts -->
		<script src="javascript/rgraph/RGraph.hbar.js"></script>             <!-- Just needed for Horizontal Bar charts -->
		<script src="javascript/rgraph/RGraph.line.js"></script>             <!-- Just needed for Line charts -->
		<script src="javascript/rgraph/RGraph.drawing.xaxis.js"></script>
		<script src="javascript/rgraph/RGraph.drawing.yaxis.js"></script>
		
		<script src="javascript/login/pbkdf2.js"></script>
		<script src="javascript/login/enc-utf16-min.js"></script>
		<script src="javascript/login/ajax.js"></script>
		<script src="javascript/login/pwdwidget.js" type="text/javascript"></script>
		<script src="javascript/login/gen_validatorv31.js" type="text/javascript"></script>
		<script src="javascript/jquery.form.js" type="text/javascript"></script>
		
		
		<script src="javascript/configAdmin.js.php" type="text/javascript"></script>
		<script src="javascript/color.js.php" type="text/javascript"></script>
		<script src="javascript/farbtastic.js" type="text/javascript"></script>
		<script src="javascript/alta.js.php" type="text/javascript"></script>
		<script src="javascript/curp.js" type="text/javascript"></script>
		<script src="javascript/torneo.js.php" type="text/javascript"></script>
		<script src="javascript/categoria.js.php" type="text/javascript"></script>
		<script src="javascript/moment.js" type="text/javascript"></script>
		<script src="javascript/week.js.php" type="text/javascript"></script>
		<script src="javascript/campo.js.php" type="text/javascript"></script>
		<script src="javascript/equipo.js.php" type="text/javascript"></script>
		<script src="javascript/avisos.js.php" type="text/javascript"></script>
		<script src="javascript/minutasAdmin.js.php" type="text/javascript"></script>
		<script src="javascript/jornadaAdmin.js.php" type="text/javascript"></script>
		<script src="javascript/calendario.js.php" type="text/javascript"></script>
		<script src="javascript/usuarios.js.php" type="text/javascript"></script>
		<script src="javascript/arbitroValidateAdmin.js.php" type="text/javascript"></script>
			
		<!--
			
			<script src="javascript/jornadaAdmin.js.php" type="text/javascript"></script>
			-->
		<script src="javascript/ckeditor/ckeditor.js"></script>
		<style>
			.table-name-text {
			  font-size: 1rem;
			}

			@include media-breakpoint-up(sm) {
			  .table-name-text {
				font-size: 1.2rem;
			  }
			}

			@include media-breakpoint-up(md) {
			  .table-name-text {
				font-size: 1.4rem;
			  }
			}

			@include media-breakpoint-up(lg) {
			  .table-name-text {
				font-size: 1.6rem;
			  }
			}
			
			@media (min-width: 1200px) {
				.margin-left{
					margin-left: -8.125rem;
					padding-left: 8.125rem;
					padding-right: 9.125rem;
				}
			}
		</style>
	</head>
	<body class="g-sidenav-show bg-gray-100">
		<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
			<div class="sidenav-header">
				<i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
				<a class="navbar-brand m-0">
				<img title="Home" src="imagenes/<?php echo $Config->logo;?>.png" width="70"  alt="" onclick="loadWeeks(); toggleSidenav();" style="cursor: pointer;margin-left: 60px;max-height: 70px;">
				
				</a>
			</div>
			<hr class="horizontal light mt-0 mb-2">
			<div class="collapse navbar-collapse  w-auto h-auto max-height-vh-100 h-100" id="sidenav-collapse-main">
			</div>
		</aside>
		<main class="main-content border-radius-lg ">
			<!-- Navbar -->
			<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl d-none d-lg-none d-xl-block" id="navbarBlur" data-scroll="true">
				<div class="container-fluid py-1 px-3">
					<div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
						<div class="ms-md-auto pe-md-3 align-items-cente" id="teamLogos" style="padding-right: 0px !important;">
						</div>
						
					</div>
				</div>
			</nav>
			<!-- End Navbar -->
			<nav class="navbar navbar-main navbar-expand-sm px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
				<div class="container-fluid py-1 px-3" >
					<div class="d-none d-lg-block d-xl-block d-md-none"><?php echo $lang['0']; ?>: </div>
					<div class="dropdown btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $lang['0']; ?>" id="seasonsel">
					</div>
				</div>
				<div class="container-fluid py-1 px-3">
					<div class="d-none d-lg-block d-xl-block d-md-none"><?php echo $lang['1']; ?>: </div>
					<div class="dropdown btn-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $lang['1']; ?>" id="categorysel">
					</div>
					<ul class="navbar-nav  justify-content-end">
						<li class="nav-item d-xl-none ps-3 d-flex align-items-center">
							<a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
								<div class="sidenav-toggler-inner">
									<i class="sidenav-toggler-line"></i>
									<i class="sidenav-toggler-line"></i>
									<i class="sidenav-toggler-line"></i>
								</div>
							</a>
						</li>
						<li class="nav-item dropdown pe-2" id="notificationssec">
							
						</li>
					</ul>
				</div>
			</nav>
			<hr class="horizontal dark" style="margin: 0; color: black; height: 5px;">
			<div class="container-fluid py-0" id="body" style="padding-left: 0; padding-right: 0;">
			</div>
			<footer class="footer">
				<div class="container-fluid">
				  <div class="row align-items-center justify-content-lg-between">
					<div class="col-lg-12 mb-lg-0 mb-4">
					  <div class="copyright text-center text-sm text-muted text-lg-start">
						© <script>
						  document.write(new Date().getFullYear())
						</script>, <a href="https://www.aztechnologies.tech" class="font-weight-bold" target="_blank">aztechnologies.tech</a>
						<img src="imagenes/aztechnologies.png" width="126" style="margin-left: 20px;">
						<img src="imagenes/ws.png" width="30" style="margin-left: 20px;">
						<!--<img src="imagenes/FMVB.png" width="30" style="margin-left: 20px;">-->
						<!--<img src="imagenes/AMVB.png" width="30" style="margin-left: 20px;">-->
						<img src="imagenes/<?php echo $Config->logo;?>.png" width="40" style="margin-left: 20px;">
					  </div>
					</div>
				  </div>
				</div>
			</footer>
            <div id="alertaContent" class="tabla" style="z-index: -1; position: fixed; inset: 0px; background: rgba(179, 177, 177, 0.6); margin: auto; display:none;">
            </div>
		</main>

		<!--   Core JS Files   -->
		<script src="./assets/js/core/popper.min.js" ></script>
		<script src="./assets/js/core/bootstrap.min.js" ></script>
                <!--<script src="./assets/js/core/bootstrap.bundle.min.js"></script>-->
		<script src="./assets/js/plugins/perfect-scrollbar.min.js" ></script>
		<script src="./assets/js/plugins/smooth-scrollbar.min.js" ></script>
		<!-- Kanban scripts -->
		<script src="./assets/js/plugins/dragula/dragula.min.js"></script>
		<script src="./assets/js/plugins/jkanban/jkanban.min.js"></script>
		<script>
			var win = navigator.platform.indexOf('Win') > -1;
			if (win && document.querySelector('#sidenav-scrollbar')) {
				var options = {
					damping: '0.5'
				}
				Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
			}
		</script>
		<!-- Github buttons -->
		<script async defer src="https://buttons.github.io/buttons.js"></script>
		<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
		<script src="./assets/js/material-dashboard.js?v=3.1.0"></script>
		<script src="../assets/js/plugins/sweetalert.min.js"></script>
		<script src="../assets/js/plugins/nouislider.min.js"></script>
		<!--<script src="./assets/js/material-dashboard.js"></script>-->
		<script src="./javascript/main.js.php" type="text/javascript"></script>
		<script src="./javascript/mainVoleibol.js.php" type="text/javascript"></script>
                <script src="./javascript/mainBasket.js.php" type="text/javascript"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYiDvsZGN5SeQjZIuwO1KwyW6BTkyuNBc&loading=async" type="text/javascript"></script>
		<script>
			window.onload = function() {
				loadTournament(<?php echo $Season; ?>); 
				loadMenu();
				reloadNotifications();
				nIntervAlert = setInterval(reloadNotifications, 300000);
				nIntervId = setInterval(checkSessionExpire, 1800000);
			};
			
			<?php
			$sql = "SELECT Aviso_ID 
			        FROM $schema.Avisos
                    where (now() between Aviso_Fecha_Inicio and Aviso_Fecha_Fin) 
                        and Aviso_Estatus = 1 
                        and Aviso_Mostrar = 1;";
    		$result = $Config->query($sql);
    		if ($result->num_rows > 0) {
    				// output data of each row
    				while($row2 = $result->fetch_assoc()) {
    						echo "loadAlert(" . $row2["Aviso_ID"] . ");";
    				}
    		}
    		?>
		</script>
		<div id="mainLoading" style="z-index: -1;display: none;margin: auto;width: 100%;height: 100%;background: rgba(179, 177, 177, 0.6); position: fixed; inset: 0px;">
    		<div class="spinner-border text-info" role="status" style="left: 52%; position: absolute; top: 52%;" >
              <span class="sr-only">Loading...</span>
            </div>
        </div>
	</body>
</html>
