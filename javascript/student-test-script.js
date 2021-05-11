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
                    printTest(test.data_testu.otazky,testOdpovede);
                    console.log(testOdpovede);
                }
                else if ( testOdpovede.kod ==="API_T__PT_GC")
                    printTest(test.data_testu.otazky,false);
                else
                    console.log(testOdpovede);
            })
        }
        else
            console.log(test);
    })
}

function printTest(otazky, odpovede){
    console.log(otazky);
    console.log(odpovede);
    $.each(otazky,function (index){
        let otazka = this;
        if (!odpovede)
            odpovede = {
            vyhodnotenieCeleho:[],
            odpovede:[],
        }
        switch (otazka.typ){
            case 1:createShortQuestion(index, otazka.nazov,odpovede.vyhodnotenieCeleho[index],odpovede.odpovede[index]);break;
            //case 2:createLongQuestion(index,otazka, odpoved);break;
            case 3:;break;
            case 4:createCanvasQuestion(index,otazka.nazov, odpovede.vyhodnotenieCeleho[index],odpovede.odpovede[index]);break;
            case 5:createMathQuestion(index,otazka.nazov, odpovede.vyhodnotenieCeleho[index],odpovede.odpovede[index]);break;
        }
    })
}



function createCheckAnswer(order){
    let checkButtons = document.createElement("div");
    $(checkButtons).addClass("check-answer-buttons");
    let wrongButton = document.createElement("button");
    wrongButton.innerText = "Nesprávne";
    $(wrongButton).addClass("btn btn-outline-danger check-button");
    $(wrongButton).on("click",function (){

    })

    let successButton = document.createElement("button");
    successButton.innerText = "Správne";
    $(successButton).addClass("btn btn-outline-success check-button");
    $(successButton).on("click",function (){

    })

    checkButtons.append(wrongButton,successButton)

    return checkButtons;

}


function createCanvasQuestion(order,name,answerCheck,odpovede){
    let img = createImgForCanvasQuestion(odpovede)
    let questionDiv = createQuestionDiv(order,name,answerCheck);
    questionDiv.append(img,createCheckAnswer(order));

    $("#test-questions").append(questionDiv);
}

function createImgForCanvasQuestion(odpovede){
    let img  = document.createElement("img");
    let src = "";
    if (odpovede)
        src = odpovede.zadana_odpoved;
    $(img).attr({
        "src":src,
        "draggable": false
    });
    $(img).addClass("img-answer");
    return img;
}

function createShortQuestion(order,name, answerCheck, odpovede){
    questionDiv = createQuestionDiv(order,name,answerCheck);
    $(questionDiv).append(createShortInput(order, odpovede));

}

function createMathQuestion(order,name,answerCheck,odpovede){
    let mathField = createMathField(odpovede);

    let questionDiv = createQuestionDiv(order,name,answerCheck);
    questionDiv.append(mathField, createCheckAnswer(order));

    $("#test-questions").append(questionDiv);
}

let promise = Promise.resolve();  // Used to hold chain of typesetting calls

function typeset(code) {
    promise = promise.then(() => MathJax.typesetPromise(code()))
        .catch((err) => console.log('Typeset failed: ' + err.message));
    return promise;
}

function createMathField(odpovede){
    let div = document.createElement("div");
    let mathValue = "";
    if (odpovede)
        mathValue = odpovede.zadana_odpoved;
    typeset(() => {
        div.innerHTML = "$$"+mathValue+"$$";
    });
    return div;
}


function createQuestionDiv(order,name, correct){
    let questionDiv = document.createElement("div");
    $(questionDiv).addClass("question-style");
    if (correct === 0)
        $(questionDiv).addClass("inCorrectQuestionBorder")
    else if (correct === 1)
        $(questionDiv).addClass("correctQuestionBorder")
    else(correct === -1)
        $(questionDiv).addClass("notCheckQuestionBorder")
    $(questionDiv).attr("id","question-"+order)
    questionDiv.append(createQuestionName(order,name))
    $("#test-questions").append(questionDiv);
    return questionDiv;
}
function createQuestionName(order,name){
    let questionHeader = document.createElement("header");
    $(questionHeader).addClass("question-header");
    let questionH3 = document.createElement("h3");
    $(questionH3).text(order+". "+name);
    questionHeader.append(questionH3);
    return questionHeader;
}
function createShortInput(order, odpoved){
    let inputAreaDiv = document.createElement("div");
    let inputArea = document.createElement("input");

    let value = "";
    if (odpoved)
        value = odpoved.zadana_odpoved;


    $(inputAreaDiv).addClass("input-area-short");
    $(inputAreaDiv).append(inputArea);
    $(inputArea).attr({
        "type":"text",
        "class": "form-control",
        "disabled":"disabled",
        "value":value

    });
    return inputAreaDiv;

}

function createLongQuestion(order,otazka, odpovede){
    let name = otazka.nazov ;
    if(otazka.vie_student_pocet_spravnych)
        name += " (počet správnych odpovedí: " + otazka.pocet_spravnych+")";

    let questionDiv = createQuestionDiv(order,name,odpovede.vyhodnotenie);
    $(questionDiv).append(createLongInput(order,otazka.odpovede));

}

function createLongInput(order, answers){
    let allCheckboxDiv = document.createElement("div");
    $(allCheckboxDiv).addClass("checkbox-array-div");
    for (let answer of answers) {
        let checkboxDiv = document.createElement("div");
        $(checkboxDiv).addClass("checkbox-div");
        let inputCheckbox = document.createElement("input");
        $(inputCheckbox).attr({
            "value":answer.text,
            "name":"checkboxName-" + order,
            "type":"checkbox",
            "class": "form-check-input checkbox-input",
            "id":"check-"+order+"-"+answer.text,
            "disabled":"disabled"

        });
        let labelCheckbox = document.createElement("label");
        $(labelCheckbox).attr({
            "for":"check-"+order+"-"+answer.text,
            "class": "form-check-label checkbox-label",

        });
        $(labelCheckbox).text(answer.text);
        $(allCheckboxDiv).append($(checkboxDiv).append(inputCheckbox,labelCheckbox));
    }


    return allCheckboxDiv;

}
