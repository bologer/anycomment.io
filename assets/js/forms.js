const switches = document.querySelectorAll(".switch > span");


/*
## Enable draggable switching.
*/
let startX;
let startY;
let ghostNode = {};

for (let i = 0; i < switches.length; i++) {

    const switchEl = switches[i];

    switchEl.draggable = true;

    ['dragstart', 'touchstart'].forEach(function(e) {
        switchEl.addEventListener(e, onDragStart);
    });

    ['dragover', 'touchmove'].forEach(function(e) {
        switchEl.addEventListener(e, onDragOver);
    });

    ['dragend', 'touchend'].forEach(function(e) {
        switchEl.addEventListener(e, onDragEnd);
    });

}

function onDragStart(evt) {
    evt = evt || window.event;
    const x = evt.pageX,
        y = evt.pageY;
    startX = x;
    startY = y;

    if (evt.type == 'dragstart') {
        evt.dataTransfer.setData('text/plain', '');

        /*
        ## Remove drag preview.
        ## Safari: node must have content (&nbsp;)
        ## otherwise dragend event will fire immediately.
        */
        ghostNode = document.createElement("div");
        ghostNode.innerHTML = "&nbsp;";
        ghostNode.style.visibility = 'hidden';
        document.body.appendChild(ghostNode);
        evt.dataTransfer.setDragImage(ghostNode, 0, 0);
    }
}

function onDragOver(evt) {
    evt = evt || window.event;

    evt.preventDefault();

    const x = evt.pageX,
        y = evt.pageY;

    const switchRect = evt.currentTarget.getBoundingClientRect();
    const checkbox = evt.currentTarget.previousElementSibling;

    if (checkbox.disabled) {
        return;
    }

    if (checkbox.checked && x <= startX - 10) {
        checkbox.checked = false;
    } else if (!checkbox.checked && x >= startX + 10) {
        checkbox.checked = true;
    }
}

function onDragEnd(evt) {
    // ## Remove ghostNode from DOM.
    if (evt.type == 'dragend') {
        ghostNode.parentNode.removeChild(ghostNode);
    }

    return;
}