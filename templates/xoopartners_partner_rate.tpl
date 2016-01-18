<div class="itemStat floatleft">
    <button id="button-stats" title="<{$smarty.const._XOO_PARTNERS_RATE_VOTES}>"><i class="stats"></i>
        <span class="average"><{$partner.xoopartners_rates}></span> / <span class="voters"><{$partner.xoopartners_vote}></span>
        <span class="yourvote <{if !$partner.xoopartners_yourvote}>hide<{/if}>">(<span class="vote"><{$partner.xoopartners_yourvote}></span>)</span>
    </button>
</div>

<div id="rating" class="rating">
    <{section name=foo loop=$xoopartners_rld.rate_scale}>
        <button id="button-stars<{if $smarty.section.foo.last}>-last<{/if}>" class="option" value="<{$smarty.section.foo.iteration}>" title="<{$smarty.section.foo.iteration}>"><i class="stars"></i>
        </button>
    <{/section}>
    <input type="hidden" id="partner_id" value="<{$partner.xoopartners_id}>">
</div>

<script language="javascript">
    <!--
    $(".option").click(function () {

        var option = $(this).val();
        var item = $("#partner_id").val();
        var token = "<{$security}>";

        $.ajax({
            type   : "POST",
            url    : "partner_rate.php",
            data   : "option=" + option + "&partner_id=" + item + "&XOOPS_TOKEN_REQUEST=" + token,
            success: function (responce) {
                var json = jQuery.parseJSON(responce);
//            alert( json.error );
                if (json.error == "0") {
                    $(".average").tpl(json.average);
                    $(".voters").tpl(json.voters);
                    $(".vote").tpl(json.vote);
                    $(".yourvote").removeClass("hide");
                }
            }
        });
    });
    //-->
</script>
