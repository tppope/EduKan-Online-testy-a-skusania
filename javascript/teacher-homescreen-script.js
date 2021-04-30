$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
    getLoggedInUser();
})

function getLoggedInUser() {
    $.getJSON("api/uzivatelia/prihlasenie/", function (data) {
        if (!data.error) {
            if (data.alreadyLogin) {
                let userName = data.user.meno + " " + data.user.priezvisko;
                $("#log-success-info").text("Vitajte " + userName + ". Boli ste úspešne prihlásený.");
                showLogInfo(data.status);
            } else {
                sessionStorage.setItem("logoutStatus", "failed");
                window.location.href = 'index.html';
            }
        }
    })
}

function showLogInfo(regInfo) {
    let logDiv = $("#log-" + regInfo);
    logDiv.css("top", 0);
    setTimeout(function () {
        logDiv.css("top", "-100px");
    }, 3000)
}

function logout() {
    $.getJSON("api/uzivatelia/odhlasenie/", function (data) {
        if (!data.error) {
            sessionStorage.setItem("logoutStatus", "success");
            window.location.href = 'index.html';
        }
    })
}
