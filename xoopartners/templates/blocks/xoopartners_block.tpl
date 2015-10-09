<{assign var=partners value=$block.partners}>

<{include file='module:xoopartners/xoopartners_partners_css.tpl'}>

<{if $block.template == 'list'}>
    <{include file="module:xoopartners/xoopartners_partners_list.tpl" partners=$block.partners}>
<{elseif $block.template == 'table'}>
    <{include file="module:xoopartners/xoopartners_partners_table.tpl" partners=$block.partners}>
<{elseif $block.template == 'news'}>
    <{include file="module:xoopartners/xoopartners_partners_news.tpl" partners=$block.partners}>
<{elseif $block.template == 'images'}>
    <{include file="module:xoopartners/xoopartners_partners_images.tpl" partners=$block.partners}>
<{/if}>
