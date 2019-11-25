<?php 

namespace src;

use stdClass;
use PDO;
use PDOException;
use Exception;

/**
 * Class DataLayer
 * Patterns used: Layer Super Type and Active Record.
 */
abstract class DataLayer
{

	use CrudTrait;
	
	/**
	 * @var string
	 */
	private $entity; 
	
	/**
	 * @var array
	 */
	private $required; 
	
	/**
	 * @var string
	 */
	private $primary;

	/**
	 * @var bool
	 */
	private $timestamps;

	/**
	 * @var array
	 */
	private $params;

	/**
	 * @var string
	 */
	private $statement;

	/**
	 * @var string
	 */
	private $group; 

	/**
	 * @var string
	 */
	private $order; 

	/**
	 * @var string
	 */
	private $offset; 

	/**
	 * @var string
	 */
	private $limit;

	/**
	 * @var PDOException
	 */
	private $fail;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * Construtor inicializa a tabela, os campos obrigatórios, a chave primária 
	 * e timestamps a serem manipulados.
	 * Constructor initializes the table, required fields, primary key, and timestamps to be handled.
	 * @param string $entity 
	 * @param array $required 
	 * @param string|string $primary 
	 * @param bool|bool $timestamps 
	 */
	public function __construct(string $entity, array $required, string $primary = 'id', bool $timestamps = true)
	{
		
		$this->entity = $entity;
		$this->required = $required;
		$this->primary = $primary;
		$this->timestamps = $timestamps;

	}

	/**
	 * [MÉTODOS MÁGICOS / MAGIC METHODS]
	 * Permite a insersão de valores às propriedades 
	 * encapsuladas na classe que modela a base de dados.
	 * Allows the insertion of values to the encapsulated 
	 * properties in the class that models the database.
	 * @param type $name 
	 * @param type $value 
	 */
	public function __set($name, $value)
	{

		if(is_array($value)){
			
			$this->data = (object)$value;			

		}

		if(empty($this->data)){

			$this->data = new stdClass();

		}

		if(!is_array($value)){

			$this->data->$name = $value;

		}

	}

	/**
	 * [MÉTODOS MÁGICOS / MAGIC METHODS]
	 * Permite o acesso às propriedades encapsuladas na classe 
	 * que modela a base de dados.
	 * Allows access to encapsulated properties in the class that 
	 * models the database.
	 * @param type $name 
	 */
	public function __get($name)
	{

		return ($this->data->$name ?? null);

	}

	/**
	 * [MÉTODOS MÁGICOS / MAGIC METHODS]
	 * Verifica a existência de uma determinada propriedade
	 * da objeto que instancia a classe modelo da base de dados.
	 * Checks for a given object property that instantiates the database model class.
	 * @param type $name 
	 * @param type $value 
	 * @return bool
	 */
	public function __isset($name)
	{

		return isset($this->data->$name);

	}

	/**
	 * Retorna os dados do objeto.
	 * Returns the data of the object.
	 * @return type
	 */
	public function data()
	{

		return $this->data;

	}

	/**
	 * Retorna uma exception do PDO
	 * Returns an exception from PDO
	 * @return PDOException|null
	 */
	public function fail(): ?PDOException
	{

		return $this->fail;

	}

	/**
	 * Inicializa o statment da consulta ao banco de dados.
	 * Initializes the statment of the database query.
	 * @param string|null $terms 
	 * @param string|null $params 
	 * @param string|string $columns 
	 * @return DataLayer
	 */
	public function find(string $terms = null, string $params = null, string $columns = '*'): DataLayer
	{
		
		if($params){

			parse_str($params, $this->params);

		}

		if($terms){

			$this->statement = "SELECT {$columns} FROM {$this->entity} WHERE {$terms}";

			return $this;

		}

			$this->statement = "SELECT {$columns} FROM {$this->entity}";

			return $this;
			
	}

	/**
	 * Retorna um objeto com os valores do registro encontrado no
	 * banco de dados de acordo com ID passado por parâmetro.
	 * Returns an object with the registry values found in the database 
	 * according to ID passed by parameter.
	 * @param int $id 
	 * @param string|string $columns 
	 * @return object
	 */
	public function findById(int $id, string $columns = '*')
	{

		$this->find($this->primary . " = :id", "id={$id}", $columns);

		return $this->fetch();

	}

	/**
	 * Recupera o número de registros encontrados de acordo 
	 * com a consulta.
	 * Retrieves the number of records found according to the query.
	 * @return int
	 */
	public function count(): int
	{

		$stmt = Connect::getInstance()->prepare($this->statement);
		
		if($this->params){

			$stmt->execute($this->params);
			return $stmt->rowCount();

		}

		$stmt->execute();
		return $stmt->rowCount();

	}

	/**
	 * Acrescenta ao statment a opção de agrupamento.
	 * Adds the grouping option to statment.
	 * @param string $column 
	 * @return DataLayer
	 */
	public function group(string $column): DataLayer
	{
		
		$this->group = " GROUP BY {$column}";

		return $this;

	}

	/**
	 * Acrescenta ao statment a opção de ordenação.
	 * Adds to the statment the sort option.
	 * @param string $column 
	 * @return DataLayer
	 */
	public function order(string $column): DataLayer
	{
		
		$this->order = " ORDER BY {$column}";

		return $this;

	}

	/**
	 * Acrescenta ao statment o limite de registros retornados.
	 * Adds to statment the limit of records returned.
	 * @param string $column 
	 * @return DataLayer
	 */
	public function limit(int $limit): DataLayer
	{
		
		$this->limit = " LIMIT {$limit}";

		return $this;

	}

	/**
	 * Acrescenta ao statment o ponto inicial da consulta ao
	 * banco de dados.
	 * Adds to the statment the starting point of the database query.
	 * @param string $column 
	 * @return DataLayer
	 */
	public function offset(int $offset): DataLayer
	{
		
		$this->offset = " OFFSET {$offset}";

		return $this;

	}

	/**
	 * Executa o statment e retorna os resultados como objeto
	 * da classe modelo do banco de dados.
	 * Performs statment and returns the results as object of the 
	 * database model class.
	 * @param bool|bool $all 
	 * @throws PDOException
	 */
	public function fetch(bool $all = false)
	{
		
		try {

			$stmt = Connect::getInstance()->prepare("{$this->statement}{$this->group}{$this->order}{$this->limit}{$this->offset}");
				
			$stmt->execute($this->params);

			if(!$stmt->rowCount()){

				return null;

			}

			if($all){

				return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);

			}

			return $stmt->fetchObject(static::class);
			
		} catch (PDOException $exception) {
			
			$this->fail = $exception;
			
			return null;
		}

	}

	/**
	 * Cria ou atualiza um registro no banco de dados.
	 * Creates or updates a record in the database.
	 * @throws Exception
	 */
	public function save()
	{
		
		$primary = $this->primary;

		if(!$this->required()){

			throw new Exception("Preencha todos os campos!");
		}

		if(empty($this->data->$primary)){

			return $this->create($this->safe((array) $this->data));
		}

		if(!empty($this->data->$primary)){

			$id = $this->data->$primary;

			return $this->update($this->safe((array) $this->data), $this->primary . " = :id", "id={$id}");	

		}

	}

	/**
	 * Deleta um registro na base de dados.
	 * Delete a record in the database.
	 * @return bool
	 */
	public function destroy(): bool
	{
		
		$primary = $this->primary;
		$id = $this->data->$primary;

		if(!$id){

			return false;

		}

		return $this->delete($this->primary . " = :id", "id={$id}");

	}

	/**
	 * Verifica se os campos requeridos estão preenchidos.
	 * Checks if the required fields are filled.
	 * @return bool
	 */
	private function required(): bool
	{

		foreach ($this->required as $value) {

			if(empty($this->data->$value)){

				return false;

			}
		}

		return true;
	}

	/**
	 * Previne erros de segurança eliminando a chave primaria
	 * do array com os dados a serem salvos.
	 * Prevents security errors by eliminating the primary key 
	 * from the array with the data to be saved.
	 * @param array $data 
	 * @return array|null
	 */
	private function safe(array $data): ?array
	{
		$primary = $this->primary;
		
		$this->data = (array) $this->data;

		if(isset($this->data[$primary])){

			unset($this->data[$primary]);

		}

		return $this->data;
	}

}