/* jQuery(document).ready(function () {
    jQuery('.overlay').show();
    jQuery('.start').click(function () {
        jQuery('.overlay').hide();
    });

    var startCell = jQuery('.cell[data-x=1][data-y=1]');
    jQuery(".harold").show();
    moveHarold();
});*/

function moveHarold() {
    jQuery(document).keydown(function(e){
        switch (e.which){
            case 37:
                jQuery(".harold").finish().animate({
                    left: "-=50"
                });
                break;
            case 38:
                jQuery(".harold").finish().animate({
                    top: "-=50"
                });
                break;
            case 39:
                jQuery(".harold").finish().animate({
                    left: "+=50"
                });
                break;
            case 40:
                jQuery(".harold").finish().animate({
                    top: "+=50"
                });
                break;
        }
    });
}