// Fallback hoverIntent for admin-bar
window.hoverIntent = window.hoverIntent || function(elem, over, out) {
    let timeout;
    const delay = 200;
    elem.addEventListener("mouseenter", function() {
        clearTimeout(timeout);
        over && over.call(this);
    });
    elem.addEventListener("mouseleave", function() {
        timeout = setTimeout(() => out && out.call(this), delay);
    });
};
console.log("✅ hoverIntent-fix активирован");