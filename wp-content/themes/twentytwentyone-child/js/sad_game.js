jQuery(document).ready(function () {
    jQuery('.overlay').show();
    jQuery('.start').click(function () {
        jQuery('.overlay').hide();
    });

    var haroldDiv = '<div class="harold"></div>';
    var tearsDiv = '<div class="boy-tears"></div>';
    var coinDiv = '<div class="coin"></div>';
    jQuery('.cell[data-x=1][data-y=10]').append(haroldDiv);
    moveHarold();
    console.log(randCell())
});

var frequency = 5000;
var interval = 0;

function startLoop() {
    if(interval > 0) clearInterval(interval);
    interval = setInterval("showTears()", frequency);
}

function randCell() {
    var x = Math.floor((Math.random() * 10) + 1);
    var y = Math.floor((Math.random() * 10) + 1);
    return jQuery('.cell[data-x=' + x + '][data-y=' + y + ']');
}

function showTears(tearsDiv) {
    jQuery(tearsDiv).fadeIn('slow', function(){
        jQuery(tearsDiv).delay(5000).fadeOut();
    });
}

function showCoin(coinDiv) {
    jQuery(coinDiv).fadeIn('slow', function(){
        jQuery(coinDiv).delay(5000).fadeOut();
    });
}

function moveHarold() {
    jQuery(document).keydown(function(e){
        var harold = jQuery('.harold');
        switch (e.which){
            case 65:
                harold.finish().animate({
                    left: '-=100'
                });
                break;
            case 87:
                harold.finish().animate({
                    top: '-=100'
                });
                break;
            case 68:
                harold.finish().animate({
                    left: '+=100'
                });
                break;
            case 83:
                harold.finish().animate({
                    top: '+=100'
                });
                break;
        }
    });
}