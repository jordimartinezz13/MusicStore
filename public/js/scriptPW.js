window.onload = function () {
    var cambio = document.getElementById("ShowPassword");
    var pw = document.getElementById("inputPassword");
    cambio.addEventListener("change", function () {
        if (this.checked) {
            pw.type = "text";
        } else {
            pw.type = "password";
        }
    });
}