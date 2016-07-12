$(document).ready(function() {
    $.ajax({
        url: "//" + window.location.hostname + "/api/GetTodaysExcercises.php",
        dataType: "json",
        success: function(data) {
            for (var index in data)
            {
                AddExcercise(data[index]['excercise_id'], data[index]['name'], data[index]['completed']);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
});

function AddExcercise(id, name, completed)
{
    $('#excercise-list').append('<div class="row listed-exercise-name"><div class="six columns ' + ((completed == 1) ? 'completed': '') + '"><hr /><a href="performance_input.html?id=' + id + '">' + name + '</a></div></div>');
}
