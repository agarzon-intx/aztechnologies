-- Update TeamCreate / TeamUpdate for all site databases.
-- Includes Institucion_ID, Nombre_Color, and Credencial_Color.
-- Run after add-equipos-institucion-id.sql and add-equipos-color-columns.sql.
-- Safe to re-run: each section drops and recreates both procedures.

DELIMITER ;;

-- aztechn1_demomina
USE `aztechn1_demomina`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

-- aztechn1_elite
USE `aztechn1_elite`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

-- aztechn1_huskies
USE `aztechn1_huskies`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

-- aztechn1_lidep
USE `aztechn1_lidep`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

-- aztechn1_nuestrodeporte
USE `aztechn1_nuestrodeporte`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

-- aztechn1_vollidep
USE `aztechn1_vollidep`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

-- aztechn1_voleibolmetepec
USE `aztechn1_voleibolmetepec`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

-- aztechn1_aztflag
USE `aztechn1_aztflag`;;


DROP PROCEDURE IF EXISTS `TeamCreate`;;
CREATE PROCEDURE `TeamCreate`(
	IN inUserName varchar(45),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_insert INT;
    DECLARE status_insert VARCHAR(55);
    
    INSERT INTO Equipos
		(Equipo_DESC,
		Activo,
		Fuerza,
		Logo,
		Equipo_FULLDESC,
		Torneo_ID,
		Campo_ID,
		Playera,
		Short,
		Calcetas,
		Equipo_DESC3,
		Institucion_ID,
		Nombre_Color,
		Credencial_Color)
	VALUES
		(inteamname,
		 inteamstatus,
		 inteamcategory,
		 '',
		 inteamnamelong,
		 (SELECT Torneo_ID FROM Torneos where Actual = 'S'),
		 inteamfield,
		 inteamshirt,
		 inteamshort,
		 inteamsoack,
		 inteamname3,
		 inteaminstitution,
		 inteamnamecolor,
		 inteamcredentialcolor);
         
    SELECT ROW_COUNT() into count_insert;
    
    IF (count_insert > 0) then
		SET status_insert = CONCAT('OK, total inserts: ',count_insert);
	ELSE
		SET status_insert = CONCAT('NO OK, total inserts: ',count_insert);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'CREATE', 'TEAM', CONCAT('INSERT INTO Equipos (Equipo_DESC, Activo, Fuerza, Logo, Equipo_FULLDESC, Torneo_ID, Campo_ID, Playera, Short, Calcetas, Equipo_DESC3, Institucion_ID, Nombre_Color, Credencial_Color) VALUES (''' , inteamname , ''', ' , inteamstatus , ', ' , inteamcategory , ', '', ''' , inteamnamelong , ''', (SELECT Torneo_ID FROM Torneos where Actual = ''S''), ' , inteamfield , ', ''' , inteamshirt , ''', ''' , inteamshort , ''', ''' , inteamsoack , ''',''' , inteamname3 , ''', ' , inteaminstitution , ', ''' , inteamnamecolor , ''', ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''');'), status_insert);
	set out_number = count_insert;
END;;

DROP PROCEDURE IF EXISTS `TeamUpdate`;;
CREATE PROCEDURE `TeamUpdate`(
	IN inUserName varchar(45),
	IN inteamid int(11),
	IN inteamname varchar(45),
	IN inteamnamelong varchar(45),
	IN inteamcategory int(11),
	IN inteamstatus int(11),
	IN inteamfield int(11),
	IN inteamshirt varchar(45),
	IN inteamshort varchar(45),
	IN inteamsoack varchar(45),
	IN inteamname3 varchar(45),
	IN inteaminstitution bigint(20),
	IN inteamnamecolor varchar(45),
	IN inteamcredentialcolor varchar(45),
    OUT out_number int(11)
)
BEGIN

	DECLARE count_update INT;
    DECLARE status_update VARCHAR(55);
    
    UPDATE Equipos
	SET Equipo_DESC = inteamname,
		Activo = inteamstatus,
		Fuerza = inteamcategory,
		Logo = '',
		Equipo_FULLDESC = inteamnamelong,
		Campo_ID = inteamfield,
		Playera = inteamshirt,
		Short = inteamshort,
		Calcetas = inteamsoack,
        Equipo_DESC3 = inteamname3,
		Institucion_ID = inteaminstitution,
		Nombre_Color = inteamnamecolor,
		Credencial_Color = inteamcredentialcolor
	WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = 'S') and Equipo_ID = inteamid;
    
    SELECT ROW_COUNT() into count_update;
    
    IF (count_update > 0) then
		SET status_update = CONCAT('OK, total updates: ',count_update);
	ELSE
		SET status_update = CONCAT('NO OK, total updates: ',count_update);
    END IF;
    
	CALL insertIntoControlTable(inUserName, 'UPDATE', 'TEAM', CONCAT('UPDATE Equipos SET Equipo_DESC = ''' , inteamname , ''', Activo = ' , inteamstatus , ', Fuerza = ' , inteamcategory , ', Logo = '', Equipo_FULLDESC = ''' , inteamnamelong , ''', Campo_ID = ' , inteamfield , ', Playera = ''' , inteamshirt , ''', Short = ''' , inteamshort , ''', Calcetas = ''' , inteamsoack , ''', Equipo_DESC3 = ''' , inteamname3 , ''', Institucion_ID = ' , inteaminstitution , ', Nombre_Color = ''' , inteamnamecolor , ''', Credencial_Color = ''' , IFNULL(inteamcredentialcolor, 'NULL') , ''' WHERE Torneo_ID = (SELECT Torneo_ID FROM Torneos where Actual = ''S'') and Equipo_ID = ' , inteamid , ';'), status_update);
	set out_number = count_update;
END;;

DELIMITER ;
