$(document).ready(function() {
    $('#readable-date').html(GetDateString(getUrlVars()['exerciseDate']));
    $('#add-exercise-button').prop('href', 'edit_exercise.html?date=' + getUrlVars()['exerciseDate']);

    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetWorkoutCategories.php",
        dataType: "json",
        success: function(data) {
            for (var index in data)
            {
                $('#select-category').append('<option value="' + data[index]['id'] + '">' + data[index]['category'] + '</option>');
            }
                $.ajax({
                    url: RELATIVE_ROOT_DIR + "/api/GetCurrentWorkoutCategory.php",
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

    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetFullPlanByDate.php",
        data: {'date': getUrlVars()['exerciseDate']},
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            for (var index in data)
            {
                addExercise(data[index]['exercise_id'], data[index]['name'], data[index]['muscles'],
                    data[index]['last_performed'], data[index]['goal_reps'], data[index]['goal_weights']);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });

});

function onCategorySelect()
{
    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/SetWorkoutCategory.php",
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

function addExercise(id, name, muscles, lastPerformed, goalReps, goalWeights)
{
    var thisDate = new Date();
    var performed = new Date(lastPerformed);
    var dayDifference = Math.ceil((thisDate.getTime() - performed.getTime()) / (1000 * 3600 * 24));
    $('#plan-table tbody').append('<tr id="planned-exercise-' + id + '"><td>' + name + '</td><td>'
        + muscles + '</td><td><a href="?exerciseDate=' + lastPerformed + '">' + dayDifference + ' days ago</a></td><td>'
        + goalReps + '</td><td>' + goalWeights + '</td><td><button onclick="removeExercise(' + id
        + ')"><img src="images/minus.svg" /></button></td><td><a href="edit_exercise.html?date='
        + getUrlVars()['exerciseDate'] + '&id=' + id + '" class="button"><img src="images/pencil.svg" /></a></td></tr>');
    console.log('we didn\'t add ' + name);
}

function removeExercise(exerciseId, name)
{
    if (confirm('Are you sure you want to remove ' + name + ' from your plan?'))
    {
        $.ajax({
            url: RELATIVE_ROOT_DIR + "/api/RemoveExerciseFromPlan.php",
            data: {'date': getUrlVars()['exerciseDate'], 'id': exerciseId},
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#planned-exercise-' + exerciseId).fadeOut(300, function() {$('#planned-exercise-' + exerciseId).remove();});
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
                var serverErrorInfo = JSON.parse(xhr.responseText);
                console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
            }
        });
    }
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

