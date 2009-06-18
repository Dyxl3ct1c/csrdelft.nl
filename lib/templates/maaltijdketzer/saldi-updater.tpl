<ul class="horizontal nobullets">
	<li>
		<a href="/actueel/maaltijden/" title="Maaltijdketzer">Maaltijdketzer</a>
	</li>
	<li>
		<a href="/actueel/maaltijden/voorkeuren/" title="Instellingen">Instellingen</a>
	</li>
	{if $loginlid->hasPermission('P_MAAL_MOD')}
		<li>
			<a href="/actueel/maaltijden/beheer/" title="Beheer">Maaltijdbeheer</a>
		</li>
		<li>
			<a href="/actueel/maaltijden/corveebeheer/" title="Beheer">Corveebeheer</a>
		</li>
		<li class="active">
			<a href="/actueel/maaltijden/saldi.php" title="Saldo's updaten">Saldo's updaten</a>
		</li>
	{/if}
</ul>
<hr />
<h1>MaalCie-saldi invoeren met een CSV-bestand.</h1>
{if $status!=''}
	<div class="waarschuwing">{$status}</div><br />
{/if}
<form name="CSVForm" action="saldi.php" method="post" enctype="multipart/form-data">
	<label for="CSVSaldi">CSV-bestand uploaden</label> 
	<input type="file" name="CSVSaldi" id="CSVSaldi" size="64" /><br />
	<input type="submit" name="submit" value="uploaden" />
</form>
