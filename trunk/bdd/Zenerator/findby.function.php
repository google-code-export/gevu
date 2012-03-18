	/**
     * Recherche une entrée %class% avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param %type% $%column%
     *
     * @return array
     */
    public function findBy%Column%($%column%)
    {
        $query = $this->select()
                    ->from( array("%ftable%" => "%table%") )                           
                    ->where( "%ftable%.%column% = ?", $%column% );

        return $this->fetchAll($query)->toArray(); 
    }
    