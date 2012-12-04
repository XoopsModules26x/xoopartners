function showImgQRcode(imgId, moduleDir, extra, xoopsUrl)
{    if (xoopsUrl == null) {
        xoopsUrl = "./";
    }

    imgDom = xoopsGetElementById(imgId);

    background_id = "xoopartners_qrcode[backgroundColor]"
    background_Dom = xoopsGetElementById(background_id);
    query = "&bgcolor=" + background_Dom.options[background_Dom.selectedIndex].value;

    foreground_id = "xoopartners_qrcode[foregroundColor]"
    foreground_Dom = xoopsGetElementById(foreground_id);
    query += "&fgcolor=" + foreground_Dom.options[foreground_Dom.selectedIndex].value;

    margin_id = "xoopartners_qrcode[whiteMargin]"
    margin_Dom = xoopsGetElementById(margin_id);
    query += "&margin=" + margin_Dom.options[margin_Dom.selectedIndex].value;

    correction_id = "xoopartners_qrcode[CorrectionLevel]"
    correction_Dom = xoopsGetElementById(correction_id);
    query += "&correction=" + correction_Dom.options[correction_Dom.selectedIndex].value;

    size_id = "xoopartners_qrcode[matrixPointSize]";
    size_Dom = xoopsGetElementById(size_id);
    query += "&size=" + size_Dom.options[size_Dom.selectedIndex].value;

    imgDom.src = xoopsUrl + moduleDir + "/qrcode.php?" + extra + query;

    size_px = 37 * size_Dom.options[size_Dom.selectedIndex].value;
    imgDom.style.width = size_px + "px";
    imgDom.style.height = size_px + "px";
}
