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

    if(value===""){
        value="zadajte text otazky"
    }
    const p=document.createElement('p');


    document.getElementById(`text-${id}`).appendChild(p);

    p.setAttribute('id',pId);
    p.setAttribute('onclick',`showQ(${id},this.id)`);
    p.setAttribute('style',`margin:1rem`);
    document.getElementById(id).style.display='none';
    p.innerText=value;
}

function skutocne(id){
    //TODO
    // 1.modal
    //poradovnik(this.id)
    //opytat sa ci naozja chce vymazat otazku
}

function createQuestion(typ,number){

    $('#modal').modal('toggle');
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

    question.required=true;
    question.setAttribute('class',`question type-${typ}`);
    question.setAttribute('rows',`5`);
    question.setAttribute('style',`margin-bottom: 1rem`);
    question.setAttribute('id',qId.toString());
    question.setAttribute('onblur',`store(this.value,${qId})`);

    button.setAttribute('onclick',`addChoice(${qId},${typ})`)




    qDiv.appendChild(button);
    qDiv.appendChild(moznostDiv);


    qId=qId+1;


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





