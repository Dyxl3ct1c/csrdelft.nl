<thead>
	<tr>
		<th style="width: 80px;">Wijzig</th>
		<th style="width: 70px;">Datum</th>
		<th>Titel</th>
		<th style="width: 60px;">Lijst</th>
		<th>Eters (Limiet)</th>
		<th>Status</th>
		<th style="text-align: center;">{strip}
			<a name="del" class="knop{if $prullenbak} confirm{/if}" onclick="taken_submit_range(this);" title="Selectie {if $prullenbak}definitief verwijderen{else}naar de prullenbak verplaatsen{/if}">
			{if $prullenbak}
				{icon get="cross"}
			{else}
				{icon get="bin_empty"}
			{/if}
			</a>
		</th>{/strip}
	</tr>
</thead>