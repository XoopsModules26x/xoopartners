<{foreach from=$categories item=category name=foo}>
<option value="<{$category.xoopartners_category_id}>"<{if $category_id ==$category.xoopartners_category_id}> selected<{/if}>><{php}>echo str_repeat("-", $this->_tpl_vars['pad']*5 ); <{/php}><{$category.xoopartners_category_title}></option>
<{if $category.categories}>
    <{assign var=pad value=$pad+1}>
    <{include file='module:xoopartners/xoopartners_categories.tpl' categories=$category.categories}>
    <{assign var=pad value=$pad-1}>
<{/if}>
<{/foreach}>
