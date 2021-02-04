jQuery(document).ready(function () {
    jQuery('[data-button="evaluation"]').click(function (e) {
        e.preventDefault();
        post_id = jQuery(this).attr('data-post-id');
        evaluation = jQuery(this).attr('data-action');
        if (jQuery(this).attr('data-chosen') === undefined) {
            sendEvaluationRequest(post_id, evaluation);
        } else {
            sendResetEvaluationRequest(post_id, evaluation);
        }
    });
});

function sendEvaluationRequest() {
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: {action: "book_evaluation_data", post_id: post_id, evaluation: evaluation},
        success: function (response) {
            if (response.status === "success") {
                jQuery("#rating_for_books").html("Rating: " + response.rating_for_books);
                if (evaluation === 'like') {
                    jQuery("#like").attr("style", "background-color:#8c8c8c").attr("data-chosen", "true");
                }
                if (evaluation === 'dislike') {
                    jQuery("#dislike").attr("style", "background-color:#8c8c8c").attr("data-chosen", "true");
                }
            } else {
                alert(response.message);
            }
        }
    });
}

function sendResetEvaluationRequest() {
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: {action: "reset_book_evaluation", post_id: post_id},
        success: function (response) {
            if (response.status === "success") {
                jQuery("#rating_for_books").html("Rating: " + response.rating_for_books);
                if (evaluation === 'like') {
                    jQuery("#like").attr("style", "background-color:#efefef").removeAttr("data-chosen");
                }
                if (evaluation === 'dislike') {
                    jQuery("#dislike").attr("style", "background-color:#efefef").removeAttr("data-chosen");
                }
            } else {
                alert(response.message);
            }
        }
    });
}