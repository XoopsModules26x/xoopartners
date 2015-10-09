<{foreach from=$categories item=category name=foo}>
<div class="categoryImage txtcenter">
    <a href="<{$category.xoopartners_category_link}>" title="<{$category.xoopartners_category_title}>"><img src="<{$category.xoopartners_category_image_link}>" alt="<{$category.xoopartners_category_title}>"></a>

    <div class="txtcenter">
        <a href="<{$category.xoopartners_category_link}>" title="<{$category.xoopartners_category_title}>"><{$category.xoopartners_category_title}></a>
    </div>
</div>
<{if $category.categories}>
    <{include file='module:xoopartners/xoopartners_categories.tpl' categories=$category.categories}>
<{/if}>
<{/foreach}>
