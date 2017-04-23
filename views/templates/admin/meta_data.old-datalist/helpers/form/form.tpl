{*
*  @author    Manuel José Pulgar Anguita
*  @copyright Manuel José Pulgar Anguita
*}

{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.type == 'text' || $input.type == 'datalist'}
		{if isset($input.lang) AND $input.lang}
			{if $languages|count > 1}
					<div class="form-group">
			{/if}
			{foreach $languages as $language}
				{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
				{if $languages|count > 1}
					<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
						<div class="col-lg-9">
				{/if}
				{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
					<div class="input-group{if isset($input.class)} {$input.class}{/if}">
				{/if}
				{if isset($input.maxchar) && $input.maxchar}
					<span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
						<span class="text-count-down">{$input.maxchar|intval}</span>
					</span>
				{/if}
				{if isset($input.prefix)}
					<span class="input-group-addon">
						{$input.prefix}
					</span>
				{/if}
				{if $input.type == 'datalist'}	
					<datalist id="datalist_{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}">{$input.options[$language.id_lang]}</datalist>
				{/if}
				<input type="text"
					{if $input.type == 'datalist'}
						list="datalist_{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
						autocomplete="off"
					{/if}
					id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
					name="{$input.name}_{$language.id_lang}"
					class="{if isset($input.class)}{$input.class}{/if}"
					value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
					onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
					{if isset($input.size)} size="{$input.size}"{/if}
					{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
					{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
					{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
					{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
					{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
					{if isset($input.required) && $input.required} required="required" {/if}
					{if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if} />
					{if isset($input.suffix)}
					<span class="input-group-addon">
						{$input.suffix}
					</span>
					{/if}
				{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
				</div>
				{/if}
				{if $languages|count > 1}
					</div>
					<div class="col-lg-2">
						<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
							{$language.iso_code}
							<i class="icon-caret-down"></i>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
							<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
							{/foreach}
						</ul>
					</div>
				</div>
				{/if}
			{/foreach}
			{if isset($input.maxchar) && $input.maxchar}
				<script type="text/javascript">
					$(document).ready(function(){
						{foreach from=$languages item=language}
							countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
						{/foreach}
					});
				</script>
			{/if}
			{if $languages|count > 1}
			</div>
		{/if}
		{else}
		
			{assign var='value_text' value=$fields_value[$input.name]}
			{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
			<div class="input-group{if isset($input.class)} {$input.class}{/if}">
			{/if}
			{if isset($input.maxchar) && $input.maxchar}
			<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
			{/if}
			{if isset($input.prefix)}
			<span class="input-group-addon">
				{$input.prefix}
			</span>
			{/if}
			{if $input.type == 'datalist'}	
				<datalist id="datalist_{if isset($input.id)}{$input.id}{else}{$input.name}{/if}">{$input.options}</datalist>
			{/if}
			<input type="text"
				{if $input.type == 'datalist'}
					list="datalist_{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
					autocomplete="off"
				{/if}
				name="{$input.name}"
				id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
				value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
				class="{if isset($input.class)}{$input.class}{/if}"
				{if isset($input.size)} size="{$input.size}"{/if}
				{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
				{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
				{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
				{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
				{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
				{if isset($input.required) && $input.required } required="required" {/if}
				{if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}
				/>
			{if isset($input.suffix)}
			<span class="input-group-addon">
				{$input.suffix}
			</span>
			{/if}

			{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
			</div>
			{/if}
			{if isset($input.maxchar) && $input.maxchar}
				<script type="text/javascript">
					$(document).ready(function(){
						countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
					});
				</script>
			{/if}
		{/if}	
	{else}
		{$smarty.block.parent}
	{/if}
{/block}{* end block input *}