$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
    getLoggedInUser()
    loadStudents();

    loadSSE();

});

let source = null;
function loadSSE(){
    if(typeof(EventSource) !== "undefined") {

        source = new EventSource("api/uzivatelia/check-test-leaving-sse.php");

        source.addEventListener("message", function(e) {
            notifyLeftTest(JSON.parse(e.data));
        },false);

    } else {
        $("#x").text("Sorry, your browser does not support server-sent events...");
    }
}


function notifyLeftTest(students){
    let notificationTab = $("#notifications");
    if (notificationTab.css("right") !== '0px')
        $("#notification-button").addClass("blink");

    $("#notifications-text").append(leftInfo(students));

}

function leftInfo(students){
    let div = document.createElement("div");
    let span = document.createElement("span");
    $(span).text(new Date().format('H:i:s d.m.Y') + " opustil test:");
    let ul = document.createElement("ul");
    div.append(span,ul);
    $.each(students,function (){
        let li = document.createElement("li");
        $(li).text(this.id+" - "+this.name+" "+this.surname);
        ul.append(li);
    });
    return div;
}

function showNotifications(){
    let notificationTab = $("#notifications");
    if (notificationTab.css("right") === '0px') {
        notificationTab.css("right", (window.innerWidth <= 768 )? "-270px":"-360px");

    }else {
        notificationTab.css("right", "0px");
        $("#notification-button").removeClass("blink");
    }
}

function getLoggedInUser() {
    $.getJSON("api/uzivatelia/prihlasenie/", function (data) {
        if (!data.error) {
            if (data.alreadyLogin) {
                let userName = data.user.meno + " " + data.user.priezvisko;
                showUserName(userName);
            } else {
                sessionStorage.setItem("logoutStatus", "failed");
                window.location.href = 'index.html';
            }
        }
        else {
            console.log(data);
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

function showUserName(userName) {
    $("#login-name").text(userName);
}

function teacherHomescreen(){
    $.getJSON("api/uzivatelia/set-test-session.php?akcia=vymaz", function (data){
        if (!data.error) {
            window.location = 'teacher-homescreen.html'
        }
    });
}

function printStudents(zoznamStudentov){
    let tbodyStudents = $("#students-tbody");
    $.each(zoznamStudentov, function () {
        let student = this;
        let tr = createTr(tbodyStudents);
        $(tr).addClass("student-tr");
        $.getJSON("api/uzivatelia/studenti/?studentId="+student.student_id, function (findStudent) {
            $(tr).on("click",function (){
                showStudentTest(student.student_id, student.datum_zaciatku_pisania, student.cas_zaciatku_pisania);
            });
            let tdTime = createTd(Math.ceil(student.zostavajuci_cas/60) + " min");
            $(tdTime).attr("id","student-time-"+findStudent.dbID)
            tr.append(createTh(findStudent.id), createTd(findStudent.name), createTd(findStudent.surname), tdTime);
        })

    })
}

function createEmptyTable(tbody){
    let emptyTr = createTr(tbody);
    let emptyTd = createTd("Zatiaľ sa nezúčastnil žiaden študent.");
    $(emptyTd).attr("colspan","4");
    $(emptyTd).attr("id","empty-students");
    emptyTr.append(emptyTd);
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

function loadStudents(){
    $.getJSON("api/testy/nacitaj-test.php", function (data) {
        if (data.kod === "API_T__LT_U_1") {
            $("#test-name").text(data.data_testu.nazov);
            printStudents(data.data_testu.zoznam_pisucich_studentov);
        }
        else if (data.kod === "API_T__GSC_1") {
            createEmptyTable($("#students-tbody"));
        }
        else
            console.log(data);
    })
}

function showStudentTest(student_id, datum_zaciatku_pisania, cas_zaciatku_pisania){
    $.getJSON("api/uzivatelia/set-data-for-answers.php?akcia=nastav&studentId="+student_id+"&datumZaciatkuPisania="+datum_zaciatku_pisania+"&casZaciatkuPisania="+cas_zaciatku_pisania, function (data){
        if (!data.error) {
            window.location.href = 'student-test.html';
        }
    });
}

