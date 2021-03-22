jQuery(document).ready(function () {
    var d = new Date();
    var month = d.getMonth() + 1;
    buildChart(month);
    jQuery('[data-form="select-month"]').submit(function (e) {
        e.preventDefault();
        var month = jQuery(this).find('[data-selected="month"]:selected').val();
        buildChart(month);
    })
});

function buildChart(month) {
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: myAjax.ajaxurl,
        data: {action: "users_activity_statistics", month: month},
        success: function (response) {
            var date = new Date(2021, month - 1,);
            var theMonth = date.toLocaleString('en-us', {month: 'long'});
            jQuery('[data-month="display-month"]').text(theMonth);

            var ctx = document.getElementById('statchart').getContext('2d');
            var statistics = [];
            var days = [];

            for (var auth in response) {
                if (response.hasOwnProperty(auth)) {
                    var postData = response[auth];
                    var dataObject = prepareDetails(auth, postData);
                    statistics.push(dataObject);
                }
            }

            if (postData !== undefined) {
                for (var i = 1; i < postData.length; i++) {
                    days.push(i);
                }
            } else {
                alert('No data for this month')
            }

            var chartData = {
                labels: days,
                datasets: statistics
            };

            var chart = new Chart(ctx, {
                type: "line",
                data: chartData,
                options: {}
            });

            function prepareDetails(author, postData) {
                var dataColor = getRandomColor();
                return {
                    label: author,
                    data: postData,
                    backgroundColor: 'transparent',
                    borderColor: dataColor,
                    pointBackgroundColor: dataColor,
                    fill: false,
                    lineTension: 0,
                    pointRadius: 3
                }
            }

            function getRandomColor() {
                var letters = '0123456789ABCDEF'.split('');
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
        }
    });
}