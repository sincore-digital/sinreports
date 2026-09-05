<?php

namespace SiNReports;

/**
 * Classe que cria e prepara os relatórios do SiNReports
 */
class Report
{
	/**
	 * Armazena o config enviado no construtor
	 */
	protected array $config;

	/**
	 * Armazena as configurações das colunas
	 */
	protected array $columnConfigs;

	/**
	 * Armazena o arquivo .tpl do relatório
	 * 
	 * @var string
	 */
	private string $templateFilepath;

	/**
	 * Armazena as variaveis do template
	 * 
	 * @var array
	 */
	private array $templateVars;

	/**
	 * Armazena os grupos do dataseet
	 * 
	 * @var array
	 */
	private array $groups = [];

	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		// inicia a config
		$this->config['debugging'] = FALSE;

		// monta o config inicial do smarty
		$this->config['smarty'] = [
			'compile_dir' => sys_get_temp_dir() . "/sinreports/tpl_compiled",
			'compile_check' => FALSE,
			'force_compile' => FALSE,

			'cache_dir' => sys_get_temp_dir() . "/sinreports/tpl_cached",
			'caching' => FALSE,
			'cache_lifetime' => 600,

			'debugging' => FALSE,
		];
	}

	/**
	 * Seta a configuração smarty custom
	 * 
	 * @param array $config
	 * @return \SiNReports\Report
	 */
	public function setSmartyConfig(array $config): \SiNReports\Report
	{
		// percorre as chave do vetor
		foreach($config as $key => $value) {

			// sobreescrerve sobre a chave enviada
			$this->config['smarty'][$key] = $value;
		}

		// retorna ele mesmo
		return $this;
	}

	/**
	 * Seta o modo debug
	 * 
	 * @param bool $debug
	 * @return \SiNReports\Report
	 */
	public function setDebugMode(bool $debug): \SiNReports\Report
	{
		// armazena o modo de debug
		$this->config['debugging'] = $debug;

		// configura o smarty para debug mode
		$this->config['smarty']['debugging'] = $debug;
		$this->config['smarty']['compile_check'] = $debug;
		$this->config['smarty']['force_compile'] = $debug;
		$this->config['smarty']['caching'] = !$debug;

		// retorna ele mesmo
		return $this;
	}
	
	/**
	 * Prepara o relatório organizando as informações e deixando-as pronta para o output
	 * 
	 * @return \SiNReports\Report
	 */
	public function prepare(): \SiNReports\Report
	{
		// verifica se tem template, se não tiver, seta o padrão
		if(!isset($this->templateFilepath)) {
			$this->templateFilepath = __DIR__ . "/Templates/dataset.tpl";
		}

		// verifica se está usando dataset, tabelado
		$this->templateVars['dataset_column_configs'] = $this->columnConfigs??[];

		// if(isset($this->templateVars['dataset'])) {
		// 	// percorre columnTitles para trocar os dados da primeira linha
		// 	foreach($this->columnTitles as $index => $name) {
		// 		$this->templateVars['dataset'][0][$index] = $name;
		// 	}
		// }

		// d($this->templateVars);









		
		// adiciona os grupos ao config
		$this->templateVars['dataset_groups'] = $this->groups;

		// retorna ele mesmo
		return $this;
	}

	/**
	 * Seta as configurações (https://wkhtmltopdf.org/usage/wkhtmltopdf.txt)
	 * 
	 * @param array $config
	 * @return \SiNReports\Report
	 */
	public function setConfig(array $config): \SiNReports\Report
	{
		// faz o merge das configurações
		$this->config = array_merge($this->config, $config);
		
		// retorna ele mesmo
		return $this;
	}

	/**
	 * Seta o nome das colunas
	 * 
	 * @param array $columnTitles
	 * @return \SiNReports\Report
	 */
	// public function setColumnTitles(array $columnTitles): \SiNReports\Report
	// {
	// 	// salva o nome das colunas
	// 	$this->columnTitles = $columnTitles;
		
	// 	// retorna ele mesmo
	// 	return $this;
	// }

	/**
	 * Seta as configurações das colunas
	 * 
	 * @param string $columnName
	 * @param array $columnConfig
	 * @return \SiNReports\Report
	 */
	public function configureColumn(string $columnName, array $columnConfig): \SiNReports\Report
	{
		// salva as configurações da coluna
		$this->columnConfigs[$columnName] = $columnConfig;
		
		// retorna ele mesmo
		return $this;
	}

	/**
	 * Armazena o arquivo template do relatório
	 * 
	 * @param string $template
	 * @return \SiNReports\Report
	 */
	public function setTemplate(string $template): \SiNReports\Report
	{
		$this->templateFilepath = $template;

		// retorna ele mesmo
		return $this;
	}

	/**
	 * Armazena as variaveis do template do relatório
	 * 
	 * @param array $vars
	 * @return \SiNReports\Report
	 */
	public function setVars(array $vars): \SiNReports\Report
	{
		$this->templateVars = $vars;

		// retorna ele mesmo
		return $this;
	}

	/**
	 * Armazena o vetor de dados
	 * 
	 * @param array $dataset
	 * @return \SiNReports\Report
	 */
	public function setDataset(array $dataset): \SiNReports\Report
	{
		// armazena os dados no vetor de variaveis, ja que é essa a variavel padrão usada no tpl padrão
		$this->templateVars['dataset'] = $dataset;

		// retorna ele mesmo
		return $this;
	}

	/**
	 * Adiciona um novo gropo
	 * 
	 * @param array $group
	 * @return \SiNReports\Report
	 */
	public function addGroup(array $group): \SiNReports\Report
	{
		// armazena o grupo
		$this->groups[] = $group;

		// retorna ele mesmo
		return $this;
	}

	/**
	 * Faz o output do relatório em HTML
	 * 
	 * @return \SiNReports\Formats\Html
	 */
	public function toHtml(): \SiNReports\Formats\Html
	{
		// cria o renderizador HTML
		$renderer = new \SiNReports\Formats\Html($this->config, $this->templateFilepath, $this->templateVars);

		// retorna o renderizador
		return $renderer;
	}

	/**
	 * Faz o output do relatório em PDF
	 * 
	 * @return \SiNReports\Formats\Pdf
	 */
	public function toPdf($options=[]): \SiNReports\Formats\Pdf
	{
		// recupera o html a partir do renderizador de html
		$html = $this->toHtml()->getHtml();

		// cria o renderizador PDF
		$renderer = new \SiNReports\Formats\Pdf($this->config, $html);

		// retorna o renderizador
		return $renderer;
	}

	/**
	 * Faz o output do relatório em XLS
	 * 
	 * @return \SiNReports\Formats\Xls
	 */
	public function toXls(): \SiNReports\Formats\Xls
	{
		// cria o renderizador Xls
		$renderer = new \SiNReports\Formats\Xls($this->config, $this->templateVars);

		// retorna o renderizador
		return $renderer;
	}
}