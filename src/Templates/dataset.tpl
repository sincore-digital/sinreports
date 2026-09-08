<html>
	<head>
		<meta charset="utf-8"/>

		<style>
			/* reset */
			html {
				box-sizing: border-box;
				-webkit-font-smoothing: antialiased;
				font-size: 14px;
			}

			*, *:before, *:after {
				box-sizing: inherit;
			}

			body, h1, h2, h3, h4, h5, h6, p, ol, ul {
				margin: 0;
				padding: 0;
				font-weight: normal;
				line-height: 1.5;
				font-family: Helvetica, Arial, sans-serif
				
			}

			ol, ul {
				list-style: none;
			}

			img {
				max-width: 100%;
				height: auto;
			}

			/* estilo da tabela */
			.report-table {
				width: 100%;
				border-collapse: collapse;
				font-size: 0.75rem;
				text-align: left;
				background-color: #ffffff;
			}

			/* .report-table thead th {
				text-align: left;
				font-weight: 700;
				text-transform: uppercase;
				background: #999999;
			} */

			.report-table td {
				padding: 4px 5px;
				border-bottom: 1px solid #dddddd;
			}

			/* alinhamentos */
			body table .align-left {
				text-align: left;
			}
			body table .align-center {
				text-align: center;
			}
			body table .align-right {
				text-align: right;
			}

			/* formatação do header */
			.report-table tr.header td {
				font-weight: 700;
				text-transform: uppercase;
			}

			/* formatação das linhas */
			.report-table tr td.row {
				background: #f7f9fc;
				font-size: 0.8rem;
			}
			.report-table tr:nth-child(even) td.row {
				background: #fff;
			}

			/* formatação do footer */
			.report-table .group_footer0 td {
				font-weight: 700;
				background: #c3dcf3;
				color: #000;
			}

			.report-table .group_footer1 td {
				font-weight: 700;
				background: #deecf9;
				color: #000000;
			}

			.report-table .group_footer2 td {
				font-weight: 700;
				color: #0f172a;
				background-color: #f1f5f9;
			}

			/* formatação do header */
			.report-table .group_header0 {
				/* border-top: 15px #fff solid; */
			}
			.report-table .group_header0 td {
				font-weight: 700;
				background: #c3dcf3;
				color: #000;
				font-size: 1.6rem;
				padding-top: 2px;
				padding-bottom: 2px;
				text-transform: uppercase;

				border-top: 1px solid #9799ca;
			}

			.report-table .group_header1 td {
				font-weight: 700;
				background: #deecf9;
				color: #000;
				font-size: 1.4rem;
				padding: 2px 5px;
				text-transform: uppercase;
			}

			.report-table .group_header2 td {
				font-weight: 700;
				background: #f1f5f9;
				color: #000;
				font-size: 1.4rem;
				padding: 2px 5px;
				text-transform: uppercase;
			}


		</style>
	</head>
	<body>
		<div class="table-container">
			<table class="report-table">

				
				<tbody>
					{* header *}
					{if !$dataset_hide_header}
						<tr class="header">
						{foreach from=$dataset_header key=column item=header}
							<td class="align-{$dataset_config[$column]['align']}">{$header}</td>
						{/foreach}
						</tr>
					{/if}

					{* percorre as linhas *}
					{foreach from=$dataset item=fields}
						<tr class="{$fields['sin_line_config']['type']|default:""} {$fields['sin_line_config']['class']|default:""}">
							
							{* se é uma linha do tipo agrupamento *}
							{if $fields['sin_line_config']['type']|default:"" == "group"}

								{* percorre as colunas *}
								{assign var=colspan value=0}
								{foreach from=$fields key=column item=value}
									{if $column == "sin_line_config"}
										{continue}
									{/if}

									{* se for uma coluna em branco, nao mostra, para poder por o colspan *}
									{if $value == ""}
										{assign var=colspan value=$colspan+1}

										{* se for uma linha vazia *}
										{if $colspan == count($fields)-1}
											<td colspan="{$colspan}" class="group_label ">{$fields['sin_line_config']['group_header_label']}</td>
										{/if}
									{else}
										{* se teve colspan *}
										{if $colspan > 0}
											{* mostra a coluna com a quantidade de colspan *}
											<td colspan="{$colspan}" class="group_label">{$fields['sin_line_config']['group_footer_label']}</td>
											{assign var=colspan value=0}
										{/if}

										{* agora sim mostra a coluna atual *}
										<td class="align-{$dataset_config[$column]['align']}">{$value}</td>
									{/if}

								{/foreach}

								{* verifica se deve repetir o header *}
								{if $fields['sin_line_config']['repeat_header']|default:FALSE}
									<tr class="header">
										{foreach from=$dataset_header key=column item=header}
											<td class="align-{$dataset_config[$column]['align']}">{$header}</td>
										{/foreach}
									</tr>
								{/if}

							{* linha normal *}
							{else}

								{* percorre as colunas *}
								{foreach from=$fields key=column item=value}
									{if $column == "sin_line_config"}
										{continue}
									{/if}

									{* se nao for uma coluna de configuração (adicionada pelo sinreports) *}
									<td class="row align-{$dataset_config[$column]['align']}">{$value}</td>

								{/foreach}

							{/if}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</body>
</html>