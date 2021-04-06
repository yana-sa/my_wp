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

    var popup = jQuery('.popup');
    var timer;
    jQuery('.cell').click(function(e) {
        var x = jQuery(this).attr("data-x");
        var y = jQuery(this).attr("data-y");
        popup.css({left: e.pageX});
        popup.css({top: e.pageY});
        popup.find('input[name="x"]').val(x);
        popup.find('input[name="y"]').val(y);
        popup.show();

        popup.hover(function(e) {
            popup.show();
        }, function() {
            popup.hide();
        });
/*
        clearTimeout(timer);
        timer = setTimeout(function() {
            popup.hide();
        }, 1000); */
    });

    jQuery('[data-select="building_type"]').change(function() {
        var price = "Price: ";
        jQuery("select option:selected").each(function() {
            price += jQuery(this).attr('data-price') + "$";
            jQuery(this).closest('[data-form="buy_building"]').find('[data-input="number_of_shares"]').attr("max", jQuery(this).attr('data-price'));
        });
        jQuery('[data-price="price"]').text(price);
    })
        .trigger("change");

    jQuery('[data-form="buy_building"]').submit(function(e) {
        e.preventDefault();
        var x = jQuery(this).find('input[name="x"]').val()
        var y = jQuery(this).find('input[name="y"]').val()
        var building_type = jQuery(this).find('select[data-select="building_type"]').val()
        var user_id = jQuery(this).find('input[data-input="user_id"]').val()
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: {action:"add_building",
                building_type:building_type,
                user_id:user_id, x:x, y:y},
            success: function (response) {
                alert(response);
            }
        });
    });
})