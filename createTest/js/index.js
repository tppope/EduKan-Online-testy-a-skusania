let qId=1;
let qNum=1;
let conNum=0;
let listOfInstances=[];


function  clicked(id,pairId){
    const items=document.getElementsByClassName('item');
    for(let i=0;i<items.length;i++){
        items[i].setAttribute('class','item');
    }
    document.getElementById(id).setAttribute('class','item active');
    document.getElementById(id).setAttribute('class','item active');

}
function showQ(id,pId){
    document.getElementById(id).style.display='block';
    if(document.getElementById(pId)!=null){
        document.getElementById(pId).remove();
    }
}
function store(value,id){
    const pId=`q`+id.toString();
    const p=document.createElement('p');
    if(value===""){
        value="ZADAJTE TEXT OTÁZKY"
        p.setAttribute('style',`font-size:large;margin:1rem;color:#e24c4b`);
    }else{
        p.setAttribute('style',`margin:1rem`);
    }



    document.getElementById(`text-${id}`).appendChild(p);

    p.setAttribute('id',pId);
    p.setAttribute('onclick',`showQ(${id},this.id)`);

    document.getElementById(id).style.display='none';
    p.innerText=value;
}



function createQuestion(typ,number){

    document.querySelector(".navbar-wrapper")
                .classList.toggle("change");


    document.getElementById('test-footer').hidden=false;




    const qDiv=document.createElement('div');
    const test= document.getElementById('test');
    const question =document.createElement('textarea');
    const pDiv=document.createElement('div');
    const questionNum=document.createElement('h1');
    const button=document.createElement('button');
    const moznostDiv=document.createElement('div');
    const rmButton=removeBtn();
    const header=document.createElement('div');


    moznostDiv.setAttribute('id',`moznostDiv-${qId}`);

    button.setAttribute('id',`adder-${qId}`);
    button.setAttribute('class','btn btn-dark')

    button.innerHTML=`<label for=${button.id} style='vertical-align:top;font-size:x-large'>Pridaj možnosť</label>`;

    button.setAttribute('style',`justify-self:end;max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;cursor:pointer`);
    button.setAttribute('onmouseover',"this.setAttribute('style',' max-width:12rem;max-height:3rem;cursor:pointer')");
    button.setAttribute('onmouseleave',"this.setAttribute('style','max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;cursor:pointer')");
       // button.setAttribute('onclick',`skutocne(this.parentElement.parentElement.remove());`);

    questionNum.innerText=qId.toString();
    questionNum.setAttribute('id',`number-${qId}`);
    questionNum.setAttribute('class',`poradovnik`);
    questionNum.setAttribute('style',`font-size: 2.5rem; font-weight: bold`);

    rmButton.setAttribute('style','max-height:2em;margin:0.5em;cursor:pointer')
    rmButton.setAttribute('id',`remover-${qId}`);

    header.appendChild(questionNum);
    header.appendChild(rmButton);
    header.setAttribute('class', 'header');
    header.setAttribute('style', 'display:flex;justify-content:space-between;padding:1rem');

    qDiv.appendChild(header);
    qDiv.appendChild(question);

    test.appendChild(qDiv);

    qDiv.setAttribute('id',`question-${qId}`)
    qDiv.setAttribute('class','card');
    qDiv.setAttribute('style','padding:3rem;margin-bottom:3rem;z-index:0');
    qDiv.appendChild(pDiv);

    pDiv.setAttribute('id',`text-${qId}`);
    pDiv.setAttribute('style',`margin-bottom:2rem`);


    question.setAttribute('class',`question type-${typ}`);
    question.setAttribute('rows',`5`);
    question.setAttribute('style',`margin-bottom: 1rem`);
    question.setAttribute('id',qId.toString());
    question.setAttribute('onblur',`store(this.value,${qId})`);

    button.setAttribute('onclick',`addChoice(${qId},${typ})`)

    const h3=document.createElement('h3',);




    if(typ===1  ){
        qDiv.appendChild(button);
        qDiv.appendChild(moznostDiv);
        store(question.value,qId);
    }

    if(typ===3){
        question.value="Spojte správne tvrdenia";
        store(question.value,qId);

        const lavyDiv=document.createElement('div');
        const pravyDiv=document.createElement('div');
        const parentDiv=document.createElement('div');
        const btnL=document.createElement('button');
        const btnP=document.createElement('button');
        const p=document.createElement('p');
        const coverL=document.createElement('div');
        const coverP=document.createElement('div');
        listOfInstances.push(jsPlumb.getInstance());
        p.setAttribute('style','padding-top:1.5em;font-size:1rem;margin-top:1rem;margin-bottom:3rem');


        btnL.setAttribute('class','btn btn-dark')

        btnL.innerHTML=`<label  style='vertical-align:top;font-size:x-large'>Pridaj možnosť</label>`;
        btnL.setAttribute('onclick',`addCard(this,${qId},${typ},${(listOfInstances.length-1).toString()})`)

        btnL.setAttribute('style',`max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;cursor:pointer`);
        btnL.setAttribute('onmouseover',"this.setAttribute('style',' max-width:12rem;max-height:3rem;cursor:pointer')");
        btnL.setAttribute('onmouseleave',"this.setAttribute('style','max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;cursor:pointer')");
        btnL.setAttribute('class','btn btn-dark left')

        btnP.innerHTML=`<label  style='vertical-align:top;font-size:x-large'>Pridaj možnosť</label>`;
        btnP.setAttribute('onclick',`addCard(this,${qId},${typ},${(listOfInstances.length-1).toString()})`)

        btnP.setAttribute('style',`max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;cursor:pointer`);
        btnP.setAttribute('onmouseover',"this.setAttribute('style',' max-width:12rem;max-height:3rem;cursor:pointer')");
        btnP.setAttribute('onmouseleave',"this.setAttribute('style','max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;cursor:pointer')");
        btnP.setAttribute('class','btn btn-dark right')
        lavyDiv.appendChild(btnL);
        pravyDiv.appendChild(btnP);
        parentDiv.appendChild(lavyDiv);
        parentDiv.appendChild(pravyDiv);
        parentDiv.setAttribute('style','display:flex;justify-content:space-between');




        qDiv.appendChild(p);
        p.innerHTML="<img src='images/info.png' alt='info' style='max-height:1.5rem;margin-right: 0.5em'>Dvojicu vytvoríte ťahom ľavej karty smerom k pravej.";
        qDiv.appendChild(parentDiv);
        moznostDiv.setAttribute('style','display:flex;justify-content:space-between;margin-top:2rem');
        moznostDiv.appendChild(coverL);
        moznostDiv.appendChild(coverP);
        //coverL.setAttribute('style','display:grid');
        coverL.setAttribute('class','left');
        //coverP.setAttribute('style','display:grid');
        coverP.setAttribute('class','right');

        // moznostDiv.setAttribute('style','display:flex;justify-content:space-around');

        qDiv.appendChild(moznostDiv);


    }

    if(typ===2){
        qDiv.appendChild(button);
        const vieOtazkyDiv=document.createElement('button');
        const vieOtazky=document.createElement('img');
        const popis=document.createElement('p');

        popis.innerText='spravné ukázané';
        popis.setAttribute('style','padding-top:1.5em;font-size:1rem;margin:auto');

        vieOtazky.setAttribute('id',`know-${qId}`);
        vieOtazky.setAttribute('class',`vie-spravne`);

        vieOtazky.setAttribute('src','images/knownQknown.png');
        vieOtazky.setAttribute('style','max-width:2rem;justify-self:space-around');
        vieOtazkyDiv.setAttribute('onclick','viePocet(this)');

        vieOtazky.setAttribute('alt','vie otazky');
        vieOtazkyDiv.setAttribute('style','border-radius:15px;margin:auto;width:5rem;border: 1px solid black');
        vieOtazkyDiv.setAttribute('class','btn btn-outline-dark');

        vieOtazkyDiv.appendChild(vieOtazky);



        qDiv.appendChild(vieOtazkyDiv);
        qDiv.appendChild(popis);
        qDiv.appendChild(moznostDiv);
        store(question.value,qId);
    }

    if(typ===4){


        h3.innerText="Odpoveďou na túto otázku bude obrázok";
        qDiv.appendChild(h3);
        store(question.value,qId);
    }
    if(typ===5){


        h3.innerText="Odpoveďou na túto otázku bude matematický vzorec";
        qDiv.appendChild(h3);
        store(question.value,qId);
    }


    qId=qId+1;


}

function viePocet(node){
    if(node.children[0].classList.contains('vie-spravne')){
        node.children[0].src='images/knownQ.png';
        node.children[0].alt='nevie spravne';
        node.children[0].setAttribute('class','nevie-spravne');
        node. setAttribute('class','btn btn-dark');
        node.parentElement.children[5].innerText='správne skryté';

    }

    else if(node.children[0].classList.contains('nevie-spravne')){
        node.children[0].src='images/knownQknown.png';
        node.children[0].alt='vie spravne';
        node.children[0].setAttribute('class','vie-spravne');
        node. setAttribute('class','btn btn-outline-dark');
        node.parentElement.children[5].innerText='správne ukázane';

    }
}

function removeBtn(){
    const button=document.createElement('img');
    button.setAttribute('src','images/trash.png');
    button.setAttribute('onmouseover',"this.src='images/thrash-active.png'");
    button.setAttribute('onmouseout',"this.src='images/trash.png'");
    button.setAttribute('class',"remover");
    button.setAttribute('style','max-height:1rem;vertical-align:top;margin-right:0.5em;cursor:pointer')
    button.setAttribute('alt','cancel');
    button.setAttribute('onclick',`this.parentElement.parentElement.remove();poradovnik(this.id)`);


    return button;
}









function addCard(node,id,typ,instance){
    const moznostDiv=node.parentElement.parentElement.parentElement.children[5];
    const left=moznostDiv.children[0];
    const right=moznostDiv.children[1];
    const button=removeBtn();
    const cover=document.createElement('div');
    const input=document.createElement('textarea');
    const dot=document.createElement('div');
    const removeDiv=document.createElement('div');

    cover.setAttribute('id',`connect-${conNum++}`)
    cover.setAttribute('style',`z-index:1;border:1px solid black;border-radius:25px;width:2rem;margin:1rem`)

    input.setAttribute('class',instance);
    button.setAttribute('onclick','this.parentElement.remove();listOfInstances['+instance+'].remove(this.parentElement.children[2].id);');
    removeDiv.setAttribute('style','display:flex;margin:1rem');
    if(node.classList.contains('right')){
        cover.appendChild(dot)

        removeDiv.appendChild(cover)
        removeDiv.appendChild(input)
        removeDiv.appendChild(button);



        right.appendChild(removeDiv)
        //instance.makeTarget(cover.id, {anchor:"Continuous",endpoint:["Dot", { width:5, height:5 }], maxConnections:1,});
        dot.setAttribute('onclick','createTarget(this.parentElement.id,listOfInstances['+instance+']);this.remove()');

    }

    if(node.classList.contains('left')){
        removeDiv.appendChild(button);
        removeDiv.appendChild(input)
        cover.appendChild(dot)

        removeDiv.appendChild(cover)

        left.appendChild(removeDiv);


        dot.setAttribute('onclick','createSource(this.parentElement.id,listOfInstances['+instance+']);this.remove()');

    }
    dot.click();

}
function createSource(id,instance){

    instance.makeSource(id, {anchor:"Continuous",endpoint:["Dot", { width:5, height:5 }], maxConnections:1,});
}

function createTarget(id,instance){
    instance.makeTarget(id, {
    anchor:"Continuous",
    endpoint:["Dot", { width:5, height:5 }],
    maxConnections:1,
});

}
function addChoice(id,typ){
    const moznostDiv=document.getElementById(`moznostDiv-${id}`)
    moznostDiv.setAttribute('class',`${id}-options row `)
    moznostDiv.setAttribute('style',`display:flex;margin-top:2rem`)
    const moznostInternyDiv=document.createElement('div');
    moznostInternyDiv.setAttribute('style','display:flex');

    const x=document.createElement('div');
    x.setAttribute('class','opt col');
    x.setAttribute('style','margin:1rem;');
    x.appendChild(removeBtn());


    const moznost=document.createElement('input');
    moznost.required=true;
    moznost.setAttribute('type','text');
    moznost.setAttribute('class',`${id}-option`);

    x.appendChild(moznost);




    if(typ===2){
        moznost.setAttribute('style','border:  dashed #e24c4b');
        x.setAttribute('class','opt nespravna');
        const checkbox=document.createElement('img');
        checkbox.setAttribute('src','images/cancel.png');
        checkbox.setAttribute('alt','wrong');
        checkbox.setAttribute('style','max-width:1rem;margin-left:0.5rem;vertical-align: middle;horizontal-align:left')
        checkbox.setAttribute('onclick','addTrue(this)');
        x.appendChild(checkbox);

    }


    moznostInternyDiv.appendChild(x);
    moznostDiv.appendChild(moznostInternyDiv);


}
function  addTrue(node){
    if(node.parentElement.classList.contains('nespravna')===true){
        node.parentElement.setAttribute('class','opt spravna');
        node.parentElement.children[1].setAttribute('style','border: dashed  #cfeb6b')
        node.setAttribute('src','images/valid.png');
    }
    else if(node.parentElement.classList.contains('spravna')===true){
        node.parentElement.setAttribute('class','opt nespravna');
        node.parentElement.children[1].setAttribute('style','border: dashed #e24c4b')
        node.setAttribute('src','images/cancel.png');
    }

}

function changeIDs(oldID,newID){

    //funkcia sposobuje bugy tak ju nepouzivat
    //jej uloha bola iba urovnat html
    //na konecny stav odosielaneho testu nema vplyv

    //disable all removers to prevent missmatch
    document.getElementById(`question-${oldID}`).setAttribute('id',`question-${newID}`);


    document.getElementById(`moznostDiv-${oldID}`).setAttribute('id',`moznostDiv-${newID}`);
    const typ=document.getElementById(oldID).classList[1].split('type-')[1];
    document.getElementById(`adder-${oldID}`).setAttribute('id',`adder-${newID}`);
    document.getElementById(`adder-${newID}`).setAttribute('onclick',`addChoice(${newID},${typ})`);

    document.getElementById(`number-${oldID}`).setAttribute('id',`number-${newID}`);

    document.getElementById(`question-${newID}`).children[0].setAttribute('id',`remover-${newID}`);


    document.getElementById(`text-${oldID}`).setAttribute('id',`text-${newID}`);
    document.getElementById(`adder-${newID}`).children[0].setAttribute('for',`adder-${newID}`);

    document.getElementById(oldID).setAttribute('id',newID.toString());
    document.getElementById(newID).setAttribute('onblur',`store(this.value,${newID})`);
    //enable them back


}

function poradovnik(id) {
    const otazky = document.getElementsByClassName('question');
    const cisla = document.getElementsByClassName('poradovnik');
    let poradie=1;

    let splitId=id.split('remover-')

    if(otazky.length===0){
        document.getElementById('test-footer').hidden=true;
        qId=1;
    }else{
        for(let i=Number(splitId[1]);i< otazky.length+1;i++){

           // changeIDs(i+1,i);
        }
    }
    for(let otazka of otazky){

       if(poradie!==otazka.id){
           cisla[poradie-1].innerText=poradie.toString();

           qId=poradie+1;

       }
        poradie=poradie+1;
    }


}





