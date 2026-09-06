<html>
	<head>
		<meta charset="utf-8"/>

		<style>
			html {
				box-sizing: border-box;
				-webkit-font-smoothing: antialiased;
				font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
				
			}

			ol, ul {
				list-style: none;
			}

			img {
				max-width: 100%;
				height: auto;
			}


			.table-container {
				
			}

			.report-table {
				width: 100%;
				border-collapse: collapse;
				font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
				font-size: 0.75rem;
				text-align: left;
				background-color: #ffffff;
			}

			.report-table thead th {
				background-color: #fff;
				color: #0a0a0a;
				font-weight: 700;
				text-align: left;
			}

			.report-table th, 
			.report-table td {
				padding: 4px 5px;
				border-bottom: 1px solid #e2e8f0;
			}

			.report-table tbody tr:nth-child(even) {
				background-color: #f8fafc; 
			}

			/* Table Footer Formatting */
			.report-table .group0 td {
				font-weight: 700;
				color: #0f172a;
				background-color: #c3dcf3;
			}

			.report-table .group1 td {
				font-weight: 700;
				color: #0f172a;
				background-color: #deecf9;
			}

			.report-table .group2 td {
				font-weight: 700;
				color: #0f172a;
				background-color: #f1f5f9;
			}

		</style>
	</head>
	<body>
		<div class="table-container">
			<table class="report-table">
				<thead>
					<tr>
					{foreach from=$dataset_header item=header}
						<th>{$header}</th>
					{/foreach}
					</tr>
				</thead>
				<tbody>

					{* percorre as linhas *}
					{foreach from=$dataset item=fields}
						<tr class="{$fields['sin_line_config']['type']|default:""} {$fields['sin_line_config']['type']|default:""}{$fields['sin_line_config']['group_index']|default:""}">
							
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
									{else}
										{* se teve colspan *}
										{if $colspan > 0}
											{* mostra a coluna com a quantidade de colspan *}
											<td colspan="{$colspan}">{$fields['sin_line_config']['group_label']}</td>
											{assign var=colspan value=0}
										{/if}

										{* agora sim mostra a coluna atual *}
										<td>{$value}</td>
									{/if}

								{/foreach}

							{* linha normal *}
							{else}

								{* percorre as colunas *}
								{foreach from=$fields key=column item=value}
									{if $column == "sin_line_config"}
										{continue}
									{/if}

									{* se nao for uma coluna de configuração (adicionada pelo sinreports) *}
									<td>{$value}</td>

								{/foreach}

							{/if}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</body>
</html>