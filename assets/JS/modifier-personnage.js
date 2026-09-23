document.addEventListener("DOMContentLoaded", function () {

    const tabs = document.querySelectorAll(".creator-tab");
    const contents = document.querySelectorAll(
        ".stardoll-panel > .tab-content:not(:last-child)"
    );

    tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {

            tabs.forEach(function (item) {
                item.classList.remove("active");
            });

            contents.forEach(function (content) {
                content.classList.remove("active");
            });

            tab.classList.add("active");

            const target = document.getElementById(
                "tab-" + tab.dataset.tab
            );

            if (target) {
                target.classList.add("active");
            }
        });
    });


    const portraitFrame = document.getElementById("portraitFrame");
    const mainHeroine = document.getElementById("mainHeroine");

    const hairInput = document.getElementById("couleur_cheveux");
    const eyeInput = document.getElementById("couleur_yeux");

    const hairPreview = document.getElementById("hairColorPreview");
    const eyePreview = document.getElementById("eyeColorPreview");


    const modelImages = {
        guerriere: "assets/images/creator/heroine-guerriere.png",
        mage: "assets/images/creator/modele-mage.png",
        archere: "assets/images/creator/modele-archere.png"
    };


    function selectedValue(name) {

        const selected = document.querySelector(
            'input[name="' + name + '"]:checked'
        );

        return selected ? selected.value : "";
    }


    function removePrefix(prefix) {

        if (!portraitFrame) {
            return;
        }

        Array.from(portraitFrame.classList).forEach(
            function (className) {

                if (className.indexOf(prefix) === 0) {
                    portraitFrame.classList.remove(className);
                }

            }
        );
    }


    function updateModel() {

        if (!mainHeroine) {
            return;
        }

        const model =
            selectedValue("modele_visuel") || "guerriere";

        if (modelImages[model]) {
            mainHeroine.src = modelImages[model];
        }
    }


    function updateFace() {

        if (!portraitFrame) {
            return;
        }

        removePrefix("face-");

        const face = selectedValue("visage");

        if (face === "Rond") {

            portraitFrame.classList.add("face-rond");

        } else if (face === "Allongé") {

            portraitFrame.classList.add("face-allonge");

        } else {

            portraitFrame.classList.add("face-normal");
        }
    }


    function updateHair() {

        if (!portraitFrame) {
            return;
        }

        removePrefix("hair-");

        const hair = selectedValue("coiffure");

        if (hair === "Court") {

            portraitFrame.classList.add("hair-court");

        } else if (hair === "Tresse") {

            portraitFrame.classList.add("hair-tresse");

        } else {

            portraitFrame.classList.add("hair-long");
        }
    }


    function updateEyes() {

        if (!portraitFrame) {
            return;
        }

        removePrefix("eyes-");

        const eyes = selectedValue("forme_yeux");

        if (eyes === "Fins") {

            portraitFrame.classList.add("eyes-fins");

        } else if (eyes === "Etroits") {

            portraitFrame.classList.add("eyes-etroits");

        } else {

            portraitFrame.classList.add("eyes-ronds");
        }
    }


    function refreshColors() {

        if (hairInput && hairPreview) {
            hairPreview.style.background =
                hairInput.value;
        }

        if (eyeInput && eyePreview) {
            eyePreview.style.background =
                eyeInput.value;
        }
    }


    document
        .querySelectorAll('input[name="modele_visuel"]')
        .forEach(function (option) {

            option.addEventListener(
                "change",
                updateModel
            );

        });


    document
        .querySelectorAll('input[name="visage"]')
        .forEach(function (option) {

            option.addEventListener(
                "change",
                updateFace
            );

        });


    document
        .querySelectorAll('input[name="coiffure"]')
        .forEach(function (option) {

            option.addEventListener(
                "change",
                updateHair
            );

        });


    document
        .querySelectorAll('input[name="forme_yeux"]')
        .forEach(function (option) {

            option.addEventListener(
                "change",
                updateEyes
            );

        });


    if (hairInput) {
        hairInput.addEventListener(
            "input",
            refreshColors
        );
    }


    if (eyeInput) {
        eyeInput.addEventListener(
            "input",
            refreshColors
        );
    }


    updateModel();
    updateFace();
    updateHair();
    updateEyes();
    refreshColors();

});