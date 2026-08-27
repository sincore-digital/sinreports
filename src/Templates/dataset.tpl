<html>
	<head>
		<meta charset="utf-8"/>

		<style>
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
				font-weight: 600;
				padding: 7px 5px;
			}

			.report-table th, 
			.report-table td {
				padding: 7px 5px;
				border-bottom: 1px solid #e2e8f0;
			}

			.report-table tbody tr:nth-child(even) {
				background-color: #f8fafc; 
			}

			/* Table Footer Formatting */
			.report-table .group1 td {
				font-weight: 700;
				color: #0f172a;
				background-color: #f1f5f9;
			}

			.report-table .group2 td {
				font-weight: 700;
				color: #0f172a;
				background-color: #deecf9;
			}

		</style>
	</head>
	<tbody>
		<div class="table-container">
			<table class="report-table">
				<thead>
					<tr>
					{foreach from=$dataset[0] key=datacolumn item=datavalue}

						{* verifica se a coluna está oculta *}
						{if $dataset_column_configs[$datacolumn]['hide']|default:FALSE}
							{continue}
						{/if}
						
						{if isset($dataset_column_configs[$datacolumn]['title'])}
							<th>{$dataset_column_configs[$datacolumn]['title']}</th>
						{else}
							<th>{$datacolumn}</th>
						{/if}

					{/foreach}
					</tr>
				</thead>
				<tbody>
				
					{foreach from=$dataset item=dataitem}
						<tr>
							{foreach from=$dataitem key=datacolumn item=datavalue}

								{assign var=column_prefix value=""}
								{assign var=column_sufix value=""}
								
								{assign var=column_value value=$datavalue}

								{* verifica se tem configuração da coluna *}
								{if isset($dataset_column_configs[$datacolumn])}

									{assign var=column_config value=$dataset_column_configs[$datacolumn]}

									{* verifica se a coluna está oculta *}
									{if $column_config['hide']|default:FALSE}
										{continue}
									{/if}

									{* salva o prefixo e sufixo *}
									{assign var=column_prefix value=$column_config['prefix']|default:""}
									{assign var=column_sufix value=$column_config['sufix']|default:""}

									{* formata o tipo decimal *}
									{if $column_config['type']|default:"" == "decimal"}
										{assign var=column_value value=number_format($column_value, $column_config['decimals']|default:2, $column_config['decimal_separator']|default:".", $column_config['thousands_separator']|default:",")}
									
									{* formata o tipo data *}
									{else if $column_config['type']|default:"" == "date"}
										{if strlen($column_value|default:"") > 0}
										{assign var=column_value value=date($column_config['format'], strtotime($column_value))}
										{/if}

									{* formata o tipo boolean *}
									{else if $column_config['type']|default:"" == "boolean"}
										{if $column_value}
											{assign var=column_value value="Sim"}
										{else}
											{assign var=column_value value="Não"}
										{/if}

									{/if}
								
								{/if}
								
								<td>
									{$column_prefix}{$column_value|escape}{$column_sufix}
								</td>
							
							{/foreach}
						</tr>
					{/foreach}		
				</tbody>
			</table>
		</div>

	</tbody>
</html>