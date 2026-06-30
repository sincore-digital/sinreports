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
		foreach($natives as $native) {
			$this->smarty->registerPlugin("modifier", $native, $native);
		}


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
}