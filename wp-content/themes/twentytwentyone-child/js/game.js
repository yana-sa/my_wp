jQuery(document).ready(function () {
    showBuildings();
    jQuery(".cell").hover(function () {
        jQuery(this).css("background-color", "rgb(237 216 175 / 50%)");
        if (jQuery(this).attr('data-building') !== undefined) {
            jQuery(this).find('[data-form="remove"]').show();
        }
    }, function () {
        jQuery(this).css("background-color", "unset");
        jQuery(this).find('[data-form="remove"]').hide();
    });

    var popup = jQuery('.popup');
    jQuery('.cell').click(function (e) {
        var x = jQuery(this).attr('data-x');
        var y = jQuery(this).attr('data-y');
        popup.css({left: e.pageX});
        popup.css({top: e.pageY});
        popup.find('input[name="x"]').val(x);
        popup.find('input[name="y"]').val(y);
        jQuery('[data-xy="coordinates"]').text(x + ':' + y);
        if (jQuery(this).attr('data-building') === undefined) {
            popup.hide();
            popup.show();
        } else {
            popup.hide();
        }
    });

    jQuery('.close').click(function () {
        popup.hide();
    })

    jQuery('[data-select="building_type"]').change(function () {
        var price = "Price: ";
        jQuery("select option:selected").each(function () {
            price += jQuery(this).attr('data-price') + "$";
        });
        jQuery('[data-price="price"]').text(price);
    })
        .trigger("change");

    add_building(popup);
    remove_building();
})

function showBuildings() {
    jQuery('.cell').each(function () {
        var currentElement = jQuery(this);
        var building = currentElement.attr('data-building');
        if (building !== undefined) {
            currentElement.css('background-image', 'url("/wp-content/plugins/game_plugin/images/' + building + '.png")');
        }
    });
}

function add_building(popup) {
    jQuery('[data-form="buy_building"]').submit(function (e) {
        e.preventDefault();
        var x = jQuery(this).find('input[name="x"]').val();
        var y = jQuery(this).find('input[name="y"]').val();
        var building_type = jQuery(this).find('select[data-select="building_type"]').val();
        var user_id = jQuery(this).find('input[data-input="user_id"]').val();
        popup.hide();
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: {
                action: "handle_add_building",
                building_type: building_type,
                user_id: user_id,
                x: x,
                y: y
            },
            success: function (response) {
                if (response.status === "success") {
                    var cell = jQuery('.cell[data-x=' + x + '][data-y=' + y + ']');
                    cell.css('background-image', 'url("/wp-content/plugins/game_plugin/images/' + building_type + '.png")');
                    cell.attr('data-building', building_type);
                    cell.append('<form method="post" data-form="remove" class="remove-form"><input type="submit" class="remove" value="x"></form>');
                    jQuery('.overlay').show().fadeOut(2000);
                    jQuery('[data-message="message"]').text(response.message);
                } else {
                    jQuery('.overlay').show().fadeOut(2000);
                    jQuery('[data-message="message"]').text(response.message);
                }
            }
        });
    });
}

function remove_building() {
    jQuery('[data-form="remove"]').submit(function (e) {
        e.preventDefault();
        var x = jQuery(this).closest('.cell').attr('data-x');
        var y = jQuery(this).closest('.cell').attr('data-y');
        var building = jQuery('.cell[data-x=' + x + '][data-y=' + y + ']').attr('data-building');
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: {
                action: "handle_remove_building",
                building: building,
                x: x,
                y: y
            },
            success: function (response) {
                if (response.status === "success") {
                    var cell = jQuery('.cell[data-x=' + x + '][data-y=' + y + ']');
                    cell.css('background-image', 'unset');
                    cell.removeAttr('data-building');
                    cell.find('[data-form="remove"]').remove();
                    jQuery('.overlay').show().fadeOut(2000);
                    jQuery('[data-message="message"]').text(response.message);
                } else {
                    jQuery('.overlay').show().fadeOut(2000);
                    jQuery('[data-message="message"]').text(response.message);
                }
            }
        });
    });
}