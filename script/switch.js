const images = ['/assets/boss1.jpg', '/assets/boss2.jpg', '/assets/boss3.jpg', '/assets/boss4.jpg', '/assets/boss5.jpg', '/assets/boss6.jpg'];
const container = document.querySelector('.saul-container');
container.style.transition = 'background-image 3s ease-in-out';

let nextIndex = 1;

const preloadImages = images.map((src) => {
    const img = new Image();
    img.src = src;
    return img;
  });

setInterval(() => {
  const nextImage = preloadImages[nextIndex];
  setTimeout(() => {
    container.style.backgroundImage = `url(${nextImage.src})`;
    //container.style.opacity = 1;
    nextIndex++;
    if (nextIndex === images.length) {
        nextIndex = 0;
    }
  }, 1000);
}, 5000);