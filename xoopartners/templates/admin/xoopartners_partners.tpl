<{include file="admin:system/admin_navigation.tpl"}>
<{include file="admin:system/admin_tips.tpl"}>

<{if $categories}>
    <div class="floatleft">
        <{$smarty.const._XOO_PARTNERS_CATEGORY_TITLE}>&nbsp;:&nbsp;<{$categories}>
    </div>
<{/if}>
<{include file="admin:system/admin_buttons.tpl"}>

<!-- Display form -->
<{if $form}>
    <{$form}>
<{/if}>

<{if $partners}>
    <table class="outer">
        <thead>
        <tr>
            <th class="txtcenter width60"><{$smarty.const._XOO_PARTNERS_TITLE}></th>
            <th class="txtcenter"><{$smarty.const._XOO_PARTNERS_ORDER}></th>
            <th class="txtcenter"><{$smarty.const._XOO_PARTNERS_DISPLAY}></th>
            <th class="txtcenter"><{$smarty.const._XOO_PARTNERS_ACCEPTED}></th>
            <th class="txtcenter"><{$smarty.const._AM_XOO_PARTNERS_ACTION}></th>
        </tr>
        </thead>

        <{foreach from=$partners item=partner}>
        <{assign var=pad value=0}>
        <tr class="even">
            <td>
                <a href="<{xoAppUrl '/modules/xoopartners/'}>partner.php?partner_id=<{$partner.xoopartners_id}>" title="<{$partner.xoopartners_title}>"><{$partner.xoopartners_title}></a>
            </td>

            <td class="txtcenter">
                <{$partner.xoopartners_order}>
            </td>

            <td class="txtcenter">
                <{if ( $partner.xoopartners_online )}>
                    <a href="partners.php?op=hide&amp;xoopartners_id=<{$partner.xoopartners_id}>&category_id=<{$category_id}>" title="<{$smarty.const._XOO_PARTNERS_SHOW_HIDE}>"><img src="<{xoImgUrl 'media/xoops/images/icons/16/on.png'}>" alt="<{$smarty.const._AM_XOO_PARTNERS_HIDE}>"></a>
                <{else}>
                    <a href="partners.php?op=view&amp;xoopartners_id=<{$partner.xoopartners_id}>&category_id=<{$category_id}>" title="<{$smarty.const._XOO_PARTNERS_SHOW_HIDE}>"><img src="<{xoImgUrl 'media/xoops/images/icons/16/off.png'}>" alt="<{$smarty.const._AM_XOO_PARTNERS_SHOW}>"></a>
                <{/if}>
            </td>

            <td class="txtcenter">
                <{if ( $partner.xoopartners_accepted )}>
                    <a href="partners.php?op=naccept&amp;xoopartners_id=<{$partner.xoopartners_id}>&category_id=<{$category_id}>" title="<{$smarty.const._XOO_PARTNERS_ACCEPTED_NO}>"><img src="<{xoImgUrl 'media/xoops/images/icons/16/on.png'}>" alt="<{$smarty.const._XOO_PARTNERS_ACCEPTED_NO}>"></a>
                <{else}>
                    <a href="partners.php?op=accept&amp;xoopartners_id=<{$partner.xoopartners_id}>&category_id=<{$category_id}>" title="<{$smarty.const._XOO_PARTNERS_ACCEPTED_YES}>"><img src="<{xoImgUrl 'media/xoops/images/icons/16/off.png'}>" alt="<{$smarty.const._XOO_PARTNERS_ACCEPTED_YES}>"></a>
                <{/if}>
            </td>

            <td class="txtcenter">
                <a href="partners.php?op=edit&amp;xoopartners_id=<{$partner.xoopartners_id}>&category_id=<{$category_id}>" title="<{$smarty.const._EDIT}>"><img src="<{xoImgUrl 'media/xoops/images/icons/16/edit.png'}>" alt="{$smarty.const._EDIT}>"></a>
                <a href="partners.php?op=del&amp;xoopartners_id=<{$partner.xoopartners_id}>&category_id=<{$category_id}>" title="<{$smarty.const._DELETE}>"><img src="<{xoImgUrl 'media/xoops/images/icons/16/delete.png'}>" alt="<{$smarty.const._DELETE}>"></a>
            </td>
        </tr>
        <{/foreach}>
    </table>
<{/if}>
