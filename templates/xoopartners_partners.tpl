<{include file='module:xoopartners/xoopartners_partners_css.tpl'}>

<{$xoopaginate->display()}>
<{if $template == 'blog'}>
    <{include file='module:xoopartners/xoopartners_partners_blog.tpl'}>
<{elseif $template == 'list'}>
    <{include file='module:xoopartners/xoopartners_partners_list.tpl'}>
<{elseif $template == 'table'}>
    <{include file='module:xoopartners/xoopartners_partners_table.tpl'}>
<{elseif $template == 'images'}>
    <{include file='module:xoopartners/xoopartners_partners_images.tpl'}>
<{/if}>
<{$xoopaginate->display()}>
