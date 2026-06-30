<html>
	<head>
		<meta charset="utf-8"/>

		<style>
			.table-container {
				overflow-x: auto;
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
						{if $datacolumn == "singrouping_sexo"}
							{continue}
						{/if}
						{if $datacolumn == "singrouping_raca"}
							{continue}
						{/if}

						<th>{$datacolumn}</th>
					
					{/foreach}
					</tr>
				</thead>
				<tbody>
				
					{foreach from=$dataset item=dataitem}
						
						{assign var=class value=""}
						{foreach from=$dataitem key=datacolumn item=datavalue}
							{if $datacolumn == "singrouping_sexo" && $datavalue == 1}
								{assign var=class value="group1"}
							{/if}
							{if $datacolumn == "singrouping_raca" && $datavalue == 1}
								{assign var=class value="group2"}
							{/if}
						{/foreach}

						<tr class="{$class}">
							{foreach from=$dataitem key=datacolumn item=datavalue}
								{if $datacolumn == "singrouping_sexo"}
									{continue}
								{/if}
								{if $datacolumn == "singrouping_raca"}
									{continue}
								{/if}

								
									

								<td class="">{($datavalue)|escape}</td>

								
							
							{/foreach}
						</tr>
					{/foreach}		
				</tbody>
			</table>
		</div>

	</tbody>
</html>