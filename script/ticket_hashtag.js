"use strict";

const addHashtagButton = document.querySelector("#add-hashtag");
const hashtagSelector = document.querySelector("#hashtag-selector");
const hashtagContainer = document.querySelector("#hashtag-container");

function addHashtag(hashtag) {
    const hashtagElement = document.createElement("span");
    hashtagElement.classList.add("hashtag");
    hashtagElement.innerHTML = hashtag;

    // add a event listener to remove the hashtag
    hashtagElement.addEventListener("click", () => {
        hashtagElement.remove();
    });

    hashtagContainer.appendChild(hashtagElement);
}

addHashtagButton.addEventListener("click", () => {
    const hashtag = hashtagSelector.value;
    if (hashtag) {
        addHashtag(hashtag);
    }
});
