
document.getElementById('send').onclick=function (){
    let test={};
    if(document.getElementById('meno-testu').value===""){
        document.getElementById('error-msg').innerText="nazov testu nemoze byt prazdny";
        return null;
    }
    else{
        document.getElementById('error-msg').innerText="";
    }


    test.nazov=document.getElementById('meno-testu').value;
    test.casovy_limit=Number(document.getElementById('cas-num').value);
    test.aktivny=false;
    let otazky= {};
    const q=document.getElementsByClassName('question');

    for(let i=0;i<q.length;i++){
        let q1Id=q[i].id;
        otazky[Number(i+1)]={}
        if(q[i].value===""){
            document.getElementById('error-msg').innerText=`nazov otazky cislo ${i+1} nemoze byt prazdny`;
            return null;
        }
        else{
            document.getElementById('error-msg').innerText="";
        }
        otazky[Number(i+1)].nazov=q[i].value;

        if(q[i].classList.contains("type-1")){
            otazky[Number(i+1)].typ=1;
        }
        if(q[i].classList.contains("type-2")){
            otazky[Number(i+1)].typ=2;
        }
        if(q[i].classList.contains("type-3")){
            otazky[Number(i+1)].typ=3;
        }
        if(q[i].classList.contains("type-4")){
            otazky[Number(i+1)].typ=4;
        }
        if(q[i].classList.contains("type-5")){
            otazky[Number(i+1)].typ=5;
        }

        const qOpt=document.getElementsByClassName(q1Id+"-option");
        if(q[i].classList.contains("type-1")===true) {

            let spravne_odpovede = [];
            for (let j = 0; j < qOpt.length; j++) {
                spravne_odpovede[j] = qOpt[j].value;
            }

            otazky[Number(i+1)].spravne_odpovede = spravne_odpovede;
            if(spravne_odpovede.length===0){
                document.getElementById('error-msg').innerText=`otazka cislo ${i+1} nema zadanu ziadnu odpoved`;
                return null;
            }else{
                document.getElementById('error-msg').innerText="";
            }
        }
        else if(q[i].classList.contains("type-2")===true) {
            let odpovede=[];
            for (let j = 0; j < qOpt.length; j++) {
                let spravnost=false;
                if(qOpt[j].parentElement.classList.contains('spravna')===true){
                    spravnost=true;
                }
                odpovede[j] = {text: qOpt[j].value , je_spravna: spravnost};

                if(odpovede.length===0){
                    document.getElementById('error-msg').innerText=`otazka cislo ${i+1} nema zadanu ziadnu odpoved`;
                    return null;
                }else{
                    document.getElementById('error-msg').innerText="";
                }
            }
            otazky[Number(i+1)].odpovede = odpovede;
            if(document.getElementById(`know-${q1Id}`).classList.contains('vie-spravne')){
                otazky[Number(i+1)].vie_student_pocet_spravnych=true;
            }
            if(document.getElementById(`know-${q1Id}`).classList.contains('nevie-spravne')){
                otazky[Number(i+1)].vie_student_pocet_spravnych=false;
            }

        }

        else if(q[i].classList.contains("type-3")===true) {
            let odpovede_lave={};
            let odpovede_prave={};
            let pary=[]
            const moznostDiv=document.getElementById(`moznostDiv-${i+1}`);

            for(let j=1;j<moznostDiv.children.length;j++){

                odpovede_lave[Number(j)]=moznostDiv.children[j].children[1].children[0].value;
                odpovede_prave[Number(j)]=moznostDiv.children[j].children[3].children[0].value;
                if(moznostDiv.children[j].children[1].children[0].value!=="" && moznostDiv.children[j].children[3].children[0].value!==""){
                    pary.push({lava:j,prava:j});

                }

            }
            otazky[Number(i+1)].odpovede_lave=odpovede_lave;
            otazky[Number(i+1)].odpovede_prave=odpovede_prave;
            otazky[Number(i+1)].pary=pary;







        }
    }
    test.otazky=otazky
    console.log(JSON.stringify(test));

    fetch("../api/testy/novy-test.php", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(test)
    })
        .then(response => response.json())
        .then(data => console.log(data));
}
