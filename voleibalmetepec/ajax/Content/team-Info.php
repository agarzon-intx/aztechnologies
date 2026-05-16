            <?php
				$fecha = new DateTime();
				$sql2 = "select j.*, b.Campo_DESC, Google, c.Categoria_Desc, concat(j.Torneo_ID,'-', j.Equipo_ID) newLogo
						from  $schema.Equipos as j 
							join $schema.Campos b on j.Campo_ID = b.Campo_ID
							join $schema.Categorias c on  j.Fuerza = c.Categoria_ID
						where j.Equipo_ID = $Team and j.Torneo_ID = $Season;";
				$result2 = $Config->query($sql2);

				$count = 0;
				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						$logoE = $row2["newLogo"];
						$fuerzaE = $row2["Categoria_Desc"];
						$nombreE = $row2["Equipo_FULLDESC"];
						$campoE = $row2["Campo_DESC"];
						$googleE = $row2["Google"];
						$playeraE = $row2["Playera"];
						$shortE = $row2["Short"];
						$calcetasE = $row2["Calcetas"];
					}
				} 
				$htmlTeam .= '<div class="container-fluid py-0">
								<div class="row">
									<div class="justify-content-center d-flex px-0 py-0 col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">' . $lang['354'] . '</div>
									<div class="justify-content-center d-flex px-0 py-0 col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6"><div class="d-none d-xs-none d-md-block d-lg-block d-xl-block" style="text-align: center;">' . $lang['355'] . '</div></div>
								</div>
								<div class="row">
									<div class="justify-content-center d-flex px-0 py-0 col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
										<div class="container-fluid py-0">
											<div class="row">
												<div class="justify-content-center d-flex "><img style=" background-color:' . $playeraE . ';" id="playeraA" src="imagenes/uniforme/playera.png" alt="Foto" width="100" height="100"/></div>
											</div>
											<div class="row">
												<div class="justify-content-center d-flex px-0 py-0 "><img style=" background-color:' . $shortE . ';" id="playeraA" src="imagenes/uniforme/short.png" alt="Foto" width="100" height="100"/></div>
											</div>
											<div class="row">
												<div class="justify-content-center d-flex px-0 py-0 "><img style=" background-color:' . $calcetasE . ';" id="playeraA" src="imagenes/uniforme/calcetas.png" alt="Foto" width="100" height="100"/></div>
											</div>
										</div>
									</div>
									<div class="justify-content-center d-flex px-0 py-0 col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
										<div class="d-none d-xs-none d-md-block d-lg-block d-xl-block" style="text-align: center;"><img id="fotoA" src="./imagenes/Original/' . $logoE . '.png?tmp=' . $fecha->getTimestamp() . '" alt="Foto" style="width: auto; height: 300px;" /></div>
									</div>
								</div>
								<div class="row">
									<div class="justify-content-center d-flex px-0 py-0 col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
										' . $lang['356'] . ' : ' . $fuerzaE . '
									</div>
									<div class="justify-content-center d-flex px-0 py-0 col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6 ">
										' . $lang['357'] . ' : <a href="' . $googleE . '" target="new">' . $campoE . '</a>
									</div>
								</div>';
				$htmlTeam .= '</div>';
				?>