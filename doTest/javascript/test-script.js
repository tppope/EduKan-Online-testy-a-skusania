let instances=[];

$(window).on("load", function () {
    createMathQuestion(1, "napiste vzorec na koleso");
    createMathQuestion(2, "napiste vzorec na hovno");
    createCanvasQuestion(3,"Nakreslite leva");
    createCanvasQuestion(4,"Nakreslite sliepku");
    let array = {
        "nazov": "Spojte spravne otazky",
        "odpovede_lave":{
            1: "červený",
            2: "ostrý",
            3: "zelená",
            4: "šľachetné"
        },
        "odpovede_prave": {
            1: "tráva",
            2: "srdce",
            3: "mak",
            4: "nôž"
        },
    }
    createConnectQuestion(5, array)
    createMathQuestion(6, "napiste vzorec na hovno");
    createCanvasQuestion(7,"Nakreslite leva");
    createCanvasQuestion(8,"Nakreslite sliepku");
    createConnectQuestion(9, array)
    //startTest();
    $('[data-toggle="tooltip"]').tooltip();
});


document.addEventListener("visibilitychange", onVisibilityChange);


function onVisibilityChange(){
    if (document.hidden){
        // $.getJSON("../api/testy/send-leave-tab-alert.php",function (data){
        //
        // })
    }
}

function startTest(){

    let zacniTest = {
        "akcia":"zacat-pisat",
        "kluc": sessionStorage.getItem("key")
    }
    let request = new Request('../api/testy/vypracovanie-testu.php', {
        method: 'POST',
        body: JSON.stringify(zacniTest),
    });
    fetch(request)
        .then(response => response.json())
        .then(data => {
            if (data.kod === "API_T__VT_U_1"){
                loadTest();
            }
            else if (data.kod === "API_T__VT_U_2"){
                loadTest();
            }
            else{
                console.log(data.kod);
            }
        });
}

function loadTest(){
    $.getJSON("../api/testy/nacitaj-test.php",function (data){
        if (data.kod === "API_T__LT_U_1") {
            $("#test-name").text(data.data_testu.nazov);
            printTest(data.data_testu.otazky)
        }
    })
}

function printTest(otazky){
    $.each(otazky,function (index){
        let otazka = this;
        switch (otazka.typ){
            case 1:true;break;
            case 2:true;break;
            case 3:createConnectQuestion(index,otazka);break;
            case 4:createMathQuestion(index,otazka.nazov);break;
            case 5:createCanvasQuestion(index,otazka.nazov);break;
        }
    })
}

function createCanvasQuestion(order,name){

    let questionDiv = createQuestionDiv(order,name,'canvas');
    questionDiv.append(createCanvas(order),createFormPictureInput(order));

}

function createCanvas(order){
    let wholeCanvas = document.createElement("div");
    $(wholeCanvas).addClass("whole-canvas math-input-content-"+order);

    let canvas = document.createElement("canvas");
    $(canvas).addClass("canvas-style");
    $(canvas).attr("id","canvas-"+order);
    let ctx = canvas.getContext("2d");
    canvasSettings(canvas,ctx);
    let canvasHeader = createCanvasHeader(canvas,ctx);
    wholeCanvas.append(canvasHeader,canvas);
    return wholeCanvas;
}

function createCanvasHeader(canvas,ctx){
    let canvasHeader = document.createElement("header");
    $(canvasHeader).addClass("canvas-header");
    let drawTools = document.createElement("div");
    $(drawTools).addClass("draw-tools");
    let pencilButton = document.createElement("div");
    let pencilImg = document.createElement("img");
    let eraseButton = document.createElement("div");
    let eraseImg = document.createElement("img");
    $(pencilButton).addClass("draw-button");
    $(pencilImg).addClass("draw-img");
    $(pencilImg).attr({
        "src":"resources/pictures/pencil-2.svg"
    })
    $(pencilButton).on("click",function (){
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 10;
    });
    $(pencilButton).append(pencilImg);
    $(eraseButton).addClass("draw-button");
    $(eraseImg).addClass("draw-img");
    $(eraseImg).attr({
        "src":"resources/pictures/rubber.svg"
    });
    $(eraseButton).on("click",function (){
        ctx.strokeStyle = 'white';
        ctx.lineWidth = 25;
    });
    $(eraseButton).append(eraseImg);

    let eraseAllButton = document.createElement("div");
    let eraseAllImage = document.createElement("img");
    $(eraseAllButton).addClass("clean-button");
    $(eraseAllImage).addClass("clean-img");
    $(eraseAllImage).attr({
        "src":"resources/pictures/clean.svg"
    });
    $(eraseAllButton).on("click",function (){
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });
    eraseAllButton.append(eraseAllImage);
    drawTools.append(pencilButton,eraseButton);
    canvasHeader.append(drawTools, eraseAllButton);
    return canvasHeader;
}

function canvasSettings(myCan, ctx){
    myCan.width = window.innerWidth;
    myCan.height = window.innerHeight;

    ctx.lineWidth = 10;

    const mouse = {
        x: 0, y: 0,                        // coordinates
        lastX: 0, lastY: 0,                // last frames mouse position
        b1: false, b2: false, b3: false,   // buttons
        buttonNames: ["b1", "b2", "b3"],   // named buttons
    }

    function mouseEvent(event) {

        let bounds = myCan.getBoundingClientRect();
        mouse.x = event.pageX - bounds.left - scrollX;
        mouse.y = event.pageY - bounds.top - scrollY;


        mouse.x /= bounds.width;
        mouse.y /= bounds.height;

        mouse.x *= myCan.width;
        mouse.y *= myCan.height;

        if (event.type === "mousedown") {
            mouse[mouse.buttonNames[event.which - 1]] = true;  // set the button as down
        } else if (event.type === "mouseup") {
            mouse[mouse.buttonNames[event.which - 1]] = false; // set the button up
            ctx.beginPath();
        }else if (event.type === "touchstart") {
            event.preventDefault();
            mouse[mouse.buttonNames[event.whzich]] = true;
        }
        else if (event.type === "touchend") {
            mouse[mouse.buttonNames[event.which]] = false; // set the button up
            ctx.beginPath();
        }
    }

    myCan.addEventListener("mousemove", mouseEvent);
    myCan.addEventListener("mousedown", mouseEvent);
    myCan.addEventListener("mouseup",   mouseEvent);
    myCan.addEventListener("touchstart", mouseEvent);
    myCan.addEventListener("touchend", mouseEvent);
    myCan.addEventListener("touchmove", mouseEvent);

    function mainLoop(time) {
        if (mouse.b1) {  // is button 1 down?

            ctx.lineCap = "round";
            ctx.lineJoin = "round";
            ctx.beginPath();
            ctx.moveTo(mouse.lastX,mouse.lastY);
            ctx.lineTo(mouse.x,mouse.y);
            ctx.stroke();
        }


        // save the last known mouse coordinate here not in the mouse event
        mouse.lastX = mouse.x;
        mouse.lastY = mouse.y;
        requestAnimationFrame(mainLoop); // get next frame
    }
    requestAnimationFrame(mainLoop);

}

function createMathQuestion(order,name){
    let mathField = createMathField(order);
    let formPictureInput = createFormPictureInput(order);

    let questionDiv = createQuestionDiv(order,name,'math');

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
    $(div).addClass("form-group col-12");
    row.append(div);
    return div;
}

function createQuestionDiv(order,name,type){
    let questionDiv = document.createElement("div");
    $(questionDiv).addClass("question-style");
    $(questionDiv).attr("id","question-"+order)
    questionDiv.append(createQuestionName(order,name,type))
    $("#test-questions").append(questionDiv);
    return questionDiv;
}

function createQuestionName(order,name,type){
    let questionHeader = document.createElement("header");
    $(questionHeader).addClass("question-header");
    let questionH3 = document.createElement("h3");
    $(questionH3).text(order+". "+name);
    questionHeader.append(questionH3);
    if (type !== null){
        let pictureButton = $("<div class=\"change-input-button math-input-content-"+order+"\" data-toggle=\"tooltip\" title=\"Vložiť obrázok riešenia\" onclick=\"changeToPictureInput("+order+")\">\n" +
            "                <img class=\"change-input-image\" src=\"resources/pictures/add.svg\" width=\"25\" height=\"25\" alt=\"picture input\">\n" +
            "            </div>").get(0);

        let mathButton;
        if (type === 'math') {
            mathButton = $("<div class=\"change-input-button picture-input-content-" + order + "\" data-toggle=\"tooltip\" title=\"Vložiť matematický vzorec\" onclick=\"changeToMathInput(" + order + ")\" style='display: none'>\n" +
                "                <img class=\"change-input-image\" src=\"resources/pictures/mathematics.svg\" width=\"32\" height=\"32\" alt=\"math input\">\n" +
                "            </div>").get(0);
        }else if (type === 'canvas'){
            mathButton = $("<div class=\"change-input-button picture-input-content-" + order + "\" data-toggle=\"tooltip\" title=\"Kresliť\" onclick=\"changeToMathInput(" + order + ")\" style='display: none'>\n" +
                "                <img class=\"change-input-image\" src=\"resources/pictures/canvas.svg\" width=\"25\" height=\"25\" alt=\"math input\">\n" +
                "            </div>").get(0);
        }
        questionHeader.append(pictureButton);
        questionHeader.append(mathButton);
    }
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

function createConnectDiv(index,question){
    let connectorDiv=document.createElement("div");
    let leftDiv=document.createElement("div");
    let rightDiv=document.createElement("div");


    leftDiv.setAttribute('class','connect-card-wrapper-left');
    rightDiv.setAttribute('class','connect-card-wrapper-right');

    connectorDiv.append(leftDiv,rightDiv);
    connectorDiv.setAttribute('class','connector-wrapper');

    for (const odpoved in question.odpovede_lave) {
        let id=`question-${index}-left-${odpoved}`;
        let card=createCard(id,question.odpovede_lave[odpoved]);

        card.classList.add(`connect-left-${index}`);
        leftDiv.appendChild(card);

    }
    for (const odpoved in question.odpovede_prave) {
        let id=`question-${index}-right-${odpoved}`;
        let card=createCard(id,question.odpovede_prave[odpoved]);
        card.classList.add(`connect-right-${index}`);
        rightDiv.appendChild(card);

    }
    return connectorDiv;
}

function createConnectQuestion(index,question){
    let questionDiv = createQuestionDiv(index,question.nazov,null);
    questionDiv.appendChild(createConnectDiv(index,question));


    //objekty uz musia byt vytvorene aby som k nim mohol priradit jsPlumb
    let newJsPlumbInstance=jsPlumb.getInstance();
    instances.push(newJsPlumbInstance);
    const lefties=document.getElementsByClassName(`connect-left-${index}`);
    const righties=document.getElementsByClassName(`connect-right-${index}`);

    for(let i=0;i<lefties.length;i++){
        newJsPlumbInstance.makeSource(lefties[i].id,{anchor:"Continuous",endpoint:["Dot", { width:5, height:5 }], maxConnections:1,});
    }
    for(let i=0;i<lefties.length;i++){
        newJsPlumbInstance.makeTarget(righties[i].id,{anchor:"Continuous",endpoint:["Dot", { width:5, height:5 }], maxConnections:1,});
    }


}

function createCard(id,card_phrase){
    let card=document.createElement("div");
    card.setAttribute('class','connect-card');
    let phrase=document.createElement("h4");

    card.setAttribute('id',id);
    phrase.innerText=card_phrase;

    card.appendChild(phrase);


    return card
}
function checkConnectQuestion(){

    for (let i = 0; i < instances.length; i++) {
        let object={};
        let pary=[];

        for(let j=0;j<instances[i].getAllConnections().length;j++){
            let dvojice={};
            dvojice={lava:document.getElementById(instances[i].getAllConnections()[j].sourceId).firstElementChild.innerHTML,prava:document.getElementById(instances[i].getAllConnections()[j].targetId).firstElementChild.innerHTML};
            pary.push(dvojice);
            let q=Number(instances[i].getAllConnections()[0].sourceId.substr(9,1));

            object={index:q};

        }
        object.typ_odpovede= "parovacia";
        object.pary=pary;
        console.log(object);
    }

}

