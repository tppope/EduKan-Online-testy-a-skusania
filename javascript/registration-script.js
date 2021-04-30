$(window).on("load", function () {
    $('[data-toggle="tooltip"]').tooltip();
})

function checkSamePasswords() {
    let password2 = $("#password-repeat");
    if ($("#password").val() !== password2.val())
        password2.get(0).setCustomValidity("Heslá sa musia zhodovať");
    else
        password2.get(0).setCustomValidity("");
}

function removeIsInvalid(){
    $("#email").removeClass("is-invalid");
}

function checkFormValidation(form) {
    let inputs = $(form).find("input");
    for (let i = 0; i < inputs.length; i++) {
        if (!inputs.get(i).checkValidity())
            return false;
    }
    return true;
}

function submitRegistration() {
    let form = document.getElementById("registration-form");
    if (checkFormValidation(form)) {
        let request = new Request('api/uzivatelia/registracia/', {
            method: 'POST',
            body: new FormData(form),
        });
        fetch(request)
            .then(response => response.json())
            .then(data => {
                let email = $("#email");
                if (!data.error) {
                    sessionStorage.setItem("regStatus", data.status);
                    window.location.replace("index.html");
                } else {
                    if (data.errorCode === 1062)
                        email.addClass("is-invalid");
                    else {
                        sessionStorage.setItem("regStatus", data.status);
                        window.location.replace("index.html");
                    }
                }
            });
    }
}
