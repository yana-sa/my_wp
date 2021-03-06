jQuery(document).ready(function () {

    jQuery('[data-select="company_shares"]').change(function () {
        var shares = "You own ";
        jQuery("select option:selected").each(function() {
            shares += jQuery(this).attr('data-sum') + " shares";
            jQuery(this).closest('[data-form="exchange-offer"]').find('[data-input="number_of_shares"]').attr("max", jQuery(this).attr('data-sum'));
        });
        jQuery('[data-company="sum"]').text(shares);
    })
        .trigger( "change" );

    jQuery('[data-form="exchange-offer"]').submit(function (e) {
        e.preventDefault();
        var company_id = jQuery(this).find('select[data-select="company_shares"]').val()
        var shares = jQuery(this).find('input[data-input="number_of_shares"]').val()
        var price = jQuery(this).find('input[data-input="price"]').val()
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: {action:"shares_exchange_offer",
                company_id:company_id,
                shares:shares,
                price:price},
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    getExchangeOffersData();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    jQuery('[data-submit="purchase"]').click(function (e) {
        e.preventDefault();
        var offer_id = jQuery(this).attr('data-offer')
        var $this = jQuery(this)
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: {action:"shares_exchange_purchase", offer_id:offer_id},
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    $this.closest('tr').remove();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    jQuery('[data-submit="remove"]').click(function (e) {
        e.preventDefault();
        var offer_id = jQuery(this).attr('data-offer')
        var $this = jQuery(this)
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: {action:"shares_exchange_remove", offer_id:offer_id},
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    $this.closest('tr').remove();
                } else {
                    alert(response.message);
                }
            }
        });
    });
});

function getExchangeOffersData() {
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: {action: "get_exchange_offers_data"},
        success: function (response) {
            var offer_data = '';
            for(var i = 0; i < response.length; i++) {
                var data = response[i];
                offer_data += '<tr>';
                offer_data += '<td>'+ data.company +'</td>';
                offer_data += '<td>'+ data.seller +'</td>';
                offer_data += '<td>'+ data.shares +'</td>';
                offer_data += '<td>'+ data.price +'</td>';
                if(data.is_owner === false) {
                    offer_data += '<td><input type="submit" formmethod="post" data-submit="purchase" data-offer="'+ data.offer_id +'" value="Purchase"></td>'
                } else {
                    offer_data += '<td><input type="submit" formmethod="post" data-submit="remove" data-offer="'+ data.offer_id +'" value="Remove offer"></td>'
                }
                offer_data += '</tr>';
            }
            jQuery('[data-table="exchange-offers"]').html(offer_data);
        }
    });
}

