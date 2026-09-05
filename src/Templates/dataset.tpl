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
	<tbody>
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
					{foreach from=$dataset item=fields}
						<tr class="{$fields['sin_line_config']['type']|default:""} {$fields['sin_line_config']['type']|default:""}{$fields['sin_line_config']['group_index']|default:""}">
							{foreach from=$fields key=column item=value}
								{if $column != "sin_line_config"}
									<td>{$value}</td>
								{/if}
							{/foreach}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

	</tbody>
</html>