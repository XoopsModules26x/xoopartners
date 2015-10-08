<{if $moduletitle != ''}>
    <fieldset>
        <legend><{$moduletitle}>
            <a href="<{xoAppUrl modules/xoopartners/backend.php}>" title="<{$smarty.const._XOO_PARTNERS_RSS_FEED}>"><img src="<{xoImgUrl modules/xoopartners/assets/icons/32/rss.png}>"/></a></legend>
    </fieldset>
<{/if}>

<{if $welcome}>
    <div class="xoopartnersMsg">
        <{$welcome}>
    </div>
<{/if}>

<{if $categories}>
    <div class="xoopartnersSection">
        <div class="xoopartnersTitle"><{$smarty.const._XOO_PARTNERS_CATEGORIES}></div>
        <{if $category_header}>
            <{$category_header}>
        <{/if}>

        <{include file='module:xoopartners/xoopartners_categories.tpl' template=$xoopartners_category.display_mode}>

        <{if $category_footer}>
            <{$category_footer}>
        <{/if}>
        <div class="clear"></div>
    </div>
<{/if}>

<div class="xoopartnersSection">
    <{if $categories}>
        <div class="xoopartnersTitle"><{$smarty.const._XOO_PARTNERS_PARTNERS}><{if $selected.xoopartners_category_title}> : <{$selected.xoopartners_category_title}><{/if}></div>
    <{/if}>
    <{if $partner_header}>
        <{$partner_header}>
    <{/if}>

    <{include file='module:xoopartners/xoopartners_partners.tpl' template=$xoopartners_partner.display_mode}>

    <{if $partner_footer}>
        <{$partner_footer}>
    <{/if}>
    <div class="clear"></div>
</div>

<{include file='module:xoopartners/xoopartners_footer.tpl'}>
