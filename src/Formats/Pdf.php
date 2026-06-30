<?php

namespace SiNReports\Formats;


/**
 * Classe que trata a renderização final no formato PDF
 */
class Pdf implements FormatInterface
{
	/**
	 * Armazena o config enviado no construtor
	 */
	protected array $config;

	/**
	 * Armazena o html final
	 */
	protected string $html;

	/**
	 * Armazena o objeto gerador de PDF
	 */
	protected $pdf;

	/**
	 * Construtor da classe
	 * 
	 * @param array $config
	 * @param string $html
	 */
	public function __construct(array $config, string $html)
	{
		// salva as variaveis
		$this->config = $config;
		$this->html = $html;

		// cria o objeto para geração de PDF
		$this->pdf = new \mikehaertl\wkhtmlto\Pdf([
			'binary' => __DIR__ . '/../../bin/wkhtmltopdf',
			'ignoreWarnings' => TRUE,
		]);
		$this->pdf->addPage($html);
		
	}

	/**
	 * Exibe o pdf na tela
	 * 
	 * @return void
	 */
	public function show(): void
	{
		// cria e envia o pdf
		if(!$this->pdf->send()) {
			// se debug estiver setado como true, exibir $this->pdf->getError()
			throw new \Exception("Could not create PDF");
		}
	}

	/**
	 * Salva o pdf
	 * 
	 * @param string $filepath
	 * @return void
	 */
	public function save(string $filepath): void
	{
		// cria o pdf e salva o arquivo
		if(!$this->pdf->saveAs($filepath)) {
    		// se debug estiver setado como true, exibir $this->pdf->getError()
			throw new \Exception("Could not create PDF");
		}
	}

	/**
	 * Envia o pdf para download
	 * 
	 * @param string $filename Nome do arquivo
	 * @return void
	 */
	public function download(string $filename=""): void
	{
		// cria e envia o pdf
		if(!$this->pdf->send($filename)) {
			// se debug estiver setado como true, exibir $this->pdf->getError()
			throw new \Exception("Could not create PDF");
		}
	}
}