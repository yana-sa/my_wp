jQuery(document).ready(function () {
/*    jQuery('.overlay').show();
    jQuery('.start').click(function () {
        jQuery('.overlay').hide();
    });

    var haroldDiv = '<div class="harold"></div>';
    var tearsDiv = '<div class="boy-tears"></div>';
    var coinDiv = '<div class="coin"></div>';
    jQuery('.cell[data-x=1][data-y=10]').append(haroldDiv);
    jQuery('.cell[data-x=2][data-y=10]').append(tearsDiv);
    jQuery('.cell[data-x=3][data-y=10]').append(coinDiv);
    moveHarold(); */
});

function showIcons() {

}

function moveHarold() {
    jQuery(document).keydown(function(e){
        var harold = jQuery(".harold");
        var currentCell = harold.closest('.cell');
        switch (e.which){
            case 65:
                harold.finish().animate({
                    left: "-=100"
                });
                break;
            case 87:
                harold.finish().animate({
                    top: "-=100"
                });
                break;
            case 68:
                harold.finish().animate({
                    left: "+=100"
                });
                break;
            case 83:
                harold.finish().animate({
                    top: "+=100"
                });
                break;
        }
    });
}