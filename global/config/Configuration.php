<?php

class Configuration
{
    // The database connection
    public $connection;
    public $connectiona;
    public $config;

    public $liga = 'Liga Premier de Veteranos';
    public $alias = 'LPV';
	
    public $Latitude = 0;
    public $Longitude = 0;

    public $logo = 'LogoLiga';
    public $logowidth = 110;
    public $logoheight = 110;

    public $colorHeader = '#3e8e69';
    public $colorBody = '#3e8e69';
    public $colorFooter = '#d76063';

    public $font = 'Montserrat';
    public $template = '';

    public $lan = 'es';
    public $referee = 0;
    public $time = '';
    public $countHidden1 = 0;
    public $countHidden2 = 0;
    public $JuegoCedulas = 0;
    public $JornadaCedulas = 0;
    public $JugadorJugado = 0;
    public $ShowIDColumn = '';
    public $EmpatesPenales = '';
    public $EmpatesPenalesFlag = 0;
    public $ByeWeekPoints = 0;
    public $ByeWeekPointsGoals = 0;
    public $path = '';
    public $DateFormat1 = '%W, %d de %M del %Y';
    public $DateFormat2 = '%d-%m-%Y';
    public $DateFormat3 = '%d de %M del %Y';
    public $DateFormat4 = '%W, %d de %M del %Y';
    public $DateFormat5 = '%a, %d de %M del %Y';
    public $ExtraPoints = '0';
    public $RedFee = '0';
    public $RosterBirthDate = '0';
	public $dbLang = 'es_MX';
	public $unjuegosemanal = 1;
	public $tressets = 1;
    public $perfiljugador = 1;
    public $jugadoresApellidos1 = 0;
    public $juegosxnombre = 0;
    public $coachjuegos = 0;
    public $coachjuegosdiainicial = 0;
    public $coachjuegosdiafinal = 0;
    public $coachjuegoshorafinal = '00:00:00';
    public $tarjetacambios = '0';
    public $VollByeWeekSets = 0;
    public $VollByeWeekPoints = 0;
    public $VollByeWeekSetPoints = 0;
    public $Apodo = 0;
    public $BuscaCurp = 0;
    public $MultiJugador = 0;
    
	public $MAILSMTPDebug = 0;
	public $MAILSMTPAuth = 'true';
	public $MAILSMTPSecure = 'tls';
	public $MAILPort = 587;
	public $MAILHost = 'smtp.gmail.com';
	public $MAILUsername = 'clanazteca@gmail.com';
	public $MAILPassword = 'ppgykbwrrdziujyy';
	public $MAILSMTPKeepAlive = 'true';
	public $MAILMailer = 'smtp';
	public $MAILsetFrom = 'administrador@ligapremierdeveteranos.com';
	public $MAILaddReplyTo = 'administrador@ligapremierdeveteranos.com';

    /**
     * Load Config Flags
     *
     */
    public function LoadFlags()
    {
        $query = "SELECT  case when MarcadorArbitro = 1 then '' else 'hidden' end MarcadorArbitro,
                          case when MarcadorFecha = 1 then '' else 'hidden' end MarcadorFecha,
			  (case when MarcadorArbitro = 1 then 0 else 1 end) + 
			  	(case when MarcadorFecha = 1 then 0 else 1 end) + 
			  	((case when EmpatesPenales = 1 then 0 else 1 end) * 2) countHidden1,
			  ((case when EmpatesPenales = 1 then 0 else 1 end) * 2) countHidden2,
			  JuegoCedulas, JornadaCedulas,
			  case when JugadorJugado = 1 then '' else 'hidden' end JugadorJugado,
			  LeagueName,
			  case when ShowIDColumn = 1 then '' else 'hidden' end ShowIDColumn,
			  case when EmpatesPenales = 1 then '' else 'hidden' end EmpatesPenales,
			  EmpatesPenales EmpatesPenalesFlag,
			  ByeWeekPoints, ByeWeekPointsGoals, Latitude, Longitude,
			  UnJuegoSemana, TresSets, PerfilJugadores, JugadoresApellidos1,JuegosxNombre,
			  CoachJuegos, CoachJuegosDiaInicial, CoachJuegosDiaFinal, CoachJuegosHoraFinal,
			  TarjetaCambios, VollByeWeekSets, VollByeWeekPoints, VollByeWeekSetPoints, Apodo, BuscaCurp, MultiJugador
		  FROM " . $this->config["schema"] . ".Configuration";
        $result = $this->query($query);
        if (!$result){
            return null;
	}
        while ($row2 = $result->fetch_assoc()) {
            $this->referee = $row2["MarcadorArbitro"];
            $this->time = $row2["MarcadorFecha"];
            $this->countHidden1 = $row2["countHidden1"];
            $this->countHidden2 = $row2["countHidden2"];
            $this->JuegoCedulas = $row2["JuegoCedulas"];
            $this->JornadaCedulas = $row2["JornadaCedulas"];
            $this->JugadorJugado = $row2["JugadorJugado"];
            $this->liga = $row2["LeagueName"];
            $this->ShowIDColumn = $row2["ShowIDColumn"];
            $this->EmpatesPenales = $row2["EmpatesPenales"];
            $this->ByeWeekPoints = $row2["ByeWeekPoints"];
            $this->ByeWeekPointsGoals = $row2["ByeWeekPointsGoals"];
            $this->Latitude = $row2["Latitude"];
            $this->Longitude = $row2["Longitude"];
	        $this->unjuegosemanal = $row2["UnJuegoSemana"];
            $this->tressets = $row2["TresSets"];
            $this->perfiljugador = $row2["PerfilJugadores"];
            $this->EmpatesPenalesFlag = $row2["EmpatesPenalesFlag"];
            $this->jugadoresApellidos1 = $row2["JugadoresApellidos1"];
            $this->juegosxnombre = $row2["JuegosxNombre"];
            $this->coachjuegos = $row2["CoachJuegos"];
            $this->coachjuegosdiainicial = $row2["CoachJuegosDiaInicial"];
            $this->coachjuegosdiafinal = $row2["CoachJuegosDiaFinal"];
            $this->coachjuegoshorafinal = $row2["CoachJuegosHoraFinal"];
            $this->tarjetacambios = $row2["TarjetaCambios"];
            $this->VollByeWeekSets = $row2["VollByeWeekSets"];
            $this->VollByeWeekPoints = $row2["VollByeWeekPoints"];
            $this->VollByeWeekSetPoints = $row2["VollByeWeekSetPoints"];
            $this->Apodo = $row2["Apodo"];
            $this->BuscaCurp = $row2["BuscaCurp"];
            $this->MultiJugador = $row2["MultiJugador"];
       }
       return $this->template;
    }

    /**
     * Load Aviso Template
     *
     */
    public function LoadAvisoTemplate()
    {
        // Try and connect to the database
        $query = "SELECT * FROM " . $this->config["schema"] . ".Configuration";
        $result = $this->query($query);
        if (!$result)
            return null;
        while ($row2 = $result->fetch_assoc()) {
            $this->template = $row2["AvisosTemplete"];
        }
        return $this->template;
    }

    /**
     * Load language info
     *
     */
    public function LoadLanguage()
    {
        // Try and connect to the database
        $query = "SELECT * FROM " . $this->config["schema"] . ".Configuration";
		//echo $query;
        $result = $this->query($query);
        if (!$result)
            return null;
        while ($row2 = $result->fetch_assoc()) {
            $this->lan = $row2["Idioma"];
        }
    }

    /**
     * Load logo info
     *
     */
    public function LoadRegionalSettings()
    {
        // Try and connect to the database
        $query = "SELECT * FROM " . $this->config["schema"] . ".Lenguaje
			            where Lenguaje_ID = '" . $_COOKIE[$this->getAlias() . 'language'] . "'";
        $result = $this->query($query);
        if (!$result)
            return null;
        while ($row2 = $result->fetch_assoc()) {
            $this->DateFormat1 = $row2["DateFormat1"];
            $this->DateFormat2 = $row2["DateFormat2"];
            $this->DateFormat3 = $row2["DateFormat3"];
            $this->DateFormat4 = $row2["DateFormat4"];
            $this->DateFormat5 = $row2["DateFormat5"];
			$this->dbLang = $row2["LenguajeDB"];
        }
						
		$query = "SET lc_time_names = '" . $this->dbLang . "';";
		$this->query($query);
    }

    /**
     * Load logo info
     *
     */
    public function LoadLogo()
    {
        // Try and connect to the database
        $query = "SELECT * FROM " . $this->config["schema"] . ".Configuration";
        $result = $this->query($query);
        if (!$result)
            return null;
        while ($row2 = $result->fetch_assoc()) {
            $this->logo = $row2["Logo"];
            $this->logowidth = $row2["LogoX"];
            $this->logoheight = $row2["LogoY"];
            $this->colorHeader = $row2["ColorHeader"];
            $this->colorBody = $row2["ColorBody"];
            $this->colorFooter = $row2["ColorFooter"];
        }
    }

    /**
     * Connect to the database
     *
     * @return null on failure / mysqli MySQLi object instance on success
     */
    public function connect()
    {
        if (!is_array($this->config)) {
            return null;
        }
        if (!isset($this->connection)) {
            $server = $this->config['servername'] ?? '';
            $user = $this->config['username'] ?? '';
            $pass = $this->config['password'] ?? '';
            $db = $this->config['schema'] ?? '';
            if ($server === '' || $user === '' || $db === '') {
                return null;
            }
            try {
                $connection = new mysqli($server, $user, $pass, $db);
            } catch (\Throwable $e) {
                return null;
            }
            if ($connection->connect_error) {
                return null;
            }
            $this->connection = $connection;
        }
        return $this->connection;
    }

    /**
     * Connect to the database
     *
     * @return null on failure / mysqli MySQLi object instance on success
     */
    public function connectAdmin()
    {
        if (!is_array($this->config)) {
            return null;
        }
        if (!isset($this->connectiona)) {
            $server = $this->config['servername'] ?? '';
            $user = $this->config['usernamea'] ?? '';
            $pass = $this->config['passworda'] ?? '';
            $db = $this->config['schemaa'] ?? '';
            if ($server === '' || $user === '' || $db === '') {
                return null;
            }
            try {
                $connectiona = new mysqli($server, $user, $pass, $db);
            } catch (\Throwable $e) {
                return null;
            }
            if ($connectiona->connect_error) {
                return null;
            }
            $this->connectiona = $connectiona;
        }
        return $this->connectiona;
    }

    /**
     * Connect to the database
     *
     * @return null on failure / mysqli MySQLi object instance on success
     */
    public function connectFG()
    {
        if (!is_array($this->config)) {
            return false;
        }
        $server = $this->config['servername'] ?? '';
        $user = $this->config['usernamea'] ?? '';
        $pass = $this->config['passworda'] ?? '';
        $db = $this->config['schemaa'] ?? '';
        if ($server === '' || $user === '' || $db === '') {
            return false;
        }
        try {
            $connection = mysqli_connect($server, $user, $pass, $db);
        } catch (\Throwable $e) {
            return false;
        }
        if ($connection === false || $connection->connect_error) {
            return false;
        }
        return $connection;
    }

    /**
     * Query the database
     *
     * @param $query - query string
     * @return mixed The result of the mysqli::query() function
     */
    public function query($query)
    {
        // Connect to the database
        $connection = $this->connect();
        if (!$connection) {
            return null;
        }
        $connection->set_charset('utf8');
        if (!empty($_COOKIE[$this->getAlias() . 'language'])) {

            $sql = "SELECT LenguajeDB 
                    FROM " . $this->config["schema"] . ".Lenguaje 
                    WHERE Lenguaje_ID = '" . $_COOKIE[$this->getAlias() . 'language'] . "'";
            $result = $connection->query($sql);
            if ($result) {
                while ($row2 = $result->fetch_assoc()) {
                    $connection->query("SET lc_time_names = '" . $row2["LenguajeDB"] . "';");
                }
            }
        }

        // Query the database
        $connection->query("SET NAMES 'utf8';");
        $result = $connection->query($query);

        return $result;
    }

    /**
     * Query the database
     *
     * @param $query - query string
     * @return mixed The result of the mysqli::query() function
     */
    public function queryAdmin($query)
    {
        // Connect to the database
        $connection = $this->connectAdmin();
        if (!$connection) {
            return null;
        }
        $connection->set_charset('utf8');
        if (!empty($_COOKIE[$this->getAlias() . 'language'])) {

            $sql = "SELECT LenguajeDB 
                    FROM " . $this->config["schema"] . ".Lenguaje 
                    WHERE Lenguaje_ID = '" . $_COOKIE[$this->getAlias() . 'language'] . "'";
            $result = $connection->query($sql);
            if ($result) {
                while ($row2 = $result->fetch_assoc()) {
                    $connection->query("SET lc_time_names = '" . $row2["LenguajeDB"] . "';");
                }
            }
        }

        // Query the database
        $connection->query("SET NAMES 'utf8';");
        $result = $connection->query($query);

        return $result;
    }

    /**
     * Close the connection
     */
    public function Close()
    {
        if (isset($this->connection) && $this->connection instanceof mysqli) {
            $this->connection->close();
        }
    }

        public function getPath(){
                return is_array($this->config) ? ($this->config["path"] ?? '') : '';
        }

 	public function getSport(){
		return is_array($this->config) ? ($this->config["sport"] ?? '') : '';
	}

	public function getSchema(){
		return is_array($this->config) ? ($this->config["schema"] ?? '') : '';
	}
	
	public function getAlias(){
		return is_array($this->config) ? ($this->config["alias"] ?? '') : '';
	}
	
    public function getAdminEmail(){
		return is_array($this->config) ? ($this->config["adminemail"] ?? '') : '';
	}
	
    public function getWebSite(){
		return is_array($this->config) ? ($this->config["website"] ?? '') : '';
	}
	
	public function getEmailConnectionInfo(){
	    if (!is_array($this->config)) {
	        return;
	    }
		$this->MAILSMTPDebug = $this->config["MAILSMTPDebug"];
		$this->MAILSMTPAuth = $this->config["MAILSMTPAuth"];
		$this->MAILSMTPSecure = $this->config["MAILSMTPSecure"];
		$this->MAILPort = $this->config["MAILPort"];
		$this->MAILHost = $this->config["MAILHost"];
		$this->MAILUsername = $this->config["MAILUsername"];
		$this->MAILPassword = $this->config["MAILPassword"];
		$this->MAILSMTPKeepAlive = $this->config["MAILSMTPKeepAlive"];
		$this->MAILMailer = $this->config["MAILMailer"];
		$this->MAILsetFrom = $this->config["MAILsetFrom"];
		$this->MAILaddReplyTo = $this->config["MAILaddReplyTo"];
	}
	
    /**
     * Fetch rows from the database (SELECT query)
     *
     * @param $query - query string
     * @return null on failure / array Database rows on success
     */
    public function select($query)
    {
        $rows = array();
        $result = $this->query($query);
        if (!$result)
            return null;
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Fetch the last error from the database
     *
     * @return string Database error message
     */
    public function error()
    {
        $connection = $this->connect();
        if (!$connection) {
            return '';
        }
        return $connection->error;
    }

    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    public function quote($value)
    {
        $connection = $this->connect();
        if (!$connection) {
            return "''";
        }
        return "'" . $connection->real_escape_string($value) . "'";
    }

    /**
     * Configuration constructor.
     * Parse the config.ini into config member of Configuration object
     */
    function __construct()
    {
        $tmp = getcwd();
        $iniFile = null;
        $candidates = [];
        if (defined('APP_INI_FILE') && is_string(APP_INI_FILE)) {
            $candidates[] = APP_INI_FILE;
        }
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $candidates[] = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DIRECTORY_SEPARATOR . 'ini' . DIRECTORY_SEPARATOR . 'config.ini';
        }
        if (!empty($_SERVER['SCRIPT_FILENAME'])) {
            $candidates[] = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR . 'ini' . DIRECTORY_SEPARATOR . 'config.ini';
        }
        foreach ($candidates as $path) {
            if ($path !== '' && is_readable($path)) {
                $iniFile = $path;
                break;
            }
        }
        if ($iniFile === null && !empty($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
            chdir($_SERVER['DOCUMENT_ROOT']);
            if (is_dir('ini')) {
                chdir('ini');
                $iniFile = 'config.ini';
            }
        }
        if ($iniFile === null) {
            if ($tmp !== false) {
                chdir($tmp);
            }
            throw new RuntimeException('Unable to locate ini/config.ini (DOCUMENT_ROOT / SCRIPT_FILENAME / APP_INI_FILE).');
        }
        $parsed = parse_ini_file($iniFile);
        if ($parsed === false) {
            if ($tmp !== false) {
                chdir($tmp);
            }
            throw new RuntimeException('Failed to parse ini file (check comment lines use ; not #): ' . $iniFile);
        }
        $this->config = $parsed;
        if ($tmp !== false) {
            chdir($tmp);
        }
    }
}

?>
