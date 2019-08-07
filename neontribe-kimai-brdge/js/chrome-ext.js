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
    // var kimaiUrl = 'http://localhost:8001';
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

function searchByTag(tag) {
    var tags = [];

    callApi('/api/timesheets?tags=' + tag, function(data) {
        tags = data;
    }, console.log);

    return tags;
}

function updateActivities() {
    console.log("FOOF");
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