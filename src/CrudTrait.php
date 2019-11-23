<?php 

namespace src;

use DateTime;
use PDO;
use PDOException;
use Exception;

/**
 * Trait responsável pelo Cadastro, Atualização e Exclusão 
 * de Registros na based de dados.
 * Trait responsible for Registration, Update and Deletion 
 * of Records in the database.
 */
trait CrudTrait
{

	/**
	 * Cria um novo registro na base de dados.
	 * Creates a new record in the database.
	 * @param array $data 
	 * @return int|null
	 * @throws PDOException
	 */
	protected function create(array $data): ?int
	{

		if($this->timestamps){

			$data['created_at'] = (new DateTime("now"))->format('Y-m-d H:i:s');
			$data['updated_at'] = $data['created_at'];

		}	

		try {

			$columns = implode(', ', array_keys($data));
			$values = ':' . implode(', :', array_keys($data));

			$stmt = Connect::getInstance()->prepare("INSERT INTO {$this->entity} ({$columns}) VALUES ({$values})");
			$stmt->execute($this->filter($data));

			return Connect::getInstance()->lastInsertId();

		} catch (PDOException $exception) {

			$this->fail = $exception;

			return null;

		}	


	}

	/**
	 * Atualiza um registro na base de dados.
	 * Updates a record in the database.
	 * @param array $data 
	 * @param string $terms 
	 * @param string $params 
	 * @return int|null
	 * @throws PDOException
	 */
	protected function update(array $data, string $terms, string $params): ?int
	{

		if($this->timestamps){

			$data['updated_at'] = (new DateTime("NOW"))->format('Y-m-d H:i:s');

		}

		try {
			
			$dataSet = [];

			foreach ($data as $key => $value) {
				
				$dataSet[] = "{$key} = :{$key}";

			}

			$dataSet = implode(', ', $dataSet);
			parse_str($params, $params);

			$stmt = Connect::getInstance()->prepare("UPDATE {$this->entity} SET {$dataSet} WHERE {$terms}");
			$stmt->execute($this->filter(array_merge($data, $params)));

			return ($stmt->rowCount() ?? 1); 

		} catch (PDOException $exception) {
			
			$this->fail = $exception;

			return null;

		}
	}

	/**
	 * Deleta um registro na base de dados.
	 * Deletes a record in the database.
	 * @param string $terms 
	 * @param ?string $params 
	 * @return bool
	 * @throws PDOException
	 */
	protected function delete(string $terms, ?string $params): bool
	{

		try {
			
			$stmt = Connect::getInstance()->prepare("DELETE FROM {$this->entity} WHERE {$terms}");

			if($params){

				parse_str($params, $params);
				$stmt->execute($params);

				return true;
			}

			$stmt->execute();

			return true;

		} catch (PDOException $exception) {
			
			$this->fail = $exception; 

			return false;
			
		}

	}

	/**
	 * Filtra os dados a serem salvos na base de dados.
	 * Filters the data to be saved in the database.
	 * @param array $data 
	 * @return array
	 */
	private function filter(array $data): ?array
	{
	
		$filter = [];

		foreach ($data as $key => $value) {
			
			$filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));

		}

		return $filter;

	}



}
