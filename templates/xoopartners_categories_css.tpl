<style type="text/css">
    [class^="xoopartners-image-"], [class*=" xoopartners-image-"] {
        background-size: <{$xoopartners_category.image_width}>px;
    }

    <{if count($categories) != 0 && $xoopartners_category.display_mode != 'select'}>
    <{foreach from=$categories item=csscategory name=foo}>
    .xoopartners-ico-category <{$csscategory.xoopartners_category_id}> {
        background-image: url('<{$csscategory.xoopartners_category_image_link}>');
    }

    .xoopartners-image-category <{$csscategory.xoopartners_category_id}> {
        background-image: url('<{$csscategory.xoopartners_category_image_link}>');
    }

    .category <{$csscategory.xoopartners_category_id}> {
    <{if $csscategory.xoopartners_category_image != "blank.gif"}> padding-left: <{$xoopartners_category.image_width}>px;
    <{/if}> min-height: <{$xoopartners_category.image_height}>px;
    }

    <{/foreach}>
    <{else}>
    .xoopartners-ico-category <{$partner.xoopartners_id}> {
        background-image: url('<{$partner.xoopartners_image_link}>');
    }

    .xoopartners-image-category <{$partner.xoopartners_id}> {
        background-image: url('<{$partner.xoopartners_image_link}>');
    }

    .category <{$partner.xoopartners_id}> {
    <{if $partner.xoopartners_image != "blank.gif"}> padding-left: <{$xoopartners_category.image_width}>px;
    <{/if}> min-height: <{$xoopartners_category.image_height}>px;
    }

    <{/if}>
    .categoryImage img {
        max-width: <{$xoopartners_category.image_width}>px;
    }
</style>
