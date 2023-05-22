"use strict";

const addHashtagButton = document.querySelector("#add-hashtag");
const hashtagSelector = document.querySelector("#hashtag-selector");
const hashtagContainer = document.querySelector("#hashtag-container");

function addHashtag(hashtag) {
    if (hashtag === "--Select Hashtag--") {
        return;
      }
    const hashtagElement = document.createElement("span");
    hashtagElement.classList.add("hashtag");
    hashtagElement.innerHTML = hashtag;

    // add a event listener to remove the hashtag
    hashtagElement.addEventListener("click", () => {
        hashtagElement.remove();
    });

    hashtagContainer.appendChild(hashtagElement);
}

function toggleBorder() {
    const selectedOption = hashtagSelector.value;
    if (selectedOption !== "none") {
      hashtagContainer.classList.add("has-elements");
    } else {
      hashtagContainer.classList.remove("has-elements");
    }
}

addHashtagButton.addEventListener("click", () => {
    const hashtag = hashtagSelector.value;
    if (hashtag && hashtag !== "--Select Hashtag--") {
        addHashtag(hashtag);
        toggleBorder();
    }
});
