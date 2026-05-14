<?php
	$sql2 = "SELECT Logo,
				LogoX,
				LogoY,
				ColorHeader,
				ColorBody,
				ColorFooter,
				TorneoCopaLiga,
				Categorias,
				EmpatesPenales,
				JugadorJugado,
				JuegoCedulas,
				MarcadorArbitro,
				MarcadorFecha,
				MarcadorDiaDefault,
				AvisosTemplete,
				Idioma,
				id,
				JornadaCedulas,
				LeagueName,
				ShowIDColumn,
				Latitude,
				Longitude, 
				ByeWeekPoints,
				ByeWeekPointsGoals,
				UnJuegoSemana,
				TresSets,
				PerfilJugadores,
				JugadoresApellidos1,
				JuegosxNombre,
			    CoachJuegos,
			    CoachJuegosDiaInicial,
			    CoachJuegosDiaFinal,
			    TIME_FORMAT(MarcadorHoraDefault, '%H:%i') horario,
			    TIME_FORMAT(CoachJuegosHoraFinal, '%H:%i') CoachJuegosHoraFinal,
			    TarjetaCambios,
			    VollByeWeekSets,
			    VollByeWeekPoints,
			    VollByeWeekSetPoints
			FROM $schema.Configuration
			where id = 0;";
	$result2 = $Config->query($sql2);
	if ($result2->num_rows > 0) {
	// output data of each row
		while($row2 = $result2->fetch_assoc()) {
			$logo = $row2["Logo"]; 
				$TorneoCopaLiga = $row2["TorneoCopaLiga"];
				$TorneoCopaLigaN = '';
				$TorneoCopaLigaC = '';
				$TorneoCopaLigaL = '';
				if($TorneoCopaLiga == 'N') $TorneoCopaLigaN = 'checked';
				if($TorneoCopaLiga == 'C') $TorneoCopaLigaC = 'checked';
				if($TorneoCopaLiga == 'L') $TorneoCopaLigaL = 'checked';
				$Categorias = $row2["Categorias"]; 
				$CategoriasCHK = ''; 
				if($Categorias == '1') $CategoriasCHK = 'checked'; 
				$EmpatesPenales = $row2["EmpatesPenales"]; 
				$EmpatesPenalesCHK = ''; 
				if($EmpatesPenales == '1') $EmpatesPenalesCHK = 'checked'; 
				$JugadorJugado = $row2["JugadorJugado"]; 
				$JugadorJugadoCHK = ''; 
				if($JugadorJugado == '1') $JugadorJugadoCHK = 'checked';
				$JuegoCedulas = $row2["JuegoCedulas"];
				$JuegoCedulasCHK = ''; 
				if($JuegoCedulas == '1') $JuegoCedulasCHK = 'checked'; 
				$MarcadorArbitro = $row2["MarcadorArbitro"]; 
				$MarcadorArbitroCHK = ''; 
				if($MarcadorArbitro == '1') $MarcadorArbitroCHK = 'checked';
				$MarcadorFecha = $row2["MarcadorFecha"]; 
				$MarcadorFechaCHK = ''; 
				if($MarcadorFecha == '1') $MarcadorFechaCHK = 'checked'; 
				$MarcadorDiaDefault = $row2["MarcadorDiaDefault"]; 
				$MarcadorDiaDefault1 = ''; 
				$MarcadorDiaDefault2 = ''; 
				$MarcadorDiaDefault3 = ''; 
				$MarcadorDiaDefault4 = ''; 
				$MarcadorDiaDefault5 = ''; 
				$MarcadorDiaDefault6 = ''; 
				$MarcadorDiaDefault7 = ''; 
				if($MarcadorDiaDefault == '1') $MarcadorDiaDefault1 = 'selected';
				if($MarcadorDiaDefault == '2') $MarcadorDiaDefault2 = 'selected';
				if($MarcadorDiaDefault == '3') $MarcadorDiaDefault3 = 'selected';
				if($MarcadorDiaDefault == '4') $MarcadorDiaDefault4 = 'selected';
				if($MarcadorDiaDefault == '5') $MarcadorDiaDefault5 = 'selected';
				if($MarcadorDiaDefault == '6') $MarcadorDiaDefault6 = 'selected';
				if($MarcadorDiaDefault == '7') $MarcadorDiaDefault7 = 'selected';
				$Idioma = $row2["Idioma"]; 
				$JornadaCedulas = $row2["JornadaCedulas"];
				$JornadaCedulasCHK = '';
				if($JornadaCedulas == '1') $JornadaCedulasCHK = 'checked';
				$columnid = $row2["ShowIDColumn"];
				$columnidCHK = '';
				if($columnid == '1') $columnidCHK = 'checked';
				$ByeWeekPoints = $row2["ByeWeekPoints"];
				$ByeWeekPointsCHK = '';
				$ByeWeekPointsDPL = 'display: none;';
				if($ByeWeekPoints == '1'){ $ByeWeekPointsCHK = 'checked'; $ByeWeekPointsDPL = 'display: block;'; }
				
				$ByeWeekPointsGoals = $row2["ByeWeekPointsGoals"];
				$tresSets = $row2["TresSets"];
                $tresSetsCHK = '';
                if($tresSets == '1') $tresSetsCHK = 'checked';
				$unjuegosemanal = $row2["UnJuegoSemana"];
                $unjuegosemanalCHK = '';
                if($unjuegosemanal == '1') $unjuegosemanalCHK = 'checked';
                $perfilJugador = $row2["PerfilJugadores"];
                $perfilJugadorCHK = '';
                if($perfilJugador == '1') $perfilJugadorCHK = 'checked';
                $jugadoresApellidos1 = $row2["JugadoresApellidos1"];
                $jugadoresApellidos1CHK = '';
                if($jugadoresApellidos1 == '1') $jugadoresApellidos1CHK = 'checked';
                $juegosxnombre = $row2["JuegosxNombre"];
                $juegosxnombreCHK = '';
                if($juegosxnombre == '1') $juegosxnombreCHK = 'checked';
                $coachjuegos = $row2["CoachJuegos"];
                $coachjuegosCHK = '';
				$coachjuegosDPL = 'display: none;';
                if($coachjuegos == '1'){ $coachjuegosCHK = 'checked'; $coachjuegosDPL = 'display: block;'; }
                $coachjuegosdiainicial = $row2["CoachJuegosDiaInicial"];
				$coachjuegosdiainicial1 = ''; 
				$coachjuegosdiainicial2 = ''; 
				$coachjuegosdiainicial3 = ''; 
				$coachjuegosdiainicial4 = ''; 
				$coachjuegosdiainicial5 = ''; 
				$coachjuegosdiainicial6 = ''; 
				$coachjuegosdiainicial7 = ''; 
				if($coachjuegosdiainicial == '1') $coachjuegosdiainicial1 = 'selected';
				if($coachjuegosdiainicial == '2') $coachjuegosdiainicial2 = 'selected';
				if($coachjuegosdiainicial == '3') $coachjuegosdiainicial3 = 'selected';
				if($coachjuegosdiainicial == '4') $coachjuegosdiainicial4 = 'selected';
				if($coachjuegosdiainicial == '5') $coachjuegosdiainicial5 = 'selected';
				if($coachjuegosdiainicial == '6') $coachjuegosdiainicial6 = 'selected';
				if($coachjuegosdiainicial == '7') $coachjuegosdiainicial7 = 'selected';
                $coachjuegosdiafinal = $row2["CoachJuegosDiaFinal"];
				$coachjuegosdiafinal1 = ''; 
				$coachjuegosdiafinal2 = ''; 
				$coachjuegosdiafinal3 = ''; 
				$coachjuegosdiafinal4 = ''; 
				$coachjuegosdiafinal5 = ''; 
				$coachjuegosdiafinal6 = ''; 
				$coachjuegosdiafinal7 = ''; 
				if($coachjuegosdiafinal == '1') $coachjuegosdiafinal1 = 'selected';
				if($coachjuegosdiafinal == '2') $coachjuegosdiafinal2 = 'selected';
				if($coachjuegosdiafinal == '3') $coachjuegosdiafinal3 = 'selected';
				if($coachjuegosdiafinal == '4') $coachjuegosdiafinal4 = 'selected';
				if($coachjuegosdiafinal == '5') $coachjuegosdiafinal5 = 'selected';
				if($coachjuegosdiafinal == '6') $coachjuegosdiafinal6 = 'selected';
				if($coachjuegosdiafinal == '7') $coachjuegosdiafinal7 = 'selected';
				$horario = $row2["horario"]; 
				$coachjuegoshorafinal = $row2["CoachJuegosHoraFinal"]; 
				$tarjetaCambios = $row2["TarjetaCambios"];
				$tarjetaCambiosCHK = '';
				if($tarjetaCambios == '1') $tarjetaCambiosCHK = 'checked';
				
			    $VollByeWeekSets = $row2["VollByeWeekSets"];
			    $VollByeWeekPoints = $row2["VollByeWeekPoints"];
			    $VollByeWeekSetPoints = $row2["VollByeWeekSetPoints"];
				$VollByeWeekPointsCHK = '';
				$VollByeWeekPointsDPL = 'display: none;';
				if($VollByeWeekSets != '0'){ $VollByeWeekPointsCHK = 'checked'; $VollByeWeekPointsDPL = 'display: block;'; }
		}
	} 

	$htmlConfig .= '<div class="container-fluid py-2">
						<div class="row">
							<div class="col-xl-12">
								<Div id="error2"></Div>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12" >
								<div class="row">
									<div class="input-group input-group-static mb-4">
										<label for="lenguaje" class="ms-0">' . $lang['453'] . '</label>
										<select class="form-control" id="lenguaje">';
	$sql = "SELECT * FROM $schema.Lenguaje
			order by 4 asc;";
	$result = $Config->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		$selected = false;
		while($row2 = $result->fetch_assoc()) {
			if($row2["Lenguaje_ID"] == $Idioma){
				$htmlConfig .= "<option value='" . $row2["Lenguaje_ID"] . "' selected>" . $row2["Lenguaje_DESC"] . "</option>";
			}else{
				$htmlConfig .= "<option value='" . $row2["Lenguaje_ID"] . "'>" . $row2["Lenguaje_DESC"] . "</option>";
			}
		}
	}
	$htmlConfig .= '					</select>
									</div>
								</div>
								<div class="row">
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="EmpatesPenales" id="EmpatesPenales" ' . $EmpatesPenalesCHK . '>
										<label class="custom-control-label" for="EmpatesPenales">' . $lang['456'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="JugadorJugado" id="JugadorJugado" ' . $JugadorJugadoCHK . '>
										<label class="custom-control-label" for="JugadorJugado">' . $lang['457'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="JuegoCedulas" id="JuegoCedulas" ' . $JuegoCedulasCHK . '>
										<label class="custom-control-label" for="JuegoCedulas">' . $lang['458'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="MarcadorArbitro" id="MarcadorArbitro" ' . $MarcadorArbitroCHK . '>
										<label class="custom-control-label" for="MarcadorArbitro">' . $lang['459'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="MarcadorFecha" id="MarcadorFecha" ' . $MarcadorFechaCHK . '>
										<label class="custom-control-label" for="MarcadorFecha">' . $lang['460'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="JornadaCedulas" id="JornadaCedulas" ' . $JornadaCedulasCHK . '>
										<label class="custom-control-label" for="JornadaCedulas">' . $lang['473'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="columnid" id="columnid" ' . $columnidCHK . '>
										<label class="custom-control-label" for="columnid">' . $lang['476'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="tarjetaCambios" id="tarjetaCambios" ' . $tarjetaCambiosCHK . '>
										<label class="custom-control-label" for="tarjetaCambios">' . $lang['491'] . '</label>
									</div>
								</div>';
								$styleSP0 = 'style="display: none;"';
								if($Config->getSport() == 0){
								    $styleSP0 = '';
								}
								$htmlConfig .= '<div class="row" ' . $styleSP0 . '>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="ByeWeekPoints" id="ByeWeekPoints" ' . $ByeWeekPointsCHK . ' onclick="handleClick(this);">
										<label class="custom-control-label" for="ByeWeekPoints">' . $lang['479'] . '</label>
										<script>
											function handleClick(cb) {
												if(cb.checked){
													$(\'#byeWeek\').css(\'display\', \'block\');
												}else{
													$(\'#byeWeek\').css(\'display\', \'none\');
												}
											}
										</script>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="' . $ByeWeekPointsDPL. '" id="byeWeek">
										<div class="input-group input-group-static mb-4">
											<label>' . $lang['480'] . '</label>
											<input type="number" class="form-control" name="ByeWeekPointsGoals" id="ByeWeekPointsGoals" value="' . $ByeWeekPointsGoals . '" onkeypress="return isNumberKey(event)">
										</div>
									</div>
								</div>';
				
								$styleSP1 = 'style="display: none;"';
								if($Config->getSport() == 1){
								    $styleSP1 = '';
								}
								$htmlConfig .= '<div class="row" ' . $styleSP1 . '>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="VByeWeekPoints" id="VByeWeekPoints" ' . $VollByeWeekPointsCHK . ' onclick="handleClick(this);">
										<label class="custom-control-label" for="VByeWeekPoints">' . $lang['479'] . '</label>
										<script>
											function handleClick(cb) {
												if(cb.checked){
													$(\'#VBbyeWeekS\').css(\'display\', \'block\');
													$(\'#VBbyeWeekP\').css(\'display\', \'block\');
												}else{
													$(\'#VBbyeWeekS\').css(\'display\', \'none\');
													$(\'#VBbyeWeekP\').css(\'display\', \'none\');
												}
											}
										</script>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="' . $VollByeWeekPointsDPL. '" id="VBbyeWeekP">
										<div class="input-group input-group-static mb-4">
											<label>' . $lang['479'] . '</label>
											<input type="number" class="form-control" name="VBByeWeekPoints" id="VBByeWeekPoints" value="' . $VollByeWeekPoints . '" onkeypress="return isNumberKey(event)">
										</div>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="' . $VollByeWeekPointsDPL. '" id="VBbyeWeekS">
										<div class="input-group input-group-static mb-4">
											<label>' . $lang['479-1'] . '</label>
											<input type="number" class="form-control" name="VBByeWeekSets" id="VBByeWeekSets" value="' . $VollByeWeekSets . '" onkeypress="return isNumberKey(event)">
										</div>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="' . $VollByeWeekPointsDPL. '" id="VBbyeWeekSP">
										<div class="input-group input-group-static mb-4">
											<label>' . $lang['480-1'] . '</label>
											<input type="number" class="form-control" name="VBByeWeekSetPoints" id="VBByeWeekSetPoints" value="' . $VollByeWeekSetPoints . '" onkeypress="return isNumberKey(event)">
										</div>
									</div>
								</div>';
								
								$htmlConfig .= '<div class="row">';
									$style = 'style="display: none;"';
									if($Config->getSport() == 1){
									    $style = '';
									}
    $htmlConfig .= '                <div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" ' . $style . '>
										<input class="form-check-input" type="checkbox" name="tressets" id="tressets" ' . $tresSetsCHK . '>
										<label class="custom-control-label" for="tressets">' . $lang['482'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="juegoSemanal" id="juegoSemanal" ' . $unjuegosemanalCHK . '>
										<label class="custom-control-label" for="juegoSemanal">' . $lang['481'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="perfilJugador" id="perfilJugador" ' . $perfilJugadorCHK . '>
										<label class="custom-control-label" for="perfilJugador">' . $lang['483'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="jugadoresApellidos1" id="jugadoresApellidos1" ' . $jugadoresApellidos1CHK . '>
										<label class="custom-control-label" for="jugadoresApellidos1">' . $lang['484'] . '</label>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="juegosxnombre" id="juegosxnombre" ' . $juegosxnombreCHK . '>
										<label class="custom-control-label" for="juegosxnombre">' . $lang['485'] . '</label>
									</div>
								</div>
								<div class="row">
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<input class="form-check-input" type="checkbox" name="coachjuegos" id="coachjuegos" ' . $coachjuegosCHK . ' onclick="handleClick1(this);">
										<label class="custom-control-label" for="coachjuegos">' . $lang['486'] . '</label>
										<script>
											function handleClick1(cb) {
												if(cb.checked){
													$(\'#coachjuegos1\').css(\'display\', \'block\');
													$(\'#coachjuegos2\').css(\'display\', \'block\');
												}else{
													$(\'#coachjuegos1\').css(\'display\', \'none\');
													$(\'#coachjuegos2\').css(\'display\', \'none\');
												}
											}
										</script>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="' . $coachjuegosDPL. '" id="coachjuegos1">
										<div class="input-group input-group-static mb-4">
											<label for="coachjuegosdiainicial" class="ms-0">' . $lang['487'] . '</label>
										    <select class="form-control" id="coachjuegosdiainicial">';
                            	$htmlConfig .= "<option value='1' ' . $coachjuegosdiainicial1 . '>" . $lang["464"] . "</option>";
                            	$htmlConfig .= "<option value='2' ' . $coachjuegosdiainicial2 . '>" . $lang["465"] . "</option>";
                            	$htmlConfig .= "<option value='3' ' . $coachjuegosdiainicial3 . '>" . $lang["466"] . "</option>";
                            	$htmlConfig .= "<option value='4' ' . $coachjuegosdiainicial4 . '>" . $lang["467"] . "</option>";
                            	$htmlConfig .= "<option value='5' ' . $coachjuegosdiainicial5 . '>" . $lang["468"] . "</option>";
                            	$htmlConfig .= "<option value='6' ' . $coachjuegosdiainicial6 . '>" . $lang["469"] . "</option>";
                            	$htmlConfig .= "<option value='7' ' . $coachjuegosdiainicial7 . '>" . $lang["470"] . "</option>";
	$htmlConfig .= '					    </select>
										</div>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="' . $coachjuegosDPL. '" id="coachjuegos2">
										<div class="input-group input-group-static mb-4">
											<label for="coachjuegosdiafinal" class="ms-0">' . $lang['488'] . '</label>
									        <select class="form-control" id="coachjuegosdiafinal">';
                            	$htmlConfig .= "<option value='1' ' . $coachjuegosdiafinal1 . '>" . $lang["464"] . "</option>";
                            	$htmlConfig .= "<option value='2' ' . $coachjuegosdiafinal2 . '>" . $lang["465"] . "</option>";
                            	$htmlConfig .= "<option value='3' ' . $coachjuegosdiafinal3 . '>" . $lang["466"] . "</option>";
                            	$htmlConfig .= "<option value='4' ' . $coachjuegosdiafinal4 . '>" . $lang["467"] . "</option>";
                            	$htmlConfig .= "<option value='5' ' . $coachjuegosdiafinal5 . '>" . $lang["468"] . "</option>";
                            	$htmlConfig .= "<option value='6' ' . $coachjuegosdiafinal6 . '>" . $lang["469"] . "</option>";
                            	$htmlConfig .= "<option value='7' ' . $coachjuegosdiafinal7 . '>" . $lang["470"] . "</option>";
    	$htmlConfig .= '					</select>
										</div>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3" style="' . $coachjuegosDPL. '">
										<label for="MarcadorHoraDefault" class="ms-0">' . $lang['490'] . '</label>
										<input type="time" style="text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $coachjuegoshorafinal . '" name="coachjuegoshorafinal" id="coachjuegoshorafinal">
									</div>
								</div>
								<div class="row">
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<label for="MarcadorDiaDefault" class="ms-0">' . $lang['461'] . '</label>
										<select class="form-control" id="MarcadorDiaDefault">';
                        	$htmlConfig .= "<option value='1' ' . $MarcadorDiaDefault1 . '>" . $lang["464"] . "</option>";
                        	$htmlConfig .= "<option value='2' ' . $MarcadorDiaDefault2 . '>" . $lang["465"] . "</option>";
                        	$htmlConfig .= "<option value='3' ' . $MarcadorDiaDefault3 . '>" . $lang["466"] . "</option>";
                        	$htmlConfig .= "<option value='4' ' . $MarcadorDiaDefault4 . '>" . $lang["467"] . "</option>";
                        	$htmlConfig .= "<option value='5' ' . $MarcadorDiaDefault5 . '>" . $lang["468"] . "</option>";
                        	$htmlConfig .= "<option value='6' ' . $MarcadorDiaDefault6 . '>" . $lang["469"] . "</option>";
                        	$htmlConfig .= "<option value='7' ' . $MarcadorDiaDefault7 . '>" . $lang["470"] . "</option>";
                        	$htmlConfig .= '					</select>
									</div>
									<div class="form-check mb-2 col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 col-xxl-3">
										<label for="MarcadorHoraDefault" class="ms-0">' . $lang['489'] . '</label>
										<input type="time" style="text-align: center;padding-right: 0px !important;padding-left: 0px !important;" class="form-control form-control-sm" value="' . $horario . '" name="MarcadorHoraDefault" id="MarcadorHoraDefault">
									</div>
								</div>
							</div>
							<div class="col-xl-12" >
								<h6>' . $lang['472'] . '</h6>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12" >
								<button type="button" class="btn btn-primary" onClick="validateGeneral()" >' . $lang['0000'] . '</button>
							</div>
						</div>
					</div>';
?>