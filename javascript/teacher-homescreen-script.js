$(window).on("load", function () {
    getLoggedInUser();
    setLottieHover();
    getTeacherTests();
    checkEditTest();

    setTimeout(function () {
        $('[data-toggle="tooltip"]').tooltip()
    }, 500);

})

function checkEditTest(){
    let editTestInfo = sessionStorage.getItem("editTest");
    if (editTestInfo != null) {
        showEditTestInfo(editTestInfo);
        sessionStorage.removeItem('editTest');
    }

}

function showEditTestInfo(editTestInfo){
    let editTestDiv = $("#editTest-" + editTestInfo);
    editTestDiv.css("top", 0);
    setTimeout(function () {
        editTestDiv.css("top", "-100px");
    }, 3000)
}

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

function getTeacherTests() {
    $.getJSON("api/testy/praca-s-testami.php?akcia=zoznam-testov", function (data) {
        printTests(data.zoznam_testov)
    })
}

function printTests(zoznamTestov) {
    let tbodyTests = $("#tests-tbody");
    if (zoznamTestov.length === 0)
        createEmptyTable(tbodyTests);
    else {
        $.each(zoznamTestov, function () {
            let tr = createTr(tbodyTests);
            let kluc = this.kluc
            let pocetPisucichStudentov = this.pocet_pisucich_studentov;
            if (pocetPisucichStudentov === undefined)
                pocetPisucichStudentov = "Aktivujte test";

            let th = createTh(kluc);
            th.prepend(createEntryImg(tr, kluc))
            $(th).addClass("keyTh");

            tr.append(th, createTd(this.nazov), createTd(this.casovy_limit + " min"), createTd(this.pocet_otazok), createTd(pocetPisucichStudentov), createToggle(this.kluc, this.aktivny))
        })
    }
}

function createEntryImg(tr, kluc) {
    let div = document.createElement("div");
    $(div).addClass("entryImgDiv");
    let entryImg = document.createElement("img");
    $(entryImg).attr({
        "src": "resources/pictures/ucitel/enter.svg",
        "data-toggle": "tooltip",
        "title": "Vstúpiť",
        "class": "entryImgButton",
        "width": "25px",
        "height": "25px"
    });

    $(entryImg).on("mouseenter", function () {
        $(tr).css("background-color", "rgba(23, 162, 184, 0.1)")
    });

    $(entryImg).on("mouseleave", function () {
        $(tr).css("background-color", "white")
    })

    $(entryImg).on("click", function () {
        showTest(kluc);
    });

    div.append(entryImg);

    return div;
}

function showTest(kluc) {
    $.getJSON("api/uzivatelia/set-test-session.php?akcia=nastav&klucTestu=" + kluc, function (data) {
        if (!data.error) {
            window.location.href = 'test-info.html';
        }
    });

}

function createToggle(kodTestu, aktivny) {
    let td = document.createElement("td");
    let label = document.createElement("label");
    $(label).addClass("switch");
    let input = document.createElement("input");
    $(input).attr("type", "checkbox");
    if (aktivny)
        $(input).prop("checked", true);
    changeState(kodTestu, $(input));
    let span = document.createElement("span");
    $(span).addClass("slider round");
    label.append(input, span);
    td.append(label);
    $(td).css("text-align", "center");
    return td;
}

function changeState(kodTest, input) {
    input.on("change", function () {
        if (this.checked)
            $.getJSON("api/testy/praca-s-testami.php?akcia=aktivuj-test&kluc=" + kodTest);
        else
            $.getJSON("api/testy/praca-s-testami.php?akcia=deaktivuj-test&kluc=" + kodTest);
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

function createTh(text) {
    let th = document.createElement("th");
    $(th).text(text);
    return th;
}

function createTd(text) {
    let td = document.createElement("td");
    $(td).text(text);
    return td;
}

function createTr(tbody) {
    let tr = document.createElement("tr");
    tbody.append(tr);
    return tr;
}

function createEmptyTable(tbody) {
    let emptyTr = createTr(tbody);
    let emptyTd = createTd("Nemáte zatiaľ vytvorené žiadne testy.");
    $(emptyTd).attr("colspan", "6");
    $(emptyTd).attr("id", "empty-tests");
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
