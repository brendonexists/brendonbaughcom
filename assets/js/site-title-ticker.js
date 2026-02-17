(() => {
  const descriptors = document.querySelectorAll('.site-title__descriptorInner');
  if (!descriptors.length) {
    return;
  }

  const phrases = ['exists', 'is real', 'is human', 'builds', 'ships', 'learns', 'logs'];
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const reduceMotion = prefersReducedMotion.matches;
  const TOP_DURATION = 180;
  const INTERVAL = 3600;

  const maxLength = phrases.reduce((max, phrase) => Math.max(max, phrase.length), 0);

  const buildCells = (descriptor) => {
    descriptor.innerHTML = '';

    const cells = [];
    for (let i = 0; i < maxLength; i += 1) {
      const cell = document.createElement('span');
      cell.className = 'site-title__flapCell';
      cell.innerHTML =
        '<span class="site-title__flap site-title__flap--top">' +
        '<span class="site-title__flapText"></span>' +
        '</span>' +
        '<span class="site-title__flap site-title__flap--bottom">' +
        '<span class="site-title__flapText"></span>' +
        '</span>';

      const topText = cell.querySelector('.site-title__flap--top .site-title__flapText');
      const bottomText = cell.querySelector('.site-title__flap--bottom .site-title__flapText');
      cells.push({ topText, bottomText });
      descriptor.appendChild(cell);
    }

    const outer = descriptor.closest('.site-title__descriptor');
    if (outer) {
      outer.style.setProperty('--descriptor-ch', String(maxLength));
    }

    return cells;
  };

  const setPhrase = (cells, phrase) => {
    const padded = phrase.padEnd(maxLength, ' ');
    for (let i = 0; i < cells.length; i += 1) {
      const char = padded[i] === ' ' ? '\u00A0' : padded[i];
      cells[i].topText.textContent = char;
      cells[i].bottomText.textContent = char;
    }
  };

  const startTicker = (descriptor) => {
    let index = 0;
    const cells = buildCells(descriptor);
    setPhrase(cells, phrases[index]);

    if (reduceMotion) {
      return;
    }

    window.setInterval(() => {
      descriptor.classList.add('is-snap', 'is-hide-bottom');
      descriptor.classList.remove('is-top-flip');

      requestAnimationFrame(() => {
        descriptor.classList.remove('is-snap');
        descriptor.classList.add('is-top-flip');

        window.setTimeout(() => {
          index = (index + 1) % phrases.length;
          setPhrase(cells, phrases[index]);

          descriptor.classList.add('is-snap');
          descriptor.classList.remove('is-top-flip');

          requestAnimationFrame(() => {
            descriptor.classList.remove('is-snap', 'is-hide-bottom');
          });
        }, TOP_DURATION + 20);
      });
    }, INTERVAL);
  };

  descriptors.forEach(startTicker);
})();
