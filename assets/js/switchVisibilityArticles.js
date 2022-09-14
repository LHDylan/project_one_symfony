import axios from "axios";

console.error("1");
let switchs = document.querySelectorAll("[data-switch-active-article]");
console.error("2");
if (switchs) {
    console.error("3");
    switchs.forEach((element) => {
        element.addEventListener("change", () => {
            console.error("change");
            let articleId = element.value;
            axios.get(`/admin/article/switch/${articleId}`);
        });
    });
}
