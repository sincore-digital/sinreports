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
	 * Armazena o tipo de input, se é um dataset ou um arquivo .tpl
	 * 
	 * @var string
	 */
	private string $input;

	/**
	 * Armazena os dados ou variaveis a serem renderizadas no input
	 * 
	 * @var array
	 */
	private array $data;
	
	/**
	 * Summary of prepare
	 * 
	 * @param string $input - arquivo .tpl ou "dataset"
	 * @param array $data
	 * @return \SiNReports\Report
	 */
	public function prepare(string $input, array $data): \SiNReports\Report
	{
		// guarda as variaveis
		$this->input = $input;
        $this->data = $data;

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
		$renderer = new \SiNReports\Formats\Html($this->config, $this->input, $this->data);

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
}