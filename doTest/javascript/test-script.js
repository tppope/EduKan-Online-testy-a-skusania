$(window).on("load", function () {
    createMathQuestion(1, "napiste vzorec na koleso");
    createMathQuestion(2, "napiste vzorec na hovno");

    $('[data-toggle="tooltip"]').tooltip();
});

function createMathQuestion(order,name){
    let mathField = createMathField(order);
    let formPictureInput = createFormPictureInput(order);

    let questionDiv = createQuestionDiv(order,name);

    questionDiv.append(mathField,formPictureInput);
    $("#test-questions").append(questionDiv);
}

function createMathField(order){
    let mfe = new MathfieldElement();
    mfe.value = '\\frac{\\pi}{2}';
    $(mfe).addClass("math-input-content-"+order);
    $(mfe).attr("virtual-keyboard-mode", "onfocus");
    return mfe;
}

function createFormPictureInput(order){
    let formDiv = document.createElement("div");
    $(formDiv).addClass("picture-input-content-"+order+" picture-form")
    let form = document.createElement("form");
    $(form).attr({
        "onsubmit": "return false;",
        "id": "picture-form-"+order,
    })
    let divFormGroup1 = createDivFormGroup(form);
    divFormGroup1.append(createFileInput(order));
    let divFormGroup2 = createDivFormGroup(form);
    divFormGroup2.append(createFileSubmit(order));
    formDiv.append(form);
    $(formDiv).hide();
    return formDiv;
}

function createFileSubmit(order){
    let button = document.createElement("input");
    $(button).attr({
        "type":"submit",
        "value":"Odoslať",
        "name": "submit",
        "class": "btn btn-outline-dark btn-block"
    });
    $(button).on("click", function (){
        submitForm(order);
    });
    return button;
}

function submitForm(order){
    let pictureForm = $("#picture-form-"+order).get(0);
    console.log($("#fileUpload-"+order).get(0));
}

function createFileInput(order){
    let div = document.createElement("div");
    $(div).addClass("custom-file");
    let fileInput = document.createElement("input");
    $(fileInput).attr({
        "type":"file",
        "id": "fileUpload-"+order,
        "name":"fileUpload-"+order,
        "class": "custom-file-input",
    })
    $(fileInput).on("change",function (){
        showName(this);
    });
    let label = document.createElement("label");
    $(label).attr({
        "id": "fileUpload-"+order+"-label",
        "for":"fileUpload-"+order,
        "class": "custom-file-label",
    });
    $(label).text("Vyberte súbor");
    div.append(fileInput,label);
    return div;
}
function createDivFormGroup(form){
    let row = document.createElement("div");
    $(row).addClass("form-row");
    form.append(row)
    let div = document.createElement("div");
    $(div).addClass("form-group col");
    row.append(div);
    return div;
}

function createQuestionDiv(order,name){
    let questionDiv = document.createElement("div");
    $(questionDiv).addClass("question-style");
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
    let pictureButton = $("<div class=\"change-input-button math-input-content-"+order+"\" data-toggle=\"tooltip\" title=\"Vložiť obrázok riešenia\" onclick=\"changeToPictureInput("+order+")\">\n" +
        "                <img class=\"change-input-image\" src=\"resources/pictures/add.svg\" width=\"25\" height=\"25\" alt=\"picture input\">\n" +
        "            </div>").get(0);
    let mathButton = $("<div class=\"change-input-button picture-input-content-"+order+"\" data-toggle=\"tooltip\" title=\"Vložiť matematický vzorec\" onclick=\"changeToMathInput("+order+")\" style='display: none'>\n" +
        "                <img class=\"change-input-image\" src=\"resources/pictures/mathematics.svg\" width=\"32\" height=\"32\" alt=\"math input\">\n" +
        "            </div>").get(0);
    questionHeader.append(pictureButton);
    questionHeader.append(mathButton);

    return questionHeader;
}

function changeToPictureInput(order){
    $(".math-input-content-"+order).hide();
    $(".picture-input-content-"+order).show();
}

function changeToMathInput(order){
    $(".picture-input-content-"+order).hide();
    $(".math-input-content-"+order).show();
}
function showName(input){
    document.getElementById(input.id+"-label").textContent = input.files[0].name;
}





