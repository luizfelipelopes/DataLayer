<?php

namespace src;

use PDO;
use PDOException;

/**
 * Classe responsável pela conexão com o banco de dados
 */
class Connect
{
	
	/**	 @var PDO	*/
	private static $instance;

	/**	 @var PDOException	*/
	private static $error;

	/**
	 * Recupera ou realiza a conexão com o banco de dados
	 * @return PDO
	 * @throws PDOException
	 */
	public static function getInstance(): ?PDO
	{

		if(empty(self::$instance)){

			try {

			self::$instance = new PDO(
				INFO_BD['driver'] . ':host=' . INFO_BD['host'] . ';port=' . INFO_BD['port'] . ';dbname=' . INFO_BD['dbname'], 
				INFO_BD['user'],
				INFO_BD['pass'],
				INFO_BD['attributes']
			);
				
			} catch (PDOException $exception) {
				self::$error = $exception;
			}

		}

		return self::$instance;
	}

	/**
	 * Recupera uma PDOException.
	 * @return PDOException|null
	 */
	public static function getError(): ?PDOException
	{
		return self::$error;
	}


	/**
	 * Desabilita o construtor de ser sobrescrito na herança 
	 * e de ser utilizado para instanciar um objeto.
	 */
	final private function __construct()
	{
		# code...
	}

	/**
	 * Desabilita o clone de ser sobrescrito na herança 
	 * e de ser utilizado para clonar um objeto.
	 */
	final private function __clone()
	{
		# code...
	}



}