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
            let th = createTh("tu bude kod test");
            $(th).addClass("test-th");
            $(th).on("click",function (){
                console.log("aaa")
            })
            tr.append(th, createTd(this.nazov), createTd(this.casovy_limit), createTd("tu bude pocet otazok"), createTd("tu bude pocet studentov"), createToggle("tu kod testu",this.aktivny))
        })
    }
}

function createToggle(kodTestu,aktivny){
    let td = document.createElement("td");
    let label = document.createElement("label");
    $(label).addClass("switch");
    let input = document.createElement("input");
    $(input).attr("type","checkbox");
    if (aktivny)
        $(input).prop("checked",true);
    let span = document.createElement("span");
    $(span).addClass("slider round");
    $(span).on("click",()=>{
        changeState(kodTestu);
    })
    label.append(input,span);
    td.append(label);
    $(td).css("text-align","center");
    return td;
}

function changeState(kodTest){
    console.log(kodTest);
    // let radioSwitch = $("#radio-a");
    // if (radioSwitch.is(":checked")) {
    //     $("#equation-system-interface").show();
    //     $("#matrix-interface").hide();
    //     $("#math-type").text("Zadaj sústavu");
    //     $("#row-equ-span").text("Rovnice");
    //     $("#col-var-span").text("Premenné");
    // } else {
    //     $("#equation-system-interface").hide();
    //     $("#matrix-interface").show();
    //     $("#math-type").text("Zadaj maticu");
    //     $("#row-equ-span").text("Riadky");
    //     $("#col-var-span").text("Stĺpce");
    // }
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
