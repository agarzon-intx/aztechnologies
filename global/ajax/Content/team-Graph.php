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

				$graphSeason = (int) $Season;
				$graphTeam = (int) $Team;
				$sql = "SELECT k.Jornada_DescCorta AS Jornada,
								IFNULL(i.PosGrupo, 0) AS PosGrupo,
								IFNULL(i.PosGeneral, 0) AS PosGeneral,
								IFNULL(i.Reales, 0) AS Reales
						FROM $schema.Jornada k
						LEFT JOIN $schema.Equipo_Stats i
							ON i.Torneo_ID = k.Torneo_ID
							AND i.Jornada_ID = k.Jornada_ID
							AND i.Equipo_ID = $graphTeam
						WHERE k.Torneo_ID = $graphSeason
							AND k.Jornada_ID <= (SELECT IFNULL(MAX(Jornada_ID), 0) - 2
												FROM $schema.Jornada
												WHERE Torneo_ID = $graphSeason)
							AND EXISTS (
								SELECT 1
								FROM $schema.Equipo_Stats ik
								WHERE ik.Torneo_ID = k.Torneo_ID
									AND ik.Jornada_ID = k.Jornada_ID
							)
						ORDER BY k.Jornada_ID";
				$result = $Config->query($sql);

				$data1 = array();
				$data2 = array();
				$data3 = array();
				$labels = array();
				if ($result && $result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$data1[] = (string) (int) $row['PosGrupo'];
						$data2[] = (string) (int) $row['PosGeneral'];
						$data3[] = (string) (int) $row['Reales'];
						$labels[] = "'" . addslashes((string) $row['Jornada']) . "'";
					}
				}

				$htmlTeam .= 'var data1 = [' . implode(', ', $data1) . ' ];';
				$htmlTeam .= "var data2 = [ " . implode(', ', $data2) . " ];";
				$htmlTeam .= "var data3 = [ " . implode(', ', $data3) . " ];";
				$htmlTeam .= "var labels = [ " . implode(', ', $labels) . " ];";
						
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