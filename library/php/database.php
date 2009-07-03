<?php class database {
    protected $config = array ();
    private $errorLog = array ();
    private $options = array (
        'ERROR_DISPLAY' => true
        );
    protected $sql = '';
    private $qryRes;
    protected $link;
    private $sQueryPerf = array ();

    /*******************************************************************
    * Constructeur
    * On peut ou non passer le nom de la base de données;
      si on le passe, la connexion à la base se fait d'elle même
    * Sinon, il faudra passer par la méthode select_base ()
    * @Param string $host => serveur de bdd
    * @Param string $user => login
    * @Param string $pwd => password
    * @Param string $db => base de donnée
    * @Param array $options => options (voir la propriété $config)
    *******************************************************************/
    public function __construct ($host, $user, $pwd, $db = '',
        $options = array ()) {
        $this -> config['HOST'] = $host;
        $this -> config['USER'] = $user;
        $this -> config['PWD'] = $pwd;
        if (!empty ($options)) {
            foreach ($options as $clef => $opt) {
                if (array_key_exists ($clef, $this -> options)
                    && is_bool ($opt)) {
                    $this -> options[$clef] = $opt;
                }
            }
        }
        if (!empty ($db)) {
            $this -> connect ();
            $this -> select_base ($db);
        }
    }

    /*******************************************************************
    * Méthode de connexion
    * méthode publique
    * Se fait automatiquement, ou peut être explicitement appelée
    *******************************************************************/
    public function connect () {
        if (false ===  $this -> private_connect ()) {
            $this -> error (get_class ($this).' :: connect()',
            $this -> private_errno().' : '.$this -> private_error(),
            'Connexion avec Host : '.$this -> config['HOST'].' User : '.
            $this -> config['USER'].' Pwd : ********');
        }
    }

    /*******************************************************************
    * Méthode de log des erreurs
    * méthode privée
    * @Param string $func => méthode appelant l'erreur
    * @Param string $err => message d'erreur interne au moteur de la bdd
    * @Param string $qry => requête ayant provquée l'erreur,
      ou message interne à la classe
    *******************************************************************/
    private function error ($func, $err,  $qry) {
        $this -> errorLog[] = $func.' : '.$err.' => '.$qry;
        if ($this -> options['ERROR_DISPLAY'] === true) {
            echo 'ERREUR! : ', $this -> errorLog[key ($this -> errorLog)],
                 '<br />';
        }
    }

    /*******************************************************************
    * Méthode de sélection d'une base de données
    * méthode publique
    * @Param string $name => nom de la base.
    *******************************************************************/
    public function select_base($name=null) {
        if (false === is_scalar ($name)) {
            $this -> error (get_class ($this).' :: select_base()',
            $this -> private_errno().' : '.$this -> private_error(),
            'Nom incorrect passé à la méthode : '.$name);
        } else {
            $this -> config['BD'] = $name;
             if (false === $this -> private_select_base()) {
                 $this -> error (get_class ($this).' :: select_base()',
                 $this -> private_errno().' : '.$this -> private_error(),
                 'La méthode n\'a pu se connecter à la base : '.$name);
             }
        }
    }

    /*******************************************************************
    * Méthode de fermeture de la connexion
    * méthode publique
    *******************************************************************/
    public function close() {
        $this -> __destruct ();
    }

    /*******************************************************************
    * Destructeur
    * méthode publique
    *******************************************************************/
    public function __destruct () {
        if (isset($this-> link) ) {
            $this -> private_close();
            unset ($this-> link);
        }
    }

    /*******************************************************************
    * Méthode de "requêtage"
    * méthode publique
    * @Param string $qry => requête
    *******************************************************************/
    public function query ($qry) {
        $this -> sql = $qry;
        if (false === $this -> qryRes = $this -> private_query ()) {
            $this -> error (get_class ($this).' :: query ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            $this -> sql);
        } else {
            return $this -> qryRes;
        }
    }

    /*******************************************************************
    * Méthode pour compter le nombre de lignes renvoyées
    * méthode publique
    * @Param mixed $qry => ressource d'une requête ou identifiant
      de ressource pour mssql
    * On peut la passer explicitement, ou prendre la propriété
    *******************************************************************/
    public function num_rows ($qry = null) {
        if ((get_class ($this) === 'mysql'
             && is_resource ($qry))
             || (get_class ($this) === 'mssql'
             && is_int ($qry))) {
            $num =  $this -> private_num_rows ($qry);
            if (false === $num) {
                $this -> error (get_class ($this).' :: num_rows ()',
                $this -> private_errno ().' : '.$this -> private_error (),
                $this -> sql);
                return false;
            } else {
                return $num;
            }
        }elseif (isset ($this -> qryRes)
                 && (get_class ($this) === 'mysql'
                 && is_resource ($this -> qryRes))
                 || (get_class ($this) === 'mssql'
                 && is_int ($this -> qryRes))) {
            $num =  $this -> private_num_rows ($this -> qryRes);
            if (false === $num) {
                $this -> error (get_class ($this).' :: num_rows ()',
                $this -> private_errno ().' : '.$this -> private_error (),
                $this -> sql);
                return false;
            } else {
                return $num;
            }
        } else {
            $this -> error (get_class ($this).' :: num_rows ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            'Pas de ressource valide');
        }
    }

    /*******************************************************************
    * Méthode pour parcourir les lignes renvoyée par ujne requête
      sous forme de tableau associatif
    * méthode publique
    * @Param mixed $qry => ressource d'une requête ou identifiant
      de ressource pour mssql
    * On peut la passer explicitement, ou prendre la propriété
    *******************************************************************/
    public function fetch_assoc ($qry = null) {
        if ((get_class ($this) === 'mysql'
             && is_resource ($qry))
             || (get_class ($this) === 'mssql'
             && is_int ($qry))) {
            return  $this -> private_fetch_assoc ($qry);
        }elseif (isset ($this -> qryRes)
                 && (get_class ($this) === 'mysql'
                 && is_resource ($this -> qryRes))
                 || (get_class ($this) === 'mssql'
                 && is_int ($this -> qryRes))) {
            return  $this -> private_fetch_assoc ($this -> qryRes);
        } else {
            $this -> error (get_class ($this).' :: fetch_assoc ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            'Pas de ressource valide');
        }
    }

    /*******************************************************************
    * Méthode pour parcourir les lignes renvoyée par ujne requête
      sous forme de tableau associatif et numérique
    * méthode publique
    * @Param mixed $qry => ressource d'une requête ou identifiant
      de ressource pour mssql
    * On peut la passer explicitement, ou prendre la propriété
    *******************************************************************/
    public function fetch_array ($qry = null) {
        if ((get_class ($this) === 'mysql'
             && is_resource ($qry))
             || (get_class ($this) === 'mssql'
             && is_int ($qry))) {
            return  $this -> private_fetch_array ($qry);
        }elseif (isset ($this -> qryRes)
                 && (get_class ($this) === 'mysql'
                 && is_resource ($this -> qryRes))
                 || (get_class ($this) === 'mssql'
                 && is_int ($this -> qryRes))) {
            return  $this -> private_fetch_array ($this -> qryRes);
        } else {
            $this -> error (get_class ($this).' :: fetch_array ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            'Pas de ressource valide');
        }
    }

    /*******************************************************************
    * Méthode pour parcourir les lignes renvoyée par ujne requête
      sous forme de tableau numérique
    * méthode publique
    * @Param mixed $qry => ressource d'une requête ou identifiant
      de ressource pour mssql
    * On peut la passer explicitement, ou prendre la propriété
    *******************************************************************/
    public function fetch_row ($qry = null) {
        if ((get_class ($this) === 'mysql'
             && is_resource ($qry))
             || (get_class ($this) === 'mssql'
             && is_int ($qry))) {
            return  $this -> private_fetch_row ($qry);
        }elseif (isset ($this -> qryRes)
                 && (get_class ($this) === 'mysql'
                 && is_resource ($this -> qryRes))
                 || (get_class ($this) === 'mssql'
                 && is_int ($this -> qryRes))) {
            return  $this -> private_fetch_row ($this -> qryRes);
        } else {
            $this -> error (get_class ($this).' :: fetch_row ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            'Pas de ressource valide');
        }
    }

    /*******************************************************************
    * Méthode renvoyant le dernier ID inséré
    * méthode publique
    *******************************************************************/
    public function insert_id () {
        if (isset ($this -> link)) {
            $id = $this -> private_insert_id ();
        } else {
            $this -> error (get_class ($this).' :: insert_id ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            'Pas de lien valide');
            return false;
        }
        if (false === $id) {
            $this -> error (get_class ($this).' :: insert_id ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            'Echec de récupération
             du dernier id inséré');
            return false;
        } else {
            return $id;
        }
    }

    /*******************************************************************
    * Méthode pour récupérer la valeur d'une ou plusieurs propriété(s)
      de la classe
    * méthode publique
    * On peut passer n'importe quel nombre de paramètres,
      sous la forme de chaînes ayant pour valeur le nom d'une
    * propriété EXISTANTE de la classe
    *******************************************************************/
    public function get () {
        $aArgs = func_get_args();
        foreach ($aArgs as $clef => $arg) {
            if (isset ($this -> $arg)) {
                $aRetour[$arg] = $this -> $arg;
            }
        }
        if (isset ($aRetour) && is_array ($aRetour)) {
            return $aRetour;
        } else {
            return false;
        }
    }

    /*******************************************************************
    * Méthode de "requêtage" renvoyant en plus les performances
      de la requête (bench)
    * méthode publique
    * @Param string $qry => requête
    *******************************************************************/
    public function queryPerf ($qry) {
        $this -> sql = $qry;
        $start = microtime ();
        $this -> qryRes = $this -> private_query ();
        $stop = microtime ();
        if (false === $this -> qryRes) {
            $this -> error (get_class ($this).' :: query ()',
            $this -> private_errno ().' : '.$this -> private_error (),
            $this -> sql);
            return false;
        } else {
            $elapsed = $stop - $start;
            $clef = count ($this -> sQueryPerf);
            $this -> sQueryPerf[$clef]['query'] = $this -> sql;
            $this -> sQueryPerf[$clef]['time'] = $elapsed;
            echo 'Query [', $this -> sQueryPerf[$clef]['query'], '] => ',
                            $this -> sQueryPerf[$clef]['time'], '<br />';
            return $this -> qryRes;
        }
    }
}


class mssql extends database {

    protected function private_connect () {
      if (false === $this -> link = @mssql_connect ($this -> config['HOST'],
                    $this -> config['USER'], $this -> config['PWD'])) {
            return false;
        } else {
            return true;
        }
    }

    protected function private_select_base () {
         if (false === @mssql_select_db($this->config['BD'], $this->link)) {
            return false;
         } else {
            return true;
         }
    }

    protected function private_close() {
        mssql_close($this-> link);
    }


    protected function private_query () {
        return @mssql_query ($this -> sql, $this -> link);
    }

    protected function private_num_rows ($qry) {
        return @mssql_num_rows ($qry);
    }

    protected function private_fetch_row ($qry) {
        return @mssql_fetch_row ($qry);
    }

    protected function private_fetch_array ($qry) {
        return @mssql_fetch_array ($qry);
    }

    protected function private_fetch_assoc ($qry) {
        return @mssql_fetch_array ($qry);
    }

    protected function private_insert_id () {
        $sQuery = 'SELECT @@IDENTITY';
        $requete = $this -> query ($sQuery);
        $res = $this -> fetch_row ($requete);
        return $res[0];
    }

    protected function private_errno () {
        return false;
    }

    protected function private_error () {
        return @mssql_get_last_message ();
    }
}

class mysql extends database {

    protected function private_connect () {
      if (false === $this -> link = @mysql_connect ($this -> config['HOST'],
                    $this -> config['USER'], $this -> config['PWD'])) {
            return false;
        } else {
            return true;
        }
    }

    protected function private_select_base () {
         if (false === @mysql_select_db($this->config['BD'], $this->link)) {
            return false;
         } else {
            return true;
         }
    }

    protected function private_close() {
        mysql_close($this-> link);
    }


    protected function private_query () {
        return @mysql_query ($this -> sql, $this -> link);
    }

    protected function private_num_rows ($qry) {
        return @mysql_num_rows ($qry);
    }

    protected function private_fetch_assoc ($qry) {
        return @mysql_fetch_assoc ($qry);
    }

    protected function private_fetch_array ($qry) {
        return @mysql_fetch_array ($qry);
    }

    protected function private_fetch_row ($qry) {
        return @mysql_fetch_row ($qry);
    }

    protected function private_insert_id () {
        return @mysql_insert_id ($this -> link);
    }

    protected function private_errno () {
        return @mysql_errno ($this -> link);
    }

    protected function private_error () {
        return @mysql_error ($this -> link);
    }
}?>