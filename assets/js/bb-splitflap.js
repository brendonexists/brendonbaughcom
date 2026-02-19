(() => {
  const boards = document.querySelectorAll('[data-bb-splitflap]');
  if (!boards.length) {
    return;
  }

  const DEFAULT_WIDTH = 12;
  const PHRASES = ['exists', 'is real', 'is human', 'builds', 'ships', 'learns', 'logs'];
  const INTERVAL = 3600;
  const FLIP_DURATION = 280;
  const STAGGER = 35;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const reduceMotion = prefersReducedMotion.matches;
  const states = new Map();

  const pad = (value, width) => {
    const trimmed = value.slice(0, width);
    const totalPad = width - trimmed.length;
    const leftPad = Math.floor(totalPad / 2);
    const rightPad = totalPad - leftPad;
    return `${' '.repeat(leftPad)}${trimmed}${' '.repeat(rightPad)}`;
  };
  const toDisplayChar = (char) => (char === ' ' ? '\u00A0' : char);

  const getBoardWidth = (board) => {
    const value = Number.parseInt(board.dataset.bbWidth || '', 10);
    if (Number.isFinite(value) && value > 0) {
      return value;
    }
    return DEFAULT_WIDTH;
  };

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

  const buildBoard = (board, width) => {
    board.style.setProperty('--bb-width', String(width));
    board.setAttribute('role', 'presentation');
    board.innerHTML = '';
    const flips = [];

    for (let i = 0; i < width; i += 1) {
      const flip = buildFlip();
      board.appendChild(flip.el);
      flips.push(flip);
    }

    return flips;
  };

  const resetBoard = (state) => {
    for (let i = 0; i < state.width; i += 1) {
      const flip = state.flips[i];
      flip.el.classList.remove('is-flipping');
      flip.isFlipping = false;
      setChar(flip, state.current[i]);
    }
  };

  const tickBoard = (state) => {
    state.phraseIndex = (state.phraseIndex + 1) % PHRASES.length;
    const next = pad(PHRASES[state.phraseIndex], state.width);

    let staggerIndex = 0;
    for (let i = 0; i < state.width; i += 1) {
      if (state.current[i] !== next[i]) {
        flipTo(state.flips[i], next[i], staggerIndex * STAGGER);
        staggerIndex += 1;
      }
    }

    state.current = next;
  };

  const initBoard = (board) => {
    if (board.dataset.bbSplitflapInit === 'true' && states.has(board)) {
      return;
    }

    const width = getBoardWidth(board);
    const flips = buildBoard(board, width);
    const phraseIndex = reduceMotion ? 0 : Math.floor(Math.random() * PHRASES.length);
    const current = pad(PHRASES[phraseIndex], width);

    for (let i = 0; i < width; i += 1) {
      setChar(flips[i], current[i]);
    }

    const state = {
      board,
      width,
      flips,
      phraseIndex,
      current,
      intervalId: null,
    };

    states.set(board, state);
    board.dataset.bbSplitflapInit = 'true';

    if (reduceMotion) {
      return;
    }

    state.intervalId = window.setInterval(() => {
      if (document.visibilityState && document.visibilityState !== 'visible') {
        return;
      }
      tickBoard(state);
    }, INTERVAL);
  };

  boards.forEach(initBoard);

  const handleVisibility = () => {
    if (document.visibilityState && document.visibilityState !== 'visible') {
      return;
    }
    states.forEach((state) => {
      resetBoard(state);
    });
  };

  document.addEventListener('visibilitychange', handleVisibility);
  window.addEventListener('pageshow', handleVisibility);
})();
