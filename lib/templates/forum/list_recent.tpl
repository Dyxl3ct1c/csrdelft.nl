<form id="forum_zoeken" action="/communicatie/forum/zoeken.php" method="post"><fieldset><input type="text" name="zoeken" value="zoeken in forum" onfocus="this.value='';" /></fieldset></form>

{capture name='navlinks'}
	<div class="forumNavigatie">
		<a href="/communicatie/forum/" class="forumGrootlink">Forum</a>
		<h1>Recente forumberichten</h1>
	</div>
{/capture}
{$smarty.capture.navlinks}
{$melding}

<table id="forumtabel">
	<tr>
		<th>Titel</th>
		<th>Reacties</th>
		<th>Verandering</th>
	</tr>
	{foreach from=$berichten item=bericht}
		<tr class="kleur{cycle values="0,1"}">
			<td class="titel">
				{if $bericht.soort=='T_POLL'}[peiling]{/if}
				{if $bericht.zichtbaar=='wacht_goedkeuring'}[ter goedkeuring...]{/if}
				<a href="/communicatie/forum/reactie/{$bericht.postID}" {if $bericht.momentGelezen<$bericht.lastpost} class="updatedTopic"{/if}>
					{if $bericht.plakkerig==1}
						<img src="{icon get="plakkerig" notag=true}" title="Dit onderwerp is plakkerig, het blijft bovenaan." alt="plakkerig" />&nbsp;&nbsp;
					{/if}	
					{if $bericht.open==0}
						<img src="{icon get="slotje" notag=true}" title="Dit onderwerp is gesloten, u kunt niet meer reageren" alt="sluiten" />&nbsp;&nbsp;
					{/if}
					{$bericht.titel|wordwrap:60:"\n":true|escape:'html'}
				</a>
			</td>
			<td class="reacties">{$bericht.reacties-1}</td>
			<td class="reactiemoment">
				{$bericht.lastpost|reldate}<br />
				<a href="/communicatie/forum/reactie/{$bericht.postID}">bericht</a> door 
				{$bericht.uid|csrnaam:'user'}
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="3">Deze categorie bevat nog geen berichten of deze categorie bestaat niet.</td>
		</tr>
	{/foreach}
	<tr>
		<th>Titel</th>
		<th>Reacties</th>
		<th>verandering</th>
	</tr>
</table>
{$smarty.capture.navlinks}