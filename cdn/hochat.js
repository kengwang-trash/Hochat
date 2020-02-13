function Hochat(key, container, path = undefined) {
    window.addEventListener("message", function (event) {
        if (event.origin === "https://api.hochat.space") {
            document.getElementById("hochat_iframe").style.height = (event.data + 30) + "px";
        }
    }, false);
    let r;
    if (path === undefined) r = ""; else r = "&path=" + path;
    let c = document.querySelector(container);
    if (c === undefined) {
        console.log("Hochat: Cannot find container " + container);
        return;
    }
    c.innerHTML = "<iframe id=\"hochat_iframe\" src=\"https://api.hochat.space/comment.php?key=" + key + r + "\" style=\"border: none; width: 100%\"></iframe>\n";
    console.log("Appended Hochat to " + container);
}