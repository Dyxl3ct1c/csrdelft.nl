<div style="width: 700px">
    {include "string:$beleid"}

    <p>
        <label><input type="radio" name="toestemming-intern" id="toestemming-ja"{if $akkoord == 'ja'} checked="checked"{/if}/> Mijn gegevens mogen gedeeld worden voor interne doeleinden. Dit geldt totdat ik dat verander.</label>
    </p>
    <p>
        <label><input type="radio" name="toestemming-intern" id="toestemming-nee"{if $akkoord == 'nee'} checked="checked"{/if}/> Ik wil graag instellen welke gegevens met gedeeld worden.</label>
    </p>

    <div id="toestemming-opties" style="{if $akkoord != 'nee'}display:none;{/if} width: 500px; clear: both;"><p>Maak een keuze, voor ieder veld moet een waarde ingevuld worden. Commissies die bepaalde gegevens nodig hebben om te kunnen functioneren blijven deze mogelijkheid houden.</p>
        {foreach from=$fields item=field}
            {$field}
        {/foreach}
    </div>
</div>

<script type="text/javascript">
    (function () {
        var toestemmingJa = $('#toestemming-ja'),
            toestemmingNee = $('#toestemming-nee'),
            toestemmingOpties = $('#toestemming-opties');

        toestemmingNee.on('change', function () {
            if (this.checked) {
                toestemmingOpties.show();
            } else {
                toestemmingOpties.hide();
            }
        });

        toestemmingJa.on('change', function () {
            if (this.checked) {
                selectRadioByValue('ja');
                toestemmingOpties.hide();
            }
        });
    })();
</script>