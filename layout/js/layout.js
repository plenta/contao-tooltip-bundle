import "../scss/layout.scss";

document.addEventListener('DOMContentLoaded', function () {
    let tooltips = document.querySelectorAll('.plenta-tooltip');

    tooltips.forEach(function (tooltip) {
        tooltip.addEventListener('click', function () {
            let modal = document.querySelector('#plenta-modal-' + tooltip.dataset.id);
            if (!modal) {
                fetch('/_plenta/tooltip/' + tooltip.dataset.id).then(r => r.json()).then(r => {
                    modal = document.createElement('dialog');
                    modal.id = 'plenta-modal-' + tooltip.dataset.id;
                    modal.innerHTML = r.buffer;
                    let button = document.createElement('button');
                    button.innerText = r.buttonText;
                    button.addEventListener('click', function () {
                        modal.close();
                    })
                    modal.append(button);
                    document.querySelector('body').append(modal);
                    modal.showModal();
                })
            } else {
                modal.showModal();
            }
        })
    })
})