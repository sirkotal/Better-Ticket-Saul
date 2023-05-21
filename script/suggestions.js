const suggestions = ["#hello", "#tuco"];

const hashtagInput = document.getElementById('hashtag');
const hashContainer = document.querySelector('.hash-container');

hashtagInput.addEventListener('input', () => {
  const input = hashtagInput.value.trim(); // delete spaces (leading and trailing)

  hashContainer.innerHTML = ''; // clear previous suggestions

  if (input === '') {
    return;
  }

  const filteredSuggestions = suggestions.filter(suggestion =>
    suggestion.startsWith(input)
  );

  // Add new suggestion elements to the hash container
  filteredSuggestions.forEach(suggestion => {
    const suggestionElement = document.createElement('div');
    suggestionElement.textContent = suggestion;
    hashContainer.appendChild(suggestionElement);
  });
});

hashtagInput.addEventListener('keydown', event => {
  if (event.key === ' ' || event.key === 'Enter') {
    hashContainer.innerHTML = ''; // clear suggestions
  }
});
