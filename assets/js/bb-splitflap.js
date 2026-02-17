(() => {
  const boards = document.querySelectorAll('.bb-flap');
  if (!boards.length) {
    return;
  }

  const WIDTH = 12;
  const PHRASES = ['exists', 'is real', 'is human', 'builds', 'ships', 'learns', 'logs'];
  const INTERVAL = 3600;
  const FLIP_DURATION = 280;
  const STAGGER = 35;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const reduceMotion = prefersReducedMotion.matches;

  const pad = (value) => {
    const trimmed = value.slice(0, WIDTH);
    const totalPad = WIDTH - trimmed.length;
    const leftPad = Math.floor(totalPad / 2);
    const rightPad = totalPad - leftPad;
    return `${' '.repeat(leftPad)}${trimmed}${' '.repeat(rightPad)}`;
  };
  const toDisplayChar = (char) => (char === ' ' ? '\u00A0' : char);

  const buildFlip = () => {
    const flip = document.createElement('span');
    flip.className = 'bb-flip';
    flip.innerHTML =
      '<span class="bb-flip-holder">' +
      '<span class="bb-flip-face bb-flip-face--top"><span class="bb-flip-char"></span></span>' +
      '<span class="bb-flip-face bb-flip-face--bottom"><span class="bb-flip-char"></span></span>' +
      '<span class="bb-flip-face bb-flip-face--flip-top"><span class="bb-flip-char"></span></span>' +
      '<span class="bb-flip-face bb-flip-face--flip-bottom"><span class="bb-flip-char"></span></span>' +
      '</span>';

    const flipTopFace = flip.querySelector('.bb-flip-face--flip-top');
    const flipBottomFace = flip.querySelector('.bb-flip-face--flip-bottom');
    const faces = flip.querySelectorAll('.bb-flip-char');
    return {
      el: flip,
      top: faces[0],
      bottom: faces[1],
      flipTop: faces[2],
      flipBottom: faces[3],
      flipTopFace,
      flipBottomFace,
      char: '\u00A0',
      isFlipping: false,
    };
  };

  const setChar = (flip, char) => {
    const displayChar = toDisplayChar(char);
    flip.char = displayChar;
    flip.top.textContent = displayChar;
    flip.bottom.textContent = displayChar;
    flip.flipTop.textContent = displayChar;
    flip.flipBottom.textContent = displayChar;
  };

  const flipTo = (flip, nextChar, delay) => {
    if (flip.isFlipping) {
      return;
    }

    flip.isFlipping = true;
    const displayChar = toDisplayChar(nextChar);
    flip.flipTop.textContent = flip.char;
    flip.flipBottom.textContent = displayChar;

    window.setTimeout(() => {
      flip.el.classList.add('is-flipping');

      const onDone = () => {
        flip.el.classList.remove('is-flipping');
        setChar(flip, displayChar);
        flip.isFlipping = false;
      };

      flip.flipBottomFace.addEventListener('transitionend', onDone, { once: true });

      window.setTimeout(() => {
        if (flip.isFlipping) {
          onDone();
        }
      }, FLIP_DURATION + 80);
    }, delay);
  };

  const buildBoard = (board) => {
    board.style.setProperty('--bb-width', String(WIDTH));
    board.setAttribute('role', 'presentation');
    const flips = [];

    for (let i = 0; i < WIDTH; i += 1) {
      const flip = buildFlip();
      board.appendChild(flip.el);
      flips.push(flip);
    }

    return flips;
  };

  boards.forEach((board) => {
    const flips = buildBoard(board);
    let phraseIndex = 0;
    let current = pad(PHRASES[phraseIndex]);

    for (let i = 0; i < WIDTH; i += 1) {
      setChar(flips[i], current[i]);
    }

    if (reduceMotion) {
      return;
    }

    window.setInterval(() => {
      phraseIndex = (phraseIndex + 1) % PHRASES.length;
      const next = pad(PHRASES[phraseIndex]);

      let staggerIndex = 0;
      for (let i = 0; i < WIDTH; i += 1) {
        if (current[i] !== next[i]) {
          flipTo(flips[i], next[i], staggerIndex * STAGGER);
          staggerIndex += 1;
        }
      }

      current = next;
    }, INTERVAL);
  });
})();
