$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
    getLoggedInUser()
    //loadStudents();

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
    if (zoznamStudentov.length === 0)
        createEmptyTable(tbodyStudents);
    else {
        $.each(zoznamStudentov, function () {
            let tr = createTr(tbodyStudents);
            $(tr).addClass("student-tr");
            let student = getStudent(this.student_id);
            $(tr).on("click",function (){
                showStudentTest();
            });
            tr.append(student.id, student.name, student.surname, this.zostavajuci_cas);
        })
    }
}

function createEmptyTable(tbody){
    let emptyTr = createTr(tbody);
    let emptyTd = createTd("Zatiaľ sa nezúčastnil žiaden študent.");
    $(emptyTd).attr("colspan","4");
    $(emptyTd).attr("id","empty-tests");
    emptyTr.append(emptyTd);
}

function loadStudents(){
    $.getJSON("api/testy/nacitaj-test.php", function (data) {
        if (data.kod === "API_T__LT_U_1") {
            $("#test-name").text(data.nazov);
            printStudents(data.zoznam_pisucich_studentov);
        }
    })
}

function showStudentTest(){

}
function getStudent(studentId){
    let student = null;
    $.getJSON("api/uzivatelia/studenti/?studentId="+studentId, function (data) {
        student = data;
    })
    return student;
}
