
document.getElementById('send').onclick=function (){
    let test={};
    let instance=0;
    if(document.getElementById('meno-testu').value===""){
        document.getElementById('error-msg').innerText="názov testu nemôže byť prázdny";
        return null;
    }
    else{
        document.getElementById('error-msg').innerText="";
    }


    test.nazov=document.getElementById('meno-testu').value;
    test.casovy_limit=Number(document.getElementById('cas-num').value);
    let otazky= {};
    const q=document.getElementsByClassName('question');

    for(let i=0;i<q.length;i++){
        let q1Id=q[i].id;
        otazky[Number(i+1)]={}
        if(q[i].value===""){
            document.getElementById('error-msg').innerText=`text otázky číslo ${i+1} nemôže byť prázdny`;
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
                document.getElementById('error-msg').innerText=`otázka číslo ${i+1} nemá zadanú žiadnu odpoveď`;
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
                    document.getElementById('error-msg').innerText=`otázka číslo ${i+1} nemá zadanú žiadnu odpoveď`;
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
            const moznostDiv=document.getElementById(`moznostDiv-${i+1}`);

            let odpovede_lave={};
            let odpovede_prave= {};
            let odpovede_laveObj=[];
            let odpovede_praveObj=[];
            for(let l=0;l<moznostDiv.children[0].children.length;l++){
                odpovede_laveObj.push(moznostDiv.children[0].children[l].children[2].id);
                odpovede_lave[l+1]=moznostDiv.children[0].children[l].children[1].value;
            }
            for(let p=0;p<moznostDiv.children[1].children.length;p++){
                odpovede_praveObj.push(moznostDiv.children[1].children[p].children[0].id);
                odpovede_prave[p+1]=moznostDiv.children[1].children[p].children[1].value;
            }
            otazky[i+1].odpovede_lave=odpovede_lave;
            otazky[i+1].odpovede_prave=odpovede_prave;

            const conn=listOfInstances[instance].getConnections();
            if(conn.length===0){
                document.getElementById('error-msg').innerText=`v otázke číslo ${i+1} musíte mať vytvorené aspoň jedno spojenie`;
                return null;
            }
            instance=instance++;
            let pary=[];
            for(let j=0;j<conn.length;j++){
                let dvojice={lava:odpovede_laveObj.indexOf(conn[j].sourceId)+1, prava:odpovede_praveObj.indexOf(conn[j].targetId)+1};

                pary.push(dvojice)
            }
            otazky[i+1].pary=pary;



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
        .then(data =>{
            console.log(data);
            if (data.kod === "API_T__NT_U_1"){
                //window.location.replace("../teacher-homescreen.html")
            }
        });


}
