
/**
 * Funcion para validar los elementos requeridos (*)
 * @param {Object} forma
 */

function validarForma(forma) {
    var password = forma.contra;
    if (password.value == "" || password.value.length < 3) {
        alert("Debe proporcionar un password al menos de 3 caracteres");
        password.focus();
        password.select();
        return false;
    }

    alert("Formulario valido, enviando datos...");
    return true;
}