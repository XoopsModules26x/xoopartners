<{assign var=pad value=$pad+2}>
<{foreach from=$categories item=category name=foo}>
<tr class="<{cycle values="even,odd"}>">
    <td style="padding-left:<{$pad}>em;">
        <a href="<{$category.xoopartners_category_link}>" title="<{$category.xoopartners_category_title}>"><i class="xoopartners-ico-category<{$category.xoopartners_category_id}>"></i><{$category.xoopartners_category_title}>
        </a></td>
</tr>
<{if $category.categories}>
    <{include file='module:xoopartners/xoopartners_categories.tpl' header=false categories=$category.categories}>
    <{assign var=pad value=$pad-2}>
<{/if}>
<{/foreach}>
