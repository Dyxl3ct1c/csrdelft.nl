<div id="groep{$groep->getId()}" class="bb-block bb-groep">
	<div class="groepleden">
		{if $groep->toonPasfotos()}
			{assign var='actie' value='pasfotos'}
		{/if}
		{include file='groepen/groepleden.tpl'}
	</div>
	<div class="titel"><h3>{$groep->getLink()}</h3></div>
	<div class="beschrijving">{$groep->getSbeschrijving()|bbcode}</div>
	<div class="clear">&nbsp;</div>
</div>
