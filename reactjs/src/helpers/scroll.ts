/**
 * Equivalence for scrollTop() in jQuery.
 *
 * @returns {number}
 */
export function scrollTop(): number {
    // Firefox, Chrome, Opera, Safari
    if (window.pageYOffset) {
        return window.pageYOffset;
    }
    // Internet Explorer 6 - standards mode
    if (document.documentElement && document.documentElement.scrollTop) {
        return document.documentElement.scrollTop;
    }
    // Internet Explorer 6, 7 and 8
    if (document.body.scrollTop) {
        return document.body.scrollTop;
    }
    return 0;
}

export function scrollLeft(id): number {
    const elm: HTMLElement | null = document.getElementById(id);

    if (elm === null) {
        return 0;
    }

    let y: number = elm.offsetTop;
    let node: HTMLElement = elm;
    while (node.offsetParent && node.offsetParent !== document.body) {
        node = node.offsetParent;
        y += node.offsetTop;
    }

    return y;
}

/**
 * Move screen to specific element.
 *
 * @param id
 * @param callback
 */
export function moveToElement(id: string, callback?: () => void) {
    let startY: number = scrollTop();
    let stopY: number = scrollLeft(id);
    let distance: number = stopY > startY ? stopY - startY : startY - stopY;
    if (distance < 100) {
        window.scrollTo(0, stopY);
        return;
    }
    let speed: number = Math.round(distance / 100);
    if (speed >= 20) speed = 20;
    let step = Math.round(distance / 25);
    let leapY = stopY > startY ? startY + step : startY - step;
    let timer = 0;
    if (stopY > startY) {
        for (let i = startY; i < stopY; i += step) {
            setTimeout('window.scrollTo(0, ' + leapY + ')', timer * speed);
            leapY += step;
            if (leapY > stopY) leapY = stopY;
            timer++;
        }
        return;
    }
    for (let i = startY; i > stopY; i -= step) {
        setTimeout('window.scrollTo(0, ' + leapY + ')', timer * speed);
        leapY -= step;
        if (leapY < stopY) leapY = stopY;
        timer++;
    }

    callback();
}
