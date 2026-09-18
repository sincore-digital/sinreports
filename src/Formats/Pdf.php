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
	 * Armazena as opções de criação de PDF
	 */
	protected array $options;

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

		// prepara as configurações
		$this->options = [
			
			// binario do metodo
			'method' => $config['method']??"wkhtmltopdf", // wkhtmltopdf / chrome / ironpress
			'binary' => $config['binary']??__DIR__ . "/../../bin/ironpress",

			// configuracoes do arquivo
			'orientation' => $config['orientation']??"portrait", // landscape
			'page-size' => $config['page-size']??"A4",
			'margin-top' => $config['margin-top']??"1",
			'margin-bottom' => $config['margin-bottom']??"1",
			'margin-left' => $config['margin-left']??"1",
			'margin-right' => $config['margin-right']??"1",
			'title' => $config['title']??"SiNCORE Reports",

			// especificos ironpress
			'basepath' => $config['basepath']??"",

			// especificos do chrome
			'fontpath' => NULL,

			// especificos wkhtmltopdf
			'ignoreWarnings' => TRUE,
			'load-error-handling' => "skip",
			'load-media-error-handling' => "skip",

		];

		// se o metodo for wk
		if($config['method'] == "wkhtmltopdf") {
			if(strlen($config['binary']??"")) {
				$this->options['binary'] = $config['binary'];
			}
			else {
				unset($config['binary']);
			}
			
		}

		// cria os diretórios temporarios
		mkdir(sys_get_temp_dir() . "/sinreports/html_compiled/");
		mkdir(sys_get_temp_dir() . "/sinreports/pdf_compiled/");
		
	}

	/**
	 * renderiza com wkhtmltopdf
	 */
	private function renderPdfWithWKHtmlToPDF()
	{
		$options = [
			'ignoreWarnings' => $this->options['ignoreWarnings'],
			'load-error-handling' => $this->options['load-error-handling'],
			'load-media-error-handling' => $this->options['load-media-error-handling'],
		];

		// cria o objeto para geração de PDF
		$pdf = new \mikehaertl\wkhtmlto\Pdf($options);
		$pdf->addPage($this->html);

		// retorna o pdf
		return $pdf;
	}

	/**
	 * renderiza e salva o pdf usando ironpress
	 */
	private function renderPdfWithIronPress()
	{
		$filename = uniqid();
		$temp_html_filepath = sys_get_temp_dir() . "/sinreports/tpl_compiled/" . $filename . ".html";
		$temp_pdf_filepath = sys_get_temp_dir() . "/sinreports/tpl_compiled/" . $filename . ".pdf";

		// grava num arquivo temporario
		file_put_contents($temp_html_filepath, $this->html);
		exec(__DIR__ . '/../../bin/ironpress --base-path "' . ($this->config['basepath']??"") . '" --margin 28 ' . $temp_html_filepath . ' ' . $temp_pdf_filepath);

		return $temp_pdf_filepath;
	}

	/**
	 * renderiza e salva o pdf usando chromium
	 */
	private function renderPdfWithChromium()
	{
		$filename = uniqid();
		$temp_html_filepath = sys_get_temp_dir() . "/sinreports/html_compiled/" . $filename . ".html";
		$temp_pdf_filepath = sys_get_temp_dir() . "/sinreports/pdf_compiled/" . $filename . ".pdf";

		// grava num arquivo temporario
		file_put_contents($temp_html_filepath, $this->html);

		// verifica se o arquivo existe
		if(!file_exists($this->options['binary'])) {
			throw new \Exception("Chrome binary not found");
		}
		
		// organiza os parametros
		$command = [
			"export FONTCONFIG_PATH=" . ($this->options['fontpath']??""),
			"&&",
			$this->options['binary'],
			"--headless=new",
			"--no-sandbox",
			"--disable-dev-shm-usage",
			"--disable-gpu",
			"--no-pdf-header-footer",
			"--print-to-pdf=\"" . $temp_pdf_filepath . "\"",
			$temp_html_filepath,
			"2>&1",
		];

		// executa o comando
		exec(implode(" ", $command), $output, $result_code);

		// echo "<pre>";
		// var_dump($output);
		// echo "</pre>";

		return $temp_pdf_filepath;
	}

	/**
	 * Exibe o pdf na tela
	 * 
	 * @return void
	 */
	public function show(): void
	{
		// verifica o metodo
		if($this->options['method'] == "wkhtmltopdf") {
			// cria o pdf
			$pdf = $this->renderPdfWithWKHtmlToPDF();
			
			// envia
			if(!$pdf->send()) {
				// se debug estiver setado como true, exibir $this->pdf->getError()
				throw new \Exception("Could not create PDF");
			}
		}
		else if($this->options['method'] == "chrome") {

			// cria o pdf
			$temp_pdf_filepath = $this->renderPdfWithChromium();

			// envia
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="' . basename($temp_pdf_filepath) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			header('Content-Length: ' . filesize($temp_pdf_filepath));
			readfile($temp_pdf_filepath);

		}
		else {

			// cria o pdf
			$temp_pdf_filepath = $this->renderPdfWithIronPress();

			// envia
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
		// verifica o metodo
		if($this->options['method'] == "wkhtmltopdf") {

			// cria o pdf
			$pdf = $this->renderPdfWithWKHtmlToPDF();
			
			// salva
			if(!$pdf->saveAs($filepath)) {
				// se debug estiver setado como true, exibir $this->pdf->getError()
				throw new \Exception("Could not create PDF");
			}

		}
		else if($this->options['method'] == "chrome") {

			// cria o pdf
			$temp_pdf_filepath = $this->renderPdfWithChromium();

			// salva
			copy($temp_pdf_filepath, $filepath);

		}
		else {

			// cria o pdf
			$temp_pdf_filepath = $this->renderPdfWithIronPress();

			// salva
			copy($temp_pdf_filepath, $filepath);

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
		// verifica o metodo
		if($this->options['method'] == "wkhtmltopdf") {

			// cria o pdf
			$pdf = $this->renderPdfWithWKHtmlToPDF();
			
			// envia
			if(!$pdf->send($filename)) {
				// se debug estiver setado como true, exibir $this->pdf->getError()
				throw new \Exception("Could not create PDF");
			}

		}
		else if($this->options['method'] == "chrome") {

			// cria o pdf
			$temp_pdf_filepath = $this->renderPdfWithChromium();

			// envia
			header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($temp_pdf_filepath));
			
			readfile($temp_pdf_filepath);

		}
		else {

			// cria o pdf
			$temp_pdf_filepath = $this->renderPdfWithIronPress();

			// envia
			header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($temp_pdf_filepath));
			
			readfile($temp_pdf_filepath);

		}
	}
}