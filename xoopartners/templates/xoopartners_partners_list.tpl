<ul class="ul-xoopartners">
    <{foreach from=$partners item=partner name=foo}>
    <li><a href="<{$partner.xoopartners_link}>" title="<{$partner.xoopartners_title}>"><i class="xoopartners-ico-partner<{$partner.xoopartners_id}>"></i><{$partner.xoopartners_title}></a></li>
    <{/foreach}>
</ul>
