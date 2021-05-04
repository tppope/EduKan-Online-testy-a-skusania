$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
    //getLoggedInUser();
    getStudents();

})

function getLoggedInUser() {
    $.getJSON("api/uzivatelia/prihlasenie/", function (data) {
        if (!data.error) {
            if (data.alreadyLogin) {
                let userName = data.user.meno + " " + data.user.priezvisko;
                $("#log-success-info").text("Vitajte " + userName + ". Boli ste úspešne prihlásený.");
                showLogInfo(data.status);
                showUserName(userName);
            } else {
                sessionStorage.setItem("logoutStatus", "failed");
                window.location.href = 'index.html';
            }
        }
    })
}

function logout() {
    $.getJSON("api/uzivatelia/odhlasenie/", function (data) {
        if (!data.error) {
            sessionStorage.setItem("logoutStatus", "success");
            window.location.replace('index.html');
        }
    })
}

function getStudents(){

}
