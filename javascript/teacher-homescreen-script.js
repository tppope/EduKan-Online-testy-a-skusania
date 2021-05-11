$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
    getLoggedInUser();
    setLottieHover();
    getTeacherTests();

})

function getLoggedInUser() {
    $.getJSON("api/uzivatelia/prihlasenie/", function (data) {
        if (!data.error) {
            if (data.alreadyLogin) {
                let userName = data.user.meno + " " + data.user.priezvisko;
                if (sessionStorage.getItem("fromLogin") === "true") {
                    $("#log-success-info").text("Vitajte " + userName + ". Boli ste úspešne prihlásený.");
                    showLogInfo(data.status);
                }
                showUserName(userName);
            } else {
                sessionStorage.setItem("logoutStatus", "failed");
                sessionStorage.removeItem("fromLogin");
                window.location.href = 'index.html';
            }
        }
        sessionStorage.removeItem("fromLogin");
    })
}

function getTeacherTests(){
    $.getJSON("api/testy/praca-s-testami.php?akcia=zoznam-testov", function (data){
        printTests(data.zoznam_testov)
    })
}

function printTests(zoznamTestov){
    let tbodyTests = $("#tests-tbody");
    if (zoznamTestov.length === 0)
        createEmptyTable(tbodyTests);
    else {
        $.each(zoznamTestov, function () {
            let tr = createTr(tbodyTests);
            let kluc = this.kluc
            let td = createTd(this.nazov);
            $(td).addClass("test-th");
            $(td).on("click",function (){
                showTest(kluc);
            });
            let pocetPisucichStudentov = this.pocet_pisucich_studentov;
            if (pocetPisucichStudentov === undefined)
                pocetPisucichStudentov = "Aktivujte test";
            tr.append(createTh(kluc), td, createTd(this.casovy_limit + " min"), createTd(this.pocet_otazok), createTd(pocetPisucichStudentov), createToggle(this.kluc,this.aktivny))
        })
    }
}

function showTest(kluc){
    $.getJSON("api/uzivatelia/set-test-session.php?akcia=nastav&klucTestu="+kluc, function (data){
        if (!data.error) {
            window.location.href = 'test-info.html';
        }
    });

}

function createToggle(kodTestu,aktivny){
    let td = document.createElement("td");
    let label = document.createElement("label");
    $(label).addClass("switch");
    let input = document.createElement("input");
    $(input).attr("type","checkbox");
    if (aktivny)
        $(input).prop("checked",true);
    changeState(kodTestu, $(input));
    let span = document.createElement("span");
    $(span).addClass("slider round");
    label.append(input,span);
    td.append(label);
    $(td).css("text-align","center");
    return td;
}

function changeState(kodTest, input){
    input.on("change", function (){
        if (this.checked)
            $.getJSON("api/testy/praca-s-testami.php?akcia=aktivuj-test&kluc="+kodTest);
        else
            $.getJSON("api/testy/praca-s-testami.php?akcia=deaktivuj-test&kluc="+kodTest);
    })

}

function setLottieHover() {
    const player = $("#add-lottie").get(0);
    $("#lottie-hover").on({
        mouseenter: function () {
            player.play();
        },
        mouseleave: function () {
            player.stop();
        }
    })
}

function createTh(text){
    let th = document.createElement("th");
    $(th).text(text);
    return th;
}
function createTd(text){
    let td = document.createElement("td");
    $(td).text(text);
    return td;
}
function createTr(tbody){
    let tr = document.createElement("tr");
    tbody.append(tr);
    return tr;
}

function createEmptyTable(tbody){
    let emptyTr = createTr(tbody);
    let emptyTd = createTd("Nemáte zatiaľ vytvorené žiadne testy.");
    $(emptyTd).attr("colspan","6");
    $(emptyTd).attr("id","empty-tests");
    emptyTr.append(emptyTd);
}

function showLogInfo(regInfo) {
    let logDiv = $("#log-" + regInfo);
    logDiv.css("top", 0);
    setTimeout(function () {
        logDiv.css("top", "-100px");
    }, 3000)
}

function showUserName(userName) {
    $("#login-name").text(userName);
}

function logout() {
    $.getJSON("api/uzivatelia/odhlasenie/", function (data) {
        if (!data.error) {
            sessionStorage.setItem("logoutStatus", "success");
            window.location.replace('index.html');
        }
    })
}
