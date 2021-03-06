<?php
class Estocador
{
	private $conn;

	function __construct()
	{
		$this->conn = new PDO("mysql:server=localhost;dbname=db_stox", "root", "");
		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativa os erros de SQL

		define("ERR_BARCODE_NOT_FOUND", 001);
		define("ERR_SUPPLIER_ID_NOT_FOUND", 002);
		define("ERR_SQL", 003);
		define("ERR_NO_FIELDS", 004);
	}

	// ---------- Essas são funções de rotina, executadas sem interação direta do usuário ---------- 
	private function vrfCodbarrasProduto($cod)
	{

		$query = $this->conn->prepare("SELECT cod_barras FROM tb_tipo_produto WHERE cod_barras = :cod");
		$query->bindValue(":cod", $cod);

		try
		{ 
			$query->execute(); 
		}
		catch(PDOException $e) { return ERR_SQL; }

		if($query->rowCount() >= 1)
		{
			$result = $query->fetchAll();
			return $result[0];
		}
		else
		{
			return false;
		}
	}

	private function getIdFornecedor($name)
	{
		$query = $this->conn->prepare("SELECT cd_fornecedor FROM tb_fornecedor WHERE nm_fornecedor = :name");
		$query->bindValue(":name", $name);
		
		try
		{ 
			$query->execute(); 
		}
		catch(PDOException $e) { return ERR_SQL; }

		if($query->rowCount() >= 1)
		{
			while($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				return $row['cd_fornecedor'];
			}
		}
		else
		{
			return false;
		}
	}

	private function vrfCampos($post)
	{	
		$campos = ['cod_barras' => 0, 'qtd' => 0, 'validade' => 0, 'fornecedor' => 0];
		$todosVazios = true;

		foreach( $campos as $nome => $valor)
		{
			if( !empty($post[$nome]) )
			{
				$campos[$nome] = 1;
				$todosVazios = false;
			}
		}
		return $todosVazios ? $todosVazios : $campos;
	}

	// ---------- Fim das funções de rotina ---------- 
	//////////////////////////////////////////////////
	/////////////////////////////////////////////////
	// ---------- Essas são funções do lote ---------- 
	
	public function receberLote($cod, $qt, $val, $supp) // Usa o nome do produto e do fornecedor pra encontrar os ids
	{
		// Caso não encontre o código de barras, retorna um erro
		$barcode = $this->vrfCodbarrasProduto($cod);
		if( !$barcode ) 
			return ERR_BARCODE_NOT_FOUND;
		else if ( $barcode == ERR_SQL )
			return ERR_SQL;

		// Caso não encontre o ID do fornecedor, retorna um erro
		$idSupp = $this->getIdFornecedor($supp); 
		if( !$idSupp ) 
			return ERR_SUPPLIER_ID_NOT_FOUND;
		else if ( $idSupp == ERR_SQL )
			return ERR_SQL;

		$query = $this->conn->prepare("INSERT INTO tb_lote VALUES (NULL, :cod, :qtd, :validade, :fornecedor)");
		$query->bindValue(':cod', $barcode);
		$query->bindValue(':qtd', $qt);
		$query->bindValue(':validade', $val);
		$query->bindValue(':fornecedor', $idSupp);

		try
		{ 
			$query->execute(); 
		}
		catch(PDOException $e) { return ERR_SQL; }
		
	}

	public function alterarLote($post)
	{
		$vrf = $vrfCampos($post);
		if( !$vrf )
			return ERR_NO_FIELDS;
		
		$query = $this->conn->prepare();
	}

	public function deletarLote()
	{
		$query = $this->conn->prepare();
	}

	public function listarLotes()
	{
		$query = $this->conn->prepare();
	}

	// ---------- Fim das funções do lote -----------
	/////////////////////////////////////////////////
	/////////////////////////////////////////////////
	// ---------- Essas são funções dos produtos ----

	public function moverProduto()
	{
		$query = $this->conn->prepare();
	}
	// ---------- Fim das funções sobre os produtos ---------- 
}