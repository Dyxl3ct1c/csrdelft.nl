{getMelding()}
<ul class="horizontal">
	{foreach from=$groeptypes item=groeptype}
		<li{if $groeptype.id==$groepen->getId()} class="active"{/if}>
			<a href="/groepen/{$groeptype.naam}">{$groeptype.naam}</a>
		</li>
	{/foreach}
</ul>
<hr />
{if !$groepen->getToonHistorie()}
	<div id="groepLijst">
		<ul>
			{foreach from=$groepen->getGroepen() item=groep name=g}
				<li><a href="#groep{$groep->getId()}">{$groep->getSnaam()}</a></li>
			{/foreach}	
		</ul>
	</div>
{/if}
{if $action=='edit'}
	<h1>{$groepen->getNaam()}</h1>
	<form action="/groepen/{$groepen->getNaam()}/?bewerken=true" method="post" class="Formulier">
		<div id="groepenFormulier" class="groepFormulier">
			<div id="bewerkPreview" class="bbcodePreview"></div>
			<label for="beschrijving"><strong>Beschrijving:</strong></label><br />
			<textarea id="typeBeschrijving" name="beschrijving" class="CsrBBPreviewField" rows="15" style="width:444px;">{$groepen->getBeschrijving()|escape:'html'}</textarea><br />
			<label for="submit"></label><input type="submit" id="submit" value="Opslaan" /> <input type="button" value="Voorbeeld" onclick="return CsrBBPreview('typeBeschrijving', 'bewerkPreview')" /> <a href="/groepen/{$groepen->getNaam()}/" class="btn">Terug</a>
			<a href="/wiki/cie:diensten:forum" target="_blank">Opmaakhulp</a>
			<a class="btn float-right vergroot" data-vergroot="#typeBeschrijving" title="Vergroot het invoerveld">&uarr;&darr;</a>
			<hr />
		</div>
	</form>
{else}
	{$groepen->getBeschrijving()|bbcode}
{/if}
<div class="clear">
	{if $groepen->isAdmin() OR $groepen->isGroepAanmaker()}
		<a href="/groepen/{$groepen->getNaam()}/0/bewerken" class="btn">Nieuwe {$groepen->getNaamEnkelvoud()}</a>
	{/if}	
	{if $groepen->isAdmin()}
		<a href="/groepen/{$groepen->getNaam()}/?maakOt=true" class="btn" 
		   onclick="return confirm('Weet u zeker dat alle h.t. groepen in deze categorie o.t. moeten worden?')">
			Maak h.t. groepen o.t.
		</a>
	{/if}
	{if LoginModel::mag('P_ADMIN') AND $action!='edit'}
		<a class="btn" href="/groepen/{$groepen->getNaam()}/?bewerken=true">
			<img src="/plaetjes/famfamfam/pencil.png" title="Bewerk beschrijving" />
		</a>
	{/if}
</div>

{foreach from=$groepen->getGroepen() item=groep}
	<div class="groep clear" id="groep{$groep->getId()}">
		<div class="groepleden">
			{if $groep->toonPasfotos()}
				{assign var='actie' value='pasfotos'}
			{/if}
			{include file='groepen/groepleden.tpl'}
		</div>
		<h3><a href="/groepen/{$groepen->getNaam()}/{$groep->getId()}">{$groep->getNaam()}</a></h3>
		{if $groep->getType()->getId()==11}
			Ouderejaars: {$groep->getEigenaar()}<br /><br />
		{/if} {* alleen bij Sjaarsacties *}
		{$groep->getSbeschrijving()|bbcode}
	</div>
{/foreach}
<hr class="clear" />
{if $groepen->isAdmin() OR $groepen->isGroepAanmaker()}
	<a href="/groepen/{$groepen->getNaam()}/0/bewerken" class="btn">Nieuwe {$groepen->getNaamEnkelvoud()}</a>
{/if}