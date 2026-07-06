<?php

namespace SiNReports\Formats;

/**
 * Classe que trata a renderização final no formato XLS
 */
class Xls implements FormatInterface
{
	/**
	 * Armazena o config enviado no construtor
	 */
	protected array $config;

	/**
	 * Armazena o vetor de variaveis
	 */
	protected array $vars;

	/**
	 * Armazena o objeto gerador da planilha
	 * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
	 */
	protected $sheet;

	/**
	 * Construtor da classe
	 * 
	 * @param array $config
	 * @param array $vars
	 */
	public function __construct(array $config, array $vars)
	{
		// salva as variaveis
		$this->config = $config;
		$this->vars = $vars;
		
		// gera a planilha
		$this->sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

		// seta se foi enviado dados no dataset
		if(!isset($this->vars['dataset'])) {
			throw new \Exception("Não existe dados no dataset");
		}

		// armazena o dataset
		$dataset = $this->vars['dataset'];

		// ativa a primeira aba
		$this->sheet->setActiveSheetIndex(0);

		// percorre a primeira linha do dataset em busca das colunas
		$column_count = 0;
		foreach($dataset[0] as $header => $value) {
			
			// escreve a coluna
			$this->sheet->getActiveSheet()->setCellValue($this->getCOlumnLetter($column_count) . "1", $header);

			$column_count++;
		}

		// percorre o dataset colocando os dados
		$line_count = 2;
		foreach($dataset as $line) {

			// reseta a coluna
			$column_count = 0;

			// percorre as colunas
			foreach($line as $value) {
			
				// escreve a coluna
				$this->sheet->getActiveSheet()->setCellValue($this->getCOlumnLetter($column_count) . "" . $line_count, $value);

				// adiciona a proxima linha
				$column_count++;
			}

			// adiciona a proxima linha
			$line_count++;
		}
		
	}

	/**
	 * Exibe o xls na tela
	 * 
	 * @return void
	 */
	public function show(): void
	{
		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->sheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}

	private function getColumnLetter($count)
	{
		return chr(65 + $count);
	}
}