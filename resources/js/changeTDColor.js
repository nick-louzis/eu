document.addEventListener('DOMContentLoaded', (event) => {
    applyProjectListColoring()

    // window.livewire.afterDomUpdate(() => {
    //     alert("hahahaha")
    // })

    console.log(Livewire.hook)

    Livewire.hook('afterDomUpdate', () => {
        console.log(Livewire.hook)
    })


    function applyProjectListColoring() {
        const trs = Array.from(document.querySelectorAll("tr")).slice(1);

        trs.forEach((tr) => {
            changeProjectsListItemColor(tr);
        });
    }

    function isCloseToWhite(rgb) {
        // Define a threshold for "close to white" (e.g., R, G, and B values > 240)
        const threshold = 240;
        rgb = rgb.replaceAll(" ", "").split(",")
        return rgb.every(val => val >= threshold);
    }

    function changeProjectsListItemColor(item) {
        const colorItem = item.querySelector(".fi-ta-color-item");

        if(colorItem) {
            const color = window.getComputedStyle(colorItem).getPropertyValue('background-color');
            const rgbValues = color.match(/\d{1,3}(?:,\s*\d{1,3})*/)[0];

            if(isCloseToWhite(rgbValues)) return;


            const rgbaNeutral = `rgba(${rgbValues}, 0.04)`;
            const rgbaAction = `rgba(${rgbValues}, 0.09)`;
            item.style.backgroundColor = rgbaNeutral;
            item.style.setProperty('--hover-bg-color', rgbaAction);
            colorItem.classList.add("non-neutral-clr");
        }
    }
});
