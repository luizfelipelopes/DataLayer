<?php

namespace src\Models;

use src\DataLayer;

/**
 * Classe responsável por modelar a tabela users 
 * na base de dados.
 * Class responsible for modeling the users table in the database.
 */
class User extends DataLayer
{
	private const entity = 'users';
	
	/**
	 * Inicializa informações necessárias para a comunicação
	 * da base de dados.
	 * (tabela, campos requeridos, chave primária, timestamps)
	 * Initializes information needed for database communication 
	 * (table, required fields, primary key, timestamps)
	 */
	public function __construct()
	{
		parent::__construct(self::entity, ["first_name", "last_name", "genre"], "id", true);
	}

}