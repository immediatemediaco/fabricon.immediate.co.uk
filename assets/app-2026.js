import './styles/app-2026.scss';

import Modal from "./components/modal";

/**
 * Sticky navbar
 */
const nav = document.querySelector('.nav');
const stickyOffset = 100;
window.addEventListener('scroll', () => {
    window.pageYOffset > stickyOffset ? nav.classList.add('is-sticky') : nav.classList.remove('is-sticky');
});
