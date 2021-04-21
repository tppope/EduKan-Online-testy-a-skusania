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

    document.getElementById(id).style.display='none';
    p.innerText=value;
}



function createQuestion(typ,number){

    $('#modal').modal('toggle');

    const qDiv=document.createElement('div');
    const test= document.getElementById('test');
    const question =document.createElement('textarea');
    const pDiv=document.createElement('div');
    const questionNum=document.createElement('h1');
    const button=document.createElement('button');
    const moznostDiv=document.createElement('div');

    moznostDiv.setAttribute('id',`moznostDiv-${number}`)

    button.innerText='PRIDAJ MOZNOST';

    questionNum.innerText=qId.toString()+'.';

    qDiv.appendChild(questionNum);
    qDiv.appendChild(question);

    test.appendChild(qDiv);

    qDiv.setAttribute('id',`question-${number}`)
    qDiv.appendChild(pDiv);

    pDiv.setAttribute('id',`text-${qId}`);

    question.setAttribute('class',`question type-${typ}`);
    question.setAttribute('id',qId.toString());
    question.setAttribute('onblur',`store(this.value,${qId})`);
    button.setAttribute('onclick',`addChoice(${number})`)
    qDiv.appendChild(button);
    qDiv.appendChild(moznostDiv);

    qId=qId+1;


}
function addChoice(id){
    const moznostDiv=document.getElementById(`moznostDiv-${id}`)
    moznostDiv.setAttribute('class',`${id}-options`)
    moznostDiv.setAttribute('style',`display:grid`)
    const moznostInternyDiv=document.createElement('div');
    moznostInternyDiv.setAttribute('style','display:flex');

    const x=document.createElement('div');
    x.setAttribute('class','remove-opt');
    x.setAttribute("draggable","true");
    x.setAttribute('ondrag','thrash(this)');

    const moznost=document.createElement('input');

    moznost.setAttribute('type','text');
    moznost.setAttribute('class',`${id}-option`);
    x.appendChild(moznost);
    moznostInternyDiv.appendChild(x);
    moznostDiv.appendChild(moznostInternyDiv);


}
function thrash(node){
    document.getElementById('bin').ondragover(node.remove());
}
//TODO
// 1.vytvorit divka
// 2 vyriest posielanie ajax cez kontrolu div