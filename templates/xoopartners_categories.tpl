<{include file='module:xoopartners/xoopartners_categories_css.tpl'}>
<{if $template == 'select'}>
    <{include file='module:xoopartners/xoopartners_categories_select.tpl' categories=$categories}>
<{elseif $template == 'list'}>
    <{include file='module:xoopartners/xoopartners_categories_list.tpl' categories=$categories}>
<{elseif $template == 'table'}>
    <{include file='module:xoopartners/xoopartners_categories_table.tpl' categories=$categories}>
<{elseif $template == 'images'}>
    <{include file='module:xoopartners/xoopartners_categories_images.tpl' categories=$categories}>
<{/if}>
