const suggestions = ["#hello", "#tuco"];

function showHashtagSuggestions(suggestions) {
    const hashtagInput = document.getElementById('hashtag');
    const hashContainer = document.querySelector('.hash-container');

    let hashtagsList = [];
    let isShowing = false;

    hashtagInput.addEventListener('input', () => {
    const input = hashtagInput.value.trim(); // delete spaces (leading and trailing)

    if (input === '') {    // no/empty input
        hashContainer.innerHTML = '';
        hashtagsList = [];
        isShowing = false;
        return;
    }

    const hashtags = input.match(/#[^\s#]*/g) || []; // hashtag extraction

    if (hashtags.length > 0) {  // if it is an hashtag -> starts with #, store
        hashtagsList = hashtags;
    } else {
        hashtagsList = [];
    }

    hashContainer.innerHTML = ''; // clear previous suggestions

    hashtagsList.forEach(hashtag => { // add some new suggestions
        const filteredSuggestions = suggestions.filter(suggestion =>
        suggestion.startsWith(hashtag)
        );

        const remainingSuggestions = filteredSuggestions.filter( // remove the input from suggestions
        suggestion => suggestion !== hashtag
        );

        remainingSuggestions.forEach(suggestion => {
        const suggestionElement = document.createElement('div');
        suggestionElement.textContent = suggestion;
        hashContainer.appendChild(suggestionElement);
        });
    });

    isShowing = true;
    });

    hashtagInput.addEventListener('keydown', event => {
    if (event.key === ' ' || event.key === 'Enter') {
        if (hashtagsList.length === 0) {  // clear suggestions if the hashtags list is empty
        hashContainer.innerHTML = '';
        isShowing = false;
        }
    }
    });
}

showHashtagSuggestions(suggestions);