jQuery(document).ready(function () {
    jQuery(".cell").hover(function() {
        var x = jQuery(this).attr("data-x");
        var y = jQuery(this).attr("data-y");
        var output = '<span>' + x + ':'+ y +'</span>';
        jQuery(this).css("background-color", "#ffffff6b");
        jQuery(this).append(jQuery(output));
    }, function() {
        jQuery(this).find("span").last().remove();
        jQuery(this).css("background-color", "unset");
    });
})