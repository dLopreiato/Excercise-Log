var DATE_PARAM = getUrlVars()['date'];
var ID_PARAM = getUrlVars()['id'];
var EXERCISE_LIST = null;
var FULL_EXERCISES = null;
var CURRENT_PAGE = 0;
var HISTORY_PAGES = null;

$(document).ready(function() {
    $('#back-button').prop('href', 'edit_plan.html?exerciseDate=' + DATE_PARAM);

    if (ID_PARAM == null)
    {
        PreparePageForNewExercise();
    }
    else
    {
        PreparePageForEditing();
    }
    
});

function PreparePageForEditing()
{
    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetExerciseById.php",
        data: {'id': ID_PARAM},
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#exerciseInput').html('<option selected value="' + ID_PARAM + '">' + data['name'] + '</option>');
            $('#muscleFilter').html('');
            for (var muscleIndex in data['muscles'])
            {
               $('#muscleFilter').append('<option>' + data['muscles'][muscleIndex]['name'] + '</option>');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });

    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetPlanByExercise.php",
        data: {'date': DATE_PARAM, 'id': ID_PARAM},
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#setRepInput').val(data['goal_reps']);
            $('#weightInput').val(data['goal_weights']);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
    showHistoryPage(0);
}

function PreparePageForNewExercise()
{
    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetAllMuscles.php",
        dataType: 'json',
        success: function(data) {
            for (var index in data)
            {
                $('#muscleFilter').append('<option value="' + data[index]['id'] + '">' + data[index]['name'] + '</option>');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });

    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetAllExercises.php",
        dataType: 'json',
        success: function(data) {
            FULL_EXERCISES = data;
            for (var index in data)
            {
                $('#exerciseInput').append('<option value="' + data[index]['id'] + '">' + data[index]['name'] + '</option>');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });

    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetExercisesByMuscle.php",
        dataType: 'json',
        success: function(data) {
            EXERCISE_LIST = data;
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
}

function onMuscleFilterChange()
{
    if (ID_PARAM != null)
    {
        return;
    }
    $('#exerciseInput').html('');
    $('#exerciseInput').append('<option disabled selected value="-1">Choose an Exercise</option>');

    if ($('#muscleFilter').val() == 0)
    {
        for (var index in FULL_EXERCISES)
            {
                $('#exerciseInput').append('<option value="' + FULL_EXERCISES[index]['id'] + '">' + FULL_EXERCISES[index]['name'] + '</option>');
            }
    }
    else
    {
        for (var index in EXERCISE_LIST)
        {
            if (EXERCISE_LIST[index]['muscle_id'] == $('#muscleFilter').val())
            {
                $('#exerciseInput').append('<option value="' + EXERCISE_LIST[index]['exercise_id'] + '">' + EXERCISE_LIST[index]['exercise'] + '</option>');
            }
        }
    }
}

function onExerciseChange()
{
    ID_PARAM = $('#exerciseInput').val();
    showHistoryPage(0);
}

function onSaveClick()
{
    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/SavePlanedExercise.php",
        data: {'date': getUrlVars()['date'], 'exercise_id': $('#exerciseInput').val(), 'reps': $('#setRepInput').val(),
            'weights': $('#weightInput').val()},
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

function showHistoryPage(pageNumber)
{
    $('#history-table tbody').html('');
    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetExerciseHistory.php",
        data: {'id': ID_PARAM, 'page': pageNumber},
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            HISTORY_PAGES = data['totalPages'];
            if (HISTORY_PAGES > 1)
            {
                $('.history-older').show();
            }
            for (var histInd in data['history'])
            {
                $('#history-table tbody').append('<tr><td>' + ((data['history'][histInd]['ready_to_increase'] == 1) ? ('<span title="Ready to Increase">(+)</span>') : (''))
                    + '</td><td>' + data['history'][histInd]['performed_reps'] + '</td><td>'
                    + data['history'][histInd]['performed_weights'] 
                    + '</td><td>X DAYS AGO</td><td>CATEGORY</td></tr>');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#acknowledge-failure').fadeIn(300).delay(1000).fadeOut(1000);
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
}

function navigateHistory(direction)
{
    if (direction == 'first')
    {
        CURRENT_PAGE = 0;
    }
    else if (direction == 'back')
    {
        CURRENT_PAGE -= 1;
        if (CURRENT_PAGE < 0)
        {
            CURRENT_PAGE = 0;
        }
    }
    else if (direction == 'next')
    {
        CURRENT_PAGE += 1;
        if (CURRENT_PAGE >= HISTORY_PAGES)
        {
            CURRENT_PAGE = HISTORY_PAGES - 1;
        }
    }
    else if (direction == 'last')
    {
        CURRENT_PAGE = HISTORY_PAGES;
    }
    if (CURRENT_PAGE == 0)
    {
        $('.history-newer').hide();
    }
    else
    {
        $('.history-newer').show();
    }
    if (CURRENT_PAGE == HISTORY_PAGES)
    {
        $('.history-older').hide();
    }
    else
    {
        $('.history-older').show();
    }
    showHistoryPage(CURRENT_PAGE);
}
