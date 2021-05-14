$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
    getLoggedInUser();
    makeConnect();
    loadTest();
});

let connectIt = false
let makeConnection = [];

function makeConnect() {
    let i = setInterval(function () {
        if (connectIt) {
            clearInterval(i);
            for (let connection of makeConnection) {
                if (connection.odpovede) {
                    for (let i = 0; i < connection.odpovede.length; i++) {
                        if (connection.odpovede[i]) {
                            let newJsPlumbInstance = jsPlumb.getInstance();
                            let sourceId = `question-${connection.index}-left-${connection.odpovede[i].par_lava_strana}`;
                            let targetId = `question-${connection.index}-right-${connection.odpovede[i].par_prava_strana}`;
                            newJsPlumbInstance.connect({
                                source: sourceId,
                                target: targetId,
                                detachable: false,
                                anchor: "Continuous",
                                endpoint: ["Dot", {width: 5, height: 5}]
                            });
                        }
                    }
                }
            }
        }
    }, 300)

}

function logout() {
    $.getJSON("api/uzivatelia/odhlasenie/", function (data) {
        if (!data.error) {
            sessionStorage.setItem("logoutStatus", "success");
            window.location.replace('index.html');
        }
    })
}


function teacherTest() {
    $.getJSON("api/uzivatelia/set-data-for-answers.php?akcia=vymaz", function (data) {
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
        } else {
            console.log(data);
        }
    })
}

function showUserName(userName) {
    $("#login-name").text(userName);
}

function loadTest() {
    $.getJSON("api/testy/nacitaj-test.php", function (test) {
        if (test.kod === "API_T__LT_U_1") {
            console.log(test);
            $("#test-name").text(test.data_testu.nazov);
            $.getJSON("api/uzivatelia/studenti/?akcia=masSession", function (findStudent) {
                $("#student-name").text(findStudent.id + "-" + findStudent.name + " " + findStudent.surname);
            });
            $.getJSON("api/testy/praca-s-testami.php?akcia=nacitaj-vysledky", function (testOdpovede) {
                if (testOdpovede.kod === "API_T__PT_U_4") {
                    printTest(test.data_testu.otazky, testOdpovede);
                } else if (testOdpovede.kod === "API_T__PT_GC") {

                    console.log(testOdpovede)
                    printTest(test.data_testu.otazky, false);
                }
                else
                    console.log(testOdpovede);
            })
        } else
            console.log(test);
    })
}

function printTest(otazky, odpovede) {
    console.log(otazky);
    console.log(odpovede);
    let counter = 0;

    if (odpovede)
        $("#points").text(" " + odpovede.suhrnnyPocetBodov.ziskaneBody);
    else
        $("#points").text(" " + 0);

    $.each(otazky, function (index) {
        let otazka = this;
        if (!odpovede)
            odpovede = {
                vyhodnotenieCeleho: [],
                odpovede: [],
            }
        switch (otazka.typ) {
            case 1:
                createShortQuestion(index, otazka.nazov, odpovede.vyhodnotenieCeleho[index], odpovede.odpovede[index]);
                break;
            case 2:
                createLongQuestion(index, otazka, odpovede.vyhodnotenieCeleho[index], odpovede.odpovede[index]);
                break;
            case 3:
                createConnectQuestion(index, otazka, odpovede.vyhodnotenieCeleho[index], odpovede.odpovede[index]);
                break;
            case 4:
                createCanvasQuestion(index, otazka.nazov, odpovede.vyhodnotenieCeleho[index], odpovede.odpovede[index]);
                break;
            case 5:
                createMathQuestion(index, otazka.nazov, odpovede.vyhodnotenieCeleho[index], odpovede.odpovede[index]);
                break;
        }

        counter++;
    })
    connectIt = true;
}


function createCheckAnswer(order) {

    let questionDiv = $("#question-" + order);
    let checkButtons = document.createElement("div");
    $(checkButtons).addClass("check-answer-buttons");
    let wrongButton = document.createElement("button");
    wrongButton.innerText = "Nesprávne";
    $(wrongButton).addClass("btn btn-outline-danger check-button");
    $(wrongButton).on("click", function () {
        $.getJSON("api/uzivatelia/testy/evaluate-question.php?vyhodnotenie=nespravne&otazkaId=" + order, function (data) {
            if (!data.error) {
                let points = $("#points");
                $(wrongButton).attr("disabled", true);
                $(wrongButton).removeClass("btn-outline-danger");
                $(wrongButton).addClass("btn-danger");
                $(successButton).attr("disabled", false);
                $(successButton).removeClass("btn-success");
                $(successButton).addClass("btn-outline-success");
                if (questionDiv.hasClass("correctQuestionBorder"))
                    points.text(" " + (Number(points.text()) - 1));
                questionDiv.removeClass("notCheckQuestionBorder");
                questionDiv.removeClass("correctQuestionBorder");
                questionDiv.addClass("inCorrectQuestionBorder");
            }
        })
    })

    let successButton = document.createElement("button");
    successButton.innerText = "Správne";
    $(successButton).addClass("btn btn-outline-success check-button");
    $(successButton).on("click", function () {
        $.getJSON("api/uzivatelia/testy/evaluate-question.php?vyhodnotenie=spravne&otazkaId=" + order, function (data) {
            if (!data.error) {
                let points = $("#points");
                $(wrongButton).attr("disabled", false);
                $(wrongButton).removeClass("btn-danger");
                $(wrongButton).addClass("btn-outline-danger");
                $(successButton).attr("disabled", true);
                $(successButton).removeClass("btn-outline-success");
                $(successButton).addClass("btn-success");
                points.text(" " + (Number(points.text()) + 1));
                questionDiv.removeClass("notCheckQuestionBorder");
                questionDiv.removeClass("inCorrectQuestionBorder");
                questionDiv.addClass("correctQuestionBorder");
            }
        })
    })

    if (questionDiv.hasClass("inCorrectQuestionBorder")) {
        $(wrongButton).attr("disabled", true);
        $(wrongButton).removeClass("btn-outline-danger");
        $(wrongButton).addClass("btn-danger");
    } else if ($("#question-" + order).hasClass("correctQuestionBorder")) {
        $(successButton).attr("disabled", true);
        $(successButton).removeClass("btn-outline-success");
        $(successButton).addClass("btn-success");
    }

    checkButtons.append(wrongButton, successButton)

    return checkButtons;

}


function createCanvasQuestion(order, name, answerCheck, odpovede) {
    let img = createImgForCanvasQuestion(odpovede, order)
    let questionDiv = createQuestionDiv(order, name, answerCheck);
    questionDiv.append(img);

    if (odpovede)
        questionDiv.append(createCheckAnswer(order));

    $("#test-questions").append(questionDiv);
}

function createImgForCanvasQuestion(odpovede, order) {
    let img = document.createElement("img");
    if (odpovede) {
        let src = odpovede.zadana_odpoved;

        if (~src.indexOf("inFiles-"))
            return showImageFile(order, src)
        else {
            $(img).attr({
                "src": src,
                "draggable": false
            });
            $(img).addClass("img-answer");
            return img;
        }
    }

    return $("<div class='not-answer-div'>Nebola zodpovedaná</div>").get(0);

}

function createShortQuestion(order, name, answerCheck, odpovede) {
    questionDiv = createQuestionDiv(order, name, answerCheck);
    $(questionDiv).append(createShortInput(order, odpovede));

}

function createMathQuestion(order, name, answerCheck, odpovede) {

    let mathField = createMathField(odpovede, order);

    let questionDiv = createQuestionDiv(order, name, answerCheck);
    questionDiv.append(mathField);
    if (odpovede)
        questionDiv.append(createCheckAnswer(order));

    $("#test-questions").append(questionDiv);
}

let promise = Promise.resolve();  // Used to hold chain of typesetting calls

function typeset(code) {
    promise = promise.then(() => MathJax.typesetPromise(code()))
        .catch((err) => console.log('Typeset failed: ' + err.message));
    return promise;
}

function createMathField(odpovede, order) {
    let div = document.createElement("div");
    if (odpovede) {
        let mathValue = odpovede.zadana_odpoved;

        if (~mathValue.indexOf("inFiles-")) {
            return showImageFile(order, mathValue);
        } else {
            typeset(() => {
                div.innerHTML = "$$" + mathValue + "$$";
            });
            return div;
        }
    }
    return $("<div class='not-answer-div'>Nebola zodpovedaná</div>").get(0);


}


function showImageFile(order, type) {
    let path = "api/uzivatelia/testy/uploadedImages/" + order + "_";
    $.ajax({
        url: "api/uzivatelia/set-data-for-answers.php?akcia=dostan",
        dataType: 'json',
        async: false,
        success: function (data) {
            path = path + data.fileName
        }
    });
    path = path + "." + (type.split("-"))[1];
    let img = document.createElement("img");
    $(img).attr({
        "src": path,
        "draggable": false
    });
    $(img).addClass("img-answer");
    return img;
}


function createQuestionDiv(order, name, correct) {
    let questionDiv = document.createElement("div");
    $(questionDiv).addClass("question-style");
    if (correct === 0)
        $(questionDiv).addClass("inCorrectQuestionBorder")
    else if (correct === 1)
        $(questionDiv).addClass("correctQuestionBorder")
    else
        $(questionDiv).addClass("notCheckQuestionBorder")
    $(questionDiv).attr("id", "question-" + order)
    questionDiv.append(createQuestionName(order, name))
    $("#test-questions").append(questionDiv);
    return questionDiv;
}

function createQuestionName(order, name) {
    let questionHeader = document.createElement("header");
    $(questionHeader).addClass("question-header");
    let questionH3 = document.createElement("h3");
    $(questionH3).text(order + ". " + name);
    questionHeader.append(questionH3);
    return questionHeader;
}

function createShortInput(order, odpoved) {
    let inputAreaDiv = document.createElement("div");
    let inputArea = document.createElement("input");

    let value = "Nebola zodpovedaná";
    if (odpoved)
        value = odpoved.zadana_odpoved;


    $(inputAreaDiv).addClass("input-area-short");
    $(inputAreaDiv).append(inputArea);
    $(inputArea).attr({
        "type": "text",
        "class": "form-control",
        "disabled": "disabled",
        "value": value

    });
    return inputAreaDiv;

}

function createLongQuestion(order, otazka, answerCheck, odpovede) {
    let questionDiv = createQuestionDiv(order, otazka.nazov, answerCheck);
    $(questionDiv).append(createLongInput(order, otazka.odpovede, odpovede));

    if (odpovede)
        studentChecked(order, odpovede);

}

function createLongInput(order, answers, studentAnswers) {
    let allCheckboxDiv = document.createElement("div");
    $(allCheckboxDiv).addClass("checkbox-array-div");


    for (let answer of answers) {
        let checkboxDiv = document.createElement("div");
        $(checkboxDiv).addClass("checkbox-div");
        let inputCheckbox = document.createElement("input");
        $(inputCheckbox).attr({
            "value": answer.text,
            "name": "checkboxName-" + order,
            "type": "checkbox",
            "class": "form-check-input checkbox-input",
            "id": "check-" + order + "-" + answer.text,
            "disabled": "disabled"
        });

        let labelCheckbox = document.createElement("label");
        $(labelCheckbox).attr({
            "for": "check-" + order + "-" + answer.text,
            "class": "form-check-label checkbox-label",

        });
        $(labelCheckbox).text(answer.text);
        $(allCheckboxDiv).append($(checkboxDiv).append(inputCheckbox, labelCheckbox));

        if (studentAnswers)
            makeColorLabel(answer, studentAnswers, labelCheckbox);

    }


    return allCheckboxDiv;

}

function studentChecked(order, studentAnswers) {
    for (let answer of studentAnswers) {
        $("#check-" + order + "-" + answer.zadana_odpoved).attr("checked", "true");
    }
}

function makeColorLabel(answer, studentAnswers, labelCheckbox) {
    if (answer.je_spravna) {
        if (isContains(answer.text, studentAnswers)) {
            $(labelCheckbox).css("color", "green");
        } else
            $(labelCheckbox).css("color", "red");
    } else {
        if (!isContains(answer.text, studentAnswers)) {
            $(labelCheckbox).css("color", "green");
        } else
            $(labelCheckbox).css("color", "red");
    }
}

function isContains(rightAnswer, studentAnswers) {
    for (let answer of studentAnswers) {
        if (answer.zadana_odpoved === rightAnswer)
            return true;
    }
    return false;

}

function createCard(id, card_phrase) {
    let card = document.createElement("div");
    card.setAttribute('class', 'connect-card');
    let phrase = document.createElement("h4");

    card.setAttribute('id', id);
    phrase.innerText = card_phrase;

    card.appendChild(phrase);


    return card
}

function createConnectDiv(index, question) {
    let connectorDiv = document.createElement("div");
    let leftDiv = document.createElement("div");
    let rightDiv = document.createElement("div");


    leftDiv.setAttribute('class', 'connect-card-wrapper-left');
    rightDiv.setAttribute('class', 'connect-card-wrapper-right');

    connectorDiv.append(leftDiv, rightDiv);
    connectorDiv.setAttribute('class', 'connector-wrapper');

    for (const odpoved in question.odpovede_lave) {
        let id = `question-${index}-left-${odpoved}`;
        let card = createCard(id, question.odpovede_lave[odpoved]);

        card.classList.add(`connect-left-${index}`);
        leftDiv.appendChild(card);

    }
    for (const odpoved in question.odpovede_prave) {
        let id = `question-${index}-right-${odpoved}`;
        let card = createCard(id, question.odpovede_prave[odpoved]);
        card.classList.add(`connect-right-${index}`);
        rightDiv.appendChild(card);

    }
    return connectorDiv;
}

function createConnectQuestion(index, question, answer, odpovede) {
    let questionDiv = createQuestionDiv(index, question.nazov, answer);
    questionDiv.appendChild(createConnectDiv(index, question));

    let connection = {
        "index": index,
        "odpovede": odpovede
    }
    makeConnection.push(connection);
}
