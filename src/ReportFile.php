<?php

namespace SiNReports;

/**
 * Classe que trata o arquivo .sin
 */
class ReportFile
{
	/**
	 * Versão do arquivo
	 * 
	 * @var string
	 */
	protected $versao;

	/**
	 * Arquivo temporario
	 * 
	 * @var string
	 */
	protected $tempfile;

	/**
	 * Nome do relatório
	 * 
	 * @var string
	 */
	protected $nome;

	/**
	 * Vetor de parametros
	 * 
	 * @var string
	 */
	protected $parametros;

	/**
	 * Vetor de queries
	 * 
	 * @var string
	 */
	protected $queries;

	/**
	 * construtor
	 */
	public function __construct($filepath, $parametros)
	{
		// se o arquivo
		$ziped = file_get_contents($filepath);

		// descompacta
		$base64 = gzdecode($ziped);

		// descriptografa
		$json = base64_decode($base64);

		// convert o json
		$data = json_decode($json, TRUE);

		// salva os dados basicos
		$this->versao = $data['version'];
		$this->nome = $data['name'];

		// salva o html
		$this->tempfile = sys_get_temp_dir() . "/sinreports/tpl_compiled/" . uniqid();
		file_put_contents($this->tempfile, $data['html']);

		// salva os parametros
		foreach($data['parameters'] as $parametro) {
			$this->parametros[$parametro['nome']] = $parametro['padrao'];
		}

		// percorre as queries
		foreach($data['data'] as $dados) {
			$sql = $dados['query'];

			// verifica quais parametros tem na query
			$query = $dados['query'];
			preg_match_all('/(?<!\w)@([a-zA-Z0-9_]+)(?:\|([a-zA-Z0-9_]+))?/', $query, $output_array, PREG_SET_ORDER);

			$query_parametros = [];
			foreach($output_array as $parametro) {

				$parametro_nome = $parametro[1];
				if(strlen($parametro_nome??"") == 0) {
					continue;
				}
				$valor = $parametros[$parametro_nome]??NULL;

				// verifica se o parametro existe
				if($valor == NULL) {
					$valor = $this->parametros[$parametro_nome]??NULL;
				}

				// se o parametro não existe
				if($valor == NULL) {
					throw new \Exception("Parametro \"" . $parametro_nome . "\" não foi setado e não possui valor padrão.");
				}

				// troca a query e armazena o valor do parametro
				$query = str_replace($parametro[0], ":" . $parametro_nome, $query);
				$query_parametros[$parametro_nome] = $valor;
			}

			// salva na variavel global
			$this->queries[] = [
				'sql' => $query,
				'nome' => $dados['nome'],
				'parametros' => $query_parametros
			];
		}

	}

	/**
	 * Recupera o arquivo temporario do tpl
	 */
	public function getTemplateFile()
	{
		return $this->tempfile;
	}

	/**
	 * Recupera as queries
	 */
	public function getQueries()
	{
		return $this->queries;
	}
}