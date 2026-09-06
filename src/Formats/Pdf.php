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

		// prepara os options
		$options = [
			// 'binary' => __DIR__ . '/../../bin/wkhtmltopdf',
			'ignoreWarnings' => TRUE,
			'load-error-handling' => "skip",
			'load-media-error-handling' => "skip",

			// 'enable-smart-shrinking',
			// 'viewport-size' => "1920x1080",
			// 'page-width' => "1920px",
			// 'page-height' => "1080px",
		];

		if(isset($config['orientation'])) $options['orientation'] = $config['orientation'];
		if(isset($config['page-size'])) $options['page-size'] = $config['page-size'];
		if(isset($config['margin-bottom'])) $options['margin-bottom'] = $config['margin-bottom'];
		if(isset($config['margin-top'])) $options['margin-top'] = $config['margin-top'];
		if(isset($config['margin-left'])) $options['margin-left'] = $config['margin-left'];
		if(isset($config['margin-right'])) $options['margin-right'] = $config['margin-right'];
		if(isset($config['title'])) $options['title'] = $config['title'];

		// cria o objeto para geração de PDF
		$this->pdf = new \mikehaertl\wkhtmlto\Pdf($options);
		$this->pdf->addPage($html);
		
	}

	/**
	 * Exibe o pdf na tela
	 * 
	 * @return void
	 */
	public function show(): void
	{
		$a = 2;

		if($a == 1) {
			// cria e envia o pdf
			if(!$this->pdf->send()) {
				// se debug estiver setado como true, exibir $this->pdf->getError()
				throw new \Exception("Could not create PDF");
			}
		}
		else {

			$filename = "teste_arquivo_temp";
			$temp_html_filepath = sys_get_temp_dir() . "/sinreports/tpl_compiled/" . $filename . ".html";
			$temp_pdf_filepath = sys_get_temp_dir() . "/sinreports/tpl_compiled/" . $filename . ".pdf";

			// grava num arquivo temporario
			file_put_contents($temp_html_filepath, $this->html);

			exec(__DIR__ . '/../../bin/ironpress --margin 28 ' . $temp_html_filepath . ' ' . $temp_pdf_filepath);

			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="' . basename($temp_pdf_filepath) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			header('Content-Length: ' . filesize($temp_pdf_filepath));
			readfile($temp_pdf_filepath);
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