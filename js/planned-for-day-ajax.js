$(document).ready(function() {
    $.ajax({
        url: RELATIVE_ROOT_DIR + "/api/GetTodaysExercises.php",
        dataType: "json",
        success: function(data) {
            for (var index in data)
            {
                AddExercise(data[index]['exercise_id'], data[index]['name'], data[index]['completed']);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
});

function AddExercise(id, name, completed)
{
    $('#exercise-list').append('<div class="row barred-list"><div class="six columns ' + ((completed == 1) ? 'completed': '') + '"><hr /><a href="performance_input.html?id=' + id + '">' + name + '</a></div></div>');
}
