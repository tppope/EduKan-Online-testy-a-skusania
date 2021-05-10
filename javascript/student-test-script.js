$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
    getLoggedInUser();
    loadTest();
});



function teacherTest(){
    $.getJSON("api/uzivatelia/set-data-for-answers.php?akcia=vymaz", function (data){
        if (!data.error) {
            window.location = 'test-info.html'
        }
    });
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

function showUserName(userName) {
    $("#login-name").text(userName);
}

function loadTest(){
    $.getJSON("api/testy/nacitaj-test.php", function (test) {
        if (test.kod === "API_T__LT_U_1") {
            console.log(test);
            $("#test-name").text(test.data_testu.nazov);
            $.getJSON("api/uzivatelia/studenti/?akcia=masSession", function (findStudent) {
                $("#student-name").text(findStudent.id+"-"+findStudent.name+" "+findStudent.surname);
            });
            $.getJSON("api/testy/praca-s-testami.php?akcia=nacitaj-vysledky", function (testOdpovede) {
                if (testOdpovede.kod === "API_T__PT_U_4"){
                    printTest(test.data_testu.otazky,testOdpovede.odpovede);
                }
                else
                    console.log(testOdpovede);
            })
        }
        else
            console.log(data);
    })
}

function printTest(otazky, odpovede){
    console.log(otazky);
    console.log(odpovede);
    // $.each(otazky,function (index){
    //     let otazka = this;
    //     switch (otazka.typ){
    //         case 1:true;break;
    //         case 2:true;break;
    //         case 3:createConnectQuestion(index,otazka);break;
    //         case 4:createCanvasQuestion(index,otazka.nazov);break;
    //         case 5:createMathQuestion(index,otazka.nazov);break;
    //     }
    // })
}
