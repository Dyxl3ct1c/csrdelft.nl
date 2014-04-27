<h1>Document {if $document->getID()==0}Toevoegen{else}Bewerken{/if}</h1>
<div class="foutje">{SimpleHtml::getMelding()}</div>
<br />
<form id="documentForm" method="post" enctype="multipart/form-data" action="/communicatie/documenten/bewerken/{$document->getId()}">
	<label for="naamInput" class="meta">Documentnaam</label> <input type="text" name="naam" id="naamInput" value="{$document->getNaam()}" /><br /><br />
	<label for="categorieInput" class="meta">Categorie</label>
	<select name="categorie" id="categorieInput">
		{foreach from=$categorieen item=categorie}
			<option value="{$categorie->getId()}"
					{if $categorie->getId()==$document->getCatID()}
						selected="selected"
					{/if}
					>{$categorie->getNaam()}</option>
		{/foreach}
	</select>
	<p>&nbsp;</p>
	{foreach from=$uploaders item=uploader}
		{$uploader->view()}
	{/foreach}
	<div>
		<label class="meta">&nbsp;</label>
		<button onclick="jQuery(this).closest('form').submit();">Opslaan</button>
		<button onclick="location.href = '/communicatie/documenten/';">Annuleren</button>
	</div>
</form>