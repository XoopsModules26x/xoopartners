<table class="outer">
    <{foreach from=$partners item=partner name=foo}>
    <tr class="<{cycle values="even,odd"}>">
        <td><a href="<{$partner.xoopartners_link}>" title="<{$partner.xoopartners_title}>"><i class="xoopartners-ico-partner<{$partner.xoopartners_id}>"></i><{$partner.xoopartners_title}></a></td>
    </tr>
    <{/foreach}>
</table>
