/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// Ajout des images statiques pour Webpack
import bToVHS from './images/bToVHS.png';

document.addEventListener('DOMContentLoaded', () => {
    const burgerBtn = document.getElementById('burger-btn');
    const closeBtn = document.getElementById('close-btn');
    const menu = document.getElementById('mobile-menu');
  
    burgerBtn.addEventListener('click', () => {
      menu.classList.remove('hidden');
      closeBtn.classList.remove('hidden');
      burgerBtn.classList.add('hidden');
    });
  
    closeBtn.addEventListener('click', () => {
      menu.classList.add('hidden');
      closeBtn.classList.add('hidden');
      burgerBtn.classList.remove('hidden');
    });
  });

  