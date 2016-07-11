$(document).ready(function() {
    PopulateExcerciseInformation(getUrlVars()['id']);
    PopulatePlanInformation(getUrlVars()['id']);
});

function PopulateExcerciseInformation(excerciseId)
{
    $.ajax({
        url: "//" + window.location.hostname + "/api/GetExcerciseById.php",
        dataType: "json",
        data: {"id": excerciseId},
        method: "GET",
        success: function(data) {
            $('#excercise-name').html(data['name']);
            if (data['reference_video'] != '')
            {
                $('#reference-video').html('<video width="100%" preload="metadata" poster="http://placehold.it/400x400?text=Show+Excercise" controls muted>'
                    + '<source src="' + data['reference_video'] + '" type="video/webm" /></video>');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#excercise-name').html('Excercise Not Found');
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
}

function PopulatePlanInformation(excerciseId)
{
    $.ajax({
        url: "//" + window.location.hostname + "/api/GetPlanByExcercise.php",
        dataType: "json",
        data: {"id": excerciseId},
        method: "GET",
        success: function(data) {
            $('#planned-weights').html(data['goal_weights']);
            $('#planned-reps').html(data['goal_reps']);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#planned-weights').html('Plan Not Found');
            $('#planned-reps').html('Plan Not Found');
            var serverErrorInfo = JSON.parse(xhr.responseText);
            console.error(serverErrorInfo['message'] + "\n" + xhr.status + " " + thrownError);
        }
    });
}

function SavePerformance()
{
    var excerciseId = getUrlVars()['id'];
    var inputRepetitions = $('input:text[id=setRepInput]').val();
    var inputWeights = $('input:text[id=weightInput]').val();
    var inputIncrease = ($('input[id=increaseInput]').is(':checked')) ? (1) : (0);
    $.ajax({
        url: "//" + window.location.hostname + "/api/SavePerfformance.php",
        dataType: "json",
        data: {"id": excerciseId, "repetitions": inputRepetitions, "weights": inputWeights, "ready_to_increase": inputIncrease},
        method: "POST",
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
