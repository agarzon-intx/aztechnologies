-- Footer logos and social media links (Configuration → General).
-- Run in each site schema (demomina, elite, huskies, huskiesb, lidep, nuestrodeporte, vollidep, voleibolmetepec, voleymvp).
-- Site admin users lack CREATE: grant it temporarily through cPanel uapi and restore afterwards.

CREATE TABLE IF NOT EXISTS FooterLogos (
	Logo_ID int NOT NULL AUTO_INCREMENT,
	Orden int NOT NULL DEFAULT 0,
	Alias varchar(100) NOT NULL DEFAULT '',
	Url varchar(500) NOT NULL DEFAULT '',
	Ancho int NOT NULL DEFAULT 0,
	Imagen mediumblob,
	Mime varchar(40) NOT NULL DEFAULT 'image/png',
	Actualizado timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (Logo_ID),
	KEY idx_footerlogos_orden (Orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS RedesSociales (
	Red_ID int NOT NULL AUTO_INCREMENT,
	Orden int NOT NULL DEFAULT 0,
	Tipo varchar(20) NOT NULL,
	Alias varchar(100) NOT NULL DEFAULT '',
	Url varchar(500) NOT NULL DEFAULT '',
	PRIMARY KEY (Red_ID),
	KEY idx_redessociales_orden (Orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
