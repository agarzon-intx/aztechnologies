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
				ByeWeekPointsGoals
			FROM $schema.Configuration
			where id = 0;";
	$result2 = $Config->query($sql2);
	if ($result2->num_rows > 0) {
	// output data of each row
		while($row2 = $result2->fetch_assoc()) {
			$AvisosTemplete = $row2["AvisosTemplete"];
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
								<h2>' . $lang['451'] . '</h2>
								<div style="height: 100%;">
									<textarea cols="80" rows="20" id="editor2" name="editor2">' . $Config->LoadAvisoTemplate() . '</textarea>
								</div>
								<script>
									CKEDITOR.replace(\'editor2\', {
									  width: \'auto\',
									  height: \'293px\',
									  language: \'' . $_COOKIE[$Config->getAlias() . "language"] . '\',
									  extraPlugins: \'autogrow\',
									  autoGrow_minHeight: 293,
									  autoGrow_maxHeight: 293,
									  autoGrow_bottomSpace: 50,
									  removePlugins: \'resize\'
									});
								</script>
							</div>
						</div>
						<div class="row">
							<div class="col-xl-12" >
								<button type="button" class="btn btn-primary" onClick="validateAlert()" >' . $lang['0000'] . '</button>
							</div>
						</div>
					</div>';
?>