<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
$__APP_SITE_PATHS_START__ = __DIR__;
$__app_here = __DIR__;
for ($__i = 0, $__prev = null; $__i < 24; $__i++) {
	$__base = ($__i === 0) ? $__app_here : dirname($__app_here, $__i);
	if ($__base === $__prev) {
		break;
	}
	$__prev = $__base;
	$__inc = $__base . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'app_site_paths.inc.php';
	if (is_readable($__inc)) {
		require_once $__inc;
		break;
	}
}
unset($__i, $__prev, $__base, $__inc, $__app_here);

	require_once("membersite_config.php");
$schema = $Config->getSchema();
	$sessionstat = $fgmembersite->CheckLogin('changeCategory.php');

	$__langCk = $Config->getAlias() . 'language';
	if (!isset($_COOKIE[$__langCk]) || $_COOKIE[$__langCk] === '') {
		$Config->LoadLanguage();
		$__lang = $Config->lan;
	} else {
		$__lang = $_COOKIE[$__langCk];
	}
	include 'lang.' . $__lang . '.php';

    $retunData = array('status' => '0', 'message' => 'Something went wrong,please try again.');
    
    $Season = SanitizeInteger($_POST["Season"]);
    $Category = SanitizeInteger($_POST["Category"]);
    $htmlLogos = '';
    $htmlLogosList = '';
	
	
	$Config->LoadFlags();
	
    setcookie($Config->getAlias() . "season",$Season,0,'/');
	setcookie($Config->getAlias() . "category",$Category,0,'/');
	$fecha = new DateTime();
    
	
    $sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado then 1";
	$sqlPenales2 = "0";
	$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado then 1";
	$sqlPenales4 = "0";
	$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 and v.Jugado then 1 ";
	$sqlByeWeekPoints1 = "";
	$sqlByeWeekPoints2 = "";
	$sqlByeWeekPoints3 = "0 ";
	$sqlByeWeekSets3 = "0 ";
	$sqlByeWeekSetPoints3 = "0 ";
	if($Config->EmpatesPenales == ""){
		$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local <> l.Penal_Visitante and l.Jugado then 1 ";
		$sqlPenales2 = "case 
					when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado then 1
					else 0
				end as ";
		$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado then 2
					when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado then 1 ";
		$sqlPenales4 = "case 
					when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado then 1
					else 0
				end as ";
		$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado then 2 
					when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado then 1 ";
	}
	if($Config->ByeWeekPoints == 1){
		$sqlByeWeekPoints1 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 3 ";
		$sqlByeWeekPoints2 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 1 ";
		$sqlByeWeekPoints3 = "" . $Config->ByeWeekPointsGoals . " ";
	}
	if($Config->VollByeWeekSets !== 0){
		$sqlByeWeekPoints1 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 3 ";
		$sqlByeWeekPoints2 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 1 ";
		$sqlByeWeekPoints3 = "" . $Config->VollByeWeekPoints . " ";
		$sqlByeWeekSets3 = "" . $Config->VollByeWeekSets . " ";
		$sqlByeWeekSetPoints3 = "" . $Config->VollByeWeekSetPoints . " ";
	}
	
    $sqlPTSL = "";
    $sqlJGL = "";
    $sqlJEL = "";
    $sqlJPL = "";
    $sqlJJL = "";
    $sqlPTSV = "";
    $sqlJGV = "";
    $sqlJEV = "";
    $sqlJPV = "";
    $sqlJJV = "";
				
	$sql20 = "  SELECT * 
                FROM (
                		SELECT * FROM $schema.Juego_Estatus) a
                order by Juego_Estatus_ID;";
	$result20 = $Config->query($sql20);
    if ($result20->num_rows > 0) {
		// output data of each row
		while($row20 = $result20->fetch_assoc()) {
			$sqlPTSL .= " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["PTSL"] . " ";
			$sqlJGL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JGL"] . " ";
			$sqlJEL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JEL"] . " ";
			$sqlJPL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JPL"] . " ";
			$sqlJJL .=  " when l.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JJL"] . " ";
			$sqlPTSV .= " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["PTSV"] . " ";
			$sqlJGV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JGV"] . " ";
			$sqlJEV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JEV"] . " ";
			$sqlJPV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JPV"] . " ";
			$sqlJJV .=  " when v.Jugado = " . $row20["Juego_Estatus_ID"] . " then " . $row20["JJV"] . " ";
		}
    }
    /*
    echo $sqlPTSL;
    echo $sqlJGL;
    echo $sqlJEL;
    echo $sqlJPL;
    echo $sqlJJL;
    echo $sqlPTSV;
    echo $sqlJGV;
    echo $sqlJEV;
    echo $sqlJPV;
    echo $sqlJJV;
    */
    
    $sql = "SET @rank:=0;";
	$Config->query($sql);
	if($Config->getSport() == 0){
	    $sql = "SELECT @rank:=@rank+1 AS rank, Logo, Equipo_ID, Equipo_DESC, Equipo_FULLDESC, JJ, JG, JE, JP, GF, GC, DIFF, Puntos, Reales, Extra
        		from (
        				Select 	Logo, 
        						j.Equipo_ID, 
        						Equipo_DESC,
        						Equipo_FULLDESC, 
        	 	                replace(Equipo_FULLDESC, '単', '&ntilde;') Equipo_FULLDESC_N,
        						fuerza, ifnull(sum(Juegos),0) as JJ, 
        						ifnull(sum(JG),0) as JG, 
        						ifnull(sum(JE),0) as JE, 
        						ifnull(sum(JP),0) as JP, 
        						ifnull(sum(Puntos),0) as Puntos, 
        						ifnull(sum(Puntos),0)+ifnull(Sum(Extra),0)+ifnull(sum(ExtraEquipo),0) as Reales, 
        						ifnull(Sum(GF),0) as GF, 
        						ifnull(Sum(GC),0) as GC, 
        						ifnull(Sum(GF),0) - ifnull(Sum(GC),0) as DIFF, 
        						ifnull(Sum(Extra),0) Extra, 
        						j.Juego_ID
        				from (
        						select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
        								l.Jornada_ID, 
        								Equipo_ID, 
        								Equipo_DESC, 
        								Equipo_FULLDESC,
        								Fuerza, 
        								Juego_ID,
        								case  
        									" . $sqlByeWeekPoints1 . "
        									when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 3 
        									" . $sqlPenales1 . "
											" . $sqlPTSL . "
        									else 0
        								end as Puntos,
        								" . $sqlPenales2 . " Extra,
        								case 
        									" . $sqlByeWeekPoints1 . "
        									when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante then 3 
        									" . $sqlPenales3 . "
											" . $sqlPTSL . "
        									else 0
        								end + l.Extra_Local as Reales, 
        								case 
        									when l.Visitante_ID is not null then Gol_Local
        									else " . $sqlByeWeekPoints3 . "
        								end as GF, 
        								Gol_Visitante as GC,
        								case 
        									" . $sqlByeWeekPoints2 . "
        									when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
        									when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
        									when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
											" . $sqlJJL . "
        									else 
        										case when l.Estatus like '5' then 1 else 0 end
        								end as Juegos,
        								case 
        									" . $sqlByeWeekPoints2 . "
        									when Equipo_ID = l.Local_ID and l.Gol_Local > l.Gol_Visitante and l.Jugado = 1 then 1
											" . $sqlJGL . "
        									else 0
        								end as JG,
        								case 
        									when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 then 1
											" . $sqlJEL . "
        									else 0
        								end as JE,
        								case 
        									when Equipo_ID = l.Local_ID and l.Gol_Local < l.Gol_Visitante and l.Jugado = 1 then 1
											" . $sqlJPL . "
        									else 0
        								end as JP, 
        								l.Extra_Local ExtraEquipo
        						from $schema.Equipos e
        							left outer join $schema.Juegos l on e.Equipo_ID = l.Local_ID and l.Torneo_ID = $Season
														  and l.Jugado <> 10
                                    join $schema.Categorias lc on e.Fuerza = lc.Categoria_ID
                                    left outer join $schema.Jornada lj on l.Jornada_ID = lj.Jornada_ID 
                                                                                and lj.Jornada_Type = 1 
                                                                                and lc.Calendario_ID = lj.Calendario_ID
                                where e.Fuerza = $Category and e.Torneo_ID = $Season and e.Equipo_ID > 0 and Activo = 1 
                                    and l.Fecha between (   SELECT min(Fecha_Inicio)
														   FROM   $schema.Jornada
														   WHERE  Torneo_ID = $Season) and (SELECT max(Fecha_Fin) Fecha_Fin
																		   FROM  $schema.Jornada
																		   WHERE Torneo_ID = $Season)
        						UNION
        						select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
        								v.Jornada_ID, 
        								Equipo_ID, 
        								Equipo_DESC, 
        								Equipo_FULLDESC,
        								Fuerza, 
        								Juego_ID,
        								case 
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
											" . $sqlPenales5 . "
											" . $sqlPTSV  . "
        									else 0
        								end as Puntos, 
        								" . $sqlPenales4 . " Extra, 
        								case 
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 3 
        									" . $sqlPenales5 . "
											" . $sqlPTSV . "
        									else 0
        								end + v.Extra_Visitante as Reales, 
        								Gol_Visitante as GF, 
        								Gol_Local as GC,
        								case 
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
											" . $sqlJJV . "
        									else 
        										case when v.Estatus like '5' then 1 else 0 end
        								end as Juegos ,
        								case 
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local < v.Gol_Visitante and v.Jugado = 1 then 1
											" . $sqlJGV . "
        									else 0
        								end as JG,
        								case 
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 then 1 
											" . $sqlJEV . "
        									else 0
        								end as JE,
        								case 
        									when Equipo_ID = v.Visitante_ID and v.Gol_Local > v.Gol_Visitante and v.Jugado = 1 then 1 
											" . $sqlJPV . "
        									else 0
        								end
        							as JP, v.Extra_Visitante ExtraEquipo
        						from $schema.Equipos e
                                	left outer join $schema.Juegos v on e.Equipo_ID = v.Visitante_ID and v.Torneo_ID = $Season
														  and v.Jugado <> 10
                                    join $schema.Categorias vc on e.Fuerza = vc.Categoria_ID
                                    left outer join $schema.Jornada vj on v.Jornada_ID = vj.Jornada_ID 
                                                                                        and vj.Jornada_Type = 1 
                                                                                        and vc.Calendario_ID = vj.Calendario_ID
                            	where e.Fuerza = $Category and e.Torneo_ID = $Season  and e.Equipo_ID > 0 and Activo = 1
                                    and v.Fecha between (   SELECT min(Fecha_Inicio)
														   FROM   $schema.Jornada
														   WHERE  Torneo_ID = $Season) and (SELECT max(Fecha_Fin) Fecha_Fin
																		   FROM   $schema.Jornada
																		   WHERE  Torneo_Id = $Season)) j
                            Group by j.Equipo_ID, Equipo_DESC, Fuerza) jj
                        order by Reales desc, DIFF desc, GF desc, Equipo_FULLDESC";
	}
	if($Config->getSport() == 1){
	    $sql = "SELECT @rank:=@rank+1 AS rank, Logo, Equipo_ID, Equipo_DESC, 	Equipo_FULLDESC, JJ, JG, JP, GF, GC, DIFF, Puntos, PF, PC, SF, SC, CP, CS
					   from (
							Select  Logo, 
    								j.Equipo_ID,
    								Equipo_DESC,
    								Equipo_FULLDESC,
        	 	                    replace(Equipo_FULLDESC, '単', '&ntilde;') Equipo_FULLDESC_N,
    								fuerza,
    								ifnull(sum(Juegos), 0) as JJ,
    								ifnull(sum(JG), 0) as JG,
    								ifnull(sum(JP), 0) as JP, 
    								ifnull(sum(Puntos), 0) + ifnull(sum(Extra), 0) as Puntos, 
                                    ifnull(Sum(GF), 0) as GF, 
                                    ifnull(Sum(GC), 0) as GC, 
                                    ifnull(Sum(GF),0) - ifnull(Sum(GC), 0) as DIFF,  
                                    IFNULL(Sum(PF),0) AS PF,
                                    IFNULL(Sum(PC),0) AS PC,
                                    IFNULL(Sum(SF),0) AS SF,
                                    IFNULL(Sum(SC),0) AS SC,
                                    j.Juego_ID,
    				                CASE 
    				                    WHEN IFNULL(Sum(PF),0) = 0 THEN ROUND(0.000, 3)
    				                    WHEN IFNULL(Sum(PC),0) = 0 THEN ROUND(IFNULL(Sum(PF),0)/1, 3)
    				                    ELSE ROUND(IFNULL(Sum(PF),0)/IFNULL(Sum(PC),0), 3)
    				                END AS CP,
    				                CASE 
    				                    WHEN IFNULL(Sum(SF),0) = 0 THEN ROUND(0.000, 3)
    				                    WHEN IFNULL(Sum(SC),0) = 0 THEN ROUND(IFNULL(Sum(SF),0)/1, 3)
    				                    ELSE ROUND(IFNULL(Sum(SF),0)/IFNULL(Sum(SC),0), 3)
    				                END AS CS
							from (
									select distinct concat(e.Torneo_ID, '-', e.Equipo_ID) Logo, 
                                              l.Jornada_ID, 
                                              Equipo_ID, 
                                              Equipo_DESC, 
    								          Equipo_FULLDESC,
                                              Fuerza, 
                                              l.Juego_ID, 
                                              s.SL, 
                                              s.SV, 
                                              s.PL, 
                                              s.PV, 
                                              case  when l.Visitante_ID is null then " . $sqlByeWeekSets3 . "
                                                    else s.SL end SF, 
                                              s.SV SC, 
                                              case  when l.Visitante_ID is null then " . $sqlByeWeekSetPoints3 . "
                                                    else s.PL end PF, 
                                              s.PV PC, 
                                              case 	when Equipo_ID = l.Local_ID and s.SL > s.SV and s.SV = 0 and l.Jugado = 1 then 2 
                                    				when Equipo_ID = l.Local_ID and s.SL > s.SV and s.SV <> 0 and l.Jugado = 1 then 2 
                                                    when Equipo_ID = l.Local_ID and s.SL < s.SV and s.SL <> 0 and l.Jugado = 1 then 1 
                                                    when Equipo_ID = l.Local_ID and s.SL < s.SV and s.SL = 0 and l.Jugado = 1 then 0 
                                                    when l.Visitante_ID is null then " . $sqlByeWeekPoints3 . "
                                    		  end as Puntos, 
                                              l.Extra_Local Extra,  
                                              case when l.Visitante_ID is not null then s.PL 
                                                        when l.Visitante_ID is null then " . $sqlByeWeekSetPoints3 . "
                                                        else 0 end as GF, 
                                              s.PV as GC, 
                                              case 	when Equipo_ID = l.Local_ID and s.PL > s.PV and l.Jugado = 1 then 1 
                                    				when Equipo_ID = l.Local_ID and s.PL = s.PL and l.Jugado = 1 then 1 
                                                    when Equipo_ID = l.Local_ID and s.PL < s.PL and l.Jugado = 1 then 1 
                                                    when l.Visitante_ID is null then 1
                                                    when l.Jugado = 2 then 1 
                                                    when l.Jugado = 0 then 0 
                                    		  end as Juegos, 
                                              case when Equipo_ID = l.Local_ID and s.SL > s.SV and l.Jugado = 1 then 1 
                                                        when l.Visitante_ID is null then 1
                                                        else 0 end as JG,
                                              case when l.Jugado = 2 then 1 when Equipo_ID = l.Local_ID and s.SL < s.SV and l.Jugado = 1 then 1 else 0 end as JP, 
                                              l.Extra_Local ExtraEquipo 
                                    from $schema.Equipos e 
                                        left outer join $schema.Juegos l on e.Equipo_ID = l.Local_ID and l.Torneo_ID = $Season
                                        left outer join (	select 	Juego_ID, case when ifnull(s1.Set1_L, 0) > ifnull(s1.Set1_V, 0) then 1 else 0 end + case when ifnull(s1.Set2_L, 0) > ifnull(s1.Set2_V, 0) then 1 else 0 end + case when ifnull(s1.Set3_L, 0) > ifnull(s1.Set3_V, 0) then 1 else 0 end + case when ifnull(s1.Set4_L, 0) > ifnull(s1.Set4_V, 0) then 1 else 0 end + case when ifnull(s1.Set5_L, 0) > ifnull(s1.Set5_V, 0) then 1 else 0 end SL, 
                                    									case when ifnull(s1.Set1_V, 0) > ifnull(s1.Set1_L, 0) then 1 else 0 end + case when ifnull(s1.Set2_V, 0) > ifnull(s1.Set2_L, 0) then 1 else 0 end + case when ifnull(s1.Set3_V, 0) > ifnull(s1.Set3_L, 0) then 1 else 0 end + case when ifnull(s1.Set4_V, 0) > ifnull(s1.Set4_L, 0) then 1 else 0 end + case when ifnull(s1.Set5_V, 0) > ifnull(s1.Set5_L, 0) then 1 else 0 end SV, 
                                    									ifnull(s1.Set1_L, 0) + ifnull(s1.Set2_L, 0) + ifnull(s1.Set3_L, 0) + ifnull(s1.Set4_L, 0) + ifnull(s1.Set5_L, 0) PL, 
                                    									ifnull(s1.Set1_V, 0) + ifnull(s1.Set2_V, 0) + ifnull(s1.Set3_V, 0) + ifnull(s1.Set4_V, 0) + ifnull(s1.Set5_V, 0) PV
						                                    from $schema.Juegos_Set s1) s on l.Juego_ID = s.Juego_ID 
                                    where e.Fuerza = $Category and e.Torneo_ID = $Season and e.Equipo_ID > 0 and Activo = 1 
                                        and l.Fecha between (   SELECT min(Fecha_Inicio)
    														   FROM   $schema.Jornada
    														   WHERE  Torneo_ID = $Season) and (SELECT max(Fecha_Fin) Fecha_Fin
    																		   FROM  $schema.Jornada
    																		   WHERE Torneo_ID = $Season)
									UNION
									select distinct concat(e.Torneo_ID,'-', e.Equipo_ID) Logo, 
											v.Jornada_ID, 
											Equipo_ID, 
											Equipo_DESC, 
    								        Equipo_FULLDESC,
											Fuerza, 
											v.Juego_ID,
                                            s.SL, 
                                            s.SV, 
                                            s.PL, 
                                            s.PV, 
                                            case    when v.Visitante_ID is null then 0
                                                    else s.SV end SF, 
                                            s.SL SC, 
                                            case    when v.Visitante_ID is null then 0
                                                    else s.PV end PF, 
                                            s.PL PC, 
                                            case 	when Equipo_ID = v.Visitante_ID and s.SL < s.SV and s.SL = 0 and v.Jugado = 1 then 2
                                				    when Equipo_ID = v.Visitante_ID and s.SL < s.SV and s.SL <> 0 and v.Jugado = 1 then 2 
                                                    when Equipo_ID = v.Visitante_ID and s.SL > s.SV and s.SV <> 0 and v.Jugado = 1 then 1 
                                                    when Equipo_ID = v.Visitante_ID and s.SL > s.SV and s.SV = 0 and v.Jugado = 1 then 0  
                                                    when v.Visitante_ID is null then 0
                                		    end as Puntos,
										    v.Extra_Visitante Extra,
											case  when v.Visitante_ID is not null then s.PV 
                                                        when v.Visitante_ID is null then 0
                                                        else 0 end as GF, 
                                            s.PL as GC, 
                                            case 	when Equipo_ID = v.Visitante_ID and s.PL < s.PV and v.Jugado = 1 then 1 
                                            	when Equipo_ID = v.Visitante_ID and s.PL = s.PV and v.Jugado = 1 then 1 
                                                when Equipo_ID = v.Visitante_ID and s.PL > s.PV and v.Jugado = 1 then 1 
                                                when v.Jugado = 2 then 1
                                                when v.Jugado = 0 then 0 
                                            end as Juegos, 
                                            case when Equipo_ID = v.Visitante_ID and s.PL < s.PV and v.Jugado = 1 then 1 else 0 end as JG, 
                                            case 	when Equipo_ID = v.Visitante_ID and s.PL > s.PV and v.Jugado = 1 then 1 
                            				        when v.Jugado = 2 then 1 else 0 end as JP, 
                            				v.Extra_Visitante ExtraEquipo
									from $schema.Equipos e
										left outer join $schema.Juegos v on e.Equipo_ID = v.Visitante_ID and v.Torneo_ID = $Season
										left outer join (	select 	Juego_ID, case when ifnull(s1.Set1_L, 0) > ifnull(s1.Set1_V, 0) then 1 else 0 end + case when ifnull(s1.Set2_L, 0) > ifnull(s1.Set2_V, 0) then 1 else 0 end + case when ifnull(s1.Set3_L, 0) > ifnull(s1.Set3_V, 0) then 1 else 0 end + case when ifnull(s1.Set4_L, 0) > ifnull(s1.Set4_V, 0) then 1 else 0 end + case when ifnull(s1.Set5_L, 0) > ifnull(s1.Set5_V, 0) then 1 else 0 end SL, 
                                    									case when ifnull(s1.Set1_V, 0) > ifnull(s1.Set1_L, 0) then 1 else 0 end + case when ifnull(s1.Set2_V, 0) > ifnull(s1.Set2_L, 0) then 1 else 0 end + case when ifnull(s1.Set3_V, 0) > ifnull(s1.Set3_L, 0) then 1 else 0 end + case when ifnull(s1.Set4_V, 0) > ifnull(s1.Set4_L, 0) then 1 else 0 end + case when ifnull(s1.Set5_V, 0) > ifnull(s1.Set5_L, 0) then 1 else 0 end SV, 
                                    									ifnull(s1.Set1_L, 0) + ifnull(s1.Set2_L, 0) + ifnull(s1.Set3_L, 0) + ifnull(s1.Set4_L, 0) + ifnull(s1.Set5_L, 0) PL, 
                                    									ifnull(s1.Set1_V, 0) + ifnull(s1.Set2_V, 0) + ifnull(s1.Set3_V, 0) + ifnull(s1.Set4_V, 0) + ifnull(s1.Set5_V, 0) PV
						                                    from $schema.Juegos_Set s1) s on v.Juego_ID = s.Juego_ID 
                                    where e.Fuerza = $Category and e.Torneo_ID = $Season and e.Equipo_ID > 0 and Activo = 1 
                                        and v.Fecha between (   SELECT min(Fecha_Inicio)
    														   FROM   $schema.Jornada
    														   WHERE  Torneo_ID = $Season) and (SELECT max(Fecha_Fin) Fecha_Fin
    																		   FROM  $schema.Jornada
    																		   WHERE Torneo_ID = $Season)) j
							where Fuerza = $Category
							Group by j.Equipo_ID, Equipo_DESC, Fuerza
							) jj
					order by Puntos desc, CS desc, CP desc, Equipo_DESC";
	}
	if($Config->getSport() == 2){
	    $sql = "SELECT 
                  @rank := @rank + 1 AS rank, 
                  Equipo_ID, 
                  Logo, 
                  Equipo_DESC,
        	 	  Equipo_FULLDESC, 
        	 	  replace(Equipo_FULLDESC, '単', '&ntilde;') Equipo_FULLDESC_N,
                  JJ, 
                  JG, 
                  JP, 
                  GF, 
                  GC, 
                  ROUND(PF - PC, 0) as DIFF, 
                  Puntos, 
                  ROUND(PF, 0) as PF, 
                  ROUND(PC, 0) as PC, 
                  CASE WHEN PF = 0 THEN ROUND(0.000, 3) WHEN PC = 0 THEN ROUND(PF / 1, 3) ELSE ROUND(PF / PC, 3) END AS CP, 
                  ROUND(SF, 0) as SF, 
                  ROUND(SC, 0) as SC, 
                  CASE WHEN SF = 0 THEN ROUND(0.000, 3) WHEN SC = 0 THEN ROUND(SF / 1, 3) ELSE ROUND(SF / SC, 3) END AS CS 
                from 
                  (
                    Select Logo, j.Equipo_ID, Equipo_DESC, Equipo_FULLDESC, fuerza, 
                		ifnull(sum(Juegos), 0) as JJ, 
                		ifnull(sum(JG), 0) as JG, ifnull(sum(JP), 0) as JP, ifnull(sum(Puntos), 0) + ifnull(sum(Extra), 0) as Puntos, 
                		ifnull(Sum(GF), 0) as GF, ifnull(Sum(GC), 0) as GC, ifnull(Sum(GF), 0) - ifnull(Sum(GC), 0) as DIFF, 
                		IFNULL(Sum(PF), 0.001) AS PF, IFNULL(Sum(PC), 0.001) AS PC, IFNULL(Sum(SF), 0.001) AS SF, IFNULL(Sum(SC), 0.001) AS SC, 
                		j.Juego_ID 
                    from 
                      (
                		select 
                			distinct concat(e.Torneo_ID, '-', e.Equipo_ID) Logo, 
                			s.Jornada_ID, Equipo_ID, Equipo_DESC, Equipo_FULLDESC, Fuerza, s.Juego_ID, 
                			s.SF, s.SC, s.PF, s.PC, 
                			case when Equipo_ID = s.Local_ID and s.PL > s.PV  and s.Jugado = 1 then 2 
                				when Equipo_ID = s.Local_ID and s.PL < s.PV and s.Jugado = 1 then 1 
                			end as Puntos, s.Extra, 
                			case when s.Visitante_ID is not null then s.PL else 0 end as GF, 
                			s.PV as GC, s.Juegos, 
                			case when Equipo_ID = s.Local_ID and s.PL > s.PV and s.Jugado = 1 then 1 else 0 end as JG, 
                			case when s.Jugado = 2 then 1 when Equipo_ID = s.Local_ID and s.PL < s.PV and s.Jugado = 1 then 1 else 0 end as JP, 
                			s.ExtraEquipo,
                			s.Local_ID, s.PL, s.PV, s.Jugado
                		from 
                			$schema.Equipos e 
                			left outer join (
                						select distinct l.Jornada_ID, l.Juego_ID, 
                							s.SL SF, s.SV SC, s.PL PF, s.PV PC, l.Local_ID, s.PL, s.PV, l.Jugado, l.Visitante_ID, 
                							l.Extra_Local Extra, s.PV as GC, 
                							case when l.Jugado = 1 then 1 when l.Jugado = 2 then 1 when l.Jugado = 0 then 0 end as Juegos, 
                							l.Extra_Local ExtraEquipo 
                						from $schema.Juegos l 
                							left outer join (
                									select 
                										  Juego_ID, 
                										  case when ifnull(s1.Set1_L, 0) > ifnull(s1.Set1_V, 0) then 1 else 0 end + case when ifnull(s1.Set2_L, 0) > ifnull(s1.Set2_V, 0) then 1 else 0 end + case when ifnull(s1.Set3_L, 0) > ifnull(s1.Set3_V, 0) then 1 else 0 end + case when ifnull(s1.Set4_L, 0) > ifnull(s1.Set4_V, 0) then 1 else 0 end + case when ifnull(s1.Set5_L, 0) > ifnull(s1.Set5_V, 0) then 1 else 0 end SL, 
                										  case when ifnull(s1.Set1_V, 0) > ifnull(s1.Set1_L, 0) then 1 else 0 end + case when ifnull(s1.Set2_V, 0) > ifnull(s1.Set2_L, 0) then 1 else 0 end + case when ifnull(s1.Set3_V, 0) > ifnull(s1.Set3_L, 0) then 1 else 0 end + case when ifnull(s1.Set4_V, 0) > ifnull(s1.Set4_L, 0) then 1 else 0 end + case when ifnull(s1.Set5_V, 0) > ifnull(s1.Set5_L, 0) then 1 else 0 end SV, 
                										  ifnull(s1.Set1_L, 0) + ifnull(s1.Set2_L, 0) + ifnull(s1.Set3_L, 0) + ifnull(s1.Set4_L, 0) + ifnull(s1.Set5_L, 0) PL, 
                										  ifnull(s1.Set1_V, 0) + ifnull(s1.Set2_V, 0) + ifnull(s1.Set3_V, 0) + ifnull(s1.Set4_V, 0) + ifnull(s1.Set5_V, 0) PV 
                									from 
                									  $schema.Juegos_Set s1
                								  ) s on l.Juego_ID = s.Juego_ID 
                						where l.Fecha between (
                									SELECT min(Fecha_Inicio) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  ) 
                								  and (
                									SELECT max(Fecha_Fin) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  )
                				  ) s on e.Equipo_ID = s.Local_ID 
                			join $schema.Categorias lc on e.Fuerza = lc.Categoria_ID 
                			left outer join $schema.Jornada lj on s.Jornada_ID = lj.Jornada_ID and lj.Jornada_Type = 1 and lc.Calendario_ID = lj.Calendario_ID 
                		where 
                			e.Fuerza = $Category and e.Torneo_ID = $Season and e.Activo = 1
                		UNION 
                		select 
                			distinct concat(e.Torneo_ID, '-', e.Equipo_ID) Logo, 
                			s.Jornada_ID, Equipo_ID, Equipo_DESC, Equipo_FULLDESC, Fuerza, s.Juego_ID, 
                			s.SF, s.SC, s.PF, s.PC, 
                			case when Equipo_ID = s.Visitante_ID and s.PL < s.PV and s.Jugado = 1 then 2
                				when Equipo_ID = s.Visitante_ID  and s.PL > s.PV and s.Jugado = 1 then 1 end as Puntos, s.Extra, 
                			case when s.Visitante_ID is not null then s.PL else 0 end as GF, 
                			s.PV as GC, s.Juegos, 
                			case when Equipo_ID = s.Visitante_ID and s.PL < s.PV and s.Jugado = 1 then 1 else 0 end as JG, 
                            case when Equipo_ID = s.Visitante_ID and s.PL > s.PV and s.Jugado = 1 then 1 when s.Jugado = 2 then 1 else 0 end as JP, 
                			s.ExtraEquipo,
                			s.Local_ID, s.PL, s.PV, s.Jugado
                		from 
                			$schema.Equipos e 
                			left outer join (
                						select distinct v.Jornada_ID, v.Juego_ID, 
                							s.SL SF, s.SV SC, s.PL PF, s.PV PC, v.Local_ID, s.PL, s.PV, v.Jugado, v.Visitante_ID, 
                							v.Extra_Visitante Extra, s.PV as GC, 
                							case when v.Jugado = 1 then 1 when v.Jugado = 2 then 1 when v.Jugado = 0 then 0 end as Juegos, 
                							v.Extra_Visitante ExtraEquipo 
                						from $schema.Juegos v 
                							left outer join (
                									select 
                										  Juego_ID, 
                										  case when ifnull(s1.Set1_L, 0) > ifnull(s1.Set1_V, 0) then 1 else 0 end + case when ifnull(s1.Set2_L, 0) > ifnull(s1.Set2_V, 0) then 1 else 0 end + case when ifnull(s1.Set3_L, 0) > ifnull(s1.Set3_V, 0) then 1 else 0 end + case when ifnull(s1.Set4_L, 0) > ifnull(s1.Set4_V, 0) then 1 else 0 end + case when ifnull(s1.Set5_L, 0) > ifnull(s1.Set5_V, 0) then 1 else 0 end SL, 
                										  case when ifnull(s1.Set1_V, 0) > ifnull(s1.Set1_L, 0) then 1 else 0 end + case when ifnull(s1.Set2_V, 0) > ifnull(s1.Set2_L, 0) then 1 else 0 end + case when ifnull(s1.Set3_V, 0) > ifnull(s1.Set3_L, 0) then 1 else 0 end + case when ifnull(s1.Set4_V, 0) > ifnull(s1.Set4_L, 0) then 1 else 0 end + case when ifnull(s1.Set5_V, 0) > ifnull(s1.Set5_L, 0) then 1 else 0 end SV, 
                										  ifnull(s1.Set1_L, 0) + ifnull(s1.Set2_L, 0) + ifnull(s1.Set3_L, 0) + ifnull(s1.Set4_L, 0) + ifnull(s1.Set5_L, 0) PL, 
                										  ifnull(s1.Set1_V, 0) + ifnull(s1.Set2_V, 0) + ifnull(s1.Set3_V, 0) + ifnull(s1.Set4_V, 0) + ifnull(s1.Set5_V, 0) PV 
                									from 
                									  $schema.Juegos_Set s1
                								  ) s on v.Juego_ID = s.Juego_ID 
                						where v.Fecha between (
                									SELECT min(Fecha_Inicio) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  ) 
                								  and (
                									SELECT max(Fecha_Fin) 
                									FROM $schema.Jornada 
                									WHERE Torneo_ID = $Season AND Jornada_Type = 1
                								  )
                				  ) s on e.Equipo_ID = s.Visitante_ID 
                			join $schema.Categorias lc on e.Fuerza = lc.Categoria_ID 
                			left outer join $schema.Jornada lj on s.Jornada_ID = lj.Jornada_ID and lj.Jornada_Type = 1 and lc.Calendario_ID = lj.Calendario_ID 
                		where 
                			e.Fuerza = $Category and e.Torneo_ID = $Season and e.Activo = 1) j 
                    where 
                      Fuerza = $Category 
                    Group by 
                      j.Equipo_ID, 
                      Equipo_DESC, 
                      Fuerza
                  ) jj 
                order by 
                    Puntos desc, 
                    CS desc, 
                    CP desc, 
                    Equipo_DESC";
	}
	//echo $sql;
    //$htmlLogos .= $sql;
	$htmlLogos .= '<div class="container-fluid py-1 px-3 d-none d-lg-none d-xl-block" style="padding-right: 0px !important;"><div class="input-group input-group-outline" style="">';
    
	$htmlLogosDrop = '<div class="container-fluid py-1 px-3 d-md-block d-lg-block d-xl-none"><div class="dropdown">';
    $result = $Config->query($sql);
    $totLogos = $result->num_rows;
    $count = 0;
    if($result){
        if ($result->num_rows > 0) {
            $width = round(860/($result->num_rows));
            $radius = round((860/($result->num_rows))/5);
            if($width > 60){
                 $width = 60;
            }	 $radius = 60/5;
            while($row2 = $result->fetch_assoc()) {
				$htmlLogos .= '<ul class="list-group list-group-horizontal" style="">
                  <li class="list-group-item" style="background: transparent;border: 0px;padding: 0.1rem 0.1rem;">
                    <div class="hover">
                      <div class="circular" style="height: ' .$width . 'px;width: ' .$width . 'px;border-radius:' . $radius . 'px;">
                        <figure style="margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;">
                          <img src="./imagenes/' . mb_convert_encoding((string)$row2["Logo"], 'UTF-8', 'ISO-8859-1') . '.png?tmp=' . $fecha->getTimestamp() . '" width="' .$width . '" height="' .$width . '" title="' . $row2["Equipo_FULLDESC"] . '" alt="" onclick="loadTeam(' . mb_convert_encoding((string)$row2["Equipo_ID"], 'UTF-8', 'ISO-8859-1') . ',' . mb_convert_encoding((string)$Season, 'UTF-8', 'ISO-8859-1') . ');">
                        </figure>
                      </div>
                    </div>
                  </li>
                </ul>';
		if($count == 0){
		  $htmlLogosDrop .= '<a href="#" class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink0" style="margin-bottom: 0rem;"><img src="./imagenes/' . mb_convert_encoding((string)$row2["Logo"], 'UTF-8', 'ISO-8859-1') . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: 17px;"/> ' . $row2["Equipo_FULLDESC"] . '</a>';
		  if($totLogos > 1){
		    $htmlLogosDrop .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink0">';
		  }
		}else{
		  $htmlLogosDrop .= '<li><a class="dropdown-item" href="#" onclick="loadTeam(' . mb_convert_encoding((string)$row2["Equipo_ID"], 'UTF-8', 'ISO-8859-1') . ',' . mb_convert_encoding((string)$Season, 'UTF-8', 'ISO-8859-1') . '); return false;"><img src="./imagenes/' . mb_convert_encoding((string)$row2["Logo"], 'UTF-8', 'ISO-8859-1') . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: 17px;"/> ' . $row2["Equipo_FULLDESC"] . '</a></li>';
		}
		$htmlLogosDrop .= '';
		$count = $count + 1;
            }
	    if($totLogos > 1){
              $htmlLogosDrop .= '</ul>';
            }
        }
    }
    $htmlLogosDrop .= '</div></div>';
    $htmlLogos .= '</div></div>';
    $htmlLogos .= $htmlLogosDrop;
	
					
	$htmlMenu = '<span>';
	$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado then 1";
	$sqlPenales2 = "0";
	$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Jugado = 1 and l.Jugado then 1";
	$sqlPenales4 = "0";
	$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Jugado = 1 and v.Jugado then 1 ";
	$sqlByeWeekPoints1 = "";
	$sqlByeWeekPoints2 = "";
	$sqlByeWeekPoints3 = "0 ";
	if($Config->EmpatesPenales == ""){
		$sqlPenales1 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local <> l.Penal_Visitante and l.Jugado then 1 ";
		$sqlPenales2 = "case 
					when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado then 1
					else 0
				end as ";
		$sqlPenales3 = "when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local > l.Penal_Visitante and l.Jugado then 2
					when Equipo_ID = l.Local_ID and l.Gol_Local = l.Gol_Visitante and l.Penal_Local < l.Penal_Visitante and l.Jugado then 1 ";
		$sqlPenales4 = "case 
					when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado then 1
					else 0
				end as ";
		$sqlPenales5 = "when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local < v.Penal_Visitante and v.Jugado then 2 
					when Equipo_ID = v.Visitante_ID and v.Gol_Local = v.Gol_Visitante and v.Penal_Local > v.Penal_Visitante and v.Jugado then 1 ";
	}
	if($Config->ByeWeekPoints == 1){
		$sqlByeWeekPoints1 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 3 ";
		$sqlByeWeekPoints2 = "when Equipo_ID = l.Local_ID and l.Visitante_ID is null then 1 ";
		$sqlByeWeekPoints3 = "" . $Config->ByeWeekPointsGoals . " ";
	}
	
	$sql1 = "SET @rank:=0;";
	$Config->query($sql1);
	$htmlMenu .= '<div class="container-fluid py-1 px-3 d-md-block d-lg-block d-xl-none" style="width: 87%;"><div class="dropdown">';
    $result = $Config->query($sql);
    $totLogos = $result->num_rows;
    $htmlMenu .= '<a class="btn bg-gradient-dark dropdown-toggle " data-bs-toggle="dropdown" id="navbarDropdownMenuLink0" style="margin-bottom: 0rem;">-- ' . $lang['112-1'] . '</a>';
	$htmlMenu .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink0">';
	$count = 1;
    if($result){
		if ($result->num_rows > 0) {
			$width = round(860/($result->num_rows));
			$radius = round((860/($result->num_rows))/5);
			if($width > 60){
				 $width = 60;
			}	 $radius = 60/5;
			while($row2 = $result->fetch_assoc()) {
				$htmlMenu .= '<li><a class="dropdown-item"  onclick="loadTeam(' . mb_convert_encoding((string)$row2["Equipo_ID"], 'UTF-8', 'ISO-8859-1') . "," . $_COOKIE[$Config->getAlias() . "season"] . '); toggleSidenav();"><img src="./imagenes/' . mb_convert_encoding((string)$row2["Logo"], 'UTF-8', 'ISO-8859-1') . '.png?tmp=' . $fecha->getTimestamp() . '" style="width: 17px;"/> ' . $row2["Equipo_FULLDESC"] . '</a></li>';
				
				$count = $count + 1;
			}
		}
    }
    $htmlMenu .= '</ul>';
	$htmlMenu .= '</div></div>';
    $htmlMenu .= '</span>';
    
    $retunData = array('status' => '1', 'message' => 'Success.', 'dataLogos' => $htmlLogos, 'menulogos' => $htmlMenu, 'Sql' => $sql);
    $Config->Close();
    echo json_encode($retunData);
?>