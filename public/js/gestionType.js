/**
 * Fonction qui se déclenche au chargement de la page.
 * 
 * @returns {undefined}
 */

window.onload = function () {
    console.log("grg");
    var html = document.getElementsByTagName('html')[0];
    if (document.querySelector("#type")) {
        localStorage.setItem("type", document.querySelector("#type").innerHTML.trim());
    }
    var type = localStorage.getItem("type");
    if (type === "Visiteur") {
        html.style.cssText = "--couleurUI: #337ab7; --couleurUIHover: #1297e0; --couleurUIDeconCadre: #d9edf7; --couleurUIDeconBorder: #0067b0; --couleurUIDeconText: #31708f;";
    } else if (type === "Comptable") {
        html.style.cssText = "--couleurUI: #F39C12; --couleurUIHover: #F5AB35; --couleurUIDeconCadre: #FDE3A7; --couleurUIDeconBorder: #f1892d; --couleurUIDeconText: #c86400;"; 
    } else {
        alert("BUG!!");
    }
};