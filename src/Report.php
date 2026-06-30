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
	 * 
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		// salva o config
		$this->config = $config;
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