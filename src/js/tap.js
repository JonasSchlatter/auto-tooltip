/*jslint browser */
(function () {
    "use strict";

    var tooltips = document.getElementsByClassName("menu")[0].getElementsByTagName("li");

    function click(targetElement) {
        if (active) {
            active.classList.remove("active");
        }

        active = targetElement;
        active.classList.add("active");
    }

    function addHandler(i) {
        return function () {
            click(menu[i]);
        };
    }

    var i = menu.length;
    while (i) {
        i -= 1;
        menu[i].addEventListener("touchstart", addHandler(i));
    }

}());
