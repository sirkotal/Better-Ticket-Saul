const loginStatus = document.getElementById('login-status');

function playMP3() {
  if (loginStatusElement.dataset.isLoggedIn === 'true' && !alreadyPlayed) {
    audioElement.play();
    sessionStorage.setItem('alreadyPlayed', 'true');
  }
}

playMP3();