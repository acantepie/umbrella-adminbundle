$(document).on('click', '[ui-nav] a', function (e) {
    let $this = $(e.target), $active, $li;
    $this.is('a') || ($this = $this.closest('a'));

    $li = $this.parent();
    $active = $li.siblings(".active");
    $li.toggleClass('active');
    $active.removeClass('active');


    let $subnav = $this.next('ul');
    if ($subnav.length > 0) {
        if ($this.next('ul').visibleInScroll().top > 400) {
            $this.closest('.hide-scroll').animate({
                scrollTop: 1000
            }, 600, 'easeInOutExpo');
        }
    }

});


/**
 * Determines whether an element is contained in the visible viewport of its scrolled parents
 *
 * @param goDeep If false or undefined, just check the immediate scroll parent.
 *               If truthy, check all scroll parents up to the Document.
 * @return Object A bounding box representing the element's visible portion:
 *         left: left edge of the visible portion of the element relative to the screen
 *         top: top edge of the visible portion of the element relative to the screen
 *         right: right edge of the visible portion of the element relative to the screen
 *         bottom: bottom edge of the visible portion of the element relative to the screen
 *         width: width of the element
 *         height: height of the element
 *         isVisible: whether any part of the element can be seen
 *         isContained: whether all of the element can be seen
 *         visibleWidth: width of the visible portion of the element
 *         visibleHeight: width of the visible portion of the element
 */

jQuery.fn.visibleInScroll = function (goDeep) {
    var parent = $(this[0]).scrollParent()[0],
        elRect = this[0].getBoundingClientRect(),
        rects = [ parent.getBoundingClientRect() ];
    elRect = {
        left: elRect.left,
        top: elRect.top,
        right: elRect.right,
        bottom: elRect.bottom,
        width: elRect.width,
        height: elRect.height,
        visibleWidth: elRect.width,
        visibleHeight: elRect.height,
        isVisible: true,
        isContained: true
    };
    var elWidth = elRect.width,
        elHeight = elRect.height;
    if (parent === this[0].ownerDocument) {
        return elRect;
    }

    while (parent !== this[0].ownerDocument && parent !== null) {
        if (parent.scrollWidth > parent.clientWidth || parent.scrollHeight > parent.clientHeight) {
            rects.push(parent.getBoundingClientRect());
        }
        if (rects.length && goDeep) { break; }
        parent = $(parent).scrollParent()[0];
    }
    if (!goDeep) {
        rects.length = 1;
    }
    for (var i = 0; i < rects.length; i += 1) {
        var rect = rects[i];
        elRect.left = Math.max(elRect.left, rect.left);
        elRect.top = Math.max(elRect.top, rect.top);
        elRect.right = Math.min(elRect.right, rect.right);
        elRect.bottom = Math.min(elRect.bottom, rect.bottom);
    }
    elRect.visibleWidth = Math.max(0, elRect.right - elRect.left);
    elRect.visibleHeight = elRect.visibleWidth && Math.max(0, elRect.bottom - elRect.top);
    if (!elRect.visibleHeight) { elRect.visibleWidth = 0; }
    elRect.isVisible = elRect.visibleWidth > 0 && elRect.visibleHeight > 0;
    elRect.isContained = elRect.visibleWidth === elRect.width && elRect.visibleHeight === elRect.height;
    return elRect;
};