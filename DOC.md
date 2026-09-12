# Documentação

arquivo para lembrete rapido do que documentar

- inicia um relatório
```php
$report = new \SiNReports\Report();
```

- criar relatórios de tabelas, usa o tpl interno e/ou consegue exportar XLS

```php
$report->setDataset($rows);
```

ainda é possivel setar um tpl custom, usar o tpl padrão e modificar conforme o gosto

- setar um tpl custom
```php
$report->setTemplate(APPLICATION_PATH . "/../public_html/files/sinreports.dataset.tpl");
$report->setVars([
		'leilao' => $leilao,
		'empresa' => $empresa,
		'lote' => $lote,
		'animais' => $animais,
		'comprador' => $comprador,
	]);
```

- preparar
```php
$report->prepare();
```

depois de preparado não é mais possivel enviar variaveis, configurações, setar templates, nada, só gerar o documento

- criar documentos
```php

$report->toPdf()->save("test.pdf");
$report->toPdf()->show();
$report->toPdf()->download("test.pdf");

$report->toHtml()->save("test.html");
$report->toHtml()->show();
$report->toHtml()->download("test.html");

// somente dataset
$report->toXls()->save("test.dls");
$report->toXls()->download("test.dls");

```

- metodos de criação de PDF
```php
// ironpress, default, ja embutido na lib
$report->setConfig([
		'basepath' => APPLICATION_PATH . "/../public_html/", // configura o path para o iron press encontrar imagens e fontes. no tpl, usar ./images/imagem.png
	]);

// chrome
$report->setConfig([
		'method' => "chrome",
		'binary' => APPLICATION_PATH . "/tmp/bin/chrome-headless-shell/chrome-headless-shell"
	]);

// wkhtmltopdf
$report->setConfig([
		'method' => "wkhtmltopdf",
		// 'binary' => APPLICATION_PATH . "/tmp/bin/wkhtmltopdf/wkhtmltopdf" // não sete o binario para usar o wkhtmltopdf do sistema
	]);

```

- setar fontes no chrome
```php
// chrome
$report->setConfig([
		'method' => "chrome",
		'binary' => APPLICATION_PATH . "/tmp/bin/chrome-headless-shell/chrome-headless-shell",
		'fontpath' => APPLICATION_PATH . "/../public_html/fonts",
	]);
```

no diretorio `APPLICATION_PATH . "/../public_html/fonts"` criar o arquivo `fonts.conf` com o conteudo:
```xml
<?xml version="1.0"?>
<!DOCTYPE fontconfig SYSTEM "fonts.dtd">
<fontconfig>
    <dir>./</dir> <!-- aponta para a pasta onde estão os arquivos .ttf / .otf -->
    <cachedir>./</cachedir>  <!-- garante que o sistema crie o cache nessa pasta temporária -->
</fontconfig>
```

- configuração do Smarty

essa ja é a configuração padrão do smarty

```php
$report->setSmartyConfig([
		'compile_dir' => sys_get_temp_dir() . "/sinreports/tpl_compiled",
		'compile_check' => FALSE,
		'force_compile' => FALSE,

		'cache_dir' => sys_get_temp_dir() . "/sinreports/tpl_cached",
		'caching' => FALSE,
		'cache_lifetime' => 600,

		'debugging' => FALSE,
	]);
```

# Datasets

datasets são relatorios baseados em listagem simples, como uma planilha do excel. ele vai listar as linhas e colunas tabelados

- configurar as colunas
```php
$report->setDataset($rows)
	->configureColumn("tipo", [
			'title' => "Tipo", 
			'hide' => TRUE
		])
	->configureColumn("especie", [
			'title' => "Espécie"
		])
	->configureColumn("valor", [
			'title' => "Valor",
			'prefix' => "R$ ",
			'type' => "decimal",
			'decimals' => 2,
			'decimal_separator' => ",",
			'thousands_separator' => ".",
			'align' => "right",
		])
	->configureColumn("quantidade", [
			'title' => "QTD",
			'align' => "center",
		])
	->configureColumn("porcentagem", [
			'title' => "Porcentagem",
			'align' => "center",
			'sufix' => " %"
			'type' => "decimal",
		])
	->configureColumn("vencimento", [
			'title' => "Vencimento",
			'align' => "center",
			'type' => "date",
			'format' => "d/m/Y",
		])
	->configureColumn("pago", [
			'title' => "Pago",
			'align' => "center",
			'type' => "boolean",
		]);

```

- esconder o header da tabela

usado geralmente quando o header se repete nos grupos

```php
$report->hideHeader();
```

- agrupamentos

é possivel criar agrupamentos com base nas linhas. não esquecer de ordenar as linhas no agrupamento.

```php
$report->addGroup([
		'group' => "especie", // nome da coluna a ser agrupada
		'footer_label' => "SUB-TOTAL POR {especie} {tipo}", // label a ser exibido na linha final. use {NOME_COLUNA} caso queira substituir alguns valores
		'header_label' => "{especie}", // label a ser exibido na linha inicial
		'show_header' => TRUE, // informa se deve mostrar a linha inicial
		'show_footer' => TRUE, // informa se deve mostrar a linha final (default)
		'repeat_header' => TRUE, // repete o header da tabela, nome das colunas
		'fields' => [ // campos a serem exibidos no footer
			'valor' => "SUM", // as colunas "valor" vão ser somadas, e quando o grupo mudar, vai mostrar o total daquele grupo
			'quantidade' => "SUM", // as colunas "quantidade" vão ser somadas, e quando o grupo mudar, vai mostrar o total daquele grupo
			'media' => "CALC({valor}/{quantidade})" // tambem é possivel usar CALC() que vai fazer o calculo do valor dos campos
		],
	]);
```

funciona assim: ele vai listando as linhas, quando a coluna do grupo mudar, ele vai adicionar uma linha separadora, com algumas informações como total, se necessario, e tambem pode por uma linha separadora no começo de cada grupo

é possivel usar mais de um grupo

