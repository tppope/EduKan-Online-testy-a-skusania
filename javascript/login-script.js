$(window).on("load", function () {
    checkIfAlreadyLogin();
    $('[data-toggle="tooltip"]').tooltip();
    $("#reg-button").hide();
    checkRegStatus();
    checkLogoutStatus()
})

function checkLogoutStatus() {
    let logoutInfo = sessionStorage.getItem("logoutStatus");
    if (logoutInfo != null) {
        if (logoutInfo === 'failed')
            $("#log-failed-info").text("Najprv sa prosím prihláste");
        showLogInfo(logoutInfo);
        sessionStorage.removeItem('logoutStatus');
    }
}

function checkRegStatus() {
    let regInfo = sessionStorage.getItem("regStatus");
    if (regInfo != null) {
        showRegInfo(regInfo);
        sessionStorage.removeItem('regStatus');
    }
}

function showRegInfo(regInfo) {
    let regDiv = $("#reg-" + regInfo);
    regDiv.css("top", 0);
    setTimeout(function () {
        regDiv.css("top", "-100px");
    }, 3000)
}

function showLogInfo(regInfo) {
    let logDiv = $("#log-" + regInfo);
    logDiv.css("top", 0);
    setTimeout(function () {
        logDiv.css("top", "-100px");
    }, 3000)
}

function changeLoginType(choiceType) {
    changeChoice(choiceType);
    changeContent(choiceType.id);
}

function changeChoice(choice) {
    $(".choice-button").css({
        "background-color": "#f0f0f0",
        "color": "black",
    });
    $(choice).css({
        "background-color": "white",
        "color": "black",
    });
}

function changeContent(content) {
    $(".teacher-login-content").hide();
    $(".student-login-content").hide();
    $(`.${content}-content`).show();
}

function submitTeacherLoginForm() {
    let form = document.getElementById("teacher-login-form");
    if (checkFormValidation(form)) {
        let request = new Request('api/uzivatelia/prihlasenie/', {
            method: 'POST',
            body: new FormData(form),
        });
        fetch(request)
            .then(response => response.json())
            .then(data => {
                let email = $("#email");
                let password = $("#password");
                if (!data.error) {
                    if (data.emailVerify && data.passwordVerify) {
                        window.location.href = 'teacher-homescreen.html';
                        sessionStorage.setItem("fromLogin", "true");
                    }
                    else if (!data.emailVerify)
                        email.addClass("is-invalid");
                    else if (!data.passwordVerify)
                        password.addClass("is-invalid");
                } else {
                    $("#log-failed-info").text("Prihlásenie neprebehlo úspešne. Skúste to prosím znovu.");
                    showLogInfo(data.status);
                }
            });
    }
    return false;
}

function submitStudentLoginForm(){
    let form = document.getElementById("student-login-form");
    if (checkFormValidation(form)) {
        let request = new Request('api/uzivatelia/prihlasenieStudenta/', {
            method: 'POST',
            body: new FormData(form),
        });
        fetch(request)
            .then(response => response.json())
            .then(data => {
                let key = $("#key");
                if (!data.error){
                    sessionStorage.setItem("key",key.val());
                    window.location.href = "doTest/test.html";
                }
                else {
                    if (data.badTestKey)
                        key.addClass("is-invalid")
                }
            });
    }
    return false;
}

function checkFormValidation(form) {
    let inputs = $(form).find("input");
    for (let i = 0; i < inputs.length; i++) {
        if (!inputs.get(i).checkValidity())
            return false;
    }
    return true;
}

function checkIfAlreadyLogin() {
    $.getJSON("api/uzivatelia/prihlasenie/", function (data) {
        if (!data.error) {
            if (data.alreadyLogin) {
                window.location.href = 'teacher-homescreen.html';
                sessionStorage.setItem("fromLogin", "true");
            }
        }
    })
}

function removeIsInvalid(dom) {
    $(dom).removeClass("is-invalid");
}


