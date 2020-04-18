// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
// Get the <span> element that closes the modal
var span2 = document.getElementsByClassName("close2")[0];
// When the user clicks on the button, open the modal
btn.onclick = function () {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function () {
    modal.style.display = "none";
}
// When the user clicks on <span2> (x), close the modal
span2.onclick = function () {
    modal.style.display = "none";
    
// When the user clicks anywhere outside of the modal, close it
// window.onclick = function (event) {
//     if (event.target == modal) {
//         modal.style.display = "none";
//     }
// }
    
// function ESCclose(evt) {
//     if (evt.keyCode == 27)
//         window.close();
// }
// window.onkeyup = function (event) {
//     if (event.keyCode == 27) {
//         window.close();
//     }
}
