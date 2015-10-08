<ul class="ul-xoocategories">
    <{foreach from=$categories item=category name=foo}>
    <li>
        <a href="<{$category.xoopartners_category_link}>" title="<{$category.xoopartners_category_title}>"><i class="xoopartners-ico-category<{$category.xoopartners_category_id}>"></i><{$category.xoopartners_category_title}>
        </a></li>
    <{if $category.categories}>
        <{include file='module:xoopartners/xoopartners_categories.tpl' categories=$category.categories}>
    <{/if}>
    <{/foreach}>
</ul>
