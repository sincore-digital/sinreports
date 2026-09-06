<?php

namespace SiNReports\Formats;

/**
 * Classe que trata a renderização final no formato HTML
 */
class Html implements FormatInterface
{
	/**
	 * Armazena o config enviado no construtor
	 */
	protected array $config;

	/**
	 * Armazena o arquivo do template
	 */
	protected string $template;

	/**
	 * Armazena o html final
	 */
	protected string $html;

	/**
	 * Armazena o vetor de variaveis
	 */
	protected array $vars;

	/**
	 * Armazena o objeto \Smarty\Smarty
	 */
	private \Smarty $smarty;

	/**
	 * Construtor da classe
	 * 
	 * @param array $config
	 * @param array $vars
	 */
	public function __construct(array $config, string $template, array $vars)
	{
		// salva as variaveis
		$this->config = $config;
		$this->template = $template;
		$this->vars = $vars;

		// cria e configura objeto smarty
		$this->smarty = new \Smarty();
		$this->smarty->setForceCompile($config['smarty']['force_compile']);
		$this->smarty->setDebugging($config['smarty']['debugging']);
		$this->smarty->setCacheDir($config['smarty']['cache_dir']);
		$this->smarty->setCaching($config['smarty']['caching']);
		$this->smarty->setCacheLifetime($config['smarty']['cache_lifetime']);
		$this->smarty->setCompileDir($config['smarty']['compile_dir']);
		if($config['smarty']['compile_check']) {
			$this->smarty->setCompileCheck(\Smarty::COMPILECHECK_ON);
		}
		else {
			$this->smarty->setCompileCheck(\Smarty::COMPILECHECK_OFF);
		}

		// native PHP functions
		$natives = [
			"strtoupper", "strtolower", "str_replace", "ucfirst", "ucwords", "sprintf", "lcfirst", "ltrim", "rtrim", "trim", 
			"constant",
			"nl2br",
			"file_exists",
			"stripos", "strpos", "strlen", 
			"explode", "implode", "array_map", "array_reverse", "count",
			"number_format", "intval", "floatval", "is_numeric", 
			"strtotime", "date", "time",
			"dechex", "htmlspecialchars", "urlencode", "urldecode",
			"var_dump", "asort",
			"md5", "base64_encode", "base64_decode", "rand",
			"json_encode", "json_decode",
			"get_class",
		];
		foreach($natives as $native => $value) {
			$this->smarty->registerPlugin("modifier", $value, $value);
		}

		// monta a configuração dos grupos
		$groups = [];
		foreach($this->vars['dataset_groups'] as $group) {
			
			foreach($group['fields'] as $field) {
				$group['result_fields'][$field] = 0;
			}

			$groups[] = $group;
		}

		// tentativa de processar a tabela aqui, ja fazendo as formatações, agrupamentos, etc
		$data = [];
		$data_header = [];
		foreach($this->vars['dataset'] as $row_index => $row) {

			$final_row = [];

			// percorre as colunas
			foreach($row as $column => $value) {

				// verifica se tem configuração da colun
				$config = NULL;
				if($this->vars['dataset_column_configs'][$column]) {
					$config = $this->vars['dataset_column_configs'][$column];
				}

				// verifica se deve esconder
				if($config['hide']??FALSE) {
					continue;
				}

				// verifica se o header ja foi montado
				if(!isset($data_header[$column])) {
					$data_header[$column] = $config['title']??$column;
				}

				// verifica a formatação
				$value = $this->formatColumn($value, $config);

				// adiciona a coluna à linha
				$final_row[$column] = $value;

			}

			// adiciona a linha ao vetor final
			$data[] = $final_row;

			// percorre os grupos
			$resetando = FALSE;
			$group_data = [];
			foreach($groups as $group_index => $grupo) {
				// inicia o label do grupo
				$group_label = $grupo['label']??"";

				// percorre os fields para calculo
				foreach($grupo['fields'] as $group_field => $group_type) {
					if($group_type == "SUM") {
						$groups[$group_index]['result_fields'][$group_field] = $groups[$group_index]['result_fields'][$group_field] + $row[$group_field];
					}
				}

				// recupera o proximo registro
				$proximo = $this->vars['dataset'][$row_index+1]??NULL;

				// se o grupo mudou
				if(($row[$grupo['group']] != $proximo[$grupo['group']]) || ($resetando)) {
					// marca que está resetando, porque assim os proximos grupos de nivel mais baixos precisam ser resetados tambem
					$resetando = TRUE;

					// percorre as colunas da query, coluna por coluna, para criar a linha do grupo
					$group_line = [];
					foreach($row as $column => $value) {
						// inicia o label do grupo
						
						$group_label = str_replace("{" . $column . "}", $final_row[$column], $group_label);
						
						// verifica se tem configuração da colun
						$config = NULL;
						if($this->vars['dataset_column_configs'][$column]) {
							$config = $this->vars['dataset_column_configs'][$column];
						}
						
						// verifica se deve esconder
						if($config['hide']??FALSE) {
							continue;
						}
						
						// se é uma coluna de conta
						if(isset($groups[$group_index]['result_fields'][$column])) {

							// formata o valor
							$value = $this->formatColumn($groups[$group_index]['result_fields'][$column]??"", $config);
							
							// exibe o valor calculado e reseta
							$group_line[] = $value;
							$groups[$group_index]['result_fields'][$column] = 0;
						}
						else {
							// se não só mostra uma linha vazia
							$group_line[] = "";
						}
					}

					// adiciona a coluna de controle
					$group_line['sin_line_config'] = [
						'type' => "group", 
						'group_name' => $grupo['group'],
						'group_label' => $group_label,
						'group_index' => $group_index,

					];

					// adiciona a linha final ao grupo
					$group_data[] = $group_line;
				}

			}

			// inverte a ordem das linhas dos grupos, e adiciona ao final do dataset
			$data = array_merge($data, array_reverse($group_data));

		}

		// 
		$this->vars['dataset'] = $data;
		$this->vars['dataset_header'] = $data_header;

		// faz o render
		$this->render();
	}

	/**
	 * Processa o arquivo final
	 * 
	 * @return void
	 * 
	 */
	public function render(): void
	{
		// assina as variaveis
		$this->smarty->assign($this->vars);

		// armazena o html gerado
		$this->html = $this->smarty->fetch($this->template);
	}

	/**
	 * retorna o html
	 * 
	 * @return string
	 */
	public function getHtml(): string
	{
		return $this->html;
	}

	/**
	 * Exibe o html na tela
	 * 
	 * @return void
	 */
	public function show(): void
	{
		flush();

		die($this->html);
	}

	/**
	 * Salva o html
	 * 
	 * @param string $filepath
	 * @return void
	 */
	public function save(string $filepath): void
	{
		// salva o arquivo
		file_put_contents($filepath, $this->html);
	}

	/**
	 * Envia o html para download
	 * 
	 * @param string $filename Nome do arquivo
	 * @return void
	 */
	public function download(string $filename=""): void
	{
		header('Content-Description: File Transfer');
		header('Content-Type: text/html'); 
		header('Content-Disposition: attachment; filename="' . ($filename??"") . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . strlen($this->html));
		
		flush();
		echo $this->html;
		exit;
	}

	/**
	 * Formata o valor conforme a configuração da coluna
	 * 
	 * @param mixed $value Valor
	 * @param array $config Configuração da coluna
	 * @return string
	 */
	private function formatColumn(mixed $value, array $config): string
	{
		// se nao tem configuração da coluna, ja retorna o proprio valor
		if(!$config) {
			return $value;
		}
		
		// recupera a formatação dessa coluna
		if(($config['type']??"") == "decimal") {
			$value = number_format($value, $config['decimals']??2, $config['decimal_separator']??",", $config['thousands_separator']??".");
		}
		else if(($config['type']??"") == "boolean") {
			if($value === TRUE) {
				$value = "Sim";
			}
			else if($value === FALSE) {
				$value = "Não";
			}
		}
		else if(($config['type']??"") == "date") {
			if(strlen($value??"") > 0) {
				$value = date($config['format'], strtotime($value));
			}
		}

		// verifica o prefixo e prefixo
		$value = ($config['prefix']??"") . $value . ($config['sufix']??"");

		// retorna o valor
		return $value;

	}
}