$(document).ready(function() {
    $('#readable-date').append(GetDateString(getUrlVars()['exerciseDate']));

    $.ajax({
        url: "//" + window.location.hostname + "/api/GetWorkoutCategories.php",
        dataType: "json",
        success: function(data) {
            for (var index in data)
            {
                $('#select-category').append('<option value="' + data[index]['id'] + '">' + data[index]['category'] + '</option>');
            }
                $.ajax({
                    url: "//" + window.location.hostname + "/api/GetCurrentWorkoutCategory.php",
                    method: 'GET',
                    data: {'date': getUrlVars()['exerciseDate']},
                    dataType: "json",
                    success: function(data) {
                        if (data != null)
                        {
                            $('#select-category option:eq(' + data['category'] + ')').prop('selected', true);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        var serverErrorInfo = JSON.parse(xhr.responseText);
                        console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
                    }
                });
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#exercise-name').html('Exercise Not Found');
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });

});

function onCategorySelect()
{
    $.ajax({
        url: "//" + window.location.hostname + "/api/SetWorkoutCategory.php",
        data: {'date': getUrlVars()['exerciseDate'], 'category_id': $('#select-category').val()},
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#acknowledge-success').fadeIn(300).delay(1000).fadeOut(1000);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
}

function GetDateString(inputDate)
{
    var monthStrings = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
        'October', 'November', 'December'];
    var dateEndingsArray = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

    var dateArray = inputDate.split('-');
    var monthAsInt = parseInt(dateArray[1], 10);
    var dayAsInt = parseInt(dateArray[2], 10);

    var dateEnding = 'th';
    if (dayAsInt < 10 || dayAsInt > 20)
    {
        dateEnding = dateEndingsArray[dayAsInt % 10];
    }

    return monthStrings[monthAsInt] + " " + dayAsInt + dateEnding;
}

