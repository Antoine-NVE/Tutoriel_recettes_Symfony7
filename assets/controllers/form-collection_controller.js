import { Controller } from "@hotwired/stimulus";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        this.index = this.element.childElementCount;
        const btn = document.createElement("button");
        btn.setAttribute("class", "btn btn-secondary");
        btn.innerText = "Ajouter un élément";
        btn.setAttribute("type", "button");
        btn.addEventListener("click", this.addElement);
        this.element.childNodes.forEach(this.addDeleteButton);
        this.element.append(btn);
    }

    addElement = (e) => {
        e.preventDefault();
        const element = document
            .createRange()
            .createContextualFragment(
                this.element.dataset["prototype"].replaceAll(
                    "__name__",
                    this.index
                )
            ).firstElementChild;
        this.addDeleteButton(element);
        this.index++;
        e.currentTarget.insertAdjacentElement("beforebegin", element);
    };

    addDeleteButton = (item) => {
        const btn = document.createElement("button");
        btn.setAttribute("class", "btn btn-secondary");
        btn.innerText = "Supprimer";
        btn.setAttribute("type", "button");
        item.append(btn);
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            item.remove();
        });
    };
}
