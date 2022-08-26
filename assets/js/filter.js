import { debounce } from "lodash";
import { Flipper, spring } from "flip-toolkit";

/**
 * Class for the filter posts bundle
 *
 * @property {HTMLElement} pagination - the element with the button(s) for pagination
 * @property {HTMLElement} content - the element with the main content
 * @property {HTMLElement} sorting - the element with the button(s) for sorting
 * @property {HTMLElement} count - the element with the number of posts on the content
 * @property {HTMLFormElement} form - the form for the search
 * @property {number} page - the elementof the page search
 */
export default class filter {
    /**
     * Constructor of the filter class
     * @param {HTMLElement} element
     * @returns
     */
    constructor(element) {
        if (element == null) {
            return;
        }

        this.pagination = element.querySelector(".js-filter-pagination");
        this.content = element.querySelector(".js-filter-content");
        this.sorting = element.querySelector(".js-filter-sorting");
        this.count = element.querySelector(".js-filter-count");
        this.form = element.querySelector(".js-filter-form");
        this.page = parseInt(
            new URLSearchParams(window.location.search).get("page") || 1
        );
        this.scrollContent = this.page == 1;

        this.bindEvents();
    }

    /**
     * add the action to the elements of the filter bundle
     */
    bindEvents() {
        const linkClickListener = (e) => {
            if (e.target.tagName === "A") {
                e.preventDefault();
                this.loadUrl(e.target.getAttribute("href"));
            }
        };

        if (this.scrollContent) {
            this.pagination.innerHTML =
                '<button class="btn btn-primary btn-show-more mt-2">Show More</button>';
            this.pagination
                .querySelector("button")
                .addEventListener("click", this.loadMore.bind(this));
        } else {
            this.pagination.addEventListener("click", linkClickListener);
        }

        this.sorting.addEventListener("click", (element) => {
            linkClickListener(element);
            this.page = 1;
        });

        this.form.querySelectorAll("input[type=checkbox]").forEach((input) => {
            input.addEventListener(
                "change",
                debounce(this.loadForm.bind(this), 666)
            );
        });

        this.form
            .querySelector("input[type=text]")
            .addEventListener("keyup", debounce(this.loadForm.bind(this), 666));
    }

    /**
     * load the url ajax
     * @param {URL} URL
     */
    async loadUrl(url, append = false) {
        this.showLoader();
        const params = new URLSearchParams(url.split("?")[1] || "");
        params.set("ajax", 1);

        // console.error(params.toString(), url);

        const response = await fetch(
            `${url.split("?")[0]}?${params.toString()}`,
            {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            }
        );

        // console.error(await response.json());

        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();

            this.sorting.innerHTML = data.sorting;
            this.count.innerHTML = data.count;

            if (!this.scrollContent) {
                this.pagination.innerHTML = data.pagination;
            } else if (this.page === data.pages) {
                this.pagination.style.display = "none";
            } else {
                this.pagination.style.display = null;
            }

            this.flipContent(data.content, append);

            params.delete("ajax");
            history.replaceState(
                {},
                "",
                `${url.split("?")[0]}?${params.toString()}`
            );
        } else {
            console.error(response);
        }

        this.hideLoader();
    }

    /**
     *get the form and send request ajax with informations
     */
    async loadForm() {
        this.page = 1;
        const data = new FormData(this.form);
        const url = new URL(
            this.form.getAttribute("action") || window.location.href
        );
        const params = new URLSearchParams();

        data.forEach((value, key) => {
            params.append(key, value);
        });

        // console.warn(url.pathname);
        return this.loadUrl(`${url.pathname}?${params.toString()}`);
    }

    /**
     *
     * @param {HTMLElement} button
     */
    async loadMore(button) {
        button.target.setAttribute("disabled", true);
        this.page++;
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        params.set("page", this.page);

        await this.loadUrl(`${url.pathname}?${params.toString()}`, true);
        button.target.removeAttribute("disabled");
    }

    flipContent(content, append) {
        const springName = "veryGentle";
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: "stiff",
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0],
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete,
            });
        };
        const appearSpring = function (element, index) {
            spring({
                config: springName,
                values: {
                    translateY: [0, 20],
                    opacity: [0, 1],
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 10,
            });
        };

        const flipper = new Flipper({
            element: this.content,
        });

        let cards = this.content.children;
        for (let card of cards) {
            flipper.addFlipped({
                element: card,
                flipId: card.id,
                spring: springName,
                shouldFlip: false,
                onExit: exitSpring,
            });
        }

        flipper.recordBeforeUpdate();

        if (append) {
            this.content.innerHTML += content;
        } else {
            this.content.innerHTML = content;
        }

        cards = this.content.children;
        for (let card of cards) {
            flipper.addFlipped({
                element: card,
                flipId: card.id,
                spring: springName,
                onAppear: appearSpring,
            });
        }

        flipper.update();
    }

    /**
     * show the loader and disable form wait response
     */
    showLoader() {
        const loader = this.form.querySelector(".js-loading");

        if (loader === null) {
            return;
        }

        this.form.classList.add("is-loading");
        loader.setAttribute("aria-hidden", false);
        loader.style.display = null;
    }

    /**
     * hide the loader and disable form wait response
     */
    hideLoader() {
        const loader = this.form.querySelector(".js-loading");

        if (loader === null) {
            return;
        }

        this.form.classList.remove("is-loading");
        loader.setAttribute("aria-hidden", true);
        loader.style.display = "none";
    }
}
