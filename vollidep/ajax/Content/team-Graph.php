            <?php
				$htmlTeam .= '<br>';
				$htmlTeam .= '<div style="overflow-x: auto">';
				$htmlTeam .= '<canvas id="puntos" style="margin: auto">[No canvas support]</canvas></div>';
				$htmlTeam .= '<script>
						
							var canvas = document.getElementById("puntos");
							RGraph.Reset(canvas);

						var text_size, y1_x;
						if($(window).width() > 600){
							// DESKTOP VERSION
							canvas.width  = $(window).width() * 1;
							canvas.style.width  = "90%";
							canvas.height = $(window).width() * 0.45;
							//console.log(canvas.height);
							text_size = Math.min(12, ($(window).width() / 1000) * 10 );
							y1_x = $(window).width() - 100;
						}
						else{
							// MOBILE VERSION
							canvas.width  = $(window).width() * 1.5;
							canvas.style.width  = "150%";
							canvas.height = $(window).width() * 0.75;
							console.log(canvas.height);
							text_size = Math.min(12, ($(window).width() / 1000) * 20 );
							y1_x = canvas.width - 100;
						}

						var linewidth = $(window).width() > 500 ? 2 : 1;
						linewidth = $(window).width() > 750 ? 3 : linewidth;
						canvas.__rgraph_aa_translated__ = false;';
						
						
				$data1out = '';
				$sql = "select distinct k.Jornada_DescCorta as Jornada,i.Pts,i.Reales,ifnull(i.PosGrupo, '0') PosGrupo,i.PosGeneral
												from $schema.Juegos as j
													left outer join $schema.Jornada as k on j.Fecha <= k.Fecha_Fin and j.Fecha >= k.Fecha_Inicio and j.Torneo_ID = $Season 
													left outer join $schema.Equipos as l on j.local_ID = l.Equipo_ID and l.Torneo_ID = $Season and j.Local_ID = $Team
													left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season and j.Visitante_ID = $Team
													left outer join $schema.Equipo_Stats i 
														on j.Torneo_ID = i.Torneo_ID and
															j.Jornada_ID = i.Jornada_ID and
															i.Equipo_ID = $Team
													join $schema.Equipo_Stats ik 
														on k.Torneo_ID = ik.Torneo_ID and
															k.Jornada_ID = ik.Jornada_ID
												where j.Jornada_ID <= (select max(Jornada_ID)-2 from $schema.Jornada where Torneo_ID = $Season) and k.Torneo_ID = $Season and j.Torneo_ID = $Season
												order by j.Torneo_ID, j.Jornada_ID;";
				//echo $sql;
				$result = $Config->query($sql); 

						
				$count = 0;
				if ($result->num_rows > 0) {
					// output data of each row
					while ($row = $result->fetch_assoc()) {
						if ($count == 0) {
							$data1out .= '' . $row["PosGrupo"] . '';
						} else {
							$data1out .= ', ' . $row["PosGrupo"] . '';
						}
						$count++;
					}
				}


				$htmlTeam .= 'var data1 = [' . $data1out . ' ];';

				$data2out = '';
				$sql = "select distinct k.Jornada_DescCorta as Jornada,i.Pts,i.Reales,ifnull(i.PosGrupo, 0) PosGrupo,ifnull(i.PosGeneral, 0) PosGeneral
												from $schema.Juegos as j
													left outer join $schema.Jornada as k on j.Fecha <= k.Fecha_Fin and j.Fecha >= k.Fecha_Inicio and j.Torneo_ID = $Season 
													left outer join $schema.Equipos as l on j.local_ID = l.Equipo_ID and l.Torneo_ID = $Season and j.Local_ID = $Team
													left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season and j.Visitante_ID = $Team
													left outer join $schema.Equipo_Stats i 
														on j.Torneo_ID = i.Torneo_ID and
															j.Jornada_ID = i.Jornada_ID and
															i.Equipo_ID = $Team
													join $schema.Equipo_Stats ik 
														on k.Torneo_ID = ik.Torneo_ID and
															k.Jornada_ID = ik.Jornada_ID
												where j.Jornada_ID <= (select max(Jornada_ID)-2 from $schema.Jornada where Torneo_ID = $Season) and k.Torneo_ID = $Season and j.Torneo_ID = $Season
												order by j.Torneo_ID, j.Jornada_ID;";
				//echo $sql;
				$result = $Config->query($sql); 

				$count = 0;
				if ($result->num_rows > 0) {
					// output data of each row
					while ($row = $result->fetch_assoc()) {
						if ($count == 0) {
							$data2out .= '' . $row["PosGeneral"] . '';
						} else {
							$data2out .= ', ' . $row["PosGeneral"] . '';
						}
						$count++;
					}
				}
						
				$htmlTeam .= "var data2 = [ " . $data2out . " ];";

				$data3out = '';
				$sql = "select distinct k.Jornada_DescCorta as Jornada,i.Pts,ifnull(i.Reales,0) Reales,i.PosGrupo,i.PosGeneral
												from $schema.Juegos as j
													left outer join $schema.Jornada as k on j.Fecha <= k.Fecha_Fin and j.Fecha >= k.Fecha_Inicio and j.Torneo_ID = $Season 
													left outer join $schema.Equipos as l on j.local_ID = l.Equipo_ID and l.Torneo_ID = $Season and j.Local_ID = $Team
													left outer join $schema.Equipos as v on j.Visitante_ID = v.Equipo_ID and v.Torneo_ID = $Season and j.Visitante_ID = $Team
													left outer join $schema.Equipo_Stats i 
														on j.Torneo_ID = i.Torneo_ID and
															j.Jornada_ID = i.Jornada_ID and
															i.Equipo_ID = $Team
													join $schema.Equipo_Stats ik 
														on k.Torneo_ID = ik.Torneo_ID and
															k.Jornada_ID = ik.Jornada_ID
												where j.Jornada_ID <= (select max(Jornada_ID)-2 from $schema.Jornada where Torneo_ID = $Season) and k.Torneo_ID = $Season and j.Torneo_ID = $Season
												order by j.Torneo_ID, j.Jornada_ID;";
				//echo $sql;
				$result = $Config->query($sql);
				$count = 0;
				if ($result->num_rows > 0) {
					// output data of each row
					while ($row = $result->fetch_assoc()) {
						if ($count == 0) {
							$data3out .= '' . $row["Reales"] . '';
						} else {
							$data3out .= ', ' . $row["Reales"] . '';
						}
						$count++;
					}
				}
				$htmlTeam .= "var data3 = [ " . $data3out . " ];";

				$labelsout = '';
				// Create connection
				$sql = "select distinct jk.Jornada_DescCorta as Jornada, case when k.Jornada_ID is null then 0 else 1 end as Activo
						from $schema.Jornada as jk  
							left outer join $schema.Juegos as j on j.Fecha <= jk.Fecha_Fin and j.Fecha >= jk.Fecha_Inicio and jk.Torneo_ID = $Season
							left outer join (select ifnull((SELECT 
											  Jornada_ID
											FROM 
											  $schema.Jornada
											where Torneo_ID = $Season and Fecha >= DATE_ADD(date(now()) , INTERVAL-3 DAY)
											LIMIT 1), (select max(Jornada_ID) from $schema.Jornada where Torneo_ID = $Season)) Jornada_ID) k on jk.Jornada_ID = k.Jornada_ID
							join $schema.Equipo_Stats i 
												on jk.Torneo_ID = i.Torneo_ID and
													jk.Jornada_ID = i.Jornada_ID
						where jk.Jornada_ID <= (select max(Jornada_ID)-2 from $schema.Jornada where Torneo_ID = $Season) and jk.Torneo_ID = $Season
						order by jk.Jornada_ID;";
						//echo $sql;
				$result = $Config->query($sql); 

				$count = 0;
				if ($result->num_rows > 0) {
					// output data of each row
					while ($row = $result->fetch_assoc()) {
						if ($count == 0) {
							$labelsout .= "'" . $row["Jornada"] . "'";
						} else {
							$labelsout .= ", '" . $row["Jornada"] . "'";
						}
						$count++;
					}
				}
				$htmlTeam .= "var labels = [ " . $labelsout . " ];";
						
				$htmlTeam .= "var bar = new RGraph.Bar({ 
							id: 'puntos', 
							data: data3, 
							options: { 
								key: ['" . $lang['370'] . "', '" . $lang['383'] . "'],
								backgroundGrid: false, keyPosition: 'gutter', keyTextSize: text_size, titleVpos: 0.5,
								colors: ['gradient(white:#0b2a61)','red'], 
								strokestyle: 'rgba(0,0,0,0)', 
								combinedchartEffect: 'wave', 
								noaxes: true, 
								ylabels: false, 
								backgroundGridVlines: false, 
								backgroundGridBorder: false, 
								unitsPost: 'Kg', 
								textAccessible: true, 
								gutter: { 
									left: 100, 
									right: 100, 
									top:70, bottom:50 
								}, 
								noendxtick: true, 
								title: '" . $lang['369'] . "' 
							} 
						})
						.set('tooltips', data3); 


						var line = new RGraph.Line({ 
							id: 'puntos', 
							data: [data1], 
							options: { 
								linewidth: linewidth, 
								tickmarks: 'filledcircle', 
								combinedchartEffect: 'trace2', 
								colors: ['red'], 
								noaxes: true, 
								ylabels: false, 
								backgroundGridVlines: false, 
								backgroundGridBorder: false, 
								unitsPost: ' 째', 
								textAccessible: true, 
								textSize: text_size, 
								tickmarks: 'dot', 
							} 
						})
						.set('tooltips', data1); 

						var combo = new RGraph.CombinedChart(bar, line);
						combo.draw();


						var yaxis = new RGraph.Drawing.YAxis({
							id: 'puntos',
							x: bar.gutterLeft,
							y: bar.gutterTop,
							options: { noyaxis: true, 
								max: bar.scale2.max,
								title: '" . $lang['370'] . "',
								colors: ['#0b2a61'], unitsPost: 'Pts', 
								gutter: { 
									left: 100, 
									top:70, bottom:50 
								}, 
								textAccessible: true, 
								textSize: text_size
							}
						}).draw();


						var yaxis1 = new RGraph.Drawing.YAxis({
							id: 'puntos',
							x: y1_x,
							y: bar.gutterTop,
							options: { noyaxis: true, 
								max: line.scale2.max,
								title: '" . $lang['383'] . "',
								colors: ['red'], unitsPost: ' °', 
								align: 'right', 
								gutter: { 
									right: 100, 
									top:70, bottom:50 
								}, 
								textAccessible: true, 
								textSize: text_size
							}
						}).draw();

						var xaxis = new RGraph.Drawing.XAxis({
							id: 'puntos',
							x: 100,
							y: bar.canvas.height - bar.gutterBottom,
							options: {
								labels: labels,
								colors: ['#000'],
								title: '" . $lang['358'] . "',
								textAccessible: true,
								gutter: { 
									right: 100, 
									left: 100, 
									top:70 
								}, 
								textSize: text_size
							}
						}).draw();

						</script>";
				?>