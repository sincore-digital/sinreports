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
	 * Armazena os dados ou variaveis a serem renderizadas no input
	 * 
	 * @var array
	 */
	private array $data;

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
	 * Prepara o relatório organizando as informações e deixando-as pronta para o output
	 * 
	 * @return \SiNReports\Report
	 */
	public function prepare(): \SiNReports\Report
	{
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
	public function toPdf(): \SiNReports\Formats\Pdf
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
		$renderer = new \SiNReports\Formats\Xls();

		// retorna o renderizador
		return $renderer;
	}
}