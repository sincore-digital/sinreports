> 🔴 ainda estamos em teste, não utilize em produção, pois pode quebrar à qualquer commit

# SiNReports

O SiNReports é uma biblioteca gratuita para a geração automatizada de relatórios e documentos sem utilização de APIs remotas, servidores e instalações complexas. Renderize templates Smarty e crie relatórios/documentos em HTML, PDF e XLS

## Instalação

```bash
composer require sincore/sinreports
```

Ao baixar a lib, os binários ja está embutidos, mas caso exista problema em executa-los, é possivel utilizar o `path` para o seu proprio binário

## Como usar

Para criar relatórios tabulados

```php

$vars = [
	[
		'id' => 12,
		'nome' => "produto 1",
		'valor' => 45.30
	],
	[
		'id' => 23,
		'nome' => "produto 2",
		'valor' => 101.41
	],
];

$report = new \SiNReports\Report();
$report->setDebugMode(TRUE)
	->setDataset($vars)

	// ->toHtml() // gera o arquivo final em HTML
	// ->toXls() // gera o arquivo final em XLS
	->toPdf() // gera o arquivo final em PDF

	//->save(APPLICATION_PATH . "/files/meupdf.pdf") // salva no arquivo
	// ->show() // exibe na tela
	->download(); // envia o arquivo para download

```

Para criar documentos complexos

```php

$vars = [
	'nome' => "Nome do comprador",
	'documento' => "xxx.xxx.xxx-xx",
	'produtos' => [
		[
			'id' => 12,
			'nome' => "produto 1",
			'valor' => 45.30
		],
		[
			'id' => 23,
			'nome' => "produto 2",
			'valor' => 101.41
		],
	];
];

$report = new \SiNReports\Report();
$report->setDebugMode(TRUE)
	->setTemplate(APPLICATION_PATH . "/relatorios/teste.tpl")
	->setVars($vars)

	// ->toHtml() // gera o arquivo final em HTML
	// ->toXls() // gera o arquivo final em XLS
	->toPdf() // gera o arquivo final em PDF

	//->save(APPLICATION_PATH . "/files/meupdf.pdf") // salva no arquivo
	// ->show() // exibe na tela
	->download(); // envia o arquivo para download

```

## Dependências

- `smarty/smarty`: Responsável por ler o template à ser gerado e renderizar em HTML
- `wkhtmltopdf (Binário)`: Binário responsável por renderizar o HTML em PDF
- `mikehaertl/phpwkhtmltopdf`: Responsável fazer as chamadas ao `wkhtmltopdf`
- `neonxp/MathExecutor`: Calculo de operações dinamicas

## Contribua

Pull requests são muito bem-vindos. Para novas ideias, por favor utilize a sessão de issues para discutir novas mudanças e funcionalidades

## License

[GPLv3](https://github.com/sincore-digital/sinreports/blob/main/LICENSE)

## Lembretes
https://download-chromium.appspot.com/
https://github.com/ungoogled-software/ungoogled-chromium-portablelinux
https://googlechromelabs.github.io/chrome-for-testing/

- wkhtmltopdf é antigo, algumas libs começaram a ser depreciadas, e não da pra compilar estatico, precisa ser instalado no servidor e ainda o libssl1 precisa ser baixado manualmente (depreciado). tambem nao consegui renderizar webp
- ironpress funcionou muito bem, mas é lento comparado aos outros (até comparado ao chrome headless) quando o documento é grande. alguns erros de renderização ocorrem com frequencia, como por exemplo texto justificado com um strong no meio dele, ele quebra. Acesso externo à imagens e fontes tambem são um problema, contornavel mas é um problema
- chrome/chromium, funciona muito bem, renderiza muito bem, apresenta bom desempenho, porem tem 700Mb
- chrome-headless-shell, rapido, mais que o wk até, mas aidna tem 220Mb, mas ja está bem mais aceitavel, melhor opção até agora