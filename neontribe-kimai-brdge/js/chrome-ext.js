// https://github.com/kevinpapst/kimai2/issues/968
// https://trello.com/c/Pv2TK7jL/92-tabs-coppermines-cm-73

function getSettings() {
    var settings = [];

    callApi('/en/neontribe/ext/settings', function(data) {
        settings = data;
    }, console.log);

    return settings;
}

function gotoKimai() {
    window.open(kimaiUrl, '_blank');
}

function callApi(path, fnSuccess, fnError) {
    // var kimaiUrl = 'http://localhost';
    var url = kimaiUrl + path;

    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function (request) {
            request.setRequestHeader("X-AUTH-SESSION", "THING-1");
        },
        headers: {
            'X-AUTH-SESSION': "THING-1",
        },
        success: fnSuccess,
        error: fnError,
        async: false
    });
}

function parseUrl(url) {
    var a = $('<a>', {
        href: url
    });
    if (a.prop('hostname') == 'github.com') {
        return parseGitPath(a.prop('pathname'));
    } else if (a.prop('hostname') == 'trello.com') {
        return parseTrelloPath(a.prop('pathname'));
    }

    return false;
}

function parseGitPath(path) {
    var parts = path.split("/");
    if (parts.length < 5 || parts[3] !== 'issues') {
        return false;
    }

    return {
        'project': parts[2],
        'issue': parts.slice(1).join('-')
    }
}

function parseTrelloPath(path) {
    var parts = path.split("/");
    if (parts.length < 4 || parts[1] !== 'c') {
        return false;
    }

    return {
        'project': parts[2],
        'issue': parts.slice(2).join('-')
    }
}

function updateActivities(projectId) {
    var activities = getActivities(projectId);

    $("#activity").find('option').remove();  
    $.each(activities, function(key, value) {
        if (value.id == 1) console.log(value);
        $("#activity").append($('<option/>', { 
            value: value.id,
            text: value.name 
        }));
    });
}

function guessProject(tags) {
    // TODO Loop through tags and find the mode project
    // For now just return the last used
    if (tags.length > 0) {
        tag = tags.pop();
        return tag.project;
    }

    return false;
}

function getProjects() {
    var projects = [];

    callApi('/api/projects', function(data) {
        projects = data;
    }, console.log);

    return projects;
}

function getProjectId(projectName) {
    projects = getProjects();
    project = false;

    $.each(projects, function (index, element){
        if (element.name === projectName) {
            project = element;
        }
    });

    return project.id;
}

function getActivities(projectId) {
    var projectActivities = [];
    var globalActivities = [];

    callApi('/api/activities?project=' + projectId, function(data) {
        projectActivities = data;
    }, console.log);

    callApi('/api/activities?globals=true', function(data) {
        globalActivities = data;
    }, console.log);

    return $.extend(projectActivities, globalActivities);
}

function checkKimaiAvailable() {
    // There should be a better way of doing this
    // var kimai = 'http://localhost:8001';
    var url = kimaiUrl + '/api/projects';

    var action = 'All is good';

    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function (request) {
            request.setRequestHeader("X-AUTH-SESSION", "THING-1");
        },
        headers: {
            'X-AUTH-SESSION': "THING-1",
        },
        success: function (data, state, xhr) {
            if (!$.isArray(data)) {
                action = "goto login";
            } 
            // else { Everything is fine. }
        },
        error: function (data, state, xhr) {
            action = "unreachable"
        },
        async: false
    });

    return action;
}

function getTimesheets(uuid) {
    // curl -X GET "http://localhost/api/timesheets?tags=qwerty" -H  "accept: application/json"
    
    var timesheets = [];
    var tags = [];

    callApi('/api/tags', function(data) {
        tags = data;
    }, console.log);

    if ($.inArray(uuid, tags)) {
        callApi('/api/timesheets?user=all&tags=' + uuid, function (data) {
            timesheets = data;
        }, console.log);
    }

    return timesheets;
}

function getActivityName(activityId) {
    var name = "Unknown!";

    callApi('/api/activities/' + activityId, function(data) {
        name = data.name;
    }, console.log);

    return name;
}
