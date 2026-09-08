-- Fix TournamentCreate: Equipos/Categorias copy must include newer columns.
-- Equipos: Institucion_ID, Nombre_Color, Credencial_Color
-- Categorias: Rondas
-- Safe to re-run.

DELIMITER ;;

DROP PROCEDURE IF EXISTS `TournamentCreate`;;

CREATE DEFINER=CURRENT_USER PROCEDURE `TournamentCreate`(
	IN inUserName varchar(45),
	IN intournamrntname varchar(45),
	IN intournamrntactual varchar(1),
	IN intournamrntinscr int(11),
	IN intournamrntvsall int(11),
	IN intournamrntweeks int(11),
	OUT out_number int(11)
)
BEGIN
	DECLARE prev_TorneoID INT;
	DECLARE curr_TorneoID INT;
	DECLARE count_insert INT;
	DECLARE status_insert VARCHAR(55);

	IF (intournamrntactual = 'S') THEN
		UPDATE Torneos
			SET Actual = 'N',
				FechaCambio = NOW();
	END IF;

	IF (intournamrntinscr > 0) THEN
		UPDATE Torneos
			SET Inscripciones = 0,
				FechaCambio = NOW();
	END IF;

	INSERT INTO Torneos
		(Torneo_Desc,
		Actual,
		Inscripciones,
		TodosVsTodos,
		FechaAlta,
		FechaCambio,
		Jornadas)
	VALUES
		(intournamrntname,
		intournamrntactual,
		intournamrntinscr,
		intournamrntvsall,
		NOW(),
		NOW(),
		intournamrntweeks);

	SELECT ROW_COUNT() INTO count_insert;

	IF (count_insert > 0) THEN
		SET status_insert = CONCAT('OK, total inserts: ', count_insert);

		SELECT max(Torneo_ID) INTO @curr_TorneoID
		FROM Torneos;

		SELECT max(Torneo_ID) INTO @prev_TorneoID
		FROM Torneos
		WHERE Torneo_ID <> @curr_TorneoID;

		INSERT INTO Equipos (
			Equipo_ID,
			Equipo_DESC,
			Activo,
			Fuerza,
			Logo,
			Equipo_FULLDESC,
			Torneo_ID,
			Campo_ID,
			Short,
			Playera,
			Calcetas,
			Equipo_DESC3,
			Institucion_ID,
			Nombre_Color,
			Credencial_Color
		)
		SELECT
			a.Equipo_ID,
			Equipo_DESC,
			Activo,
			Fuerza,
			Logo,
			Equipo_FULLDESC,
			@curr_TorneoID,
			Campo_ID,
			Short,
			Playera,
			Calcetas,
			Equipo_DESC3,
			IFNULL(Institucion_ID, 0),
			Nombre_Color,
			Credencial_Color
		FROM Equipos a
		WHERE Torneo_ID = @prev_TorneoID;

		INSERT INTO Categorias (
			Categoria_ID,
			Categoria_Desc,
			Categoria_Orden,
			Edad_Inicial,
			Edad_Final,
			Color,
			Torneo_ID,
			Calendario_ID,
			Rondas
		)
		SELECT
			a.Categoria_ID,
			Categoria_Desc,
			Categoria_Orden,
			Edad_Inicial,
			Edad_Final,
			Color,
			@curr_TorneoID,
			Calendario_ID,
			IFNULL(Rondas, 1)
		FROM Categorias a
		WHERE Torneo_ID = @prev_TorneoID;
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ', count_insert);
	END IF;

	CALL insertIntoControlTable(
		inUserName,
		'CREATE',
		'TOURNAMENT',
		CONCAT(
			'INSERT INTO Torneos (Torneo_Desc, Actual, Inscripciones, TodosVsTodos, FechaAlta, FechaCambio, Jornadas) VALUES (''',
			intournamrntname, ''',', intournamrntactual, ',', intournamrntinscr, ',', intournamrntvsall,
			',NOW(),NOW(),', intournamrntweeks, ');'
		),
		status_insert
	);
	SET out_number = count_insert;
END;;

DELIMITER ;
