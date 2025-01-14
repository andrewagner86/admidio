{if $data.property eq 4}
    <input class="form-control" type="{$type}" name="{$id}" id="{$id}" value="{$value}"
        {foreach $data.attributes as $itemvar}
            {$itemvar@key}="{$itemvar}"
        {/foreach}
    >
{else}
    <div id="{$id}_group" class="form-control-group{if $data.formtype eq "navbar"} form-floating{else} mb-4{/if}{if $property eq 1} admidio-form-group-required{/if}{if $type == 'datetime'} row{/if}">
        {if $data.formtype neq "navbar"}
            <label for="{$id}" class="form-label">
                {include file='sys-template-parts/parts/form.part.icon.tpl'}
                {$label}
            </label>
        {/if}
        {if $type == 'datetime'}
            <div class="col-auto">
                <input id="{$id}" name="{$id}" class="form-control {$class}" type="date" value="{$data.attributes.dateValue}"
                    {foreach $data.attributes.dateValueAttributes as $itemvar}
                        {$itemvar@key}="{$itemvar}"
                    {/foreach}
                >
            </div>
            <div class="col-auto">
                <input id="{$id}_time" name="{$id}_time" class="form-control {$class}" type="time" value="{$data.attributes.timeValue}"
                    {foreach $data.attributes.timeValueAttributes as $itemvar}
                        {$itemvar@key}="{$itemvar}"
                    {/foreach}
                >
            </div>
            {$htmlAfter}
        {elseif $type == 'date'}
            <input id="{$id}" name="{$id}" class="form-control {$class}" type="date" value="{$value}"
                {foreach $data.attributes as $itemvar}
                    {$itemvar@key}="{$itemvar}"
                {/foreach}
            >
            {$htmlAfter}
        {elseif $type == 'password'}
            <input id="{$id}" name="{$id}" class="form-control {$class}" type="{$type}" value="{$value}"
                {foreach $data.attributes as $itemvar}
                    {$itemvar@key}="{$itemvar}"
                {/foreach}
            >
            {if $data.passwordStrength eq 1}
                <div id="admidio-password-strength" class="progress {$data.attributes.class}">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                    <div id="admidio-password-strength-minimum"></div>
                </div>
            {/if}
            {$htmlAfter}
        {else}
            <input id="{$id}" name="{$id}" class="form-control {$class}" type="{$type}" value="{$value}"
                {foreach $data.attributes as $itemvar}
                    {$itemvar@key}="{$itemvar}"
                {/foreach}
            >
            {$htmlAfter}
        {/if}
        {if $data.formtype eq "navbar"}
            <label for="{$id}" class="form-label">
                {include file='sys-template-parts/parts/form.part.icon.tpl'}
                {$label}
            </label>
        {/if}
        {if $data.formtype eq "navbar"}
            {include file='sys-template-parts/parts/form.part.iconhelp.tpl'}
        {else}
            {include file='sys-template-parts/parts/form.part.helptext.tpl'}
        {/if}
        {include file='sys-template-parts/parts/form.part.warning.tpl'}
    </div>
{/if}
