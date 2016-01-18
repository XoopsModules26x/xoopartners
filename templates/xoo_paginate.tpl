<{if isset($xoopages)}>
    <link rel="stylesheet" href="<{xoImgUrl 'modules/xoopartners/assets/css/xoopaginate.css'}>" type="text/css" media="screen"/>
    <div id="pagination">
        <div class="floatleft prevnext">
            <{if $xoopaginate->getValue('first') != false}>
                <a href="<{$xoopaginate->getValue('first')}>" title="" style="text-decoration: underline;">&laquo;&laquo;</a>
            <{else}>
                <span class="disabled"></span>
            <{/if}>

            <{if $xoopaginate->getValue('prev') != false}>
                <a href="<{$xoopaginate->getValue('prev')}>" title="" style="text-decoration: underline;">&laquo;</a>
            <{else}>
                <span class="disabled"></span>
            <{/if}>
        </div>

        <div class="floatright prevnext">
            <{if $xoopaginate->getValue('next') != false}>
                <a href="<{$xoopaginate->getValue('next')}>" title="" style="text-decoration: underline;">&raquo;</a>
            <{else}>
                <span class="disabled"></span>
            <{/if}>

            <{if $xoopaginate->getValue('last') != false}>
                <a href="<{$xoopaginate->getValue('last')}>" title="" style="text-decoration: underline;">&raquo;&raquo;</a>
            <{else}>
                <span class="disabled"></span>
            <{/if}>
        </div>

        <div>
            <{foreach from=$xoopages item=xoopage name=foo}>
                <{if $xoopage.link != false}>
                    <a <{if $xoopaginate->getValue('current') == $xoopage.value}>class="current" <{/if}>href="<{$xoopage.link}>" title="<{$xoopage.text}>"><{$xoopage.text}></a>
                <{else}>
                    <span>...</span>
                <{/if}>
            <{/foreach}>
        </div>
    </div>
    <div class="clear"></div>
<{/if}>
