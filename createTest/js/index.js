let qId=1;
let qNum=1;
// document.getElementsByClassName()
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
        value="zadajte text otazky"
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

    $('#modal').modal('toggle');
    document.getElementById('test-footer').hidden=false;


    console.log(getPos(document.getElementById('test')));

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

    button.innerHTML=`<label for=${button.id} style='vertical-align:top;font-size:x-large'>Pridaj moznost</label>`;

    button.setAttribute('style',`justify-self:end;max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;`);
    button.setAttribute('onmouseover',"this.setAttribute('style',' max-width:12rem;max-height:3rem;')");
    button.setAttribute('onmouseleave',"this.setAttribute('style','max-width:12rem;max-height:3rem;box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;')");
       // button.setAttribute('onclick',`skutocne(this.parentElement.parentElement.remove());`);

    questionNum.innerText=qId.toString();
    questionNum.setAttribute('id',`number-${qId}`);
    questionNum.setAttribute('class',`poradovnik`);
    questionNum.setAttribute('style',`font-size: 2.5rem; font-weight: bold`);

    rmButton.setAttribute('style','max-height:2em;margin:0.5em')
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
    qDiv.setAttribute('style','padding:3rem;margin-bottom:3rem');
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

    if(typ===2){
        question.value="Spojte spravne tvrdenia";
        store(question.value,qId);
        const  canvas=document.createElement('canvas');
        canvas.setAttribute('class','align-self-stretch');

        canvas.setAttribute('style','margin:0');
        const lavyDiv=document.createElement('div');
        const pravyDiv=document.createElement('div');
        const pravyBtn=document.createElement('button');
        const lavyBtn=document.createElement('button');
        lavyBtn.innerText="pridaj moznost";
        lavyBtn.setAttribute('onclick',`addCard(this,${qId},${typ})`)
        lavyBtn.setAttribute('class',`lavyBtn`);
        pravyBtn.setAttribute('onclick',`addCard(this,${qId},${typ})`)
        pravyBtn.setAttribute('class',`pravyBtn`);
        pravyBtn.innerText="pridaj moznost";
        moznostDiv.appendChild(lavyDiv);
        lavyDiv.appendChild(lavyBtn);
        moznostDiv.appendChild(canvas)
        pravyDiv.appendChild(pravyBtn);
        moznostDiv.appendChild(pravyDiv);
        // moznostDiv.setAttribute('style','display:flex;justify-content:space-around');
        moznostDiv.setAttribute('class','d-flex justify-content-center');
        qDiv.appendChild(moznostDiv);


    }

    if(typ===3){
        qDiv.appendChild(button);
        const vieOtazkyDiv=document.createElement('button');
        const vieOtazky=document.createElement('img');
        const popis=document.createElement('p');
        popis.innerText='student vie pocet spravnych odpovedi';
        popis.setAttribute('style','padding-top:1.5em;font-size:small');
        vieOtazky.setAttribute('id',`know-${qId}`);
        vieOtazky.setAttribute('class',`vie-spravne`);

        vieOtazky.setAttribute('src','images/knownQknown.png');
        vieOtazky.setAttribute('style','max-height:4vh;');
        vieOtazkyDiv.setAttribute('onclick','viePocet(this)');

        vieOtazky.setAttribute('alt','vie otazky');
        vieOtazkyDiv.setAttribute('style','display:flex;margin:auto;max-width:13vw;max-height:7vh');
        vieOtazkyDiv.setAttribute('class','btn btn-outline-dark');
        vieOtazkyDiv.appendChild(vieOtazky);
        vieOtazkyDiv.appendChild(popis);


        qDiv.appendChild(vieOtazkyDiv);
        qDiv.appendChild(moznostDiv);
        store(question.value,qId);
    }

    if(typ===4){


        h3.innerText="Odpovedov na tuto otazku bude obrazok";
        qDiv.appendChild(h3);
        store(question.value,qId);
    }
    if(typ===5){


        h3.innerText="Odpovedov na tuto otazku bude matematicky vzorec";
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
        node.children[1].innerText='student nevie pocet spravnych odpovedi';

    }

    else if(node.children[0].classList.contains('nevie-spravne')){
        node.children[0].src='images/knownQknown.png';
        node.children[0].alt='vie spravne';
        node.children[0].setAttribute('class','vie-spravne');
        node.children[1].innerText='student vie pocet spravnych odpovedi';

    }
}

function removeBtn(){
    const button=document.createElement('img');
    button.setAttribute('src','images/trash.png');
    button.setAttribute('onmouseover',"this.src='images/thrash-active.png'");
    button.setAttribute('onmouseout',"this.src='images/trash.png'");
    button.setAttribute('class',"remover");
    button.setAttribute('style','max-height:1rem;vertical-align:top;margin-right:0.5em;')
    button.setAttribute('alt','cancel');
    button.setAttribute('onclick',`this.parentElement.parentElement.remove();poradovnik(this.id)`);


    return button;
}

function getPos(el) {
    // yay readability
    for (var lx=0, ly=0;
         el != null;
         lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
    return {x: lx,y: ly};
}
function spojit(node){
    ;
    console.log(getPos(node));
}


function addCard(node,id,typ){
   const moznostDiv=document.createElement('div');
    const moznost=document.createElement('input');
    const vonkajsiDiv=document.createElement('div');
    const spajac=document.createElement('img');
    spajac.setAttribute('src','images/inactive.png');
    spajac.setAttribute('style','max-height:1rem;vertical-align:center;margin:0.5em');
    spajac.setAttribute('onclick','spojit(this)')
    moznostDiv.setAttribute('style','margin-top:1rem')
    vonkajsiDiv.appendChild(moznostDiv);
    if(node.classList.contains('lavyBtn')){
        moznostDiv.appendChild(removeBtn());
        moznostDiv.appendChild(moznost);
        node.parentElement.setAttribute('style',`;display:grid`);
        node.setAttribute('style',`justify-self:center;max-height:2em`);
        moznostDiv.appendChild(spajac);
    }
    if(node.classList.contains('pravyBtn')){
        moznostDiv.appendChild(spajac);
        moznostDiv.appendChild(moznost);
        moznostDiv.appendChild(removeBtn());
        moznostDiv.children[2].setAttribute('style','max-height:1rem;vertical-align:top;margin-left:0.5rem')
        node.parentElement.setAttribute('style',`display:grid`);
        node.setAttribute('style',`justify-self:center;max-height:2em`);
    }

    node.parentElement.appendChild(vonkajsiDiv);


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




    if(typ===3){
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





